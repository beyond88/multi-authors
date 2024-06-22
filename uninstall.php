<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

/**
 * MultiAuthors Uninstall
 *
 * Uninstalling MultiAuthors deletes user roles, tables, pages, meta data and options.
 *
 * @since   1.0.0
 *
 * @package MultiAuthors\Uninstaller
 */
class MultiAuthors_Uninstaller {
    /**
     * Constructor for the class MultiAuthors_Uninstaller
     *
     * @since 1.0.0
     */
    public function __construct() {

        $this->delete_options();

        wp_cache_flush();

    }

    /**
     * Delete MultiAuthors settings
     *
     * @since 1.0.0
     *
     * @return void
     */
    private function delete_options() {
    }
}

new MultiAuthors_Uninstaller();