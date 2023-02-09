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
 * Settings file.
 *
 * @package   theme_luniversitenumerique
 * @copyright 2022 Pierre Duverneix
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    $settings = new theme_boost_admin_settingspage_tabs('themesettingluniversitenumerique', get_string('configtitle', 'theme_luniversitenumerique'));
    $page = new admin_settingpage('theme_luniversitenumerique_general', get_string('generalsettings', 'theme_luniversitenumerique'));

    // We use an empty default value because the default colour should come from the preset.
    $name = 'theme_luniversitenumerique/brandcolor';
    $title = get_string('brandcolor', 'theme_luniversitenumerique');
    $description = get_string('brandcolor_desc', 'theme_luniversitenumerique');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Must add the page after definiting all the settings!
    $settings->add($page);

    // Texts settings.
    $page = new admin_settingpage('theme_luniversitenumerique_texts', get_string('textssettings', 'theme_luniversitenumerique'));

    // Advanced settings.
    $page = new admin_settingpage('theme_luniversitenumerique_advanced', get_string('advancedsettings', 'theme_luniversitenumerique'));

    // Raw SCSS to include before the content.
    $setting = new admin_setting_configtextarea('theme_luniversitenumerique/scsspre',
        get_string('rawscsspre', 'theme_luniversitenumerique'), get_string('rawscsspre_desc', 'theme_luniversitenumerique'), '', PARAM_RAW);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Raw SCSS to include after the content.
    $setting = new admin_setting_configtextarea('theme_luniversitenumerique/scss', get_string('rawscss', 'theme_luniversitenumerique'),
        get_string('rawscss_desc', 'theme_luniversitenumerique'), '', PARAM_RAW);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Theme frontpage layout option.
    $choices = array();
    $choices['default'] = get_string('settings:frontpage_default', 'theme_luniversitenumerique');
    $choices['training'] = get_string('settings:frontpage_training', 'theme_luniversitenumerique');
    $setting = new admin_setting_configselect(
        'theme_luniversitenumerique/frontpage', 
        get_string('settings:frontpage', 'theme_luniversitenumerique'),
        get_string('settings:frontpage_desc', 'theme_luniversitenumerique'), 'default', $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // System role to display breadcrumbs.
    $contextsystem = \context_system::instance();
    $systemroles = get_roles_for_contextlevels($contextsystem->contextlevel);
    $insql = "IN (" . implode(',', array_keys($systemroles)) . ")";
    $sql = "SELECT id, shortname FROM {role} WHERE id IN (SELECT roleid FROM {role_context_levels} WHERE id $insql)";
    $roles = $DB->get_records_sql($sql);
    $rolesarray = [];
    foreach ($roles as $role) {
        $rolesarray[$role->id] = $role->shortname;
    }
    $setting = new admin_setting_configselect(
        'theme_luniversitenumerique/breadcrumbrole', 
        get_string('settings:breadcrumbrole', 'theme_luniversitenumerique'),
        get_string('settings:breadcrumbrole_desc', 'theme_luniversitenumerique'), 'default', $rolesarray);
    $page->add($setting);

    $settings->add($page);
}
