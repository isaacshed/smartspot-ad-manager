<?php
/**
 * Bricks Builder Element for SmartSpot Ad Manager
 */

if (!defined('ABSPATH')) {
    exit;
}

if (defined('BRICKS_VERSION')) {

    class THESIADM_Bricks_Element extends \Bricks\Element {

        public $category     = 'general';
        public $name         = 'thesiadm-bricks-ad';
        public $icon         = 'fas fa-ad';
        public $css_selector = '';
        public $scripts      = array();

        public function get_label() {
            return esc_html__('SmartSpot Ad', 'smartspot-ad-manager');
        }

        public function set_control_groups() {
            $this->control_groups['settings'] = array(
                'title' => esc_html__('Ad Settings', 'smartspot-ad-manager'),
                'tab'   => 'content',
            );
        }

        public function set_controls() {
            $ads_list = thesiadm_get_ads_for_page_builders();

            $this->controls['mode'] = array(
                'group'   => 'settings',
                'label'   => esc_html__('Display Mode', 'smartspot-ad-manager'),
                'type'    => 'select',
                'options' => array(
                    'position' => esc_html__('By Position', 'smartspot-ad-manager'),
                    'single'   => esc_html__('Single Ad', 'smartspot-ad-manager'),
                ),
                'default' => 'position',
                'inline'  => true,
            );

            $this->controls['position'] = array(
                'group'       => 'settings',
                'label'       => esc_html__('Ad Position', 'smartspot-ad-manager'),
                'type'        => 'select',
                'options'     => array(
                    'before-content' => esc_html__('Before Content', 'smartspot-ad-manager'),
                    'after-content'  => esc_html__('After Content', 'smartspot-ad-manager'),
                    'sidebar'        => esc_html__('Sidebar', 'smartspot-ad-manager'),
                    'header'         => esc_html__('Header', 'smartspot-ad-manager'),
                    'footer'         => esc_html__('Footer', 'smartspot-ad-manager'),
                    'custom-1'       => esc_html__('Custom Position 1', 'smartspot-ad-manager'),
                    'custom-2'       => esc_html__('Custom Position 2', 'smartspot-ad-manager'),
                ),
                'default'     => 'before-content',
                'description' => esc_html__('Show all ads for this position that match current URL/device.', 'smartspot-ad-manager'),
                'required'    => array('mode', '=', array('position')),
            );

            $this->controls['ad_id'] = array(
                'group'       => 'settings',
                'label'       => esc_html__('Select Ad', 'smartspot-ad-manager'),
                'type'        => 'select',
                'options'     => $ads_list,
                'default'     => '',
                'description' => esc_html__('Select a single specific ad to display.', 'smartspot-ad-manager'),
                'required'    => array('mode', '=', array('single')),
            );
        }

        public function render() {
            $settings = $this->settings;
            $mode     = !empty($settings['mode']) ? $settings['mode'] : 'position';
            $position = !empty($settings['position']) ? $settings['position'] : 'before-content';
            $ad_id    = !empty($settings['ad_id']) ? absint($settings['ad_id']) : 0;

            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Bricks render_attributes() returns framework-pre-escaped HTML attribute strings.
            echo '<div ' . $this->render_attributes('_root') . '>';
            if ('single' === $mode && $ad_id > 0) {
                echo wp_kses(thesiadm_display_single_ad($ad_id), thesiadm_get_allowed_ad_html());
            } else {
                echo wp_kses(thesiadm_get_ads($position), thesiadm_get_allowed_ad_html());
            }

            echo '</div>';
        }
    }
}
