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
 * Moodle renderer used to display special elements of the modmendeley module
 *
* This file was adapted from the mod/lesson/renderer.php
*
 * @package   mod_modmendeley
 * @copyright 2011 María del Mar Jiménez Torres (mjimenez@fidesol.org) - Fundación I+D del Software Libre (www.fidesol.org)
 \* @license   http://www.apache.org/licenses/LICENSE-2.0.html Apache License, Version 2.0
 **/

defined('MOODLE_INTERNAL') || die();

class mod_modmendeley_renderer extends plugin_renderer_base {
    /**
     * Returns the header for the modmendeley module
     *
     * @param modmendeley $modmendeley
     * @param string $currenttab
     * @param bool $extraeditbuttons
     * @return string
     */
    public function header($modmendeley, $cm, $currenttab = '', $extraeditbuttons = false) {
        global $CFG;

        $activityname = format_string($modmendeley->name, true, $modmendeley->course);
        $title = $this->page->course->shortname.": ".$activityname;

        // Build the buttons
        $context = get_context_instance(CONTEXT_MODULE, $cm->id);

    /// Header setup
        $this->page->set_title($title);
        $this->page->set_heading($this->page->course->fullname);
        $output = $this->output->header();

        if (has_capability('mod/modmendeley:manage', $context)) {
            $output .= $this->output->heading_with_help($activityname, 'overview', 'modmendeley');
            if (!empty($currenttab)) {
                ob_start();
                include($CFG->dirroot.'/mod/modmendeley/tabs.php');
                $output .= ob_get_contents();
                ob_end_clean();
            }
        } else {
            $output .= $this->output->heading($activityname);
            if (!empty($currenttab)) {
                ob_start();
                include($CFG->dirroot.'/mod/modmendeley/tabs.php');
                $output .= ob_get_contents();
                ob_end_clean();
            }
        }

        return $output;
    }

}
