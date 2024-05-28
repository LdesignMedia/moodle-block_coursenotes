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
 * Block Coursenotes presets helper class.
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   block_coursenotes
 * @copyright 23/05/2024 Mfreak.nl | LdesignMedia.nl - Luuk Verhoeven
 * @author    Nihaal Shaikh
 */
class helper {

    /**
     * Will delete the oldest coursenote before saving the new one if total coursenotes for the
     * course are > 10
     *
     * @param $notes
     *
     * @return void
     */
    public static function deleteoldestnote($notes): void {
        global $DB;

        // If the user has 10 or more notes, delete the oldest one.
        if (count($notes) >= 10) {
            $oldestnote = reset($notes);
            $DB->delete_records('block_coursenotes', ['id' => $oldestnote->id]);
        }
    }

    /**
     * Delete all coursenotes for the course
     *
     * @return void
     */
    public static function deletecoursenotes(): void {
        global $DB, $COURSE;

        $conditions = [
            'courseid' => $COURSE->id,
        ];
        $DB->delete_records('block_coursenotes', $conditions);
    }

    /**
     * Save the coursenote
     *
     * @param $params
     *
     * @return array
     */
    public static function savenote($params): array {
        global $USER, $DB;

        // Insert the new note.
        $note = new stdClass();
        $note->userid = $USER->id;
        $note->courseid = $params['courseid'];
        $note->coursenote = $params['coursenote'];
        $note->timecreated = time();

        try {
            $DB->insert_record('block_coursenotes', $note);

            return ['status' => true, 'message' => 'Note saved successfully'];
        } catch (Exception $e) {
            return ['status' => false, 'message' => 'Error saving note: ' . $e->getMessage()];
        }
    }

    /**
     * Check if the new note is the same as the latest note
     *
     * @param $params
     * @param $notes
     *
     * @return bool
     */
    public static function isduplicate($params, $notes): bool {
        $latestnote = end($notes); // Get the latest note.
        if ($latestnote->coursenote === $params['coursenote']) {
            return true;
        }
        return false;
    }

    /**
     * Fetches the latest course note for a user and course.
     *
     * @return mixed The latest course note or false if none found.
     */
    public static function fetch_latest_coursenote_for_user(): mixed {
        global $DB, $COURSE, $USER;

        $conditions = [
            'userid' => $USER->id,
            'courseid' => $COURSE->id,
        ];

        $notes = $DB->get_records('block_coursenotes', $conditions, 'timecreated DESC', '*', 0, 1);

        return $notes ? reset($notes) : false;
    }

    /**
     * Fetches all notes for a user and course.
     *
     * @return array An array of course notes.
     */
    public static function fetch_all_coursenotes_for_user(): array {
        global $DB, $COURSE, $USER;

        $conditions = [
            'userid' => $USER->id,
            'courseid' => $COURSE->id,
        ];

        return $DB->get_records('block_coursenotes', $conditions, 'timecreated ASC');
    }
}
