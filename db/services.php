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

$functions = array(
    'mod_ovmsurvey_get_answers' => array(
        'classname'     => 'mod_ovmsurvey_external',
        'methodname'    => 'get_answers',
        'description'   => 'Get the choices of the user for a given survey',
        'type'          => 'write',
        'ajax'          => true,
        'capabilities'  => ''
    ),
    'mod_ovmsurvey_set_answer' => array(
        'classname'     => 'mod_ovmsurvey_external',
        'methodname'    => 'set_answer',
        'description'   => 'Set the choice of the user for a given statement',
        'type'          => 'write',
        'ajax'          => true,
        'capabilities'  => ''
    )
);
