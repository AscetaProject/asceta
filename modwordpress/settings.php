<?php

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    $servers = $DB->get_records_sql('SELECT * FROM {modwordpress_servers}');
    $html = "<table style='border: 1px solid;'><thead align='center'><tr style='background-color: #a0a0a0;'><td style='border-bottom: 1px solid gray;'>".get_string('name','modwordpress')."</td><td style='border-bottom: 1px solid gray;'>URL</td><td style='border-bottom: 1px solid gray;'>".get_string('actions','modwordpress')."</td></tr></thead><tbody>";
    $odd = true;
    foreach ($servers as $server) {
        $background = $odd ? 'transparent' : '#e0e0e0';
        $html .= "<tr style='background-color: ".$background.";'><td>".$server->name."</td><td>".$server->url."</td><td>";
        if ( $server->consumer_key == null) {
	$html .= "<button name='register'>".get_string("register",'modwordpress')."</button>";
        } elseif ( $server->request_token == null) {
	$html .= "<button name='request'>".get_string("request",'modwordpress')."</button>";
        } elseif ( $server->access_token == null) {
	$html .= "<button name='authorize'>".get_string("authorize",'modwordpress')."</button>";
        }
        $html .= "</td></tr>";
        $odd = $odd ? false : true;
    }
    $html .= "</tbody></table>";

    $html .= "<button name='new_server'>".get_string("new_server","modwordpress")."</button>";
    $settings->add(new admin_setting_heading('modwordpress_servers_header', get_string('wordpress_rest_api_server_manager', 'modwordpress'), $html));
    var_dump($servers);
}


