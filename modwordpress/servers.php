<?php
/// This file allows to manage the default behaviour of the display formats

require_once("../../config.php");
require_once($CFG->libdir . '/adminlib.php');
require_once("lib.php");
require_once("locallib.php");

$id = optional_param('id', '', PARAM_INT);
$mode = optional_param('mode', '', PARAM_ACTION);

$url = new moodle_url('/mod/modredmine/servers.php', array('id' => $id));
if ($mode !== '') {
    $url->param('mode', $mode);
}
$PAGE->set_url($url);
global $DB;

admin_externalpage_setup('managemodules'); // this is hacky, tehre should be a special hidden page for it

$form = data_submitted();

if ($id != '') {
    $server = $DB->get_records("modredmine_servers", array('id' => $id));
}


// --------------------------  SAVE NEW SERVER ----------------------------------------------------------------
if ($mode == 'save_new' and $form and confirm_sesskey()) {
    if (!strlen($form->server_name)) {
        echo print_string('server_name_empty', 'modredmine');
        die;
    } elseif (!strlen($form->server_url)) {
        echo print_string('server_url_empty', 'modredmine');
        die;
    } else {
        $dataobject = array();
        $dataobject['name'] = $form->server_name;
        $dataobject['url'] = $form->server_url;
        $dataobject['auth'] = (isset($form->auth) ? 1 : 0);
        if (isset($form->api_key)) $dataobject['api_key'] = $form->api_key;
        $DB->insert_record('modredmine_servers', $dataobject, false, false);
        redirect("$CFG->wwwroot/$CFG->admin/settings.php?section=modsettingmodredmine#modredmine_servers_header");
        die;
    }



// -------------------------- MODIFY SERVER ----------------------------------------------------------------
} elseif ($mode == 'save_edit' and $form and confirm_sesskey()) {
    if (!strlen($form->server_name)) {
        echo print_string('server_name_empty', 'modredmine');
        die;
    } elseif (!strlen($form->server_url)) {
        echo print_string('server_url_empty', 'modredmine');
        die;
    } else {
        $dataobject = array();
        $dataobject['id'] = $form->id;
        $dataobject['name'] = $form->server_name;
        $dataobject['url'] = $form->server_url;
        $dataobject['auth'] = (isset($form->auth) ? 1 : 0);
        if (isset($form->api_key)) $dataobject['api_key'] = $form->api_key;
        $DB->update_record('modredmine_servers', $dataobject, false, false);
        redirect("$CFG->wwwroot/$CFG->admin/settings.php?section=modsettingmodredmine#modredmine_servers_header");
        die;
    }



// -------------------------- DELETE SERVER ----------------------------------------------------------------
} elseif ($mode == 'delete_server' and $id != '' and confirm_sesskey()) {
    $DB->delete_records("modredmine_servers", array('id' => $id));
    redirect("$CFG->wwwroot/$CFG->admin/settings.php?section=modsettingmodredmine#modredmine_servers_header");
    die;
// -------------------------- vvv New/Edit Server Form vvv ----------------------------------------------------------------
} elseif ($mode == 'edit' and $id != '' and confirm_sesskey()) {
    $server = $DB->get_records("modredmine_servers", array('id' => $id));


} elseif ($mode == 'key_request' and $id != '' and confirm_sesskey()) {
    $url = rtrim($server[$id]->url, '/') . '/my/account';

} elseif ($mode == 'save_key' and $id != '' and confirm_sesskey()) {
    if (!strlen($form->api_key)) {
        echo print_string('key_empty', 'modredmine');
        die;
    } else {
        $dataobject = array();
        $dataobject['id'] = $form->id;
        $dataobject['api_key'] = $form->api_key;
        $DB->update_record('modredmine_servers', $dataobject, false, false);
        redirect("$CFG->wwwroot/$CFG->admin/settings.php?section=modsettingmodredmine#modredmine_servers_header");
        die;
    }

} elseif ($mode == 'getkey' and $id != '' and confirm_sesskey()) {
    $url = rtrim($server[$id]->url, '/') . '/issues.xml';
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($curl, CURLOPT_USERPWD, "admin:admin");
    $response = curl_exec($curl);
    if (!$response) {
        $response = curl_error($curl);
    }
    curl_close($curl);

    $dom = new DOMDocument();
    @$dom->loadHTML($response);
    echo $dom->saveHTML();
    $xpath = new DomXPath($dom);
    $nodes = $xpath->query("//*[@id='api-access-key']");
    foreach ($nodes as $node) {
          var_dump($node);
    }
    //$response_data = explode_assoc("=", "&", $response);
    //$DB->update_record('modredmine_servers', $dataobject, false, false);
    //redirect("$CFG->wwwroot/$CFG->admin/settings.php?section=modsettingmodredmine#modredmine_servers_header");
    //redirect("$CFG->wwwroot/mod/modredmine/servers.php?id=$id&mode=auth&sesskey=" . sesskey());
    die;

}

$strmodulename = get_string("modulename", "modredmine");
$yes = get_string("yes");
$no = get_string("no");






if ($mode == 'key_request') {

    echo $OUTPUT->header();
    echo $OUTPUT->heading($strmodulename . ': ' . get_string("authenticate", "modredmine"));

    echo $OUTPUT->box(get_string("redmine_login_message", 'modredmine') . "<br><br><a href='$url' target='_BLANK'>" . get_string("get_your_api_key", "modredmine") . "</a>", "generalbox boxaligncenter boxwidthnormal");

    echo '<form method="post" action="servers.php" id="form">';
    echo '<table width="60%" align="center" class="generalbox">';
?>
    <tr>
        <td>
            <label for="api_key"><?php echo print_string('key', 'modredmine'); ?></label>
        </td>
        <td>
            <input name="api_key" id="api_key" size="100" />
        </td>
    </tr>
    <tr>
        <td colspan="3" align="left">
            <input type="submit" value="<?php print_string("savechanges") ?>" />
        </td>
    </tr>
    <input type="hidden" name="id"    value="<?php p($id) ?>" />
    <input type="hidden" name="sesskey" value="<?php echo sesskey() ?>" />
    <input type='hidden' name='mode'    value='save_key' />

<?php
    echo '</table>';
    echo '</form>';
    echo $OUTPUT->footer();









// -------------------------- NEW/EDIT SERVER FORM ----------------------------------------------------------------
} else {
    echo $OUTPUT->header();
    if ($mode == "edit")
        echo $OUTPUT->heading($strmodulename . ': ' . get_string("edit_server", "modredmine"));
    else
        echo $OUTPUT->heading($strmodulename . ': ' . get_string("new_server", "modredmine"));

    echo '<form name="serverform" method="post" action="servers.php" onsubmit="return serverform_validation();" id="serverform">';
    echo '<table width="60%" align="center" class="generalbox">';
?>
    <tr>
        <td>
            <label for="server_name"><?php echo print_string('name', 'modredmine'); ?></label>
        </td>
        <td>
            <input name="server_name" id="server_name" size="60" <?php if ($mode == "edit")
        echo "value='" . $server[$id]->name . "'"; ?>/>
        </td>
    </tr>
    <tr>
        <td>
            <label for="server_url">URL</label>
        </td>
        <td>
            <input name="server_url" id="server_url" size="60" <?php if ($mode == "edit")
        echo "value='" . $server[$id]->url . "'"; ?> />
        </td>
    </tr>
    <tr>
        <td>
            <label for="auth"><?php echo print_string('requires_auth', 'modredmine'); ?></label>
        </td>
        <td>
            <input type="checkbox" name="auth" id="auth" size="60" value="1" <?php if ($mode == "edit" && $server[$id]->auth)
        echo "checked='checked'"; ?>/>
        </td>
    </tr>
    <tr>
        <td>
            <label for="api_key">API Key</label>
        </td>
        <td>
            <input name="api_key" id="api_key" size="60" <?php if ($mode == "edit")
        echo "value='" . $server[$id]->api_key . "'"; ?> />
        </td>
    </tr>



    <tr>
        <td colspan="3" align="left">
            <input type="submit" value="<?php print_string("savechanges") ?>" />
        </td>
    </tr>
    <input type="hidden" name="id"    value="<?php p($id) ?>" />
    <input type="hidden" name="sesskey" value="<?php echo sesskey() ?>" />
<?php
    if ($mode == "edit") {
        echo "<input type='hidden' name='mode'    value='save_edit' />";
    } else {
        echo "<input type='hidden' name='mode'    value='save_new' />";
    }
    echo '</table>';
    echo '</form>';
    echo $OUTPUT->footer();
}
?>

<script type="text/javascript">
    function serverform_validation() {
        if (document.serverform.server_name.value.length == 0) {
	alert("<?php echo print_string('server_name_empty', 'modredmine'); ?>");
	document.serverform.server_name.focus();
	return false;
        }
        if (document.serverform.server_url.value.length == 0) {
	alert("<?php echo print_string('server_url_empty', 'modredmine'); ?>");
	document.serverform.server_url.focus();
	return false;
        }
        document.serverform.submit();
    }
</script>
