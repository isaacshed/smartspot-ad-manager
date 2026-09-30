<?php
/**
 * Template for SiteOrigin Widget
 */

if (!defined('ABSPATH')) {
    exit;
}

$title    = !empty($instance['title']) ? $instance['title'] : '';
$mode     = !empty($instance['mode']) ? $instance['mode'] : 'position';
$thesiadm_position = !empty($instance['position']) ? $instance['position'] : 'before-content';
$thesiadm_ad_id    = !empty($instance['ad_id']) ? absint($instance['ad_id']) : 0;
$thesiadm_align    = !empty($instance['align']) ? $instance['align'] : 'center';

echo wp_kses_post($args['before_widget']);

if ($title) {
    echo wp_kses_post($args['before_title'] . $title . $args['after_title']);
}

echo '<div class="thesiadm-siteorigin-wrapper" style="text-align:' . esc_attr($thesiadm_align) . ';">';

if ('single' === $mode && $thesiadm_ad_id > 0) {
    echo wp_kses(thesiadm_display_single_ad($thesiadm_ad_id), thesiadm_get_allowed_ad_html());
} else {
    echo wp_kses(thesiadm_get_ads($thesiadm_position), thesiadm_get_allowed_ad_html());
}

echo '</div>';

echo wp_kses_post($args['after_widget']);
