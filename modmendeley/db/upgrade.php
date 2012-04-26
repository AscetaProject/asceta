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
 * This file keeps track of upgrades to the modmendeley module
 *
 * Sometimes, changes between versions involve alterations to database
 * structures and other major things that may break installations. The upgrade
 * function in this file will attempt to perform all the necessary actions to
 * upgrade your older installation to the current version. If there's something
 * it cannot do itself, it will tell you what you need to do.  The commands in
 * here will all be database-neutral, using the functions defined in DLL libraries.
 *
 * @package   mod_modmendeley
 * @copyright 2011 María del Mar Jiménez Torres (mjimenez@fidesol.org) - Fundación I+D del Software Libre (www.fidesol.org)
 \* @license   http://www.apache.org/licenses/LICENSE-2.0.html Apache License, Version 2.0
 */

defined('MOODLE_INTERNAL') || die();

/**
 * xmldb_modmendeley_upgrade
 *
 * @param int $oldversion
 * @return bool
 */
function xmldb_modmendeley_upgrade($oldversion) {

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
/// for demonstrative purposes and to show how the modmendeley
/// iself has been upgraded.

/// For each upgrade block, the file modmendeley/version.php
/// needs to be updated . Such change allows Moodle to know
/// that this file has to be processed.

/// To know more about how to write correct DB upgrade scripts it's
/// highly recommended to read information available at:
///   http://docs.moodle.org/en/Development:XMLDB_Documentation
/// and to play with the XMLDB Editor (in the admin menu) and its
/// PHP generation posibilities.

/// First example, some fields were added to install.xml on 2007/04/01
    if ($oldversion < 2007040100) {

    /// Define field course to be added to modmendeley
        $table = new xmldb_table('modmendeley');
        $field = new xmldb_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'id');

    /// Add field course
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

    /// Define field intro to be added to modmendeley
        $table = new xmldb_table('modmendeley');
        $field = new xmldb_field('intro', XMLDB_TYPE_TEXT, 'medium', null, null, null, null,'name');

    /// Add field intro
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

    /// Define field introformat to be added to modmendeley
        $table = new xmldb_table('modmendeley');
        $field = new xmldb_field('introformat', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0',
            'intro');

    /// Add field introformat
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
    }

/// Second example, some hours later, the same day 2007/04/01
/// two more fields and one index were added to install.xml (note the micro increment
/// "01" in the last two digits of the version
    if ($oldversion < 2007040101) {

    /// Define field timecreated to be added to modmendeley
        $table = new xmldb_table('modmendeley');
        $field = new xmldb_field('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0',
            'introformat');

    /// Add field timecreated
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

    /// Define field timemodified to be added to modmendeley
        $table = new xmldb_table('modmendeley');
        $field = new xmldb_field('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0',
            'timecreated');

    /// Add field timemodified
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

    /// Define index course (not unique) to be added to modmendeley
        $table = new xmldb_table('modmendeley');
        $index = new xmldb_index('courseindex', XMLDB_INDEX_NOTUNIQUE, array('course'));

    /// Add index to course field
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

    }

    if ($oldversion < 2011072101) {

        // Define table modmendeley to be created
        $table = new xmldb_table('modmendeley');

        // Adding fields to table modmendeley
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('intro', XMLDB_TYPE_TEXT, 'medium', null, null, null, null);
        $table->add_field('introformat', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');

        // Adding keys to table modmendeley
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Adding indexes to table modmendeley
        $table->add_index('course', XMLDB_INDEX_NOTUNIQUE, array('course'));

        // Conditionally launch create table for modmendeley
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // modmendeley savepoint reached
        upgrade_mod_savepoint(true, 2011072101, 'modmendeley');
    }



    if ($oldversion < 2011072104) {

        // Define field user_id to be added to modmendeley
        $table = new xmldb_table('modmendeley');
        $field = new xmldb_field('user_id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, 'timemodified');

        // Conditionally launch add field user_id
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define key url (foreign) to be added to modmendeley
        $table = new xmldb_table('modmendeley');
        $key = new xmldb_key('url', XMLDB_KEY_FOREIGN, array('user_id'), 'modmendeley_users', array('id'));

        // Launch add key url
        $dbman->add_key($table, $key);

        // Define table modmendeley_users to be created
        $table = new xmldb_table('modmendeley_users');

        // Adding fields to table modmendeley_users
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('url', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('consumer_key', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('consumer_secret', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('request_token', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('request_secret', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('access_token', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('access_secret', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('oauth', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '1');

        // Adding keys to table modmendeley_users
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Adding indexes to table modmendeley_users
        $table->add_index('url', XMLDB_INDEX_NOTUNIQUE, array('url'));

        // Conditionally launch create table for modmendeley_users
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // modmendeley savepoint reached
        upgrade_mod_savepoint(true, 2011072104, 'modmendeley');
    }

        if ($oldversion < 2011072114) {

        // Define field verifier to be added to modmendeley_users
        $table = new xmldb_table('modmendeley_users');
        $field = new xmldb_field('verifier', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'request_secret');

        // Conditionally launch add field verifier
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // modmendeley savepoint reached
        upgrade_mod_savepoint(true, 2011072114, 'modmendeley');
    }

if ($oldversion < 2011072502) {

        // Define field permission_create_document to be added to modmendeley
        $table = new xmldb_table('modmendeley');
        $field = new xmldb_field('permission_create_document', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, 'user_id');

        // Conditionally launch add field permission_create_document
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field permission_upload_document to be added to modmendeley
        $table = new xmldb_table('modmendeley');
        $field = new xmldb_field('permission_upload_document', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, 'permission_create_document');

        // Conditionally launch add field permission_upload_document
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field permission_delete_document to be added to modmendeley
        $table = new xmldb_table('modmendeley');
        $field = new xmldb_field('permission_delete_document', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, 'permission_upload_document');

        // Conditionally launch add field permission_delete_document
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field permission_new_folder to be added to modmendeley
        $table = new xmldb_table('modmendeley');
        $field = new xmldb_field('permission_new_folder', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, 'permission_delete_document');

        // Conditionally launch add field permission_new_folder
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field permission_delete_folder to be added to modmendeley
        $table = new xmldb_table('modmendeley');
        $field = new xmldb_field('permission_delete_folder', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, 'permission_new_folder');

        // Conditionally launch add field permission_delete_folder
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field permission_new_group to be added to modmendeley
        $table = new xmldb_table('modmendeley');
        $field = new xmldb_field('permission_new_group', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, 'permission_delete_folder');

        // Conditionally launch add field permission_new_group
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field permission_delete_group to be added to modmendeley
        $table = new xmldb_table('modmendeley');
        $field = new xmldb_field('permission_delete_group', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, 'permission_new_group');

        // Conditionally launch add field permission_delete_group
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // modmendeley savepoint reached
        upgrade_mod_savepoint(true, 2011072502, 'modmendeley');
    }

    if ($oldversion < 2011072505) {

        // Define field private to be added to modmendeley
        $table = new xmldb_table('modmendeley');
        $field = new xmldb_field('private', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '1', 'user_id');

        // Conditionally launch add field private
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // modmendeley savepoint reached
        upgrade_mod_savepoint(true, 2011072505, 'modmendeley');
    }

    if ($oldversion < 2011091902) {

        // Define field permission_add_doc_folder to be added to modmendeley
        $table = new xmldb_table('modmendeley');
        $field = new xmldb_field('permission_add_doc_folder', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, 'permission_create_document');

        // Conditionally launch add field permission_add_doc_folder
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field permission_delete_doc_folder to be added to modmendeley
        $table = new xmldb_table('modmendeley');
        $field = new xmldb_field('permission_delete_doc_folder', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, 'permission_add_doc_folder');

        // Conditionally launch add field permission_delete_doc_folder
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // modmendeley savepoint reached
        upgrade_mod_savepoint(true, 2011091902, 'modmendeley');
    }

    if ($oldversion < 2011091903) {

        // Define field permission_create_document to be dropped from modmendeley
        $table = new xmldb_table('modmendeley');
        $field = new xmldb_field('permission_upload_document');

        // Conditionally launch drop field permission_create_document
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }
        // modmendeley savepoint reached
        upgrade_mod_savepoint(true, 2011091903, 'modmendeley');
    }

    if ($oldversion < 2011101002) {

        // Define field principal_tab to be added to modmendeley
        $table = new xmldb_table('modmendeley');
        $field = new xmldb_field('principal_tab', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'permission_delete_group');

        // Conditionally launch add field principal_tab
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // modmendeley savepoint reached
        upgrade_mod_savepoint(true, 2011101002, 'modmendeley');
    }

    if ($oldversion < 2011101004) {

        // Changing type of field principal_tab on table modmendeley to int
        $table = new xmldb_table('modmendeley');
        $field = new xmldb_field('principal_tab', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, 'permission_delete_group');

        // Launch change of type for field principal_tab
        $dbman->change_field_type($table, $field);

        // modmendeley savepoint reached
        upgrade_mod_savepoint(true, 2011101004, 'modmendeley');
    }



    return true;
}
