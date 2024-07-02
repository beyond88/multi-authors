<?php

use MultiAuthors\Admin;

class Test_Admin extends WP_UnitTestCase {

    private $admin;

    public function setUp(): void {
        parent::setUp();
        $this->admin = new Admin();
    }

    public function test_add_contributors_metabox() {
        global $wp_meta_boxes;

        // Trigger add_meta_boxes action
        do_action('add_meta_boxes');

        // Check if metabox is added
        $this->assertTrue(isset($wp_meta_boxes['post']['side']['high']['ma_contributors']));
    }

    public function test_contributors_metabox_callback() {
        $post_id = $this->factory->post->create();
        $post = get_post($post_id);

        // Output buffering to capture the metabox HTML
        ob_start();
        $this->admin->contributors_metabox_callback($post);
        $output = ob_get_clean();

        // Check if nonce field is present
        $this->assertStringContainsString('ma_contributors_nonce', $output);

        // Check if user checkboxes are present
        $users = get_users();
        foreach ($users as $user) {
            $this->assertStringContainsString('value="' . $user->ID . '"', $output);
        }
    }

    public function test_save_contributors_metabox() {
        $post_id = $this->factory->post->create();
        $user_id = $this->factory->user->create(array('role' => 'author'));

        // Set up POST data with valid nonce and contributors
        $_POST['ma_contributors_nonce'] = wp_create_nonce('ma_save_contributors');
        $_POST['ma_contributors'] = array($user_id);

        // Simulate saving the post
        $this->admin->save_contributors_metabox($post_id);

        // Retrieve saved contributors meta data
        $contributors = get_post_meta($post_id, '_ma_contributors', true);

        // Check if contributors meta data is saved correctly
        $this->assertTrue(is_array($contributors), 'Contributors should be saved as an array.');
        $this->assertContains($user_id, $contributors, 'User ID should be in contributors array.');
    }

    public function test_save_contributors_metabox_invalid_nonce() {
        $post_id = $this->factory->post->create();
        $user_id = $this->factory->user->create(array('role' => 'author'));

        // Set up POST data with invalid nonce and contributors
        $_POST['ma_contributors_nonce'] = 'invalid_nonce';
        $_POST['ma_contributors'] = array($user_id);

        // Simulate saving the post
        $this->admin->save_contributors_metabox($post_id);

        // Check if contributors meta data is not saved
        $contributors = get_post_meta($post_id, '_ma_contributors', true);

        $this->assertFalse($contributors, 'Contributors should not be saved with invalid nonce.');
    }

    public function test_save_contributors_metabox_no_permission() {
        $post_id = $this->factory->post->create();
        $user_id = $this->factory->user->create(array('role' => 'author'));

        // Simulate an unauthorized user
        wp_set_current_user(0);

        // Set up POST data with valid nonce and contributors
        $_POST['ma_contributors_nonce'] = wp_create_nonce('ma_save_contributors');
        $_POST['ma_contributors'] = array($user_id);

        // Simulate saving the post
        $this->admin->save_contributors_metabox($post_id);

        // Check if contributors meta data is not saved
        $contributors = get_post_meta($post_id, '_ma_contributors', true);

        $this->assertFalse($contributors, 'Contributors should not be saved without permission.');
    }
}