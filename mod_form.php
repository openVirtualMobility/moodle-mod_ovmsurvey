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
 * This file is the entry point to the assign module. All pages are rendered from here
 *
 * @package   mod_ovmsurvey
 * @author    Pierre Duverneix
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/moodleform_mod.php');

class mod_ovmsurvey_mod_form extends moodleform_mod {

    protected function definition() {
        global $CFG, $COURSE;

        $mform    =& $this->_form;

        $mform->addElement('header', 'general', get_string('general', 'form'));

        $mform->addElement('text', 'name', get_string('name', 'ovmsurvey'), array('size' => '64'));
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');

        $this->standard_intro_elements(get_string('description'));

        if (isset($_GET['lang'])) {
            $lang = $_GET['lang'];
        } else {
            $lang = $CFG->lang;
        }

        $string = file_get_contents(dirname(__FILE__) . '/json/'.$lang.'/students.json');
        $json = json_decode($string, true);
        $arr = $json[$lang][0];
        $skills = [];
        foreach($arr as $key => $val) {
            array_push($skills, $val['name']);
        }

        $mform->addElement('select', 'skill', get_string('skill', 'ovmsurvey'), $skills, null);
        $mform->setType('skill', PARAM_TEXT);
        $mform->addRule('skill', null, 'required', null, 'client');

        $this->standard_coursemodule_elements();

        $this->add_action_buttons();
    }

    public function data_preprocessing(&$defaultvalues) {
        return;
    }

    public function validation($data, $files) {
        $errors = parent::validation($data, $files);
        return $errors;
    }

}