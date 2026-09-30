<?php
/**
 * Beaver Builder Module for SmartSpot Ad Manager
 */

if (!defined('ABSPATH')) {
    exit;
}

if (class_exists('FLBuilderModule')) {

    class THESIADM_Beaver_Builder_Module extends FLBuilderModule {

        public function __construct() {
            parent::__construct(array(
                'name'            => esc_html__('SmartSpot Ads', 'smartspot-ad-manager'),
                'description'     => esc_html__('Display ads from SmartSpot Ad Manager.', 'smartspot-ad-manager'),
                'category'        => esc_html__('Basic', 'smartspot-ad-manager'),
                'dir'             => THESIADM_PLUGIN_DIR . 'includes/page-builders/',
                'url'             => THESIADM_PLUGIN_URL . 'includes/page-builders/',
                'editor_export'   => true,
                'enabled'         => true,
                'partial_refresh' => true,
                'icon'            => 'megaphone.svg',
            ));
        }
    }

    FLBuilder::register_module('THESIADM_Beaver_Builder_Module', array(
        'general' => array(
            'title'    => esc_html__('General', 'smartspot-ad-manager'),
            'sections' => array(
                'general' => array(
                    'title'  => esc_html__('Ad Display', 'smartspot-ad-manager'),
                    'fields' => array(
                        'mode' => array(
                            'type'    => 'select',
                            'label'   => esc_html__('Display Mode', 'smartspot-ad-manager'),
                            'default' => 'position',
                            'options' => array(
                                'position' => esc_html__('By Position', 'smartspot-ad-manager'),
                                'single'   => esc_html__('Single Ad', 'smartspot-ad-manager'),
                            ),
                            'toggle'  => array(
                                'position' => array(
                                    'fields' => array('position'),
                                ),
                                'single'   => array(
                                    'fields' => array('ad_id'),
                                ),
                            ),
                        ),
                        'position' => array(
                            'type'    => 'select',
                            'label'   => esc_html__('Ad Position', 'smartspot-ad-manager'),
                            'default' => 'before-content',
                            'options' => array(
                                'before-content' => esc_html__('Before Content', 'smartspot-ad-manager'),
                                'after-content'  => esc_html__('After Content', 'smartspot-ad-manager'),
                                'sidebar'        => esc_html__('Sidebar', 'smartspot-ad-manager'),
                                'header'         => esc_html__('Header', 'smartspot-ad-manager'),
                                'footer'         => esc_html__('Footer', 'smartspot-ad-manager'),
                                'custom-1'       => esc_html__('Custom Position 1', 'smartspot-ad-manager'),
                                'custom-2'       => esc_html__('Custom Position 2', 'smartspot-ad-manager'),
                            ),
                        ),
                        'ad_id' => array(
                            'type'    => 'select',
                            'label'   => esc_html__('Select Ad', 'smartspot-ad-manager'),
                            'default' => '',
                            'options' => thesiadm_get_ads_for_page_builders(),
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
                ),
            ),
        ),
    ));
}

if (!function_exists('thesiadm_get_ads_for_page_builders')) {
    function thesiadm_get_ads_for_page_builders() {
        $ads = get_posts(array(
            'post_type'      => 'thesiadm_ad',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
        ));

        $options = array('' => esc_html__('&mdash; Select &mdash;', 'smartspot-ad-manager'));
        foreach ($ads as $ad) {
            $position = get_post_meta($ad->ID, '_thesiadm_ad_position', true);
            $label    = $ad->post_title;
            if (!empty($position)) {
                $label .= ' (' . ucwords(str_replace('-', ' ', $position)) . ')';
            }
            $options[ $ad->ID ] = $label;
        }

        return $options;
    }
}
