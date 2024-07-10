<?php

use MultiAuthors\Admin;

class TestAdmin extends WP_UnitTestCase {

    /**
     * @var MultiAuthors\Admin
     */
    private $admin;

    public function setUp(): void {
        parent::setUp();
        $this->admin = new Admin();
    }

    public function tearDown(): void {
        parent::tearDown();
    }

    /**
     * Test that the add_contributors_metabox method adds a meta box to the post editor
     */
    public function test_add_contributors_metabox() {

        // Mock the add_meta_box function
        $mock = $this->createMock( 'WP_UnitTest_MockObject' );
        $mock->expects($this->once())
            ->method('__call')
            ->with(
                'add_meta_box',
                array(
                    'ma_contributors',
                    'Contributors',
                    array( $this->admin, 'contributors_metabox_callback' ),
                    'post',
                    'side',
                    'high',
                )
            );

        $this->replaceFunction( 'add_meta_box', array( $mock, '__call' ) );

        $this->admin->add_contributors_metabox();
    }

    /**
     * Test that the contributors_metabox_callback method displays a list of users with checkboxes
     */
    public function test_contributors_metabox_callback() {

        $user_1 = new WP_User( 1 );
        $user_1->display_name = 'John Doe';

        $user_2 = new WP_User( 2 );
        $user_2->display_name = 'Jane Doe';

        $users = array( $user_1, $user_2 );

        $post = new WP_Post(array(
            'ID' => 123,
        ));

        ob_start();
        $this->admin->contributors_metabox_callback( $post );
        $output = ob_get_clean();

        $expected_output = '';
        $expected_output .= wp_nonce_field( 'ma_save_contributors', 'ma_contributors_nonce', false );

        foreach ( $users as $user ) {
            $checked = '';
            $expected_output .= "<label>";
            $expected_output .= "<input type=\"checkbox\" name=\"ma_contributors[]\" value=\"" . esc_attr( $user->ID ) . "\" $checked>";
            $expected_output .= esc_html( $user->display_name );
            $expected_output .= "</label><br>";
        }

        $this->assertEquals( $expected_output, $output );
    }

    /**
     * Test that the save_contributors_metabox method saves the selected contributors
     */
    public function test_save_contributors_metabox() {

        $post_id = 123;

        // Set up mock data
        $_POST['ma_contributors_nonce'] = wp_create_nonce( 'ma_save_contributors' );
        $_POST['ma_contributors'] = array( 1, 3 );

        // Mock current_user_can
        $this->expects($this->once())
            ->method('current_user_can')
            ->with('edit_post', $post_id)
            ->willReturn(true);

        $this->admin->save_contributors_metabox( $post_id );

        $expected_contributors = array( 1, 3 );
        $actual_contributors = get_post_meta( $post_id, '_ma_contributors' );

        $this->assertEquals( $expected_contributors, $actual_contributors );

        // Cleanup
        unset( $_POST['ma_contributors_nonce'] );
        unset( $_POST['ma_contributors'] );
    }
}
