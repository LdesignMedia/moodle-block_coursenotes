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
 * block_coursenotes web services.
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   block_coursenotes
 * @copyright 27/05/2024 Mfreak.nl | LdesignMedia.nl - Luuk Verhoeven
 * @author    Nihaal Shaikh
 */

defined('MOODLE_INTERNAL') || die();

$functions = [
    'block_coursenotes_save_note' => [
        'classname' => 'block_coursenotes_external',
        'methodname' => 'save_note',
        'classpath' => 'blocks/coursenotes/externallib.php',
        'description' => 'Save the course note if conditions are met',
        'type' => 'write',
        'ajax' => true,
        'capabilities' => 'block/coursenotes:myaddinstance',
    ],
    'block_coursenotes_fetch_notes' => [
        'classname' => 'block_coursenotes_external',
        'methodname' => 'fetch_notes',
        'classpath' => 'blocks/coursenotes/externallib.php',
        'description' => 'Fetch all course notes for the current user and course',
        'type' => 'read',
        'ajax' => true,
        'capabilities' => 'block/coursenotes:myaddinstance',
    ],
];

$services = [
    'coursenotes_service' => [
        'functions' => ['block_coursenotes_save_note', 'block_coursenotes_fetch_notes'],
        'restrictedusers' => 0,
        'enabled' => 1,
    ],
];
