<?php
//// This file is part of Moodle - http://moodle.org/
////
//// Moodle is free software: you can redistribute it and/or modify
//// it under the terms of the GNU General Public License as published by
//// the Free Software Foundation, either version 3 of the License, or
//// (at your option) any later version.
////
//// Moodle is distributed in the hope that it will be useful,
//// but WITHOUT ANY WARRANTY; without even the implied warranty of
//// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//// GNU General Public License for more details.
////
//// You should have received a copy of the GNU General Public License
//// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
//
///**
// * The coursenotes block helper functions and callbacks
// *
// * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
// *
// * @package   block_coursenotes
// * @copyright 21/05/2024 Mfreak.nl | LdesignMedia.nl - Luuk Verhoeven
// * @author    Nihaal Shaikh
// */
//
//
///**
// * Validate coursenote parameter before perform other coursenotes actions
// *
// * @package  block_coursenotes
// * @category coursenote
// *
// * @param stdClass $coursenote_param {
// *              context  => context the context object
// *              courseid => int course id
// *              cm       => stdClass course module object
// *              coursenotearea => string coursenote area
// *              itemid      => int itemid
// * }
// * @return boolean
// */
//function block_coursenotes_coursenote_validate($coursenote_param) {
//    if ($coursenote_param->coursenotearea != 'page_coursenotes') {
//        throw new coursenote_exception('invalidcoursenotearea');
//    }
//    if ($coursenote_param->itemid != 0) {
//        throw new coursenote_exception('invalidcoursenoteitemid');
//    }
//    return true;
//}
//
///**
// * Running addtional permission check on plugins
// *
// * @package  block_coursenotes
// * @category coursenote
// *
// * @param stdClass $args
// * @return array
// */
//function block_coursenotes_coursenote_permissions($args) {
//    global $DB, $USER;
//    // By default, anyone can post and view coursenotes.
//    $canpost = $canview = true;
//    // Check if it's the user context and not the owner's profile.
//    if ($args->context->contextlevel == CONTEXT_USER && $USER->id != $args->context->instanceid) {
//        // Check whether the context owner has a coursenote block in the user's profile.
//        $sqlparam = [
//            'blockname' => 'coursenotes',
//            'parentcontextid' => $args->context->id,
//            'pagetypepattern' => 'user-profile',
//        ];
//        // If the coursenote block is not present at the target user's profile,
//        // then the logged-in user cannot post or view coursenotes.
//        $canpost = $canview = $DB->record_exists_select(
//            'block_instances',
//            'blockname = :blockname AND parentcontextid = :parentcontextid AND pagetypepattern = :pagetypepattern',
//            $sqlparam,
//        );
//    }
//    return ['post' => $canpost, 'view' => $canview];
//}
//
///**
// * Validate coursenote data before displaying coursenotes
// *
// * @package  block_coursenotes
// * @category coursenote
// *
// * @param stdClass $coursenote
// * @param stdClass $args
// * @return boolean
// */
//function block_coursenotes_coursenote_display($coursenotes, $args) {
//    if ($args->coursenotearea != 'page_coursenotes') {
//        throw new coursenote_exception('invalidcoursenotearea');
//    }
//    if ($args->itemid != 0) {
//        throw new coursenote_exception('invalidcoursenoteitemid');
//    }
//    return $coursenotes;
//}
