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
 * This file replaces the legacy STATEMENTS section in db/install.xml,
 * lib.php/modulename_install() post installation hook and partially defaults.php
 *
 * @package   mod_modmediawiki
 * @copyright 2010 Your Name <your@email.adress>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Post installation procedure
 */
function xmldb_modmediawiki_install() {
    global $DB;

    $record = new stdClass();
    $record->name         = 'servidor 1';
    $record->url          = 'http://192.168.4.49/mediawiki/index.php/Especial:Asceta/';
    $DB->insert_record('modmediawiki_server', $record);

    $record = new stdClass();
    $record->name         = 'servidor 2';
    $record->url          = 'http://192.168.2.16/mediawiki/index.php/Especial:Asceta/';
    $DB->insert_record('modmediawiki_server', $record);
    
}
