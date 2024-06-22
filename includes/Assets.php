<?php
namespace MultiAuthors;

/**
 * Assets handlers class
 */
class Assets {

    /**
     * Class constructor
     * 
     * @since   1.0.0
     * @access  public
     * @param   none
     * @return  void
     */
    function __construct() {
        add_action( 'wp_enqueue_scripts', array( $this, 'register_assets' ) );
    }

    /**
     * All available styles
     *
     * @since   1.0.0
     * @access  public
     * @param   none
     * @return  array
     */
    public function get_styles() {
        return array(
            'frontend-style' => array(
                'src'     => MULTI_AUTHORS_ASSETS . '/css/frontend.css',
                'version' => filemtime(MULTI_AUTHORS_PATH . '/assets/css/frontend.css'),
            ),
        );
    }

    /**
     * Register scripts and styles
     *
     * @since   1.0.0
     * @access  public
     * @param   none
     * @return  array
     */
    public function register_assets() {

        $styles  = $this->get_styles();

        foreach ($styles as $handle => $style) {
            $deps = isset($style['deps']) ? $style['deps'] : false;
            $type = isset($script['type']) ? $script['type'] : '';

            wp_enqueue_style($handle, $style['src'], $deps, $style['version']);
        }
    }

}
