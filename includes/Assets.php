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
        add_action('wp_enqueue_scripts', array($this, 'register_assets'));
        add_action('admin_enqueue_scripts', array($this, 'register_admin_assets'));
    }

    /**
     * All available scripts
     *
     * @since   1.0.0
     * @access  public
     * @param   none
     * @return  array
     */
    public function get_scripts() {
        return array();
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
        return array();
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
        
        $scripts = $this->get_scripts();
        $styles  = $this->get_styles();

        foreach ($scripts as $handle => $script) {
            $deps = isset($script['deps']) ? $script['deps'] : false;
            $type = isset($script['type']) ? $script['type'] : '';

            wp_enqueue_script($handle, $script['src'], $deps, $script['version'], true);
        }

        foreach ($styles as $handle => $style) {
            $deps = isset($style['deps']) ? $style['deps'] : false;
            $type = isset($script['type']) ? $script['type'] : '';

            wp_enqueue_style($handle, $style['src'], $deps, $style['version']);
        }

        wp_localize_script('multi-authors-script', 'multi_authors', array(
            'ajax_url'  => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('multi-authors-nonce'),
        ));
    }

    /**
     * All available scripts
     * 
     * @since   1.0.0
     * @access  public
     * @param   none
     * @return  array
     */
    public function get_admin_scripts() {
        return array();
    }

    /**
     * All available styles
     *
     * @since   1.0.0
     * @access  public
     * @param   none
     * @return  array
     */
    public function get_admin_styles() {
        return array();
    }

    /**
     * Register scripts and styles
     *
     * @since   1.0.0
     * @access  public
     * @param   none
     * @return  array
     */
    public function register_admin_assets($hook) {

        $scripts = $this->get_admin_scripts();
        $styles  = $this->get_admin_styles();

        foreach ($scripts as $handle => $script) {
            $deps = isset($script['deps']) ? $script['deps'] : false;
            $type = isset($script['type']) ? $script['type'] : '';
            wp_enqueue_script($handle, $script['src'], $deps, $script['version'], true);
        }

        foreach ($styles as $handle => $style) {
            $deps = isset($style['deps']) ? $style['deps'] : false;
            $type = isset($script['type']) ? $script['type'] : '';

            wp_enqueue_style($handle, $style['src'], $deps, $style['version']);
        }

        wp_localize_script('multi-authors-admin-script', 'multi_authors', array(
            'ajax_url'  => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('multi-authors-admin-nonce'),
        ));
    }
}
