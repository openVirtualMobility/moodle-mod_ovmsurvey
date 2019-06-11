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
 * @package   mod_ovmsurvey
 * @author    Pierre Duverneix
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function get_lang($lang = 'en') {
    global $CFG, $USER;

    if (isset($_GET['lang'])) {
        $lang = $_GET['lang'];
    } else {
        $lang = $CFG->lang;
        if (isset($USER->lang)) {
            $lang = $USER->lang;
        }
    }

    return $lang;
}

function get_status() {
    global $DB, $USER;

    $status = $DB->get_record('ovmsurvey_status', array('userid' => $USER->id));
    if ($status && $status->status) {
        return $status->status;
    }

    return 'student';
}

function get_question_total($skill) {
    $count = 0;

    foreach ($skill['subskills'] as $subskills) {
        $count = $count + count($subskills['statements']);
    }

    return $count;
}

function get_skills($lang) {
    $string = file_get_contents(dirname(__FILE__) . '/json/'.$lang.'/students.json');
    $json = json_decode($string, true);
    $data = $json[$lang][0];
    return $data;
}

function build_review_data($skills, $courseid) {
    global $DB, $USER;

    $surveys = $DB->get_records('ovmsurvey', array('course' => $courseid));
    $dataobject = array();
    $scores = array();

    foreach ($skills as $k => $data) {
        $stmtcount = 0;
        $score = 0;

        if ($data['subskills']) {
            foreach ($data['subskills'] as $subskill) {
                // Make count for porcents.
                $stmtcount = $stmtcount + count($subskill['statements']) * 4;

                foreach ($subskill['statements'] as $stmt) {
                    // Getting the user response.
                    foreach ($surveys as $survey) {
                        $records = $DB->get_records('ovmsurvey_response', array(
                            'survey_id' => $survey->id,
                            'user_id' => $USER->id));

                        foreach ($records as $r) {
                            if ($stmt['id'] == $r->question_id) {
                                $score = $score + $r->response;
                                break;
                            }
                        }
                    }
                }
            }
        }

        $porcent = round(($score / $stmtcount), 2);
        array_push($scores, '{axis:"'.$data['name'].'",value:'.$porcent.'},');
    }

    return $scores;
}