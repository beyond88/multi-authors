<?php
use MultiAuthors\MultiAuthorsi18n;

class Test_MultiAuthorsi18n extends WP_UnitTestCase {

    /**
     * @var MultiAuthorsi18n
     */
    private $i18n;

    /**
     * Set up the test environment
     */
    public function setUp(): void {
        parent::setUp();
        $this->i18n = new MultiAuthorsi18n();
    }

    /**
     * Test if the constructor correctly hooks load_plugin_textdomain
     */
    public function test_constructor() {
        $this->assertEquals(10, has_action('plugins_loaded', array($this->i18n, 'load_plugin_textdomain')), 'The load_plugin_textdomain method should be hooked to plugins_loaded action with priority 10.');
    }

    /**
     * Test if load_plugin_textdomain is called
     */
    public function test_load_plugin_textdomain() {
        // Temporarily replace the load_plugin_textdomain function
        $original_load_plugin_textdomain = 'load_plugin_textdomain';
        $mocked_textdomain = null;
        $mocked_mofile = null;

        add_filter('load_textdomain', function($domain, $mofile) use (&$mocked_textdomain, &$mocked_mofile) {
            $mocked_textdomain = $domain;
            $mocked_mofile = $mofile;
            return true;
        }, 10, 2);

        // Call the method
        $this->i18n->load_plugin_textdomain();

        // Verify the parameters
        $this->assertEquals('multi-authors', $mocked_textdomain, 'The load_textdomain function should be called with the correct textdomain.');
        $this->assertStringContainsString('languages', $mocked_mofile, 'The load_textdomain function should be called with the correct language directory.');

        // Restore the original load_plugin_textdomain function
        remove_filter('load_textdomain', 10);
    }
}