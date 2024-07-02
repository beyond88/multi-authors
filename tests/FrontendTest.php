<?php

use MultiAuthors\Frontend;

class Test_Frontend extends WP_UnitTestCase {

    private $frontend;

    public function setUp(): void {
        parent::setUp();
        $this->frontend = new Frontend();
    }

    public function test_ma_display_contributors() {
        $post_id = $this->factory->post->create();
        $user_id = $this->factory->user->create(array('role' => 'author'));
    
        // Add contributor meta to the post
        update_post_meta($post_id, '_ma_contributors', array($user_id));
    
        // Set up the global $post and query variables to simulate a single post view
        $post = get_post($post_id);
        setup_postdata($post);
        $GLOBALS['wp_query'] = new WP_Query(array('p' => $post_id, 'post_type' => 'post', 'is_single' => true));
    
        // Simulate the post content filter
        $content = 'Original content';
        $filtered_content = $this->frontend->ma_display_contributors($content);
        wp_reset_postdata();
    
        // Check that the original content is included in the filtered content
        $this->assertStringContainsString('Original content', $filtered_content);
    
        // Check that the contributors box is included in the filtered content
        $this->assertStringContainsString('Contributors', $filtered_content);
        $this->assertStringContainsString(get_avatar($user_id, 32), $filtered_content);
        $this->assertStringContainsString(get_author_posts_url($user_id), $filtered_content);
        $this->assertStringContainsString(esc_html(get_userdata($user_id)->display_name), $filtered_content);
    }

    public function test_ma_modify_author_archive_query() {
        $author_id = $this->factory->user->create(array('role' => 'author'));
        $post_id = $this->factory->post->create();
        $post_id_2 = $this->factory->post->create();
    
        // Add contributor meta to the posts
        update_post_meta($post_id, '_ma_contributors', array($author_id));
        update_post_meta($post_id_2, '_ma_contributors', array($author_id));
    
        // Set up the global $wp_query to simulate an author archive query
        $query = new WP_Query(array('author' => $author_id, 'is_author' => true));
        $GLOBALS['wp_query'] = $query;
    
        // Call the method to modify the query
        $this->frontend->ma_modify_author_archive_query($query);
    
        // Check the modified query
        $meta_query = $query->get('meta_query');
        $this->assertIsArray($meta_query); // Ensure $meta_query is an array
        $this->assertCount(1, $meta_query);
        $this->assertEquals('_ma_contributors', $meta_query[0]['key']);
        $this->assertEquals('"' . $author_id . '"', $meta_query[0]['value']);
        $this->assertEquals('LIKE', $meta_query[0]['compare']);
    
        // Run the modified query and check the results
        $query->query(array('meta_query' => $meta_query)); // Make sure to set the query parameters correctly
        $posts = $query->get_posts();
        $this->assertCount(2, $posts);
        $this->assertEquals($post_id, $posts[0]->ID);
        $this->assertEquals($post_id_2, $posts[1]->ID);
    }
}



