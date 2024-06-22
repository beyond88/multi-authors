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
                $content .= '<ul>';
    
                foreach ($contributors as $contributor_id) {
                    $user_info = get_userdata($contributor_id);
                    $avatar = get_avatar($contributor_id, 32);
                    $author_link = get_author_posts_url($contributor_id);
                    $content .= '<li>';
                    $content .= '<a href="' . esc_url($author_link) . '">';
                    $content .= $avatar;
                    $content .= esc_html($user_info->display_name);
                    $content .= '</a>';
                    $content .= '</li>';
                }
    
                $content .= '</ul>';
                $content .= '</div>';
            }
        }
    
        return $content;
    }
}