<?php
/**
 * Divi Builder Module for SmartSpot Ad Manager
 */

if (!defined('ABSPATH')) {
    exit;
}

if (class_exists('ET_Builder_Module')) {

    class THESIADM_Divi_Module extends ET_Builder_Module {

        public $slug       = 'thesiadm_divi_ad';
        public $vb_support = 'on';

        protected $module_credits = array(
            'module_uri' => 'https://smartspotad.isaacauta.com',
            'author'     => 'Isaac Shed',
            'author_uri' => 'https://isaacauta.com',
        );

        public function init() {
            $this->name = esc_html__('SmartSpot Ad', 'smartspot-ad-manager');
            $this->icon_path = plugin_dir_path(__FILE__) . 'divi-icon.svg';
            $this->folder_name = 'et_pb_plugin';

            $this->settings_modal_toggles = array(
                'general'  => array(
                    'toggles' => array(
                        'main_content' => esc_html__('Ad Settings', 'smartspot-ad-manager'),
                    ),
                ),
            );
        }

        public function get_fields() {
            $ads_list = thesiadm_get_ads_for_page_builders();

            return array(
                'mode' => array(
                    'label'           => esc_html__('Display Mode', 'smartspot-ad-manager'),
                    'type'            => 'select',
                    'option_category' => 'basic_option',
                    'options'         => array(
                        'position' => esc_html__('By Position', 'smartspot-ad-manager'),
                        'single'   => esc_html__('Single Ad', 'smartspot-ad-manager'),
                    ),
                    'default'         => 'position',
                    'toggle_slug'     => 'main_content',
                    'description'     => esc_html__('Choose to display ads by position or a specific single ad.', 'smartspot-ad-manager'),
                ),
                'position' => array(
                    'label'           => esc_html__('Ad Position', 'smartspot-ad-manager'),
                    'type'            => 'select',
                    'option_category' => 'basic_option',
                    'options'         => array(
                        'before-content' => esc_html__('Before Content', 'smartspot-ad-manager'),
                        'after-content'  => esc_html__('After Content', 'smartspot-ad-manager'),
                        'sidebar'        => esc_html__('Sidebar', 'smartspot-ad-manager'),
                        'header'         => esc_html__('Header', 'smartspot-ad-manager'),
                        'footer'         => esc_html__('Footer', 'smartspot-ad-manager'),
                        'custom-1'       => esc_html__('Custom Position 1', 'smartspot-ad-manager'),
                        'custom-2'       => esc_html__('Custom Position 2', 'smartspot-ad-manager'),
                    ),
                    'default'         => 'before-content',
                    'toggle_slug'     => 'main_content',
                    'show_if'         => array(
                        'mode' => 'position',
                    ),
                ),
                'ad_id' => array(
                    'label'           => esc_html__('Select Ad', 'smartspot-ad-manager'),
                    'type'            => 'select',
                    'option_category' => 'basic_option',
                    'options'         => $ads_list,
                    'default'         => '',
                    'toggle_slug'     => 'main_content',
                    'show_if'         => array(
                        'mode' => 'single',
                    ),
                ),
                'align' => array(
                    'label'           => esc_html__('Alignment', 'smartspot-ad-manager'),
                    'type'            => 'select',
                    'option_category' => 'basic_option',
                    'options'         => array(
                        'left'   => esc_html__('Left', 'smartspot-ad-manager'),
                        'center' => esc_html__('Center', 'smartspot-ad-manager'),
                        'right'  => esc_html__('Right', 'smartspot-ad-manager'),
                    ),
                    'default'         => 'center',
                    'toggle_slug'     => 'main_content',
                ),
            );
        }

        public function render($attrs, $content = null, $render_slug = null) {
            $mode     = !empty($this->props['mode']) ? $this->props['mode'] : 'position';
            $position = !empty($this->props['position']) ? $this->props['position'] : 'before-content';
            $ad_id    = !empty($this->props['ad_id']) ? absint($this->props['ad_id']) : 0;
            $align    = !empty($this->props['align']) ? $this->props['align'] : 'center';

            $output = '<div class="thesiadm-divi-wrapper" style="text-align:' . esc_attr($align) . ';">';

            if ('single' === $mode && $ad_id > 0) {
                $output .= wp_kses(thesiadm_display_single_ad($ad_id), thesiadm_get_allowed_ad_html());
            } else {
                $output .= wp_kses(thesiadm_get_ads($position), thesiadm_get_allowed_ad_html());
            }

            $output .= '</div>';

            return $output;
        }
    }

    new THESIADM_Divi_Module();
}
