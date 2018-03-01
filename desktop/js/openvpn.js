
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
 $('.eqLogicAttr[data-l1key=configuration][data-l2key=auth_mode]').on('change',function(){
    $('.auth_mode').hide();
    $('.auth_mode.'+$(this).value()).show();
});


 function printEqLogic(_eqLogic){
    $('#bt_uploadCaCrt').fileupload({
        replaceFileInput: false,
        url: 'plugins/openvpn/core/ajax/openvpn.ajax.php?action=uploadCaCrt&type=ca&id=' + _eqLogic.id+'&jeedom_token='+JEEDOM_AJAX_TOKEN,,
        dataType: 'json',
        done: function (e, data) {
            if (data.result.state != 'ok') {
                $('#div_alert').showAlert({message: data.result.result, level: 'danger'});
                return;
            }else{
                $('#div_alert').showAlert({message: '{{Fichier envoyé avec succès}}', level: 'success'});
            }
        }
    });

    $('#bt_uploadCaCrtClient').fileupload({
        replaceFileInput: false,
        url: 'plugins/openvpn/core/ajax/openvpn.ajax.php?action=uploadCaCrt&type=caClient&id=' + _eqLogic.id+'&jeedom_token='+JEEDOM_AJAX_TOKEN,,
        dataType: 'json',
        done: function (e, data) {
            if (data.result.state != 'ok') {
                $('#div_alert').showAlert({message: data.result.result, level: 'danger'});
                return;
            }else{
                $('#div_alert').showAlert({message: '{{Fichier envoyé avec succès}}', level: 'success'});
            }
        }
    });

    $('#bt_uploadCaKeyClient').fileupload({
        replaceFileInput: false,
        url: 'plugins/openvpn/core/ajax/openvpn.ajax.php?action=uploadCaCrt&type=keyClient&id=' + _eqLogic.id+'&jeedom_token='+JEEDOM_AJAX_TOKEN,,
        dataType: 'json',
        done: function (e, data) {
            if (data.result.state != 'ok') {
                $('#div_alert').showAlert({message: data.result.result, level: 'danger'});
                return;
            }else{
                $('#div_alert').showAlert({message: '{{Fichier envoyé avec succès}}', level: 'success'});
            }
        }
    });
}


/*
 * Fonction pour l'ajout de commande, appellé automatiquement par plugin.template
 */
 function addCmdToTable(_cmd) {
    if (!isset(_cmd)) {
        var _cmd = {configuration: {}};
    }
    var tr = '<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '">';
    tr += '<td>';
    tr += '<input class="cmdAttr form-control input-sm" data-l1key="id" style="display : none;">';
    tr += '<input class="cmdAttr form-control input-sm" data-l1key="name"></td>';
    tr += '<td>';
    tr += '<span><label class="checkbox-inline"><input type="checkbox" class="cmdAttr checkbox-inline" data-l1key="isVisible" checked/>{{Afficher}}</label></span> ';
    tr += '<span><label class="checkbox-inline"><input type="checkbox" class="cmdAttr checkbox-inline" data-l1key="isHistorized" checked/>{{Historiser}}</label></span> ';
    tr += '</td>';
    tr += '<td>';
    tr += '<input class="cmdAttr form-control input-sm" data-l1key="type" style="display : none;">';
    tr += '<input class="cmdAttr form-control input-sm" data-l1key="subType" style="display : none;">';
    if (is_numeric(_cmd.id)) {
        tr += '<a class="btn btn-default btn-xs cmdAction" data-action="configure"><i class="fa fa-cogs"></i></a> ';
        tr += '<a class="btn btn-default btn-xs cmdAction" data-action="test"><i class="fa fa-rss"></i> {{Tester}}</a>';
    }
    tr += '<i class="fa fa-minus-circle pull-right cmdAction cursor" data-action="remove"></i></td>';
    tr += '</tr>';
    $('#table_cmd tbody').append(tr);
    $('#table_cmd tbody tr:last').setValues(_cmd, '.cmdAttr');
    jeedom.cmd.changeType($('#table_cmd tbody tr:last'), init(_cmd.subType));
}
