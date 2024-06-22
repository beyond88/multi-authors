<?php 
namespace MultiAuthors;

/**
 * The admin class for handling contributors metabox
 */
class Admin {

    /**
     * Initialize the class
     * 
     * @since   1.0.0
     */
    public function __construct() {
        add_action( 'add_meta_boxes', array( $this, 'add_contributors_metabox' ) );
        add_action( 'save_post', array( $this, 'save_contributors_metabox' ) );
    }

    /**
     * Add contributors metabox to post editor
     * 
     * @since   1.0.0
     */
    public function add_contributors_metabox() {
        add_meta_box(
            'ma_contributors',
            'Contributors',
            array( $this, 'contributors_metabox_callback' ),
            'post',
            'side',
            'high'
        );
    }

    /**
     * Callback function to display contributors metabox content
     * 
     * @since   1.0.0
     * 
     * @param   WP_Post $post The post object
     */
    public function contributors_metabox_callback( $post ) {
        // Add nonce field for security
        wp_nonce_field( 'ma_save_contributors', 'ma_contributors_nonce' );

        // Get the list of all authors
        $users = get_users();

        // Get current contributors
        $current_contributors = get_post_meta( $post->ID, '_ma_contributors', true );

        foreach ( $users as $user ) {
            $checked = is_array( $current_contributors ) && in_array( $user->ID, $current_contributors ) ? 'checked' : '';
            ?>
            <label>
                <input type="checkbox" name="ma_contributors[]" value="<?php echo esc_attr( $user->ID ); ?>" <?php echo $checked; ?>>
                <?php echo esc_html( $user->display_name ); ?>
            </label><br>
            <?php
        }
    }

    /**
     * Save contributors data when post is saved
     * 
     * @since   1.0.0
     * 
     * @param   int $post_id The post ID
     */
    public function save_contributors_metabox( $post_id ) {
        // Check nonce
        if ( ! isset( $_POST['ma_contributors_nonce'] ) || ! wp_verify_nonce( $_POST['ma_contributors_nonce'], 'ma_save_contributors' ) ) {
            return;
        }

        // Check autosave
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        // Check permissions
        if ( isset( $_POST['post_type'] ) && 'post' == $_POST['post_type'] ) {
            if ( ! current_user_can( 'edit_post', $post_id ) ) {
                return;
            }
        }

        // Save contributors
        $contributors = isset( $_POST['ma_contributors'] ) ? array_map( 'sanitize_text_field', $_POST['ma_contributors'] ) : array();
        update_post_meta( $post_id, '_ma_contributors', $contributors );
    }
}
