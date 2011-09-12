<?php

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    $users = $DB->get_records_sql('SELECT * FROM {modmendeley_users}');
    $html = "<table style='border: 1px solid;'><thead align='center'><tr style='background-color: #a0a0a0;'><td style='border-bottom: 1px solid gray;'>".get_string('name','modmendeley')."</td><td style='border-bottom: 1px solid gray;'>URL</td><td style='border-bottom: 1px solid gray;'>".get_string('actions','modmendeley')."</td></tr></thead><tbody>";
    $odd = true;
    if (count($users)) {
        foreach ($users as $user) {
            $background = $odd ? 'transparent' : '#e0e0e0';
            $html .= "<tr style='background-color: ".$background.";'><td>".$user->name."</td><td>".$user->url."</td><td>";
            if ( $user->consumer_key == null) {
            $html .= "<a title='register' href='$CFG->wwwroot/mod/modmendeley/users.php?id=$user->id&amp;mode=register&amp;sesskey=".sesskey()."'>Register</a>";
            } elseif ( $user->oauth && $user->request_token == null && $user->access_token == null) {
            $html .= "<a title='request_token' href='$CFG->wwwroot/mod/modmendeley/users.php?id=$user->id&amp;mode=request_token&amp;sesskey=".sesskey()."'>Request</a>";
            } elseif ( $user->oauth && $user->access_token == null) {
            $html .= "<a title='authorize' href='$CFG->wwwroot/mod/modmendeley/users.php?id=$user->id&amp;mode=authorize&amp;sesskey=".sesskey()."' >Authorize</a>";
            } elseif ( $user->oauth && $user->consumer_key != null && $user->access_token != null) {
            $html .= get_string("authorized","modmendeley");
            }
            $html .= "&nbsp;<a title='edit' href='$CFG->wwwroot/mod/modmendeley/users.php?id=$user->id&amp;mode=edit&amp;sesskey=".sesskey()."'>Edit</a>";
            $html .= "&nbsp;<a title='delete' href='$CFG->wwwroot/mod/modmendeley/users.php?id=$user->id&amp;mode=delete_user&amp;sesskey=".sesskey()."'>Delete</a>";
            $html .= "</td></tr>";
            $odd = $odd ? false : true;
        }
    } else {
        $html .= "<tr style='background-color: #e0e0e0;'><td colspan=3>".get_string('no_user','modmendeley')."</td></tr>";
    }
    $html .= "</tbody></table>";
    $html .= "<a title='".get_string("new_user","modmendeley")."' href='$CFG->wwwroot/mod/modmendeley/users.php'>".get_string("new_user","modmendeley")."</a>";
    $settings->add(new admin_setting_heading('modmendeley_users_header', get_string('mendeley_rest_api_user_manager', 'modmendeley'), $html));
}
