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
 * Internal library of functions for module modredmine
 *
 * All the modredmine specific functions, needed to implement the module
 * logic, should go here. Never include this file from your lib.php!
 *
 * @package   mod_modredmine
 * @copyright 2011 Vicente Manuel García Huete (vmgarcia@fidesol.org) - Fundación I+D del Software Libre (www.fidesol.org)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();
require_once("ActiveResource.php");

define("BUG", 1);
define("FEATURE", 2);
define("SUPPORT", 3);
define("OPEN", 'open');
define("CLOSED", 'closed');
define("ALL", 'all');
define("MAX_SUMMARY_NEWS", 10);
define("MAX_SUMMARY_ISSUES", 10);
define("MAX_ISSUES", 10);
define("MAX_NEWS", 10);
define("MAX_TIME_ENTRIES", 10);


class RedmineUser extends ActiveResource {

  var $site = "";
  var $request_format = "xml";
  var $element_name = "user";
  var $element_name_plural = "users";

  function setSite($data) {
    $this->site = $data;
    return true;
  }

}

class RedmineIssue extends ActiveResource {

  var $site = "";
  var $request_format = "xml";
  var $element_name = "issue";
  var $element_name_plural = "issues";

  function setSite($data) {
    $this->site = $data;
    return true;
  }

}

class RedmineProject extends ActiveResource {

  var $site = "";
  var $request_format = "xml";
  var $element_name = "project";
  var $element_name_plural = "projects";

  function setSite($data) {
    $this->site = $data;
    return true;
  }

}

class RedmineTimeEntry extends ActiveResource {

  var $site = "";
  var $request_format = "xml";
  var $element_name = "time_entry";
  var $element_name_plural = "time_entries";

  function setSite($data) {
    $this->site = $data;
    return true;
  }

}

class RedmineNews extends ActiveResource {

  var $site = "";
  var $request_format = "xml";
  var $element_name = "new";
  var $element_name_plural = "news";

  function setSite($data) {
    $this->site = $data;
    return true;
  }

}

if (!function_exists('implode_assoc')) {


  /**
   * Joins key:value pairs by inner_glue and each pair together by outer_glue
   * @param string $inner_glue The HTTP method (GET, POST, PUT, DELETE)
   * @param string $outer_glue Full URL of the resource to access
   * @param array $array Associative array of query parameters
   * @return string Urlencoded string of query parameters
   */
  function implode_assoc($inner_glue, $outer_glue, $array) {
    $output = array();
    foreach ($array as $key => $item) {
      $output[] = $key . $inner_glue . urlencode($item);
    }
    return implode($outer_glue, $output);
  }

}


if (!function_exists('explode_assoc')) {

  /**
   * Split key:value pairs by inner_glue and each pair by outer_glue from a string
   * @param string $inner_glue The HTTP method (GET, POST, PUT, DELETE)
   * @param string $outer_glue Full URL of the resource to access
   * @param string $string String of query parameters
   * @return array Parameters array
   */
  function explode_assoc($inner_glue, $outer_glue, $string) {
    $data = array();
    foreach (explode($outer_glue, $string) as $param) {
      $d = explode($inner_glue, $param);
      $data[$d[0]] = $d[1];
    }
    return $data;
  }

}
/**
 * Does something really useful with the passed things
 *
 * @param array $things
 * @return object
 */
//function modredmine_do_something_useful(array $things) {
//    return new stdClass();
//}
