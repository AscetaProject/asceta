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
 * @copyright 2010 Your Name
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/// (Replace modmediawiki with the name of your module and remove this line)

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');

$id = optional_param('id', 0, PARAM_INT); // course_module ID, or
$n  = optional_param('n', 0, PARAM_INT);  // modmediawiki instance ID - it should be named as the first character of the module

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

require_login($course, true, $cm);

add_to_log($course->id, 'modmediawiki', 'view', "view.php?id=$cm->id", $modmediawiki->name, $cm->id);

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

// Replace the following lines with you own code
echo $OUTPUT->heading('Yay! It works!');

// Finish the page
echo $OUTPUT->footer();
