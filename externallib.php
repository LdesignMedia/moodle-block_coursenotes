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


defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/externallib.php");
require_once(__DIR__ . '/classes/helper.php');

/**
 * The coursenotes block
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   block_coursenotes
 * @copyright 21/05/2024 Mfreak.nl | LdesignMedia.nl - Luuk Verhoeven
 * @author    Nihaal Shaikh
 */

/**
 * External API for the Course Notes block.
 *
 * Provides methods to save and fetch course notes via web services.
 */
class block_coursenotes_external extends external_api {

    /**
     * Defines the parameters for the save_note function
     *
     * @return external_function_parameters
     */
    public static function save_note_parameters(): external_function_parameters {
        return new external_function_parameters(
            [
                'coursenote' => new external_value(PARAM_TEXT, 'The course note text'),
                'courseid' => new external_value(PARAM_INT, 'The course ID'),
            ]
        );
    }

    /**
     * Save the course note
     *
     * @param $coursenote
     * @param $courseid
     *
     * @return array
     */
    public static function save_note($coursenote, $courseid): array {
        global $USER, $DB;

        // Validate parameters.
        $params = self::validate_parameters(
            self::save_note_parameters(),
            ['coursenote' => $coursenote, 'courseid' => $courseid]
        );

        // Check if the note length is greater than 10 characters.
        if (strlen($params['coursenote']) <= 10) {
            return ['status' => false, 'message' => 'Note is too short'];
        }

        // Fetch existing notes.
        $conditions = [
            'userid' => $USER->id,
            'courseid' => $params['courseid'],
        ];
        $notes = $DB->get_records('block_coursenotes', $conditions, 'timecreated ASC');

        // If the new note is the same as the latest one, no point in saving it.
        if (!empty($notes) && helper::isduplicate($params, $notes)) {
            return ['status' => false, 'message' => 'Note is the same as the latest one'];
        }

        // If the user has 10 or more notes, delete the oldest one.
        helper::deleteoldestnote($notes);

        // Insert the new note.
        $savenote = helper::savenote($params);

        return ['status' => $savenote['status'], 'message' => $savenote['message']];
    }

    /**
     * Returns the structure of the save_note function response
     *
     * @return external_single_structure
     */
    public static function save_note_returns(): external_single_structure {
        return new external_single_structure(
            [
                'status' => new external_value(PARAM_BOOL, 'Status of the request'),
                'message' => new external_value(PARAM_TEXT, 'Response message'),
            ]
        );
    }

    /**
     * Defines the parameters for the fetch_notes function
     *
     * @return external_function_parameters
     */
    public static function fetch_notes_parameters(): external_function_parameters {
        return new external_function_parameters(
            [
                'courseid' => new external_value(PARAM_INT, 'The course ID'),
            ]
        );
    }

    /**
     * Fetch the notes
     *
     * @param $courseid
     *
     * @return array
     */
    public static function fetch_notes($courseid): array {
        global $USER, $DB;

        // Validate parameters.
        $params = self::validate_parameters(self::fetch_notes_parameters(), ['courseid' => $courseid]);

        // Fetch notes.
        $conditions = [
            'userid' => $USER->id,
            'courseid' => $params['courseid'],
        ];
        $notes = $DB->get_records('block_coursenotes', $conditions, 'timecreated ASC');

        // Format notes for output.
        $notelist = [];
        foreach ($notes as $note) {
            $notelist[] = $note->coursenote;
        }

        return ['status' => true, 'notes' => $notelist, 'note_count' => count($notelist)];
    }

    /**
     * Returns the structure of the fetch_notes function response
     *
     * @return external_single_structure
     */
    public static function fetch_notes_returns(): external_single_structure {
        return new external_single_structure(
            [
                'status' => new external_value(PARAM_BOOL, 'Status of the request'),
                'notes' => new external_multiple_structure(
                    new external_value(PARAM_TEXT, 'Course note')
                ),
                'note_count' => new external_value(PARAM_INT, 'Number of course notes'),
            ]
        );
    }

}
