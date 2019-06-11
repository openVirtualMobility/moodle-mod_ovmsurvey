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

/**
 * Add ovmsurvey instance.
 * @param object $data
 * @param object $mform
 * @return int new ovmsurvey instance id
 */
function ovmsurvey_add_instance($data, $mform) {
    global $DB;

    $cmid        = $data->coursemodule;

    $data->timemodified = time();
    $data->id = $DB->insert_record('ovmsurvey', $data);

    $DB->set_field('course_modules', 'instance', $data->id, array('id' => $cmid));
    $context = context_module::instance($cmid);

    $completiontimeexpected = !empty($data->completionexpected) ? $data->completionexpected : null;
    \core_completion\api::update_completion_date_event($data->coursemodule, 'ovmsurvey', $data->id, $completiontimeexpected);

    return $data->id;
}


/**
 * Update ovmsurvey instance.
 * @param object $data
 * @param object $mform
 * @return bool true
 */
function ovmsurvey_update_instance($data, $mform) {
    global $CFG, $DB;

    $cmid        = $data->coursemodule;
    $data->timemodified = time();
    $data->id           = $data->instance;

    $DB->update_record('ovmsurvey', $data);

    $context = context_module::instance($cmid);

    $completiontimeexpected = !empty($data->completionexpected) ? $data->completionexpected : null;
    \core_completion\api::update_completion_date_event($data->coursemodule, 'ovmsurvey', $data->id, $completiontimeexpected);

    return true;
}


/**
 * Delete ovmsurvey instance.
 * @param int $id
 * @return bool true
 */
function ovmsurvey_delete_instance($id) {
    global $DB;

    if (!$ovmsurvey = $DB->get_record('ovmsurvey', array('id' => $id))) {
        return false;
    }

    $cm = get_coursemodule_from_instance('ovmsurvey', $id);
    \core_completion\api::update_completion_date_event($cm->id, 'ovmsurvey', $ovmsurvey->id, null);

    $DB->delete_records('ovmsurvey', array('id' => $ovmsurvey->id));

    return true;
}