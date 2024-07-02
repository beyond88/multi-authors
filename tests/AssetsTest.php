<?php

use MultiAuthors\Assets;

class Test_Assets extends WP_UnitTestCase {

    /**
     * @var Assets
     */
    private $assets;

    /**
     * Set up the test environment
     */
    public function setUp(): void {
        parent::setUp();
        $this->assets = new Assets();
    }

    /**
     * Test if styles are correctly registered and enqueued
     */
    public function test_register_assets() {
        // Enqueue styles
        $this->assets->register_assets();

        // Get registered styles
        $registered_styles = wp_styles();

        // Test if the frontend style is registered
        $this->assertTrue(isset($registered_styles->registered['frontend-style']));
        
        // Check if the handle points to the correct src
        $style = $registered_styles->registered['frontend-style'];
        $this->assertEquals(MULTI_AUTHORS_ASSETS . '/css/frontend.css', $style->src);

        // Check if the version is set correctly
        $expected_version = filemtime(MULTI_AUTHORS_PATH . '/assets/css/frontend.css');
        $this->assertEquals($expected_version, $style->ver);
    }

}
