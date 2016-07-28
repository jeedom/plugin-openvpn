<?php
if (!isConnect('admin')) {
	throw new Exception('{{401 - Accès non autorisé}}');
}
sendVarToJS('eqType', 'openvpn');
$eqLogics = eqLogic::byType('openvpn');
?>

<div class="row row-overflow">
  <div class="col-lg-2 col-md-3 col-sm-4">
    <div class="bs-sidebar">
      <ul id="ul_eqLogic" class="nav nav-list bs-sidenav">
        <a class="btn btn-default eqLogicAction" style="width : 100%;margin-top : 5px;margin-bottom: 5px;" data-action="add"><i class="fa fa-plus-circle"></i> {{Ajouter un openvpn}}</a>
        <li class="filter" style="margin-bottom: 5px;"><input class="filter form-control input-sm" placeholder="{{Rechercher}}" style="width: 100%"/></li>
        <?php
foreach ($eqLogics as $eqLogic) {
	echo '<li class="cursor li_eqLogic" data-eqLogic_id="' . $eqLogic->getId() . '"><a>' . $eqLogic->getHumanName() . '</a></li>';
}
?>
     </ul>
   </div>
 </div>

 <div class="col-lg-10 col-md-9 col-sm-8 eqLogicThumbnailDisplay" style="border-left: solid 1px #EEE; padding-left: 25px;">
  <legend>{{Mes openvpns}}
  </legend>

  <div class="eqLogicThumbnailContainer">
    <div class="cursor eqLogicAction" data-action="add" style="background-color : #ffffff; height : 200px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;" >
     <center>
      <i class="fa fa-plus-circle" style="font-size : 7em;color:#94ca02;"></i>
    </center>
    <span style="font-size : 1.1em;position:relative; top : 23px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#94ca02"><center>Ajouter</center></span>
  </div>
  <?php
foreach ($eqLogics as $eqLogic) {
	echo '<div class="eqLogicDisplayCard cursor" data-eqLogic_id="' . $eqLogic->getId() . '" style="background-color : #ffffff; height : 200px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;" >';
	echo "<center>";
	echo '<img src="plugins/openvpn/doc/images/openvpn_icon.png" height="105" width="95" />';
	echo "</center>";
	echo '<span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;"><center>' . $eqLogic->getHumanName(true, true) . '</center></span>';
	echo '</div>';
}
?>
</div>
</div>

<div class="col-lg-10 col-md-9 col-sm-8 eqLogic" style="border-left: solid 1px #EEE; padding-left: 25px;display: none;">
 <a class="btn btn-success eqLogicAction pull-right" data-action="save"><i class="fa fa-check-circle"></i> {{Sauvegarder}}</a>
 <a class="btn btn-danger eqLogicAction pull-right" data-action="remove"><i class="fa fa-minus-circle"></i> {{Supprimer}}</a>

 <ul class="nav nav-tabs" role="tablist">
  <li role="presentation" class="active"><a href="#eqlogictab" aria-controls="home" role="tab" data-toggle="tab"><i class="fa fa-tachometer"></i> {{Equipement}}</a></li>
  <li role="presentation"><a href="#commandtab" aria-controls="profile" role="tab" data-toggle="tab"><i class="fa fa-list-alt"></i> {{Commandes}}</a></li>
</ul>

<div class="tab-content" style="height:calc(100% - 50px);overflow:auto;overflow-x: hidden;">
  <div role="tabpanel" class="tab-pane active" id="eqlogictab">
    <div class="row">
      <div class="col-sm-6">
        <form class="form-horizontal">
          <fieldset>
            <legend><i class="fa fa-arrow-circle-left eqLogicAction cursor" data-action="returnToThumbnailDisplay"></i> {{Général}}  <i class='fa fa-cogs eqLogicAction pull-right cursor expertModeVisible' data-action='configure'></i></legend>
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
foreach (object::all() as $object) {
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
            <label class="col-sm-4 control-label">Catégorie</label>
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
          <label class="col-sm-4 control-label">{{Serveur hote}}</label>
          <div class="col-sm-4">
            <input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="remote_host" />
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-4 control-label">{{Port hote}}</label>
          <div class="col-sm-4">
            <input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="remote_port" />
          </div>
        </div>

      </fieldset>
    </form>
  </div>
  <div class="col-sm-6">
    <form class="form-horizontal">
      <fieldset>
        <legend>{{Configuration}}</legend>
        <div class="form-group">
          <label class="col-sm-4 control-label">{{Authentification mode}}</label>
          <div class="col-sm-4">
            <select class="eqLogicAttr form-control expertModeVisible" data-l1key="configuration" data-l2key="auth_mode">
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
              <input type="password" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="password" />
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
          <select class="eqLogicAttr form-control expertModeVisible" data-l1key="configuration" data-l2key="proto">
            <option value="udp">UDP</option>
            <option value="tcp">TCP</option>
          </select>
        </div>
      </div>
      <div class="form-group">
        <label class="col-sm-4 control-label">{{Interface}}</label>
        <div class="col-sm-4">
          <select class="eqLogicAttr form-control expertModeVisible" data-l1key="configuration" data-l2key="dev">
            <option value="tun">TUN</option>
            <option value="tap">TAP</option>
          </select>
        </div>
      </div>
      <div class="form-group">
        <label class="col-sm-4 control-label">{{Compression}}</label>
        <div class="col-sm-4">
          <select class="eqLogicAttr form-control expertModeVisible" data-l1key="configuration" data-l2key="compression">
            <option value="">Non</option>
            <option value="comp-lzo">Oui</option>
          </select>
        </div>
      </div>
      <div class="form-group">
        <label class="col-sm-4 control-label">{{Script sécurité}}</label>
        <div class="col-sm-4">
          <select class="eqLogicAttr form-control expertModeVisible" data-l1key="configuration" data-l2key="script_security">
            <option value="">Non</option>
            <option value="script-security 2">2</option>
          </select>
        </div>
      </div>
      <div class="form-group">
        <label class="col-sm-4 control-label">{{Pull}}</label>
        <div class="col-sm-4">
          <select class="eqLogicAttr form-control expertModeVisible" data-l1key="configuration" data-l2key="pull">
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