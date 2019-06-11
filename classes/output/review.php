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
 * @copyright  2019 Pierre Duverneix <pierre.duverneix@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_ovmsurvey\output;
defined('MOODLE_INTERNAL') || die();

use renderable;
use renderer_base;
use templatable;
use stdClass;

class review implements renderable, templatable {

    public function __construct($lang, $courseid, $surveyid) {
        $this->lang = $lang;
        $this->courseid = $courseid;
        $this->surveyid = $surveyid;
    }

    private static function get_json($lang) {
        $string = file_get_contents(dirname(__FILE__) . '/../../json/'.$lang.'/students.json');
        $json = json_decode($string, true);
        $data = $json[$lang][0];
        return $data;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        global $CFG, $USER, $DB;
        require_once($CFG->dirroot . '/mod/ovmsurvey/locallib.php');

        $datas = self::get_json($this->lang);
        $surveys = $DB->get_records('ovmsurvey', array('course' => $this->courseid));
        $dataobject = array();

        foreach ($surveys as $survey) {
            $records = $DB->get_records('ovmsurvey_response', array(
                'survey_id' => $survey->id, 
                'user_id' => $USER->id));

            foreach ($datas as $k => $data) {
                // If the survey is set in the course.
                if ($k == intval($survey->skill + 1)) {
                    $skill = new \stdClass();
                    $skill->name = $data['name'];
                    $skill->subskills = [];

                    if ($data['subskills']) {
                        foreach ($data['subskills'] as $subskill) {
                            $subobj = new \stdClass();
                            $subobj->name = $subskill['name'];
                            $subobj->statements = [];

                            foreach ($subskill['statements'] as $stmt) {
                                $stmtobj = new \stdClass();
                                $stmtobj->id = $stmt['id'];
                                $stmtobj->stmt = $stmt['stmt'];
                                $stmtobj->response = '-';

                                // Getting the user response.
                                foreach ($records as $r) {
                                    if ($stmt['id'] == $r->question_id) {
                                        $stmtobj->response = $r->response;
                                    }
                                }

                                array_push($subobj->statements, $stmtobj);
                            }

                            array_push($skill->subskills, $subobj);
                        }
                    }

                    array_push($dataobject, $skill);
                }
            }
        }

        return [
            'data' => $dataobject
        ];
    }
}
