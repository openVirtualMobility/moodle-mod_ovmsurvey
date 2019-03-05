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
 * This file is the entry point to the module
 *
 * @package   mod_ovmsurvey
 * @author    Pierre Duverneix
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once(dirname(__FILE__) . '/locallib.php');

require_login();

$id = required_param('id', PARAM_INT); // Course Module ID
$cm = get_coursemodule_from_id('ovmsurvey', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$context = context_module::instance($cm->id);

$PAGE->set_cm($cm);

$PAGE->set_url('/mod/ovmsurvey/view.php', array('id' => $cm->id));
$PAGE->set_title($course->shortname.': Survey');
$PAGE->set_heading($course->fullname);

$review = optional_param('review', 0, PARAM_INT);

require_login();

echo $OUTPUT->header();

if (isset($_GET['lang'])) {
    $lang = $_GET['lang'];
} else {
    $lang = $CFG->lang;
    if (isset($USER->lang)) {
        $lang = $USER->lang;
    }
}
if (!file_exists('json/questions_student_' . $lang . '.json')) {
    $lang = "en";
}

echo '<script type="text/javascript">var site_root="'.$CFG->wwwroot.'"; var moodle_lang="'.$lang.'"; var id = "'.$id.'";</script>';

if ($review === 0) {
    // print the survey
    echo '<div class="container"><div class="alert alert-info">'.get_string('instructions', 'mod_ovmsurvey').'</div></div>';
    echo '<div id="surveyapp"></div>';
    echo '<script type="text/javascript" src="dist/index.bundle.js"></script>';
} else {
    // print the report
    $lib = new ovmsurvey($context, $id, $lang);
    $lib->view_report($id, $USER->id);
}

echo $OUTPUT->footer();