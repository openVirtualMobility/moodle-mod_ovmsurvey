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
 * Local library of functions
 *
 * @package   mod_ovmsurvey
 * @author    Pierre Duverneix
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/accesslib.php');

class ovmsurvey {

    /** @var stdClass the course ID of the survey */
    private $courseid;

    /** @var context the context of the course module */
    private $context;

    /** @var context determines wether the user has teacher capabilities or not */
    public $is_teacher;

    /** @var stdClass the lang of Moodle */
    private $lang;

    /** @var stdClass the questions of the JSON file for the given lang */
    private $competencies;

    public function __construct($context, $courseid, $lang) {
        global $SESSION, $USER;

        require_login(null, false);
        if (isguestuser()) {
            throw new require_login_exception('Guests are not allowed here.');
        }

        $this->context = $context;
        $this->courseid = $courseid;
        $this->is_teacher = false;
        $this->lang = $lang;

        if (has_capability('moodle/course:manageactivities', $context)) {
            $this->is_teacher = true;
        }

        $string = file_get_contents(dirname(__FILE__) . '/json/questions_student_' . $this->lang . '.json');
        $json = json_decode($string, true);
        $this->competencies = $json[$this->lang][0];
    }

    public function view_report($survey_id, $uid) {
        if (isset($survey_id) && $survey_id != 0) {
            echo "<div class='ovmsurvey-report'>";
            $this->print_report($survey_id, $uid);
            echo "</div>";
        }
    }

    private function print_report($survey_id, $uid) {
        global $DB;

        $qid = 0;
        $step_id = 0;
        $labels = [];
        $data = [];

        echo "<div class=\"col-12\"><h3>". get_string('reports_heading', 'mod_ovmsurvey') ."</h3></div>";
        echo "<canvas  id=\"report-chart\" class=\"radar-chart col-12\"></canvas>";

        foreach($this->competencies as $key => $val) {
            $competency = $val;
            echo "<div class='col-12'>";
            echo "<table class='table table-striped table-hover table-results'>";
            echo "<thead class='thead-dark'>";
            echo "<tr>";
            echo "<th colspan='2' scope='col'><h4>" . $competency['name'] . "</h4></th>";
            echo "</tr>";
            echo "</thead>";
            echo "<tbody>";

            $subres = 0;
            $subtotal = 0;

            foreach($competency['subskills'] as $subskill) {
                echo "<tr class='thead-light'>";
                echo "<th colspan='2' scope='col'><h5>".$subskill['name']."</h5></th>";

                foreach($subskill['statements'] as $k => $v) {
                    echo "<tr>";
                    $sql =  'SELECT * FROM {ovmsurvey_response} '.
                            'WHERE step_id = ? '.
                            'AND question_id = ? '.
                            'AND user_id = ? '.
                            'ORDER BY id DESC LIMIT 1';

                    $record = $DB->get_record_sql($sql, array(
                        $step_id,
                        ++$qid,
                        $uid
                    ));

                    $subtotal = $subtotal + 4;

                    if ($record) {
                        echo "<td scope='row'>". $v['stmt'] . "</td>";
                        echo "<td scope='row'>" . $record->response . "</td>";
                        $subres = $subres + intval($record->response);
                    } else {
                        echo "<td scope='row'>". $v['stmt'] . "</td>";
                        echo "<td scope='row'>-</td>";
                    }
                    echo "</tr>";
                }
            }

            $porcent = 0;
            if ($subtotal > 0) {
                $porcent = round((($subres / $subtotal)), 2);
            }

            // push results to json array
            array_push($labels, $competency['name']);
            array_push($data, $porcent);
            // increment step, as one compentency = one step
            $step_id++;
            echo "</tbody></table></div>";
        }

        echo "<script src=\"dist/chart.bundle.js\" charset=\"utf-8\"></script>";
    }
}