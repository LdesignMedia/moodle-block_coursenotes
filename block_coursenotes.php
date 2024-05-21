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
 * The coursenotes block
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   block_coursenotes
 * @copyright 21/05/2024 Mfreak.nl | LdesignMedia.nl - Luuk Verhoeven
 * @author    Nihaal Shaikh
 */

class block_coursenotes extends block_base {
    public function init() {
        $this->title = get_string('coursenotes', 'block_coursenotes');
    }

    public function get_content() {
        global $USER, $DB, $COURSE;

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass();
        $this->content->text = '';

        $notes = $DB->get_record('block_coursenotes', array('userid' => $USER->id, 'courseid' => $COURSE->id));

        if ($notes) {
            $this->content->text .= format_text($notes->note);
        } else {
            $this->content->text .= get_string('nonotes', 'block_coursenotes');
        }

        $this->content->text .= '<form method="post" action="">';
        $this->content->text .= '<textarea name="coursenote" rows="4" cols="50">' . ($notes ? $notes->note : '') . '</textarea>';
        $this->content->text .= '<input type="submit" value="' . get_string('savenote', 'block_coursenotes') . '">';
        $this->content->text .= '</form>';

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['coursenote'])) {
            $note = new stdClass();
            $note->userid = $USER->id;
            $note->courseid = $COURSE->id;
            $note->note = $_POST['coursenote'];

            if ($notes) {
                $note->id = $notes->id;
                $DB->update_record('block_coursenotes', $note);
            } else {
                $DB->insert_record('block_coursenotes', $note);
            }

            // Refresh the page to see the changes.
            redirect(new moodle_url('/course/view.php', array('id' => $COURSE->id)));
        }

        return $this->content;
    }

    public function applicable_formats() {
        return array('course-view' => true);
    }
}
