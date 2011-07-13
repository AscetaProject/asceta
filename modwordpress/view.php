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
$edit_comment = optional_param('edit_comment', 0, PARAM_INT); // Post ID to get comments
$comment_content = optional_param('comment_content', '', PARAM_TEXT); // Post ID to get comments
$comment_post_ID = optional_param('comment_post_ID', 0, PARAM_INT); // Post ID to get comments
$comment_ID = optional_param('comment_ID', 0, PARAM_INT); // Post ID to get comments
$post = optional_param('post', 0, PARAM_INT);
$page = optional_param('page', 0, PARAM_INT);
$edit_post = optional_param('edit_post', 0, PARAM_INT);
$new_post = optional_param('new_post', '', PARAM_TEXT); // Post ID to get comments
$edit_post = optional_param('edit_post', '', PARAM_TEXT); // Post ID to get comments
$new_page = optional_param('new_page', '', PARAM_TEXT); // Post ID to get comments
$edit_page = optional_param('edit_page', '', PARAM_TEXT); // Post ID to get comments
$post_title = optional_param('post_title', '', PARAM_TEXT); // Post ID to get comments
$post_content = optional_param('post_content', '', PARAM_TEXT); // Post ID to get comments
$post_type = optional_param('post_type', '', PARAM_TEXT); // Post ID to get comments
$post_ID = optional_param('post_ID', 0, PARAM_INT);

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
    $mdl_users = $DB->get_records_sql('SELECT moodle_id, wordpress_id, username, firstname from {modwordpress_users} mu, {user} u WHERE u.id = mu.moodle_id and mu.server_id=?', array($modwordpress->server_id));
    $wp_users = $DB->get_records_sql('SELECT wordpress_id, moodle_id, username, firstname from {modwordpress_users} mu, {user} u WHERE u.id = mu.moodle_id and mu.server_id=?', array($modwordpress->server_id));
}

//var_dump($cm);
//var_dump($course);
//var_dump($modwordpress);
//var_dump($server);

require_login($course, true, $cm);

//add_to_log($course->id, 'modwordpress', 'view', "view.php?id=$cm->id", $modwordpress->name, $cm->id);
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




// SAVE NEW/EDITED COMMENT
    if ($comment_content != '' and confirm_sesskey()) {
        if (($modwordpress->permission_create_comment && !$comment_ID) || ($modwordpress->permission_edit_comment && $comment_ID)) {
	$user_id = 1;
	if (isset($mdl_users[$USER->id]->wordpress_id))
	    $user_id = $mdl_users[$USER->id]->wordpress_id;
	$params = array('comment_content' => $comment_content, 'comment_author' => $user_id);

	// edit comment
	if ($comment_ID) {
	    $basefeed = rtrim($server->url, '/') . "/comment/$comment_ID.json";
	    $method = 'PUT';

	// new comment
	} else {
	    $basefeed = rtrim($server->url, '/') . "/comment/$comment_post_ID.json";
	    $method = 'POST';
	}

	if ($server->oauth) {
	    $consumer_key = $server->consumer_key;
	    $consumer_secret = $server->consumer_secret;
	    $access_token = $server->access_token;
	    $access_secret = $server->access_secret;
	    $consumer = new OAuthConsumer($consumer_key, $consumer_secret, NULL);
	    $token = new OAuthToken($access_token, $access_secret, NULL);
	    $request = OAuthRequest::from_consumer_and_token($consumer, $token, $method, $basefeed, $params);
	    $request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $consumer, $token);
	    $response = send_request($request->get_normalized_http_method(), $basefeed, $request->to_header(), $params);
	} else {
	    $response = send_request($request->get_normalized_http_method(), $basefeed, null, $params);
	}
	add_to_log($course->id, 'modwordpress', 'create comment', "view.php?id=$cm->id&comments=$comment_post_ID&sesskey=" . sesskey(), $modwordpress->name, $cm->id);
        }
        redirect("$CFG->wwwroot/mod/modwordpress/view.php?id=$cm->id&comments=$comment_post_ID&sesskey=".sesskey());
        die;




// SAVE NEW/EDITED POST OR PAGE
    } elseif ($post_title != '' and $post_content != '' and confirm_sesskey()) {
        if (($post_type != '' && $modwordpress->permission_create_page) || ($post_type == '' && $modwordpress->permission_create_post) || ($post_ID && $modwordpress->permission_edit_post)) {
	$user_id = 1;
	if (isset($mdl_users[$USER->id]->wordpress_id))
	    $user_id = $mdl_users[$USER->id]->wordpress_id;
	$params = array('post_title' => $post_title, 'post_content' => $post_content, 'post_author' => $user_id);

	// edit post
	if ($post_ID) {
	        $basefeed = rtrim($server->url, '/') . "/post/$post_ID.json";
	        $method = "PUT";

	// new comment
	} else {
	    $method = "POST";
	    if ($post_type != '') {
	        $basefeed = rtrim($server->url, '/') . "/page.json";
	    } else {
	        $basefeed = rtrim($server->url, '/') . "/post.json";
	    }
	}

	if ($server->oauth) {
	    $consumer_key = $server->consumer_key;
	    $consumer_secret = $server->consumer_secret;
	    $access_token = $server->access_token;
	    $access_secret = $server->access_secret;
	    $consumer = new OAuthConsumer($consumer_key, $consumer_secret, NULL);
	    $token = new OAuthToken($access_token, $access_secret, NULL);
	    $request = OAuthRequest::from_consumer_and_token($consumer, $token, $method, $basefeed, $params);
	    $request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $consumer, $token);
	    $response = send_request($request->get_normalized_http_method(), $basefeed, $request->to_header(), $params);
	} else {
	    $response = send_request($request->get_normalized_http_method(), $basefeed, null, $params);
	}
	$json = json_decode($response);
	if ($post_type != '' && isset($json->ID)) {
	    add_to_log($course->id, 'modwordpress', 'create page', "view.php?id=$cm->id&page=$json->ID&sesskey=" . sesskey(), $modwordpress->name, $cm->id);
	} else {
	    add_to_log($course->id, 'modwordpress', 'create post', "view.php?id=$cm->id&post=$json->ID&sesskey=" . sesskey(), $modwordpress->name, $cm->id);
	}
        }
        redirect("$CFG->wwwroot/mod/modwordpress/view.php?id=$cm->id");
        die;
    }


    echo $OUTPUT->header();





// VIEW COMMENTS
    if ($comments and confirm_sesskey()) {

        $basefeed = rtrim($server->url, '/') . "/post/$comments.json";
        if ($server->oauth) {
	$consumer = new OAuthConsumer($server->consumer_key, $server->consumer_secret, NULL);
	$token = new OAuthToken($server->access_token, $server->access_secret, NULL);
	$request = OAuthRequest::from_consumer_and_token($consumer, $token, 'GET', $basefeed, array());
	$request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $consumer, $token);
	$response = send_request($request->get_normalized_http_method(), $basefeed, $request->to_header());
        } else {
	$response = send_request($request->get_normalized_http_method(), $basefeed, null);
        }
        $json = json_decode($response);

        echo $OUTPUT->heading($modwordpress->name . ": " . $json->post_title);
        if ($modwordpress->permission_create_comment) {
	echo "<a style='margin: 5px 10px 20px 10px;' href='$CFG->wwwroot/mod/modwordpress/view.php?id=$cm->id&amp;new_comment=$json->ID&amp;sesskey=" . sesskey() . "'>" . get_string("comment_post", "modwordpress") . "</a>";
        }
        if (count($json)) {
	foreach ($json->comments as $comment) {
	    $author = (isset($wp_users[$comment->comment_author]) ? $wp_users[$comment->comment_author]->firstname : $comment->comment_author_name);
	    echo "<div id='$comment->comment_ID' style='margin-bottom: 50px;'>";
	    echo "	<div class='navbar clearfix' style='border: 1px solid #DDD; padding: 1px;'>";
	    echo "	    <span style='margin: 0; font-weight:bold'>" . $author . "</span> dijo:";
	    echo "	    <p style='font-size: 75%; color: gray;'>Publicado en $comment->comment_date</p>";
	    echo "	</div>";
	    echo "	<div class='clearfix' style='margin: 5px 10px;'>$comment->comment_content</div>";
	    echo "	<div class='clearfix' style='margin: 5px 10px; color: gray; font-size: 90%;'>";
	    echo "	</div>";
	    echo "</div>";
	    if (isset($wp_users[$comment->comment_author])) {
	        if ($wp_users[$comment->comment_author]->moodle_id == $USER->id && $modwordpress->permission_edit_comment) {
		echo "<a style='margin: 5px 10px 20px 10px;' href='$CFG->wwwroot/mod/modwordpress/view.php?id=$cm->id&amp;edit_comment=$comment->comment_ID&amp;sesskey=" . sesskey() . "'>" . get_string("edit_comment", "modwordpress") . "</a><br/><br/>";
	        }
	    }
	}
        }
        if ($modwordpress->permission_create_comment) {
	echo "<a style='margin: 5px 10px 20px 10px;' href='$CFG->wwwroot/mod/modwordpress/view.php?id=$cm->id&amp;new_comment=$json->ID&amp;sesskey=" . sesskey() . "'>" . get_string("comment_post", "modwordpress") . "</a><br/><br/>";
        }
        echo "<button style='margin: 5px 10px 20px 10px;' onclick='javascript:history.back()'>Volver</button>  ";
        add_to_log($course->id, 'modwordpress', 'view comments', "view.php?id=$cm->id", $modwordpress->name, $cm->id);




// VIEW POST
    } elseif ($post and confirm_sesskey()) {
        $basefeed = rtrim($server->url, '/') . "/post/$post.json";
        if ($server->oauth) {
	$consumer = new OAuthConsumer($server->consumer_key, $server->consumer_secret, NULL);
	$token = new OAuthToken($server->access_token, $server->access_secret, NULL);
	$request = OAuthRequest::from_consumer_and_token($consumer, $token, 'GET', $basefeed, array());
	$request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $consumer, $token);
	$response = send_request($request->get_normalized_http_method(), $basefeed, $request->to_header());
        } else {
	$response = send_request($request->get_normalized_http_method(), $basefeed, null);
        }
        $post = json_decode($response);
        if (count($post)) {
	echo $OUTPUT->heading($modwordpress->name . " : " . $post->post_title);
	echo "<p style='font-size: 75%; color: gray;'>Publicado en $post->post_date por $post->post_author</p>";
	echo $post->post_content;
	if ($modwordpress->permission_create_comment) {
	    echo "<br/><br/><a style='margin: 5px 10px 20px 10px;' href='$CFG->wwwroot/mod/modwordpress/view.php?id=$cm->id&amp;new_comment=$post->ID&amp;sesskey=" . sesskey() . "'>" . get_string("comment_post", "modwordpress") . "</a>";
	}
	if (isset($post->comments)) {
	    foreach ($post->comments as $comment) {
	        $author = (isset($wp_users[$comment->comment_author]) ? $wp_users[$comment->comment_author]->firstname : $comment->comment_author_name);
	        echo "<div id='$comment->comment_ID' style='margin-bottom: 50px;'>";
	        echo "<div class='navbar clearfix' style='border: 1px solid #DDD; padding: 1px;'>";
	        echo "<span style='margin: 0; font-weight:bold'>$author</span> dijo:";
	        echo "<p style='font-size: 75%; color: gray;'>Publicado en $comment->comment_date</p>";
	        echo "</div>";
	        echo "<div class='clearfix' style='margin: 5px 10px;'>$comment->comment_content</div>";
	        echo "<div class='clearfix' style='margin: 5px 10px; color: gray; font-size: 90%;'>";
	        echo "</div>";
	        echo "</div>";
	        if (isset($wp_users[$comment->comment_author])) {
		if ($wp_users[$comment->comment_author]->moodle_id == $USER->id && $modwordpress->permission_edit_comment) {
		    echo "<a style='margin: 5px 10px 20px 10px;' href='$CFG->wwwroot/mod/modwordpress/view.php?id=$cm->id&amp;edit_comment=$comment->comment_ID&amp;sesskey=" . sesskey() . "'>" . get_string("edit_comment", "modwordpress") . "</a><br/><br/>";
		}
	        }

	    }
	    if ($modwordpress->permission_create_comment) {
	        echo "<a href='$CFG->wwwroot/mod/modwordpress/view.php?id=$cm->id&amp;new_comment=$post->ID&amp;sesskey=" . sesskey() . "'>" . get_string("comment_post", "modwordpress") . "</a>";
	    }
	}
        }
        echo "<br/><button style='margin-top: 20px;' onclick='javascript:history.back()'>Volver</button>  ";
        add_to_log($course->id, 'modwordpress', 'view post', "view.php?id=$cm->id&post=$post->ID&sesskey=" . sesskey(), $modwordpress->name, $cm->id);




// VIEW PAGE
    } elseif ($page and confirm_sesskey()) {
        $basefeed = rtrim($server->url, '/') . "/page/$page.json";
        if ($server->oauth) {
	$consumer = new OAuthConsumer($server->consumer_key, $server->consumer_secret, NULL);
	$token = new OAuthToken($server->access_token, $server->access_secret, NULL);
	$request = OAuthRequest::from_consumer_and_token($consumer, $token, 'GET', $basefeed, array());
	$request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $consumer, $token);
	$response = send_request($request->get_normalized_http_method(), $basefeed, $request->to_header());
        } else {
	$response = send_request($request->get_normalized_http_method(), $basefeed, null);
        }
        $post = json_decode($response);
        if (count($post)) {
	echo $OUTPUT->heading($modwordpress->name . " : " . $post->post_title);
	echo "<p style='font-size: 75%; color: gray;'>Publicado en $post->post_date por $post->post_author</p>";
	echo $post->post_content;
        }
        echo "<br/><button style='margin-top: 20px;' onclick='javascript:history.back()'>Volver</button>  ";
        add_to_log($course->id, 'modwordpress', 'view page', "view.php?id=$cm->id&page=$page&sesskey=" . sesskey(), $modwordpress->name, $cm->id);




// NEW COMMENT FORM
    } elseif ($new_comment and confirm_sesskey()) {
        if ($modwordpress->permission_create_comment) {
	echo $OUTPUT->heading($modwordpress->name);
	echo '<form name="new_comment_form" method="post" action="view.php" id="new_comment_form" onsubmit="return new_comment_form_validation();">';
	echo "<p>" . get_string("write_comment", "modwordpress") . "</p>";
	echo '<textarea cols=90 rows=10 name="comment_content"></textarea>';
	echo "<input type='hidden' name='sesskey' value='" . sesskey() . "' />";
	echo "<input type='hidden' name='comment_post_ID' value='$new_comment' />";
	echo "<input type='hidden' name='id' value='$cm->id' />";
	echo "<br><input type='submit' value='" . get_string("save", "modwordpress") . "' />";
	echo "<button onclick='javascript:history.back()'>" . get_string("back", "modwordpress") . "</button>  ";
	echo '</form>';
	echo " <script type='text/javascript'> function new_comment_form_validation() { if (document.new_comment_form.comment_content.value.length == 0) { alert('" . print_string('comment_empty', 'modwordpress') . "'); document.new_comment_form.comment_content.focus(); return false; } }</script>";
        } else {
	echo "<p>" . get_string("no_permission", "modwordpress") . "</p>";
	echo "<button onclick='javascript:history.back()'>" . get_string("back", "modwordpress") . "</button>  ";
        }

// EDIT COMMENT FORM
    } elseif ($edit_comment and confirm_sesskey()) {
        if ($modwordpress->permission_edit_comment) {
	$basefeed = rtrim($server->url, '/') . "/comment/$edit_comment.json";
	if ($server->oauth) {
	    $consumer = new OAuthConsumer($server->consumer_key, $server->consumer_secret, NULL);
	    $token = new OAuthToken($server->access_token, $server->access_secret, NULL);
	    $request = OAuthRequest::from_consumer_and_token($consumer, $token, 'GET', $basefeed, array());
	    $request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $consumer, $token);
	    $response = send_request($request->get_normalized_http_method(), $basefeed, $request->to_header());
	} else {
	    $response = send_request($request->get_normalized_http_method(), $basefeed, null);
	}
	$json = json_decode($response);

	echo $OUTPUT->heading($modwordpress->name);
	echo '<form name="new_comment_form" method="post" action="view.php" id="new_comment_form" onsubmit="return new_comment_form_validation();">';
	echo "<p>" . get_string("write_comment", "modwordpress") . "</p>";
	echo '<textarea cols=90 rows=10 name="comment_content">'.$json->comment_content.'</textarea>';
	echo "<input type='hidden' name='sesskey' value='" . sesskey() . "' />";
	echo "<input type='hidden' name='id' value='$cm->id' />";
	echo "<input type='hidden' name='comment_ID' value='$edit_comment' />";
	echo "<input type='hidden' name='comment_post_ID' value='$json->comment_post_ID' />";
	echo "<br><input type='submit' value='" . get_string("save", "modwordpress") . "' />";
	echo "<button onclick='javascript:history.back()'>" . get_string("back", "modwordpress") . "</button>  ";
	echo '</form>';
	echo " <script type='text/javascript'> function new_comment_form_validation() { if (document.new_comment_form.comment_content.value.length == 0) { alert('" . print_string('comment_empty', 'modwordpress') . "'); document.new_comment_form.comment_content.focus(); return false; } }</script>";
        } else {
	echo "<p>" . get_string("no_permission", "modwordpress") . "</p>";
	echo "<button onclick='javascript:history.back()'>" . get_string("back", "modwordpress") . "</button>  ";
        }





// NEW POST OR PAGE FORM
    } elseif (($new_post != '' or $new_page != '') and confirm_sesskey()) {
        if (($new_page != '' && $modwordpress->permission_create_page) || ($new_page == '' && $modwordpress->permission_create_post)) {
	if ($new_page != '') {
	    echo $OUTPUT->heading($modwordpress->name . " : " . get_string('new_page', 'modwordpress'));
	} else {
	    echo $OUTPUT->heading($modwordpress->name . " : " . get_string('new_post', 'modwordpress'));
	}
	echo '<form name="new_post_form" method="post" action="view.php" id="new_post_form" onsubmit="return new_post_form_validation();">';
	echo '<table><thead></thead><tbody>';
	echo '<tr>';
	echo "<td><label for='post_title'>" . get_string("title", "modwordpress") . "</label></td>";
	echo "<td><input type='text' name='post_title' value='' size='80px' /></td>";
	echo "</tr><tr>";
	echo "<td colspan='2'><textarea cols=90 rows=10 name='post_content'></textarea></td>";
	echo "</tr></tbody></table>";
	echo "<input type='submit' value='" . get_string("save", "modwordpress") . "' />";
	echo "<button onclick='javascript:history.back()'>" . get_string("back", "modwordpress") . "</button>  ";
	echo "<input type='hidden' name='sesskey' value='" . sesskey() . "' />";
	echo "<input type='hidden' name='id' value='$cm->id' />";
	if ($new_page != '') {
	    echo "<input type='hidden' name='post_type' value='page' />";
	}
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
	echo "<p>" . get_string("no_permission", "modwordpress") . "</p>";
	echo "<button onclick='javascript:history.back()'>" . get_string("back", "modwordpress") . "</button>  ";
        }



// EDIT POST FORM
    } elseif ( $edit_post and confirm_sesskey()) {
        if ($modwordpress->permission_edit_post) {
	$basefeed = rtrim($server->url, '/') . "/post/$edit_post.json";
	if ($server->oauth) {
	    $consumer = new OAuthConsumer($server->consumer_key, $server->consumer_secret, NULL);
	    $token = new OAuthToken($server->access_token, $server->access_secret, NULL);
	    $request = OAuthRequest::from_consumer_and_token($consumer, $token, 'GET', $basefeed, array());
	    $request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $consumer, $token);
	    $response = send_request($request->get_normalized_http_method(), $basefeed, $request->to_header());
	} else {
	    $response = send_request($request->get_normalized_http_method(), $basefeed, null);
	}
	$json = json_decode($response);


	echo $OUTPUT->heading($modwordpress->name . " : ".$json->post_title. " (" . get_string('edit_post', 'modwordpress').") ");
	echo '<form name="new_post_form" method="post" action="view.php" id="new_post_form" onsubmit="return new_post_form_validation();">';
	echo '<table><thead></thead><tbody>';
	echo '<tr>';
	echo "<td><label for='post_title'>" . get_string("title", "modwordpress") . "</label></td>";
	echo "<td><input type='text' name='post_title' value='$json->post_title' size='80px' /></td>";
	echo "</tr><tr>";
	echo "<td colspan='2'><textarea cols=90 rows=10 name='post_content'>$json->post_content</textarea></td>";
	echo "</tr></tbody></table>";
	echo "<input type='submit' value='" . get_string("save", "modwordpress") . "' />";
	echo "<button onclick='javascript:history.back()'>" . get_string("back", "modwordpress") . "</button>  ";
	echo "<input type='hidden' name='sesskey' value='" . sesskey() . "' />";
	echo "<input type='hidden' name='id' value='$cm->id' />";
	echo "<input type='hidden' name='post_ID' value='$edit_post' />";
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
	echo "<p>" . get_string("no_permission", "modwordpress") . "</p>";
	echo "<button onclick='javascript:history.back()'>" . get_string("back", "modwordpress") . "</button>  ";
        }







// LIST ALL POSTS
    } else {

        $consumer = new OAuthConsumer($server->consumer_key, $server->consumer_secret, NULL);
        $token = new OAuthToken($server->access_token, $server->access_secret, NULL);
        $basefeed = rtrim($server->url, '/') . '/pages.json';
        $request = OAuthRequest::from_consumer_and_token($consumer, $token, 'GET', $basefeed, array());
        $request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $consumer, $token);
        $response = send_request($request->get_normalized_http_method(), $basefeed, $request->to_header());
        $json = json_decode($response);

        echo $OUTPUT->heading($modwordpress->name);


        /*
          // Estilo desde Wordpress

          $url = rtrim($server->url, '/');
          $url = rtrim($url, '/api');
          $url = rtrim($url, '/API');
          $url = rtrim($url, '/API');
          $url .= "/";
          $curl = curl_init();
          curl_setopt($curl, CURLOPT_URL, $url);
          $ch = $curl;
          curl_setopt($ch, CURLOPT_FAILONERROR, true);
          curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
          curl_setopt($ch, CURLOPT_AUTOREFERER, true);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          curl_setopt($ch, CURLOPT_TIMEOUT, 10);
          $html = curl_exec($ch);
          $dom = new DOMDocument();
          @$dom->loadHTML($html);
          $xpath = new DOMXPath($dom);
          $hrefs = $xpath->evaluate("/html/head/child::link[attribute::type='text/css'][attribute::rel='stylesheet']");
          $css = '';
          if ($hrefs->length) {
          $href = $hrefs->item(0);
          $css = $href->getAttribute('href');
          }

          if ($css != '') {
          echo "<link rel='stylesheet' href='$css' />";
          }






          echo "<div id='access' role='navigation' >";
          echo "<div class='menu'>";
          echo "<ul style='list-style: none;'>";
          echo "  <li style='list-style: none;' class='page-item'>";
          echo "	<a href='$CFG->wwwroot/mod/modwordpress/view.php?id=$cm->id&amp;new_post=post&amp;sesskey=" . sesskey() . "'>" . get_string('new_post', 'modwordpress') . "</a>";
          echo "  </li>";
          echo "  <li style='list-style: none;' class='page_item'>";
          echo "	<a href='$CFG->wwwroot/mod/modwordpress/view.php?id=$cm->id&amp;new_page=page&amp;sesskey=" . sesskey() . "'>" . get_string('new_page', 'modwordpress') . "</a>";
          echo "  </li>";
          foreach ($json as $page) {
          if ($page->post_title != 'api') {
          echo "  <li style='list-style: none;' class='page_item'>";
          echo "<a href='$CFG->wwwroot/mod/modwordpress/view.php?id=$cm->id&amp;page=$page->ID&amp;sesskey=" . sesskey() . "'>$page->post_title</a>";
          echo "  </li>";
          }
          }
          echo "</ul>";
          echo "</div>";
          echo "</div>";
         */



        // Estilo Moodle

        if ($modwordpress->permission_create_post)
	echo "<a style='margin: 5px 10px 20px 10px;' href='$CFG->wwwroot/mod/modwordpress/view.php?id=$cm->id&amp;new_post=post&amp;sesskey=" . sesskey() . "'>" . get_string('new_post', 'modwordpress') . "</a>";
        if ($modwordpress->permission_create_page) {
	if ($modwordpress->permission_create_post) echo " | ";
	echo "<a style='margin: 5px 10px 20px 10px;' href='$CFG->wwwroot/mod/modwordpress/view.php?id=$cm->id&amp;new_page=page&amp;sesskey=" . sesskey() . "'>" . get_string('new_page', 'modwordpress') . "</a>";
        }
        $block = "";
        if (count($json) && ($modwordpress->permission_create_page || $modwordpress->permission_create_post)) $block = " | ";
        foreach ($json as $page) {
	if ($page->post_title != 'api') {
	    echo " $block <a style='margin: 5px 10px 20px 10px;' href='$CFG->wwwroot/mod/modwordpress/view.php?id=$cm->id&amp;page=$page->ID&amp;sesskey=" . sesskey() . "'>$page->post_title</a>";
	    $block = " | ";
	}
        }



        $basefeed = rtrim($server->url, '/') . '/posts.json';
        $request = OAuthRequest::from_consumer_and_token($consumer, $token, 'GET', $basefeed, array());
        $request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $consumer, $token);
        $response = send_request($request->get_normalized_http_method(), $basefeed, $request->to_header());
        $json = json_decode($response);

        foreach ($json as $post) {
	$post_author = (isset($wp_users[$post->post_author]) ? $wp_users[$post->post_author]->firstname : $post->user_nicename);



	/*

	  // Estilo Wordpress
	  echo "<div id='post-$post->ID' class='post-$post->ID post type-post status-publish format-standard hentry category-sin-categoria'>";
	  echo "  <h2 class='entry-title'><a href='$CFG->wwwroot/mod/modwordpress/view.php?id=$cm->id&amp;post=$post->ID&amp;sesskey=" . sesskey() . "'>$post->post_title</a></h2>";
	  echo "  <div class='entry-meta'>";
	  echo "	<span class='meta-prep meta-prep-author'>Publicado en</span> <span class='entry-date'>$post->post_date</span> <span class='meta-sep'>por</span> <span class='author vcard'>$post_author</span>";
	  echo "  </div>";
	  echo "  <div class='entry-content'>";
	  echo $post->post_content;
	  echo "  </div>";
	  echo "  <div class='entry-utility'>";
	  echo "	<span class='cat-links'>";
	  echo "	    <span class='comments-link'>";
	  if ($post->comment_count > 1) {
	  echo "<a title='' href='$CFG->wwwroot/mod/modwordpress/view.php?id=$cm->id&amp;comments=$post->ID&amp;sesskey=" . sesskey() . "'>";
	  echo "$post->comment_count Comentarios";
	  echo "</a> | ";
	  } elseif ($post->comment_count == 1) {
	  echo "<a title='' href='$CFG->wwwroot/mod/modwordpress/view.php?id=$cm->id&amp;comments=$post->ID&amp;sesskey=" . sesskey() . "'>";
	  echo "$post->comment_count Comentario";
	  echo "</a> | ";
	  }
	  echo "	    </span>";
	  echo "	    <span class='meta-sep'>|</span>";
	  echo "	    <span class='edit-link'>";
	  echo "<a href='$CFG->wwwroot/mod/modwordpress/view.php?id=$cm->id&amp;new_comment=$post->ID&amp;sesskey=" . sesskey() . "'>" . get_string("comment_post", "modwordpress") . "</a>";
	  echo "	    </span>";
	  echo "	</span>";
	  echo "  </div>";
	  echo "</div>";

	 */




	// Estilo Moodle
	echo "<div id='$post->ID' style='margin-bottom: 50px;'>";
	echo "<div class='navbar clearfix' style='border: 1px solid #DDD; padding: 5px;'><h3 style='margin: 0;'><a style='margin: 5px 10px 20px 10px;' href='$CFG->wwwroot/mod/modwordpress/view.php?id=$cm->id&amp;post=$post->ID&amp;sesskey=" . sesskey() . "'>$post->post_title</a></h3>";
	echo "<p style='font-size: 75%; color: gray;'>Publicado en $post->post_date por $post_author</p>";
	echo "</div>";
	echo "<div class='clearfix' style='margin: 5px 10px;'>$post->post_content</div>";
	echo "<div class='clearfix' style='margin: 5px 10px; color: gray; font-size: 90%;'>";
	if ($post->comment_count > 1) {
	    echo "<a title='' href='$CFG->wwwroot/mod/modwordpress/view.php?id=$cm->id&amp;comments=$post->ID&amp;sesskey=" . sesskey() . "'>";
	    echo "$post->comment_count Comentarios";
	} elseif ($post->comment_count == 1) {
	    echo "<a title='' href='$CFG->wwwroot/mod/modwordpress/view.php?id=$cm->id&amp;comments=$post->ID&amp;sesskey=" . sesskey() . "'>";
	    echo "$post->comment_count Comentario";
	}
	if ($modwordpress->permission_create_comment) {
	    if ($post->comment_count)
	        echo "</a> | ";
	    echo "<a href='$CFG->wwwroot/mod/modwordpress/view.php?id=$cm->id&amp;new_comment=$post->ID&amp;sesskey=" . sesskey() . "'>" . get_string("comment_post", "modwordpress") . "</a>";
	}
	if ($modwordpress->permission_edit_post) {
	        echo " | ";
	        echo "<a href='$CFG->wwwroot/mod/modwordpress/view.php?id=$cm->id&amp;edit_post=$post->ID&amp;sesskey=" . sesskey() . "'>" . get_string("edit_post", "modwordpress") . "</a>";
	}
	echo "</div>";
	echo "</div>";
        }
        add_to_log($course->id, 'modwordpress', 'view', "view.php?id=$cm->id", $modwordpress->name, $cm->id);
        //echo htmlentities($response);
    }
}





// Finish the page
echo $OUTPUT->footer();
