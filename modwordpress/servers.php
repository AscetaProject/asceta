<?php
/// This file allows to manage the default behaviour of the display formats

require_once("../../config.php");
require_once($CFG->libdir . '/adminlib.php');
require_once("lib.php");
require_once("locallib.php");
require_once("OAuth.php");

$id = optional_param('id', '', PARAM_INT);
$mode = optional_param('mode', '', PARAM_ACTION);

$url = new moodle_url('/mod/modwordpress/servers.php', array('id' => $id));
if ($mode !== '') {
    $url->param('mode', $mode);
}
$PAGE->set_url($url);
global $DB;

admin_externalpage_setup('managemodules'); // this is hacky, tehre should be a special hidden page for it

$form = data_submitted();

if ($id != '') {
    $server = $DB->get_records("modwordpress_servers", array('id' => $id));
}


// --------------------------  SAVE NEW SERVER ----------------------------------------------------------------
if ($mode == 'save_new' and $form and confirm_sesskey()) {
    if (!strlen($form->server_name)) {
        echo print_string('server_name_empty', 'modwordpress');
        die;
    } elseif (!strlen($form->server_url)) {
        echo print_string('server_url_empty', 'modwordpress');
        die;
    } else {
        $dataobject = array();
        $dataobject['name'] = $form->server_name;
        $dataobject['url'] = $form->server_url;
        $dataobject['oauth'] = (isset($form->oauth) ? 1 : 0);
        $DB->insert_record('modwordpress_servers', $dataobject, false, false);
        redirect("$CFG->wwwroot/$CFG->admin/settings.php?section=modsettingmodwordpress#modwordpress_servers_header");
        die;
    }



// -------------------------- MODIFY SERVER ----------------------------------------------------------------
} elseif ($mode == 'save_edit' and $form and confirm_sesskey()) {
    if (!strlen($form->server_name)) {
        echo print_string('server_name_empty', 'modwordpress');
        die;
    } elseif (!strlen($form->server_url)) {
        echo print_string('server_url_empty', 'modwordpress');
        die;
    } else {
        $dataobject = array();
        $dataobject['id'] = $form->id;
        $dataobject['name'] = $form->server_name;
        $dataobject['url'] = $form->server_url;
        $dataobject['consumer_key'] = $form->consumer_key;
        $dataobject['consumer_secret'] = $form->consumer_secret;
        $dataobject['request_token'] = $form->request_token;
        $dataobject['request_secret'] = $form->request_secret;
        $dataobject['access_token'] = $form->access_token;
        $dataobject['access_secret'] = $form->access_secret;
        $dataobject['oauth'] = (isset($form->oauth) ? 1 : 0);
        $DB->update_record('modwordpress_servers', $dataobject, false, false);
        redirect("$CFG->wwwroot/$CFG->admin/settings.php?section=modsettingmodwordpress#modwordpress_servers_header");
        die;
    }



// -------------------------- DELETE SERVER ----------------------------------------------------------------
} elseif ($mode == 'delete_server' and $id != '' and confirm_sesskey()) {
    $DB->delete_records("modwordpress_servers", array('id' => $id));
    redirect("$CFG->wwwroot/$CFG->admin/settings.php?section=modsettingmodwordpress#modwordpress_servers_header");
    die;
} elseif ($mode == 'edit' and $id != '' and confirm_sesskey()) {
    $server = $DB->get_records("modwordpress_servers", array('id' => $id));

// -------------------------- vvv New/Edit Server Form vvv ----------------------------------------------------------------
} elseif ($mode == 'register' and $id != '' and confirm_sesskey()) {

    $url = rtrim($server[$id]->url, '/') . '/register';
    $params = array('callback_uri' => "$CFG->wwwroot/mod/modwordpress/servers.php?mode=access&id=$id&sesskey=" . sesskey(), 'application_uri' => $CFG->wwwroot, 'application_title' => $CFG->wwwroot, 'application_type' => 'website', 'application_commercial' => 0);
    $params = implode_assoc('=', '&', $params);
} elseif ($mode == 'save_register' and $id != '' and confirm_sesskey()) {
    $dataobject = array();
    $dataobject['id'] = $form->id;
    $dataobject['consumer_key'] = $form->consumer_key;
    $dataobject['consumer_secret'] = $form->consumer_secret;
    $DB->update_record('modwordpress_servers', $dataobject, false, false);
    //redirect("$CFG->wwwroot/$CFG->admin/settings.php?section=modsettingmodwordpress#modwordpress_servers_header");
    redirect("$CFG->wwwroot/mod/modwordpress/servers.php?id=$form->id&mode=request_token&sesskey=" . sesskey());
    die;
} elseif ($mode == 'request_token' and $id != '' and confirm_sesskey()) {
    $consumer_key = $server[$id]->consumer_key;
    $consumer_secret = $server[$id]->consumer_secret;
    $basefeed = rtrim($server[$id]->url, '/') . '/request-token';
    $consumer = new OAuthConsumer($consumer_key, $consumer_secret, NULL);
    $request = OAuthRequest::from_consumer_and_token($consumer, NULL, 'GET', $basefeed, array());
    $request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $consumer, NULL);
    $response = send_request($request->get_normalized_http_method(), $basefeed, $request->to_header());
    $response_data = explode_assoc("=", "&", $response);
    $dataobject = array();
    $dataobject['id'] = $id;
    $dataobject['request_token'] = $response_data['oauth_token'];
    $dataobject['request_secret'] = $response_data['oauth_token_secret'];
    $DB->update_record('modwordpress_servers', $dataobject, false, false);
    //redirect("$CFG->wwwroot/$CFG->admin/settings.php?section=modsettingmodwordpress#modwordpress_servers_header");
    redirect("$CFG->wwwroot/mod/modwordpress/servers.php?id=$id&mode=auth&sesskey=" . sesskey());
    die;
} elseif ($mode == 'auth' and $id != '' and confirm_sesskey()) {
    $consumer_key = $server[$id]->consumer_key;
    $consumer_secret = $server[$id]->consumer_secret;
    $request_token = $server[$id]->request_token;
    $request_secret = $server[$id]->request_secret;
    $basefeed = rtrim($server[$id]->url, '/') . '/auth';
    $consumer = new OAuthConsumer($consumer_key, $consumer_secret, NULL);
    $token = new OAuthToken($request_token, $request_secret, NULL);
    $request = OAuthRequest::from_consumer_and_token($consumer, $token, 'GET', $basefeed, array());
    $request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $consumer, $token);
    $url = $request->to_url();
    redirect($url);
    die;
} elseif ($mode == 'access' and $id != '' and confirm_sesskey()) {
    $consumer_key = $server[$id]->consumer_key;
    $consumer_secret = $server[$id]->consumer_secret;
    $request_token = $server[$id]->request_token;
    $request_secret = $server[$id]->request_secret;
    $basefeed = rtrim($server[$id]->url, '/') . '/access-token';
    $consumer = new OAuthConsumer($consumer_key, $consumer_secret, NULL);
    $token = new OAuthToken($request_token, $request_secret, NULL);
    $request = OAuthRequest::from_consumer_and_token($consumer, $token, 'GET', $basefeed, array());
    $request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $consumer, $token);
    $response = send_request($request->get_normalized_http_method(), $basefeed, $request->to_header());
    $response_data = explode_assoc("=", "&", $response);
    $dataobject = array();
    $dataobject['id'] = $id;
    $dataobject['access_token'] = $response_data['oauth_token'];
    $dataobject['access_secret'] = $response_data['oauth_token_secret'];
    $dataobject['request_token'] = "";
    $dataobject['request_secret'] = "";
    $DB->update_record('modwordpress_servers', $dataobject, false, false);
    redirect("$CFG->wwwroot/$CFG->admin/settings.php?section=modsettingmodwordpress#modwordpress_servers_header");
    die;
}

$strmodulename = get_string("modulename", "modwordpress");
$yes = get_string("yes");
$no = get_string("no");






// -------------------------- OAUTH REGISTER FORM ----------------------------------------------------------------
if ($mode == 'register') {

    echo $OUTPUT->header();
    echo $OUTPUT->heading($strmodulename . ': ' . get_string("register_server", "modwordpress"));

    echo $OUTPUT->box(get_string("register_help_message", 'modwordpress') . "<br><br><a href='$url?$params' target='_BLANK'>" . get_string("register_your_app", "modwordpress") . "</a>", "generalbox boxaligncenter boxwidthnormal");

    echo '<form method="post" action="servers.php" id="form">';
    echo '<table width="60%" align="center" class="generalbox">';
?>
    <tr>
        <td>
            <label for="consumer_key"><?php echo print_string('consumer_key', 'modwordpress'); ?></label>
        </td>
        <td>
            <input name="consumer_key" id="consumer_key" size="40" />
        </td>
    </tr>
    <tr>
        <td>
            <label for="consumer_secret"><?php echo print_string('consumer_secret', 'modwordpress'); ?></label>
        </td>
        <td>
            <input name="consumer_secret" id="consumer_secret" size="40" />
        </td>
    </tr>
    <tr>
        <td colspan="3" align="left">
            <input type="submit" value="<?php print_string("savechanges") ?>" />
        </td>
    </tr>
    <input type="hidden" name="id"    value="<?php p($id) ?>" />
    <input type="hidden" name="sesskey" value="<?php echo sesskey() ?>" />
    <input type='hidden' name='mode'    value='save_register' />

<?php
    echo '</table>';
    echo '</form>';
    echo $OUTPUT->footer();









// -------------------------- NEW/EDIT SERVER FORM ----------------------------------------------------------------
} else {
    echo $OUTPUT->header();
    if ($mode == "edit")
        echo $OUTPUT->heading($strmodulename . ': ' . get_string("edit_server", "modwordpress"));
    else
        echo $OUTPUT->heading($strmodulename . ': ' . get_string("new_server", "modwordpress"));

    echo '<form name="serverform" method="post" action="servers.php" onsubmit="return serverform_validation();" id="serverform">';
    echo '<table width="60%" align="center" class="generalbox">';
?>
    <tr>
        <td>
            <label for="server_name"><?php echo print_string('name', 'modwordpress'); ?></label>
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
            <label for="oauth"><?php echo print_string('requires_oauth', 'modwordpress'); ?></label>
        </td>
        <td>
            <input type="checkbox" name="oauth" id="oauth" size="60" value="1" <?php if ($mode == "edit" && $server[$id]->oauth)
        echo "checked='checked'"; ?>/>
        </td>
    </tr>
<?php
    if ($mode == "edit") {
?>
        <tr>
            <td>
                <label for="consumer_key"><?php echo print_string('consumer_key', 'modwordpress'); ?></label>
            </td>
            <td>
                <input name="consumer_key" id="consumer_key" size="40" <?php echo "value='" . $server[$id]->consumer_key . "'"; ?> />
            </td>
        </tr>
        <tr>
            <td>
                <label for="consumer_secret"><?php echo print_string('consumer_secret', 'modwordpress'); ?></label>
            </td>
            <td>
                <input name="consumer_secret" id="consumer_secret" size="40" <?php echo "value='" . $server[$id]->consumer_secret . "'"; ?> />
            </td>
        </tr>
        <tr>
            <td>
                <label for="request_token"><?php echo print_string('request_token', 'modwordpress'); ?></label>
            </td>
            <td>
                <input name="request_token" id="request_token" size="40" <?php echo "value='" . $server[$id]->request_token . "'"; ?> />
            </td>
        </tr>
        <tr>
            <td>
                <label for="request_secret"><?php echo print_string('request_secret', 'modwordpress'); ?></label>
            </td>
            <td>
                <input name="request_secret" id="request_secret" size="40" <?php echo "value='" . $server[$id]->request_secret . "'"; ?> />
            </td>
        </tr>
        <tr>
            <td>
                <label for="access_token"><?php echo print_string('access_token', 'modwordpress'); ?></label>
            </td>
            <td>
                <input name="access_token" id="access_token" size="40" <?php echo "value='" . $server[$id]->access_token . "'"; ?> />
            </td>
        </tr>
        <tr>
            <td>
                <label for="access_secret"><?php echo print_string('access_secret', 'modwordpress'); ?></label>
            </td>
            <td>
                <input name="access_secret" id="access_secret" size="40" <?php echo "value='" . $server[$id]->access_secret . "'"; ?> />
            </td>
        </tr>
<?php
    }
?>
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
	alert("<?php echo print_string('server_name_empty', 'modwordpress'); ?>");
	document.serverform.server_name.focus();
	return false;
        }
        if (document.serverform.server_url.value.length == 0) {
	alert("<?php echo print_string('server_url_empty', 'modwordpress'); ?>");
	document.serverform.server_url.focus();
	return false;
        }
        document.serverform.submit();
    }
</script>