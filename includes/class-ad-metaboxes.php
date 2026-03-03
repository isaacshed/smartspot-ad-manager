<?php
/**
 * Ad Metaboxes Class
 * Handles all metaboxes for ad post type
 */

if (!defined('ABSPATH')) {
    exit;
}

class THESIADM_Metaboxes {
    
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
            esc_html__('URL Targeting', 'smartspot-ad-manager'),
            array($this, 'url_targeting_metabox'),
            'thesiadm_ad',
            'normal',
            'high'
        );
        
        add_meta_box(
            'thesiadm_ad_settings',
            esc_html__('Ad Settings', 'smartspot-ad-manager'),
            array($this, 'ad_settings_metabox'),
            'thesiadm_ad',
            'normal',
            'high'
        );
        
        add_meta_box(
            'thesiadm_device_targeting',
            esc_html__('Device Targeting', 'smartspot-ad-manager'),
            array($this, 'device_targeting_metabox'),
            'thesiadm_ad',
            'side',
            'default'
        );
        
        add_meta_box(
            'thesiadm_ad_code',
            esc_html__('Ad Code / HTML', 'smartspot-ad-manager'),
            array($this, 'ad_code_metabox'),
            'thesiadm_ad',
            'normal',
            'default'
        );
        
        add_meta_box(
            'thesiadm_ad_preview',
            esc_html__('Ad Preview', 'smartspot-ad-manager'),
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
            <p class="description"><?php esc_html_e('Specify which pages this ad should appear on. Enter one URL per line (relative paths work best).', 'smartspot-ad-manager'); ?></p>
            
            <table class="form-table">
                <tr>
                    <th><label for="thesiadm_url_match_type"><?php esc_html_e('Matching Type', 'smartspot-ad-manager'); ?></label></th>
                    <td>
                        <select name="thesiadm_url_match_type" id="thesiadm_url_match_type" style="width: 100%;">
                            <option value="exact" <?php selected($url_match_type, 'exact'); ?>><?php esc_html_e('Exact Match', 'smartspot-ad-manager'); ?></option>
                            <option value="contains" <?php selected($url_match_type, 'contains'); ?>><?php esc_html_e('Contains (Pattern Match)', 'smartspot-ad-manager'); ?></option>
                            <option value="starts_with" <?php selected($url_match_type, 'starts_with'); ?>><?php esc_html_e('Starts With', 'smartspot-ad-manager'); ?></option>
                        </select>
                        <p class="description">
                            <strong><?php esc_html_e('Exact:', 'smartspot-ad-manager'); ?></strong> <?php esc_html_e('/about-us/ (only this exact URL)', 'smartspot-ad-manager'); ?><br>
                            <strong><?php esc_html_e('Contains:', 'smartspot-ad-manager'); ?></strong> <?php esc_html_e('/blog/ (matches /blog/, /blog/post-1/, /my-blog/, etc.)', 'smartspot-ad-manager'); ?><br>
                            <strong><?php esc_html_e('Starts With:', 'smartspot-ad-manager'); ?></strong> <?php esc_html_e('/blog/ (matches /blog/, /blog/post-1/, /blog/category/tech/, etc.)', 'smartspot-ad-manager'); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th><label for="thesiadm_target_urls"><?php esc_html_e('Target URLs', 'smartspot-ad-manager'); ?></label></th>
                    <td>
                        <textarea name="thesiadm_target_urls" id="thesiadm_target_urls" rows="10" style="width: 100%; font-family: monospace;" placeholder="<?php esc_attr_e('/\n/blog/\n/products/\n/contact-us/', 'smartspot-ad-manager'); ?>"><?php echo esc_textarea($target_urls); ?></textarea>
                        <p class="description">
                            <?php esc_html_e('Enter URLs (one per line). Examples:', 'smartspot-ad-manager'); ?><br>
                            <code>/</code> <?php esc_html_e('(homepage)', 'smartspot-ad-manager'); ?><br>
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
        
        // Check if Pro
        $is_pro = function_exists('thesiadm_is_pro') ? thesiadm_is_pro() : false;
        $upgrade_url = function_exists('thesiadm_get_upgrade_url') ? thesiadm_get_upgrade_url() : '#';
        ?>
        
        <div class="thesiadm-metabox">
            <table class="form-table">
                <!-- AD POSITION with Pro restrictions -->
                <tr>
                    <th><label for="thesiadm_ad_position"><?php esc_html_e('Ad Position', 'smartspot-ad-manager'); ?></label></th>
                    <td>
                        <select name="thesiadm_ad_position" id="thesiadm_ad_position" style="width: 100%;">
                            <!-- Free Positions -->
                            <optgroup label="<?php esc_attr_e('Free Positions', 'smartspot-ad-manager'); ?>">
                                <option value="before-content" <?php selected($ad_position, 'before-content'); ?>>
                                    <?php esc_html_e('Before Content', 'smartspot-ad-manager'); ?>
                                </option>
                                <option value="after-content" <?php selected($ad_position, 'after-content'); ?>>
                                    <?php esc_html_e('After Content', 'smartspot-ad-manager'); ?>
                                </option>
                                <option value="sidebar" <?php selected($ad_position, 'sidebar'); ?>>
                                    <?php esc_html_e('Sidebar', 'smartspot-ad-manager'); ?>
                                </option>
                            </optgroup>
                            
                            <!-- Pro Positions -->
                            <optgroup label="<?php echo !$is_pro ? esc_attr__('Pro Positions [Unlock with Pro]', 'smartspot-ad-manager') : esc_attr__('Pro Positions', 'smartspot-ad-manager'); ?>">
                                <option value="header" <?php selected($ad_position, 'header'); ?> <?php echo !$is_pro ? 'disabled' : ''; ?>>
                                    <?php esc_html_e('Header', 'smartspot-ad-manager'); ?> <?php if (!$is_pro) echo '[PRO]'; ?>
                                </option>
                                <option value="footer" <?php selected($ad_position, 'footer'); ?> <?php echo !$is_pro ? 'disabled' : ''; ?>>
                                    <?php esc_html_e('Footer', 'smartspot-ad-manager'); ?> <?php if (!$is_pro) echo '[PRO]'; ?>
                                </option>
                                <option value="custom-1" <?php selected($ad_position, 'custom-1'); ?> <?php echo !$is_pro ? 'disabled' : ''; ?>>
                                    <?php esc_html_e('Custom Position 1', 'smartspot-ad-manager'); ?> <?php if (!$is_pro) echo '[PRO]'; ?>
                                </option>
                                <option value="custom-2" <?php selected($ad_position, 'custom-2'); ?> <?php echo !$is_pro ? 'disabled' : ''; ?>>
                                    <?php esc_html_e('Custom Position 2', 'smartspot-ad-manager'); ?> <?php if (!$is_pro) echo '[PRO]'; ?>
                                </option>
                            </optgroup>
                        </select>
                        
                        <?php if (!$is_pro): ?>
                            <div style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 12px; margin-top: 10px; border-radius: 4px;">
                                <p style="margin: 0 0 8px 0;">
                                    <strong>🔒 <?php esc_html_e('Pro Feature:', 'smartspot-ad-manager'); ?></strong> 
                                    <?php esc_html_e('Header, Footer & Custom Positions are only available in Pro.', 'smartspot-ad-manager'); ?>
                                </p>
                        <p style="margin: 0;">
                            <a href="<?php echo esc_url($upgrade_url); ?>" class="button button-primary button-small">
                                <?php esc_html_e('Upgrade to Pro for $20 - Onetime →', 'smartspot-ad-manager'); ?>
                            </a>
                        </p>
                            </div>
                        <?php else: ?>
                            <p class="description"><?php esc_html_e('Choose where this ad should appear. Use shortcode or function call for custom positions.', 'smartspot-ad-manager'); ?></p>
                        <?php endif; ?>
                    </td>
                </tr>
                
                <!-- AD TYPE with Pro restrictions -->
                <tr>
                    <th><label for="thesiadm_ad_type"><?php esc_html_e('Ad Type', 'smartspot-ad-manager'); ?></label></th>
                    <td>
                        <select name="thesiadm_ad_type" id="thesiadm_ad_type" style="width: 100%;">
                            <option value="image" <?php selected($ad_type, 'image'); ?>>
                                <?php esc_html_e('Image Ad', 'smartspot-ad-manager'); ?>
                            </option>
                            <option value="code" <?php selected($ad_type, 'code'); ?> <?php echo !$is_pro ? 'disabled' : ''; ?>>
                                <?php esc_html_e('Custom Code (AdSense, HTML, JavaScript)', 'smartspot-ad-manager'); ?>
                                <?php if (!$is_pro) echo ' [PRO]'; ?>
                            </option>
                        </select>
                        
                        <?php if (!$is_pro): ?>
                            <div style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 12px; margin-top: 10px; border-radius: 4px;">
                                <p style="margin: 0 0 8px 0;">
                                    <strong>🔒 <?php esc_html_e('Pro Feature:', 'smartspot-ad-manager'); ?></strong> 
                                    <?php esc_html_e('Custom Code ads (AdSense, HTML, JavaScript) are only available in Pro.', 'smartspot-ad-manager'); ?>
                                </p>
                                <p style="margin: 0 0 8px 0; font-size: 13px; color: #666;">
                                    <?php esc_html_e('Unlock the ability to add Google AdSense, custom HTML, and JavaScript widgets to monetize your site.', 'smartspot-ad-manager'); ?>
                                </p>
                                <p style="margin: 0;">
                                    <a href="<?php echo esc_url($upgrade_url); ?>" class="button button-primary button-small">
                                        <?php esc_html_e('Upgrade to Pro for $20 - Onetime →', 'smartspot-ad-manager'); ?>
                                    </a>
                                </p>
                            </div>
                        <?php else: ?>
                            <p class="description"><?php esc_html_e('Image ads use the Featured Image. Code ads use the "Ad Code" box below.', 'smartspot-ad-manager'); ?></p>
                        <?php endif; ?>
                    </td>
                </tr>
                
                <tr class="thesiadm-image-field" style="<?php echo $ad_type === 'code' ? 'display:none;' : ''; ?>">
                    <th><label for="thesiadm_ad_link"><?php esc_html_e('Ad Link URL', 'smartspot-ad-manager'); ?></label></th>
                    <td>
                        <input type="url" name="thesiadm_ad_link" id="thesiadm_ad_link" value="<?php echo esc_url($ad_link); ?>" style="width: 100%;" placeholder="https://example.com">
                        <p class="description"><?php esc_html_e('Where should users go when they click the image ad?', 'smartspot-ad-manager'); ?></p>
                    </td>
                </tr>
                
                <tr class="thesiadm-image-field" style="<?php echo $ad_type === 'code' ? 'display:none;' : ''; ?>">
                    <th><label for="thesiadm_open_new_tab"><?php esc_html_e('Open in New Tab', 'smartspot-ad-manager'); ?></label></th>
                    <td>
                        <label>
                            <input type="checkbox" name="thesiadm_open_new_tab" id="thesiadm_open_new_tab" value="1" <?php checked($open_new_tab, '1'); ?>>
                            <?php esc_html_e('Open link in new tab/window', 'smartspot-ad-manager'); ?>
                        </label>
                    </td>
                </tr>
                
                <tr>
                    <th><label for="thesiadm_ad_priority"><?php esc_html_e('Priority', 'smartspot-ad-manager'); ?></label></th>
                    <td>
                        <input type="number" name="thesiadm_ad_priority" id="thesiadm_ad_priority" value="<?php echo esc_attr($ad_priority); ?>" min="1" max="100" style="width: 100px;">
                        <p class="description"><?php esc_html_e('Lower number = higher priority. If multiple ads match, highest priority shows first.', 'smartspot-ad-manager'); ?></p>
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
        if (!is_array($target_devices) || empty($target_devices)) {
            $target_devices = array('desktop', 'mobile', 'tablet');
        }
        
        // Check if Pro
        $is_pro = function_exists('thesiadm_is_pro') ? thesiadm_is_pro() : false;
        $upgrade_url = function_exists('thesiadm_get_upgrade_url') ? thesiadm_get_upgrade_url() : '#';
        ?>
        
        <div class="thesiadm-metabox thesiadm-device-metabox">
            <?php if (!$is_pro): ?>
                <div style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 12px; margin-bottom: 15px; border-radius: 4px;">
                    <p style="margin: 0 0 8px 0; font-weight: 600;">
                        🔒 <?php esc_html_e('Pro Feature', 'smartspot-ad-manager'); ?>
                    </p>
                    <p style="margin: 0 0 8px 0; font-size: 13px;">
                        <?php esc_html_e('Granular device targeting is a Pro feature. Free version shows ads on all devices.', 'smartspot-ad-manager'); ?>
                    </p>
                    <p style="margin: 0 0 8px 0; font-size: 13px; color: #666;">
                        <?php esc_html_e('Show different ads on desktop, mobile, or tablet to optimize for each screen size.', 'smartspot-ad-manager'); ?>
                    </p>
                            <a href="<?php echo esc_url($upgrade_url); ?>" class="button button-primary button-small">
                                        <?php esc_html_e('Upgrade to Pro for $20 - Onetime →', 'smartspot-ad-manager'); ?>
                    </a>
                </div>
            <?php endif; ?>
            
            <p class="description" style="margin-bottom: 15px;">
                <?php if ($is_pro): ?>
                    <?php esc_html_e('Select which devices should display this ad:', 'smartspot-ad-manager'); ?>
                <?php else: ?>
                    <?php esc_html_e('Free version displays ads on all devices. Upgrade to Pro for granular control:', 'smartspot-ad-manager'); ?>
                <?php endif; ?>
            </p>
            
            <div style="padding: 10px 0; <?php echo !$is_pro ? 'opacity: 0.6; pointer-events: none;' : ''; ?>">
                <label style="display: flex; align-items: center; padding: 8px; margin-bottom: 8px; border: 1px solid #ddd; border-radius: 4px; <?php echo !$is_pro ? 'background: #f5f5f5;' : 'cursor: pointer;'; ?>">
                    <input type="checkbox" name="thesiadm_target_devices[]" value="desktop" 
                           <?php checked(in_array('desktop', $target_devices)); ?> 
                           <?php echo !$is_pro ? 'checked disabled' : ''; ?>>
                    <span class="dashicons dashicons-desktop" style="color: #2271b1; margin: 0 8px;"></span>
                    <strong><?php esc_html_e('Desktop', 'smartspot-ad-manager'); ?></strong>
                    <?php if (!$is_pro): ?>
                        <span style="margin-left: auto; color: #999; font-size: 11px; text-transform: uppercase; background: #e0e0e0; padding: 2px 6px; border-radius: 3px;">
                            <?php esc_html_e('Locked', 'smartspot-ad-manager'); ?>
                        </span>
                    <?php endif; ?>
                </label>
                
                <label style="display: flex; align-items: center; padding: 8px; margin-bottom: 8px; border: 1px solid #ddd; border-radius: 4px; <?php echo !$is_pro ? 'background: #f5f5f5;' : 'cursor: pointer;'; ?>">
                    <input type="checkbox" name="thesiadm_target_devices[]" value="mobile" 
                           <?php checked(in_array('mobile', $target_devices)); ?> 
                           <?php echo !$is_pro ? 'checked disabled' : ''; ?>>
                    <span class="dashicons dashicons-smartphone" style="color: #2271b1; margin: 0 8px;"></span>
                    <strong><?php esc_html_e('Mobile', 'smartspot-ad-manager'); ?></strong>
                    <?php if (!$is_pro): ?>
                        <span style="margin-left: auto; color: #999; font-size: 11px; text-transform: uppercase; background: #e0e0e0; padding: 2px 6px; border-radius: 3px;">
                            <?php esc_html_e('Locked', 'smartspot-ad-manager'); ?>
                        </span>
                    <?php endif; ?>
                </label>
                
                <label style="display: flex; align-items: center; padding: 8px; margin-bottom: 8px; border: 1px solid #ddd; border-radius: 4px; <?php echo !$is_pro ? 'background: #f5f5f5;' : 'cursor: pointer;'; ?>">
                    <input type="checkbox" name="thesiadm_target_devices[]" value="tablet" 
                           <?php checked(in_array('tablet', $target_devices)); ?> 
                           <?php echo !$is_pro ? 'checked disabled' : ''; ?>>
                    <span class="dashicons dashicons-tablet" style="color: #2271b1; margin: 0 8px;"></span>
                    <strong><?php esc_html_e('Tablet', 'smartspot-ad-manager'); ?></strong>
                    <?php if (!$is_pro): ?>
                        <span style="margin-left: auto; color: #999; font-size: 11px; text-transform: uppercase; background: #e0e0e0; padding: 2px 6px; border-radius: 3px;">
                            <?php esc_html_e('Locked', 'smartspot-ad-manager'); ?>
                        </span>
                    <?php endif; ?>
                </label>
            </div>
            
            <?php if ($is_pro): ?>
                <p class="description" style="margin-top: 10px; padding-top: 10px; border-top: 1px solid #ddd;">
                    <strong><?php esc_html_e('Note:', 'smartspot-ad-manager'); ?></strong> 
                    <?php esc_html_e('Select at least one device. If none selected, ad will display on all devices.', 'smartspot-ad-manager'); ?>
                </p>
            <?php endif; ?>
        </div>
        
        <?php
    }
    
    /**
     * Ad Code Metabox
     */
    public function ad_code_metabox($post) {
        $ad_code = get_post_meta($post->ID, '_thesiadm_ad_code', true);
        $ad_type = get_post_meta($post->ID, '_thesiadm_ad_type', true);
        
        // Check if Pro
        $is_pro = function_exists('thesiadm_is_pro') ? thesiadm_is_pro() : false;
        $upgrade_url = function_exists('thesiadm_get_upgrade_url') ? thesiadm_get_upgrade_url() : '#';
        ?>
        
        <div class="thesiadm-metabox">
            <?php if (!$is_pro): ?>
                <div style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 12px; margin-bottom: 15px; border-radius: 4px;">
                    <p style="margin: 0 0 8px 0; font-weight: 600;">
                        🔒 <?php esc_html_e('Pro Feature', 'smartspot-ad-manager'); ?>
                    </p>
                    <p style="margin: 0 0 8px 0; font-size: 13px;">
                        <?php esc_html_e('Custom ad code (AdSense, HTML, JavaScript) is only available in Pro.', 'smartspot-ad-manager'); ?>
                    </p>
                    <a href="<?php echo esc_url($upgrade_url); ?>" class="button button-primary button-small">
                        <?php esc_html_e('Upgrade to Pro for $20 - Onetime →', 'smartspot-ad-manager'); ?>
                    </a>
                </div>
            <?php endif; ?>
            
            <p class="description"><?php esc_html_e('Paste your ad code here (Google AdSense, custom HTML, JavaScript, etc.). Only used if Ad Type is set to "Custom Code".', 'smartspot-ad-manager'); ?></p>
            
            <textarea name="thesiadm_ad_code" id="thesiadm_ad_code" rows="10" 
                      style="width: 100%; font-family: monospace; <?php echo !$is_pro ? 'background: #f5f5f5; opacity: 0.6;' : ''; ?>" 
                      placeholder="<?php esc_attr_e('<!-- Your ad code here -->', 'smartspot-ad-manager'); ?>"
                      <?php echo !$is_pro ? 'readonly' : ''; ?>><?php echo esc_textarea($ad_code); ?></textarea>
            
            <?php if ($is_pro): ?>
                <p class="description" style="margin-top: 10px;">
                    <strong><?php esc_html_e('Tip:', 'smartspot-ad-manager'); ?></strong> 
                    <?php esc_html_e('For Google AdSense, paste the entire script tag provided by Google.', 'smartspot-ad-manager'); ?>
                </p>
            <?php endif; ?>
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
                <p class="description"><?php esc_html_e('Custom code preview not available. Preview will show on frontend.', 'smartspot-ad-manager'); ?></p>
            <?php else: ?>
                <p class="description"><?php esc_html_e('Set a Featured Image to see preview.', 'smartspot-ad-manager'); ?></p>
            <?php endif; ?>
            
            <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #ddd;">
                <p class="description">
                    <strong><?php esc_html_e('Shortcode:', 'smartspot-ad-manager'); ?></strong><br>
                    <code style="user-select: all;">[thesiadm_ads position="<?php echo esc_attr(get_post_meta($post->ID, '_thesiadm_ad_position', true) ?: 'custom-1'); ?>"]</code>
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
        if (!isset($_POST['thesiadm_metabox_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['thesiadm_metabox_nonce'])), 'thesiadm_save_metaboxes')) {
            return;
        }
        
        // Check autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        // Check permissions
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        // Check if Pro (for restrictions)
        $is_pro = function_exists('thesiadm_is_pro') ? thesiadm_is_pro() : false;
        
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
        
        // Save ad position (ENFORCE FREE RESTRICTIONS)
        if (isset($_POST['thesiadm_ad_position'])) {
            $position = sanitize_text_field(wp_unslash($_POST['thesiadm_ad_position']));
            $free_positions = array('before-content', 'after-content', 'sidebar');
            
            if (!$is_pro && !in_array($position, $free_positions)) {
                // Free users trying to use Pro position - default to before-content
                update_post_meta($post_id, '_thesiadm_ad_position', 'before-content');
            } else {
                update_post_meta($post_id, '_thesiadm_ad_position', $position);
            }
        }
        
        // Save ad type (ENFORCE FREE RESTRICTIONS)
        if (isset($_POST['thesiadm_ad_type'])) {
            $ad_type = sanitize_text_field(wp_unslash($_POST['thesiadm_ad_type']));
            
            if (!$is_pro && $ad_type === 'code') {
                // Free users can only use image ads
                update_post_meta($post_id, '_thesiadm_ad_type', 'image');
            } else {
                update_post_meta($post_id, '_thesiadm_ad_type', $ad_type);
            }
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
        
        // Save device targeting (ENFORCE FREE RESTRICTIONS)
        if (!$is_pro) {
            update_post_meta($post_id, '_thesiadm_target_devices', array('desktop', 'mobile', 'tablet'));
        } else {
            if (isset($_POST['thesiadm_target_devices']) && is_array($_POST['thesiadm_target_devices'])) {
                $devices = array_map(
                    'sanitize_text_field',
                    wp_unslash($_POST['thesiadm_target_devices'])
                );
                $allowed_devices = array('desktop', 'mobile', 'tablet');
                $devices = array_intersect($devices, $allowed_devices);
                update_post_meta($post_id, '_thesiadm_target_devices', $devices);
            } else {
                update_post_meta($post_id, '_thesiadm_target_devices', array('desktop', 'mobile', 'tablet'));
            }
        }
        
        // Save ad code (ENFORCE FREE RESTRICTIONS)
        if (isset($_POST['thesiadm_ad_code'])) {
            if (!$is_pro) {
                // Free users cannot save ad code
                update_post_meta($post_id, '_thesiadm_ad_code', '');
            } else {
                // Pro users can save ad code
                // Allow more HTML tags for ad code
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
    }
    
    /**
     * Set custom columns
     */
    public function set_custom_columns($columns) {
        $new_columns = array();
        $new_columns['cb'] = $columns['cb'];
        $new_columns['title'] = $columns['title'];
        $new_columns['ad_position'] = esc_html__('Position', 'smartspot-ad-manager');
        $new_columns['target_urls'] = esc_html__('Target URLs', 'smartspot-ad-manager');
        $new_columns['ad_type'] = esc_html__('Type', 'smartspot-ad-manager');
        $new_columns['target_devices'] = esc_html__('Devices', 'smartspot-ad-manager');
        $new_columns['ad_priority'] = esc_html__('Priority', 'smartspot-ad-manager');
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
                    $label = ($count === 1) ? esc_html__('URL', 'smartspot-ad-manager') : esc_html__('URLs', 'smartspot-ad-manager');
                    echo '<strong>' . esc_html(number_format_i18n($count) . ' ' . $label) . '</strong><br>';
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
                    echo '<span class="thesiadm-badge thesiadm-badge-code">' . esc_html__('Custom Code', 'smartspot-ad-manager') . '</span>';
                } else {
                    echo '<span class="thesiadm-badge thesiadm-badge-image">' . esc_html__('Image', 'smartspot-ad-manager') . '</span>';
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
                            echo wp_kses_post($device_icons[$device]) . ' ';
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
