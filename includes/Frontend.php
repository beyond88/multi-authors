<?php
namespace MultiAuthors;

/**
 * Frontend handler class for displaying contributors
 * 
 * @since    1.0.0
 */
class Frontend {

    /**
     * Initialize the class
     *
     * @since    1.0.0
     */
    public function __construct() {
        add_filter('the_content', array( $this, 'ma_display_contributors' ) );
        add_action( 'pre_get_posts', array( $this, 'ma_modify_author_archive_query' ) );

    }

    /**
     * Display contributors box at the end of single post content
     *
     * @since    1.0.0
     *
     * @param    string $content The post content
     * @return   string Modified content with contributors box
     */
    public function ma_display_contributors($content) {
        if (is_single() && is_main_query()) {
            global $post;
            $contributors = get_post_meta($post->ID, '_ma_contributors', true);
    
            if (!empty($contributors)) {
                $content .= '<div class="ma-contributors-box">';
                $content .= '<h3>'.__('Contributors', 'multi-authors').'</h3>';
                $content .= '<div class="contributors-container">'; // Use a div container for flex layout
    
                foreach ($contributors as $contributor_id) {
                    $user_info = get_userdata($contributor_id);
                    if ($user_info) {
                        $avatar = get_avatar($contributor_id, 32);
                        $author_link = get_author_posts_url($contributor_id);
    
                        // Get Biographical Info
                        $biography = get_the_author_meta('description', $contributor_id);
    
                        $content .= '<div class="contributor">';
                        $content .= '<div class="contributor-avatar">';
                        $content .= $avatar;
                        $content .= '</div>';
                        $content .= '<div class="contributor-details">';
                        $content .= '<a href="' . esc_url($author_link) . '">';
                        $content .= esc_html($user_info->display_name);
                        $content .= '</a>';
                        
                        // Display Biographical Info
                        if (!empty($biography)) {
                            $content .= '<p>' . esc_html($biography) . '</p>';
                        }
                        
                        $content .= '</div>'; // Close .contributor-details
                        $content .= '</div>'; // Close .contributor
                    }
                }
    
                $content .= '</div>'; // Close .contributors-container
                $content .= '</div>'; // Close .ma-contributors-box
            }
        }
    
        return $content;
    }    

    public function ma_modify_author_archive_query($query) {
        if ( is_author() && $query->is_main_query() ) {
            $author_id = get_query_var( 'author' );
    
            // Get posts where this author ID is in _ma_contributors meta field
            $meta_query = array(
                array(
                    'key'     => '_ma_contributors',
                    'value'   => '"' . $author_id . '"',
                    'compare' => 'LIKE',
                ),
            );
    
            $query->set( 'meta_query', $meta_query );
        }
    }
    
    
    
}