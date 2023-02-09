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

namespace theme_luniversitenumerique\output;

use coding_exception;
use html_writer;
use tabobject;
use tabtree;
use custom_menu_item;
use custom_menu;
use block_contents;
use navigation_node;
use action_link;
use stdClass;
use moodle_url;
use preferences_groups;
use action_menu;
use help_icon;
use single_button;
use single_select;
use paging_bar;
use url_select;
use context_course;
use pix_icon;
use user_picture;
use context_user;
use action_menu_filler;
use action_menu_link_secondary;
use core_text;

defined('MOODLE_INTERNAL') || die;

/**
 * Renderers to align Moodle's HTML with that expected by Bootstrap
 *
 * @package    theme_luniversitenumerique
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class core_renderer extends \core_renderer {
    
    /**
     * Change the default generated course image for the course-default.svg image
     * 
     * @param int $id Id to use when generating the pattern
     * @return string datauri
     */
    public function get_generated_image_for_id($id = null) {
        global $CFG;
        return $CFG->wwwroot . "/theme/luniversitenumerique/pix/course-default.svg";
    }
    
    public function full_header() {
        global $CFG, $DB, $COURSE, $PAGE, $USER;

        require_once($CFG->libdir. '/filestorage/file_storage.php');
        require_once($CFG->dirroot. '/course/lib.php');
        $fs = get_file_storage();
        $context = context_course::instance($COURSE->id);
        $files = $fs->get_area_files($context->id, 'course', 'overviewfiles', false, 'filename', false);

        $admins = get_admins();
        $isadmin = in_array($USER->id, array_keys($admins));
        // Get user system role matching the theme breadcrumbrole setting.
        $contextsystem = \context_system::instance();
        $breadcrumbrole = get_config('theme_luniversitenumerique', 'breadcrumbrole');
        $userbreadcrumbrole = $DB->get_record("role_assignments", array("userid" => $USER->id, "contextid" => $contextsystem->id, "roleid" => $breadcrumbrole));

        if (count($files)) {
            $overviewfilesoptions = course_overviewfiles_options($COURSE->id);
            $acceptedtypes = $overviewfilesoptions['accepted_types'];
            if ($acceptedtypes !== '*') {
                // Filter only files with allowed extensions.
                require_once($CFG->libdir. '/filelib.php');
                foreach ($files as $key => $file) {
                    if (!file_extension_in_typegroup($file->get_filename(), $acceptedtypes)) {
                        unset($files[$key]);
                    }
                }
            }
            if (count($files) > $CFG->courseoverviewfileslimit) {
                // Return no more than $CFG->courseoverviewfileslimit files.
                $files = array_slice($files, 0, $CFG->courseoverviewfileslimit, true);
            }
        }

        $courseimage = "";
        foreach ($files as $file) {
            $isimage = $file->is_valid_image();
            if ($isimage) {
                $courseimage = file_encode_url("$CFG->wwwroot/pluginfile.php",
                    '/'. $file->get_contextid(). '/'. $file->get_component(). '/'.
                    $file->get_filearea(). $file->get_filepath(). $file->get_filename(), !$isimage);
            }
        }

        if ($this->page->include_region_main_settings_in_header_actions() &&
                !$this->page->blocks->is_block_present('settings')) {
            // Only include the region main settings if the page has requested it and it doesn't already have
            // the settings block on it. The region main settings are included in the settings block and
            // duplicating the content causes behat failures.
            $this->page->add_header_action(html_writer::div(
                $this->region_main_settings_menu(),
                'd-print-none',
                ['id' => 'region-main-settings-menu']
            ));
        }

        $header = new stdClass();
        $header->settingsmenu = $this->context_header_settings_menu();
        $header->contextheader = $this->context_header();
        $header->hasnavbar = empty($this->page->layout_options['nonavbar']);
        // Remove the navbar if the user musn't see it.
        if (!$isadmin && !isset($userbreadcrumbrole->id)) {
            $header->hasnavbar = false;
        }
        $header->navbar = $this->navbar();
        $header->pageheadingbutton = $this->page_heading_button();
        $header->courseheader = $this->course_header();
        $header->headeractions = $this->page->get_header_actions();
        if ($courseimage) {
            $tags = \core_tag_tag::get_item_tags('core', 'course', $COURSE->id);
            $tagnames = array_map(function($o) { return $o->name;}, $tags);
            if (in_array('punchy', $tagnames)) {
                $header->preimage = $CFG->wwwroot . "/theme/luniversitenumerique/pix/punchy/bandeau-punchy.svg";
                if (in_array('logo-noir', $tagnames)) {
                    $header->postimage = $CFG->wwwroot . "/theme/luniversitenumerique/pix/punchy/logo-punchy-noir.svg";
                } else {
                    $header->postimage = $CFG->wwwroot . "/theme/luniversitenumerique/pix/punchy/logo-punchy.svg";
                }
            }
            $header->backgroundimage = $courseimage;
        }
        $summary = $this->page->course->summary;
        if ($summary) {
            $header->summary = $summary;
        }

        return $this->render_from_template('theme_luniversitenumerique/full_header', $header);
    }

}
