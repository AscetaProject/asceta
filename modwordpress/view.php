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

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(__FILE__).'/locallib.php');
require_once(dirname(__FILE__).'/OAuth.php');

$id = optional_param('id', 0, PARAM_INT); // course_module ID, or
$n  = optional_param('n', 0, PARAM_INT);  // modwordpress instance ID - it should be named as the first character of the module

if ($id) {
    $cm         = get_coursemodule_from_id('modwordpress', $id, 0, false, MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $modwordpress  = $DB->get_record('modwordpress', array('id' => $cm->instance), '*', MUST_EXIST);
} elseif ($n) {
    $modwordpress  = $DB->get_record('modwordpress', array('id' => $n), '*', MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $modwordpress->course), '*', MUST_EXIST);
    $cm         = get_coursemodule_from_instance('modwordpress', $modwordpress->id, $course->id, false, MUST_EXIST);
} else {
    error('You must specify a course_module ID or an instance ID');
}

if ($modwordpress->server_id) {
    $server = $DB->get_record('modwordpress_servers', array('id' => $modwordpress->server_id), '*', MUST_EXIST);
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
echo $OUTPUT->header();


if (!$modwordpress->server_id) {
    echo $OUTPUT->heading(get_string("configure_server_url","modwordpress"));
} else {
    echo $OUTPUT->heading($modwordpress->name.'\'s Posts');
    $consumer = new OAuthConsumer($server->consumer_key, $server->consumer_secret, NULL);
    $token = new OAuthToken($server->access_token, $server->access_secret, NULL);
    $basefeed = rtrim($server->url,'/').'/posts';
    $request = OAuthRequest::from_consumer_and_token($consumer, $token, 'GET', $basefeed, array());
    $request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $consumer, $token);
    $response = send_request($request->get_normalized_http_method(), $basefeed, $request->to_header());
    echo htmlentities($response);

}





// Finish the page
echo $OUTPUT->footer();
