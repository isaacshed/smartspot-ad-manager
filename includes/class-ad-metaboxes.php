<?php
/**
 * Ad Metaboxes Class
 * Handles all metaboxes for ad post type
 */

if (! defined('ABSPATH')) {
    exit;
}

class THESIADM_Pro_Metaboxes {
    
    public function __construct() {
        add_action('add_meta_boxes', array($this, 'add_metaboxes'));
        add_action('save_post_thesiadm_ad', array($this, 'save_metaboxes'), 10, 2);
        add_filter('manage_thesiadm_ad_posts_columns', array($this, 'set_custom_columns'));
        add_action('manage_thesiadm_ad_posts_custom_column', array($this, 'custom_column_content'), 10, 2);
        add_filter('manage_edit-thesiadm_ad_sortable_columns', array($this, 'sortable_columns'));
    }
    
    /**
     * Add metaboxes
     */
    public function add_metaboxes() {
        add_meta_box(
            'thesiadm_url_targeting',
            __('URL Targeting', 'smartspot-ad-manager'),
            array($this, 'url_targeting_metabox'),
            'thesiadm_ad',
            'normal',
            'high'
        );
        
        add_meta_box(
            'thesiadm_ad_settings',
            __('Ad Settings', 'smartspot-ad-manager'),
            array($this, 'ad_settings_metabox'),
            'thesiadm_ad',
            'normal',
            'high'
        );
        
        add_meta_box(
            'thesiadm_device_targeting',
            __('Device Targeting', 'smartspot-ad-manager'),
            array($this, 'device_targeting_metabox'),
            'thesiadm_ad',
            'side',
            'default'
        );
        
        add_meta_box(
            'thesiadm_ad_code',
            __('Ad Code / HTML', 'smartspot-ad-manager'),
            array($this, 'ad_code_metabox'),
            'thesiadm_ad',
            'normal',
            'default'
        );
        
        add_meta_box(
            'thesiadm_ad_preview',
            __('Ad Preview', 'smartspot-ad-manager'),
            array($this, 'ad_preview_metabox'),
            'thesiadm_ad',
            'side',
            'default'
        );
    }
    
    /**
     * URL Targeting Metabox
     */
    public function url_targeting_metabox($post) {
        wp_nonce_field('thesiadm_save_metaboxes', 'thesiadm_metabox_nonce');
        
        $target_urls = get_post_meta($post->ID, '_thesiadm_target_urls', true);
        $url_match_type = get_post_meta($post->ID, '_thesiadm_url_match_type', true);
        
        if (!$url_match_type) {
            $url_match_type = 'exact';
        }
        ?>
        
        <div class="thesiadm-metabox">
            <p class="description"><?php _e('Specify which pages this ad should appear on. Enter one URL per line (relative paths work best).', 'smartspot-ad-manager'); ?></p>
            
            <table class="form-table">
                <tr>
                    <th><label for="thesiadm_url_match_type"><?php _e('Matching Type', 'smartspot-ad-manager'); ?></label></th>
                    <td>
                        <select name="thesiadm_url_match_type" id="thesiadm_url_match_type" style="width: 100%;">
                            <option value="exact" <?php selected($url_match_type, 'exact'); ?>><?php _e('Exact Match', 'smartspot-ad-manager'); ?></option>
                            <option value="contains" <?php selected($url_match_type, 'contains'); ?>><?php _e('Contains (Pattern Match)', 'smartspot-ad-manager'); ?></option>
                            <option value="starts_with" <?php selected($url_match_type, 'starts_with'); ?>><?php _e('Starts With', 'smartspot-ad-manager'); ?></option>
                        </select>
                        <p class="description">
                            <strong><?php _e('Exact:', 'smartspot-ad-manager'); ?></strong> <?php _e('/about-us/ (only this exact URL)', 'smartspot-ad-manager'); ?><br>
                            <strong><?php _e('Contains:', 'smartspot-ad-manager'); ?></strong> <?php _e('/blog/ (matches /blog/, /blog/post-1/, /my-blog/, etc.)', 'smartspot-ad-manager'); ?><br>
                            <strong><?php _e('Starts With:', 'smartspot-ad-manager'); ?></strong> <?php _e('/blog/ (matches /blog/, /blog/post-1/, /blog/category/tech/, etc.)', 'smartspot-ad-manager'); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th><label for="thesiadm_target_urls"><?php _e('Target URLs', 'smartspot-ad-manager'); ?></label></th>
                    <td>
                        <textarea name="thesiadm_target_urls" id="thesiadm_target_urls" rows="10" style="width: 100%; font-family: monospace;" placeholder="<?php esc_attr_e('/\n/blog/\n/products/\n/contact-us/', 'smartspot-ad-manager'); ?>"><?php echo esc_textarea($target_urls); ?></textarea>
                        <p class="description">
                            <?php _e('Enter URLs (one per line). Examples:', 'smartspot-ad-manager'); ?><br>
                            <code>/</code> <?php _e('(homepage)', 'smartspot-ad-manager'); ?><br>
                            <code>/blog/</code><br>
                            <code>/products/electronics/</code><br>
                            <code>/contact-us/</code>
                        </p>
                    </td>
                </tr>
            </table>
        </div>
        
        <?php
    }
    
    /**
     * Ad Settings Metabox
     */
    public function ad_settings_metabox($post) {
        $ad_position = get_post_meta($post->ID, '_thesiadm_ad_position', true);
        $ad_type = get_post_meta($post->ID, '_thesiadm_ad_type', true);
        $ad_link = get_post_meta($post->ID, '_thesiadm_ad_link', true);
        $ad_priority = get_post_meta($post->ID, '_thesiadm_ad_priority', true);
        $open_new_tab = get_post_meta($post->ID, '_thesiadm_open_new_tab', true);
        
        if (!$ad_type) {
            $ad_type = 'image';
        }
        
        if (!$ad_priority) {
            $ad_priority = 10;
        }
        
        ?>
        
        <div class="thesiadm-metabox">
            <table class="form-table">
                <tr>
                    <th><label for="thesiadm_ad_position"><?php _e('Ad Position', 'smartspot-ad-manager'); ?></label></th>
                    <td>
                        <select name="thesiadm_ad_position" id="thesiadm_ad_position" style="width: 100%;">
                            <option value="before-content" <?php selected($ad_position, 'before-content'); ?>>
                                <?php _e('Before Content', 'smartspot-ad-manager'); ?>
                            </option>
                            <option value="after-content" <?php selected($ad_position, 'after-content'); ?>>
                                <?php _e('After Content', 'smartspot-ad-manager'); ?>
                            </option>
                            <option value="sidebar" <?php selected($ad_position, 'sidebar'); ?>>
                                <?php _e('Sidebar', 'smartspot-ad-manager'); ?>
                            </option>
                            <option value="header" <?php selected($ad_position, 'header'); ?>>
                                <?php _e('Header', 'smartspot-ad-manager'); ?>
                            </option>
                            <option value="footer" <?php selected($ad_position, 'footer'); ?>>
                                <?php _e('Footer', 'smartspot-ad-manager'); ?>
                            </option>
                            <option value="custom-1" <?php selected($ad_position, 'custom-1'); ?>>
                                <?php _e('Custom Position 1', 'smartspot-ad-manager'); ?>
                            </option>
                            <option value="custom-2" <?php selected($ad_position, 'custom-2'); ?>>
                                <?php _e('Custom Position 2', 'smartspot-ad-manager'); ?>
                            </option>
                        </select>
                        
                        <p class="description">
                            <?php _e('Choose where this ad should appear. Use shortcode or function call for custom positions.', 'smartspot-ad-manager'); ?>
                        </p>
                    </td>
                </tr>
                
                <tr>
                    <th><label for="thesiadm_ad_type"><?php _e('Ad Type', 'smartspot-ad-manager'); ?></label></th>
                    <td>
                        <select name="thesiadm_ad_type" id="thesiadm_ad_type" style="width: 100%;">
                            <option value="image" <?php selected($ad_type, 'image'); ?>>
                                <?php _e('Image Ad', 'smartspot-ad-manager'); ?>
                            </option>
                            <option value="code" <?php selected($ad_type, 'code'); ?>>
                                <?php _e('Custom Code (AdSense, HTML, JavaScript)', 'smartspot-ad-manager'); ?>
                            </option>
                        </select>
                        
                        <p class="description">
                            <?php _e('Image ads use the Featured Image. Code ads use the "Ad Code" box below.', 'smartspot-ad-manager'); ?>
                        </p>
                    </td>
                </tr>
                
                <tr class="thesiadm-image-field" style="<?php echo $ad_type === 'code' ? 'display:none;' : ''; ?>">
                    <th><label for="thesiadm_ad_link"><?php _e('Ad Link URL', 'smartspot-ad-manager'); ?></label></th>
                    <td>
                        <input type="url" name="thesiadm_ad_link" id="thesiadm_ad_link" value="<?php echo esc_url($ad_link); ?>" style="width: 100%;" placeholder="https://example.com">
                        <p class="description"><?php _e('Where should users go when they click the image ad?', 'smartspot-ad-manager'); ?></p>
                    </td>
                </tr>
                
                <tr class="thesiadm-image-field" style="<?php echo $ad_type === 'code' ? 'display:none;' : ''; ?>">
                    <th><label for="thesiadm_open_new_tab"><?php _e('Open in New Tab', 'smartspot-ad-manager'); ?></label></th>
                    <td>
                        <label>
                            <input type="checkbox" name="thesiadm_open_new_tab" id="thesiadm_open_new_tab" value="1" <?php checked($open_new_tab, '1'); ?>>
                            <?php _e('Open link in new tab/window', 'smartspot-ad-manager'); ?>
                        </label>
                    </td>
                </tr>
                
                <tr>
                    <th><label for="thesiadm_ad_priority"><?php _e('Priority', 'smartspot-ad-manager'); ?></label></th>
                    <td>
                        <input type="number" name="thesiadm_ad_priority" id="thesiadm_ad_priority" value="<?php echo esc_attr($ad_priority); ?>" min="1" max="100" style="width: 100px;">
                        <p class="description"><?php _e('Lower number = higher priority. If multiple ads match, highest priority shows first.', 'smartspot-ad-manager'); ?></p>
                    </td>
                </tr>
            </table>
        </div>
        
        <?php
    }
    
    /**
     * Device Targeting Metabox
     */
    public function device_targeting_metabox($post) {
        $target_devices = get_post_meta($post->ID, '_thesiadm_target_devices', true);
        
        // Default to all devices if not set
        if (! is_array($target_devices) || empty($target_devices)) {
            $target_devices = array('desktop', 'mobile', 'tablet');
        }
        ?>
        
        <div class="thesiadm-metabox thesiadm-device-metabox">
            <p class="description" style="margin-bottom: 15px;">
                <?php _e('Select which devices should display this ad:', 'smartspot-ad-manager'); ?>
            </p>
            
            <div style="padding: 10px 0;">
                <label style="display: flex; align-items: center; padding: 8px; margin-bottom: 8px; border: 1px solid #ddd; border-radius: 4px; cursor: pointer;">
                    <input type="checkbox" name="thesiadm_target_devices[]" value="desktop" 
                           <?php checked(in_array('desktop', $target_devices)); ?>>
                    <span class="dashicons dashicons-desktop" style="color: #2271b1; margin: 0 8px;"></span>
                    <strong><?php _e('Desktop', 'smartspot-ad-manager'); ?></strong>
                </label>
                
                <label style="display: flex; align-items: center; padding: 8px; margin-bottom: 8px; border: 1px solid #ddd; border-radius: 4px; cursor: pointer;">
                    <input type="checkbox" name="thesiadm_target_devices[]" value="mobile" 
                           <?php checked(in_array('mobile', $target_devices)); ?>>
                    <span class="dashicons dashicons-smartphone" style="color: #2271b1; margin: 0 8px;"></span>
                    <strong><?php _e('Mobile', 'smartspot-ad-manager'); ?></strong>
                </label>
                
                <label style="display: flex; align-items: center; padding: 8px; margin-bottom: 8px; border: 1px solid #ddd; border-radius: 4px; cursor: pointer;">
                    <input type="checkbox" name="thesiadm_target_devices[]" value="tablet" 
                           <?php checked(in_array('tablet', $target_devices)); ?>>
                    <span class="dashicons dashicons-tablet" style="color: #2271b1; margin: 0 8px;"></span>
                    <strong><?php _e('Tablet', 'smartspot-ad-manager'); ?></strong>
                </label>
            </div>
            
            <p class="description" style="margin-top: 10px; padding-top: 10px; border-top: 1px solid #ddd;">
                <strong><?php _e('Note:', 'smartspot-ad-manager'); ?></strong> 
                <?php _e('Select at least one device. If none selected, ad will display on all devices.', 'smartspot-ad-manager'); ?>
            </p>
        </div>
        
        <?php
    }
    
    /**
     * Ad Code Metabox
     */
    public function ad_code_metabox($post) {
        $ad_code = get_post_meta($post->ID, '_thesiadm_ad_code', true);
        $ad_type = get_post_meta($post->ID, '_thesiadm_ad_type', true);
        ?>
        
        <div class="thesiadm-metabox">
            <p class="description">
                <?php _e('Paste your ad code here (Google AdSense, custom HTML, JavaScript, etc.). Only used if Ad Type is set to \"Custom Code\".', 'smartspot-ad-manager'); ?>
            </p>
            
            <textarea name="thesiadm_ad_code" id="thesiadm_ad_code" rows="10" 
                      style="width: 100%; font-family: monospace;" 
                      placeholder="<?php esc_attr_e('<!-- Your ad code here -->', 'smartspot-ad-manager'); ?>"><?php echo esc_textarea($ad_code); ?></textarea>
            
            <p class="description" style="margin-top: 10px;">
                <strong><?php _e('Tip:', 'smartspot-ad-manager'); ?></strong> 
                <?php _e('For Google AdSense, paste the entire script tag provided by Google.', 'smartspot-ad-manager'); ?>
            </p>
        </div>
        
        <?php
    }
    
    /**
     * Ad Preview Metabox
     */
    public function ad_preview_metabox($post) {
        $ad_type = get_post_meta($post->ID, '_thesiadm_ad_type', true);
        $image_url = get_the_post_thumbnail_url($post->ID, 'medium');
        
        ?>
        <div class="thesiadm-metabox thesiadm-preview-box">
            <?php if ($ad_type === 'image' && $image_url): ?>
                <div class="thesiadm-image-preview">
                    <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($post->post_title); ?>" style="max-width: 100%; height: auto; border-radius: 4px;">
                </div>
            <?php elseif ($ad_type === 'code'): ?>
                <p class="description"><?php _e('Custom code preview not available. Preview will show on frontend.', 'smartspot-ad-manager'); ?></p>
            <?php else: ?>
                <p class="description"><?php _e('Set a Featured Image to see preview.', 'smartspot-ad-manager'); ?></p>
            <?php endif; ?>
            
            <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #ddd;">
                <p class="description">
                    <strong><?php _e('Shortcode:', 'smartspot-ad-manager'); ?></strong><br>
                    <code style="user-select: all;">[smartspot_ad position="<?php echo esc_attr(get_post_meta($post->ID, '_thesiadm_ad_position', true) ?: 'custom-1'); ?>"]</code>
                </p>
            </div>
        </div>
        <?php
    }
    
    /**
     * Save metaboxes
     */
    public function save_metaboxes($post_id, $post) {
        // Check nonce
        if (
            ! isset($_POST['thesiadm_metabox_nonce']) ||
            ! wp_verify_nonce(
                sanitize_text_field(wp_unslash($_POST['thesiadm_metabox_nonce'])),
                'thesiadm_save_metaboxes'
            )
        ) {
            return;
        }
        
        // Check autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        // Check permissions
        if (! current_user_can('edit_post', $post_id)) {
            return;
        }
        
        // Save URL targeting
        if (isset($_POST['thesiadm_target_urls'])) {
            update_post_meta(
                $post_id,
                '_thesiadm_target_urls',
                sanitize_textarea_field(wp_unslash($_POST['thesiadm_target_urls']))
            );
        }
        
        if (isset($_POST['thesiadm_url_match_type'])) {
            update_post_meta(
                $post_id,
                '_thesiadm_url_match_type',
                sanitize_text_field(wp_unslash($_POST['thesiadm_url_match_type']))
            );
        }
        
        // Save ad position (all positions available in Pro)
        if (isset($_POST['thesiadm_ad_position'])) {
            $position = sanitize_text_field(wp_unslash($_POST['thesiadm_ad_position']));
            $allowed_positions = array(
                'before-content',
                'after-content',
                'sidebar',
                'header',
                'footer',
                'custom-1',
                'custom-2'
            );
            if (! in_array($position, $allowed_positions, true)) {
                $position = 'before-content';
            }
            update_post_meta($post_id, '_thesiadm_ad_position', $position);
        }
        
        // Save ad type
        if (isset($_POST['thesiadm_ad_type'])) {
            $ad_type = sanitize_text_field(wp_unslash($_POST['thesiadm_ad_type']));
            $allowed_types = array('image', 'code');
            if (! in_array($ad_type, $allowed_types, true)) {
                $ad_type = 'image';
            }
            update_post_meta($post_id, '_thesiadm_ad_type', $ad_type);
        }
        
        if (isset($_POST['thesiadm_ad_link'])) {
            update_post_meta(
                $post_id,
                '_thesiadm_ad_link',
                esc_url_raw(wp_unslash($_POST['thesiadm_ad_link']))
            );
        }
        
        if (isset($_POST['thesiadm_ad_priority'])) {
            update_post_meta(
                $post_id,
                '_thesiadm_ad_priority',
                absint(wp_unslash($_POST['thesiadm_ad_priority']))
            );
        }
        
        // Save checkbox
        $open_new_tab = isset($_POST['thesiadm_open_new_tab']) ? '1' : '0';
        update_post_meta($post_id, '_thesiadm_open_new_tab', $open_new_tab);
        
        // Save device targeting
        if (isset($_POST['thesiadm_target_devices']) && is_array($_POST['thesiadm_target_devices'])) {
            $devices = array_map(
                'sanitize_text_field',
                wp_unslash($_POST['thesiadm_target_devices'])
            );
            $allowed_devices = array('desktop', 'mobile', 'tablet');
            $devices         = array_intersect($devices, $allowed_devices);
            if (empty($devices)) {
                $devices = $allowed_devices;
            }
            update_post_meta($post_id, '_thesiadm_target_devices', $devices);
        } else {
            update_post_meta($post_id, '_thesiadm_target_devices', array('desktop', 'mobile', 'tablet'));
        }
        
        // Save ad code
        if (isset($_POST['thesiadm_ad_code'])) {
            $allowed_tags = wp_kses_allowed_html('post');
            $allowed_tags['script'] = array(
                'src' => true,
                'type' => true,
                'async' => true,
                'defer' => true,
                'crossorigin' => true,
            );
            $allowed_tags['iframe'] = array(
                'src' => true,
                'width' => true,
                'height' => true,
                'frameborder' => true,
                'allowfullscreen' => true,
                'style' => true,
                'class' => true,
            );
            $allowed_tags['ins'] = array(
                'class' => true,
                'style' => true,
                'data-ad-client' => true,
                'data-ad-slot' => true,
                'data-ad-format' => true,
                'data-full-width-responsive' => true,
            );
            
            update_post_meta(
                $post_id,
                '_thesiadm_ad_code',
                wp_kses(wp_unslash($_POST['thesiadm_ad_code']), $allowed_tags)
            );
        }
    }
    
    /**
     * Set custom columns
     */
    public function set_custom_columns($columns) {
        $new_columns = array();
        $new_columns['cb'] = $columns['cb'];
        $new_columns['title'] = $columns['title'];
        $new_columns['ad_position'] = __('Position', 'smartspot-ad-manager');
        $new_columns['target_urls'] = __('Target URLs', 'smartspot-ad-manager');
        $new_columns['ad_type'] = __('Type', 'smartspot-ad-manager');
        $new_columns['target_devices'] = __('Devices', 'smartspot-ad-manager');
        $new_columns['ad_priority'] = __('Priority', 'smartspot-ad-manager');
        $new_columns['date'] = $columns['date'];
        
        return $new_columns;
    }
    
    /**
     * Make columns sortable
     */
    public function sortable_columns($columns) {
        $columns['ad_priority'] = 'ad_priority';
        return $columns;
    }
    
    /**
     * Custom column content
     */
    public function custom_column_content($column, $post_id) {
        switch ($column) {
            case 'ad_position':
                $position = get_post_meta($post_id, '_thesiadm_ad_position', true);
                if ($position) {
                    $position_label = ucwords(str_replace('-', ' ', $position));
                    echo '<span class="thesiadm-badge thesiadm-badge-position">' . esc_html($position_label) . '</span>';
                } else {
                    echo '—';
                }
                break;
                
            case 'target_urls':
                $urls = get_post_meta($post_id, '_thesiadm_target_urls', true);
                $url_array = array_filter(array_map('trim', explode("\n", $urls)));
                $count = count($url_array);
                
                if ($count > 0) {
                    echo '<strong>' . sprintf(_n('%s URL', '%s URLs', $count, 'smartspot-ad-manager'), number_format_i18n($count)) . '</strong><br>';
                    echo '<small style="color: #666;">' . esc_html(implode(', ', array_slice($url_array, 0, 2))) . '</small>';
                    if ($count > 2) {
                        echo '<small style="color: #666;">...</small>';
                    }
                } else {
                    echo '—';
                }
                break;
                
            case 'ad_type':
                $type = get_post_meta($post_id, '_thesiadm_ad_type', true);
                if ($type === 'code') {
                    echo '<span class="thesiadm-badge thesiadm-badge-code">' . __('Custom Code', 'smartspot-ad-manager') . '</span>';
                } else {
                    echo '<span class="thesiadm-badge thesiadm-badge-image">' . __('Image', 'smartspot-ad-manager') . '</span>';
                }
                break;
                
            case 'target_devices':
                $devices = get_post_meta($post_id, '_thesiadm_target_devices', true);
                if (is_array($devices) && !empty($devices)) {
                    $device_icons = array(
                        'desktop' => '<span class="dashicons dashicons-desktop" title="' . esc_attr__('Desktop', 'smartspot-ad-manager') . '"></span>',
                        'mobile' => '<span class="dashicons dashicons-smartphone" title="' . esc_attr__('Mobile', 'smartspot-ad-manager') . '"></span>',
                        'tablet' => '<span class="dashicons dashicons-tablet" title="' . esc_attr__('Tablet', 'smartspot-ad-manager') . '"></span>'
                    );
                    
                    foreach ($devices as $device) {
                        if (isset($device_icons[$device])) {
                            echo $device_icons[$device] . ' ';
                        }
                    }
                } else {
                    echo '—';
                }
                break;
                
            case 'ad_priority':
                $priority = get_post_meta($post_id, '_thesiadm_ad_priority', true);
                echo $priority ? '<strong>' . esc_html($priority) . '</strong>' : '—';
                break;
        }
    }
}
