
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


function printEqLogic(_eqLogic){
  $('#bt_uploadCaCrt').fileupload({
    replaceFileInput: false,
    url: 'plugins/openvpn/core/ajax/openvpn.ajax.php?action=uploadCaCrt&type=ca&id=' + _eqLogic.id+'&jeedom_token='+JEEDOM_AJAX_TOKEN,
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
    url: 'plugins/openvpn/core/ajax/openvpn.ajax.php?action=uploadCaCrt&type=caClient&id=' + _eqLogic.id+'&jeedom_token='+JEEDOM_AJAX_TOKEN,
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
    url: 'plugins/openvpn/core/ajax/openvpn.ajax.php?action=uploadCaCrt&type=keyClient&id=' + _eqLogic.id+'&jeedom_token='+JEEDOM_AJAX_TOKEN,
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
  jeedomUtils.taAutosize();
}


/*
* Fonction pour l'ajout de commande, appellé automatiquement par plugin.template
*/
$("#table_cmd").sortable({axis: "y", cursor: "move", items: ".cmd", placeholder: "ui-state-highlight", tolerance: "intersect", forcePlaceholderSize: true});

function addCmdToTable(_cmd) {
  if (!isset(_cmd)) {
    var _cmd = {configuration: {}};
  }
  if (!isset(_cmd.configuration)) {
    _cmd.configuration = {};
  }
  var tr = '<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '">';
  tr += '<td>';
  tr += '<input class="cmdAttr form-control input-sm" data-l1key="id" style="display : none;">';
  tr += '<input class="cmdAttr form-control input-sm" data-l1key="name"></td>';
  tr += '<td>';
  tr += '<label class="checkbox-inline"><input type="checkbox" class="cmdAttr" data-l1key="isVisible" checked/>{{Afficher}}</label> '
  tr += '<label class="checkbox-inline"><input type="checkbox" class="cmdAttr" data-l1key="isHistorized" checked/>{{Historiser}}</label> '
  tr += '<td>';
  tr += '<span class="cmdAttr" data-l1key="htmlstate"></span>'; 
  tr += '</td>';
  tr += '<td>';
  if (is_numeric(_cmd.id)) {
    tr += '<a class="btn btn-default btn-xs cmdAction" data-action="configure" title="{{Configuration avancée}}"><i class="fas fa-cogs"></i></a> ';
    tr += '<a class="btn btn-default btn-xs cmdAction" data-action="test"><i class="fas fa-rss"></i> {{Tester}}</a>';
  }
  tr += '</td>';
  tr += '<td>';
  tr += '<i class="fas fa-minus-circle pull-right cmdAction cursor" data-action="remove" title="{{Supprimer la commande}}"></i>';
  tr += '</td>';
  tr += '</tr>';
  $('#table_cmd tbody').append(tr);
  var tr = $('#table_cmd tbody tr').last();
  jeedom.eqLogic.buildSelectCmd({
    id:  $('.eqLogicAttr[data-l1key=id]').value(),
    filter: {type: 'info'},
    error: function (error) {
      $('#div_alert').showAlert({message: error.message, level: 'danger'});
    },
    success: function (result) {
      tr.find('.cmdAttr[data-l1key=value]').append(result);
      tr.setValues(_cmd, '.cmdAttr');
      jeedom.cmd.changeType(tr, init(_cmd.subType));
    }
  });
}
