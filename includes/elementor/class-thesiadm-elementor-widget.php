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
        return esc_html__('Ad Manager', 'smartspot-ad-manager');
    }
    
    public function get_icon() {
        return 'eicon-ads';
    }
    
    public function get_categories() {
        return ['general'];
    }
    
    protected function register_controls() {
        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__('Ad Selection', 'smartspot-ad-manager'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );
        
        // Get all ads
        $ads_list = ['' => esc_html__('-- Select Ad --', 'smartspot-ad-manager')];
        $ads = get_posts([
            'post_type' => 'thesiadm_ad',
            'posts_per_page' => -1,
            'post_status' => 'publish'
        ]);
        
        foreach ($ads as $ad) {
            $position = get_post_meta($ad->ID, '_thesiadm_ad_position', true);
            $ads_list[$ad->ID] = $ad->post_title . ' (' . ucwords(str_replace('-', ' ', $position)) . ')';
        }
        
        $this->add_control(
            'ad_id',
            [
                'label' => esc_html__('Select Ad', 'smartspot-ad-manager'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => $ads_list,
                'default' => '',
            ]
        );
        
        $this->end_controls_section();
    }
    
    protected function render() {
        $settings = $this->get_settings_for_display();
        
        if (empty($settings['ad_id'])) {
            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                echo '<div style="padding: 20px; background: #f0f0f1; text-align: center; border-radius: 4px;">' . esc_html__('Please select an ad', 'smartspot-ad-manager') . '</div>';
            }
            return;
        }
        
        $display = new THESIADM_Display();
        $ad = get_post($settings['ad_id']);
        
        if ($ad && $ad->post_type === 'thesiadm_ad') {
            echo wp_kses($display->display_ad($ad), thesiadm_get_allowed_ad_html());
        }
    }
}
