<?php

use MultiAuthors\Installer;

class Test_Installer extends WP_UnitTestCase {

    /**
     * @var Installer
     */
    private $installer;

    /**
     * Set up the test environment
     */
    public function setUp(): void {
        parent::setUp();
        $this->installer = new Installer();
    }

    /**
     * Test if the run method correctly calls add_version
     */
    public function test_run() {
        // Run the installer
        $this->installer->run();

        // Check if the multi_authors_installed option is set
        $installed = get_option('multi_authors_installed');
        $this->assertNotFalse($installed, 'The option multi_authors_installed should be set.');

        // Check if the multi_authors_version option is set correctly
        $version = get_option('multi_authors_version');
        $this->assertEquals(MULTI_AUTHORS_VERSION, $version, 'The option multi_authors_version should be set to the correct version.');
    }

    /**
     * Test if add_version correctly sets options
     */
    public function test_add_version() {
        // Delete options to start fresh
        delete_option('multi_authors_installed');
        delete_option('multi_authors_version');

        // Add version
        $this->installer->add_version();

        // Check if the multi_authors_installed option is set
        $installed = get_option('multi_authors_installed');
        $this->assertNotFalse($installed, 'The option multi_authors_installed should be set.');

        // Check if the multi_authors_version option is set correctly
        $version = get_option('multi_authors_version');
        $this->assertEquals(MULTI_AUTHORS_VERSION, $version, 'The option multi_authors_version should be set to the correct version.');
    }

    /**
     * Test if add_version does not overwrite existing installation time
     */
    public function test_add_version_does_not_overwrite_existing_installation_time() {
        // Set an initial installation time
        $initial_time = time() - 10000; // Set to 10000 seconds ago
        update_option('multi_authors_installed', $initial_time);

        // Add version
        $this->installer->add_version();

        // Check if the multi_authors_installed option is not overwritten
        $installed = get_option('multi_authors_installed');
        $this->assertEquals($initial_time, $installed, 'The option multi_authors_installed should not be overwritten.');
    }
}
