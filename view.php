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
 *
 * @package    mod_ovmsurvey
 * @copyright  2019 Pierre Duverneix - Fondation UNIT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->dirroot . '/mod/ovmsurvey/lib.php');
require_once($CFG->dirroot . '/mod/ovmsurvey/locallib.php');

global $USER;

$id       = required_param('id', PARAM_INT);        // Course module ID.
$u        = optional_param('u', 0, PARAM_INT);         // URL instance id.
$redirect = optional_param('redirect', 0, PARAM_BOOL);

$lang = get_lang();

if ($u) {  // Two ways to specify the module.
    $survey = $DB->get_record('ovmsurvey', array('id' => $u), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('ovmsurvey', $survey->id, $survey->course, false, MUST_EXIST);

} else {
    $cm = get_coursemodule_from_id('ovmsurvey', $id, 0, false, MUST_EXIST);
    $survey = $DB->get_record('ovmsurvey', array('id' => $cm->instance), '*', MUST_EXIST);
}

$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);

require_course_login($course, true, $cm);
$context = context_module::instance($cm->id);
require_capability('mod/ovmsurvey:view', $context);

// Print the page header.
$url = new moodle_url('/mod/ovmsurvey/view.php', array('id' => $cm->id));

$PAGE->set_url($url);
$PAGE->set_context($context);
$PAGE->set_title($survey->name);
$PAGE->set_heading($course->fullname);

echo $OUTPUT->header();
echo $OUTPUT->box_start('generalbox mod_ovmsurvey-box boxaligncenter');

// Get the mod config.
$config = get_config('mod_visio');

$header = new \mod_ovmsurvey\output\header($survey->id, $survey->name);
$output = $PAGE->get_renderer('mod_ovmsurvey');
echo $output->render($header);

$surveyout = new \mod_ovmsurvey\output\main($lang, $cm->id, $survey->id, $survey->skill);
$output = $PAGE->get_renderer('mod_ovmsurvey');
echo $output->render($surveyout);

echo $OUTPUT->box_end();
echo $OUTPUT->footer();