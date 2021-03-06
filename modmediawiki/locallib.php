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
 * Internal library of functions for module modmediawiki
 *
 * All the modmediawiki specific functions, needed to implement the module
 * logic, should go here. Never include this file from your lib.php!
 *
 * @package   mod_modmediawiki
 * @copyright 2011 María del Mar Jiménez Torres (mjimenez@fidesol.org) - Fundación I+D del Software Libre (www.fidesol.org)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();



/**
 * Joins key:value pairs by inner_glue and each pair together by outer_glue
 * @param string $inner_glue The HTTP method (GET, POST, PUT, DELETE)
 * @param string $outer_glue Full URL of the resource to access
 * @param array $array Associative array of query parameters
 * @return string Urlencoded string of query parameters
 */
function implode_assoc($inner_glue, $outer_glue, $array) {
  $output = array();
  foreach($array as $key => $item) {
    $output[] = $key . $inner_glue . urlencode($item);
  }
  return implode($outer_glue, $output);
}

/**
 * Split key:value pairs by inner_glue and each pair by outer_glue from a string
 * @param string $inner_glue The HTTP method (GET, POST, PUT, DELETE)
 * @param string $outer_glue Full URL of the resource to access
 * @param string $string String of query parameters
 * @return array Parameters array
 */
function explode_assoc($inner_glue, $outer_glue, $string) {
    $data = array();
    foreach (explode($outer_glue,$string) as $param) {
        $d = explode($inner_glue,$param);
        $data[trim($d[0])] = $d[1];
    }
    return $data;
}

/**
 * Does something really useful with the passed things
 *
 * @param array $things
 * @return object
 */
//function modmediawiki_do_something_useful(array $things) {
//    return new stdClass();
//}
