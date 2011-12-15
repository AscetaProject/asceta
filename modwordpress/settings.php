<?php

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
  $servers = $DB->get_records_sql('SELECT * FROM {modredmine_servers}');
  $html = "<table style='border: 1px solid; margin-bottom: 0;'><thead align='center'><tr style='background-color: #a0a0a0;'><td style='border-bottom: 1px solid gray;'>".get_string('name','modredmine')."</td><td style='border-bottom: 1px solid gray;'>URL</td><td style='border-bottom: 1px solid gray;'>".get_string('actions','modredmine')."</td></tr></thead><tbody>";
  $odd = true;
  $delete_activities_first = false;
  if (count($servers)) {
    foreach ($servers as $server) {

      $background = $odd ? 'transparent' : '#e0e0e0';
      $html .= "<tr style='background-color: ".$background.";'><td>".$server->name."</td><td>".$server->url."</td><td>";
      if ( $server->auth && $server->api_key == null) {
        $html .= "<a title='register' href='$CFG->wwwroot/mod/modredmine/servers.php?id=$server->id&amp;mode=key_request&amp;sesskey=".sesskey()."'>Authenticate</a>";
      }
      $html .= "&nbsp;<a title='edit' href='$CFG->wwwroot/mod/modredmine/servers.php?id=$server->id&amp;mode=edit&amp;sesskey=".sesskey()."'>Edit</a>";
      if (!$modredmine_instance = $DB->get_record('modredmine', array('server_id' => $server->id))) {
        $html .= "&nbsp;<a title='delete' href='$CFG->wwwroot/mod/modredmine/servers.php?id=$server->id&amp;mode=delete_server&amp;sesskey=".sesskey()."'>Delete</a>";
      } else {
        $html .= "&nbsp;Delete <font size='-1'>*</font>";
        $delete_activities_first = true;
      }
      $html .= "</td></tr>";
      $odd = $odd ? false : true;
    }
  } else {
    $html .= "<tr style='background-color: #e0e0e0;'><td colspan=3>".get_string('no_server','modredmine')."</td></tr>";
  }
  $html .= "</tbody></table>";
  if ($delete_activities_first) {
    $html .= "<font size='-3'>* Servidor con actividades dependientes.</font><br>";
  }
  $html .= "<br>";
  $html .= "<a title='".get_string("new_server","modredmine")."' href='$CFG->wwwroot/mod/modredmine/servers.php' style='margin-top: 5px'>".get_string("new_server","modredmine")."</a>";
  $settings->add(new admin_setting_heading('modredmine_servers_header', get_string('redmine_rest_api_server_manager', 'modredmine'), $html));
  //var_dump($servers);
}


