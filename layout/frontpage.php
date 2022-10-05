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
 * Layout file.
 *
 * @package   theme_luniversitenumerique
 * @copyright 2022 Pierre Duverneix
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

user_preference_allow_ajax_update('drawer-open-nav', PARAM_ALPHA);
require_once($CFG->libdir . '/behat/lib.php');

if (isloggedin()) {
    $navdraweropen = (get_user_preferences('drawer-open-nav', 'true') == 'true');
} else {
    $navdraweropen = false;
}
$extraclasses = [];
if ($navdraweropen) {
    $extraclasses[] = 'drawer-open-left';
}

global $DB;
$sql = "SELECT id,name,idnumber FROM {course_categories} WHERE parent = 0 AND id > 1;";
$results = $DB->get_records_sql($sql);
$categories = array();
foreach ($results as $category) {
    if ($category->idnumber != "") {
        $categories[] = array(
            'name' => $category->name,
            'idnumber' => $category->idnumber,
            'link' => new moodle_url('/course/index.php', array('categoryid' => $category->id))
        );
    }
}

$bodyattributes = $OUTPUT->body_attributes($extraclasses);
$blockshtml = $OUTPUT->blocks('side-pre');
$hasblocks = strpos($blockshtml, 'data-block=') !== false;
$regionmainsettingsmenu = $OUTPUT->region_main_settings_menu();

if (isloggedin() && !behat_is_test_site()) {
    $navdraweropen = (get_user_preferences('drawer-open-nav', 'true') == 'true');
    $templatecontext = [
        'sitename' => format_string($SITE->shortname, true, array('context' => context_course::instance(SITEID))),
        'output' => $OUTPUT,
        'sidepreblocks' => $blockshtml,
        'hasblocks' => $hasblocks,
        'bodyattributes' => $bodyattributes,
        'navdraweropen' => $navdraweropen,
        'regionmainsettingsmenu' => $regionmainsettingsmenu,
        'hasregionmainsettingsmenu' => !empty($regionmainsettingsmenu),
        'isloggedin' => isloggedin(),
        'logo-white' => $OUTPUT->image_url('logo-white', 'theme'),
        'logo-un' => $OUTPUT->image_url('logo-un', 'theme'),
        'year' => date("Y")
    ];    
} else {
    $templatecontext = [
        'sitename' => format_string($SITE->shortname, true, array('context' => context_course::instance(SITEID))),
        'output' => $OUTPUT,
        'sidepreblocks' => $blockshtml,
        'hasblocks' => $hasblocks,
        'bodyattributes' => $bodyattributes,
        'navdraweropen' => $navdraweropen,
        'regionmainsettingsmenu' => $regionmainsettingsmenu,
        'hasregionmainsettingsmenu' => !empty($regionmainsettingsmenu),
        'isloggedin' => isloggedin(),
        'categories' => $categories,
        'logintoken' => \core\session\manager::get_login_token(),
        'logo-un' => $OUTPUT->image_url('logo-un', 'theme'),
        'logo-white' => $OUTPUT->image_url('logo-white', 'theme'),
        'brand-img' => $OUTPUT->image_url('templates/frontpage/brand', 'theme'),
        'year' => date("Y"),
        'promo-un' => $OUTPUT->image_url('templates/frontpage/promo-un', 'theme')
    ];
}

// LUniversiteNumerique navigation
theme_luniversitenumerique_extend_flat_navigation($PAGE->flatnav);

// Load the theme's settings
$settings = get_config('theme_luniversitenumerique');

$templatecontext['flatnavigation'] = $PAGE->flatnav;
if (isloggedin()) {
    echo $OUTPUT->render_from_template('theme_luniversitenumerique/frontpage-loggedin', $templatecontext);
    return;
} else {
    if ($settings->frontpage == 'training') {
        echo $OUTPUT->render_from_template('theme_luniversitenumerique/frontpage-training', $templatecontext);
        return;
    }
    echo $OUTPUT->render_from_template('theme_luniversitenumerique/frontpage', $templatecontext);
    return;
}
