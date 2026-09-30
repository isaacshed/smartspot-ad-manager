<?php
/**
 * Elementor Widget for SmartSpot Ad Manager
 * File: includes/elementor/class-thesiadm-elementor-widget.php
 */

if (!defined('ABSPATH')) exit;

class THESIADM_Elementor_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'thesiadm_elementor_widget';
    }

    public function get_title() {
        return esc_html__('SmartSpot Ad Manager', 'smartspot-ad-manager');
    }

    public function get_icon() {
        return 'eicon-ads';
    }

    public function get_categories() {
        return array('general', 'wordpress');
    }

    public function get_keywords() {
        return array('ad', 'ads', 'advertising', 'banner', 'smartspot', 'monetization');
    }

    private function get_position_options() {
        return array(
            ''             => esc_html__('-- Select Position --', 'smartspot-ad-manager'),
            'before-content' => esc_html__('Before Content', 'smartspot-ad-manager'),
            'after-content'  => esc_html__('After Content', 'smartspot-ad-manager'),
            'sidebar'        => esc_html__('Sidebar', 'smartspot-ad-manager'),
            'header'         => esc_html__('Header', 'smartspot-ad-manager'),
            'footer'         => esc_html__('Footer', 'smartspot-ad-manager'),
            'custom-1'       => esc_html__('Custom Position 1', 'smartspot-ad-manager'),
            'custom-2'       => esc_html__('Custom Position 2', 'smartspot-ad-manager'),
        );
    }

    private function get_ads_list() {
        $ads_list = array(
            '' => esc_html__('-- Use Position Above --', 'smartspot-ad-manager'),
        );

        $ads = get_posts(array(
            'post_type'      => 'thesiadm_ad',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
        ));

        foreach ($ads as $ad) {
            $position = get_post_meta($ad->ID, '_thesiadm_ad_position', true);
            $label = $ad->post_title;
            if (!empty($position)) {
                $label .= ' (' . ucwords(str_replace('-', ' ', $position)) . ')';
            }
            $ads_list[$ad->ID] = $label;
        }

        return $ads_list;
    }

    protected function register_controls() {
        $this->start_controls_section(
            'content_section',
            array(
                'label' => esc_html__('Ad Display', 'smartspot-ad-manager'),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'display_mode',
            array(
                'label'   => esc_html__('Display Mode', 'smartspot-ad-manager'),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'options' => array(
                    'position' => esc_html__('By Position (all matching ads)', 'smartspot-ad-manager'),
                    'single'   => esc_html__('Specific Single Ad', 'smartspot-ad-manager'),
                ),
                'default' => 'position',
            )
        );

        $this->add_control(
            'position',
            array(
                'label'     => esc_html__('Ad Position', 'smartspot-ad-manager'),
                'type'      => \Elementor\Controls_Manager::SELECT,
                'options'   => $this->get_position_options(),
                'default'   => 'before-content',
                'condition' => array(
                    'display_mode' => 'position',
                ),
            )
        );

        $this->add_control(
            'ad_id',
            array(
                'label'     => esc_html__('Select Specific Ad', 'smartspot-ad-manager'),
                'type'      => \Elementor\Controls_Manager::SELECT,
                'options'   => $this->get_ads_list(),
                'default'   => '',
                'condition' => array(
                    'display_mode' => 'single',
                ),
            )
        );

        $this->add_control(
            'alignment',
            array(
                'label'     => esc_html__('Alignment', 'smartspot-ad-manager'),
                'type'      => \Elementor\Controls_Manager::CHOOSE,
                'options'   => array(
                    'left'   => array(
                        'title' => esc_html__('Left', 'smartspot-ad-manager'),
                        'icon'  => 'eicon-text-align-left',
                    ),
                    'center' => array(
                        'title' => esc_html__('Center', 'smartspot-ad-manager'),
                        'icon'  => 'eicon-text-align-center',
                    ),
                    'right'  => array(
                        'title' => esc_html__('Right', 'smartspot-ad-manager'),
                        'icon'  => 'eicon-text-align-right',
                    ),
                ),
                'default'   => 'center',
                'toggle'    => true,
                'selectors' => array(
                    '{{WRAPPER}} .thesiadm-elementor-wrapper' => 'text-align: {{VALUE}};',
                ),
            )
        );

        $this->add_responsive_control(
            'margin',
            array(
                'label'      => esc_html__('Margin', 'smartspot-ad-manager'),
                'type'       => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => array('px', 'em', '%'),
                'selectors'  => array(
                    '{{WRAPPER}} .thesiadm-elementor-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->add_responsive_control(
            'padding',
            array(
                'label'      => esc_html__('Padding', 'smartspot-ad-manager'),
                'type'       => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => array('px', 'em', '%'),
                'selectors'  => array(
                    '{{WRAPPER}} .thesiadm-elementor-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings     = $this->get_settings_for_display();
        $display_mode = !empty($settings['display_mode']) ? $settings['display_mode'] : 'position';

        echo '<div class="thesiadm-elementor-wrapper">';

        if ('single' === $display_mode) {
            $ad_id = !empty($settings['ad_id']) ? absint($settings['ad_id']) : 0;

            if (empty($ad_id)) {
                if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                    echo '<div style="padding: 20px; background: #f0f0f1; text-align: center; border-radius: 4px; color: #646970;">' . esc_html__('Please select a specific ad to display', 'smartspot-ad-manager') . '</div>';
                }
                echo '</div>';
                return;
            }

            $ad = get_post($ad_id);

            if (!$ad || 'thesiadm_ad' !== $ad->post_type || 'publish' !== $ad->post_status) {
                if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                    echo '<div style="padding: 20px; background: #fef7f1; text-align: center; border-radius: 4px; color: #8a2424; border: 1px solid #f0b849;">' . esc_html__('Selected ad not found or not published.', 'smartspot-ad-manager') . '</div>';
                }
                echo '</div>';
                return;
            }

            $display = new THESIADM_Display();
            echo wp_kses($display->display_ad($ad), thesiadm_get_allowed_ad_html());

        } else {
            $position = !empty($settings['position']) ? sanitize_text_field($settings['position']) : 'before-content';

            if (empty($position)) {
                if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                    echo '<div style="padding: 20px; background: #f0f0f1; text-align: center; border-radius: 4px; color: #646970;">' . esc_html__('Please select an ad position', 'smartspot-ad-manager') . '</div>';
                }
                echo '</div>';
                return;
            }

            $output = thesiadm_get_ads($position);

            if (empty($output)) {
                if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                    echo '<div style="padding: 20px; background: #f0f6fc; text-align: center; border-radius: 4px; color: #135e96; border: 1px solid #72aee6;">';
                    printf(
                        /* translators: %s: the human-readable ad position name, e.g. "Before Content" */
                        esc_html__('No ads found for position "%s". Create an ad in Ad Manager with this position.', 'smartspot-ad-manager'),
                        esc_html(ucwords(str_replace('-', ' ', $position)))
                    );
                    echo '</div>';
                }
            } else {
                echo wp_kses($output, thesiadm_get_allowed_ad_html());
            }
        }

        echo '</div>';
    }
}
