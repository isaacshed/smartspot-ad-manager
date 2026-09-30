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
            esc_html__('Ad Preview & Shortcode', 'smartspot-ad-manager'),
            array($this, 'ad_preview_metabox'),
            'thesiadm_ad',
            'side',
            'default'
        );
    }

    private function get_allowed_positions() {
        return array(
            'before-content' => __('Before Content', 'smartspot-ad-manager'),
            'after-content'  => __('After Content', 'smartspot-ad-manager'),
            'sidebar'        => __('Sidebar', 'smartspot-ad-manager'),
            'header'         => __('Header', 'smartspot-ad-manager'),
            'footer'         => __('Footer', 'smartspot-ad-manager'),
            'custom-1'       => __('Custom Position 1', 'smartspot-ad-manager'),
            'custom-2'       => __('Custom Position 2', 'smartspot-ad-manager'),
        );
    }

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
                        <textarea name="thesiadm_target_urls" id="thesiadm_target_urls" rows="10" style="width: 100%; font-family: monospace;" placeholder="<?php esc_attr_e('/&#10;/blog/&#10;/products/&#10;/contact-us/', 'smartspot-ad-manager'); ?>"><?php echo esc_textarea($target_urls); ?></textarea>
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

        if (!$ad_position) {
            $ad_position = 'before-content';
        }

        $positions = $this->get_allowed_positions();
        ?>

        <div class="thesiadm-metabox">
            <table class="form-table">
                <tr>
                    <th><label for="thesiadm_ad_position"><?php esc_html_e('Ad Position', 'smartspot-ad-manager'); ?></label></th>
                    <td>
                        <select name="thesiadm_ad_position" id="thesiadm_ad_position" style="width: 100%;">
                            <?php foreach ($positions as $value => $label): ?>
                                <option value="<?php echo esc_attr($value); ?>" <?php selected($ad_position, $value); ?>>
                                    <?php echo esc_html($label); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="description"><?php esc_html_e('Choose where this ad should appear. Use shortcode or function call for custom positions.', 'smartspot-ad-manager'); ?></p>
                    </td>
                </tr>

                <tr>
                    <th><label for="thesiadm_ad_type"><?php esc_html_e('Ad Type', 'smartspot-ad-manager'); ?></label></th>
                    <td>
                        <select name="thesiadm_ad_type" id="thesiadm_ad_type" style="width: 100%;">
                            <option value="image" <?php selected($ad_type, 'image'); ?>>
                                <?php esc_html_e('Image Ad', 'smartspot-ad-manager'); ?>
                            </option>
                            <option value="code" <?php selected($ad_type, 'code'); ?>>
                                <?php esc_html_e('Custom Code (AdSense, HTML, JavaScript)', 'smartspot-ad-manager'); ?>
                            </option>
                        </select>
                        <p class="description"><?php esc_html_e('Image ads use the Featured Image. Code ads use the "Ad Code" box below.', 'smartspot-ad-manager'); ?></p>
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

    public function device_targeting_metabox($post) {
        $target_devices = get_post_meta($post->ID, '_thesiadm_target_devices', true);

        if (!is_array($target_devices) || empty($target_devices)) {
            $target_devices = array('desktop', 'mobile', 'tablet');
        }
        ?>

        <div class="thesiadm-metabox thesiadm-device-metabox">
            <p class="description" style="margin-bottom: 15px;">
                <?php esc_html_e('Select which devices should display this ad:', 'smartspot-ad-manager'); ?>
            </p>

            <div style="padding: 10px 0;">
                <label style="display: flex; align-items: center; padding: 8px; margin-bottom: 8px; border: 1px solid #ddd; border-radius: 4px; cursor: pointer;">
                    <input type="checkbox" name="thesiadm_target_devices[]" value="desktop"
                           <?php checked(in_array('desktop', $target_devices, true)); ?>>
                    <span class="dashicons dashicons-desktop" style="color: #2271b1; margin: 0 8px;"></span>
                    <strong><?php esc_html_e('Desktop', 'smartspot-ad-manager'); ?></strong>
                </label>

                <label style="display: flex; align-items: center; padding: 8px; margin-bottom: 8px; border: 1px solid #ddd; border-radius: 4px; cursor: pointer;">
                    <input type="checkbox" name="thesiadm_target_devices[]" value="mobile"
                           <?php checked(in_array('mobile', $target_devices, true)); ?>>
                    <span class="dashicons dashicons-smartphone" style="color: #2271b1; margin: 0 8px;"></span>
                    <strong><?php esc_html_e('Mobile', 'smartspot-ad-manager'); ?></strong>
                </label>

                <label style="display: flex; align-items: center; padding: 8px; margin-bottom: 8px; border: 1px solid #ddd; border-radius: 4px; cursor: pointer;">
                    <input type="checkbox" name="thesiadm_target_devices[]" value="tablet"
                           <?php checked(in_array('tablet', $target_devices, true)); ?>>
                    <span class="dashicons dashicons-tablet" style="color: #2271b1; margin: 0 8px;"></span>
                    <strong><?php esc_html_e('Tablet', 'smartspot-ad-manager'); ?></strong>
                </label>
            </div>

            <p class="description" style="margin-top: 10px; padding-top: 10px; border-top: 1px solid #ddd;">
                <strong><?php esc_html_e('Note:', 'smartspot-ad-manager'); ?></strong>
                <?php esc_html_e('Select at least one device. If none selected, ad will display on all devices.', 'smartspot-ad-manager'); ?>
            </p>
        </div>

        <?php
    }

    public function ad_code_metabox($post) {
        $ad_code = get_post_meta($post->ID, '_thesiadm_ad_code', true);
        $ad_type = get_post_meta($post->ID, '_thesiadm_ad_type', true);
        ?>

        <div class="thesiadm-metabox">
            <p class="description">
                <?php esc_html_e('Paste your ad code here (Google AdSense, custom HTML, JavaScript, etc.). Only used if Ad Type is set to "Custom Code".', 'smartspot-ad-manager'); ?>
            </p>

            <textarea name="thesiadm_ad_code" id="thesiadm_ad_code" rows="10"
                      style="width: 100%; font-family: monospace;"
                      placeholder="<?php esc_attr_e('<!-- Your ad code here -->', 'smartspot-ad-manager'); ?>"><?php echo esc_textarea($ad_code); ?></textarea>

            <p class="description" style="margin-top: 10px;">
                <strong><?php esc_html_e('Tip:', 'smartspot-ad-manager'); ?></strong>
                <?php esc_html_e('For Google AdSense, paste the entire script tag provided by Google.', 'smartspot-ad-manager'); ?>
            </p>
        </div>

        <?php
    }

    public function ad_preview_metabox($post) {
        $ad_type = get_post_meta($post->ID, '_thesiadm_ad_type', true);
        $image_url = get_the_post_thumbnail_url($post->ID, 'medium');
        $position = get_post_meta($post->ID, '_thesiadm_ad_position', true);
        if (!$position) {
            $position = 'custom-1';
        }
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
                    <strong><?php esc_html_e('Shortcode (by Position):', 'smartspot-ad-manager'); ?></strong><br>
                    <code style="user-select: all; display: block; padding: 4px; background: #f0f0f1; border-radius: 3px; margin-top: 4px;">[thesiadm_ads position="<?php echo esc_attr($position); ?>"]</code>
                </p>
                <p class="description" style="margin-top: 12px;">
                    <strong><?php esc_html_e('Shortcode (Single Ad):', 'smartspot-ad-manager'); ?></strong><br>
                    <code style="user-select: all; display: block; padding: 4px; background: #f0f0f1; border-radius: 3px; margin-top: 4px;">[thesiadm_ad id="<?php echo esc_attr($post->ID); ?>"]</code>
                </p>
                <p class="description" style="margin-top: 12px;">
                    <strong><?php esc_html_e('Template Tag:', 'smartspot-ad-manager'); ?></strong><br>
                    <code style="user-select: all; display: block; padding: 4px; background: #f0f0f1; border-radius: 3px; margin-top: 4px;">&lt;?php thesiadm_display_ads( '<?php echo esc_attr($position); ?>' ); ?&gt;</code>
                </p>
            </div>
        </div>
        <?php
    }

    public function save_metaboxes($post_id, $post) {
        if (!isset($_POST['thesiadm_metabox_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['thesiadm_metabox_nonce'])), 'thesiadm_save_metaboxes')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        if (isset($_POST['thesiadm_target_urls'])) {
            update_post_meta(
                $post_id,
                '_thesiadm_target_urls',
                sanitize_textarea_field(wp_unslash($_POST['thesiadm_target_urls']))
            );
        }

        if (isset($_POST['thesiadm_url_match_type'])) {
            $match_type = sanitize_text_field(wp_unslash($_POST['thesiadm_url_match_type']));
            $allowed_match_types = array('exact', 'contains', 'starts_with');
            if (!in_array($match_type, $allowed_match_types, true)) {
                $match_type = 'exact';
            }
            update_post_meta($post_id, '_thesiadm_url_match_type', $match_type);
        }

        if (isset($_POST['thesiadm_ad_position'])) {
            $position = sanitize_text_field(wp_unslash($_POST['thesiadm_ad_position']));
            $allowed_positions = array_keys($this->get_allowed_positions());
            if (!in_array($position, $allowed_positions, true)) {
                $position = 'before-content';
            }
            update_post_meta($post_id, '_thesiadm_ad_position', $position);
        }

        if (isset($_POST['thesiadm_ad_type'])) {
            $ad_type = sanitize_text_field(wp_unslash($_POST['thesiadm_ad_type']));
            $allowed_types = array('image', 'code');
            if (!in_array($ad_type, $allowed_types, true)) {
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
            $priority = absint(wp_unslash($_POST['thesiadm_ad_priority']));
            if ($priority < 1) {
                $priority = 1;
            }
            if ($priority > 100) {
                $priority = 100;
            }
            update_post_meta($post_id, '_thesiadm_ad_priority', $priority);
        }

        $open_new_tab = isset($_POST['thesiadm_open_new_tab']) ? '1' : '0';
        update_post_meta($post_id, '_thesiadm_open_new_tab', $open_new_tab);

        if (isset($_POST['thesiadm_target_devices']) && is_array($_POST['thesiadm_target_devices'])) {
            $devices = array_map(
                'sanitize_text_field',
                wp_unslash($_POST['thesiadm_target_devices'])
            );
            $allowed_devices = array('desktop', 'mobile', 'tablet');
            $devices = array_intersect($devices, $allowed_devices);
            if (empty($devices)) {
                $devices = $allowed_devices;
            }
            update_post_meta($post_id, '_thesiadm_target_devices', $devices);
        } else {
            update_post_meta($post_id, '_thesiadm_target_devices', array('desktop', 'mobile', 'tablet'));
        }

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
                'data' => true,
            );
            $allowed_tags['ins'] = array(
                'class' => true,
                'style' => true,
                'data-ad-client' => true,
                'data-ad-slot' => true,
                'data-ad-format' => true,
                'data-full-width-responsive' => true,
            );

            $raw_code = isset($_POST['thesiadm_ad_code']) ? sanitize_textarea_field(wp_unslash($_POST['thesiadm_ad_code'])) : '';
            if (current_user_can('unfiltered_html')) {
                update_post_meta($post_id, '_thesiadm_ad_code', $raw_code);
            } else {
                update_post_meta(
                    $post_id,
                    '_thesiadm_ad_code',
                    wp_kses($raw_code, $allowed_tags)
                );
            }
        }
    }

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

    public function sortable_columns($columns) {
        $columns['ad_priority'] = 'ad_priority';
        return $columns;
    }

    public function custom_column_content($column, $post_id) {
        switch ($column) {
            case 'ad_position':
                $position = get_post_meta($post_id, '_thesiadm_ad_position', true);
                if ($position) {
                    $positions = $this->get_allowed_positions();
                    $label = isset($positions[$position]) ? $positions[$position] : ucwords(str_replace('-', ' ', $position));
                    echo '<span class="thesiadm-badge thesiadm-badge-position">' . esc_html($label) . '</span>';
                } else {
                    echo '&mdash;';
                }
                break;

            case 'target_urls':
                $urls = get_post_meta($post_id, '_thesiadm_target_urls', true);
                $url_array = array_filter(array_map('trim', explode("\n", $urls)));
                $count = count($url_array);

                if ($count > 0) {
                    /* translators: %s: number of target URLs (singular or plural form) */
                    $label = _n('%s URL', '%s URLs', $count, 'smartspot-ad-manager');
                    echo '<strong>' . esc_html(sprintf($label, number_format_i18n($count))) . '</strong><br>';
                    echo '<small style="color: #666;">' . esc_html(implode(', ', array_slice($url_array, 0, 2))) . '</small>';
                    if ($count > 2) {
                        echo '<small style="color: #666;">...</small>';
                    }
                } else {
                    echo '&mdash;';
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
                        'mobile'  => '<span class="dashicons dashicons-smartphone" title="' . esc_attr__('Mobile', 'smartspot-ad-manager') . '"></span>',
                        'tablet'  => '<span class="dashicons dashicons-tablet" title="' . esc_attr__('Tablet', 'smartspot-ad-manager') . '"></span>',
                    );

                    foreach ($devices as $device) {
                        if (isset($device_icons[$device])) {
                            echo wp_kses_post($device_icons[$device]) . ' ';
                        }
                    }
                } else {
                    echo '&mdash;';
                }
                break;

            case 'ad_priority':
                $priority = get_post_meta($post_id, '_thesiadm_ad_priority', true);
                echo $priority ? '<strong>' . esc_html($priority) . '</strong>' : '&mdash;';
                break;
        }
    }
}
