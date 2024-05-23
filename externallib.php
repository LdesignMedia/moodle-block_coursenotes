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

require_once("$CFG->libdir/externallib.php");
require_once(__DIR__ . '/classes/helper.php');

class block_coursenotes_external extends external_api {

    public static function save_note_parameters() {
        return new external_function_parameters(
            array(
                'coursenote' => new external_value(PARAM_TEXT, 'The course note text'),
                'blockinstanceid' => new external_value(PARAM_INT, 'The block instance ID'),
                'courseid' => new external_value(PARAM_INT, 'The course ID'),
            )
        );
    }

    public static function save_note($coursenote, $blockinstanceid, $courseid) {
        global $USER, $DB;

        // Validate parameters.
        $params = self::validate_parameters(
            self::save_note_parameters(),
            ['coursenote' => $coursenote, 'blockinstanceid' => $blockinstanceid, 'courseid' => $courseid]
        );

        // Check if the note length is greater than 10 characters.
        if (strlen($params['coursenote']) <= 10) {
            return ['status' => false, 'message' => 'Note is too short'];
        }

        // Fetch existing notes.
        $conditions = [
            'userid' => $USER->id,
            'courseid' => $params['courseid'],
            'blockinstanceid' => $params['blockinstanceid']
        ];
        $notes = $DB->get_records('block_coursenotes', $conditions, 'timecreated ASC');

        // If the user has 10 or more notes, delete the oldest one.
        helper::deleteoldestnoteifneeded($notes);

        // Insert the new note.
        $savenote = helper::savenote($params);

        return ['status' => $savenote['status'], 'message' => $savenote['message']];
    }

    public static function save_note_returns() {
        return new external_single_structure(
            array(
                'status' => new external_value(PARAM_BOOL, 'Status of the request'),
                'message' => new external_value(PARAM_TEXT, 'Response message')
            )
        );
    }
}
