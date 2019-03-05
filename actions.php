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
 * Internal functions for the module ovmsurvey
 *
 * @package   mod_ovmsurvey
 * @author    Pierre Duverneix
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../config.php');

defined('MOODLE_INTERNAL') || die();

const PLUGIN_NAME = 'ovmsurvey';

if (isguestuser() || !isloggedin()) {
    require_login();
}

function router($httpMethods, $route, $callback, $exit = true) {
    static $path = null;

    if ($path === null) {
        $path = parse_url($_SERVER['REQUEST_URI'])['path'];
        $scriptName = dirname(dirname($_SERVER['SCRIPT_NAME']));
        $scriptName = str_replace('\\', '/', $scriptName);
        $len = strlen($scriptName);
        if ($len > 0 && $scriptName !== '/') {
            $path = substr($path, $len);
        }
    }

    if (!in_array($_SERVER['REQUEST_METHOD'], (array) $httpMethods)) {
        return;
    }

    $matches = null;
    $regex = '/' . str_replace('/', '\/', $route) . '/';

    if (!preg_match_all($regex, $path, $matches)) {
        return;
    }

    if (empty($matches)) {
        $callback();
    } else {
        $params = array();
        foreach ($matches as $k => $v) {
            if (!is_numeric($k) && !isset($v[1])) {
                $params[$k] = $v[0];
            }
        }
        $callback($params);
    }

    if ($exit) {
        exit;
    }
}

function build_response($code) {
    switch($code) {
        case 404:
            header("HTTP/1.1 404 Not Found");
            echo '404 Not Found';
            break;
        case 500:
            header("HTTP/1.1 500 Internal Server Error");
            echo '500 Internal Server Error';
            break;
        default:
            header("HTTP/1.1 500 Internal Server Error");
            echo '500 Internal Server Error';
            break;
    }
}

function get_lang($user) {
    global $CFG;
    if (isset($_GET['lang'])) {
        $lang = $_GET['lang'];
    } else {
        $lang = $CFG->lang;
        if (isset($user->lang)) {
            $lang = $user->lang;
        }
    }
    if (!file_exists('json/questions_student_' . $lang . '.json')) {
        $lang = "en";
    }
    return $lang;
}

/*
 * Retrieve the selected skill for the survey ID
 */
router('GET', '/actions.php/skill/(?<survey_id>\w+)$', function($params) {
    global $DB;
    $sql =  'SELECT * FROM {course_modules} '.
            'WHERE id = ?';
    $mod = $DB->get_record_sql($sql, array($params['survey_id']));

    if ($mod) {
        $sql_query =  'SELECT * FROM {ovmsurvey} '.
            'WHERE id = ?';
        $record = $DB->get_record_sql($sql_query, array($mod->instance));
        
        if ($record) {
            header('Content-Type: application/json');
            echo json_encode(['results' => $record->skill]);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['results' => NULL]);
        }
    } else {
        header('Content-Type: application/json');
        echo json_encode(['results' => NULL]);
    }
});

/*
 * Retrieve all the responses from the given survey step
 */
router('GET', '/actions.php/responses/(?<survey_id>\w+)/(?<step_id>\d+)$', function($params) {
    global $DB, $USER;
    $sql =  'SELECT * FROM {ovmsurvey_response} '.
            'WHERE survey_id = ? '.
            'AND step_id = ? '.
            'AND user_id = ? '.
            'ORDER BY question_id ASC';
    $records = $DB->get_records_sql($sql, array(
        $params['survey_id'],
        $params['step_id'],
        $USER->id));

    if ($records) {
        header('Content-Type: application/json');
        echo json_encode(['results' => $records]);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['results' => NULL]);
    }
});

/*
 * Post an answer
 */
router('POST', '/actions.php/response$', function() {
    $json = json_decode(file_get_contents('php://input'), true);

    global $DB, $USER;
    $date = new DateTime();

    $sql =  'SELECT COUNT(*) as total FROM {ovmsurvey_response} '.
            'WHERE survey_id = ? '.
            'AND survey_type = ? '.
            'AND step_id = ? '.
            'AND question_id = ? '.
            'AND user_id = ? '.
            'ORDER BY question_id ASC';

    $record = $DB->get_record_sql($sql, array(
        $json['survey_id'],
        $json['survey_type'],
        $json['step_id'],
        $json['question_id'],
        $USER->id));

    if ($record->total > 0) {
        $DB->delete_records('ovmsurvey_response', array(
            'survey_id' => $json['survey_id'],
            'survey_type' => $json['survey_type'],
            'step_id' => $json['step_id'],
            'question_id' => $json['question_id'],
            'user_id' => $USER->id));
    } 

    $obj = new stdClass();
    $obj->survey_id = $json['survey_id'];
    $obj->survey_type = $json['survey_type'];
    $obj->step_id = $json['step_id'];
    $obj->question_id = $json['question_id'];
    $obj->user_id = $USER->id;
    $obj->response = $json['response'];
    $obj->timecreated = $date->getTimestamp();
    $obj->id = $DB->insert_record('ovmsurvey_response', $obj);

    if ($obj->id) {
        header('Content-Type: application/json');
        echo json_encode(['results' => $obj]);
    } else {
        build_response(500);
    }
});

/*
 * Delete all the responses from the given survey step (after change status)
 */
router('GET', '/actions.php/change_status/(?<survey_id>\w+)/(?<step_id>\d+)$', function($params) {
    global $DB, $USER;

    $DB->delete_records('ovmsurvey_response', array(
        'survey_id' => $params['survey_id'],
        'step_id' => $params['step_id'],
        'user_id' => $USER->id));

    header('Content-Type: application/json');
    echo json_encode(['results' => []]);
});

/*
 * Format chart data
 */
router('GET', '/actions.php/chart_data$', function($params) {
    global $DB, $USER;

    $qid = 0;
    $step_id = 0;
    $labels = [];
    $data = [];

    $lang = get_lang($USER);
    $string = file_get_contents(dirname(__FILE__) . '/json/questions_student_'.$lang.'.json');
    $json = json_decode($string, true);
    $competencies = $json[$lang][0];

    foreach($competencies as $key => $val) {
        $competency = $val;
        $subres = 0;
        $subtotal = 0;

        foreach($competency['subskills'] as $subskill) {
            foreach($subskill['statements'] as $k => $v) {
                $sql =  'SELECT * FROM {ovmsurvey_response} '.
                        'WHERE step_id = ? '.
                        'AND question_id = ? '.
                        'AND user_id = ? '.
                        'ORDER BY id DESC LIMIT 1';

                $record = $DB->get_record_sql($sql, array(
                    $step_id,
                    ++$qid,
                    $USER->id));

                $subtotal = $subtotal + 4;
                
                if ($record) {
                    $subres = $subres + intval($record->response);
                }
            }
        }

        $porcent = 0;
        if ($subtotal > 0) {
            $porcent = round((($subres / $subtotal * 100)), 2);
        }

        // push results to json array
        array_push($labels, $competency['name']);
        array_push($data, $porcent);
        // increment step, as one compentency = one step
        $step_id++;
    }

    $res = array("labels" => $labels, "data" => $data);
    header('Content-Type: application/json');
    echo json_encode(['results' => $res]);
});

header("HTTP/1.1 404 Not Found");
echo '404 Not Found';