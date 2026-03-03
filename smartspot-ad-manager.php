<?php
/**
 * Plugin Name: SmartSpot Ad Manager
 * Plugin URI: https://smartspotad.isaacauta.com
 * Description: The simplest way to manage ads on your WordPress site. Works perfectly with Elementor, Gutenberg, and all page builders.
 * Version: 1.0.0
 * Author: Isaac Shed
 * Text Domain: smartspot-ad-manager
 * Requires at least: 5.0
 * Requires PHP: 7.2
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if (!defined('ABSPATH')) exit;

define('THESIADM_VERSION', '1.0.0');
define('THESIADM_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('THESIADM_PLUGIN_URL', plugin_dir_url(__FILE__));
define('THESIADM_PLUGIN_BASENAME', plugin_basename(__FILE__));
define('THESIADM_PLUGIN_FILE', __FILE__);
define('THESIADM_PRO_URL', 'https://smartspotad.isaacauta.com/pro'); // Your Pro sales page

/**
 * Helper: Check if Pro version is active
 * Pro version will be a separate plugin that defines THESIADM_PRO_VERSION constant
 */
if (!function_exists('thesiadm_is_pro')) {
    function thesiadm_is_pro() {
        return defined('THESIADM_PRO_VERSION');
    }
}

/**
 * Helper: Check if Free
 */
if (!function_exists('thesiadm_is_free')) {
    function thesiadm_is_free() {
        return !thesiadm_is_pro();
    }
}

/**
 * Helper: Get upgrade URL
 */
if (!function_exists('thesiadm_get_upgrade_url')) {
    function thesiadm_get_upgrade_url() {
        return THESIADM_PRO_URL;
    }
}

/**
 * Main Plugin Class
 */
class THESIADM_Plugin {
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->init_hooks();
        $this->includes();
    }
    
    private function init_hooks() {
        add_action('init', array($this, 'register_ad_post_type'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_public_assets'));
        add_filter('plugin_action_links_' . THESIADM_PLUGIN_BASENAME, array($this, 'add_action_links'));
        
        // Add admin notices for free users
        if (!thesiadm_is_pro()) {
            add_action('admin_notices', array($this, 'upgrade_notices'));
        }
        
        // Register Elementor widget
        add_action('elementor/widgets/register', array($this, 'register_elementor_widget'));
        
        // Register Gutenberg block
        add_action('init', array($this, 'register_gutenberg_block'));
        
        register_activation_hook(THESIADM_PLUGIN_FILE, array($this, 'activate'));
    }
    
    private function includes() {
        require_once THESIADM_PLUGIN_DIR . 'includes/class-ad-metaboxes.php';
        require_once THESIADM_PLUGIN_DIR . 'includes/class-ad-display.php';
        require_once THESIADM_PLUGIN_DIR . 'includes/class-ad-functions.php';
        
        new THESIADM_Metaboxes();
        new THESIADM_Display();
    }
    
    /**
     * Register Elementor Widget
     */
    public function register_elementor_widget($widgets_manager) {
        // Check if Elementor is loaded
        if (!did_action('elementor/loaded')) {
            return;
        }
        
        require_once THESIADM_PLUGIN_DIR . 'includes/class-elementor-widget.php';
        $widgets_manager->register(new \THESIADM_Elementor_Widget());
    }
    
    /**
     * Register Gutenberg Block
     */
    public function register_gutenberg_block() {
        if (!function_exists('register_block_type')) {
            return;
        }
        
        wp_register_script(
            'thesiadm-block-js',
            THESIADM_PLUGIN_URL . 'assets/js/block.js',
            array('wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-i18n'),
            THESIADM_VERSION
        );
        
        register_block_type('thesiadm/ad-block', array(
            'editor_script' => 'thesiadm-block-js',
            'render_callback' => array($this, 'render_block')
        ));
    }
    
    /**
     * Render Gutenberg Block
     */
    public function render_block($attributes) {
        $position = isset($attributes['position']) ? $attributes['position'] : 'before-content';
        return thesiadm_get_ads($position);
    }
    
    /**
     * Register Ad Custom Post Type
     */
    public function register_ad_post_type() {
        // If post type is already registered (e.g. by Pro version), don't register it again
        if (post_type_exists('thesiadm_ad')) {
            return;
        }

        $labels = array(
            'name'                  => _x('Ads', 'Post Type General Name', 'smartspot-ad-manager'),
            'singular_name'         => _x('Ad', 'Post Type Singular Name', 'smartspot-ad-manager'),
            'menu_name'             => __('Ad Manager', 'smartspot-ad-manager'),
            'add_new'               => __('Add New Ad', 'smartspot-ad-manager'),
            'add_new_item'          => __('Add New Ad', 'smartspot-ad-manager'),
            'edit_item'             => __('Edit Ad', 'smartspot-ad-manager'),
            'new_item'              => __('New Ad', 'smartspot-ad-manager'),
            'view_item'             => __('View Ad', 'smartspot-ad-manager'),
            'search_items'          => __('Search Ads', 'smartspot-ad-manager'),
            'not_found'             => __('No ads found', 'smartspot-ad-manager'),
            'not_found_in_trash'    => __('No ads found in trash', 'smartspot-ad-manager'),
            'all_items'             => __('All Ads', 'smartspot-ad-manager'),
        );
        
        $args = array(
            'labels'                => $labels,
            'description'           => __('Manage advertisements on your website', 'smartspot-ad-manager'),
            'public'                => false,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_icon'             => 'dashicons-megaphone',
            'menu_position'         => 25,
            'capability_type'       => 'post',
            'hierarchical'          => false,
            'supports'              => array('title', 'thumbnail'),
            'has_archive'           => false,
            'rewrite'               => false,
            'query_var'             => false,
            'can_export'            => true,
            'show_in_rest'          => false,
        );
        
        register_post_type('thesiadm_ad', $args);
    }
    
    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets($hook) {
        global $post_type;
        
        if ('thesiadm_ad' !== $post_type) {
            return;
        }
        
        wp_enqueue_style(
            'thesiadm-admin-css',
            THESIADM_PLUGIN_URL . 'assets/css/admin-style.css',
            array(),
            THESIADM_VERSION
        );
        
        wp_enqueue_script(
            'thesiadm-admin-js',
            THESIADM_PLUGIN_URL . 'assets/js/admin-script.js',
            array('jquery'),
            THESIADM_VERSION,
            true
        );
        
        // Localize script
        wp_localize_script('thesiadm-admin-js', 'thesiadmAdmin', array(
            'confirmDelete' => __('Are you sure you want to delete this ad?', 'smartspot-ad-manager'),
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('thesiadm-admin-nonce')
        ));
    }
    
    /**
     * Enqueue public assets
     */
    public function enqueue_public_assets() {
        wp_enqueue_style(
            'thesiadm-public-css',
            THESIADM_PLUGIN_URL . 'assets/css/public-style.css',
            array(),
            THESIADM_VERSION
        );
        
        wp_enqueue_script(
            'thesiadm-public-js',
            THESIADM_PLUGIN_URL . 'assets/js/public-script.js',
            array('jquery'),
            THESIADM_VERSION,
            true
        );
    }
    
    /**
     * Add action links to plugins page
     */
    public function add_action_links($links) {
        $plugin_links = array(
            '<a href="' . admin_url('edit.php?post_type=thesiadm_ad') . '">' . __('Manage Ads', 'smartspot-ad-manager') . '</a>',
            '<a href="' . THESIADM_PRO_URL . '" style="color: #2271b1; font-weight: bold;" target="_blank">' . __('Go Pro', 'smartspot-ad-manager') . '</a>',
        );
        
        return array_merge($plugin_links, $links);
    }
    
    /**
     * Show upgrade notices
     */
    public function upgrade_notices() {
        $screen = get_current_screen();
        
        if ($screen->id === 'edit-thesiadm_ad' || $screen->id === 'thesiadm_ad') {
            ?>
            <div class="notice notice-info is-dismissible">
                <p>
                    <strong><?php _e('Unlock Powerful Features!', 'smartspot-ad-manager'); ?></strong> 
                    <?php _e('Upgrade to SmartSpot Ad Manager Pro to unlock unlimited ad positions, advanced device targeting, AdSense support, and priority support.', 'smartspot-ad-manager'); ?> 
                    <a href="<?php echo esc_url(THESIADM_PRO_URL); ?>" target="_blank" class="button button-primary" style="margin-left: 10px;"><?php _e('Upgrade Now', 'smartspot-ad-manager'); ?></a>
                </p>
            </div>
            <?php
        }
    }
    
    /**
     * Plugin activation
     */
    public function activate() {
        $this->register_ad_post_type();
        flush_rewrite_rules();
        
        if (!get_option('thesiadm_version')) {
            add_option('thesiadm_version', THESIADM_VERSION);
            add_option('thesiadm_activated_time', current_time('timestamp'));
        }
    }
}

/**
 * Initialize the plugin
 */
function thesiadm_plugin() {
    return THESIADM_Plugin::get_instance();
}

// Kick off the plugin
thesiadm_plugin();
