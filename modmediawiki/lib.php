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
 * Library of interface functions and constants for module modmediawiki
 *
 * All the core Moodle functions, neeeded to allow the module to work
 * integrated in Moodle should be placed here.
 * All the modmediawiki specific functions, needed to implement all the module
 * logic, should go to locallib.php. This will help to save some memory when
 * Moodle is performing actions across all modules.
 *
 * @package   mod_modmediawiki
 * @copyright 2011 María del Mar Jiménez Torres (mjimenez@fidesol.org) - Fundación I+D del Software Libre (www.fidesol.org)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

if (!function_exists('implode_assoc')) require_once("locallib.php");
if (!class_exists('OAuthConsumer')) require_once("OAuth.php");

/** example constant */
//define('NEWMODULE_ULTIMATE_ANSWER', 42);

/**
 * If you for some reason need to use global variables instead of constants, do not forget to make them
 * global as this file can be included inside a function scope. However, using the global variables
 * at the module level is not a recommended.
 */
//global $NEWMODULE_GLOBAL_VARIABLE;
//$NEWMODULE_QUESTION_OF = array('Life', 'Universe', 'Everything');

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @param object $modmediawiki An object from the form in mod_form.php
 * @return int The id of the newly inserted modmediawiki record
 */
function modmediawiki_add_instance($modmediawiki, $mform) {
    global $DB;

    $modmediawiki->timecreated = time();

    # You may have to add extra stuff in here #
    $newmod = $DB->insert_record('modmediawiki', $modmediawiki);

    if ($newmod) {

        //Adding Moodle users to mediawiki
        $modmediawiki_instance = $DB->get_record_select("modmediawiki", "id=$newmod");
        $course_id = $modmediawiki_instance->course;
        $server_id = $modmediawiki_instance->server_id;
        $server = $DB->get_record_select("modmediawiki_servers", "id=$server_id");


        if ($server->oauth) {
	$consumer_key = $server->consumer_key;
	$consumer_secret = $server->consumer_secret;
	$access_token = $server->access_token;
	$access_secret = $server->access_secret;
	$consumer = new OAuthConsumer($consumer_key, $consumer_secret, NULL);
	$token = new OAuthToken($access_token, $access_secret, NULL);
        }

        $context = get_context_instance(CONTEXT_COURSE, $course_id);
        $contextlists = get_related_contexts_string($context);

        $sql = "SELECT u.id, u.username, u.firstname, u.email
	      FROM {user} u
	      JOIN {role_assignments} ra ON ra.userid = u.id
	     WHERE u.deleted = 0 AND u.confirmed = 1 AND ra.contextid $contextlists";
        $course_users = $DB->get_records_sql($sql);

        foreach ($course_users as $user) {
        $basefeed = rtrim($server->url, '/') . '/users';
	$params = array('name' => $user->username, 'email' => $user->email, 'realname' => $user->firstname, 'password' => substr(md5(rand() . rand()), 0, 15));

	if ($server->oauth) {
	    $request = OAuthRequest::from_consumer_and_token($consumer, $token, 'POST', $basefeed, $params);
	    $request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $consumer, $token);
	    $response = modmediawiki_send_request($request->get_normalized_http_method(), $basefeed, $request->to_header(), $params);
	} else {
	    $response = modmediawiki_send_request('POST', $basefeed, null, $params);
	}

	$json = json_decode($response);
	if ($json->mId != null) {
	$dataobject = array();
	$dataobject['moodle_id'] = $user->id;
	$dataobject['mediawiki_id'] = $json->mId;
	$dataobject['server_id'] = $server_id;
	$DB->insert_record('modmediawiki_users', $dataobject, false, false);
    }
	 	}

    }
    return $newmod;
}

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @param object $modmediawiki An object from the form in mod_form.php
 * @return boolean Success/Fail
 */
function modmediawiki_update_instance($modmediawiki) {
    global $DB;

    $modmediawiki->timemodified = time();
    $modmediawiki->id = $modmediawiki->instance;

    # You may have to add extra stuff in here #

    $newmod = $DB->update_record('modmediawiki', $modmediawiki);


    if ($newmod) {

        //Adding Moodle users to mediawiki
        //$modmediawiki_instance = $DB->get_record_select("modmediawiki", "id=$newmod");
        $course_id = $modmediawiki->course;
        $server_id = $modmediawiki->server_id;
        $server = $DB->get_record_select("modmediawiki_servers", "id=$server_id");


        if ($server->oauth) {
	$consumer_key = $server->consumer_key;
	$consumer_secret = $server->consumer_secret;
	$access_token = $server->access_token;
	$access_secret = $server->access_secret;
	$consumer = new OAuthConsumer($consumer_key, $consumer_secret, NULL);
	$token = new OAuthToken($access_token, $access_secret, NULL);
        }

        $context = get_context_instance(CONTEXT_COURSE, $course_id);
        $contextlists = get_related_contexts_string($context);

        // Get new enrolled users that are not in the mediawiki-moodle equivalence's table
        $sql = "SELECT u.id, u.username, u.firstname, u.email
	      FROM {user} u
	      JOIN {role_assignments} ra ON ra.userid = u.id
	      LEFT OUTER JOIN {modmediawiki_users} mwu ON u.id = mwu.moodle_id
	     WHERE u.deleted = 0 AND u.confirmed = 1 AND ra.contextid $contextlists AND mwu.mediawiki_id IS NULL";
        $course_users = $DB->get_records_sql($sql);

        foreach ($course_users as $user) {
	$basefeed = rtrim($server->url, '/') . '/users';
	$params = array('name' => $user->username, 'email' => $user->email, 'realname' => $user->firstname, 'password' => substr(md5(rand() . rand()), 0, 15));

	if ($server->oauth) {
	    $request = OAuthRequest::from_consumer_and_token($consumer, $token, 'POST', $basefeed, $params);
	    $request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $consumer, $token);
	    $response = send_request($request->get_normalized_http_method(), $basefeed, $request->to_header(), $params);
	} else {
	    $response = send_request('POST', $basefeed, null, $params);
	}

	$json = json_decode($response);
	if ($json->mId != null) {
	    $dataobject = array();
	    $dataobject['moodle_id'] = $user->id;
	    $dataobject['mediawiki_id'] = $json->mId;
	    $dataobject['server_id'] = $server_id;
	    $DB->insert_record('modmediawiki_users', $dataobject, false, false);
	}
        }

    }
    return $newmod;
}

/**
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param int $id Id of the module instance
 * @return boolean Success/Failure
 */
function modmediawiki_delete_instance($id) {
    global $DB;

    if (! $modmediawiki_instance = $DB->get_record('modmediawiki', array('id' => $id))) {
        return false;
    }

    # Delete any dependent records here #

    $course_id = $modmediawiki_instance->course;
    $server_id = $modmediawiki_instance->server_id;
    $server = $DB->get_record_select("modmediawiki_servers", "id=$server_id");

    if ($server->oauth) {
        $consumer_key = $server->consumer_key;
        $consumer_secret = $server->consumer_secret;
        $access_token = $server->access_token;
        $access_secret = $server->access_secret;
        $consumer = new OAuthConsumer($consumer_key, $consumer_secret, NULL);
        $token = new OAuthToken($access_token, $access_secret, NULL);
    }

    $users = $DB->get_records_select("modmediawiki_users", "server_id=$server_id");
    foreach ($users as $user) {
        $basefeed = rtrim($server->url, '/') . "/users/$user->mediawiki_id";

        if ($server->oauth) {
	$request = OAuthRequest::from_consumer_and_token($consumer, $token, 'DELETE', $basefeed);
	$request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $consumer, $token);
	$response = modmediawiki_send_request($request->get_normalized_http_method(), $basefeed, $request->to_header());
        } else {
	$response = modmediawiki_send_request('DELETE', $basefeed);
        }

        $json = json_decode($response);
        if ($json->deleted) {
	$DB->delete_records("modmediawiki_users", array('id'=>$user->id));
        }
    }

    $DB->delete_records('modmediawiki', array('id' => $modmediawiki_instance->id));

    return true;
}

/**
 * Return a small object with summary information about what a
 * user has done with a given particular instance of this module
 * Used for user activity reports.
 * $return->time = the time they did it
 * $return->info = a short text description
 *
 * @return null
 * @todo Finish documenting this function
 */
function modmediawiki_user_outline($course, $user, $mod, $modmediawiki) {
    $return = new stdClass;
    $return->time = 0;
    $return->info = '';
    return $return;
}

/**
 * Print a detailed representation of what a user has done with
 * a given particular instance of this module, for user activity reports.
 *
 * @return boolean
 * @todo Finish documenting this function
 */
function modmediawiki_user_complete($course, $user, $mod, $modmediawiki) {
    return true;
}

/**
 * Given a course and a time, this module should find recent activity
 * that has occurred in modmediawiki activities and print it out.
 * Return true if there was output, or false is there was none.
 *
 * @return boolean
 * @todo Finish documenting this function
 */
function modmediawiki_print_recent_activity($course, $viewfullnames, $timestart) {
    return false;  //  True if anything was printed, otherwise false
}

/**
 * Function to be run periodically according to the moodle cron
 * This function searches for things that need to be done, such
 * as sending out mail, toggling flags etc ...
 *
 * @return boolean
 * @todo Finish documenting this function
 **/
function modmediawiki_cron () {
    return true;
}

/**
 * Must return an array of users who are participants for a given instance
 * of modmediawiki. Must include every user involved in the instance,
 * independient of his role (student, teacher, admin...). The returned
 * objects must contain at least id property.
 * See other modules as example.
 *
 * @param int $modmediawikiid ID of an instance of this module
 * @return boolean|array false if no participants, array of objects otherwise
 */
function modmediawiki_get_participants($modmediawikiid) {
    return false;
}

/**
 * @return array
 */
function modmediawiki_get_view_actions() {
    return array('view', 'view pages', 'view page');
}

/**
 * @return array
 */
function modmediawiki_get_post_actions() {
    return array('add','create page');
}

/**
 * This function returns if a scale is being used by one modmediawiki
 * if it has support for grading and scales. Commented code should be
 * modified if necessary. See forum, glossary or journal modules
 * as reference.
 *
 * @param int $modmediawikiid ID of an instance of this module
 * @return mixed
 * @todo Finish documenting this function
 */
function modmediawiki_scale_used($modmediawikiid, $scaleid) {
    global $DB;

    $return = false;

    //$rec = $DB->get_record("modmediawiki", array("id" => "$modmediawikiid", "scale" => "-$scaleid"));
    //
    //if (!empty($rec) && !empty($scaleid)) {
    //    $return = true;
    //}

    return $return;
}

/**
 * Checks if scale is being used by any instance of modmediawiki.
 * This function was added in 1.9
 *
 * This is used to find out if scale used anywhere
 * @param $scaleid int
 * @return boolean True if the scale is used by any modmediawiki
 */
function modmediawiki_scale_used_anywhere($scaleid) {
    global $DB;

    if ($scaleid and $DB->record_exists('modmediawiki', 'grade', -$scaleid)) {
        return true;
    } else {
        return false;
    }
}

/**
 * Execute post-uninstall custom actions for the module
 * This function was added in 1.9
 *
 * @return boolean true if success, false on error
 */
function modmediawiki_uninstall() {
    return true;
}

/**
 * This function gets run whenever user is enrolled into course
 *
 * @param object $cp
 * @return void
 */
function modmediawiki_user_enrolled($cp) {
    $context = get_context_instance(CONTEXT_COURSE, $cp->courseid);
    modmediawiki_add_user($cp->userid, $context);
}

/**
 * This function gets run whenever user is unenrolled from course
 *
 * @param object $cp
 * @return void
 */
function modmediawiki_user_unenrolled($cp) {
    if ($cp->lastenrol) {
        $context = get_context_instance(CONTEXT_COURSE, $cp->courseid);
        modmediawiki_remove_user($cp->userid, $context);
    }
}

/**
 * Add subscriptions for new users
 *
 * @global object
 * @uses CONTEXT_SYSTEM
 * @uses CONTEXT_COURSE
 * @uses CONTEXT_COURSECAT
 * @uses FORUM_INITIALSUBSCRIBE
 * @param int $userid
 * @param object $context
 * @return bool
 */
function modmediawiki_add_user($userid, $context) {
    global $DB;
    if (empty($context->contextlevel)) {
        return false;
    }

    switch ($context->contextlevel) {

        case CONTEXT_SYSTEM:   // For the whole site
	$rs = $DB->get_recordset('course', null, '', 'id');
	foreach ($rs as $course) {
	    $subcontext = get_context_instance(CONTEXT_COURSE, $course->id);
	    modmediawiki_add_user($userid, $subcontext);
	}
	$rs->close();
	break;

        case CONTEXT_COURSECAT:   // For a whole category
	$rs = $DB->get_recordset('course', array('category' => $context->instanceid), '', 'id');
	foreach ($rs as $course) {
	    $subcontext = get_context_instance(CONTEXT_COURSE, $course->id);
	    modmediawiki_add_user($userid, $subcontext);
	}
	$rs->close();
	if ($categories = $DB->get_records('course_categories', array('parent' => $context->instanceid))) {
	    foreach ($categories as $category) {
	        $subcontext = get_context_instance(CONTEXT_COURSECAT, $category->id);
	        modmediawiki_add_user($userid, $subcontext);
	    }
	}
	break;


        case CONTEXT_COURSE:   // For a whole course
	if (is_enrolled($context, $userid)) {
	    if ($course = $DB->get_record('course', array('id' => $context->instanceid))) {
	        if ($modsmediawiki = get_all_instances_in_course('modmediawiki', $course, $userid, false)) {
		foreach ($modsmediawiki as $modmediawiki_instance) {
		    $course_id = $modmediawiki_instance->course;
		    $server_id = $modmediawiki_instance->server_id;
		    $server = $DB->get_record_select("modmediawiki_servers", "id=$server_id");

		    $sql = "SELECT u.id, u.username, u.firstname, u.email
			  FROM {user} u
			 WHERE u.id = $userid";
		    $user = $DB->get_record_sql($sql);

		    $basefeed = rtrim($server->url, '/') . '/users';
                    $params = array('name' => $user->username, 'email' => $user->email, 'realname' => $user->firstname, 'password' => substr(md5(rand() . rand()), 0, 15));

		    if ($server->oauth) {
		        $consumer_key = $server->consumer_key;
		        $consumer_secret = $server->consumer_secret;
		        $access_token = $server->access_token;
		        $access_secret = $server->access_secret;
		        $consumer = new OAuthConsumer($consumer_key, $consumer_secret, NULL);
		        $token = new OAuthToken($access_token, $access_secret, NULL);
		        $request = OAuthRequest::from_consumer_and_token($consumer, $token, 'POST', $basefeed, $params);
		        $request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $consumer, $token);
		        $response = modmediawiki_send_request($request->get_normalized_http_method(), $basefeed, $request->to_header(), $params);
		    } else {
		        $response = modmediawiki_send_request('POST', $basefeed, null, $params);
		    }

		    $json = json_decode($response);
		    if (isset($json->mId)) {
		        $dataobject = array();
		        $dataobject['moodle_id'] = $user->id;
		        $dataobject['mediawiki_id'] = $json->mId;
		        $dataobject['server_id'] = $server_id;
		        $DB->insert_record('modmediawiki_users', $dataobject, false, false);
		    }
		}
	        }
	    }
	}
	break;

        case CONTEXT_MODULE:   // Just one forum
	if ($cm = get_coursemodule_from_id('modmediawiki', $context->instanceid)) {
	    if ($modmediawiki_instance = $DB->get_record('modmediawiki', array('id' => $cm->instance))) {
	        $course_id = $modmediawiki_instance->course;
	        $server_id = $modmediawiki_instance->server_id;
	        $server = $DB->get_record_select("modmediawiki_servers", "id=$server_id");

	        $sql = "SELECT u.id, u.username, u.firstname, u.email
		      FROM {user} u
		     WHERE u.id = $userid";
	        $user = $DB->get_record_sql($sql);

	        $basefeed = rtrim($server->url, '/') . '/users';
	        $params = array('name' => $user->username, 'email' => $user->email, 'realname' => $user->firstname, 'password' => substr(md5(rand() . rand()), 0, 15));

	        if ($server->oauth) {
		$consumer_key = $server->consumer_key;
		$consumer_secret = $server->consumer_secret;
		$access_token = $server->access_token;
		$access_secret = $server->access_secret;
		$consumer = new OAuthConsumer($consumer_key, $consumer_secret, NULL);
		$token = new OAuthToken($access_token, $access_secret, NULL);
		$request = OAuthRequest::from_consumer_and_token($consumer, $token, 'POST', $basefeed, $params);
		$request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $consumer, $token);
		$response = modmediawiki_send_request($request->get_normalized_http_method(), $basefeed, $request->to_header(), $params);
	        } else {
		$response = modmediawiki_send_request('POST', $basefeed, null, $params);
	        }

	        $json = json_decode($response);
	        if (isset($json->mId)) {
				$dataobject = array();
				$dataobject['moodle_id'] = $user->id;
				$dataobject['mediawiki_id'] = $json->mId;
				$dataobject['server_id'] = $server_id;
				$DB->insert_record('modmediawiki_users', $dataobject, false, false);
	        }
	    }
	}
	break;
    }

    return true;
}

/**
 * Remove subscriptions for a user in a context
 *
 * @global object
 * @global object
 * @uses CONTEXT_SYSTEM
 * @uses CONTEXT_COURSECAT
 * @uses CONTEXT_COURSE
 * @uses CONTEXT_MODULE
 * @param int $userid
 * @param object $context
 * @return bool
 */
function modmediawiki_remove_user($userid, $context) {

    global $CFG, $DB;

    if (empty($context->contextlevel)) {
        return false;
    }

    switch ($context->contextlevel) {

        case CONTEXT_SYSTEM:   // For the whole site
	// find all courses in which this user has a forum subscription
	if ($courses = $DB->get_records_sql("SELECT c.id
                                                  FROM {course} c,
                                                       {forum_subscriptions} fs,
                                                       {forum} f
                                                       WHERE c.id = f.course AND f.id = fs.forum AND fs.userid = ?
                                                       GROUP BY c.id", array($userid))) {

	    foreach ($courses as $course) {
	        $subcontext = get_context_instance(CONTEXT_COURSE, $course->id);
	        modmediawiki_remove_user($userid, $subcontext);
	    }
	}
	break;

        case CONTEXT_COURSECAT:   // For a whole category
	if ($courses = $DB->get_records('course', array('category' => $context->instanceid), '', 'id')) {
	    foreach ($courses as $course) {
	        $subcontext = get_context_instance(CONTEXT_COURSE, $course->id);
	        modmediawiki_remove_user($userid, $subcontext);
	    }
	}
	if ($categories = $DB->get_records('course_categories', array('parent' => $context->instanceid), '', 'id')) {
	    foreach ($categories as $category) {
	        $subcontext = get_context_instance(CONTEXT_COURSECAT, $category->id);
	        modmediawiki_remove_user($userid, $subcontext);
	    }
	}
	break;

        case CONTEXT_COURSE:   // For a whole course
	if (!is_enrolled($context, $userid)) {
	    if ($course = $DB->get_record('course', array('id' => $context->instanceid), 'id')) {
	        // find all forums in which this user has a subscription, and its coursemodule id
	        if ($modsmediawiki = $DB->get_records_sql("SELECT * FROM {modmediawiki} WHERE course = ?", array($context->instanceid))) {

		// TODO no todos los mediawiki usan OAUTH
		foreach ($modsmediawiki as $mediawiki) {
		    $server = $DB->get_record_select("modmediawiki_servers", "id=$mediawiki->server_id");

		    if ($server->oauth) {
		        $consumer_key = $server->consumer_key;
		        $consumer_secret = $server->consumer_secret;
		        $access_token = $server->access_token;
		        $access_secret = $server->access_secret;
		        $consumer = new OAuthConsumer($consumer_key, $consumer_secret, NULL);
		        $token = new OAuthToken($access_token, $access_secret, NULL);
		    }

		    $users = $DB->get_records_select("modmediawiki_users", "server_id=$mediawiki->server_id and moodle_id=$userid");
		    foreach ($users as $user) {
		        $basefeed = rtrim($server->url, '/') . "/users/$user->mediawiki_id";
		        if ($server->oauth) {
			$request = OAuthRequest::from_consumer_and_token($consumer, $token, 'DELETE', $basefeed);
			$request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $consumer, $token);
			$response = modmediawiki_send_request($request->get_normalized_http_method(), $basefeed, $request->to_header());
		        } else {
			$response = modmediawiki_send_request('DELETE', $basefeed);
		        }

		        $json = json_decode($response);
		        if ($json->deleted) {
			$DB->delete_records("modmediawiki_users", array('id'=>$user->id));
		        }
		        break;
		    }
		}
	        }
	    }
	}
	break;

        case CONTEXT_MODULE:   // Just one forum
	if (!is_enrolled($context, $userid)) {
	    if ($cm = get_coursemodule_from_id('forum', $context->instanceid)) {
	        if ($mediawiki = $DB->get_record('modmediawiki', array('id' => $cm->instance))) {
		$server = $DB->get_record_select("modmediawiki_servers", "id=$mediawiki->server_id");

		if ($server->oauth) {
		    $consumer_key = $server->consumer_key;
		    $consumer_secret = $server->consumer_secret;
		    $access_token = $server->access_token;
		    $access_secret = $server->access_secret;
		    $consumer = new OAuthConsumer($consumer_key, $consumer_secret, NULL);
		    $token = new OAuthToken($access_token, $access_secret, NULL);
		}

		$users = $DB->get_record_select("modmediawiki_users", "server_id=$mediawiki->server_id");
		foreach ($users as $user) {
		    $basefeed = rtrim($server->url, '/') . "/users/$user->mediawiki_id";

		    if ($server->oauth) {
		        $request = OAuthRequest::from_consumer_and_token($consumer, $token, 'DELETE', $basefeed);
		        $request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $consumer, $token);
		        $response = modmediawiki_send_request($request->get_normalized_http_method(), $basefeed, $request->to_header());
		    } else {
		        $response = modmediawiki_send_request('DELETE', $basefeed);
		    }

		    $json = json_decode($response);
		    if ($json->deleted) {
		        $DB->delete_records("modmediawiki_users", array('id'=>$user->id));
		    }
		    break;
		}
	        }
	    }
	}
	break;
    }

    return true;
}


/**
 * Makes an HTTP request to the specified URL
 * @param string $http_method The HTTP method (GET, POST, PUT, DELETE)
 * @param string $url Full URL of the resource to access
 * @param string $auth_header (optional) Authorization header
 * @param string $postData (optional) POST/PUT request body
 * @return string Response body from the server
 */
function modmediawiki_send_request($http_method, $url, $auth_header=null, $postData=null) {
  $curl = curl_init($url);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
  //curl_setopt($curl, CURLOPT_FAILONERROR, false);
  //curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

  switch($http_method) {
    case 'GET':
      if ($auth_header) {
        curl_setopt($curl, CURLOPT_HTTPHEADER, array($auth_header));
      }
      break;
    case 'POST':
      curl_setopt($curl, CURLOPT_HTTPHEADER, array($auth_header));
      curl_setopt($curl, CURLOPT_POST, 1);
      curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
      break;
    case 'PUT':
      curl_setopt($curl, CURLOPT_HTTPHEADER, array($auth_header));
      curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $http_method);
      curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($postData));
      break;
    case 'DELETE':
      curl_setopt($curl, CURLOPT_HTTPHEADER, array($auth_header));
      curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $http_method);
      break;
  }
  $response = curl_exec($curl);
  if (!$response) {
    $response = curl_error($curl);
  }
  curl_close($curl);
  return $response;
}