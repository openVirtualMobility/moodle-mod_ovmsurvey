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

class main implements renderable, templatable {

    public function __construct($lang, $cmid, $surveyid, $skillid) {
        $this->lang = $lang;
        $this->cmid = $cmid;
        $this->surveyid = $surveyid;
        $this->skillid = $skillid;
    }

    private static function get_json($lang, $skillid) {
        $string = file_get_contents(dirname(__FILE__) . '/../../json/'.$lang.'/students.json');
        $json = json_decode($string, true);
        $data = $json[$lang][0][$skillid+1];
        return $data;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        global $CFG;
        require_once($CFG->dirroot . '/mod/ovmsurvey/locallib.php');

        $statements = self::get_json($this->lang, $this->skillid);

        $total = get_question_total($statements);

        return [
            'cmid' => $this->cmid,
            'name' => $statements['name'],
            'surveyid' => $this->surveyid,
            'subskills' => $statements['subskills'],
            'total' => $total
        ];
    }
}
