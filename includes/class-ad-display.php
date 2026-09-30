<?php
/**
 * Ad Display Class
 * Handles frontend ad display logic
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('THESIADM_Display')) {
    class THESIADM_Display {

        public function __construct() {
            add_filter('the_content', array($this, 'inject_automatic_positions'));
        }

        public function inject_automatic_positions($content) {
            if (is_admin()) {
                return $content;
            }

            if (!is_singular()) {
                return $content;
            }

            if (get_post_type() === 'thesiadm_ad') {
                return $content;
            }

            $before = $this->render_ads('before-content');
            $after  = $this->render_ads('after-content');

            return $before . $content . $after;
        }

        public function get_ads_for_position($position) {
            $current_url    = $this->get_current_url_path();
            $current_device = $this->detect_device();

            $args = array(
                'post_type'      => 'thesiadm_ad',
                'posts_per_page' => -1,
                'post_status'    => 'publish',
                'no_found_rows'  => true,
                // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- Ad position is a low-cardinality postmeta filter used with no_found_rows=true.
                'meta_query'     => array(
                    array(
                        'key'     => '_thesiadm_ad_position',
                        'value'   => $position,
                        'compare' => '=',
                    ),
                ),
                'orderby'        => 'meta_value_num date',
                'order'          => 'ASC',
                // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- Ad priority meta_key ordering is bounded by the ad count and uses no_found_rows.
                'meta_key'       => '_thesiadm_ad_priority',
            );

            $ads = get_posts($args);
            $matching_ads = array();

            foreach ($ads as $ad) {
                if (!$this->url_matches($ad->ID, $current_url)) {
                    continue;
                }

                if (!$this->device_matches($ad->ID, $current_device)) {
                    continue;
                }

                $matching_ads[] = $ad;
            }

            return $matching_ads;
        }

        private function detect_device() {
            if (function_exists('wp_is_mobile') && wp_is_mobile()) {
                $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? sanitize_text_field(wp_unslash($_SERVER['HTTP_USER_AGENT'])) : '';

                $tablet_patterns = array(
                    'iPad',
                    'tablet',
                    'Tablet',
                    'PlayBook',
                    'Kindle',
                    'Silk',
                    'Android(?!.*Mobile)',
                );

                foreach ($tablet_patterns as $pattern) {
                    if (@preg_match('/' . $pattern . '/i', $user_agent)) {
                        return 'tablet';
                    }
                }

                return 'mobile';
            }

            return 'desktop';
        }

        private function device_matches($ad_id, $current_device) {
            $target_devices = get_post_meta($ad_id, '_thesiadm_target_devices', true);

            if (!is_array($target_devices) || empty($target_devices)) {
                return true;
            }

            return in_array($current_device, $target_devices, true);
        }

        private function url_matches($ad_id, $current_url) {
            $target_urls = get_post_meta($ad_id, '_thesiadm_target_urls', true);
            $match_type  = get_post_meta($ad_id, '_thesiadm_url_match_type', true);

            if (empty($target_urls)) {
                return true;
            }

            $url_list = array_filter(array_map('trim', explode("\n", $target_urls)));

            if (empty($url_list)) {
                return true;
            }

            $current_url = '/' . trim((string) $current_url, '/') . '/';

            foreach ($url_list as $target_url) {
                $target_url = '/' . trim((string) $target_url, '/') . '/';

                switch ($match_type) {
                    case 'exact':
                        if ($current_url === $target_url) {
                            return true;
                        }
                        break;

                    case 'contains':
                        $needle = trim($target_url, '/');
                        if ($needle !== '' && strpos($current_url, $needle) !== false) {
                            return true;
                        }
                        break;

                    case 'starts_with':
                    default:
                        if (strpos($current_url, $target_url) === 0) {
                            return true;
                        }
                        break;
                }
            }

            return false;
        }

        private function get_current_url_path() {
            $req_uri  = isset($_SERVER['REQUEST_URI']) ? sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])) : '';
            $url_path = !empty($req_uri) ? wp_parse_url($req_uri, PHP_URL_PATH) : '/';

            if (empty($url_path)) {
                $url_path = '/';
            }

            return $url_path;
        }

        public function display_ad($ad) {
            if (!is_object($ad) || !isset($ad->ID)) {
                return '';
            }

            $ad_type = get_post_meta($ad->ID, '_thesiadm_ad_type', true);

            if ($ad_type === 'code') {
                return $this->display_code_ad($ad);
            }

            return $this->display_image_ad($ad);
        }

        private function display_image_ad($ad) {
            $ad_link      = get_post_meta($ad->ID, '_thesiadm_ad_link', true);
            $open_new_tab = get_post_meta($ad->ID, '_thesiadm_open_new_tab', true);
            $image_url    = get_the_post_thumbnail_url($ad->ID, 'full');
            $ad_title     = get_the_title($ad);

            if (!$image_url) {
                return '';
            }

            ob_start();
            ?>
            <div class="thesiadm-ad-wrapper thesiadm-image-ad" data-ad-id="<?php echo esc_attr($ad->ID); ?>">
                <?php if ($ad_link): ?>
                    <a href="<?php echo esc_url($ad_link); ?>"<?php if ('1' === $open_new_tab) { ?> target="_blank" rel="noopener noreferrer"<?php } ?> class="thesiadm-ad-link">
                        <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($ad_title); ?>" class="thesiadm-ad-image" loading="lazy">
                    </a>
                <?php else: ?>
                    <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($ad_title); ?>" class="thesiadm-ad-image" loading="lazy">
                <?php endif; ?>
            </div>
            <?php
            return ob_get_clean();
        }

        private function display_code_ad($ad) {
            $ad_code = get_post_meta($ad->ID, '_thesiadm_ad_code', true);

            if (!$ad_code) {
                return '';
            }

            $allowed_html = thesiadm_get_allowed_ad_html();

            ob_start();
            ?>
            <div class="thesiadm-ad-wrapper thesiadm-code-ad" data-ad-id="<?php echo esc_attr($ad->ID); ?>">
                <?php echo wp_kses($ad_code, $allowed_html); ?>
            </div>
            <?php
            return ob_get_clean();
        }

        public function render_ads($position) {
            $ads = $this->get_ads_for_position($position);

            if (empty($ads)) {
                return '';
            }

            $allowed_html = thesiadm_get_allowed_ad_html();

            ob_start();
            ?>
            <div class="thesiadm-position-wrapper thesiadm-position-<?php echo esc_attr($position); ?>">
                <?php foreach ($ads as $ad): ?>
                    <?php echo wp_kses($this->display_ad($ad), $allowed_html); ?>
                <?php endforeach; ?>
            </div>
            <?php
            return ob_get_clean();
        }
    }
}
