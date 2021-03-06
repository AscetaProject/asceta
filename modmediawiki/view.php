<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.


/**
 * Prints a particular instance of modmediawiki
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package   mod_modmediawiki
 * @copyright 2011 María del Mar Jiménez Torres (mjimenez@fidesol.org) - Fundación I+D del Software Libre (www.fidesol.org)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/// (Replace modmediawiki with the name of your module and remove this line)

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(__FILE__).'/locallib.php');
require_once(dirname(__FILE__).'/OAuth.php');

$id = optional_param('id', 0, PARAM_INT); // course_module ID, or
$n  = optional_param('n', 0, PARAM_INT);  // modmediawiki instance ID - it should be named as the first character of the module
$page = optional_param('page', 0, PARAM_INT);
$new_page = optional_param('new_page', '', PARAM_TEXT); // New Page
$edit_page = optional_param('edit_page', '', PARAM_TEXT); // New Page
$page_title = optional_param('page_title', '', PARAM_TEXT); // Page title
$page_content = optional_param('page_content', '', PARAM_TEXT); // Page content
$page_resume = optional_param('page_resume', '', PARAM_TEXT); //Page resume

if ($id) {
    $cm         = get_coursemodule_from_id('modmediawiki', $id, 0, false, MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $modmediawiki  = $DB->get_record('modmediawiki', array('id' => $cm->instance), '*', MUST_EXIST);
} elseif ($n) {
    $modmediawiki  = $DB->get_record('modmediawiki', array('id' => $n), '*', MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $modmediawiki->course), '*', MUST_EXIST);
    $cm         = get_coursemodule_from_instance('modmediawiki', $modmediawiki->id, $course->id, false, MUST_EXIST);
} else {
    error('You must specify a course_module ID or an instance ID');
}

if ($modmediawiki->server_id) {
    $server = $DB->get_record('modmediawiki_servers', array('id' => $modmediawiki->server_id), '*', MUST_EXIST);
}
require_login($course, true, $cm);

//add_to_log($course->id, 'modmediawiki', 'view', "view.php?id=$cm->id", $modmediawiki->name, $cm->id);

/// Print the page header

$PAGE->set_url('/mod/modmediawiki/view.php', array('id' => $cm->id));
$PAGE->set_title($modmediawiki->name);
$PAGE->set_heading($course->shortname);
$PAGE->set_button(update_module_button($cm->id, $course->id, get_string('modulename', 'modmediawiki')));

// other things you may want to set - remove if not needed
//$PAGE->set_cacheable(false);
//$PAGE->set_focuscontrol('some-html-id');

// Output starts here
echo $OUTPUT->header();

if (!$modmediawiki->server_id) {
    echo $OUTPUT->heading(get_string("configure_server_url","modmediawiki"));
} else {
    if ($error_message){
       echo '<script type="text/javascript">';
       echo 'alert("'.$error_message.'");';
       echo '</script>';
       redirect("$CFG->wwwroot/mod/modmediawiki/view.php?id=$cm->id");
       die;
    }
    if ($page_title != '' and $page_content != '' and !$edit_page and confirm_sesskey()) {
        $params = array('page_title' => $page_title, 'page_content' => $page_content, 'page_resume' => $page_resume);
        $consumer_key = $server->consumer_key;
        $consumer_secret = $server->consumer_secret;
        $access_token = $server->access_token;
        $access_secret = $server->access_secret;
        $consumer = new OAuthConsumer($consumer_key, $consumer_secret, NULL);
        $token = new OAuthToken($access_token, $access_secret, NULL);
        $basefeed = rtrim($server->url, '/') . "/pages";
        $request = OAuthRequest::from_consumer_and_token($consumer, $token, 'POST', $basefeed, $params);
        $request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $consumer, $token);
        $response = modmediawiki_send_request($request->get_normalized_http_method(), $basefeed, $request->to_header(), $params);
        $page_info = json_decode($response);
        if($page_info->Error){
            echo '<script>alert("'.$page_info->Message.'")</script>';
            echo '<script>javascript:history.back()</script>';
            die;
        }
        add_to_log($course->id, 'modmediawiki', 'create page', "view.php?id=$cm->id&page=$page_info->ID&sesskey=".  sesskey(), $modmediawiki->name, $cm->id);
        redirect("$CFG->wwwroot/mod/modmediawiki/view.php?id=$cm->id");
        die;
    } else if($page_title != '' and $page_content != '' and $edit_page and confirm_sesskey()){
        $params = array('page_title' => $page_title, 'page_content' => $page_content, 'page_resume' => $page_resume);
        $consumer_key = $server->consumer_key;
        $consumer_secret = $server->consumer_secret;
        $access_token = $server->access_token;
        $access_secret = $server->access_secret;
        $consumer = new OAuthConsumer($consumer_key, $consumer_secret, NULL);
        $token = new OAuthToken($access_token, $access_secret, NULL);
        $basefeed = rtrim($server->url, '/') . "/pages/$edit_page";
        $request = OAuthRequest::from_consumer_and_token($consumer, $token, 'PUT', $basefeed);
        $request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $consumer, $token);
        $response = modmediawiki_send_request($request->get_normalized_http_method(), $basefeed, $request->to_header(), $params);
        if($page_info->Error){
            echo '<script>alert("'.$page_info->Message.'")</script>';
            echo '<script>javascript:history.back()</script>';
            die;
        }
        add_to_log($course->id, 'modmediawiki', 'edit page', "view.php?id=$cm->id&page=$edit_page&sesskey=".sesskey(), $modmediawiki->name, $cm->id);
        redirect("$CFG->wwwroot/mod/modmediawiki/view.php?id=$cm->id");
        die;
    }
    if ( $new_page != '' and confirm_sesskey()) {
        echo $OUTPUT->heading($modmediawiki->name." : ".get_string("new_page","modmediawiki"));
        echo '<form name="new_page_form" method="post" action="view.php" id="new_page_form" onsubmit="return new_page_form_validation();">';
        echo '<table><thead></thead><tbody>';
        echo '<tr>';
        echo "<td><label for='page_title'>".get_string("title","modmediawiki")."</label></td>";
        echo "<td><input type='text' name='page_title' value='' size='80px' /></td>";
        echo "</tr><tr>";
        echo "<td colspan='2'><textarea cols=90 rows=10 name='page_content'></textarea></td>";
        echo "</tr><tr>";
        echo "<td><label for='page_resume'>".get_string("resumen", "modmediawiki")."</label></td>";
        echo "<td><input type='text'name='page_resume' value='' size='80px'/></td>";
        echo "</tr></tbody></table>";
        echo "<input type='submit' value='".get_string("save", "modmediawiki")."' />";
        echo "<input type='hidden' name='sesskey' value='" . sesskey() . "' />";
        echo "<input type='hidden' name='id' value='$cm->id' />";
        echo '</form>';
        //echo "<br/><br/><a style='margin: 5px 10px 20px 10px;' href='$CFG->wwwroot/mod/modmediawiki/view.php?id=$cm->id&amp;page=$edit_page&amp;sesskey=" . sesskey() . "'>".get_string('back','modmediawiki')."</a>";
        echo "<br/><br/><a style='margin: 5px 10px 20px 10px;' href='javascript:history.back()'>".get_string('back','modmediawiki')."</a>";
        echo '<script type="text/javascript">';
        echo 'function new_page_form_validation() {';
	echo 'if (document.new_page_form.page_title.value.length == 0) {';
            echo 'alert("Page title can not be empty");';
	    echo 'document.new_page_form.page_title.focus();';
	    echo 'return false; ';
	echo '}';
	echo 'if (document.new_page_form.page_content.value.length == 0) {';
	    echo 'alert("Page content can not be empty");';
	    echo 'document.new_page_form.page_content.focus();';
	    echo 'return false; ';
	echo '}';
        echo '}';
        echo '</script>';
    } elseif ($edit_page and confirm_sesskey()){
        $consumer = new OAuthConsumer($server->consumer_key, $server->consumer_secret, NULL);
        $token = new OAuthToken($server->access_token, $server->access_secret, NULL);
        $basefeed = rtrim($server->url, '/') . "/pages/$edit_page";
        $request = OAuthRequest::from_consumer_and_token($consumer, $token, 'GET', $basefeed, array());
        $request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $consumer, $token);
        $response = modmediawiki_send_request($request->get_normalized_http_method(), $basefeed, $request->to_header());
        $page_info = json_decode($response);
        echo $OUTPUT->heading($modmediawiki->name." : ".$page_info->page_title);
        echo '<form name="edit_page_form" method="post" action="view.php?edit_page='.$edit_page.'" id="edit_page_form" onsubmit="return edit_page_form_validation();">';
        echo '<table><thead></thead><tbody>';
        echo '<tr>';
        echo "<td colspan='2'><textarea cols=90 rows=10 name='page_content'>$page_info->page_content_wiki</textarea></td>";
        echo "</tr><tr>";
        echo "<td><label for='page_resume'>".get_string("resumen", "modmediawiki")."</label></td>";
        echo "<td><input type='text'name='page_resume' value='' size='80px'/></td>";
        echo "</tr></tbody></table>";
        echo "<input type='submit' value='".get_string("save", "modmediawiki")."' />";
        echo "<input type='hidden' name='page_title' value='$page_info->page_title' size='80px' />";
        echo "<input type='hidden' name='sesskey' value='" . sesskey() . "' />";
        echo "<input type='hidden' name='id' value='$cm->id' />";
        echo '</form>';
        //echo "<br/><br/><a style='margin: 5px 10px 20px 10px;' href='$CFG->wwwroot/mod/modmediawiki/view.php?id=$cm->id&amp;page=$edit_page&amp;sesskey=" . sesskey() . "'>".get_string('back','modmediawiki')."</a>";
        echo "<br/><br/><a style='margin: 5px 10px 20px 10px;' href='javascript:history.back()'>".get_string('back','modmediawiki')."</a>";
        echo " <script type='text/javascript'>";
        echo "function edit_page_form_validation() {";
	echo "if (document.edit_page_form.page_title.value.length == 0) {";
	    echo 'alert("Page title can not be empty");';
	    echo "document.edit_page_form.page_title.focus();";
	    echo "return false; ";
	echo "}";
	echo "if (document.edit_page_form.page_content.value.length == 0) {";
	    echo 'alert("Page content can not be empty");';
	    echo "document.edit_page_form.page_content.focus();";
	    echo "return false; ";
	echo "}";
        echo "}";
        echo "</script>";
    } elseif ($page and confirm_sesskey()) {
        $consumer = new OAuthConsumer($server->consumer_key, $server->consumer_secret, NULL);
        $token = new OAuthToken($server->access_token, $server->access_secret, NULL);
        $basefeed = rtrim($server->url, '/') . "/pages/$page";
        $request = OAuthRequest::from_consumer_and_token($consumer, $token, 'GET', $basefeed, array());
        $request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $consumer, $token);
        $response = modmediawiki_send_request($request->get_normalized_http_method(), $basefeed, $request->to_header());
        $page_info = json_decode($response);
        if (count($page_info)) {
	echo $OUTPUT->heading($modmediawiki->name." : ".$page_info->page_title);
	echo $page_info->page_content;
        echo "<div class='clearfix' style='margin: 5px 10px; color: gray; font-size: 90%;'> $page_info->page_resume</div>";
        }
        if($modmediawiki->permission_edit){
            echo "<div class='clearfix' style='margin: 5px 10px; color: gray; font-size: 90%;'>";
            echo "<a href='$CFG->wwwroot/mod/modmediawiki/view.php?id=$cm->id&amp;edit_page=$page&amp;sesskey=" . sesskey() . "'>" . get_string("edit_page", "modmediawiki") . "</a>";
            echo "</div>";
        }
        //echo "<br/><br/><a style='margin: 5px 10px 20px 10px;' href='$CFG->wwwroot/mod/modmediawiki/view.php?id=$cm->id'>" . get_string("back", "modmediawiki") . "</a><br/><br/>";
        echo "<br/><br/><a style='margin: 5px 10px 20px 10px;' href='javascript:history.back()'>".get_string('back','modmediawiki')."</a>";
        add_to_log($course->id, 'modmediawiki', 'view page', "view.php?id=$cm->id&page=$page&sesskey=".sesskey(), $modmediawiki->name, $cm->id);
    } else {
        echo $OUTPUT->heading($modmediawiki->name);
        if($modmediawiki->permission_create){
            echo "<a style='margin: 5px 10px 20px 10px;' href='$CFG->wwwroot/mod/modmediawiki/view.php?id=$cm->id&amp;new_page=page&amp;sesskey=" . sesskey() . "'>".get_string("new_page","modmediawiki")."</a>";
        }
        $consumer = new OAuthConsumer($server->consumer_key, $server->consumer_secret, NULL);
        $token = new OAuthToken($server->access_token, $server->access_secret, NULL);
        $basefeed = rtrim($server->url,'/').'/pages';
        $request = OAuthRequest::from_consumer_and_token($consumer, $token, 'GET', $basefeed, array());
        $request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $consumer, $token);
        $response = modmediawiki_send_request($request->get_normalized_http_method(), $basefeed, $request->to_header());
        $json = json_decode($response);

        foreach ($json as $page) {
            echo "<div id='$page->ID' style='margin-bottom: 50px;'>";
            echo "<div class='navbar clearfix' style='border: 1px solid #DDD; padding: 5px;'><h3 style='margin: 0;'><a style='margin: 5px 10px 20px 10px;' href='$CFG->wwwroot/mod/modmediawiki/view.php?id=$cm->id&amp;page=$page->ID&amp;sesskey=" . sesskey() . "'>$page->page_title</a></h3>";
            echo "</div>";
            echo "<div class='clearfix' style='margin: 5px 10px;'>$page->page_content</div>";
            echo "<div class='clearfix' style='margin: 5px 10px; color: gray; font-size: 100%;'>$page->page_resume";
            if($modmediawiki->permission_edit){
                echo "<div class='clearfix' style='margin: 5px 10px; color: gray; font-size: 90%;'>";
                echo "<a href='$CFG->wwwroot/mod/modmediawiki/view.php?id=$cm->id&amp;edit_page=$page->ID&amp;sesskey=" . sesskey() . "'>" . get_string("edit_page", "modmediawiki") . "</a>";
                echo "</div>";
            }
            echo "</div>";
            echo "</div>";
        }
        add_to_log($course->id, 'modmediawiki', 'view', "view.php?id=$cm->id", $modmediawiki->name, $cm->id);
        //echo htmlentities($response);
    }
}

// Finish the page
echo $OUTPUT->footer();
