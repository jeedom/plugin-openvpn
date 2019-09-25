<?php
if (!isConnect('admin')) {
	throw new Exception('{{401 - Accès non autorisé}}');
}
$plugin = plugin::byId('openvpn');
sendVarToJS('eqType', $plugin->getId());
$eqLogics = eqLogic::byType($plugin->getId());
?>

<div class="row row-overflow">
	<div class="col-xs-12 eqLogicThumbnailDisplay">
		<legend><i class="fa fa-cog"></i> {{Gestion}}</legend>
		<div class="eqLogicThumbnailContainer">
			<div class="cursor eqLogicAction logoPrimary" data-action="add">
				<i class="fas fa-plus-circle"></i>
				<br/>
				<span >{{Ajouter}}</span>
			</div>
		</div>
		<legend><i class="fas fa-archway"></i> {{Mes openvpns}}</legend>
		<input class="form-control" placeholder="{{Rechercher}}" id="in_searchEqlogic" />
		<div class="eqLogicThumbnailContainer">
			<?php
			foreach ($eqLogics as $eqLogic) {
				$opacity = ($eqLogic->getIsEnable()) ? '' : 'disableCard';
				echo '<div class="eqLogicDisplayCard cursor '.$opacity.'" data-eqLogic_id="' . $eqLogic->getId() . '">';
				echo '<img src="' . $plugin->getPathImgIcon() . '"/>';
				echo '<br/>';
				echo '<span class="name">' . $eqLogic->getHumanName(true, true) . '</span>';
				echo '</div>';
			}
			?>
		</div>
	</div>
	
	<div class="col-xs-12 eqLogic" style="display: none;">
		<div class="input-group pull-right" style="display:inline-flex">
			<span class="input-group-btn">
				<a class="btn btn-default eqLogicAction btn-sm roundedLeft" data-action="configure"><i class="fas fa-cogs"></i> {{Configuration avancée}}</a><a class="btn btn-sm btn-success eqLogicAction" data-action="save"><i class="fas fa-check-circle"></i> {{Sauvegarder}}</a><a class="btn btn-danger btn-sm eqLogicAction roundedRight" data-action="remove"><i class="fas fa-minus-circle"></i> {{Supprimer}}</a>
			</span>
		</div>
		<ul class="nav nav-tabs" role="tablist">
			<li role="presentation"><a class="eqLogicAction cursor" aria-controls="home" role="tab" data-action="returnToThumbnailDisplay"><i class="fas fa-arrow-circle-left"></i></a></li>
			<li role="presentation" class="active"><a href="#eqlogictab" aria-controls="home" role="tab" data-toggle="tab"><i class="fas fa-tachometer-alt"></i> {{Equipement}}</a></li>
			<li role="presentation"><a href="#commandtab" aria-controls="profile" role="tab" data-toggle="tab"><i class="fas fa-list-alt"></i> {{Commandes}}</a></li>
		</ul>
		<div class="tab-content" style="height:calc(100% - 50px);overflow:auto;overflow-x: hidden;">
			<div role="tabpanel" class="tab-pane active" id="eqlogictab">
				<br/>
				<div class="row">
					<div class="col-sm-6">
						<form class="form-horizontal">
							<fieldset>
								<div class="form-group">
									<label class="col-sm-4 control-label">{{Nom de l'équipement openvpn}}</label>
									<div class="col-sm-4">
										<input type="text" class="eqLogicAttr form-control" data-l1key="id" style="display : none;" />
										<input type="text" class="eqLogicAttr form-control" data-l1key="name" placeholder="{{Nom de l'équipement openvpn}}"/>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label" >{{Objet parent}}</label>
									<div class="col-sm-4">
										<select id="sel_object" class="eqLogicAttr form-control" data-l1key="object_id">
											<option value="">{{Aucun}}</option>
											<?php
											foreach (jeeObject::all() as $object) {
												echo '<option value="' . $object->getId() . '">' . $object->getName() . '</option>';
											}
											?>
										</select>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-3 control-label"></label>
									<div class="col-sm-9">
										<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isEnable" checked/>{{Activer}}</label>
										<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isVisible" checked/>{{Visible}}</label>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">{{Catégorie}}</label>
									<div class="col-sm-8">
										<?php
										foreach (jeedom::getConfiguration('eqLogic:category') as $key => $value) {
											echo '<label class="checkbox-inline">';
											echo '<input type="checkbox" class="eqLogicAttr" data-l1key="category" data-l2key="' . $key . '" />' . $value['name'];
											echo '</label>';
										}
										?>
										
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">{{Certificat CA}}</label>
									<div class="col-sm-8">
										<span class="btn btn-default btn-file">
											<i class="fa fa-cloud-upload"></i> {{Envoyer}}<input  id="bt_uploadCaCrt" type="file" name="file" style="display: inline-block;">
										</span>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">{{Serveur hôte}}</label>
									<div class="col-sm-4">
										<input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="remote_host" />
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">{{Port hôte}}</label>
									<div class="col-sm-4">
										<input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="remote_port" />
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">{{Paramètres optionnels}}</label>
									<div class="col-sm-8">
										<textarea class="eqLogicAttr form-control ta_autosize" data-l1key="configuration" data-l2key="additionalVpnParameters" ></textarea>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">{{Commande post démarrage}}</label>
									<div class="col-sm-8">
										<textarea class="eqLogicAttr form-control ta_autosize" data-l1key="configuration" data-l2key="optionsAfterStart" ></textarea>
									</div>
								</div>
							</fieldset>
						</form>
					</div>
					<div class="col-sm-6">
						<form class="form-horizontal">
							<fieldset>
								<div class="form-group">
									<label class="col-sm-4 control-label">{{Authentification mode}}</label>
									<div class="col-sm-4">
										<select class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="auth_mode">
											<option value="cert">Certificat</option>
											<option value="password">Mot de passe</option>
										</select>
									</div>
								</div>
								<div class="auth_mode password" style="display:none;">
									<div class="form-group">
										<label class="col-sm-4 control-label">{{Nom d'utilisateur}}</label>
										<div class="col-sm-4">
											<input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="username" />
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-4 control-label">{{Password}}</label>
										<div class="col-sm-4">
											<input type="password" autocomplete="new-password" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="password" />
										</div>
									</div>
								</div>
								<div class="auth_mode cert">
									<div class="form-group">
										<label class="col-sm-4 control-label">{{Certification client}}</label>
										<div class="col-sm-8">
											<span class="btn btn-default btn-file">
												<i class="fa fa-cloud-upload"></i> {{Envoyer}}<input  id="bt_uploadCaCrtClient" type="file" name="file" style="display: inline-block;">
											</span>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-4 control-label">{{Clef client}}</label>
										<div class="col-sm-8">
											<span class="btn btn-default btn-file">
												<i class="fa fa-cloud-upload"></i> {{Envoyer}}<input  id="bt_uploadCaKeyClient" type="file" name="file" style="display: inline-block;">
											</span>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">{{Protocole}}</label>
									<div class="col-sm-4">
										<select class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="proto">
											<option value="udp">UDP</option>
											<option value="tcp">TCP</option>
										</select>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">{{Interface}}</label>
									<div class="col-sm-4">
										<select class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="dev">
											<option value="tun">TUN</option>
											<option value="tap">TAP</option>
										</select>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">{{Compression}}</label>
									<div class="col-sm-4">
										<select class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="compression">
											<option value="">Non</option>
											<option value="comp-lzo">Oui</option>
										</select>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">{{Script sécurité}}</label>
									<div class="col-sm-4">
										<select class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="script_security">
											<option value="">Non</option>
											<option value="script-security 2">2</option>
										</select>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">{{Pull}}</label>
									<div class="col-sm-4">
										<select class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="pull">
											<option value="">Non</option>
											<option value="pull">Oui</option>
										</select>
									</div>
								</div>
							</fieldset>
						</form>
					</div>
				</div>
				
			</div>
			<div role="tabpanel" class="tab-pane" id="commandtab">
				<br/>
				<table id="table_cmd" class="table table-bordered table-condensed">
					<thead>
						<tr>
							<th>{{Nom}}</th><th>{{Type}}</th><th>{{Action}}</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
		</div>
		
		
	</div>
</div>

<?php include_file('desktop', 'openvpn', 'js', 'openvpn');?>
<?php include_file('core', 'plugin.template', 'js');?>
