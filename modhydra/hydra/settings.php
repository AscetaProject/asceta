<?php

/**
* Settings
*
*
 * @package   mod_hydra
 * @copyright 2013 María del Mar Jiménez Torres (mjimenez@fidesol.org) - Fundación I+D del Software Libre (www.fidesol.org)
 \* @license   http://www.apache.org/licenses/LICENSE-2.0.html Apache License, Version 2.0
*/

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    $data_hydra = $DB->get_records_sql('SELECT * FROM {hydra_api} ORDER BY id DESC LIMIT 1');
    if (count($data_hydra)) {
        $html = "<div><label>Hydra api url: </label>";
        foreach ($data_hydra as $data) {
            $html .= ''.$data->apiurl.'';
        }
        $html .= "</div><br><div><a title='new' href='$CFG->wwwroot/mod/hydra/data.php?id=$data->id'>Change hydra url</a></div>";
    } else {
        $html = "<div><label>No Hydra api url configured</label>";
        $html .= "</div><br><div><a title='edit' href='$CFG->wwwroot/mod/hydra/data.php'>Set hydra url</a></div>";
    }    
    $settings->add(new admin_setting_heading('hydra_servers_header', get_string('hydra_rest_api_server_manager', 'hydra'), $html));
}
