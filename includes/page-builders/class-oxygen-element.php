<?php
/**
 * Oxygen Builder Element for SmartSpot Ad Manager
 */

if (!defined('ABSPATH')) {
    exit;
}

if (class_exists('OxygenElement')) {

    class THESIADM_Oxygen_Element extends OxygenElement {

        var $name            = 'thesiadm_oxygen_ad';
        var $tag             = 'thesiadm-oxygen-ad';
        var $icon_path;

        function __construct() {
            $this->icon_path = THESIADM_PLUGIN_URL . 'assets/images/icon-128x128.png';
            parent::__construct();
        }

        function controls() {
            $ads_list = thesiadm_get_ads_for_page_builders();
            unset($ads_list['']);

            $this->addOptionControl(
                array(
                    'type'    => 'buttons-list',
                    'name'    => esc_html__('Display Mode', 'smartspot-ad-manager'),
                    'slug'    => 'mode',
                    'value'   => 'position',
                    'buttons' => array(
                        'position' => esc_html__('Position', 'smartspot-ad-manager'),
                        'single'   => esc_html__('Single Ad', 'smartspot-ad-manager'),
                    ),
                )
            );

            $this->addOptionControl(
                array(
                    'type'  => 'dropdown',
                    'name'  => esc_html__('Position', 'smartspot-ad-manager'),
                    'slug'  => 'position',
                    'value' => 'before-content',
                    'list'  => array(
                        'before-content' => esc_html__('Before Content', 'smartspot-ad-manager'),
                        'after-content'  => esc_html__('After Content', 'smartspot-ad-manager'),
                        'sidebar'        => esc_html__('Sidebar', 'smartspot-ad-manager'),
                        'header'         => esc_html__('Header', 'smartspot-ad-manager'),
                        'footer'         => esc_html__('Footer', 'smartspot-ad-manager'),
                        'custom-1'       => esc_html__('Custom Position 1', 'smartspot-ad-manager'),
                        'custom-2'       => esc_html__('Custom Position 2', 'smartspot-ad-manager'),
                    ),
                )
            )
            ->setCondition('mode=position');

            $this->addOptionControl(
                array(
                    'type'  => 'dropdown',
                    'name'  => esc_html__('Select Ad', 'smartspot-ad-manager'),
                    'slug'  => 'ad_id',
                    'value' => '',
                    'list'  => $ads_list,
                )
            )
            ->setCondition('mode=single');

            $this->addStyleControl(
                array(
                    'name'     => esc_html__('Alignment', 'smartspot-ad-manager'),
                    'selector' => '',
                    'property' => 'text-align',
                )
            );
        }

        function render($options, $defaults, $content) {
            $mode     = isset($options['mode']) ? $options['mode'] : 'position';
            $position = isset($options['position']) ? $options['position'] : 'before-content';
            $ad_id    = isset($options['ad_id']) ? absint($options['ad_id']) : 0;

            if ('single' === $mode && $ad_id > 0) {
                echo wp_kses(thesiadm_display_single_ad($ad_id), thesiadm_get_allowed_ad_html());
            } else {
                echo wp_kses(thesiadm_get_ads($position), thesiadm_get_allowed_ad_html());
            }
        }
    }

    new THESIADM_Oxygen_Element();
}
