<?php
/**
 * Plugin Name: SmartSpot Ad Manager Pro
 * Plugin URI: https://smartspotad.isaacauta.com/pro
 * Description: SmartSpot Ad Manager Pro adds custom positions, device targeting, and code ads.
 * Version: 1.0.0
 * Author: Isaac Shed
 * Author URI: https://isaacauta.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: smartspot-ad-manager
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.2
 */

// Exit if accessed directly
if (! defined('ABSPATH')) {
    exit;
}

// Freemius integration
if ( ! function_exists( 'sam_fs' ) ) { 
    // Create a helper function for easy SDK access. 
    function sam_fs() { 
        global $sam_fs; 

        if ( ! isset( $sam_fs ) ) { 
            // Activate multisite network integration. 
            if ( ! defined( 'WP_FS__PRODUCT_21306_MULTISITE' ) ) { 
                define( 'WP_FS__PRODUCT_21306_MULTISITE', true ); 
            } 

            // Include Freemius SDK. 
            require_once dirname( __FILE__ ) . '/freemius/start.php'; 

            $sam_fs = fs_dynamic_init( array( 
                'id'                  => '21306', 
                'slug'                => 'smartspot-ad-manager', 
                'premium_slug'        => 'smartspot-ad-manager-pro', 
                'type'                => 'plugin', 
                'public_key'          => 'pk_4259af966f4a639119548d5efa454', 
                'is_premium'          => true, 
                'premium_suffix'      => 'Pro', 
                // If your plugin is a serviceware, set this option to false. 
                'has_premium_version' => true, 
                'has_addons'          => false, 
                'has_paid_plans'      => true, 
                'is_org_compliant'    => true, 
                // Automatically removed in the free version. If you're not using the 
                // auto-generated free version, delete this line before uploading to wp.org. 
                'wp_org_gatekeeper'   => 'OA7#BoRiBNqdf52FvzEf!!074aRLPs8fspif$7K1#4u4Csys1fQlCecVcUTOs2mcpeVHi#C2j9d09fOTvbC0HloPT7fFee5WdS3G', 
                'menu'                => array( 
                    'slug'           => 'edit.php?post_type=thesiadm_ad', 
                    'support'        => false, 
                ), 
            ) ); 
        } 

        return $sam_fs; 
    } 

    // Init Freemius. 
    sam_fs(); 
    // Signal that SDK was initiated. 
    do_action( 'sam_fs_loaded' ); 
} 

// Set basename for Freemius
if ( function_exists( 'sam_fs' ) ) { 
    sam_fs()->set_basename( true, __FILE__ ); 
}

// Define plugin constants (Pro-specific)
if (! defined('THESIADM_PRO_PLUGIN_VERSION')) {
    define('THESIADM_PRO_PLUGIN_VERSION', '1.0.0');
}
if (! defined('THESIADM_PRO_PLUGIN_DIR')) {
    define('THESIADM_PRO_PLUGIN_DIR', plugin_dir_path(__FILE__));
}
if (! defined('THESIADM_PRO_PLUGIN_URL')) {
    define('THESIADM_PRO_PLUGIN_URL', plugin_dir_url(__FILE__));
}
if (! defined('THESIADM_PRO_PLUGIN_BASENAME')) {
    define('THESIADM_PRO_PLUGIN_BASENAME', plugin_basename(__FILE__));
}
if (! defined('THESIADM_PRO_PLUGIN_FILE')) {
    define('THESIADM_PRO_PLUGIN_FILE', __FILE__);
}

// Signal that Pro is active
if (! defined('THESIADM_PRO_VERSION')) {
    define('THESIADM_PRO_VERSION', '1.0.0');
}

/**
 * Main Plugin Class (Pro Version)
 */
class THESIADM_Pro_Plugin {
    
    /**
     * Instance of this class
     */
    private static $instance = null;
    
    /**
     * Get instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        $this->init_hooks();
        $this->includes();
    }
    
    /**
     * Initialize hooks
     */
    private function init_hooks() {
        add_action('init', array($this, 'register_ad_post_type'));
        add_action('init', array($this, 'load_textdomain'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_public_assets'));
        
        // Add settings link on plugins page
        add_filter('plugin_action_links_' . THESIADM_PRO_PLUGIN_BASENAME, array($this, 'add_action_links'));
        
        // Activation and deactivation hooks
        register_activation_hook(THESIADM_PRO_PLUGIN_FILE, array($this, 'activate'));
        register_deactivation_hook(THESIADM_PRO_PLUGIN_FILE, array($this, 'deactivate'));
    }
    
    /**
     * Include required files
     */
    private function includes() {
        // Load Pro-specific metaboxes first
        require_once THESIADM_PRO_PLUGIN_DIR . 'includes/class-ad-metaboxes.php';
        
        // Only load display logic if not already loaded by Free plugin
        if (!class_exists('THESIADM_Display')) {
            require_once THESIADM_PRO_PLUGIN_DIR . 'includes/class-ad-display.php';
        }
        
        // Only load functions if not already loaded by Free plugin
        // Note: Functions are loaded but wrapped in function_exists checks inside the file
        require_once THESIADM_PRO_PLUGIN_DIR . 'includes/class-ad-functions.php';
        
        // Initialize classes
        // Use Pro Metaboxes (renamed to avoid conflict)
        if (class_exists('THESIADM_Pro_Metaboxes')) {
            new THESIADM_Pro_Metaboxes();
        }
        
        // Conditionally initialize Display class if not already done by Free plugin
        if (class_exists('THESIADM_Display') && !class_exists('THESIADM_Plugin')) {
             new THESIADM_Display();
        } elseif (class_exists('THESIADM_Display') && class_exists('THESIADM_Plugin')) {
            // Free plugin handles it
        } else {
            // Pro standalone
             if (class_exists('THESIADM_Display')) {
                 new THESIADM_Display();
             }
        }
    }
    
    /**
     * Load plugin textdomain for translations
     */
    public function load_textdomain() {
        load_plugin_textdomain('smartspot-ad-manager', false, dirname(THESIADM_PRO_PLUGIN_BASENAME) . '/languages');
    }
    
    /**
     * Register Ad Custom Post Type
     */
    public function register_ad_post_type() {
        // If post type already exists (from Free plugin), we don't need to re-register
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
            THESIADM_PRO_PLUGIN_URL . 'assets/css/admin-style.css',
            array(),
            THESIADM_PRO_PLUGIN_VERSION
        );
        
        wp_enqueue_script(
            'thesiadm-admin-js',
            THESIADM_PRO_PLUGIN_URL . 'assets/js/admin-script.js',
            array('jquery'),
            THESIADM_PRO_PLUGIN_VERSION,
            true
        );
        
        // Localize script for translations
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
            THESIADM_PRO_PLUGIN_URL . 'assets/css/public-style.css',
            array(),
            THESIADM_PRO_PLUGIN_VERSION
        );
        
        wp_enqueue_script(
            'thesiadm-public-js',
            THESIADM_PRO_PLUGIN_URL . 'assets/js/public-script.js',
            array('jquery'),
            THESIADM_PRO_PLUGIN_VERSION,
            true
        );
    }
    
    /**
     * Add action links to plugins page
     */
    public function add_action_links($links) {
        $plugin_links = array(
            '<a href="' . admin_url('edit.php?post_type=thesiadm_ad') . '">' . __('Manage Ads', 'smartspot-ad-manager') . '</a>',
            '<a href="https://smartspotad.isaacauta.com/docs" target="_blank">' . __('Documentation', 'smartspot-ad-manager') . '</a>',
        );
        
        return array_merge($plugin_links, $links);
    }
    
    /**
     * Plugin activation
     */
    public function activate() {
        // Register post type
        $this->register_ad_post_type();
        
        // Flush rewrite rules
        flush_rewrite_rules();
        
        // Set default options
        if (!get_option('thesiadm_version')) {
            add_option('thesiadm_version', THESIADM_PRO_PLUGIN_VERSION);
            add_option('thesiadm_activated_time', current_time('timestamp'));
        }
    }
    
    /**
     * Plugin deactivation
     */
    public function deactivate() {
        // Flush rewrite rules
        flush_rewrite_rules();
    }
}

/**
 * Initialize the plugin
 */
function thesiadm_pro_plugin() {
    return THESIADM_Pro_Plugin::get_instance();
}

/**
 * Helper: Check if Pro
 */
if (! function_exists('thesiadm_is_pro')) {
    function thesiadm_is_pro() {
        if (! function_exists('sam_fs')) {
            return false;
        }

        return sam_fs()->can_use_premium_code();
    }
}

/**
 * Helper: Check if Free
 */
if (! function_exists('thesiadm_is_free')) {
    function thesiadm_is_free() {
        if (! function_exists('sam_fs')) {
            return true;
        }

        return sam_fs()->is_free_plan();
    }
}

/**
 * Helper: Get upgrade URL
 */
if (! function_exists('thesiadm_get_upgrade_url')) {
    function thesiadm_get_upgrade_url() {
        if (! function_exists('sam_fs')) {
            return 'https://smartspotad.isaacauta.com/pro';
        }

        return sam_fs()->get_upgrade_url();
    }
}

// Kick off the plugin
thesiadm_pro_plugin();
