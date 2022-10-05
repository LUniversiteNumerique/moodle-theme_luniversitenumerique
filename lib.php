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
 * Lib file.
 *
 * @package   theme_luniversitenumerique
 * @copyright 2022 Pierre Duverneix
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// This line protects the file from being accessed by a URL directly
defined('MOODLE_INTERNAL') || die();

/**
 * Extend the theme navigation
 *
 * @param flat_navigation $flatnav
 */
function theme_luniversitenumerique_extend_flat_navigation(\flat_navigation $flatnav) {
    theme_luniversitenumerique_delete_menuitems($flatnav);
}

/**
 * Remove items from navigation
 *
 * @param flat_navigation $flatnav
 */
function theme_luniversitenumerique_delete_menuitems(\flat_navigation $flatnav) {
    $itemstodelete = [
        'privatefiles',
        'badgesview',
        'competencies',
        'grades',
        'calendar'
    ];

    $coursehome = null;
    $sections = array();

    foreach ($flatnav as $item) {
        if ($item->key == 'coursehome') {
            $coursehome = $item;
            $coursehome->iscustom = true;
            $flatnav->remove($item->key);
            continue;
        }

        if ($item->text == "" || $item->text == " ") {
            $flatnav->remove($item->key);
            continue;
        }

        if ($item->key > 0 && isset($item->parent) && $item->parent->key != 'mycourses') {
            // Sections
            $sections[] = $item;
            $flatnav->remove($item->key);
            continue;
        }

        if (in_array($item->key, $itemstodelete)) {
            $flatnav->remove($item->key);
            continue;
        }

        if (isset($item->parent->key) && $item->parent->key == 'mycourses' &&
            isset($item->type) && $item->type == \navigation_node::TYPE_COURSE) {

            $flatnav->remove($item->key, \navigation_node::TYPE_COURSE);
            continue;
        }
    }

    if ($coursehome != null) {
        $coursehome->iscustom = true;
        $coursehome->iscoursehome = true;
        $coursehome->text = "Accueil du cours";
        $coursehome->icon->pix = "i/sign-out-alt";
        $flatnav->add($coursehome);
    }

    foreach ($sections as $item) {
        $item->iscustom = true;
        $item->icon->pix = "i/folder";
        $flatnav->add($item);
    }
}
