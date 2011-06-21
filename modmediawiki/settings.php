<?php

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    $servers = $DB->get_records_sql('SELECT * FROM {modmediawiki_servers}');
    $html = "<table style='border: 1px solid;'><thead align='center'><tr style='background-color: #a0a0a0;'><td style='border-bottom: 1px solid gray;'>".get_string('name','modmediawiki')."</td><td style='border-bottom: 1px solid gray;'>URL</td><td style='border-bottom: 1px solid gray;'>".get_string('actions','modmediawiki')."</td></tr></thead><tbody>";
    $odd = true;
    foreach ($servers as $server) {
        $background = $odd ? 'transparent' : '#e0e0e0';
        $html .= "<tr style='background-color: ".$background.";'><td>".$server->name."</td><td>".$server->url."</td><td>";
        if ( $server->consumer_key == null) {
	$html .= "<a title='register' href='$CFG->wwwroot/mod/modmediawiki/servers.php?id=$server->id&amp;mode=register&amp;sesskey=".sesskey()."'>Register</a>";
        } elseif ( $server->request_token == null && $server->access_token == null) {
	$html .= "<a title='request_token' href='$CFG->wwwroot/mod/modmediawiki/servers.php?id=$server->id&amp;mode=request_token&amp;sesskey=".sesskey()."'>Request</a>";
        } elseif ( $server->access_token == null) {
	$html .= "<a title='authorize' href='$CFG->wwwroot/mod/modmediawiki/servers.php?id=$server->id&amp;mode=auth&amp;sesskey=".sesskey()."'>Authorize</a>";
        } elseif ( $server->consumer_key != null && $server->access_token != null) {
	$html .= get_string("authorized","modmediawiki");
        }
        $html .= "&nbsp;<a title='edit' href='$CFG->wwwroot/mod/modmediawiki/servers.php?id=$server->id&amp;mode=edit&amp;sesskey=".sesskey()."'>Edit</a>";
        $html .= "&nbsp;<a title='delete' href='$CFG->wwwroot/mod/modmediawiki/servers.php?id=$server->id&amp;mode=delete_server&amp;sesskey=".sesskey()."'>Delete</a>";
        $html .= "</td></tr>";
        $odd = $odd ? false : true;
    }
    $html .= "</tbody></table>";
    $html .= "<a title='".get_string("new_server","modmediawiki")."' href='$CFG->wwwroot/mod/modmediawiki/servers.php'>".get_string("new_server","modmediawiki")."</a>";
    $settings->add(new admin_setting_heading('modmediawiki_servers_header', get_string('mediawiki_rest_api_server_manager', 'modmediawiki'), $html));
    //var_dump($servers);
}
