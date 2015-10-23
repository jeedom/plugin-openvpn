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

try {
	require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
	include_file('core', 'authentification', 'php');

	if (!isConnect('admin')) {
		throw new Exception(__('401 - Accès non autorisé', __FILE__));
	}

	if (init('action') == 'uploadCaCrt') {
		$eqLogic = eqLogic::byId(init('id'));
		if (!is_object($eqLogic)) {
			throw new Exception(__('EqLogic inconnu verifié l\'id', __FILE__));
		}
		if (!isset($_FILES['file'])) {
			throw new Exception(__('Aucun fichier trouvé. Vérifié parametre PHP (post size limit)', __FILE__));
		}
		$extension = strtolower(strrchr($_FILES['file']['name'], '.'));
		if (!in_array($extension, array('.crt'))) {
			throw new Exception('Extension du fichier non valide (autorisé .crt) : ' . $extension);
		}
		if (filesize($_FILES['file']['tmp_name']) > 1000000) {
			throw new Exception(__('Le fichier est trop gros (maximum 1mo)', __FILE__));
		}
		$filepath = dirname(__FILE__) . '/../../data/ca_' . $eqLogic->getConfiguration('key') . '.crt';
		file_put_contents($filepath, file_get_contents($_FILES['file']['tmp_name']));
		ajax::success();
	}

	if (init('action') == 'updateOpenvpn') {
		openvpn::updateOpenvpn();
		ajax::success();
	}

	throw new Exception(__('Aucune méthode correspondante à : ', __FILE__) . init('action'));
	/*     * *********Catch exeption*************** */
} catch (Exception $e) {
	ajax::error(displayExeption($e), $e->getCode());
}
?>
