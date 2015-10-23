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

	public static function updateOpenvpn() {
		log::remove('openvpn_update');
		$cmd = 'sudo /bin/bash ' . dirname(__FILE__) . '/../../ressources/install.sh';
		$cmd .= ' >> ' . log::getPathToLog('openvpn_update') . ' 2>&1 &';
		exec($cmd);
	}

	public static function cron15() {
		foreach (self::byType('openvpn') as $eqLogic) {
			if ($eqLogic->getConfiguration('enable') == 1 && !$eqLogic->getState()) {
				$eqLogic->start_openvpn();
			}
			if ($eqLogic->getConfiguration('enable') == 0 && $eqLogic->getState()) {
				$eqLogic->stop_openvpn();
			}
		}
	}

	/*     * *********************Méthodes d'instance************************* */

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
			$state->setName(__('Status', __FILE__));
		}
		$state->setType('info');
		$state->setSubType('string');
		$state->setEventOnly(1);
		$state->setEqLogic_id($this->getId());
		$state->save();

		$start = $this->getCmd(null, 'start');
		if (!is_object($start)) {
			$start = new openvpnCmd();
			$start->setLogicalId('start');
			$start->setIsVisible(1);
			$start->setName(__('Démarrer', __FILE__));
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
		}
		$stop->setType('action');
		$stop->setSubType('other');
		$stop->setEqLogic_id($this->getId());
		$stop->save();
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
			'#auth_path#' => '/tmp/openvpn_auth_' . $this->getConfiguration('key') . '.conf',
		);
		$config = str_replace(array_keys($replace), $replace, file_get_contents(dirname(__FILE__) . '/../config/openvpn.client.tmpl.ovpn'));
		file_put_contents('/tmp/openvpn_' . $this->getId() . '.ovpn', $config);
		file_put_contents('/tmp/openvpn_auth_' . $this->getConfiguration('key') . '.conf', $this->getConfiguration('username') . "\n" . $this->getConfiguration('password'));
	}

	public function getCmdLine() {
		return 'openvpn --config /tmp/openvpn_' . $this->getId() . '.ovpn';
	}

	public function start_openvpn() {
		$this->writeConfig();
		log::remove('openvpn_' . $this->getName());
		$cmd = $this->getCmdLine() . ' 2>&1 >> ' . log::getPathToLog('openvpn_' . $this->getName()) . ' &';
		log::add('openvpn_' . $this->getName(), 'info', __('Lancement openvpn : ', __FILE__) . $cmd);
		shell_exec($cmd);
		$this->updateState();
	}

	public function stop_openvpn() {
		exec("(ps ax || ps w) | grep -ie '" . $this->getCmdLine() . "' | grep -v grep | awk '{print $2}' | xargs kill -9 > /dev/null 2>&1");
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
