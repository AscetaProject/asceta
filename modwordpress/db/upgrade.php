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
 * This file keeps track of upgrades to the modredmine module
 *
 * Sometimes, changes between versions involve alterations to database
 * structures and other major things that may break installations. The upgrade
 * function in this file will attempt to perform all the necessary actions to
 * upgrade your older installation to the current version. If there's something
 * it cannot do itself, it will tell you what you need to do.  The commands in
 * here will all be database-neutral, using the functions defined in DLL libraries.
 *
 * @package   mod_modredmine
 * @copyright 2011 Vicente Manuel García Huete (vmgarcia@fidesol.org) - Fundación I+D del Software Libre (www.fidesol.org)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

/**
 * xmldb_modredmine_upgrade
 *
 * @param int $oldversion
 * @return bool
 */
function xmldb_modredmine_upgrade($oldversion) {

    global $DB;

    $dbman = $DB->get_manager(); // loads ddl manager and xmldb classes
/// And upgrade begins here. For each one, you'll need one
/// block of code similar to the next one. Please, delete
/// this comment lines once this file start handling proper
/// upgrade code.
/// if ($oldversion < YYYYMMDD00) { //New version in version.php
///
/// }
/// Lines below (this included)  MUST BE DELETED once you get the first version
/// of your module ready to be installed. They are here only
/// for demonstrative purposes and to show how the modredmine
/// iself has been upgraded.
/// For each upgrade block, the file modredmine/version.php
/// needs to be updated . Such change allows Moodle to know
/// that this file has to be processed.
/// To know more about how to write correct DB upgrade scripts it's
/// highly recommended to read information available at:
///   http://docs.moodle.org/en/Development:XMLDB_Documentation
/// and to play with the XMLDB Editor (in the admin menu) and its
/// PHP generation posibilities.

    if ($oldversion < 2011100504) {

        // Define field key to be added to modredmine_servers
        $table = new xmldb_table('modredmine_servers');
        $field = new xmldb_field('key', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'auth');

        // Conditionally launch add field key
        if (!$dbman->field_exists($table, $field)) {
	$dbman->add_field($table, $field);
        }

        // modredmine savepoint reached
        upgrade_mod_savepoint(true, 2011100504, 'modredmine');
    }

    if ($oldversion < 2011100505) {

        // Define field admin to be dropped from modredmine_servers
        $table = new xmldb_table('modredmine_servers');
        $field = new xmldb_field('admin');

        // Conditionally launch drop field admin
        if ($dbman->field_exists($table, $field)) {
	$dbman->drop_field($table, $field);
        }

        $table = new xmldb_table('modredmine_servers');
        $field = new xmldb_field('password');

        // Conditionally launch drop field password
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // modredmine savepoint reached
        upgrade_mod_savepoint(true, 2011100505, 'modredmine');
    }

    if ($oldversion < 2011100507) {

        // Define field admin to be dropped from modredmine_servers
        $table = new xmldb_table('modredmine_servers');
        $field = new xmldb_field('key');

        // Conditionally launch drop field admin
        if ($dbman->field_exists($table, $field)) {
	$dbman->drop_field($table, $field);
        }

        // Define field key to be added to modredmine_servers
        $table = new xmldb_table('modredmine_servers');
        $field = new xmldb_field('api_key', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'auth');

        // Conditionally launch add field key
        if (!$dbman->field_exists($table, $field)) {
	$dbman->add_field($table, $field);
        }



        // modredmine savepoint reached
        upgrade_mod_savepoint(true, 2011100507, 'modredmine');
    }

    if ($oldversion < 2011100508) {

        // Define field admin to be dropped from modredmine_servers
        $table = new xmldb_table('modredmine');
        $field = new xmldb_field('api');

        // Conditionally launch drop field admin
        if ($dbman->field_exists($table, $field)) {
	$dbman->drop_field($table, $field);
        }

        // Define field admin to be dropped from modredmine_servers
        $table = new xmldb_table('modredmine');
        $field = new xmldb_field('permission_create_issue');

        // Conditionally launch drop field admin
        if ($dbman->field_exists($table, $field)) {
	$dbman->drop_field($table, $field);
        }

        // Define field admin to be dropped from modredmine_servers
        $table = new xmldb_table('modredmine');
        $field = new xmldb_field('permission_edit_issue');

        // Conditionally launch drop field admin
        if ($dbman->field_exists($table, $field)) {
	$dbman->drop_field($table, $field);
        }

        // Define field admin to be dropped from modredmine_servers
        $table = new xmldb_table('modredmine');
        $field = new xmldb_field('permission_delete_issue');

        // Conditionally launch drop field admin
        if ($dbman->field_exists($table, $field)) {
	$dbman->drop_field($table, $field);
        }

        // Define field admin to be dropped from modredmine_servers
        $table = new xmldb_table('modredmine');
        $field = new xmldb_field('permission_create_user');

        // Conditionally launch drop field admin
        if ($dbman->field_exists($table, $field)) {
	$dbman->drop_field($table, $field);
        }

        // Define field admin to be dropped from modredmine_servers
        $table = new xmldb_table('modredmine');
        $field = new xmldb_field('permission_edit_user');

        // Conditionally launch drop field admin
        if ($dbman->field_exists($table, $field)) {
	$dbman->drop_field($table, $field);
        }

        // Define field admin to be dropped from modredmine_servers
        $table = new xmldb_table('modredmine');
        $field = new xmldb_field('permission_delete_user');

        // Conditionally launch drop field admin
        if ($dbman->field_exists($table, $field)) {
	$dbman->drop_field($table, $field);
        }

        // Define field admin to be dropped from modredmine_servers
        $table = new xmldb_table('modredmine');
        $field = new xmldb_field('permission_create_news');

        // Conditionally launch drop field admin
        if ($dbman->field_exists($table, $field)) {
	$dbman->drop_field($table, $field);
        }

        // Define field admin to be dropped from modredmine_servers
        $table = new xmldb_table('modredmine');
        $field = new xmldb_field('permission_edit_news');

        // Conditionally launch drop field admin
        if ($dbman->field_exists($table, $field)) {
	$dbman->drop_field($table, $field);
        }

        // Define field admin to be dropped from modredmine_servers
        $table = new xmldb_table('modredmine');
        $field = new xmldb_field('permission_delete_news');

        // Conditionally launch drop field admin
        if ($dbman->field_exists($table, $field)) {
	$dbman->drop_field($table, $field);
        }

        // Define field admin to be dropped from modredmine_servers
        $table = new xmldb_table('modredmine');
        $field = new xmldb_field('permission_create_time_entries');

        // Conditionally launch drop field admin
        if ($dbman->field_exists($table, $field)) {
	$dbman->drop_field($table, $field);
        }

        // Define field admin to be dropped from modredmine_servers
        $table = new xmldb_table('modredmine');
        $field = new xmldb_field('permission_edit_time_entries');

        // Conditionally launch drop field admin
        if ($dbman->field_exists($table, $field)) {
	$dbman->drop_field($table, $field);
        }

        // Define field admin to be dropped from modredmine_servers
        $table = new xmldb_table('modredmine');
        $field = new xmldb_field('permission_delete_time_entries');

        // Conditionally launch drop field admin
        if ($dbman->field_exists($table, $field)) {
	$dbman->drop_field($table, $field);
        }

        // Define field admin to be dropped from modredmine_servers
        $table = new xmldb_table('modredmine');
        $field = new xmldb_field('permission_create_project');

        // Conditionally launch drop field admin
        if ($dbman->field_exists($table, $field)) {
	$dbman->drop_field($table, $field);
        }

        // Define field admin to be dropped from modredmine_servers
        $table = new xmldb_table('modredmine');
        $field = new xmldb_field('permission_edit_project');

        // Conditionally launch drop field admin
        if ($dbman->field_exists($table, $field)) {
	$dbman->drop_field($table, $field);
        }

        // Define field admin to be dropped from modredmine_servers
        $table = new xmldb_table('modredmine');
        $field = new xmldb_field('permission_delete_project');

        // Conditionally launch drop field admin
        if ($dbman->field_exists($table, $field)) {
	$dbman->drop_field($table, $field);
        }

        // Define field key to be added to modredmine_servers
        $table = new xmldb_table('modredmine_servers');
        $field = new xmldb_field('project_id', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'auth');

        // Conditionally launch add field key
        if (!$dbman->field_exists($table, $field)) {
	$dbman->add_field($table, $field);
        }

  // Define field project_id to be added to modredmine
        $table = new xmldb_table('modredmine');
        $field = new xmldb_field('project_id', XMLDB_TYPE_INTEGER, '20', null, null, null, null, 'server_id');

        // Conditionally launch add field project_id
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field project_name to be added to modredmine
        $table = new xmldb_table('modredmine');
        $field = new xmldb_field('project_name', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'project_id');

        // Conditionally launch add field project_name
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

      // Define field project_identifier to be added to modredmine
        $table = new xmldb_table('modredmine');
        $field = new xmldb_field('project_identifier', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'project_name');

        // Conditionally launch add field project_identifier
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

                // Define field project_description to be added to modredmine
        $table = new xmldb_table('modredmine');
        $field = new xmldb_field('project_description', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'project_identifier');

        // Conditionally launch add field project_description
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }



        // modredmine savepoint reached
        upgrade_mod_savepoint(true, 2011100508, 'modredmine');
    }





    return true;
}
