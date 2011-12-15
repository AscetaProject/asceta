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
 * The main modredmine configuration form
 *
 * It uses the standard core Moodle formslib. For more info about them, please
 * visit: http://docs.moodle.org/en/Development:lib/formslib.php
 *
 * @package   mod_modredmine
 * @copyright 2011 Vicente Manuel García Huete (vmgarcia@fidesol.org) - Fundación I+D del Software Libre (www.fidesol.org)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/moodleform_mod.php');

class mod_modredmine_mod_form extends moodleform_mod {

    function definition() {

        global $COURSE, $DB;
        $mform = & $this->_form;
        if ($servers = $DB->get_records('modredmine_servers', array())) {

//-------------------------------------------------------------------------------
	/// Adding the "general" fieldset, where all the common settings are showed
	$mform->addElement('header', 'general', get_string('general', 'form'));

	/// Adding the standard "name" field
	$mform->addElement('text', 'name', get_string('modredminename', 'modredmine'), array('size' => '64'));
	if (!empty($CFG->formatstringstriptags)) {
	    $mform->setType('name', PARAM_TEXT);
	} else {
	    $mform->setType('name', PARAM_CLEAN);
	}
	$mform->addRule('name', null, 'required', null, 'client');
	$mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
	$mform->addHelpButton('name', 'modredminename', 'modredmine');

	/// Adding the standard "intro" and "introformat" fields
	$this->add_intro_editor();

//-------------------------------------------------------------------------------
	/// Adding the rest of modredmine settings, spreeading all them into this fieldset
	/// or adding more fieldsets ('header' elements) if needed for better logic
//	$mform->addElement('static', 'label1', 'modredminesetting1', 'Your modredmine fields go here. Replace me!');

	$mform->addElement('header', 'modredminefieldset', get_string("redmine_server", "modredmine"));
	//$mform->addElement('static', 'label2', 'modredminesetting2', 'Your modredmine fields go here. Replace me!');
	$options = array();
	foreach ($servers as $server) {
	    $options[$server->id] = format_string($server->name);
	}
	$mform->addElement('select', 'server_id', get_string('available_servers', 'modredmine'), $options);
	$mform->addRule('server_id', null, 'required', null, 'client');

	$mform->addElement('header', 'modredminefieldset', get_string('project_details', 'modredmine'));

	$mform->addElement('text', 'project_name', get_string('project_name','modredmine'));
	$mform->addElement('text', 'project_identifier', get_string('project_identifier','modredmine'));
	$mform->addHelpButton('project_identifier', 'project_identifier_advert', 'modredmine');
	$mform->addElement('text', 'project_description', get_string('project_description','modredmine'));

	$mform->addRule('project_name', null, 'required', null, 'client');
	$mform->addRule('project_identifier', null, 'required', null, 'client');
	$mform->addRule('project_identifier', 'lowercase letters, numbers and underscores only with a length between 1 and 100', 'regex', '/^[a-z,0-9,_]{1,100}$/');



//-------------------------------------------------------------------------------
	// add standard elements, common to all modules
	$this->standard_coursemodule_elements();
//-------------------------------------------------------------------------------
	// add standard buttons, common to all modules
	$this->add_action_buttons();
        } else {
	$mform->addElement('static', 'update', '', get_string("no_server", "modredmine"));
        }
    }

}
