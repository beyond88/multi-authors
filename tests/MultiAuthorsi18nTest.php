<?php

use MultiAuthors\MultiAuthorsi18n;
use Brain\Monkey;
use Brain\Monkey\Functions;

class Test_MultiAuthorsi18n extends \WP_UnitTestCase {

    /**
     * @var MultiAuthorsi18n
     */
    private $i18n;

    /**
     * Set up the test environment
     */
    public function setUp(): void {
        parent::setUp();
        Monkey\setUp();
        $this->i18n = new MultiAuthorsi18n();
    }

    /**
     * Tear down the test environment
     */
    public function tearDown(): void {
        Monkey\tearDown();
        parent::tearDown();
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
        // Mock the load_plugin_textdomain function
        $textdomain = 'multi-authors';
        $mofile = 'languages';

        Functions\expect('load_plugin_textdomain')
            ->once()
            ->with($textdomain, false, $mofile)
            ->andReturn(true);

        // Call the method
        $this->i18n->load_plugin_textdomain();
    }
}
