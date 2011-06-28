<?php

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    $servers = $DB->get_records_sql('SELECT * FROM {modwordpress_servers}');
    $html = "<table style='border: 1px solid;'><thead align='center'><tr style='background-color: #a0a0a0;'><td style='border-bottom: 1px solid gray;'>".get_string('name','modwordpress')."</td><td style='border-bottom: 1px solid gray;'>URL</td><td style='border-bottom: 1px solid gray;'>".get_string('actions','modwordpress')."</td></tr></thead><tbody>";
    $odd = true;
    if (count($servers)) {
    foreach ($servers as $server) {
        $background = $odd ? 'transparent' : '#e0e0e0';
        $html .= "<tr style='background-color: ".$background.";'><td>".$server->name."</td><td>".$server->url."</td><td>";
        if ( $server->oauth && $server->consumer_key == null) {
	$html .= "<a title='register' href='$CFG->wwwroot/mod/modwordpress/servers.php?id=$server->id&amp;mode=register&amp;sesskey=".sesskey()."'>Register</a>";
        } elseif ( $server->oauth && $server->request_token == null && $server->access_token == null) {
	$html .= "<a title='request_token' href='$CFG->wwwroot/mod/modwordpress/servers.php?id=$server->id&amp;mode=request_token&amp;sesskey=".sesskey()."'>Request</a>";
        } elseif ( $server->oauth && $server->access_token == null) {
	$html .= "<a title='authorize' href='$CFG->wwwroot/mod/modwordpress/servers.php?id=$server->id&amp;mode=auth&amp;sesskey=".sesskey()."'>Authorize</a>";
        } elseif ( $server->oauth && $server->consumer_key != null && $server->access_token != null) {
	$html .= get_string("authorized","modwordpress");
        }
        $html .= "&nbsp;<a title='edit' href='$CFG->wwwroot/mod/modwordpress/servers.php?id=$server->id&amp;mode=edit&amp;sesskey=".sesskey()."'>Edit</a>";
        $html .= "&nbsp;<a title='delete' href='$CFG->wwwroot/mod/modwordpress/servers.php?id=$server->id&amp;mode=delete_server&amp;sesskey=".sesskey()."'>Delete</a>";
        $html .= "</td></tr>";
        $odd = $odd ? false : true;
    }
    } else {
        $html .= "<tr style='background-color: #e0e0e0;'><td colspan=3>".get_string('no_configured_servers','modwordpress')."</td></tr>";
    }
    $html .= "</tbody></table>";
    $html .= "<a title='".get_string("new_server","modwordpress")."' href='$CFG->wwwroot/mod/modwordpress/servers.php'>".get_string("new_server","modwordpress")."</a>";
    $settings->add(new admin_setting_heading('modwordpress_servers_header', get_string('wordpress_rest_api_server_manager', 'modwordpress'), $html));
    //var_dump($servers);
}


