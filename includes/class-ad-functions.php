<?php
/**
 * Ad Functions
 * Global functions, shortcodes, widgets, and page-builder compatibility layers
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('thesiadm_get_allowed_ad_html')) {
    function thesiadm_get_allowed_ad_html() {
        $allowed_html = wp_kses_allowed_html('post');

        $allowed_html['script'] = array(
            'src'         => true,
            'type'        => true,
            'async'       => true,
            'defer'       => true,
            'crossorigin' => true,
            'charset'     => true,
            'id'          => true,
            'class'       => true,
        );

        $allowed_html['noscript'] = array(
            'id'    => true,
            'class' => true,
        );

        $allowed_html['iframe'] = array(
            'src'             => true,
            'width'           => true,
            'height'          => true,
            'frameborder'     => true,
            'allowfullscreen' => true,
            'style'           => true,
            'class'           => true,
            'data'            => true,
            'id'              => true,
            'name'            => true,
            'title'           => true,
            'scrolling'       => true,
            'marginwidth'     => true,
            'marginheight'    => true,
            'allow'           => true,
            'referrerpolicy'  => true,
            'sandbox'         => true,
            'loading'         => true,
        );

        $allowed_html['ins'] = array(
            'class'                     => true,
            'style'                     => true,
            'data-ad-client'            => true,
            'data-ad-slot'              => true,
            'data-ad-format'            => true,
            'data-full-width-responsive'=> true,
            'data-ad-layout'            => true,
            'data-ad-layout-key'        => true,
            'data-ad-channel'           => true,
            'id'                        => true,
        );

        $allowed_html['div']['data-*']  = true;
        $allowed_html['span']['data-*'] = true;

        return apply_filters('thesiadm_allowed_ad_html', $allowed_html);
    }
}

if (!function_exists('thesiadm_display_ads')) {
    function thesiadm_display_ads($position = 'before-content') {
        $position = sanitize_text_field($position);
        $display  = new THESIADM_Display();
        echo wp_kses($display->render_ads($position), thesiadm_get_allowed_ad_html());
    }
}

if (!function_exists('thesiadm_get_ads')) {
    function thesiadm_get_ads($position = 'before-content') {
        $position = sanitize_text_field($position);
        $display  = new THESIADM_Display();
        return $display->render_ads($position);
    }
}

if (!function_exists('thesiadm_display_single_ad')) {
    function thesiadm_display_single_ad($ad_id = 0) {
        $ad_id = absint($ad_id);
        if (!$ad_id) {
            return '';
        }

        $ad = get_post($ad_id);
        if (!$ad || 'thesiadm_ad' !== $ad->post_type || 'publish' !== $ad->post_status) {
            return '';
        }

        $display = new THESIADM_Display();
        return $display->display_ad($ad);
    }
}

if (!function_exists('thesiadm_has_ads')) {
    function thesiadm_has_ads($position = 'before-content') {
        $position = sanitize_text_field($position);
        $display  = new THESIADM_Display();
        $ads      = $display->get_ads_for_position($position);
        return !empty($ads);
    }
}

if (!function_exists('thesiadm_ads_shortcode')) {
    function thesiadm_ads_shortcode($atts) {
        $atts = shortcode_atts(
            array(
                'position' => 'before-content',
            ),
            $atts,
            'thesiadm_ads'
        );

        $position = sanitize_text_field($atts['position']);
        return thesiadm_get_ads($position);
    }
}

if (!shortcode_exists('thesiadm_ads')) {
    add_shortcode('thesiadm_ads', 'thesiadm_ads_shortcode');
}

if (!function_exists('thesiadm_single_ad_shortcode')) {
    function thesiadm_single_ad_shortcode($atts) {
        $atts = shortcode_atts(
            array(
                'id' => 0,
            ),
            $atts,
            'thesiadm_ad'
        );

        $ad_id = absint($atts['id']);
        if (!$ad_id) {
            return '';
        }

        return wp_kses(thesiadm_display_single_ad($ad_id), thesiadm_get_allowed_ad_html());
    }
}

if (!shortcode_exists('thesiadm_ad')) {
    add_shortcode('thesiadm_ad', 'thesiadm_single_ad_shortcode');
}

if (!function_exists('thesiadm_smartspot_ads_shortcode_alias')) {
    function thesiadm_smartspot_ads_shortcode_alias($atts) {
        return thesiadm_ads_shortcode($atts);
    }
}

if (!shortcode_exists('smartspot_ads')) {
    add_shortcode('smartspot_ads', 'thesiadm_smartspot_ads_shortcode_alias');
}

if (!function_exists('thesiadm_smartspot_ad_shortcode_alias')) {
    function thesiadm_smartspot_ad_shortcode_alias($atts) {
        return thesiadm_single_ad_shortcode($atts);
    }
}

if (!shortcode_exists('smartspot_ad')) {
    add_shortcode('smartspot_ad', 'thesiadm_smartspot_ad_shortcode_alias');
}

if (!class_exists('THESIADM_Widget')) {
    class THESIADM_Widget extends WP_Widget {

        public function __construct() {
            parent::__construct(
                'thesiadm_widget',
                esc_html__('SmartSpot Ad Manager', 'smartspot-ad-manager'),
                array(
                    'description'                 => esc_html__('Display ads from SmartSpot Ad Manager by position or specific ad.', 'smartspot-ad-manager'),
                    'customize_selective_refresh' => true,
                    'show_instance_in_rest'       => true,
                )
            );
        }

        public function widget($args, $instance) {
            echo wp_kses_post($args['before_widget']);

            if (!empty($instance['title'])) {
                echo wp_kses_post($args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title']);
            }

            $mode = !empty($instance['mode']) ? $instance['mode'] : 'position';

            if ('single' === $mode && !empty($instance['ad_id'])) {
                $output = thesiadm_display_single_ad(absint($instance['ad_id']));
            } else {
                $position = !empty($instance['position']) ? $instance['position'] : 'sidebar';
                $output   = thesiadm_get_ads($position);
            }

            echo wp_kses($output, thesiadm_get_allowed_ad_html());

            echo wp_kses_post($args['after_widget']);
        }

        public function form($instance) {
            $title    = !empty($instance['title']) ? $instance['title'] : '';
            $mode     = !empty($instance['mode']) ? $instance['mode'] : 'position';
            $position = !empty($instance['position']) ? $instance['position'] : 'sidebar';
            $ad_id    = !empty($instance['ad_id']) ? absint($instance['ad_id']) : 0;

            $positions = array(
                'sidebar'        => esc_html__('Sidebar', 'smartspot-ad-manager'),
                'before-content' => esc_html__('Before Content', 'smartspot-ad-manager'),
                'after-content'  => esc_html__('After Content', 'smartspot-ad-manager'),
                'header'         => esc_html__('Header', 'smartspot-ad-manager'),
                'footer'         => esc_html__('Footer', 'smartspot-ad-manager'),
                'custom-1'       => esc_html__('Custom Position 1', 'smartspot-ad-manager'),
                'custom-2'       => esc_html__('Custom Position 2', 'smartspot-ad-manager'),
            );

            $ads = get_posts(array(
                'post_type'      => 'thesiadm_ad',
                'posts_per_page' => -1,
                'post_status'    => 'publish',
            ));
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
                <label for="<?php echo esc_attr($this->get_field_id('mode')); ?>">
                    <?php esc_html_e('Display Mode:', 'smartspot-ad-manager'); ?>
                </label>
                <select class="widefat thesiadm-widget-mode" id="<?php echo esc_attr($this->get_field_id('mode')); ?>"
                        name="<?php echo esc_attr($this->get_field_name('mode')); ?>">
                    <option value="position" <?php selected($mode, 'position'); ?>>
                        <?php esc_html_e('By Position (all matching ads)', 'smartspot-ad-manager'); ?>
                    </option>
                    <option value="single" <?php selected($mode, 'single'); ?>>
                        <?php esc_html_e('Specific Single Ad', 'smartspot-ad-manager'); ?>
                    </option>
                </select>
            </p>

            <p class="thesiadm-widget-position-field" style="<?php echo 'single' === $mode ? 'display:none;' : ''; ?>">
                <label for="<?php echo esc_attr($this->get_field_id('position')); ?>">
                    <?php esc_html_e('Ad Position:', 'smartspot-ad-manager'); ?>
                </label>
                <select class="widefat" id="<?php echo esc_attr($this->get_field_id('position')); ?>"
                        name="<?php echo esc_attr($this->get_field_name('position')); ?>">
                    <?php foreach ($positions as $value => $label): ?>
                        <option value="<?php echo esc_attr($value); ?>" <?php selected($position, $value); ?>>
                            <?php echo esc_html($label); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </p>

            <p class="thesiadm-widget-ad-field" style="<?php echo 'single' !== $mode ? 'display:none;' : ''; ?>">
                <label for="<?php echo esc_attr($this->get_field_id('ad_id')); ?>">
                    <?php esc_html_e('Select Ad:', 'smartspot-ad-manager'); ?>
                </label>
                <select class="widefat" id="<?php echo esc_attr($this->get_field_id('ad_id')); ?>"
                        name="<?php echo esc_attr($this->get_field_name('ad_id')); ?>">
                    <option value="0"><?php esc_html_e('&mdash; Select &mdash;', 'smartspot-ad-manager'); ?></option>
                    <?php foreach ($ads as $ad): ?>
                        <?php
                        $ad_position = get_post_meta($ad->ID, '_thesiadm_ad_position', true);
                        $label       = $ad->post_title;
                        if (!empty($ad_position)) {
                            $label .= ' (' . ucwords(str_replace('-', ' ', $ad_position)) . ')';
                        }
                        ?>
                        <option value="<?php echo esc_attr($ad->ID); ?>" <?php selected($ad_id, $ad->ID); ?>>
                            <?php echo esc_html($label); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </p>
            <?php
        }

        public function update($new_instance, $old_instance) {
            $instance             = array();
            $instance['title']    = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
            $instance['mode']     = (!empty($new_instance['mode']) && in_array($new_instance['mode'], array('position', 'single'), true)) ? $new_instance['mode'] : 'position';
            $instance['position'] = (!empty($new_instance['position'])) ? sanitize_text_field($new_instance['position']) : 'sidebar';
            $instance['ad_id']    = (!empty($new_instance['ad_id'])) ? absint($new_instance['ad_id']) : 0;

            return $instance;
        }
    }
}

if (!function_exists('thesiadm_register_widget')) {
    function thesiadm_register_widget() {
        register_widget('THESIADM_Widget');
    }
    add_action('widgets_init', 'thesiadm_register_widget');
}

if (!function_exists('thesiadm_widget_admin_toggle_script')) {
    function thesiadm_widget_admin_toggle_script($hook) {
        if ('widgets.php' !== $hook && 'customize.php' !== $hook) {
            return;
        }

        wp_enqueue_script(
            'thesiadm-widget-toggle',
            THESIADM_PLUGIN_URL . 'assets/js/widget-toggle.js',
            array('jquery'),
            THESIADM_VERSION,
            true
        );
    }
    add_action('admin_enqueue_scripts', 'thesiadm_widget_admin_toggle_script');
}

if (!function_exists('thesiadm_wpbakery_shortcodes')) {
    function thesiadm_wpbakery_shortcodes() {
        if (!function_exists('vc_map')) {
            return;
        }

        $positions = array(
            esc_html__('Before Content', 'smartspot-ad-manager') => 'before-content',
            esc_html__('After Content', 'smartspot-ad-manager')  => 'after-content',
            esc_html__('Sidebar', 'smartspot-ad-manager')        => 'sidebar',
            esc_html__('Header', 'smartspot-ad-manager')         => 'header',
            esc_html__('Footer', 'smartspot-ad-manager')         => 'footer',
            esc_html__('Custom Position 1', 'smartspot-ad-manager') => 'custom-1',
            esc_html__('Custom Position 2', 'smartspot-ad-manager') => 'custom-2',
        );

        vc_map(array(
            'name'        => esc_html__('SmartSpot Ads', 'smartspot-ad-manager'),
            'base'        => 'thesiadm_ads',
            'icon'        => 'icon-wpb-application-icon-large',
            'category'    => esc_html__('Content', 'smartspot-ad-manager'),
            'description' => esc_html__('Display ads by position', 'smartspot-ad-manager'),
            'params'      => array(
                array(
                    'type'       => 'dropdown',
                    'heading'    => esc_html__('Ad Position', 'smartspot-ad-manager'),
                    'param_name' => 'position',
                    'value'      => $positions,
                    'std'        => 'before-content',
                ),
            ),
        ));

        $ads_list = array(esc_html__('&mdash; Select Ad &mdash;', 'smartspot-ad-manager') => '');
        $ads      = get_posts(array(
            'post_type'      => 'thesiadm_ad',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
        ));
        foreach ($ads as $ad) {
            $ads_list[$ad->post_title] = (string) $ad->ID;
        }

        vc_map(array(
            'name'        => esc_html__('SmartSpot Single Ad', 'smartspot-ad-manager'),
            'base'        => 'thesiadm_ad',
            'icon'        => 'icon-wpb-application-icon-large',
            'category'    => esc_html__('Content', 'smartspot-ad-manager'),
            'description' => esc_html__('Display a single specific ad', 'smartspot-ad-manager'),
            'params'      => array(
                array(
                    'type'       => 'dropdown',
                    'heading'    => esc_html__('Select Ad', 'smartspot-ad-manager'),
                    'param_name' => 'id',
                    'value'      => $ads_list,
                ),
            ),
        ));
    }
    add_action('vc_before_init', 'thesiadm_wpbakery_shortcodes');
}

if (!function_exists('thesiadm_beaver_builder_modules')) {
    function thesiadm_beaver_builder_modules() {
        if (!class_exists('FLBuilder')) {
            return;
        }

        require_once THESIADM_PLUGIN_DIR . 'includes/page-builders/class-beaver-builder-module.php';
    }
    add_action('init', 'thesiadm_beaver_builder_modules', 20);
}

if (!function_exists('thesiadm_divi_modules')) {
    function thesiadm_divi_modules() {
        if (!class_exists('ET_Builder_Module')) {
            return;
        }

        require_once THESIADM_PLUGIN_DIR . 'includes/page-builders/class-divi-module.php';
    }
    add_action('et_builder_ready', 'thesiadm_divi_modules');
    add_action('divi_extensions_init', 'thesiadm_divi_modules');
}

if (!function_exists('thesiadm_siteorigin_widgets')) {
    function thesiadm_siteorigin_widgets($folders) {
        $folders[] = THESIADM_PLUGIN_DIR . 'includes/page-builders/siteorigin/';
        return $folders;
    }
    add_filter('siteorigin_widgets_widget_folders', 'thesiadm_siteorigin_widgets');
}

if (!function_exists('thesiadm_oxygen_integration')) {
    function thesiadm_oxygen_integration() {
        if (!function_exists('oxygen_vsb_current_user_can_access')) {
            return;
        }
        add_action('oxygen_add_plus_sections', function () {
            if (class_exists('OxygenElement')) {
                require_once THESIADM_PLUGIN_DIR . 'includes/page-builders/class-oxygen-element.php';
            }
        });
    }
    add_action('init', 'thesiadm_oxygen_integration', 20);
}

if (!function_exists('thesiadm_bricks_elements')) {
    function thesiadm_bricks_elements($elements) {
        if (defined('BRICKS_VERSION')) {
            $elements[] = THESIADM_PLUGIN_DIR . 'includes/page-builders/class-bricks-element.php';
        }
        return $elements;
    }
    add_filter('bricks/builder/elements', 'thesiadm_bricks_elements');
}

if (!function_exists('thesiadm_admin_notice_after_upgrade')) {
    function thesiadm_admin_notice_after_upgrade() {
        global $pagenow;
        if ('plugins.php' !== $pagenow && !get_transient('thesiadm_v2_welcome')) {
            return;
        }

        if (!get_transient('thesiadm_v2_welcome')) {
            return;
        }

        delete_transient('thesiadm_v2_welcome');
        ?>
        <div class="notice notice-success is-dismissible">
            <p>
                <strong><?php esc_html_e('SmartSpot Ad Manager v2.0.0 is now active!', 'smartspot-ad-manager'); ?></strong>
            </p>
            <p>
                <?php esc_html_e('All features are now free. Enjoy unlimited ad positions, device targeting, custom code ads, and native support for Elementor, Gutenberg, Beaver Builder, Divi, WPBakery, and more.', 'smartspot-ad-manager'); ?>
            </p>
            <p>
                <a class="button button-primary" href="<?php echo esc_url(admin_url('edit.php?post_type=thesiadm_ad')); ?>">
                    <?php esc_html_e('Manage Ads', 'smartspot-ad-manager'); ?>
                </a>
            </p>
        </div>
        <?php
    }
    add_action('admin_notices', 'thesiadm_admin_notice_after_upgrade');
}
