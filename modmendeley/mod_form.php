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
 * The main modmendeley configuration form
 *
 * It uses the standard core Moodle formslib. For more info about them, please
 * visit: http://docs.moodle.org/en/Development:lib/formslib.php
 *
 * @package   mod_modmendeley
 * @copyright 2011 María del Mar Jiménez Torres (mjimenez@fidesol.org) - Fundación I+D del Software Libre (www.fidesol.org)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/moodleform_mod.php');
require_once('locallib.php');

class mod_modmendeley_mod_form extends moodleform_mod {

    function definition() {

        global $COURSE, $DB, $PAGE;
        $mform =& $this->_form;

        $PAGE->requires->js('/mod/modmendeley/form.js');

        if ($users = $DB->get_records('modmendeley_users', array())) {
//-------------------------------------------------------------------------------
    /// Adding the "general" fieldset, where all the common settings are showed
        $mform->addElement('header', 'general', get_string('general', 'form'));

    /// Adding the standard "name" field
        $mform->addElement('text', 'name', get_string('modmendeleyname', 'modmendeley'), array('size'=>'64'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEAN);
        }
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addHelpButton('name', 'modmendeleyname', 'modmendeley');

    /// Adding the standard "intro" and "introformat" fields
        $this->add_intro_editor();

//-------------------------------------------------------------------------------
    /// Adding the rest of modmendeley settings, spreeading all them into this fieldset
    /// or adding more fieldsets ('header' elements) if needed for better logic

        $mform->addElement('header', 'modmendeleyfieldset', get_string('new_user', 'modmendeley'));
        $options = array();
        $options[0] = get_string('none');
        foreach ($users as $user) {
        if ($user->oauth) {
            if ($user->consumer_key != '' && $user->consumer_secret != '' && $user->access_token != '' && $user->access_secret != '') {
                $options[$user->id] = format_string($user->name);
            }
        } else {
            $options[$user->id] = format_string($user->name);
        }
        }

        $mform->addElement('select', 'user_id', get_string('available_users', 'modmendeley'), $options);
        $mform->addElement('select', 'private', get_string('access', 'modmendeley'), array(get_string('access_public','modmendeley'),get_string('access_private','modmendeley')));

        $mform->disabledIf('private', 'user_id', 'eq', 0);
        $user = $mform->getElement('user_id');
        $value = getLibraryValue($user, 'folders');

        $mform->addElement('header', 'modmendeleyfieldset', get_string('permission', 'modmendeley'));
        $mform->addElement('checkbox', 'permission_create_document', get_string('new_document','modmendeley'));
        $mform->addElement('checkbox', 'permission_upload_document', get_string('upload_document','modmendeley'));
        $mform->addElement('checkbox', 'permission_delete_document', get_string('delete_document','modmendeley'));
        $mform->addElement('checkbox', 'permission_new_folder', get_string('new_folder','modmendeley'));
        $mform->addElement('checkbox', 'permission_delete_folder', get_string('delete_folder','modmendeley'));
        $mform->addElement('checkbox', 'permission_new_group', get_string('new_group','modmendeley'));
        $mform->addElement('checkbox', 'permission_delete_group', get_string('delete_group','modmendeley'));


//-------------------------------------------------------------------------------
        // add standard elements, common to all modules
        $this->standard_coursemodule_elements();
//-------------------------------------------------------------------------------
        // add standard buttons, common to all modules
        $this->add_action_buttons();
        } else {
	$mform->addElement('static', 'update', '', get_string("no_user", "modmendeley"));
        }

    }
}
