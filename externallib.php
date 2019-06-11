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

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir . "/externallib.php");

/**
 * feedback save functions
 */
class mod_ovmsurvey_external extends external_api {
    /**
     * Describes the parameters for get_answers.
     *
     * @return external_function_parameters
     * @since  Moodle 3.4
     */
    public static function get_answers_parameters() {
        return new external_function_parameters(
            array(
                'surveyid' => new external_value(PARAM_INT, 'The survey ID')
            )
        );
    }

    public static function get_answers($surveyid) {
        global $DB, $USER;

        // Parameters validation.
        $params = self::validate_parameters(self::get_answers_parameters(),
            array('surveyid' => $surveyid));

        $data = $DB->get_records('ovmsurvey_response', array('survey_id' => $surveyid, 'user_id' => $USER->id));
        return $data;
    }

    /**
     * Returns description of method result value.
     *
     * @return external_description
     * @since Moodle 3.4
     */
    public static function get_answers_returns() {
        return new external_multiple_structure(new external_single_structure(
            array(
                'id' => new external_value(PARAM_INT, 'The component id.'),
                'survey_id' => new external_value(PARAM_INT, 'The survey id.'),
                'question_id' => new external_value(PARAM_INT, 'The question id.'),
                'user_id' => new external_value(PARAM_INT, 'The user id.'),
                'response' => new external_value(PARAM_INT, 'Value of the answer.'),
                'timecreated' => new external_value(PARAM_INT, 'Timestamp.')
            )
        ));
    }


    /**
     * Describes the parameters for set_answer.
     *
     * @return external_function_parameters
     * @since  Moodle 3.4
     */
    public static function set_answer_parameters() {
        return new external_function_parameters(
            array(
                'surveyid' => new external_value(PARAM_INT, 'The survey ID'),
                'status' => new external_value(PARAM_TEXT, 'The user status'),
                'stmtid' => new external_value(PARAM_INT, 'The statement ID'),
                'value' => new external_value(PARAM_INT, 'The value'),
            )
        );
    }

    public static function set_answer($surveyid, $status, $stmtid, $value) {
        global $DB, $USER;

        // Parameters validation.
        $params = self::validate_parameters(self::set_answer_parameters(),
            array('surveyid' => $surveyid, 'status' => $status, 'stmtid' => $stmtid, 'value' => $value));

        $dataobject = new \stdClass();
        $dataobject->survey_id = $params['surveyid'];
        $dataobject->survey_type = $params['status'];
        $dataobject->question_id = $params['stmtid'];
        $dataobject->user_id = $USER->id;
        $dataobject->response = $params['value'];
        $dataobject->timecreated = \time();

        $data = $DB->get_record('ovmsurvey_response', array(
            'survey_id' => $params['surveyid'],
            'question_id' => $params['stmtid'],
            'user_id' => $USER->id));

        if ($data) {
            $dataobject->id = $data->id;
            $DB->update_record('ovmsurvey_response', $dataobject);
        } else {
            $DB->insert_record('ovmsurvey_response', $dataobject, true);
        }

        $total = $DB->count_records('ovmsurvey_response', array(
            'survey_id' => $params['surveyid'],
            'user_id' => $USER->id));

        return $total;
    }

    /**
     * Returns description of method result value.
     *
     * @return external_description
     * @since Moodle 3.4
     */
    public static function set_answer_returns() {
        return new external_value(PARAM_INT, 'Total of the users answers for the given survey.');
    }
}