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
		$return['progress_file'] = jeedom::getTmpFolder('openvpn') . '/dependance';
		$return['state'] = 'ok';
		if (exec('which openvpn | wc -l') == 0){
			$return['state'] = 'nok';
		}
		return $return;
	}
	
	public static function dependancy_install() {
		log::remove(__CLASS__ . '_update');
		return array('script' => dirname(__FILE__) . '/../../resources/install_#stype#.sh ' . jeedom::getTmpFolder('openvpn') . '/dependance', 'log' => log::getPathToLog(__CLASS__ . '_update'));
	}
	
	public static function start() {
		self::cron5();
	}
	
	public static function cron5() {
		foreach (self::byType('openvpn') as $eqLogic) {
			try {
				if ($eqLogic->getConfiguration('enable') == 1 && !$eqLogic->getState()) {
					if ($eqLogic->getLogicalId() == 'dnsjeedom') {
						try {
							repo_market::test();
						} catch (Exception $e) {
							
						}
						if (!$eqLogic->getState()) {
							$eqLogic->start_openvpn();
						}
						$eqLogic->updateState();
						return;
					}
					$eqLogic->start_openvpn();
				}
				if ($eqLogic->getConfiguration('enable') == 0 && $eqLogic->getState()) {
					$eqLogic->stop_openvpn();
				}
				$eqLogic->updateState();
			} catch (Exception $e) {
				
			}
		}
	}
	
	public static function cleanVpnName($_name){
		return str_replace(array(' ','(',')','/',',',';','\\','%','*','$'), '_', $_name);
	}
	
	/*     * *********************Méthodes d'instance************************* */
	
	public function getInterfaceName() {
		$log_name = ('openvpn_' . self::cleanVpnName($this->getName()));
		$path =  log::getPathToLog($log_name);
		if (!file_exists($path)) {
			return false;
		}
		$result = shell_exec('grep "/sbin/ip addr add dev " ' . $path . ' | tail -n 1');
		$i = 0;
		while($result == ''){
			sleep(1);
			$result = shell_exec('grep "/sbin/ip addr add dev " ' . $path . ' | tail -n 1');
			$i++;
			if($i > 5){
				break;
			}
		}
		return trim(substr($result, strpos($result, 'tun'), 4));
	}
	
	public function getIp() {
		$log_name = ('openvpn_' . self::cleanVpnName($this->getName()));
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
		$result = shell_exec('ip addr show ' . $interface . ' 2>&1 | wc -l');
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
			$state->setConfiguration('repeatEventManagement','never');
		}
		$state->setType('info');
		$state->setSubType('binary');
		$state->setEqLogic_id($this->getId());
		$state->save();
		
		$up = $this->getCmd(null, 'up');
		if (!is_object($up)) {
			$up = new openvpnCmd();
			$up->setLogicalId('up');
			$up->setIsVisible(1);
			$up->setName(__('Actif', __FILE__));
			$state->setOrder(2);
			$up->setConfiguration('repeatEventManagement','never');
		}
		$up->setType('info');
		$up->setSubType('binary');
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
		$ip->setEqLogic_id($this->getId());
		$ip->save();
		
		if ($this->getIsEnable() == 0) {
			$this->stop_openvpn();
		}
	}
	
	public function decrypt(){
		$this->setConfiguration('password',utils::decrypt($this->getConfiguration('password')));
	}
	public function encrypt(){
		$this->setConfiguration('password',utils::encrypt($this->getConfiguration('password')));
	}
	
	private function writeConfig() {
		if (!file_exists(dirname(__FILE__) . '/../../data')) {
			mkdir(dirname(__FILE__) . '/../../data');
		}
		if(trim($this->getConfiguration('remote')) != ''){
			$remotes = explode(',',$this->getConfiguration('remote'));
			shuffle($remotes);
			$remote = '';
			foreach ($remotes as $value) {
				$remote .= 'remote '.$value."\n";
			}
		}else{
			$remote = 'remote '.$this->getConfiguration('remote_host').' '.$this->getConfiguration('remote_port', 1194);
		}
		$replace = array(
			'#dev#' => $this->getConfiguration('dev'),
			'#proto#' => $this->getConfiguration('proto'),
			'#remote#' => $remote,
			'#ca_path#' => trim(dirname(__FILE__) . '/../../data/ca_' . $this->getConfiguration('key') . '.crt'),
			'#compression#' => $this->getConfiguration('compression'),
			'#script_security#' => $this->getConfiguration('script_security'),
			'#pull#' => $this->getConfiguration('pull'),
			'#auth_path#' => jeedom::getTmpFolder('openvpn') . '/openvpn_auth_' . $this->getConfiguration('key') . '.conf',
		);
		
		if ($this->getConfiguration('auth_mode') == 'password') {
			$replace['#authentification#'] = 'auth-user-pass ' . jeedom::getTmpFolder('openvpn') . '/openvpn_auth_' . $this->getConfiguration('key') . '.conf';
			file_put_contents(jeedom::getTmpFolder('openvpn') . '/openvpn_auth_' . $this->getConfiguration('key') . '.conf', trim($this->getConfiguration('username')) . "\n" . trim($this->getConfiguration('password')));
		} else {
			$replace['#authentification#'] = 'cert ' . dirname(__FILE__) . '/../../data/cert_' . $this->getConfiguration('key') . '.crt' . "\n";
			$replace['#authentification#'] .= 'key ' . dirname(__FILE__) . '/../../data/key_' . $this->getConfiguration('key') . '.key';
		}
		$config = str_replace(array_keys($replace), $replace, file_get_contents(dirname(__FILE__) . '/../config/openvpn.client.tmpl.ovpn'));
		if (trim($this->getConfiguration('additionalVpnParameters')) != '') {
			$config .= "\n\n" . $this->getConfiguration('additionalVpnParameters');
		}
		file_put_contents(jeedom::getTmpFolder('openvpn') . '/openvpn_' . $this->getId() . '.ovpn', $config);
	}
	
	public function getCmdLine() {
		return 'openvpn --config ' . jeedom::getTmpFolder('openvpn') . '/openvpn_' . $this->getId() . '.ovpn';
	}
	
	public function start_openvpn() {
		$this->stop_openvpn();
		$this->writeConfig();
		$log_name = ('openvpn_' . self::cleanVpnName($this->getName()));
		log::remove($log_name);
		$cmd = system::getCmdSudo() . $this->getCmdLine() . ' >> ' . log::getPathToLog($log_name) . '  2>&1 &';
		log::add($log_name, 'info', __('Lancement openvpn : ', __FILE__) . $cmd);
		shell_exec($cmd);
		$this->updateState();
		if (trim($this->getConfiguration('optionsAfterStart')) != '') {
			sleep(2);
			$cmd = str_replace('#interface#', $this->getInterfaceName(), $this->getConfiguration('optionsAfterStart'));
			log::add('openvpn','debug','Exec post start cmd : '.$cmd);
			shell_exec($cmd);
		}
		if($this->getLogicalId() == 'dnsjeedom'){
			$interface = $this->getInterfaceName();
			if ($interface !== null && $interface != '' && $interface !== false) {
				$cmd = system::getCmdSudo().'iptables -L INPUT -v --line-numbers | grep '.$interface;
				log::add('openvpn','debug',$cmd);
				$rules = shell_exec($cmd);
				$c = 0;
				while($rules != ''){
					$ln = explode(" ",explode("\n",$rules)[0])[0];
					if($ln == ''){
						break;
					}
					$cmd = system::getCmdSudo().'iptables -D INPUT '.$ln;
					log::add('openvpn','debug',$cmd);
					shell_exec($cmd);
					$rules = shell_exec(system::getCmdSudo().'iptables -L INPUT -v --line-numbers | grep '.$interface);
					$c++;
					if($c > 25){
						break;
					}
				}
				$cmd = system::getCmdSudo() . 'iptables -A INPUT -i ' . $interface . ' -p tcp  --destination-port 80 -j ACCEPT';
				log::add('openvpn','debug',$cmd);
				shell_exec($cmd);
				if (config::byKey('dns::openport') != '') {
					foreach (explode(',', config::byKey('dns::openport')) as $port) {
						if (is_nan($port)) {
							continue;
						}
						try {
							$cmd = system::getCmdSudo() . 'iptables -A INPUT -i ' . $interface . ' -p tcp  --destination-port ' . $port . ' -j ACCEPT';
							log::add('openvpn','debug',$cmd);
							shell_exec($cmd);
						} catch (Exception $e) {
							
						}
					}
				}
				$cmd = system::getCmdSudo() . 'iptables -A INPUT -i ' . $interface . ' -j DROP';
				log::add('openvpn','debug',$cmd);
				shell_exec($cmd);
			}
		}
	}
	
	public function stop_openvpn() {
		exec("(ps ax || ps w) | grep -ie '" . $this->getCmdLine() . "' | grep -v grep | awk '{print $1}' | xargs sudo kill -9 > /dev/null 2>&1");
		$this->updateState();
	}
	
	public function getState() {
		$return = (shell_exec("(ps ax || ps w) | grep -ie '" . $this->getCmdLine() . "' | grep -v grep | wc -l") > 0);
		if(!$return){
			usleep(rand(10000,2000000));
			$return = (shell_exec("(ps ax || ps w) | grep -ie '" . $this->getCmdLine() . "' | grep -v grep | wc -l") > 0);
		}
		return $return;
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
		if ($up) {
			$ip = $this->getIp();
		} else {
			$ip = __('Aucune', __FILE__);
		}
		$this->checkAndUpdateCmd('up', $up);
		$this->checkAndUpdateCmd('ip', $ip);
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
			$eqLogic->save(true);
		}
	}
	if ($this->getLogicalId() == 'stop') {
		$eqLogic->stop_openvpn();
		if ($eqLogic->getConfiguration('enable') == 1) {
			$eqLogic->setConfiguration('enable', 0);
			$eqLogic->save(true);
		}
	}
}

/*     * **********************Getteur Setteur*************************** */
}

?>
