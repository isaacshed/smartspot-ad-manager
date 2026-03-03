<?php
/**
 * Ad Functions
 * Global functions and shortcodes for displaying ads
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('thesiadm_get_allowed_ad_html')) {
    function thesiadm_get_allowed_ad_html() {
        $allowed_html = wp_kses_allowed_html('post');
        $allowed_html['script'] = array(
            'src' => true,
            'type' => true,
            'async' => true,
            'defer' => true,
            'crossorigin' => true,
        );
        $allowed_html['iframe'] = array(
            'src' => true,
            'width' => true,
            'height' => true,
            'frameborder' => true,
            'allowfullscreen' => true,
            'style' => true,
            'class' => true,
        );
        $allowed_html['ins'] = array(
            'class' => true,
            'style' => true,
            'data-ad-client' => true,
            'data-ad-slot' => true,
            'data-ad-format' => true,
            'data-full-width-responsive' => true,
        );

        return $allowed_html;
    }
}

/**
 * Display ads for a specific position
 * 
 * @param string $position The ad position slug (header, before-content, after-content, etc.)
 * @return void
 */
if (!function_exists('thesiadm_display_ads')) {
    function thesiadm_display_ads($position = 'before-content') {
        $display = new THESIADM_Display();
        echo wp_kses($display->render_ads($position), thesiadm_get_allowed_ad_html());
    }
}

/**
 * Get ads for a specific position (returns HTML)
 * 
 * @param string $position The ad position slug
 * @return string HTML output
 */
if (!function_exists('thesiadm_get_ads')) {
    function thesiadm_get_ads($position = 'before-content') {
        $display = new THESIADM_Display();
        return $display->render_ads($position);
    }
}

/**
 * Check if ads exist for current page and position
 * 
 * @param string $position The ad position slug
 * @return bool
 */
if (!function_exists('thesiadm_has_ads')) {
    function thesiadm_has_ads($position = 'before-content') {
        $display = new THESIADM_Display();
        $ads = $display->get_ads_for_position($position);
        return !empty($ads);
    }
}

/**
 * Shortcode for displaying ads
 * Usage: [thesiadm_ads position="header"]
 * 
 * @param array $atts Shortcode attributes
 * @return string HTML output
 */
if (!function_exists('thesiadm_ads_shortcode')) {
    function thesiadm_ads_shortcode($atts) {
        $atts = shortcode_atts(array(
            'position' => 'before-content'
        ), $atts);
        
        return thesiadm_get_ads($atts['position']);
    }
}

// Only add shortcodes if they are not already added (though add_shortcode overwrites, checking function_exists is safer for logic flow)
if (!shortcode_exists('thesiadm_ads')) {
    add_shortcode('thesiadm_ads', 'thesiadm_ads_shortcode');
}
if (!shortcode_exists('smartspot_ad')) {
    add_shortcode('smartspot_ad', 'thesiadm_ads_shortcode');
}
if (!shortcode_exists('simple_ads')) {
    add_shortcode('simple_ads', 'thesiadm_ads_shortcode');
}

/**
 * Widget for displaying ads
 * Only define if not already defined (by Free plugin)
 */
if (!class_exists('THESIADM_Widget')) {
    class THESIADM_Widget extends WP_Widget {
        
        public function __construct() {
            parent::__construct(
                'thesiadm_widget',
                esc_html__('SmartSpot Ad Manager', 'smartspot-ad-manager'),
                array(
                    'description' => esc_html__('Display ads from SmartSpot Ad Manager', 'smartspot-ad-manager')
                )
            );
        }
        
        public function widget($args, $instance) {
            echo wp_kses_post($args['before_widget']);
            
            if (!empty($instance['title'])) {
                echo wp_kses_post($args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title']);
            }
            
            $position = !empty($instance['position']) ? $instance['position'] : 'sidebar';
            thesiadm_display_ads($position);
            
            echo wp_kses_post($args['after_widget']);
        }
        
        public function form($instance) {
            $title = !empty($instance['title']) ? $instance['title'] : '';
            $position = !empty($instance['position']) ? $instance['position'] : 'sidebar';
            ?>
            <p>
                <label for="<?php echo esc_attr($this->get_field_id('title')); ?>">
                    <?php esc_html_e('Title:', 'smartspot-ad-manager'); ?>
                </label>
                <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" 
                       name="<?php echo esc_attr($this->get_field_name('title')); ?>" 
                       type="text" value="<?php echo esc_attr($title); ?>">
            </p>
            <p>
                <label for="<?php echo esc_attr($this->get_field_id('position')); ?>">
                    <?php esc_html_e('Ad Position:', 'smartspot-ad-manager'); ?>
                </label>
                <select class="widefat" id="<?php echo esc_attr($this->get_field_id('position')); ?>" 
                        name="<?php echo esc_attr($this->get_field_name('position')); ?>">
                    <option value="sidebar" <?php selected($position, 'sidebar'); ?>><?php esc_html_e('Sidebar', 'smartspot-ad-manager'); ?></option>
                    <option value="header" <?php selected($position, 'header'); ?>><?php esc_html_e('Header', 'smartspot-ad-manager'); ?></option>
                    <option value="footer" <?php selected($position, 'footer'); ?>><?php esc_html_e('Footer', 'smartspot-ad-manager'); ?></option>
                    <option value="before-content" <?php selected($position, 'before-content'); ?>><?php esc_html_e('Before Content', 'smartspot-ad-manager'); ?></option>
                    <option value="after-content" <?php selected($position, 'after-content'); ?>><?php esc_html_e('After Content', 'smartspot-ad-manager'); ?></option>
                    <option value="custom-1" <?php selected($position, 'custom-1'); ?>><?php esc_html_e('Custom Position 1', 'smartspot-ad-manager'); ?></option>
                    <option value="custom-2" <?php selected($position, 'custom-2'); ?>><?php esc_html_e('Custom Position 2', 'smartspot-ad-manager'); ?></option>
                </select>
            </p>
            <?php
        }
        
        public function update($new_instance, $old_instance) {
            $instance = array();
            $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
            $instance['position'] = (!empty($new_instance['position'])) ? sanitize_text_field($new_instance['position']) : 'sidebar';
            return $instance;
        }
    }
}

// Register widget
if (!function_exists('thesiadm_register_widget')) {
    function thesiadm_register_widget() {
        register_widget('THESIADM_Widget');
    }
    // Only hook if function didn't exist (meaning Free didn't load it yet, or we just defined it)
    // If Free loaded it, it already added the action.
    // But wait, if Free loaded, it defined the function AND added the action.
    // If we are here, and Free loaded, function exists, so we skip definition.
    // Should we add action? No, Free already added it.
    // If Free NOT loaded, we defined function, so we must add action.
    add_action('widgets_init', 'thesiadm_register_widget');
}
