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
            // No automatic hooks - ads are displayed via function calls or shortcodes
        }
        
        /**
         * Get ads for current URL and position
         */
        public function get_ads_for_position($position) {
            $current_url = $this->get_current_url_path();
            $current_device = $this->detect_device();
            
            // Query for ads
            $args = array(
                'post_type' => 'thesiadm_ad',
                'posts_per_page' => -1,
                'post_status' => 'publish',
                'meta_query' => array(
                    array(
                        'key' => '_thesiadm_ad_position',
                        'value' => $position,
                        'compare' => '='
                    )
                ),
                'orderby' => 'meta_value_num',
                'order' => 'ASC',
                'meta_key' => '_thesiadm_ad_priority'
            );
            
            $ads = get_posts($args);
            $matching_ads = array();
            
            foreach ($ads as $ad) {
                // Check URL match
                if (!$this->url_matches($ad->ID, $current_url)) {
                    continue;
                }
                
                // Check device match
                if (!$this->device_matches($ad->ID, $current_device)) {
                    continue;
                }
                
                $matching_ads[] = $ad;
            }
            
            return $matching_ads;
        }
        
        /**
         * Detect current device type
         */
        private function detect_device() {
            // Check if wp_is_mobile() exists (WordPress function)
            if (function_exists('wp_is_mobile') && wp_is_mobile()) {
                // Further detect if it's tablet or mobile
                $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? sanitize_text_field(wp_unslash($_SERVER['HTTP_USER_AGENT'])) : '';
                
                // Tablet detection patterns
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
                    if (preg_match('/' . $pattern . '/i', $user_agent)) {
                        return 'tablet';
                    }
                }
                
                // If mobile but not tablet, it's mobile
                return 'mobile';
            }
            
            // Desktop
            return 'desktop';
        }
        
        /**
         * Check if current device matches ad targeting
         */
        private function device_matches($ad_id, $current_device) {
            $target_devices = get_post_meta($ad_id, '_thesiadm_target_devices', true);
            
            // If no devices set or empty, show on all devices
            if (!is_array($target_devices) || empty($target_devices)) {
                return true;
            }
            
            // Check if current device is in target devices
            return in_array($current_device, $target_devices);
        }
        
        /**
         * Check if current URL matches ad targeting
         */
        private function url_matches($ad_id, $current_url) {
            $target_urls = get_post_meta($ad_id, '_thesiadm_target_urls', true);
            $match_type = get_post_meta($ad_id, '_thesiadm_url_match_type', true);
            
            if (empty($target_urls)) {
                return false;
            }
            
            // Split URLs by line
            $url_list = array_filter(array_map('trim', explode("\n", $target_urls)));
            
            foreach ($url_list as $target_url) {
                // Normalize URLs
                $target_url = '/' . trim($target_url, '/') . '/';
                $current_url = '/' . trim($current_url, '/') . '/';
                
                switch ($match_type) {
                    case 'exact':
                        if ($current_url === $target_url) {
                            return true;
                        }
                        break;
                        
                    case 'contains':
                        if (strpos($current_url, trim($target_url, '/')) !== false) {
                            return true;
                        }
                        break;
                        
                    case 'starts_with':
                        if (strpos($current_url, $target_url) === 0) {
                            return true;
                        }
                        break;
                }
            }
            
            return false;
        }
        
        /**
         * Get current URL path
         */
        private function get_current_url_path() {
            $url_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            return $url_path;
        }
        
        /**
         * Display ad HTML
         */
        public function display_ad($ad) {
            $ad_type = get_post_meta($ad->ID, '_thesiadm_ad_type', true);
            
            if ($ad_type === 'code') {
                return $this->display_code_ad($ad);
            } else {
                return $this->display_image_ad($ad);
            }
        }
        
        /**
         * Display image ad
         */
        private function display_image_ad($ad) {
            $ad_link = get_post_meta($ad->ID, '_thesiadm_ad_link', true);
            $open_new_tab = get_post_meta($ad->ID, '_thesiadm_open_new_tab', true);
            $image_url = get_the_post_thumbnail_url($ad->ID, 'full');
            
            if (!$image_url) {
                return '';
            }
            
            $target = $open_new_tab ? ' target="_blank" rel="noopener noreferrer"' : '';
            
            ob_start();
            ?>
            <div class="thesiadm-ad-wrapper thesiadm-image-ad" data-ad-id="<?php echo esc_attr($ad->ID); ?>">
                <?php if ($ad_link): ?>
                    <a href="<?php echo esc_url($ad_link); ?>"<?php echo $target; ?> class="thesiadm-ad-link">
                        <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($ad->post_title); ?>" class="thesiadm-ad-image">
                    </a>
                <?php else: ?>
                    <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($ad->post_title); ?>" class="thesiadm-ad-image">
                <?php endif; ?>
            </div>
            <?php
            return ob_get_clean();
        }
        
        /**
         * Display code ad
         */
        private function display_code_ad($ad) {
            $ad_code = get_post_meta($ad->ID, '_thesiadm_ad_code', true);
            
            if (!$ad_code) {
                return '';
            }
            
            ob_start();
            ?>
            <div class="thesiadm-ad-wrapper thesiadm-code-ad" data-ad-id="<?php echo esc_attr($ad->ID); ?>">
                <?php echo $ad_code; ?>
            </div>
            <?php
            return ob_get_clean();
        }
        
        /**
         * Render ads for a position
         */
        public function render_ads($position) {
            $ads = $this->get_ads_for_position($position);
            
            if (empty($ads)) {
                return '';
            }
            
            $output = '<div class="thesiadm-position-wrapper thesiadm-position-' . esc_attr($position) . '">';
            
            foreach ($ads as $ad) {
                $output .= $this->display_ad($ad);
            }
            
            $output .= '</div>';
            
            return $output;
        }
    }
}
