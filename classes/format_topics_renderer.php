<?php

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . "/course/format/topics/renderer.php");

class theme_luniversitenumerique_format_topics_renderer extends format_topics_renderer {
    

    /**
     * Generate a summary of a section for display on the 'course index page'
     *
     * @param stdClass $section The course_section entry from DB
     * @param stdClass $course The course entry from DB
     * @param array    $mods (argument not used)
     * @return string HTML to output.
     */
    protected function section_summary($section, $course, $mods) {
        $classattr = 'section main section-summary clearfix';
        $linkclasses = '';

        // If section is hidden then display grey section link
        if (!$section->visible) {
            $classattr .= ' hidden';
            $linkclasses .= ' dimmed_text';
        } else if (course_get_format($course)->is_section_current($section)) {
            $classattr .= ' current';
        }

        $title = get_section_name($course, $section);
        $colorclass = ' color-section';

        $image = $this->output->image_url('jalon', 'theme');
        if (strpos(strtolower($title), 'classe in') === 0) {
            $colorclass = ' color-flipped';
            $image = $this->output->image_url('flipped', 'theme');
        }
        if (strpos(strtolower($title), 'devoir') === 0
             || strpos(strtolower($title), 'examen') === 0
             || strpos(strtolower($title), 'évaluation') === 0
             || strpos(strtolower($title), 'evaluation') === 0) {
            $colorclass = ' color-assign';
            $image = $this->output->image_url('assign', 'theme');
        }
        if (strpos(strtolower($title), 'document') === 0) {
            $colorclass = ' color-documents';
        }
        $classattr .= $colorclass;

        $o = '';
        $o .= html_writer::start_tag('li', array('id' => 'section-'.$section->section,
            'class' => $classattr, 'role'=>'region', 'aria-label'=> $title));

        $o .= html_writer::tag('div', '', array('class' => 'left side'));
        $o .= html_writer::tag('div', '', array('class' => 'right side'));
        $o .= html_writer::start_tag('div', array('class' => 'content row'));

        $o .= html_writer::start_tag('div', array('class' => 'col-12 col-md-8 col-lg-9'));
        if ($section->uservisible) {
            $title = html_writer::tag('a', $title,
                    array('href' => course_get_url($course, $section->section), 'class' => $linkclasses));
        }

        $o .= $this->output->heading($title, 3, 'section-title mb-4');
        $o .= $this->section_availability($section);
        
        if ($section->uservisible || $section->visible) {
            // Show summary if section is available or has availability restriction information.
            // Do not show summary if section is hidden but we still display it because of course setting
            // "Hidden sections are shown in collapsed form".
            $summary = $this->format_summary_text($section);
            if ($summary != "") {
                $o .= html_writer::start_tag('div', array('class' => 'summarytext mb-4'));
                $o .= '<a class="collapse-link show-summary" data-toggle="collapse" href="#jalon'.$section->section.'-desc" role="button" aria-expanded="false" aria-controls="jalon'.$section->section.'-desc">Voir le résumé</a>';
                $o .= html_writer::start_tag('div', array('id' => 'jalon'.$section->section.'-desc', 'class' => 'jalon-desc collapse')) . $summary . html_writer::end_tag('div');
                $o .= html_writer::end_tag('div');
            }
        }
                
        if ($section->uservisible || $section->visible) {
            $sectionurl = course_get_url($course, $section->section, array('navigation' => true));
            $o .= html_writer::start_tag('div', array('class' => 'mt-3 mb-3'));
            $o .= html_writer::link($sectionurl, 'Accéder au contenu <i class="fa fa-arrow-right pl-2 p-1"></i>', array('class' => 'btn-un mt-4 d-inline-flex')) . html_writer::end_tag('div');
        }

        $image = $this->output->image_url('section-default', 'theme');

        $o .= html_writer::end_tag('div');
        $o .= html_writer::start_tag('div', array(
            'class' => 'section-icon col-12 col-md-4 col-lg-3'
        ));

        $o .= html_writer::end_tag('div');
        $o.= $this->section_activity_summary($section, $course, null);
        $o .= html_writer::end_tag('div');
        $o .= html_writer::end_tag('li');

        return $o;
    }
    
    /**
     * Generate a summary of the activites in a section
     *
     * @param stdClass $section The course_section entry from DB
     * @param stdClass $course the course record from DB
     * @param array    $mods (argument not used)
     * @return string HTML to output.
     */
    protected function section_activity_summary($section, $course, $mods) {
        $modinfo = get_fast_modinfo($course);
        if (empty($modinfo->sections[$section->section])) {
            return '';
        }

        // Generate array with count of activities in this section:
        $sectionmods = array();
        $total = 0;
        $complete = 0;
        $cancomplete = isloggedin() && !isguestuser();
        $completioninfo = new completion_info($course);
        foreach ($modinfo->sections[$section->section] as $cmid) {
            $thismod = $modinfo->cms[$cmid];

            if ($thismod->uservisible) {
                if (isset($sectionmods[$thismod->modname])) {
                    $sectionmods[$thismod->modname]['name'] = $thismod->modplural;
                    $sectionmods[$thismod->modname]['count']++;
                } else {
                    $sectionmods[$thismod->modname]['name'] = $thismod->modfullname;
                    $sectionmods[$thismod->modname]['count'] = 1;
                }
                $sectionmods[$thismod->modname]['icon'] = $thismod->get_icon_url()->out();
                if ($cancomplete && $completioninfo->is_enabled($thismod) != COMPLETION_TRACKING_NONE) {
                    $total++;
                    $completiondata = $completioninfo->get_data($thismod, true);
                    if ($completiondata->completionstate == COMPLETION_COMPLETE ||
                            $completiondata->completionstate == COMPLETION_COMPLETE_PASS) {
                        $complete++;
                    }
                }
            }
        }

        if (empty($sectionmods)) {
            // No sections
            return '';
        }

        // Output section activities summary:
        $o = '';
        $o.= html_writer::start_tag('div', array('class' => 'col-12 mt-3 section-summary-activities'));
        foreach ($sectionmods as $mod) {
            $o.= html_writer::start_tag('span', array('class' => 'activity-count mr-3'));
            $o.= html_writer::empty_tag('img', array('src' => $mod['icon'], 'class' => 'icon')) . $mod['count'];
            $o.= html_writer::end_tag('span');
        }
        $o.= html_writer::end_tag('div');

        // Output section completion data
        if ($total > 0) {
            $a = new stdClass;
            $a->complete = $complete;
            $a->total = $total;
            $percentage = round($complete / $total * 100);

            $o.= html_writer::start_tag('div', array('class' => 'col-12 mt-3'));
            $o.= html_writer::start_tag('div', array('class' => 'progress'));
            $o.= html_writer::start_tag('div', array('class' => 'progress-bar', 
                                                        'role' => 'prorgessbar', 
                                                        'aria-valuenow' => $percentage, 
                                                        'aria-valuemin' => 0, 
                                                        'aria-valuemax' => 100,
                                                        'style' => 'width:'.$percentage.'%'));
            $o.= $percentage.'%' . html_writer::end_tag('div');
            $o.= html_writer::end_tag('div');
            $o.= html_writer::end_tag('div');
        }

        return $o;
    }
    
}