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
* Sets up the tabs used by the Mendeley options.
*
* This file was adapted from the mod/lesson/tabs.php
*
 * @package   mod_modmendeley
 * @copyright 2011 María del Mar Jiménez Torres (mjimenez@fidesol.org) - Fundación I+D del Software Libre (www.fidesol.org)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

defined('MOODLE_INTERNAL') || die();

/// This file to be included so we can assume config.php has already been included.
global $DB;
if (empty($modmendeley)) {
    print_error('cannotcallscript');
}
if (!isset($currenttab)) {
    $currenttab = '';
}
if (!isset($cm)) {
    $cm = get_coursemodule_from_instance('modmendeley', $modmendeley->id);
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
}
if (!isset($course)) {
    $course = $DB->get_record('course', array('id' => $modmendeley->course));
}

$tabs = $row = $inactive = $activated = array();


if($modmendeley->private){
    $row[] = new tabobject('library', "view.php?id=$cm->id&amp;option=library&amp;action=documents&amp;element_selected=folder-profile-all&amp;sesskey=" . sesskey(), get_string('librarytb', modmendeley, format_string($modmendeley->name)));
}
$row[] = new tabobject('paper', "view.php?id=$cm->id&amp;option=paper&amp;sesskey=" . sesskey(), get_string('papertb', modmendeley, format_string($modmendeley->name)));
$row[] = new tabobject('group', "view.php?id=$cm->id&amp;option=group&amp;sesskey=" . sesskey(), get_string('grouptb', modmendeley, format_string($modmendeley->name)));
if($modmendeley->private){
    $row[] = new tabobject('people', "view.php?id=$cm->id&amp;option=people&amp;sesskey=" . sesskey(), get_string('peopletb', modmendeley, format_string($modmendeley->name)));
}
$tabs[] = $row;


// To add sub tabs
switch ($currenttab) {
    case 'library':
    case 'paper':
    case 'group':
    case 'people';
    break;
}

print_tabs($tabs, $currenttab, $inactive, $activated);
