<?php

/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */

/* * ***************************Includes********************************* */
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';

class openvpn extends eqLogic {
	/*     * *************************Attributs****************************** */

	/*     * ***********************Methode static*************************** */

	public static function dependancy_info() {
		$return = array();
		$return['log'] = 'openvpn_update';
		$return['progress_file'] = '/tmp/dependancy_openvpn_in_progress';
		if (exec('which openvpn | wc -l') != 0) {
			$return['state'] = 'ok';
		} else {
			$return['state'] = 'nok';
		}
		return $return;
	}

	public static function dependancy_install() {
		log::remove('openvpn_update');
		$cmd = 'sudo /bin/bash ' . dirname(__FILE__) . '/../../ressources/install.sh';
		$cmd .= ' >> ' . log::getPathToLog('openvpn_update') . ' 2>&1 &';
		exec($cmd);
	}

	public static function start() {
		self::cron15();
	}

	public static function cron15() {
		foreach (self::byType('openvpn') as $eqLogic) {
			if ($eqLogic->getConfiguration('enable') == 1 && !$eqLogic->getState()) {
				$eqLogic->start_openvpn();
			}
			if ($eqLogic->getConfiguration('enable') == 0 && $eqLogic->getState()) {
				$eqLogic->stop_openvpn();
			}
			$eqLogic->updateState();
		}
	}

	/*     * *********************Méthodes d'instance************************* */

	public function getInterfaceName() {
		$log_name = ('openvpn_' . str_replace(' ', '_', $this->getName()));
		if (!file_exists(log::getPathToLog($log_name))) {
			return false;
		}
		$result = shell_exec('grep "/sbin/ip addr add dev " ' . log::getPathToLog($log_name) . ' | tail -n 1');
		return trim(substr($result, strpos($result, 'tun'), 4));
	}

	public function getIp() {
		$log_name = ('openvpn_' . str_replace(' ', '_', $this->getName()));
		if (!file_exists(log::getPathToLog($log_name))) {
			return false;
		}
		$result = shell_exec('grep "/sbin/ip addr add dev " ' . log::getPathToLog($log_name) . ' | tail -n 1');
		$result = trim(substr($result, strpos($result, 'local') + 5));
		return trim(substr($result, 0, strpos($result, 'peer')));
	}

	public function isUp() {
		$interface = $this->getInterfaceName();
		if ($interface === false) {
			return false;
		}
		$result = shell_exec('sudo ip addr show ' . $interface . ' 2>&1 | wc -l');
		return ($result > 1);
	}

	public function preRemove() {
		$this->stop_openvpn();
	}

	public function preInsert() {
		$this->setConfiguration('remote_port', 1194);
	}

	public function preSave() {
		if ($this->getConfiguration('key') == '') {
			$this->setConfiguration('key', config::genKey(30));
		}
	}

	public function postSave() {
		$state = $this->getCmd(null, 'state');
		if (!is_object($state)) {
			$state = new openvpnCmd();
			$state->setLogicalId('state');
			$state->setIsVisible(1);
			$state->setName(__('Démarré', __FILE__));
			$state->setOrder(1);
		}
		$state->setType('info');
		$state->setSubType('binary');
		$state->setEventOnly(1);
		$state->setEqLogic_id($this->getId());
		$state->save();

		$up = $this->getCmd(null, 'up');
		if (!is_object($up)) {
			$up = new openvpnCmd();
			$up->setLogicalId('up');
			$up->setIsVisible(1);
			$up->setName(__('Actif', __FILE__));
			$state->setOrder(2);
		}
		$up->setType('info');
		$up->setSubType('binary');
		$up->setEventOnly(1);
		$up->setEqLogic_id($this->getId());
		$up->save();

		$start = $this->getCmd(null, 'start');
		if (!is_object($start)) {
			$start = new openvpnCmd();
			$start->setLogicalId('start');
			$start->setIsVisible(1);
			$start->setName(__('Démarrer', __FILE__));
			$state->setOrder(4);
		}
		$start->setType('action');
		$start->setSubType('other');
		$start->setEqLogic_id($this->getId());
		$start->save();

		$stop = $this->getCmd(null, 'stop');
		if (!is_object($stop)) {
			$stop = new openvpnCmd();
			$stop->setLogicalId('stop');
			$stop->setIsVisible(1);
			$stop->setName(__('Arrêter', __FILE__));
			$state->setOrder(5);
		}
		$stop->setType('action');
		$stop->setSubType('other');
		$stop->setEqLogic_id($this->getId());
		$stop->save();

		$ip = $this->getCmd(null, 'ip');
		if (!is_object($ip)) {
			$ip = new openvpnCmd();
			$ip->setLogicalId('ip');
			$ip->setIsVisible(1);
			$ip->setName(__('IP', __FILE__));
			$state->setOrder(3);
		}
		$ip->setType('info');
		$ip->setSubType('string');
		$ip->setEventOnly(1);
		$ip->setEqLogic_id($this->getId());
		$ip->save();

		if ($this->getIsEnable() == 0) {
			$this->stop_openvpn();
		} else {
			if ($this->getConfiguration('enable', 1) == 1) {
				$this->start_openvpn();
			}
		}
		$this->updateState();
	}

	private function writeConfig() {
		if (!file_exists(dirname(__FILE__) . '/../../data')) {
			mkdir(dirname(__FILE__) . '/../../data');
		}
		$replace = array(
			'#dev#' => $this->getConfiguration('dev'),
			'#proto#' => $this->getConfiguration('proto'),
			'#remote_host#' => $this->getConfiguration('remote_host'),
			'#remote_port#' => $this->getConfiguration('remote_port', 1194),
			'#ca_path#' => dirname(__FILE__) . '/../../data/ca_' . $this->getConfiguration('key') . '.crt',
			'#compression#' => $this->getConfiguration('compression'),
			'#script_security#' => $this->getConfiguration('script_security'),
			'#pull#' => $this->getConfiguration('pull'),
			'#auth_path#' => '/tmp/openvpn_auth_' . $this->getConfiguration('key') . '.conf',
		);

		if ($this->getConfiguration('auth_mode') == 'password') {
			$replace['#authentification#'] = 'auth-user-pass /tmp/openvpn_auth_' . $this->getConfiguration('key') . '.conf';
			file_put_contents('/tmp/openvpn_auth_' . $this->getConfiguration('key') . '.conf', trim($this->getConfiguration('username')) . "\n" . trim($this->getConfiguration('password')));
		} else {
			$replace['#authentification#'] = 'cert ' . dirname(__FILE__) . '/../../data/cert_' . $this->getConfiguration('key') . '.crt' . "\n";
			$replace['#authentification#'] .= 'key ' . dirname(__FILE__) . '/../../data/key_' . $this->getConfiguration('key') . '.key';
		}
		$config = str_replace(array_keys($replace), $replace, file_get_contents(dirname(__FILE__) . '/../config/openvpn.client.tmpl.ovpn'));
		file_put_contents('/tmp/openvpn_' . $this->getId() . '.ovpn', $config);
	}

	public function getCmdLine() {
		return 'openvpn --config /tmp/openvpn_' . $this->getId() . '.ovpn';
	}

	public function start_openvpn() {
		$this->stop_openvpn();
		$this->writeConfig();
		$log_name = ('openvpn_' . str_replace(' ', '_', $this->getName()));
		log::remove($log_name);
		$cmd = 'sudo ' . $this->getCmdLine() . ' >> ' . log::getPathToLog($log_name) . '  2>&1 &';
		log::add($log_name, 'info', __('Lancement openvpn : ', __FILE__) . $cmd);
		shell_exec($cmd);
		$this->updateState();
	}

	public function stop_openvpn() {
		exec("(ps ax || ps w) | grep -ie '" . $this->getCmdLine() . "' | grep -v grep | awk '{print $1}' | xargs sudo kill -9 > /dev/null 2>&1");
		$this->updateState();
	}

	public function getState() {
		return (shell_exec("(ps ax || ps w) | grep -ie '" . $this->getCmdLine() . "' | grep -v grep | wc -l") > 0);
	}

	public function updateState() {
		$cmd = $this->getCmd('info', 'state');
		if (is_object($cmd)) {
			if ($this->getState()) {
				$cmd->event(1);
			} else {
				$cmd->event(0);
			}
		}
		$up = $this->isUp();
		$up_cmd = $this->getCmd('info', 'up');
		if (is_object($up_cmd) && $up_cmd->execCmd(null, 2) != $up_cmd->formatValue($up)) {
			$up_cmd->event($up);
		}

		$ip_cmd = $this->getCmd('info', 'ip');
		if ($up) {
			$ip = $this->getIp();
		} else {
			$ip = __('Aucune', __FILE__);
		}
		if (is_object($ip_cmd) && $ip_cmd->execCmd(null, 2) != $ip_cmd->formatValue($ip)) {
			$ip_cmd->event($ip);
		}
	}

	/*     * **********************Getteur Setteur*************************** */
}

class openvpnCmd extends cmd {
	/*     * *************************Attributs****************************** */

	/*     * ***********************Methode static*************************** */

	/*     * *********************Methode d'instance************************* */

	/*
		 * Non obligatoire permet de demander de ne pas supprimer les commandes même si elles ne sont pas dans la nouvelle configuration de l'équipement envoyé en JS
		public function dontRemoveCmd() {
		return true;
		}
	*/

	public function execute($_options = array()) {
		$eqLogic = $this->getEqLogic();
		if ($this->getLogicalId() == 'start') {
			$eqLogic->start_openvpn();
			if ($eqLogic->getConfiguration('enable') == 0) {
				$eqLogic->setConfiguration('enable', 1);
				$eqLogic->save();
			}
		}
		if ($this->getLogicalId() == 'stop') {
			$eqLogic->stop_openvpn();
			if ($eqLogic->getConfiguration('enable') == 1) {
				$eqLogic->setConfiguration('enable', 0);
				$eqLogic->save();
			}
		}
	}

	/*     * **********************Getteur Setteur*************************** */
}

?>
