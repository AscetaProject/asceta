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
 * Prints a particular instance of modwordpress
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package   mod_modwordpress
 * @copyright 2011 Vicente Manuel García Huete (vmgarcia@fidesol.org) - Fundación I+D del Software Libre (www.fidesol.org)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
/// (Replace modwordpress with the name of your module and remove this line)

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once(dirname(__FILE__) . '/lib.php');
require_once(dirname(__FILE__) . '/locallib.php');
require_once(dirname(__FILE__) . '/OAuth.php');

$id = optional_param('id', 0, PARAM_INT); // course_module ID, or
$n = optional_param('n', 0, PARAM_INT);  // modwordpress instance ID - it should be named as the first character of the module
$comments = optional_param('comments', 0, PARAM_INT); // Post ID to get comments
$new_comment = optional_param('new_comment', 0, PARAM_INT); // Post ID to get comments
$comment_content = optional_param('comment_content', '', PARAM_TEXT); // Post ID to get comments
$comment_post_ID = optional_param('comment_post_ID', 0, PARAM_INT); // Post ID to get comments
$post = optional_param('post', 0, PARAM_INT);
$page = optional_param('page', 0, PARAM_INT);
$new_post = optional_param('new_post', '', PARAM_TEXT); // Post ID to get comments
$post_title = optional_param('post_title', '', PARAM_TEXT); // Post ID to get comments
$post_content = optional_param('post_content', '', PARAM_TEXT); // Post ID to get comments

if ($id) {
    $cm = get_coursemodule_from_id('modwordpress', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $modwordpress = $DB->get_record('modwordpress', array('id' => $cm->instance), '*', MUST_EXIST);
} elseif ($n) {
    $modwordpress = $DB->get_record('modwordpress', array('id' => $n), '*', MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $modwordpress->course), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('modwordpress', $modwordpress->id, $course->id, false, MUST_EXIST);
} else {
    error('You must specify a course_module ID or an instance ID');
}

if ($modwordpress->server_id) {
    $server = $DB->get_record('modwordpress_servers', array('id' => $modwordpress->server_id), '*', MUST_EXIST);
    $mdl_users = $DB->get_records_sql('SELECT moodle_id, wordpress_id, username, firstname from {modwordpress_users} mu, {user} u WHERE u.id = mu.moodle_id and mu.server_id=?',array($modwordpress->server_id));
    $wp_users = $DB->get_records_sql('SELECT wordpress_id, moodle_id, username, firstname from {modwordpress_users} mu, {user} u WHERE u.id = mu.moodle_id and mu.server_id=?',array($modwordpress->server_id));
}

//var_dump($cm);
//var_dump($course);
//var_dump($modwordpress);
//var_dump($server);

require_login($course, true, $cm);

add_to_log($course->id, 'modwordpress', 'view', "view.php?id=$cm->id", $modwordpress->name, $cm->id);

/// Print the page header

$PAGE->set_url('/mod/modwordpress/view.php', array('id' => $cm->id));
$PAGE->set_title($modwordpress->name);
$PAGE->set_heading($course->shortname);
$PAGE->set_button(update_module_button($cm->id, $course->id, get_string('modulename', 'modwordpress')));

// other things you may want to set - remove if not needed
//$PAGE->set_cacheable(false);
//$PAGE->set_focuscontrol('some-html-id');
// Output starts here


if (!$modwordpress->server_id) {
    echo $OUTPUT->heading(get_string("configure_server_url", "modwordpress"));
} else {
    
    global $USER;

    if ($comment_content != '' and confirm_sesskey()) {
        $user_id = 1;
        if (isset($mdl_users[$USER->id]->wordpress_id)) $user_id = $mdl_users[$USER->id]->wordpress_id;
        $params = array('comment_content' => $comment_content, 'comment_author' => $user_id);
        $consumer_key = $server->consumer_key;
        $consumer_secret = $server->consumer_secret;
        $access_token = $server->access_token;
        $access_secret = $server->access_secret;
        $consumer = new OAuthConsumer($consumer_key, $consumer_secret, NULL);
        $token = new OAuthToken($access_token, $access_secret, NULL);
        $basefeed = rtrim($server->url, '/') . "/comment/$comment_post_ID.json";
        $request = OAuthRequest::from_consumer_and_token($consumer, $token, 'POST', $basefeed, $params);
        $request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $consumer, $token);
        $response = send_request($request->get_normalized_http_method(), $basefeed, $request->to_header(), $params);
        redirect("$CFG->wwwroot/mod/modwordpress/view.php?id=$cm->id");
        die;
    } elseif ($post_title != '' and $post_content != '' and confirm_sesskey()) {
        $user_id = 1;
        if (isset($mdl_users[$USER->id]->wordpress_id)) $user_id = $mdl_users[$USER->id]->wordpress_id;
        $params = array('post_title' => $post_title, 'post_content' => $post_content, 'post_author' => $user_id);
        $consumer_key = $server->consumer_key;
        $consumer_secret = $server->consumer_secret;
        $access_token = $server->access_token;
        $access_secret = $server->access_secret;
        $consumer = new OAuthConsumer($consumer_key, $consumer_secret, NULL);
        $token = new OAuthToken($access_token, $access_secret, NULL);
        $basefeed = rtrim($server->url, '/') . "/post.json";
        $request = OAuthRequest::from_consumer_and_token($consumer, $token, 'POST', $basefeed, $params);
        $request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $consumer, $token);
        $response = send_request($request->get_normalized_http_method(), $basefeed, $request->to_header(), $params);
        redirect("$CFG->wwwroot/mod/modwordpress/view.php?id=$cm->id");
        die;
    }





    echo $OUTPUT->header();

    if ($comments and confirm_sesskey()) {
        $consumer = new OAuthConsumer($server->consumer_key, $server->consumer_secret, NULL);
        $token = new OAuthToken($server->access_token, $server->access_secret, NULL);
        $basefeed = rtrim($server->url, '/') . "/post/$comments.json";
        $request = OAuthRequest::from_consumer_and_token($consumer, $token, 'GET', $basefeed, array());
        $request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $consumer, $token);
        $response = send_request($request->get_normalized_http_method(), $basefeed, $request->to_header());
        $json = json_decode($response);
        if (count($json)) {
	echo $OUTPUT->heading($modwordpress->name . ": " . $json->post_title);
	foreach ($json->comments as $comment) {
	    echo "<div id='$comment->comment_ID' style='margin-bottom: 50px;'>";
	    echo "<div class='navbar clearfix' style='border: 1px solid #DDD; padding: 1px;'>";
	    echo "<span style='margin: 0; font-weight:bold'>".$comment->comment_author."</span> dijo:";
	    echo "<p style='font-size: 75%; color: gray;'>Publicado en $comment->comment_date</p>";
	    echo "</div>";
	    echo "<div class='clearfix' style='margin: 5px 10px;'>$comment->comment_content</div>";
	    echo "<div class='clearfix' style='margin: 5px 10px; color: gray; font-size: 90%;'>";
	    echo "<a href='$CFG->wwwroot/mod/modwordpress/view.php?id=$cm->id&amp;new_comment=$json->ID&amp;sesskey=" . sesskey() . "'>".  get_string("comment_post", "modwordpress")."</a>";
	    echo "</div>";
	    echo "</div>";
	}
        }
        echo "<button onclick='javascript:history.back()'>Volver</button>  ";
    } elseif ($post and confirm_sesskey()) {
        $consumer = new OAuthConsumer($server->consumer_key, $server->consumer_secret, NULL);
        $token = new OAuthToken($server->access_token, $server->access_secret, NULL);
        $basefeed = rtrim($server->url, '/') . "/post/$post.json";
        $request = OAuthRequest::from_consumer_and_token($consumer, $token, 'GET', $basefeed, array());
        $request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $consumer, $token);
        $response = send_request($request->get_normalized_http_method(), $basefeed, $request->to_header());
        $post = json_decode($response);
        if (count($post)) {
	echo $OUTPUT->heading($modwordpress->name." : ".$post->post_title);
	echo "<p style='font-size: 75%; color: gray;'>Publicado en $post->post_date por $post->post_author</p>";
	echo $post->post_content;
	echo "<br/><br/><a href='$CFG->wwwroot/mod/modwordpress/view.php?id=$cm->id&amp;new_comment=$post->ID&amp;sesskey=" . sesskey() . "'>".  get_string("comment_post", "modwordpress")."</a>";
	if (isset ($post->comments)) {
	    foreach ($post->comments as $comment) {
	        echo "<div id='$comment->comment_ID' style='margin-bottom: 50px;'>";
	        echo "<div class='navbar clearfix' style='border: 1px solid #DDD; padding: 1px;'>";
	        echo "<span style='margin: 0; font-weight:bold'>$comment->comment_author</span> dijo:";
	        echo "<p style='font-size: 75%; color: gray;'>Publicado en $comment->comment_date</p>";
	        echo "</div>";
	        echo "<div class='clearfix' style='margin: 5px 10px;'>$comment->comment_content</div>";
	        echo "<div class='clearfix' style='margin: 5px 10px; color: gray; font-size: 90%;'>";
	        echo "</div>";
	        echo "</div>";
	    }
	echo "<a href='$CFG->wwwroot/mod/modwordpress/view.php?id=$cm->id&amp;new_comment=$post->ID&amp;sesskey=" . sesskey() . "'>".  get_string("comment_post", "modwordpress")."</a>";
	}
        }
        echo "<br/><button style='margin-top: 20px;' onclick='javascript:history.back()'>Volver</button>  ";
    } elseif ($page and confirm_sesskey()) {
        $consumer = new OAuthConsumer($server->consumer_key, $server->consumer_secret, NULL);
        $token = new OAuthToken($server->access_token, $server->access_secret, NULL);
        $basefeed = rtrim($server->url, '/') . "/page/$page.json";
        $request = OAuthRequest::from_consumer_and_token($consumer, $token, 'GET', $basefeed, array());
        $request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $consumer, $token);
        $response = send_request($request->get_normalized_http_method(), $basefeed, $request->to_header());
        $post = json_decode($response);
        if (count($post)) {
	echo $OUTPUT->heading($modwordpress->name." : ".$post->post_title);
	echo "<p style='font-size: 75%; color: gray;'>Publicado en $post->post_date por $post->post_author</p>";
	echo $post->post_content;
        }
        echo "<br/><button style='margin-top: 20px;' onclick='javascript:history.back()'>Volver</button>  ";
    } elseif ($new_comment and confirm_sesskey()) {
        echo $OUTPUT->heading($modwordpress->name);
        echo '<form name="new_comment_form" method="post" action="view.php" id="new_comment_form" onsubmit="return new_comment_form_validation();">';
        echo "<p>".  get_string("write_comment","modwordpress")."</p>";
        echo '<textarea cols=90 rows=10 name="comment_content"></textarea>';
        echo "<input type='hidden' name='sesskey' value='" . sesskey() . "' />";
        echo "<input type='hidden' name='comment_post_ID' value='$new_comment' />";
        echo "<input type='hidden' name='id' value='$cm->id' />";
        echo "<br><input type='submit' value='".get_string("save","modwordpress")."' />";
        echo "<button onclick='javascript:history.back()'>".get_string("back","modwordpress")."</button>  ";
        echo '</form>';
        echo " <script type='text/javascript'> function new_comment_form_validation() { if (document.new_comment_form.comment_content.value.length == 0) { alert('" . print_string('comment_empty', 'modwordpress') . "'); document.new_comment_form.comment_content.focus(); return false; } }</script>";
    } elseif ( $new_post != '' and confirm_sesskey()) {
        echo $OUTPUT->heading($modwordpress->name." : Nuevo Post");
        echo '<form name="new_post_form" method="post" action="view.php" id="new_post_form" onsubmit="return new_post_form_validation();">';
        echo '<table><thead></thead><tbody>';
        echo '<tr>';
        echo "<td><label for='post_title'>".get_string("title","modwordpress")."</label></td>";
        echo "<td><input type='text' name='post_title' value='' size='80px' /></td>";
        echo "</tr><tr>";
        echo "<td colspan='2'><textarea cols=90 rows=10 name='post_content'></textarea></td>";
        echo "</tr></tbody></table>";
        echo "<input type='submit' value='".get_string("save", "modwordpress")."' />";
        echo "<button onclick='javascript:history.back()'>".get_string("back","modwordpress")."</button>  ";
        echo "<input type='hidden' name='sesskey' value='" . sesskey() . "' />";
        echo "<input type='hidden' name='id' value='$cm->id' />";
        echo '</form>';
        echo " <script type='text/javascript'>";
        echo "function new_post_form_validation() {";
	echo "if (document.new_post_form.post_title.value.length == 0) {";
	    echo "alert('" . print_string('post_title_empty', 'modwordpress') . "');";
	    echo "document.new_post_form.post_title.focus();";
	    echo "return false; ";
	echo "}";
	echo "if (document.new_post_form.post_content.value.length == 0) {";
	    echo "alert('" . print_string('post_content_empty', 'modwordpress') . "');";
	    echo "document.new_post_form.post_content.focus();";
	    echo "return false; ";
	echo "}";
        echo "}";
        echo "</script>";
    } else {
        $consumer = new OAuthConsumer($server->consumer_key, $server->consumer_secret, NULL);
        $token = new OAuthToken($server->access_token, $server->access_secret, NULL);
        $basefeed = rtrim($server->url, '/') . '/pages.json';
        $request = OAuthRequest::from_consumer_and_token($consumer, $token, 'GET', $basefeed, array());
        $request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $consumer, $token);
        $response = send_request($request->get_normalized_http_method(), $basefeed, $request->to_header());
        $json = json_decode($response);

        echo $OUTPUT->heading($modwordpress->name);
        echo "<a style='margin: 5px 10px 20px 10px;' href='$CFG->wwwroot/mod/modwordpress/view.php?id=$cm->id&amp;new_post=post&amp;sesskey=" . sesskey() . "'>Nuevo Post</a>";
        foreach ($json as $page) {
	if ($page->post_title != 'api')
	    echo " | <a style='margin: 5px 10px 20px 10px;' href='$CFG->wwwroot/mod/modwordpress/view.php?id=$cm->id&amp;page=$page->ID&amp;sesskey=" . sesskey() . "'>$page->post_title</a>";
        }

        $basefeed = rtrim($server->url, '/') . '/posts.json';
        $request = OAuthRequest::from_consumer_and_token($consumer, $token, 'GET', $basefeed, array());
        $request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $consumer, $token);
        $response = send_request($request->get_normalized_http_method(), $basefeed, $request->to_header());
        $json = json_decode($response);

        foreach ($json as $post) {
	$post_author = (isset($wp_users[$post->post_author]) ? $wp_users[$post->post_author]->firstname : $post->post_author);
	echo "<div id='$post->ID' style='margin-bottom: 50px;'>";
	echo "<div class='navbar clearfix' style='border: 1px solid #DDD; padding: 5px;'><h3 style='margin: 0;'><a style='margin: 5px 10px 20px 10px;' href='$CFG->wwwroot/mod/modwordpress/view.php?id=$cm->id&amp;post=$post->ID&amp;sesskey=" . sesskey() . "'>$post->post_title</a></h3>";
	echo "<p style='font-size: 75%; color: gray;'>Publicado en $post->post_date por $post_author</p>";
	echo "</div>";
	echo "<div class='clearfix' style='margin: 5px 10px;'>$post->post_content</div>";
	echo "<div class='clearfix' style='margin: 5px 10px; color: gray; font-size: 90%;'>";
	if ($post->comment_count > 1) {
	    echo "<a title='' href='$CFG->wwwroot/mod/modwordpress/view.php?id=$cm->id&amp;comments=$post->ID&amp;sesskey=" . sesskey() . "'>";
	    echo "$post->comment_count Comentarios";
	    echo "</a> | ";
	} elseif ($post->comment_count == 1) {
	    echo "<a title='' href='$CFG->wwwroot/mod/modwordpress/view.php?id=$cm->id&amp;comments=$post->ID&amp;sesskey=" . sesskey() . "'>";
	    echo "$post->comment_count Comentario";
	    echo "</a> | ";
	}
	echo "<a href='$CFG->wwwroot/mod/modwordpress/view.php?id=$cm->id&amp;new_comment=$post->ID&amp;sesskey=" . sesskey() . "'>".  get_string("comment_post", "modwordpress")."</a>";
	echo "</div>";
	echo "</div>";
        }
        //echo htmlentities($response);
    }
}





// Finish the page
echo $OUTPUT->footer();
