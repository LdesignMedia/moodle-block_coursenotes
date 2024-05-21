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

    function init() {
        global $CFG;

        require_once($CFG->dirroot . '/coursenote/lib.php');

        $this->title = get_string('pluginname', 'block_coursenotes');
    }

    function specialization() {
        // require js for commenting
        coursenote::init();
    }
    function applicable_formats() {
        return array('all' => true);
    }

    function instance_allow_multiple() {
        return false;
    }

    function get_content() {
        global $CFG;

        if ($this->content !== NULL) {
            return $this->content;
        }
        if (!$CFG->usecomments) {
            $this->content = new stdClass();
            $this->content->text = '';
            if ($this->page->user_is_editing()) {
                $this->content->text = get_string('disabledcomments');
            }
            return $this->content;
        }
        $this->content = new stdClass();
        $this->content->footer = '';
        $this->content->text = '';
        if (empty($this->instance)) {
            return $this->content;
        }
        [$context, $course, $cm] = get_context_info_array($this->page->context->id);

        $args = new stdClass;
        $args->context   = $this->page->context;
        $args->course    = $course;
        $args->area      = 'page_coursenotes';
        $args->itemid    = 0;
        $args->component = 'block_coursenotes';
        $args->linktext  = get_string('showcoursenotes');
        $args->notoggle  = true;
        $args->autostart = true;
        $args->displaycancel = false;
        $coursenote = new coursenote($args);
        $coursenote->set_view_permission(true);
        $coursenote->set_fullwidth();

        $this->content = new stdClass();
        $this->content->text = $coursenote->output(true);
        $this->content->footer = '';
        return $this->content;
    }

    /**
     * This block shouldn't be added to a page if the comments advanced feature is disabled.
     *
     * @param moodle_page $page
     * @return bool
     */
    public function can_block_be_added(moodle_page $page): bool {
        global $CFG;

        return $CFG->usecoursenotes;
    }
}
