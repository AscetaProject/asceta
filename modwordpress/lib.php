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
 * Library of interface functions and constants for module modredmine
 *
 * All the core Moodle functions, neeeded to allow the module to work
 * integrated in Moodle should be placed here.
 * All the modredmine specific functions, needed to implement all the module
 * logic, should go to locallib.php. This will help to save some memory when
 * Moodle is performing actions across all modules.
 *
 * @package   mod_modredmine
 * @copyright 2011 Vicente Manuel García Huete (vmgarcia@fidesol.org) - Fundación I+D del Software Libre (www.fidesol.org)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__) . '/locallib.php');




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
 * @param object $modredmine An object from the form in mod_form.php
 * @return int The id of the newly inserted modredmine record
 */
function modredmine_add_instance($modredmine) {
  global $DB;
  error_log("[ERROR REDMINE] ");

  $modredmine->timecreated = time();

  # You may have to add extra stuff in here #
  $newmod = $DB->insert_record('modredmine', $modredmine);
  error_log('modredmine_add_instance');

  if ($newmod) {

    //Adding Moodle users to redmine
    $modredmine_instance = $DB->get_record_select("modredmine", "id=$newmod");
    $course_id = $modredmine_instance->course;
    $server_id = $modredmine_instance->server_id;
    $server = $DB->get_record_select("modredmine_servers", "id=$server_id");

    $api_key = null;
    if ($server->auth) {
      $api_key = $server->api_key;
    }
    if (strlen($api_key)) {
      $api_key .= ':@';
    }
    $server_url = rtrim($server->url, '/');
    $server_url = str_replace('http://', '', $server_url);
    $server_url = "http://$api_key$server_url/";

    $context = get_context_instance(CONTEXT_COURSE, $course_id);
    $contextlists = get_related_contexts_string($context);

    $sql = "SELECT u.id, u.username, u.firstname, u.lastname, u.email
      FROM {user} u
      JOIN {role_assignments} ra ON ra.userid = u.id
      WHERE u.deleted = 0 AND u.confirmed = 1 AND ra.contextid $contextlists";
    $course_users = $DB->get_records_sql($sql);

    foreach ($course_users as $user) {
      $pass = substr(md5(rand() . rand()), 0, 15);
      $redmine_user = new RedmineUser(array(
        'login' => $user->username,
        'password' => $pass,
        'mail' => $user->email,
        'firstname' => $user->firstname,
        'lastname' => $user->lastname
      ));
      $redmine_user->setSite($server_url);
      $redmine_user->save();

      if ($redmine_user->errno == NULL) {
        $dataobject = array();
        $dataobject['moodle_id'] = $user->id;
        $dataobject['redmine_id'] = $redmine_user->id;
        $dataobject['server_id'] = $server_id;
        $dataobject['redmine_login'] = $redmine_user->login;
        $dataobject['redmine_password'] = $pass;
        $DB->insert_record('modredmine_users', $dataobject, false, false);
      } else {
        error_log("[ERROR REDMINE: ".$redmine_user->errno."] " . $redmine_user->errno . ":" . $redmine_user->error);
      }
    }

    error_log("Project Name: ".$modredmine_instance->project_name);
    error_log("Project Identifier: ".$modredmine_instance->project_identifier);
    error_log("Project Description: ".$modredmine_instance->project_description);

    $redmine_project = new RedmineProject(array(
      'name' => $modredmine_instance->project_name,
      'identifier' => $modredmine_instance->project_identifier,
      'description' => $modredmine_instance->project_description
    ));
    $redmine_project->setSite($server_url);
    $redmine_project->save();
    if (!isset($redmine_project->errno) || !strlen($redmine_project->errno)) {
      $modredmine_instance->project_id = $redmine_project->id;
      if (!$DB->set_field('modredmine', 'project_id', $redmine_project->id, array('id'=>$modredmine_instance->id))) {
        error_log("[MOODLE DB ERROR] set_field modredmine project_id ".$redmine_project->id." array('id'=>".$modredmine_instance->id);
      }
    } else {
      error_log("[ERROR REDMINE: ".$redmine_project->errno."] " . $redmine_project->errno . ":" . $redmine_project->error);
    }

  }
  return $newmod;
}

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @param object $modredmine An object from the form in mod_form.php
 * @return boolean Success/Fail
 */
function modredmine_update_instance($modredmine) {
  global $DB;

  $modredmine->timemodified = time();
  $modredmine->id = $modredmine->instance;

  # You may have to add extra stuff in here #

  $newmod = $DB->update_record('modredmine', $modredmine);


  if ($newmod) {

    //Adding Moodle users to redmine
    //$modredmine_instance = $DB->get_record_select("modredmine", "id=$newmod");
    $course_id = $modredmine->course;
    $server_id = $modredmine->server_id;
    $server = $DB->get_record_select("modredmine_servers", "id=$server_id");


    $api_key = null;
    if ($server->auth) {
      $api_key = $server->api_key;
    }
    if (strlen($api_key)) {
      $api_key .= ':@';
    }
    $server_url = rtrim($server->url, '/');
    $server_url = str_replace('http://', '', $server_url);
    $server_url = "http://$api_key$server_url/";

    $context = get_context_instance(CONTEXT_COURSE, $course_id);
    $contextlists = get_related_contexts_string($context);

    // Get new enrolled users that are not in the redmine-moodle equivalence's table
    $sql = "SELECT u.id, u.username, u.firstname, u.lastname, u.email
      FROM {user} u
      JOIN {role_assignments} ra ON ra.userid = u.id
      LEFT OUTER JOIN {modredmine_users} rmu ON u.id = rmu.moodle_id
      WHERE u.deleted = 0 AND u.confirmed = 1 AND ra.contextid $contextlists AND rmu.redmine_id IS NULL";

    error_log(str_replace("\r\n\t", '', $sql));
    $course_users = $DB->get_records_sql($sql);

    foreach ($course_users as $user) {
      error_log($user->username . " " . $user->firstname . " " . $user->lastname);
      $pass = substr(md5(rand() . rand()), 0, 15);
      $redmine_user = new RedmineUser(array(
        'login' => $user->username,
        'password' => $pass,
        'mail' => $user->email,
        'firstname' => $user->firstname,
        'lastname' => $user->lastname
      ));
      $redmine_user->setSite($server_url);
      $redmine_user->save();

      $dataobject = array();
      $dataobject['moodle_id'] = $user->id;
      $dataobject['redmine_id'] = $redmine_user->id;
      $dataobject['server_id'] = $server_id;
      $dataobject['redmine_login'] = $redmine_user->login;
      $dataobject['redmine_password'] = $pass;
      if ($redmine_user->errno == NULL) {
        $DB->insert_record('modredmine_users', $dataobject, false, false);
      }elseif($redmine_user->errno == 422) {
        $DB->insert_record('modredmine_users', $dataobject, false, false);
      } else {
        error_log("[ERROR REDMINE: ".$redmine_user->errno."] " . $redmine_user->error);
      }
    }

    $redmine_project = new RedmineProject();
    $redmine_project->setSite($server_url);
    $redmine_project->find($modredmine->project_id);
    $redmine_project->set('name', $modredmine->project_name);
    $redmine_project->set('description', $modredmine->project_description);
    $redmine_project->save();
    if (isset($redmine_project->errno) || strlen($redmine_project->errno)) {
      error_log("[ERROR REDMINE: ".$redmine_user->errno."] " . $redmine_project->errno . ":" . $redmine_project->error);
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
function modredmine_delete_instance($id) {
  global $DB;

  if (!$modredmine_instance = $DB->get_record('modredmine', array('id' => $id))) {
    return false;
  }

  # Delete any dependent records here #

  $course_id = $modredmine_instance->course;
  $server_id = $modredmine_instance->server_id;
  $server = $DB->get_record_select("modredmine_servers", "id=$server_id");

  $api_key = null;
  if ($server->auth) {
    $api_key = $server->api_key;
  }
  if (strlen($api_key)) {
    $api_key .= ':@';
  }
  $server_url = rtrim($server->url, '/');
  $server_url = str_replace('http://', '', $server_url);
  $server_url = "http://$api_key$server_url/";

  $users = $DB->get_records_select("modredmine_users", "server_id=$server_id");
  foreach ($users as $user) {
    $redmine_user = new RedmineUser();
    $redmine_user->setSite($server_url);
    $redmine_user->find($user->redmine_id);
    $redmine_user->destroy();
    if (!$redmine_user->errno) {
      $DB->delete_records("modredmine_users", array('id' => $user->id));
    } else {
      error_log("[ERROR REDMINE: ".$redmine_user->errno."] " . $redmine_user->error);
    }
  }
  /*
  if (isset($modredmine_instance->project_id)) {
    $redmine_project = new RedmineProject();
    $redmine_project->setSite($server_url);
    $redmine_project->find($modredmine_instance->project_id);
    $redmine_project->destroy();
    if ($redmine_project->errno == NULL) {
      error_log("[ERROR REDMINE: ".$redmine_project->errno."] " . $redmine_project->error);
    }
  }
   */

  $DB->delete_records('modredmine', array('id' => $modredmine_instance->id));

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
function modredmine_user_outline($course, $user, $mod, $modredmine) {
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
function modredmine_user_complete($course, $user, $mod, $modredmine) {
  return true;
}

/**
 * Given a course and a time, this module should find recent activity
 * that has occurred in modredmine activities and print it out.
 * Return true if there was output, or false is there was none.
 *
 * @return boolean
 * @todo Finish documenting this function
 */
function modredmine_print_recent_activity($course, $viewfullnames, $timestart) {
  return false;  //  True if anything was printed, otherwise false
}

/**
 * Function to be run periodically according to the moodle cron
 * This function searches for things that need to be done, such
 * as sending out mail, toggling flags etc ...
 *
 * @return boolean
 * @todo Finish documenting this function
 * */
function modredmine_cron() {
  return true;
}

/**
 * Must return an array of users who are participants for a given instance
 * of modredmine. Must include every user involved in the instance,
 * independient of his role (student, teacher, admin...). The returned
 * objects must contain at least id property.
 * See other modules as example.
 *
 * @param int $modredmineid ID of an instance of this module
 * @return boolean|array false if no participants, array of objects otherwise
 */
function modredmine_get_participants($modredmineid) {
  return false;
}

/**
 * @return array
 */
function modredmine_get_view_actions() {
  return array('view', 'view posts', 'view posts', 'view page', 'view comments');
}

/**
 * @return array
 */
function modredmine_get_post_actions() {
  return array('add', 'create comment', 'create post', 'create page');
}

/**
 * This function returns if a scale is being used by one modredmine
 * if it has support for grading and scales. Commented code should be
 * modified if necessary. See forum, glossary or journal modules
 * as reference.
 *
 * @param int $modredmineid ID of an instance of this module
 * @return mixed
 * @todo Finish documenting this function
 */
function modredmine_scale_used($modredmineid, $scaleid) {
  global $DB;

  $return = false;

  //$rec = $DB->get_record("modredmine", array("id" => "$modredmineid", "scale" => "-$scaleid"));
  //
  //if (!empty($rec) && !empty($scaleid)) {
  //    $return = true;
  //}

  return $return;
}

/**
 * Checks if scale is being used by any instance of modredmine.
 * This function was added in 1.9
 *
 * This is used to find out if scale used anywhere
 * @param $scaleid int
 * @return boolean True if the scale is used by any modredmine
 */
function modredmine_scale_used_anywhere($scaleid) {
  global $DB;

  if ($scaleid and $DB->record_exists('modredmine', 'grade', -$scaleid)) {
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
function modredmine_uninstall() {
  return true;
}

/**
 * This function gets run whenever user is enrolled into course
 *
 * @param object $cp
 * @return void
 */
function modredmine_user_enrolled($cp) {
  $context = get_context_instance(CONTEXT_COURSE, $cp->courseid);
  modredmine_add_user($cp->userid, $context);
}

/**
 * This function gets run whenever user is unenrolled from course
 *
 * @param object $cp
 * @return void
 */
function modredmine_user_unenrolled($cp) {
  if ($cp->lastenrol) {
    $context = get_context_instance(CONTEXT_COURSE, $cp->courseid);
    modredmine_remove_user($cp->userid, $context);
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
function modredmine_add_user($userid, $context) {
  global $DB;
  if (empty($context->contextlevel)) {
    return false;
  }

  switch ($context->contextlevel) {

  case CONTEXT_SYSTEM:   // For the whole site
    $rs = $DB->get_recordset('course', null, '', 'id');
    foreach ($rs as $course) {
      $subcontext = get_context_instance(CONTEXT_COURSE, $course->id);
      modredmine_add_user($userid, $subcontext);
    }
    $rs->close();
    break;

  case CONTEXT_COURSECAT:   // For a whole category
    $rs = $DB->get_recordset('course', array('category' => $context->instanceid), '', 'id');
    foreach ($rs as $course) {
      $subcontext = get_context_instance(CONTEXT_COURSE, $course->id);
      modredmine_add_user($userid, $subcontext);
    }
    $rs->close();
    if ($categories = $DB->get_records('course_categories', array('parent' => $context->instanceid))) {
      foreach ($categories as $category) {
        $subcontext = get_context_instance(CONTEXT_COURSECAT, $category->id);
        modredmine_add_user($userid, $subcontext);
      }
    }
    break;


  case CONTEXT_COURSE:   // For a whole course
    if (is_enrolled($context, $userid)) {
      if ($course = $DB->get_record('course', array('id' => $context->instanceid))) {
        if ($modsredmine = get_all_instances_in_course('modredmine', $course, $userid, false)) {
          foreach ($modsredmine as $modredmine_instance) {
            $course_id = $modredmine_instance->course;
            $server_id = $modredmine_instance->server_id;
            $server = $DB->get_record_select("modredmine_servers", "id=$server_id");

            $api_key = null;
            if ($server->auth) {
              $api_key = $server->api_key;
            }
            if (strlen($api_key)) {
              $api_key .= ':@';
            }
            $server_url = rtrim($server->url, '/');
            $server_url = str_replace('http://', '', $server_url);
            $server_url = "http://$api_key$server_url/";


            $sql = "SELECT u.id, u.username, u.firstname, u.lastname, u.email
              FROM {user} u
              WHERE u.id = $userid";
            $user = $DB->get_record_sql($sql);
            $pass = substr(md5(rand() . rand()), 0, 15);
            $redmine_user = new RedmineUser(array(
              'login' => $user->username,
              'password' => $pass,
              'mail' => $user->email,
              'firstname' => $user->firstname,
              'lastname' => $user->lastname
            ));
            $redmine_user->setSite($server_url);
            $redmine_user->save();

            $dataobject = array();
            $dataobject['moodle_id'] = $user->id;
            $dataobject['redmine_id'] = $redmine_user->id;
            $dataobject['server_id'] = $server_id;
            $dataobject['redmine_login'] = $redmine_user->login;
            $dataobject['redmine_password'] = $pass;
            if ($redmine_user->errno == NULL) {
              $DB->insert_record('modredmine_users', $dataobject, false, false);
            } elseif($redmine_user->errno == 422) {
              $DB->insert_record('modredmine_users', $dataobject, false, false);
            } else {
              error_log("[ERROR REDMINE: ".$redmine_user->errno."] " . $redmine_user->error);
            }
          }
        }
      }
    }
    break;

  case CONTEXT_MODULE:   // Just one forum
    if ($cm = get_coursemodule_from_id('modredmine', $context->instanceid)) {
      if ($modredmine_instance = $DB->get_record('modredmine', array('id' => $cm->instance))) {
        $course_id = $modredmine_instance->course;
        $server_id = $modredmine_instance->server_id;
        $server = $DB->get_record_select("modredmine_servers", "id=$server_id");

        $api_key = null;
        if ($server->auth) {
          $api_key = $server->api_key;
        }
        if (strlen($api_key)) {
          $api_key .= ':@';
        }
        $server_url = rtrim($server->url, '/');
        $server_url = str_replace('http://', '', $server_url);
        $server_url = "http://$api_key$server_url/";


        $sql = "SELECT u.id, u.username, u.firstname, u.lastname, u.email
          FROM {user} u
          WHERE u.id = $userid";
        $user = $DB->get_record_sql($sql);
        $pass = substr(md5(rand() . rand()), 0, 15);
        $redmine_user = new RedmineUser(array(
          'login' => $user->username,
          'password' => $pass,
          'mail' => $user->email,
          'firstname' => $user->firstname,
          'lastname' => $user->lastname
        ));
        $redmine_user->setSite($server_url);
        $redmine_user->save();

        $dataobject = array();
        $dataobject['moodle_id'] = $user->id;
        $dataobject['redmine_id'] = $redmine_user->id;
        $dataobject['server_id'] = $server_id;
        $dataobject['redmine_login'] = $redmine_user->login;
        $dataobject['redmine_password'] = $pass;
        $dataobject['redmine_key'] = NULL;
        if ($redmine_user->errno == NULL) {
          $DB->insert_record('modredmine_users', $dataobject, false, false);
        } elseif($redmine_user->errno == 422) {
          $DB->insert_record('modredmine_users', $dataobject, false, false);

        } else {
          error_log("[ERROR REDMINE: ".$redmine_user->errno."] " . $redmine_user->error);
        }
      }
    }
    break;
    return true;
  }
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
function modredmine_remove_user($userid, $context) {

  global $CFG, $DB;

  if (empty($context->contextlevel)) {
    return false;
  }

  error_log(" Context Level: $context->contextlevel");

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
        modredmine_remove_user($userid, $subcontext);
      }
    }
    break;

  case CONTEXT_COURSECAT:   // For a whole category
    if ($courses = $DB->get_records('course', array('category' => $context->instanceid), '', 'id')) {
      foreach ($courses as $course) {
        $subcontext = get_context_instance(CONTEXT_COURSE, $course->id);
        modredmine_remove_user($userid, $subcontext);
      }
    }
    if ($categories = $DB->get_records('course_categories', array('parent' => $context->instanceid), '', 'id')) {
      foreach ($categories as $category) {
        $subcontext = get_context_instance(CONTEXT_COURSECAT, $category->id);
        modredmine_remove_user($userid, $subcontext);
      }
    }
    break;

  case CONTEXT_COURSE:   // For a whole course
    if (!is_enrolled($context, $userid)) {
      if ($course = $DB->get_record('course', array('id' => $context->instanceid), 'id')) {
        // find all forums in which this user has a subscription, and its coursemodule id
        if ($modsredmine = $DB->get_records_sql("SELECT * FROM {modredmine} WHERE course = ?", array($context->instanceid))) {

          // TODO no todos los redmine usan OAUTH
          foreach ($modsredmine as $redmine) {
            $server = $DB->get_record_select("modredmine_servers", "id=$redmine->server_id");

            $api_key = null;
            if ($server->auth) {
              $api_key = $server->api_key;
            }
            if (strlen($api_key)) {
              $api_key .= ':@';
            }
            $server_url = rtrim($server->url, '/');
            $server_url = str_replace('http://', '', $server_url);
            $server_url = "http://$api_key$server_url/";

            $users = $DB->get_records_select("modredmine_users", "server_id=$redmine->server_id and moodle_id=$userid");
            foreach ($users as $user) {
              $redmine_user = new RedmineUser();
              $redmine_user->setSite($server_url);
              $redmine_user->find($user->redmine_id);
              $redmine_user->destroy();
              if ($redmine_user->errno == NULL) {
                $DB->delete_records("modredmine_users", array('id' => $user->id));
              } else {
                error_log("[ERROR REDMINE: ".$redmine_user->errno."] " . $redmine_user->error);
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
        if ($redmine = $DB->get_record('modredmine', array('id' => $cm->instance))) {
          $server = $DB->get_record_select("modredmine_servers", "id=$redmine->server_id");

          $server = $DB->get_record_select("modredmine_servers", "id=$redmine->server_id");

          $api_key = null;
          if ($server->auth) {
            $api_key = $server->api_key;
          }
          if (strlen($api_key)) {
            $api_key .= ':@';
          }
          $server_url = rtrim($server->url, '/');
          $server_url = str_replace('http://', '', $server_url);
          $server_url = "http://$api_key$server_url/";

          $users = $DB->get_records_select("modredmine_users", "server_id=$redmine->server_id and moodle_id=$userid");
          foreach ($users as $user) {
            $redmine_user = new RedmineUser();
            $redmine_user->setSite($server_url);
            $redmine_user->find($user->redmine_id);
            $redmine_user->destroy();
            if ($redmine_user->errno == NULL) {
              $DB->delete_records("modredmine_users", array('id' => $user->id));
            } else {
              error_log("[ERROR REDMINE: ".$redmine_user->errno."] " . $redmine_user->error);
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

