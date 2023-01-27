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

/**
 * Layout login file.
 *
 * @package   theme_luniversitenumerique
 * @copyright 2022 Pierre Duverneix
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$bodyattributes = $OUTPUT->body_attributes();

$templatecontext = [
    'sitename' => format_string($SITE->shortname, true, ['context' => context_course::instance(SITEID), "escape" => false]),
    'output' => $OUTPUT,
    'bodyattributes' => $bodyattributes,
    'logo-un' => $OUTPUT->image_url('logo-un', 'theme'),
    'logo-white' => $OUTPUT->image_url('logo-white', 'theme'),
    'logo-uved' => $OUTPUT->image_url('logos/logo-uved', 'theme'),
    'logo-uness' => $OUTPUT->image_url('logos/logo-uness', 'theme'),
    'logo-unit' => $OUTPUT->image_url('logos/logo-unit', 'theme'),
    'logo-iutenligne' => $OUTPUT->image_url('logos/logo-iutenligne', 'theme'),
    'logo-uoh' => $OUTPUT->image_url('logos/logo-uoh', 'theme'),
    'logo-aunege' => $OUTPUT->image_url('logos/logo-aunege', 'theme'),
    'year' => date("Y")
];

echo $OUTPUT->render_from_template('theme_luniversitenumerique/login', $templatecontext);
