<?php

defined('MOODLE_INTERNAL') || die();

function get_question_total($skill) {
    $count = 0;

    foreach($skill['subskills'] as $subskills) {
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
                                $score =  $score + $r->response;
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