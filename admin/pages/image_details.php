<?php
/* Prohibit direct script loading */
defined('ABSPATH') || die('No direct script access allowed!');
?>
<div id="form_image_details" data-id="<?php echo esc_attr($id) ?>"
     class="form_image_details form_image_details_popup full">
    <div class="wp-media-sidebar" data-id="<?php echo esc_attr($id) ?>">
        <div>
            <label class="setting">
                <span class="name"><?php esc_html_e('Title', 'wp-load-gallery') ?></span>
                <input type="text" class="img_title wplg-input" value="<?php echo esc_attr($details->post_title) ?>">
            </label>

            <label class="setting">
                <span class="name"><?php esc_html_e('Caption', 'wp-load-gallery') ?></span>
                <input type="text" class="img_excerpt wplg-input"
                       value="<?php echo esc_attr($details->post_excerpt) ?>">
            </label>

            <label class="setting">
                <span class="name"><?php esc_html_e('Link to', 'wp-load-gallery') ?></span>
                <input type="text" class="text wplg_image_custom_link wplg-input"
                       value="<?php echo esc_url($link_to) ?>">
            </label>

            <label class="setting">
                <span class="name"><?php esc_html_e('Show', 'wp-load-gallery') ?></span>
                <div style="width: 80% !important;vertical-align: middle;float: right;">
                    <div class="wplg-switch-button" style="margin: 0">
                        <label class="switch" style="margin: 8px 0;">
                            <input type="checkbox" name="wplg_image_status"
                                   class="wplg_image_status" <?php checked($status, 1) ?> value="1">
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>
            </label>
        </div>
    </div>
</div>
