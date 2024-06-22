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
                        $biography = get_user_meta($contributor_id, 'description', true);
    
                        $content .= '<div class="contributor">';
                        $content .= '<div class="contributor-avatar">';
                        $content .= $avatar;
                        $content .= '</div>';
                        $content .= '<div class="contributor-details">';
                        $content .= '<a href="' . esc_url($author_link) . '">';
                        $content .= esc_html($user_info->display_name);
                        $content .= '</a>';

                        if (!empty($biography)) {
                            $content .= '<p>' . esc_html($biography) . '</p>';
                        }

                        $content .= '</div>';
                        $content .= '</div>';
                    }
                }
    
                $content .= '</div>'; // Close contributors-container
                $content .= '</div>'; // Close ma-contributors-box
            }
        }
    
        return $content;
    }
    
}