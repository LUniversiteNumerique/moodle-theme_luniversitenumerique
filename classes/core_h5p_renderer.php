<?php

require_once($CFG->dirroot . '/h5p/classes/output/renderer.php');

class theme_luniversitenumerique_core_h5p_renderer extends \core_h5p\output\renderer {
    /**
     * Add styles when an H5P is displayed.
     *
     * @param array $styles Styles that will be applied.
     * @param array $libraries Libraries that wil be shown.
     * @param string $embedType How the H5P is displayed.
     */
    public function h5p_alter_styles(&$styles, $libraries, $embedType) {
        global $CFG;

        $styles[] = (object) array(
            'path'    => $CFG->httpswwwroot . '/theme/luniversitenumerique/style/h5p.css',
            'version' => '?ver=0.0.1',
        );
    }
}
