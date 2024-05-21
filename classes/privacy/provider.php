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
// * Privacy Subsystem implementation for block_coursenotes.
// *
// * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
// *
// * @package   block_coursenotes
// * @copyright 21/05/2024 Mfreak.nl | LdesignMedia.nl - Luuk Verhoeven
// * @author    Nihaal Shaikh
// */
//
//namespace block_coursenotes\privacy;
//
//use core_privacy\local\metadata\collection;
//use core_privacy\local\request\approved_contextlist;
//use core_privacy\local\request\contextlist;
//use core_privacy\local\request\userlist;
//use core_privacy\local\request\approved_userlist;
//
///**
// * Privacy Subsystem implementation for block_coursenotes.
// *
// * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
// *
// * @package   block_coursenotes
// * @copyright 21/05/2024 Mfreak.nl | LdesignMedia.nl - Luuk Verhoeven
// * @author    Nihaal Shaikh
// */
//class provider implements
//        // The block_coursenotes block stores user provided data.
//        \core_privacy\local\metadata\provider,
//        \core_privacy\local\request\core_userlist_provider,
//        // The block_coursenotes block provides data directly to core.
//        \core_privacy\local\request\plugin\provider {
//
//    /**
//     * Returns meta data about this system.
//     *
//     * @param collection $collection
//     * @return collection
//     */
//    public static function get_metadata(collection $collection) : collection {
//        return $collection->add_subsystem_link('core_coursenote', [], 'privacy:metadata:core_coursenote');
//    }
//
//    /**
//     * Get the list of contexts that contain user information for the specified user.
//     *
//     * @param int $userid
//     * @return contextlist
//     */
//    public static function get_contexts_for_userid(int $userid) : contextlist {
//        $contextlist = new contextlist();
//
//        $sql = "SELECT contextid
//                  FROM {coursenotes}
//                 WHERE component = :component
//                   AND userid = :userid";
//        $params = [
//            'component' => 'block_coursenotes',
//            'userid' => $userid
//        ];
//
//        $contextlist->add_from_sql($sql, $params);
//
//        return $contextlist;
//    }
//
//    /**
//     * Get the list of users within a specific context.
//     *
//     * @param userlist $userlist The userlist containing the list of users who have data in this context/plugin combination.
//     */
//    public static function get_users_in_context(userlist $userlist) {
//        $context = $userlist->get_context();
//
//        $params = [
//            'contextid' => $context->id,
//            'component' => 'block_coursenotes',
//        ];
//
//        $sql = "SELECT userid as userid
//                  FROM {coursenotes}
//                 WHERE component = :component
//                       AND contextid = :contextid";
//
//        $userlist->add_from_sql('userid', $sql, $params);
//    }
//
//    /**
//     * Export all user data for the specified user, in the specified contexts.
//     *
//     * @param approved_contextlist $contextlist
//     */
//    public static function export_user_data(approved_contextlist $contextlist) {
//        $contexts = $contextlist->get_contexts();
//        foreach ($contexts as $context) {
//            \core_coursenote\privacy\provider::export_coursenotes(
//                    $context,
//                    'block_coursenotes',
//                    'page_coursenotes',
//                    0,
//                    []
//            );
//        }
//    }
//
//    /**
//     * Delete all data for all users in the specified context.
//     *
//     * @param \context $context
//     */
//    public static function delete_data_for_all_users_in_context(\context $context) {
//        \core_coursenote\privacy\provider::delete_coursenotes_for_all_users($context, 'block_coursenotes');
//    }
//
//    /**
//     * Delete multiple users within a single context.
//     *
//     * @param approved_userlist $userlist The approved context and user information to delete information for.
//     */
//    public static function delete_data_for_users(approved_userlist $userlist) {
//        \core_coursenote\privacy\provider::delete_coursenotes_for_users($userlist, 'block_coursenotes');
//    }
//
//    /**
//     * Delete all user data for the specified user, in the specified contexts.
//     *
//     * @param approved_contextlist $contextlist
//     */
//    public static function delete_data_for_user(approved_contextlist $contextlist) {
//        \core_coursenote\privacy\provider::delete_coursenotes_for_user($contextlist, 'block_coursenotes');
//    }
//}
