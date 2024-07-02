<?php
/**
 * Plugin Name: MultiAuthors
 * Description: Enables multiple author attribution per post with a dedicated contributors box displaying their names, Gravatars, and links to author pages.
 * Plugin URI: https://github.com/beyond88/multi-authors
 * Author: Mohiuddin Abdul Kader
 * Author URI: https://github.com/beyond88/
 * Version: 1.0.0
 * License: GPL2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       multi-authors
 * Domain Path:       /languages
 * Requires PHP:      5.6
 * Requires at least: 4.4
 * Tested up to:      6.5.2
 * @package MultiAuthors
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html 
 */

 if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';

/**
 * The main plugin class
 */
final class MultiAuthors {

    /**
     * Plugin version
     *
     * @var string
     */
    const version = '1.0.0';

    /**
     * Class constructor
     */
    private function __construct() {
        $this->define_constants();

        register_activation_hook( __FILE__, array( $this, 'activate' ) );
        add_action( 'plugins_loaded', array( $this, 'init_plugin') );
    }

    /**
     * Initializes a singleton instance
     *
     * @return \MultiAuthors
     */
    public static function init() {
        static $instance = false;

        if (!$instance) {
            $instance = new self();
        }

        return $instance;
    }

    /**
     * Define the required plugin constants
     *
     * @return void
     */
    public function define_constants() {
        define('MULTI_AUTHORS_VERSION', self::version);
        define('MULTI_AUTHORS_FILE', __FILE__);
        define('MULTI_AUTHORS_PATH', __DIR__);
        define('MULTI_AUTHORS_URL', plugins_url('', MULTI_AUTHORS_FILE));
        define('MULTI_AUTHORS_ASSETS', MULTI_AUTHORS_URL . '/assets');
        define('MULTI_AUTHORS_BASENAME', plugin_basename(__FILE__));
        define('MULTI_AUTHORS_PLUGIN_NAME', 'MultiAuthors');
        define('MULTI_AUTHORS_MINIMUM_PHP_VERSION', '5.6.0');
        define('MULTI_AUTHORS_MINIMUM_WP_VERSION', '4.4');
    }

    /**
     * Initialize the plugin
     *
     * @return void
     */
    public function init_plugin() {

        new MultiAuthors\Assets();
        new MultiAuthors\MultiAuthorsi18n();

        if (is_admin()) {
            new MultiAuthors\Admin();
        } else {
            new MultiAuthors\Frontend();
        }
    }

    /**
     * Do stuff upon plugin activation
     *
     * @return void
     */
    public function activate() {
        $installer = new MultiAuthors\Installer();
        $installer->run();
    }
}

/**
 * Initializes the main plugin
 */
function multi_authors() {
    return MultiAuthors::init();
}

// kick-off the plugin
multi_authors();

function ma_modify_author_archive_query($query) {
    if (is_author() && $query->is_main_query()) {
        $author_id = get_query_var('author');
        
        // Get posts where this author ID is in _ma_contributors meta field
        $meta_query = array(
            array(
                'key'     => '_ma_contributors',
                'value'   => '"' . $author_id . '"',
                'compare' => 'LIKE',
            ),
        );

        $query->set('meta_query', $meta_query);
    }
}

add_action('pre_get_posts', 'ma_modify_author_archive_query');
