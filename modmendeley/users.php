<?php

/// This file allows to manage the default behaviour of the display formats

require_once("../../config.php");
require_once($CFG->libdir.'/adminlib.php');
require_once("lib.php");
require_once("locallib.php");
require_once("OAuth.php");

$id   = optional_param('id', '', PARAM_INT);
$mode = optional_param('mode', '', PARAM_ACTION);

$url = new moodle_url('/mod/modmendeley/users.php', array('id'=>$id));
if ($mode !== '') {
    $url->param('mode', $mode);
}
$PAGE->set_url($url);
global $DB;

admin_externalpage_setup('managemodules'); // this is hacky, tehre should be a special hidden page for it

$form = data_submitted();

if ($id != '') {
    $user = $DB->get_records("modmendeley_users", array('id'=>$id));
}


// --------------------------  SAVE NEW USER ----------------------------------------------------------------
if ($mode == 'save_new' and $form and confirm_sesskey()) {
    if (!strlen($form->user_name)) {
        echo print_string('user_name_empty', 'modmendeley');
        die;
    } elseif (!strlen($form->user_url)) {
        echo print_string('user_url_empty', 'modmendeley');
        die;
    } else {
    $dataobject = array();
    $dataobject['name'] = $form->user_name;
    $dataobject['url'] = $form->user_url;
    $dataobject['oauth'] = (isset($form->oauth) ? 1 : 0);
    $user_id = $DB->insert_record('modmendeley_users', $dataobject, true, false);
    redirect("$CFG->wwwroot/$CFG->admin/settings.php?section=modsettingmodmendeley#modmendeley_users_header");
    die;
    }



// -------------------------- MODIFY USER ----------------------------------------------------------------
} elseif ($mode == 'save_edit' and $form and confirm_sesskey()) {
    if (!strlen($form->user_name)) {
        echo print_string('user_name_empty', 'modmendeley');
        die;
    } elseif (!strlen($form->user_url)) {
        echo print_string('user_url_empty', 'modmendeley');
        die;
    } else {
    $dataobject = array();
    $dataobject['id'] = $form->id;
    $dataobject['name'] = $form->user_name;
    $dataobject['url'] = $form->user_url;
    $dataobject['consumer_key'] = $form->consumer_key;
    $dataobject['consumer_secret'] = $form->consumer_secret;
    $dataobject['request_token'] = $form->request_token;
    $dataobject['request_secret'] = $form->request_secret;
    $dataobject['access_token'] = $form->access_token;
    $dataobject['access_secret'] = $form->access_secret;
    $dataobject['oauth'] = (isset($form->oauth) ? 1 : 0);
    $DB->update_record('modmendeley_users', $dataobject, false, false);
    redirect("$CFG->wwwroot/$CFG->admin/settings.php?section=modsettingmodmendeley#modmendeley_users_header");
    die;
    }    




// -------------------------- DELETE USER ----------------------------------------------------------------
} elseif ( $mode == 'delete_user' and $id != '' and confirm_sesskey()) {
    $DB->delete_records("modmendeley_users", array('id'=>$id));
    redirect("$CFG->wwwroot/$CFG->admin/settings.php?section=modsettingmodmendeley#modmendeley_users_header");
    die;

} elseif ( $mode == 'edit' and $id != '' and confirm_sesskey()) {
    $user = $DB->get_records("modmendeley_users", array('id'=>$id));

// -------------------------- vvv New/Edit USER Form vvv ----------------------------------------------------------------
} elseif ( $mode == 'register' and $id != '' and confirm_sesskey()) {
    
    //$url = rtrim($user[$id]->url,'/').'/Página_Principal';
    $url = 'http://dev.mendeley.com/applications';
    $params = array('application_uri' => "$CFG->wwwroot/mod/modmendeley/users.php.php?mode=access&id=$id&sesskey=".sesskey(), 'application_title' => $CFG->wwwroot);
    $params = implode_assoc('=', '&', $params);
} elseif ( $mode == 'save_register' and $id != '' and confirm_sesskey()) {
    $dataobject = array();
    $dataobject['id'] = $form->id;
    $dataobject['consumer_key'] = $form->consumer_key;
    $dataobject['consumer_secret'] = $form->consumer_secret;
    $DB->update_record('modmendeley_users', $dataobject, false, false);
    //redirect("$CFG->wwwroot/$CFG->admin/settings.php?section=modsettingmodmendeley#modmendeley_users_header");
    if ($user[$id]->oauth == "0"){
        redirect("$CFG->wwwroot/$CFG->admin/settings.php?section=modsettingmodmendeley#modmendeley_users_header");
    } else{
        redirect("$CFG->wwwroot/mod/modmendeley/users.php?id=$form->id&mode=request_token&sesskey=".sesskey());
    }
    die;
} elseif ( $mode == 'authorize' and $id != '' and confirm_sesskey()) {

    //$url = rtrim($user[$id]->url,'/').'/Página_Principal';
    $url = 'http://www.mendeley.com/oauth/authorize/';
    $params = array('oauth_token' => $user[$id]->request_token);
    $params = implode_assoc('=', '&', $params);
} elseif ( $mode == 'save_verifier'and $id != '' and confirm_sesskey()) {
    $dataobject = array();
    $dataobject['id'] = $form->id;
    $dataobject['verifier'] = $form->verifier;
    $DB->update_record('modmendeley_users', $dataobject, false, false);
    //redirect("$CFG->wwwroot/$CFG->admin/settings.php?section=modsettingmodmendeley#modmendeley_users_header");
    redirect("$CFG->wwwroot/mod/modmendeley/users.php?id=$form->id&mode=access&sesskey=".sesskey());
    die;
} elseif ( $mode == 'request_token' and $id != '' and confirm_sesskey() ) {
    $consumer_key = $user[$id]->consumer_key;
    $consumer_secret = $user[$id]->consumer_secret;
    $basefeed = 'http://www.mendeley.com/oauth/request_token/';
    $consumer = new OAuthConsumer($consumer_key, $consumer_secret, NULL);
    $request = OAuthRequest::from_consumer_and_token($consumer, NULL, 'GET', $basefeed, array());
    $request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $consumer, NULL);
    $response = modmendeley_send_request($request->get_normalized_http_method(), $basefeed, $request->to_header());
    $response_data = explode_assoc("=","&",$response);
    $dataobject = array();
    $dataobject['id'] = $id;
    $dataobject['request_token'] = $response_data['oauth_token'];
    $dataobject['request_secret'] = $response_data['oauth_token_secret'];
    $DB->update_record('modmendeley_users', $dataobject, false, false);
    //redirect("$CFG->wwwroot/$CFG->admin/settings.php?section=modsettingmodmendeley#modmendeley_users_header");
    redirect("$CFG->wwwroot/mod/modmendeley/users.php?id=$id&mode=authorize&sesskey=".sesskey());
    die;
} elseif ( $mode == 'auth' and $id != '' and confirm_sesskey() ) {
    $consumer_key = $user[$id]->consumer_key;
    $consumer_secret = $user[$id]->consumer_secret;
    $request_token = $user[$id]->request_token;
    $request_secret = $user[$id]->request_secret;
    //$basefeed = rtrim($user[$id]->url,'/').'/auth';
    $basefeed = 'http://www.mendeley.com/oauth/authorize/';
    $consumer = new OAuthConsumer($consumer_key, $consumer_secret, NULL);
    $token = new OAuthToken($request_token, $request_secret, NULL);
    $request = OAuthRequest::from_consumer_and_token($consumer, $token, 'GET', $basefeed, array());
    $request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $consumer, $token);
    redirect("$CFG->wwwroot/$CFG->admin/settings.php?section=modsettingmodmendeley#modmendeley_users_header");
    //$url = $request->to_url();
    //redirect($url);
    die;
} elseif ( $mode == 'access' and $id != '' and confirm_sesskey() ) {
    $consumer_key = $user[$id]->consumer_key;
    $consumer_secret = $user[$id]->consumer_secret;
    $request_token = $user[$id]->request_token;
    $request_secret = $user[$id]->request_secret;
    $verifier_code = $user[$id]->verifier;
    //$basefeed = rtrim($user[$id]->url,'/').'/access-token';
    $basefeed = 'http://www.mendeley.com/oauth/access_token/';
    $consumer = new OAuthConsumer($consumer_key, $consumer_secret, NULL);
    $token = new OAuthToken($request_token, $request_secret, NULL);
    $request = OAuthRequest::from_consumer_and_token($consumer, $token, 'GET', $basefeed, array('oauth_verifier' => $verifier_code));
    $request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $consumer, $token);
    $response = modmendeley_send_request($request->get_normalized_http_method(), $basefeed, $request->to_header());
    $response_data = explode_assoc("=","&",$response);
    $dataobject = array();
    $dataobject['id'] = $id;
    $dataobject['access_token'] = $response_data['oauth_token'];
    $dataobject['access_secret'] = $response_data['oauth_token_secret'];
    $dataobject['request_token'] = "";
    $dataobject['request_secret'] = "";
    $DB->update_record('modmendeley_users', $dataobject, false, false);
    redirect("$CFG->wwwroot/$CFG->admin/settings.php?section=modsettingmodmendeley#modmendeley_users_header");
    die;
}

$strmodulename = get_string("modulename", "modmendeley");
$yes = get_string("yes");
$no  = get_string("no");






// -------------------------- OAUTH REGISTER FORM ----------------------------------------------------------------
if ($mode == 'register') {

    echo $OUTPUT->header();
    echo $OUTPUT->heading($strmodulename . ': ' . get_string("register_user","modmendeley"));
    
    echo $OUTPUT->box(get_string("register_help_message", 'modmendeley')."<br><br><a href='$url' target='_BLANK'>".get_string("register_your_app","modmendeley")."</a>", "generalbox boxaligncenter boxwidthnormal");

    echo '<form method="post" action="users.php" id="form">';
    echo '<table width="60%" align="center" class="generalbox">';
    ?>
    <tr>
        <td>
	<label for="consumer_key"><?php echo print_string('consumer_key','modmendeley'); ?></label>
        </td>
        <td>
	<input name="consumer_key" id="consumer_key" size="40" />
        </td>
    </tr>
    <?php
        if ($user[$id]->oauth == "1"){
    ?>
    <tr>
        <td>
	<label for="consumer_secret"><?php echo print_string('consumer_secret','modmendeley'); ?></label>
        </td>
        <td>
	<input name="consumer_secret" id="consumer_secret" size="40" />
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
    <input type='hidden' name='mode'    value='save_register' />

<?php
    echo '</table>';
    echo '</form>';
    echo $OUTPUT->footer();

// -------------------------- OAUTH AUTHORIZE FORM ----------------------------------------------------------------
} elseif ($mode == 'authorize') {
    echo $OUTPUT->header();
    echo $OUTPUT->heading($strmodulename . ': ' . get_string("verifier_user","modmendeley"));

    echo $OUTPUT->box(get_string("authorize_help_message", 'modmendeley')."<br><br><a href='$url?$params' target='_BLANK'>".get_string("authorize_your_app","modmendeley")."</a>", "generalbox boxaligncenter boxwidthnormal");

    echo '<form method="post" action="users.php" id="form">';
    echo '<table width="60%" align="center" class="generalbox">';
    ?>
    <tr>
        <td>
	<label for="oauth verifier"><?php echo print_string('verifier','modmendeley'); ?></label>
        </td>
        <td>
	<input name="verifier" id="consumer_key" size="40" />
        </td>
    </tr>
    <tr>
        <td colspan="3" align="left">
	<input type="submit" value="<?php print_string("savechanges") ?>" />
        </td>
    </tr>
    <input type="hidden" name="id"    value="<?php p($id) ?>" />
    <input type="hidden" name="sesskey" value="<?php echo sesskey() ?>" />
    <input type='hidden' name='mode'    value='save_verifier' />

<?php
    echo '</table>';
    echo '</form>';
    echo $OUTPUT->footer();

// -------------------------- NEW/EDIT user FORM ----------------------------------------------------------------
}else {
    echo $OUTPUT->header();
    if ($mode == "edit")
        echo $OUTPUT->heading($strmodulename . ': ' . get_string("edit_user","modmendeley"));
    else
        echo $OUTPUT->heading($strmodulename . ': ' . get_string("new_user","modmendeley"));


    echo '<form name="userform" method="post" action="users.php" onsubmit="return userform_validation();" id="userform">';
    echo '<table width="60%" align="center" class="generalbox">';
    ?>
    <tr>
        <td>
	<label for="user_name"><?php echo print_string('name','modmendeley'); ?></label>
        </td>
        <td>
	<input name="user_name" id="user_name" size="60" <?php if ($mode == "edit" || $mode == "public_user") echo "value='".$user[$id]->name."'"; ?>/>
        </td>
    </tr>
    <tr>
        <td>
	<label for="user_url">URL</label>
        </td>
        <td>
	<input name="user_url" id="user_url" size="60" <?php if ($mode == "edit" || $mode == "public_user") echo "value='".$user[$id]->url."'"; ?> />
        </td>
    </tr>
        <tr>
        <td>
	<label for="oauth"><?php echo print_string('requires_oauth','modmendeley'); ?></label>
        </td>
        <td>
	<input type="checkbox" name="oauth" id="oauth" size="60" value="1" <?php if (($mode == "edit" || $mode == "public_user") && $user[$id]->oauth)
        echo "checked='checked'"; ?> />
        </td>
    </tr>
    <?php
        if ($mode == "edit") {
    ?>
        <tr>
	<td>
	    <label for="consumer_key"><?php echo print_string('consumer_key','modmendeley'); ?></label>
	</td>
	<td>
	    <input name="consumer_key" id="consumer_key" size="40" <?php echo "value='".$user[$id]->consumer_key."'"; ?> />
	</td>
        </tr>
        <tr>
	<td>
	    <label for="consumer_secret"><?php echo print_string('consumer_secret','modmendeley'); ?></label>
	</td>
	<td>
	    <input name="consumer_secret" id="consumer_secret" size="40" <?php echo "value='".$user[$id]->consumer_secret."'"; ?> />
	</td>
        </tr>
        <tr>
	<td>
	    <label for="request_token"><?php echo print_string('request_token','modmendeley'); ?></label>
	</td>
	<td>
	    <input name="request_token" id="request_token" size="40" <?php echo "value='".$user[$id]->request_token."'"; ?> />
	</td>
        </tr>
        <tr>
	<td>
	    <label for="request_secret"><?php echo print_string('request_secret','modmendeley'); ?></label>
	</td>
	<td>
	    <input name="request_secret" id="request_secret" size="40" <?php echo "value='".$user[$id]->request_secret."'"; ?> />
	</td>
        </tr>
        <tr>
	<td>
	    <label for="verifier"><?php echo print_string('verifier','modmendeley'); ?></label>
	</td>
	<td>
	    <input name="verifier" id="request_secret" size="40" <?php echo "value='".$user[$id]->verifier."'"; ?> />
	</td>
        </tr>
        <tr>
	<td>
	    <label for="access_token"><?php echo print_string('access_token','modmendeley'); ?></label>
	</td>
	<td>
	    <input name="access_token" id="access_token" size="40" <?php echo "value='".$user[$id]->access_token."'"; ?> />
	</td>
        </tr>
        <tr>
	<td>
	    <label for="access_secret"><?php echo print_string('access_secret','modmendeley'); ?></label>
	</td>
	<td>
	    <input name="access_secret" id="access_secret" size="40" <?php echo "value='".$user[$id]->access_secret."'"; ?> />
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
    function userform_validation() {
        if (document.userform.user_name.value.length == 0) {
	alert("<?php echo print_string('user_name_empty', 'modmendeley'); ?>");
	document.userform.user_name.focus();
	return false;
        }
        if (document.userform.user_url.value.length == 0) {
	alert("<?php echo print_string('user_url_empty', 'modmendeley'); ?>");
	document.userform.user_url.focus();
	return false;
        }
        document.userform.submit();
    }
</script>