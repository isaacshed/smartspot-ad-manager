<?php
/**
 * SiteOrigin Page Builder Widget for SmartSpot Ad Manager
 */

if (!defined('ABSPATH')) {
    exit;
}

if (class_exists('SiteOrigin_Widget')) {

    class THESIADM_SiteOrigin_Widget extends SiteOrigin_Widget {

        function __construct() {
            parent::__construct(
                'thesiadm-siteorigin-widget',
                esc_html__('SmartSpot Ads', 'smartspot-ad-manager'),
                array(
                    'description'   => esc_html__('Display ads from SmartSpot Ad Manager.', 'smartspot-ad-manager'),
                    'panels_groups' => array('smartspot'),
                    'has_preview'   => false,
                ),
                array(),
                array(
                    'title' => array(
                        'type'  => 'text',
                        'label' => esc_html__('Title', 'smartspot-ad-manager'),
                    ),
                    'mode' => array(
                        'type'    => 'select',
                        'label'   => esc_html__('Display Mode', 'smartspot-ad-manager'),
                        'default' => 'position',
                        'options' => array(
                            'position' => esc_html__('By Position', 'smartspot-ad-manager'),
                            'single'   => esc_html__('Single Ad', 'smartspot-ad-manager'),
                        ),
                        'state_emitter' => array(
                            'callback' => 'select',
                            'args'     => array('mode'),
                        ),
                    ),
                    'position' => array(
                        'type'      => 'select',
                        'label'     => esc_html__('Ad Position', 'smartspot-ad-manager'),
                        'default'   => 'before-content',
                        'options'   => array(
                            'before-content' => esc_html__('Before Content', 'smartspot-ad-manager'),
                            'after-content'  => esc_html__('After Content', 'smartspot-ad-manager'),
                            'sidebar'        => esc_html__('Sidebar', 'smartspot-ad-manager'),
                            'header'         => esc_html__('Header', 'smartspot-ad-manager'),
                            'footer'         => esc_html__('Footer', 'smartspot-ad-manager'),
                            'custom-1'       => esc_html__('Custom Position 1', 'smartspot-ad-manager'),
                            'custom-2'       => esc_html__('Custom Position 2', 'smartspot-ad-manager'),
                        ),
                        'state_handler' => array(
                            'mode[position]' => array('show'),
                            '_else[mode]'    => array('hide'),
                        ),
                    ),
                    'ad_id' => array(
                        'type'      => 'select',
                        'label'     => esc_html__('Select Ad', 'smartspot-ad-manager'),
                        'default'   => 0,
                        'options'   => thesiadm_get_ads_for_page_builders(),
                        'state_handler' => array(
                            'mode[single]' => array('show'),
                            '_else[mode]'  => array('hide'),
                        ),
                    ),
                    'align' => array(
                        'type'    => 'select',
                        'label'   => esc_html__('Alignment', 'smartspot-ad-manager'),
                        'default' => 'center',
                        'options' => array(
                            'left'   => esc_html__('Left', 'smartspot-ad-manager'),
                            'center' => esc_html__('Center', 'smartspot-ad-manager'),
                            'right'  => esc_html__('Right', 'smartspot-ad-manager'),
                        ),
                    ),
                ),
                THESIADM_PLUGIN_DIR . 'includes/page-builders/siteorigin/'
            );
        }

        function get_template_name($instance) {
            return 'ads-template';
        }

        function get_style_name($instance) {
            return false;
        }

        function modify_form($form) {
            return $form;
        }
    }

    siteorigin_widget_register('thesiadm-siteorigin-widget', __FILE__, 'THESIADM_SiteOrigin_Widget');
}
