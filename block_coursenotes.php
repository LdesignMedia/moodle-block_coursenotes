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

require_once(__DIR__ . '/externallib.php');

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
        global $USER, $DB, $COURSE, $OUTPUT;

        if ($this->content !== null) {
            return $this->content;
        }

        $this->page->requires->js_call_amd('block_coursenotes/coursenotes', 'init');
        $this->content = new stdClass();

        // Prepare data for the template.
        $data = [
            'title' => $this->title,
            'content' => '',
            'coursenote' => '',
            'savebutton' => get_string('savenote', 'block_coursenotes'),
            'blockinstanceid' => $this->context->instanceid,
            'courseid' => $COURSE->id,
        ];

        // Fetch notes from the database based on instance id.
        $conditions = [
            'userid' => $USER->id,
            'courseid' => $COURSE->id,
            'blockinstanceid' => $this->context->instanceid,
        ];
        $notes = $DB->get_records('block_coursenotes', $conditions, 'timecreated DESC', '*', 0, 1);

        if ($notes) {
            $latestnote = reset($notes); // Get the first (latest) record.
            $data['coursenote'] = $latestnote->coursenote;
        }

        // Render the Mustache template.
        $templatecontext = (object) array_merge($data, ['output' => $OUTPUT]);
        $this->content->text = $OUTPUT->render_from_template('block_coursenotes/block', $templatecontext);

        // Handle form submission.
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['coursenote'])) {

            $coursenote = required_param('coursenote', PARAM_TEXT);
            // Save the note.
            block_coursenotes_external::save_note($coursenote, $this->context->instanceid, $COURSE->id);
            // Refresh the page to see the changes.
            redirect(new moodle_url('/course/view.php', ['id' => $COURSE->id]));
        }

        return $this->content;
    }

    public function applicable_formats() {
        return ['course-view' => true, 'site-index' => false, 'my' => true];
    }
}
