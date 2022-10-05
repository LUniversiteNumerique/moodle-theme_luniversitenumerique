<?php

require_once($CFG->dirroot . '/mod/hvp/renderer.php');

/**
 * Class theme_h5pmod_mod_hvp_renderer
 *
 * Extends the H5P renderer so that we are able to override the relevant
 * functions declared there
 */
class theme_luniversitenumerique_mod_hvp_renderer extends mod_hvp_renderer {
    /**
     * Add styles when an H5P is displayed.
     *
     * @param array $styles Styles that will be applied.
     * @param array $libraries Libraries that wil be shown.
     * @param string $embedType How the H5P is displayed.
     */
    public function hvp_alter_styles(&$styles, $libraries, $embedType) {
        global $CFG;

        $styles[] = (object) array(
            'path'    => $CFG->httpswwwroot . '/theme/luniversitenumerique/style/h5p.css',
            'version' => '?ver=0.0.1',
        );
    }
}
