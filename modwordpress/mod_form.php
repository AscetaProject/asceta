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
 * The main modwordpress configuration form
 *
 * It uses the standard core Moodle formslib. For more info about them, please
 * visit: http://docs.moodle.org/en/Development:lib/formslib.php
 *
 * @package   mod_modwordpress
 * @copyright 2010 Your Name
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/moodleform_mod.php');

class mod_modwordpress_mod_form extends moodleform_mod {

    function definition() {

        global $COURSE, $DB;
        $mform =& $this->_form;

//-------------------------------------------------------------------------------
    /// Adding the "general" fieldset, where all the common settings are showed
        $mform->addElement('header', 'general', get_string('general', 'form'));

    /// Adding the standard "name" field
        $mform->addElement('text', 'name', get_string('modwordpressname', 'modwordpress'), array('size'=>'64'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEAN);
        }
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addHelpButton('name', 'modwordpressname', 'modwordpress');

    /// Adding the standard "intro" and "introformat" fields
        $this->add_intro_editor();

//-------------------------------------------------------------------------------
    /// Adding the rest of modwordpress settings, spreeading all them into this fieldset
    /// or adding more fieldsets ('header' elements) if needed for better logic
        $mform->addElement('static', 'label1', 'modwordpresssetting1', 'Your modwordpress fields go here. Replace me!');

        $mform->addElement('header', 'modwordpressfieldset', get_string("new_server","modwordpress"));
        //$mform->addElement('static', 'label2', 'modwordpresssetting2', 'Your modwordpress fields go here. Replace me!');
        $options = array();
        $options[0] = get_string('none');
        if ($servers = $DB->get_records('modwordpress_servers', array())) {
            foreach ($servers as $server) {
                $options[$server->id] = format_string($server->name);
            }
        }
        $mform->addElement('select', 'server_id', get_string('modmediawikiservers', 'modmediawiki'), $options);


//-------------------------------------------------------------------------------
        // add standard elements, common to all modules
        $this->standard_coursemodule_elements();
//-------------------------------------------------------------------------------
        // add standard buttons, common to all modules
        $this->add_action_buttons();

    }
}
