<?php
/**
 * Plugin Name: SmartSpot Ad Manager
 * Plugin URI: https://smartspotad.isaacauta.com
 * Description: The simplest way to manage ads on your WordPress site. Works perfectly with Elementor, Gutenberg, and all page builders.
 * Version: 2.0.2
 * Author: Isaac Shed
 * Text Domain: smartspot-ad-manager
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.2
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if (!defined('ABSPATH')) exit;

define('THESIADM_VERSION', '2.0.2');
define('THESIADM_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('THESIADM_PLUGIN_URL', plugin_dir_url(__FILE__));
define('THESIADM_PLUGIN_BASENAME', plugin_basename(__FILE__));
define('THESIADM_PLUGIN_FILE', __FILE__);

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
        add_action('plugins_loaded', array($this, 'load_textdomain'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_public_assets'));
        add_filter('plugin_action_links_' . THESIADM_PLUGIN_BASENAME, array($this, 'add_action_links'));

        add_action('elementor/widgets/register', array($this, 'register_elementor_widget'));
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

    public function load_textdomain() {
        /*
         * load_plugin_textdomain() is called here for backwards-compatibility
         * with non-WordPress.org installs and pre-4.6 translation packs.
         * WordPress.org-hosted installs auto-load translations from the
         * language packs system regardless of this call.
         */
        // phpcs:ignore PluginCheck.CodeAnalysis.DiscouragedFunctions.load_plugin_textdomainFound
        load_plugin_textdomain('smartspot-ad-manager', false, dirname(THESIADM_PLUGIN_BASENAME) . '/languages');
    }

    public function register_elementor_widget($widgets_manager) {
        if (!did_action('elementor/loaded')) {
            return;
        }

        require_once THESIADM_PLUGIN_DIR . 'includes/elementor/class-thesiadm-elementor-widget.php';
        $widgets_manager->register(new \THESIADM_Elementor_Widget());
    }

    public function register_gutenberg_block() {
        if (!function_exists('register_block_type')) {
            return;
        }

        wp_register_script(
            'thesiadm-block-js',
            THESIADM_PLUGIN_URL . 'assets/js/block.js',
            array('wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-i18n'),
            THESIADM_VERSION,
            true
        );

        $positions = $this->get_position_options_for_block();

        wp_localize_script('thesiadm-block-js', 'thesiadmBlockData', array(
            'positions' => $positions,
            'selectAdLabel' => __('Select Ad Position', 'smartspot-ad-manager'),
            'adsLabel' => __('Or Select Specific Ad', 'smartspot-ad-manager'),
            'ads' => $this->get_ads_list_for_block(),
        ));

        register_block_type('thesiadm/ad-block', array(
            'editor_script' => 'thesiadm-block-js',
            'render_callback' => array($this, 'render_block'),
            'attributes' => array(
                'position' => array(
                    'type' => 'string',
                    'default' => 'before-content',
                ),
                'adId' => array(
                    'type' => 'number',
                    'default' => 0,
                ),
            ),
        ));
    }

    private function get_position_options_for_block() {
        return array(
            array('value' => 'before-content', 'label' => __('Before Content', 'smartspot-ad-manager')),
            array('value' => 'after-content', 'label' => __('After Content', 'smartspot-ad-manager')),
            array('value' => 'sidebar', 'label' => __('Sidebar', 'smartspot-ad-manager')),
            array('value' => 'header', 'label' => __('Header', 'smartspot-ad-manager')),
            array('value' => 'footer', 'label' => __('Footer', 'smartspot-ad-manager')),
            array('value' => 'custom-1', 'label' => __('Custom Position 1', 'smartspot-ad-manager')),
            array('value' => 'custom-2', 'label' => __('Custom Position 2', 'smartspot-ad-manager')),
        );
    }

    private function get_ads_list_for_block() {
        $ads = get_posts(array(
            'post_type' => 'thesiadm_ad',
            'posts_per_page' => -1,
            'post_status' => 'publish',
        ));

        $list = array(
            array('value' => 0, 'label' => __('-- Use Position Above --', 'smartspot-ad-manager')),
        );

        foreach ($ads as $ad) {
            $position = get_post_meta($ad->ID, '_thesiadm_ad_position', true);
            $list[] = array(
                'value' => $ad->ID,
                'label' => $ad->post_title . ' (' . ucwords(str_replace('-', ' ', $position)) . ')',
            );
        }

        return $list;
    }

    public function render_block($attributes) {
        $ad_id = isset($attributes['adId']) ? absint($attributes['adId']) : 0;
        $position = isset($attributes['position']) ? sanitize_text_field($attributes['position']) : 'before-content';

        if ($ad_id > 0) {
            $ad = get_post($ad_id);
            if ($ad && $ad->post_type === 'thesiadm_ad' && $ad->post_status === 'publish') {
                $display = new THESIADM_Display();
                return wp_kses($display->display_ad($ad), thesiadm_get_allowed_ad_html());
            }
        }

        return thesiadm_get_ads($position);
    }

    public function register_ad_post_type() {
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
            'show_in_rest'          => true,
        );

        register_post_type('thesiadm_ad', $args);
    }

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

        wp_localize_script('thesiadm-admin-js', 'thesiadmAdmin', array(
            'confirmDelete' => __('Are you sure you want to delete this ad?', 'smartspot-ad-manager'),
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('thesiadm-admin-nonce'),
        ));
    }

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

    public function add_action_links($links) {
        $plugin_links = array(
            '<a href="' . admin_url('edit.php?post_type=thesiadm_ad') . '">' . __('Manage Ads', 'smartspot-ad-manager') . '</a>',
            '<a href="' . admin_url('post-new.php?post_type=thesiadm_ad') . '">' . __('Add New Ad', 'smartspot-ad-manager') . '</a>',
        );

        return array_merge($plugin_links, $links);
    }

    public function activate() {
        $this->register_ad_post_type();
        flush_rewrite_rules();

        if (!get_option('thesiadm_version')) {
            add_option('thesiadm_version', THESIADM_VERSION);
            add_option('thesiadm_activated_time', current_time('timestamp'));
        } else {
            update_option('thesiadm_version', THESIADM_VERSION);
        }

        set_transient('thesiadm_v2_welcome', true, 30);
    }
}

function thesiadm_plugin() {
    return THESIADM_Plugin::get_instance();
}

thesiadm_plugin();
