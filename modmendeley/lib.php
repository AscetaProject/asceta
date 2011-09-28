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
 * Library of interface functions and constants for module modmendeley
 *
 * All the core Moodle functions, neeeded to allow the module to work
 * integrated in Moodle should be placed here.
 * All the modmendeley specific functions, needed to implement all the module
 * logic, should go to locallib.php. This will help to save some memory when
 * Moodle is performing actions across all modules.
 *
 * @package   mod_modmendeley
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
 * @param object $modmendeley An object from the form in mod_form.php
 * @return int The id of the newly inserted modmendeley record
 */
function modmendeley_add_instance($modmendeley) {
    global $DB;

    $modmendeley->timecreated = time();

    # You may have to add extra stuff in here #

    if($modmendeley->permission_create_document == null) $modmendeley->permission_create_document = 0;
    if($modmendeley->permission_delete_document == null) $modmendeley->permission_delete_document = 0;
    if($modmendeley->permission_new_folder == null) $modmendeley->permission_new_folder = 0;
    if($modmendeley->permission_delete_folder == null) $modmendeley->permission_delete_folder = 0;
    if($modmendeley->permission_add_doc_folder == null) $modmendeley->permission_add_doc_folder = 0;
    if($modmendeley->permission_delete_doc_folder == null) $modmendeley->permission_delete_doc_folder = 0;
    if($modmendeley->permission_new_group == null) $modmendeley->permission_new_group = 0;
    if($modmendeley->permission_delete_group == null) $modmendeley->permission_delete_group = 0;


    return $DB->insert_record('modmendeley', $modmendeley);
}

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @param object $modmendeley An object from the form in mod_form.php
 * @return boolean Success/Fail
 */
function modmendeley_update_instance($modmendeley) {
    global $DB;

    $modmendeley->timemodified = time();
    $modmendeley->id = $modmendeley->instance;

    # You may have to add extra stuff in here #

    if($modmendeley->permission_create_document == null) $modmendeley->permission_create_document = 0;
    if($modmendeley->permission_delete_document == null) $modmendeley->permission_delete_document = 0;
    if($modmendeley->permission_new_folder == null) $modmendeley->permission_new_folder = 0;
    if($modmendeley->permission_delete_folder == null) $modmendeley->permission_delete_folder = 0;
    if($modmendeley->permission_add_doc_folder == null) $modmendeley->permission_add_doc_folder = 0;
    if($modmendeley->permission_delete_doc_folder == null) $modmendeley->permission_delete_doc_folder = 0;
    if($modmendeley->permission_new_group == null) $modmendeley->permission_new_group = 0;
    if($modmendeley->permission_delete_group == null) $modmendeley->permission_delete_group = 0;

    return $DB->update_record('modmendeley', $modmendeley);
}

/**
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param int $id Id of the module instance
 * @return boolean Success/Failure
 */
function modmendeley_delete_instance($id) {
    global $DB;

    if (! $modmendeley = $DB->get_record('modmendeley', array('id' => $id))) {
        return false;
    }

    # Delete any dependent records here #

    $DB->delete_records('modmendeley', array('id' => $modmendeley->id));

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
function modmendeley_user_outline($course, $user, $mod, $modmendeley) {
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
function modmendeley_user_complete($course, $user, $mod, $modmendeley) {
    return true;
}

/**
 * Given a course and a time, this module should find recent activity
 * that has occurred in modmendeley activities and print it out.
 * Return true if there was output, or false is there was none.
 *
 * @return boolean
 * @todo Finish documenting this function
 */
function modmendeley_print_recent_activity($course, $viewfullnames, $timestart) {
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
function modmendeley_cron () {
    return true;
}

/**
 * Must return an array of users who are participants for a given instance
 * of modmendeley. Must include every user involved in the instance,
 * independient of his role (student, teacher, admin...). The returned
 * objects must contain at least id property.
 * See other modules as example.
 *
 * @param int $modmendeleyid ID of an instance of this module
 * @return boolean|array false if no participants, array of objects otherwise
 */
function modmendeley_get_participants($modmendeleyid) {
    return false;
}

/**
 * @return array
 */
function modmendeley_get_view_actions() {
    return array('view', 'view paper', 'view library', 'view group', 'view people');
}

/**
 * @return array
 */
function modmendeley_get_post_actions() {
    return array('add document','delete document', 'add document to folder', 'delete document from folder', 'add folder', 'delete folder', 'add group', 'delete group');
}

/**
 * This function returns if a scale is being used by one modmendeley
 * if it has support for grading and scales. Commented code should be
 * modified if necessary. See forum, glossary or journal modules
 * as reference.
 *
 * @param int $modmendeleyid ID of an instance of this module
 * @return mixed
 * @todo Finish documenting this function
 */
function modmendeley_scale_used($modmendeleyid, $scaleid) {
    global $DB;

    $return = false;

    //$rec = $DB->get_record("modmendeley", array("id" => "$modmendeleyid", "scale" => "-$scaleid"));
    //
    //if (!empty($rec) && !empty($scaleid)) {
    //    $return = true;
    //}

    return $return;
}

/**
 * Checks if scale is being used by any instance of modmendeley.
 * This function was added in 1.9
 *
 * This is used to find out if scale used anywhere
 * @param $scaleid int
 * @return boolean True if the scale is used by any modmendeley
 */
function modmendeley_scale_used_anywhere($scaleid) {
    global $DB;

    if ($scaleid and $DB->record_exists('modmendeley', 'grade', -$scaleid)) {
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
function modmendeley_uninstall() {
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
function modmendeley_send_request($http_method, $url, $auth_header=null, $postData=null, $getData=null) {
  $curl = curl_init($url);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
  //curl_setopt($curl, CURLOPT_FAILONERROR, false);
  //curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

  switch($http_method) {
    case 'GET':
      if ($auth_header) {
        curl_setopt($curl, CURLOPT_HTTPHEADER, array($auth_header));
      }
      if ($getData){
        curl_setopt($curl, CURLOPT_URL, $url. (strpos($url, '?') === FALSE ? '?' : ''). str_replace('&amp;', '&', http_build_query($getData)));
      }
      break;
    case 'POST':
      if($auth_header){
        curl_setopt($curl, CURLOPT_HTTPHEADER, array($auth_header));
      }
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
