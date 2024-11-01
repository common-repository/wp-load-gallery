<?php
/* Prohibit direct script loading */
defined('ABSPATH') || die('No direct script access allowed!');
global $current_screen;
$themes_array = \WPLG\Helper::getThemesList();
$sizes = \WPLG\Helper::getSizesList();
$sizes_list = array();
foreach ($sizes as $size_key => $size_name) {
    $sizes_list[] = array('value' => $size_key, 'label' => $size_name);
}

$colors = array(
    '#ac725e' => esc_html__('Chocolate ice cream', 'wp-load-gallery'),
    '#d06b64' => esc_html__('Old brick red', 'wp-load-gallery'),
    '#f83a22' => esc_html__('Cardinal', 'wp-load-gallery'),
    '#fa573c' => esc_html__('Wild strawberries', 'wp-load-gallery'),
    '#ff7537' => esc_html__('Mars orange', 'wp-load-gallery'),
    '#ffad46' => esc_html__('Yellow cab', 'wp-load-gallery'),
    '#42d692' => esc_html__('Spearmint', 'wp-load-gallery'),
    '#16a765' => esc_html__('Vern fern', 'wp-load-gallery'),
    '#7bd148' => esc_html__('Asparagus', 'wp-load-gallery'),
    '#b3dc6c' => esc_html__('Slime green', 'wp-load-gallery'),
    '#fbe983' => esc_html__('Desert sand', 'wp-load-gallery'),
    '#fad165' => esc_html__('Macaroni', 'wp-load-gallery'),
    '#92e1c0' => esc_html__('Sea foam', 'wp-load-gallery'),
    '#9fe1e7' => esc_html__('Pool', 'wp-load-gallery'),
    '#9fc6e7' => esc_html__('Denim', 'wp-load-gallery'),
    '#4986e7' => esc_html__('Rainy sky', 'wp-load-gallery'),
    '#9a9cff' => esc_html__('Blue velvet', 'wp-load-gallery'),
    '#b99aff' => esc_html__('Purple dino', 'wp-load-gallery'),
    '#8f8f8f' => esc_html__('Mouse', 'wp-load-gallery'),
    '#cabdbf' => esc_html__('Mountain grey', 'wp-load-gallery'),
    '#cca6ac' => esc_html__('Earthworm', 'wp-load-gallery'),
    '#f691b2' => esc_html__('Bubble gum', 'wp-load-gallery'),
    '#cd74e6' => esc_html__('Purple rain', 'wp-load-gallery'),
    '#000000' => esc_html__('Black', 'wp-load-gallery'),
    '#ffffff' => esc_html__('White', 'wp-load-gallery')
);
?>

<a href="#server_folder_preview" class="server_folder_preview" style="display: none"></a>
<div id="server_folder_preview" class="white-popup mfp-hide">
    <div class="server_folder_preview_wrap">
        <img class="server_folder_preview_loader"
             src="<?php echo esc_url(WPLG_PLUGIN_URL . '/assets/images/loader_skype_trans.gif') ?>">
    </div>
</div>
<div id="add-gallery-box" class="add_gallery_wrap white-popup mfp-hide">
    <div class="gallery-options-wrap">
        <div class="wplg-configs">
            <div class="wplg-field">
                <label><?php esc_html_e('New gallery', 'wp-load-gallery') ?></label>
                <input type="text" size="35" class="new-gallery-name gallery_name wplg-input"
                       value="<?php esc_html_e('New gallery', 'wp-load-gallery') ?>">
            </div>
        </div>

        <div class="wplg-configs" style="margin: 0">
            <button type="button" class="wplg-button wplg-button-colored btn_create_gallery" style="float: right">
                <?php esc_html_e('Create', 'wp-load-gallery') ?>
            </button>

            <span class="spinner" style="float: right"></span>
        </div>
    </div>
</div>

<!-- Edit form -->
<div class="edit_wrap wp-settings-enable wplg-pro">
    <div class="wplg-actions-wrapper">
        <h2 class="wplg_top_title"><?php esc_html_e('WP Load Gallery Settings', 'wp-load-gallery') ?></h2>
        <div class="gallery-toolbar">
            <button type="button" class="wplg_save wplg-button">
                <span class="material-icons-outlined">check</span>
                <span><?php esc_html_e('Save', 'wp-load-gallery') ?></span>
            </button>

            <div class="wplg-add-image">
                <button type="button" class="wplg-button">
                    <span class="material-icons-outlined">add_circle_outline</span>
                    <span><?php esc_html_e('Add items', 'wp-load-gallery') ?></span>
                </button>

                <div class="cu-dropdown__menu" style="display: none">
                    <div class="wplg-dropdown__container">
                        <div class="wplg-dropdown__views">
                            <div class="wplg-dropdown__views-list">
                                <div class="wplg-dropdown__views-item add_image_from_media_library">
                                    <div class="wplg-dropdown__views-item-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" width="18px"
                                             height="18px">
                                            <linearGradient id="MqDBRrEAg140YpAAB2zYma" x1="32" x2="32" y1="-1367.5"
                                                            y2="-1421.251" gradientTransform="matrix(1 0 0 -1 0 -1362)"
                                                            gradientUnits="userSpaceOnUse">
                                                <stop offset="0" stop-color="#1a6dff"/>
                                                <stop offset="1" stop-color="#c822ff"/>
                                            </linearGradient>
                                            <path fill="url(#MqDBRrEAg140YpAAB2zYma)"
                                                  d="M32,58C17.664,58,6,46.337,6,32S17.664,6,32,6s26,11.663,26,26S46.336,58,32,58z M32,8	C18.767,8,8,18.767,8,32s10.767,24,24,24s24-10.767,24-24S45.233,8,32,8z"/>
                                            <linearGradient id="MqDBRrEAg140YpAAB2zYmb" x1="32" x2="32" y1="8.021"
                                                            y2="54.229" gradientUnits="userSpaceOnUse">
                                                <stop offset="0" stop-color="#6dc7ff"/>
                                                <stop offset="1" stop-color="#e6abff"/>
                                            </linearGradient>
                                            <path fill="url(#MqDBRrEAg140YpAAB2zYmb)"
                                                  d="M12,31.999c0,7.916,4.6,14.758,11.272,17.999L13.732,23.86C12.622,26.347,12,29.1,12,31.999z M45.502,30.991c0-2.472-0.888-4.184-1.649-5.516c-1.014-1.648-1.965-3.043-1.965-4.691c0-1.838,1.394-3.549,3.359-3.549	c0.089,0,0.173,0.011,0.259,0.016C41.948,13.991,37.207,12,32,12c-6.988,0-13.134,3.585-16.711,9.014	c0.47,0.015,0.912,0.024,1.287,0.024c2.091,0,5.33-0.254,5.33-0.254c1.078-0.063,1.205,1.521,0.128,1.648	c0,0-1.084,0.127-2.289,0.19l7.283,21.664l4.377-13.126l-3.116-8.537c-1.078-0.063-2.098-0.19-2.098-0.19	c-1.078-0.064-0.952-1.711,0.127-1.648c0,0,3.302,0.254,5.267,0.254c2.091,0,5.331-0.254,5.331-0.254	c1.078-0.063,1.205,1.521,0.127,1.648c0,0-1.085,0.127-2.289,0.19l7.228,21.499l2.063-6.538	C44.964,34.726,45.502,32.702,45.502,30.991z M32.351,33.749L26.35,51.185C28.142,51.712,30.037,52,32,52	c2.329,0,4.563-0.402,6.642-1.134c-0.054-0.086-0.103-0.176-0.144-0.276L32.351,33.749z M49.551,22.405	c0.086,0.637,0.134,1.32,0.134,2.056c0,2.029-0.38,4.31-1.521,7.163l-6.108,17.661C48.001,45.819,52,39.378,52,31.999	C52,28.522,51.111,25.253,49.551,22.405z"/>
                                        </svg>
                                    </div>

                                    <div class="wplg-dropdown__views-item-title">
                                        <span><?php esc_html_e('Media library', 'wp-load-gallery') ?></span>
                                    </div>
                                </div>

                                <a class="wplg-dropdown__views-item upload_image_from_pc"
                                   href="#wplg_upload_image_from_pc">
                                    <div class="wplg-dropdown__views-item-icon">
                                        <span class="material-icons">publish</span>
                                    </div>

                                    <div class="wplg-dropdown__views-item-title">
                                        <span><?php esc_html_e('Upload images', 'wp-load-gallery') ?></span>
                                    </div>
                                </a>

                                <a class="wplg-dropdown__views-item wplg_add_posts" href="#wplg_posts">
                                    <div class="wplg-dropdown__views-item-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="18px"
                                             height="18px">
                                            <linearGradient id="eo9Iz~gJX5QQxF9vIcujya" x1="41.018" x2="45.176" y1="26"
                                                            y2="26" gradientUnits="userSpaceOnUse">
                                                <stop offset="0" stop-color="#3537b0"/>
                                                <stop offset="1" stop-color="#4646cf"/>
                                            </linearGradient>
                                            <path fill="url(#eo9Iz~gJX5QQxF9vIcujya)"
                                                  d="M43,11h-3v30h3c1.105,0,2-0.895,2-2V13C45,11.895,44.105,11,43,11z"/>
                                            <path fill="#5286ff"
                                                  d="M41,39V9c0-1.105-0.895-2-2-2H5C3.895,7,3,7.895,3,9v30c0,1.105,0.895,2,2,2h38	C41.895,41,41,40.105,41,39z"/>
                                            <path fill="#fff"
                                                  d="M37,17H7c-0.552,0-1-0.448-1-1v-2c0-0.552,0.448-1,1-1h30c0.552,0,1,0.448,1,1v2	C38,16.552,37.552,17,37,17z"/>
                                            <path fill="#fff"
                                                  d="M19,36H7c-0.552,0-1-0.448-1-1V22c0-0.552,0.448-1,1-1h12c0.552,0,1,0.448,1,1v13	C20,35.552,19.552,36,19,36z"/>
                                            <path fill="#fff"
                                                  d="M38,24H24v-2c0-0.552,0.448-1,1-1h12c0.552,0,1,0.448,1,1V24z"/>
                                            <rect width="14" height="3" x="24" y="24" fill="#e6eeff"/>
                                            <rect width="14" height="3" x="24" y="27" fill="#ccdcff"/>
                                            <rect width="14" height="3" x="24" y="30" fill="#b3cbff"/>
                                            <path fill="#9abaff"
                                                  d="M37,36H25c-0.552,0-1-0.448-1-1v-2h14v2C38,35.552,37.552,36,37,36z"/>
                                        </svg>
                                    </div>

                                    <div class="wplg-dropdown__views-item-title">
                                        <span><?php esc_html_e('Post type', 'wp-load-gallery') ?></span>
                                        <span class="wplg_pro_version"><?php esc_html_e(' (Pro)', 'wp-load-gallery') ?></span>
                                    </div>
                                </a>

                                <a class="wplg-dropdown__views-item wplg_add_media_category"
                                   href="#wplg_media_category">
                                    <div class="wplg-dropdown__views-item-icon">
                                        <i class="material-icons-outlined">folder</i>
                                    </div>

                                    <div class="wplg-dropdown__views-item-title">
                                        <span><?php esc_html_e('Media Category', 'wp-load-gallery') ?></span>
                                        <span class="wplg_pro_version"><?php esc_html_e(' (Pro)', 'wp-load-gallery') ?></span>
                                    </div>
                                </a>

                                <a class="wplg-dropdown__views-item add_image_from_folder" href="#wplg_server_folder">
                                    <div class="wplg-dropdown__views-item-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="18px"
                                             height="18px">
                                            <path fill="#FFA000"
                                                  d="M40,12H22l-4-4H8c-2.2,0-4,1.8-4,4v8h40v-4C44,13.8,42.2,12,40,12z"/>
                                            <path fill="#FFCA28"
                                                  d="M40,12H8c-2.2,0-4,1.8-4,4v20c0,2.2,1.8,4,4,4h32c2.2,0,4-1.8,4-4V16C44,13.8,42.2,12,40,12z"/>
                                        </svg>
                                    </div>

                                    <div class="wplg-dropdown__views-item-title">
                                        <?php esc_html_e('Server Folder', 'wp-load-gallery') ?>
                                    </div>
                                </a>
                                <a class="wplg-dropdown__views-item wplg_add_youtube" href="#wplg_youtube">
                                    <div class="wplg-dropdown__views-item-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="18px"
                                             height="18px">
                                            <path fill="#FF3D00"
                                                  d="M43.2,33.9c-0.4,2.1-2.1,3.7-4.2,4c-3.3,0.5-8.8,1.1-15,1.1c-6.1,0-11.6-0.6-15-1.1c-2.1-0.3-3.8-1.9-4.2-4C4.4,31.6,4,28.2,4,24c0-4.2,0.4-7.6,0.8-9.9c0.4-2.1,2.1-3.7,4.2-4C12.3,9.6,17.8,9,24,9c6.2,0,11.6,0.6,15,1.1c2.1,0.3,3.8,1.9,4.2,4c0.4,2.3,0.9,5.7,0.9,9.9C44,28.2,43.6,31.6,43.2,33.9z"/>
                                            <path fill="#FFF" d="M20 31L20 17 32 24z"/>
                                        </svg>
                                    </div>

                                    <div class="wplg-dropdown__views-item-title">
                                        <?php esc_html_e('Youtube', 'wp-load-gallery') ?>
                                    </div>
                                </a>
                                <a class="wplg-dropdown__views-item wplg_add_vimeo" href="#wplg_vimeo">
                                    <div class="wplg-dropdown__views-item-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="18px"
                                             height="18px">
                                            <path fill="#29B6F6"
                                                  d="M45,14.3c-0.2,4.1-3,9.6-8.6,16.6c-5.7,7.4-10.5,11-14.5,11c-2.4,0-4.5-2.2-6.2-6.7c-1.1-4.1-2.3-8.2-3.4-12.3c-1.3-4.5-2.6-6.7-4-6.7C8,16.2,6.9,16.7,5,18l-2-2.3c2.1-1.8,4-3.1,6-4.9c2.8-2.4,4.8-3.6,6.2-3.7c3.3-0.3,5.3,1.9,6,6.6c0.8,5.1,1.5,7.8,1.8,9c0.9,4.2,2,6.4,3.1,6.4c0.9,0,2.2-1.4,4-4.1c1.8-2.8,2.7-4.8,2.8-6.3c0.3-2.4-0.7-3.6-2.8-3.6c-1,0-2.1,0.5-3.2,0.9c2.1-6.7,6.1-10.2,11.9-10C43.2,6.1,45.2,8.9,45,14.3z"/>
                                        </svg>
                                    </div>

                                    <div class="wplg-dropdown__views-item-title">
                                        <?php esc_html_e('Vimeo', 'wp-load-gallery') ?>
                                    </div>
                                </a>
                                <a class="wplg-dropdown__views-item wplg_add_google_drive" href="#wplg_google_drive">
                                    <div class="wplg-dropdown__views-item-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="18px"
                                             height="18px">
                                            <path fill="#FFC107" d="M17 6L31 6 45 30 31 30z"/>
                                            <path fill="#1976D2" d="M9.875 42L16.938 30 45 30 38 42z"/>
                                            <path fill="#4CAF50" d="M3 30.125L9.875 42 24 18 17 6z"/>
                                        </svg>
                                    </div>

                                    <div class="wplg-dropdown__views-item-title">
                                        <span><?php esc_html_e('Google drive', 'wp-load-gallery') ?></span>
                                        <span class="wplg_pro_version"><?php esc_html_e(' (Pro)', 'wp-load-gallery') ?></span>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <button type="button" class="wplg-button wplg-load-default-options">
                <span class="material-icons-outlined">settings_backup_restore</span>
                <span><?php esc_html_e('Load default', 'wp-load-gallery') ?></span>
                <span class="spinner" style="margin: 0 5px; display: none"></span>
            </button>

            <button type="button" class="wplg-button wplg-remove-item-btn">
                <i class="material-icons-outlined"> delete_outline </i>
                <span><?php esc_html_e('Delete', 'wp-load-gallery') ?></span>
            </button>
            <div style="margin: 5px 10px 5px 0; width: 300px; max-width: 100%; display: inline-block; vertical-align: middle;">
                <input type="text" class="wplg-shortcode-value" readonly
                       style="width: 100%; height: 38px !important; border: 0;">
            </div>
        </div>
    </div>
    <div class="wplg-nav-tab-wrapper">
        <div class="nav-tab-container">
            <a class="wplg-nav-tab js-action-link wplg-nav-tab-active" data-tab="main-gallery">
                <span class="material-icons"> photo_size_select_actual </span><?php esc_html_e('Source', 'wp-load-gallery') ?>
            </a>
            <a class="wplg-nav-tab js-action-link" data-tab="main-gallery-settings"><span class="material-icons"> view_quilt </span>
                <?php esc_html_e('Themes - Options', 'wp-load-gallery') ?>            </a>
        </div>
    </div>
    <div class="gallery-options-wrap">
        <div id="main-gallery" class="wplg-content show">
            <div class="wplg-selection-wrap">
                <div class="server_folders_gallery_output_wrap">
                    <div class="server_folders_gallery_output"></div>
                </div>
                <div class="wplg-images-wrap" id="wplg-images-wrap"></div>
                <div class="wplg-image-page-wrap"></div>
            </div>
        </div>

        <div id="main-gallery-settings" class="wplg-content">
            <div class="wplg-panel-wrap wplg-panel-theme-wrap">
                <?php foreach ($themes_array as $theme_value => $theme_info) : ?>
                    <div class="wplg-panel-theme wplg-panel" data-theme="<?php echo esc_attr($theme_value) ?>">
                        <?php if ($theme_info['version'] === 'pro') : ?>
                            <div class="wplg-pro-theme"><?php esc_html_e('Pro', 'wp-load-gallery') ?></div>
                        <?php endif; ?>
                        <div class="wplg-panel__header wplg-panel__header-status">
                            <div class="wplg-panel__status-info">
                                <div class="wplg-panel__main-title">
                                    <?php echo esc_html($theme_info['label']) ?>
                                </div>
                            </div>
                            <div class="wplg-panel__header-options">
                                <a class="wplp-theme-demo" href="<?php echo esc_url($theme_info['img']) ?>"><span class="material-icons-outlined wplp-theme-demo-icon">remove_red_eye</span></a>
                                <span class="material-icons-outlined">check_circle_outline</span>
                            </div>
                        </div>

                        <div class="wplg-theme-preview">
                            <div class="square_thumbnail">
                                <div class="img_centered img_demo_wrap">
                                    <img alt="" src="<?php echo esc_url($theme_info['img']) ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="wplg-configs wplg-configs-edit">
                <form class="wplg-form-edit">
                    <input type="hidden" class="edit-gallery-theme" name="theme">
                    <div class="wplg-panel-wrap wplg-panel-options-wrap wplg_box_closed">
                        <div class="main_option_title wplg_box">
                            <span class="dashicons dashicons-arrow-down wplg_box_icon"></span>
                            <span class="material-icons wplg_box_icon_before"> settings </span>
                            <span class="main_option_title_title">
                                <?php esc_html_e('General', 'wp-load-gallery') ?>
                            </span>
                        </div>

                        <div class="wplg_box_content">
                            <div class="wplg-panel">
                                <div class="wplg-panel__header wplg-panel__header-status">
                                    <div class="wplg-panel__status-info">
                                        <div class="wplg-panel__main-title">
                                            <?php esc_html_e('Title', 'wp-load-gallery') ?>
                                        </div>
                                    </div>
                                    <div class="wplg-panel__header-options">
                                        <input type="text" class="edit-gallery-name gallery_name wplg-input"
                                               placeholder="<?php esc_html_e('Title', 'wp-load-gallery') ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="wplg-panel">
                                <div class="wplg-panel__main-title">
                                    <?php esc_html_e('Custom shortcode', 'wp-load-gallery') ?>
                                </div>
                                <div class="wplg-panel__header-options">
                                    <textarea class="wplg-custom-shortcode" readonly></textarea>
                                </div>
                            </div>

                            <div class="wplg-panel" style="text-align: center">
                                <div class="wplg-panel__main-title">
                                    <?php esc_html_e('Background color', 'wp-load-gallery') ?>
                                </div>

                                <div class="wplg-panel__header-options wplg-color-main">
                                    <div class="wplg-color-wrapper background_color_wrap">
                                        <?php foreach ($colors as $color => $label_color) : ?>
                                            <?php if ($color === '#ffffff'): ?>
                                                <div data-color="<?php echo esc_attr($color) ?>"
                                                     title="<?php echo esc_attr($label_color) ?>"
                                                     class="wplg-color wplg-background-color"
                                                     style="background: <?php echo esc_attr($color) ?>; border: #ccc 1px solid"></div>
                                            <?php else : ?>
                                                <div data-color="<?php echo esc_attr($color) ?>"
                                                     title="<?php echo esc_attr($label_color) ?>"
                                                     class="wplg-color wplg-background-color"
                                                     style="background: <?php echo esc_attr($color) ?>"></div>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </div>

                                    <div class="wplg-color-action">
                                        <input type="text" size="7"
                                               placeholder="<?php esc_attr_e('Custom color', 'wp-load-gallery') ?>"
                                               data-type="background_color_wrap" name="background_color"
                                               class="background_color wplg-input-color wplg-input">
                                        <button class="wplg-clear-color wplg-button" type="button"
                                                data-field="background_color_wrap"><?php esc_html_e('Clear', 'wp-load-gallery') ?></button>
                                    </div>
                                </div>
                            </div>

                            <?php
                            \WPLG\Helper::renderFields('radio', array('label' => esc_html__('Full width', 'wp-load-gallery'), 'name' => 'gallery_full_width', 'class' => 'gallery_full_width'));
                            \WPLG\Helper::renderFields('number', array(
                                    'label' => esc_html__('Width (px)', 'wp-load-gallery'),
                                    'name' => 'gallery_width',
                                    'class' => 'gallery_width',
                                    'min' => 150,
                                    'step' => 1
                                )
                            );
                            \WPLG\Helper::renderFields('number', array(
                                    'label' => esc_html__('Columns', 'wp-load-gallery'),
                                    'name' => 'columns',
                                    'class' => 'columns',
                                    'min' => 1,
                                    'max' => 8,
                                    'step' => 1
                                )
                            );
                            \WPLG\Helper::renderFields('number', array(
                                    'label' => esc_html__('Max items', 'wp-load-gallery'),
                                    'name' => 'max_items',
                                    'class' => 'max_items',
                                    'min' => 1,
                                    'max' => 1000,
                                    'step' => 1
                                )
                            );
                            ?>
                        </div>
                    </div>

                    <div class="wplg-panel-wrap wplg-panel-options-wrap wplg_box_closed">
                        <div class="main_option_title wplg_box">
                            <span class="dashicons dashicons-arrow-down wplg_box_icon"></span>
                            <span class="material-icons wplg_box_icon_before"> photo_size_select_actual </span>
                            <span class="main_option_title_title"><?php esc_html_e('Image', 'wp-load-gallery') ?></span>
                        </div>

                        <div class="wplg_box_content">
                            <?php
                            \WPLG\Helper::renderFields('number', array(
                                    'label' => esc_html__('Margin', 'wp-load-gallery'),
                                    'name' => 'gutterwidth',
                                    'class' => 'gutterwidth',
                                    'min' => 0,
                                    'max' => 50,
                                    'step' => 1
                                )
                            );
                            \WPLG\Helper::renderFields('select', array(
                                    'label' => esc_html__('Image size', 'wp-load-gallery'),
                                    'name' => 'size',
                                    'class' => 'size',
                                    'lists' => $sizes_list
                                )
                            );
                            \WPLG\Helper::renderFields('select', array(
                                    'label' => esc_html__('Item ratio', 'wp-load-gallery'),
                                    'name' => 'item_ratio',
                                    'class' => 'item_ratio',
                                    'lists' => array(
                                        array('value' => 'default', 'label' => esc_html__('Default', 'wp-load-gallery')),
                                        array('value' => '4-3', 'label' => '4-3'),
                                        array('value' => '3-4', 'label' => '3-4'),
                                        array('value' => '5-4', 'label' => '5-4'),
                                        array('value' => '4-5', 'label' => '4-5'),
                                        array('value' => '1-1', 'label' => '1-1'),
                                        array('value' => '3-1', 'label' => '3-1'),
                                        array('value' => '3-2', 'label' => '3-2'),
                                        array('value' => '2-3', 'label' => '2-3'),
                                        array('value' => '1-2', 'label' => '1-2'),
                                        array('value' => '2-1', 'label' => '2-1'),
                                        array('value' => '16-9', 'label' => '16-9'),
                                        array('value' => '9-16', 'label' => '9-16')
                                    )
                                )
                            );
                            ?>
                        </div>
                    </div>

                    <div class="wplg-panel-wrap wplg-panel-options-wrap wplg_box_closed">
                        <div class="main_option_title wplg_box">
                            <span class="dashicons dashicons-arrow-down wplg_box_icon"></span>
                            <span class="material-icons wplg_box_icon_before"> sort </span>
                            <span class="main_option_title_title"><?php esc_html_e('Sorting', 'wp-load-gallery') ?></span>
                        </div>

                        <div class="wplg_box_content">
                            <?php
                            \WPLG\Helper::renderFields('select', array(
                                    'label' => esc_html__('Order by', 'wp-load-gallery'),
                                    'name' => 'wp_orderby',
                                    'class' => 'wp_orderby',
                                    'lists' => array(
                                        array('value' => 'rand', 'label' => esc_html__('Random', 'wp-load-gallery')),
                                        array('value' => 'title', 'label' => esc_html__('Title', 'wp-load-gallery')),
                                        array('value' => 'date', 'label' => esc_html__('Date', 'wp-load-gallery'))
                                    )
                                )
                            );
                            \WPLG\Helper::renderFields('select', array(
                                    'label' => esc_html__('Order', 'wp-load-gallery'),
                                    'name' => 'wp_order',
                                    'class' => 'wp_order',
                                    'lists' => array(
                                        array('value' => 'ASC', 'label' => esc_html__('Ascending', 'wp-load-gallery')),
                                        array('value' => 'DESC', 'label' => esc_html__('Descending', 'wp-load-gallery'))
                                    )
                                )
                            );
                            ?>
                        </div>
                    </div>

                    <div class="wplg-panel-wrap wplg-panel-options-wrap wplg_box_closed">
                        <div class="main_option_title wplg_box">
                            <span class="dashicons dashicons-arrow-down wplg_box_icon"></span>
                            <span class="dashicons dashicons-editor-kitchensink wplg_box_icon_before"></span>
                            <span class="main_option_title_title"><?php esc_html_e('Navigation & Pagination', 'wp-load-gallery') ?></span>
                        </div>

                        <div class="wplg_box_content">
                            <?php
                            \WPLG\Helper::renderFields('radio', array('label' => esc_html__('Navigation', 'wp-load-gallery'), 'name' => 'gallery_navigation', 'class' => 'gallery_navigation'));
                            \WPLG\Helper::renderFields('select', array(
                                    'label' => esc_html__('Navigation Type', 'wp-load-gallery'),
                                    'name' => 'navigation_type',
                                    'class' => 'navigation_type',
                                    'lists' => array(
                                        array('value' => 'menu', 'label' => esc_html__('Dropdown', 'wp-load-gallery')),
                                        array('value' => 'folder', 'label' => esc_html__('Folder', 'wp-load-gallery'))
                                    )
                                )
                            );
                            \WPLG\Helper::renderFields('radio', array('label' => esc_html__('Pagination', 'wp-load-gallery'), 'name' => 'enable_pagination', 'class' => 'enable_pagination'));
                            \WPLG\Helper::renderFields('select', array(
                                    'label' => esc_html__('Pagination Type', 'wp-load-gallery'),
                                    'name' => 'pagination_type',
                                    'class' => 'pagination_type',
                                    'lists' => array(
                                        array('value' => 'loadmore', 'label' => esc_html__('Load more', 'wp-load-gallery')),
                                        array('value' => 'number_page', 'label' => esc_html__('Page', 'wp-load-gallery')),
                                        array('value' => 'lazyload', 'label' => esc_html__('Lazy load', 'wp-load-gallery'))
                                    )
                                )
                            );
                            \WPLG\Helper::renderFields('number', array(
                                    'label' => esc_html__('Items per page', 'wp-load-gallery'),
                                    'name' => 'items_per_page',
                                    'class' => 'items_per_page',
                                    'min' => 1,
                                    'max' => 50,
                                    'step' => 1
                                )
                            )
                            ?>
                        </div>
                    </div>

                    <div class="wplg-panel-wrap wplg-panel-options-wrap wplg_box_closed">
                        <div class="main_option_title wplg_box">
                            <span class="dashicons dashicons-arrow-down wplg_box_icon"></span>
                            <span class="material-icons wplg_box_icon_before"> settings_overscan </span>
                            <span class="main_option_title_title"><?php esc_html_e('Overlay & Image effect', 'wp-load-gallery') ?></span>
                        </div>

                        <div class="wplg_box_content">
                            <?php
                            \WPLG\Helper::renderFields('radio', array('label' => esc_html__('Enable overlay', 'wp-load-gallery'), 'name' => 'enable_overlay', 'class' => 'enable_overlay'));
                            \WPLG\Helper::renderFields('number', array(
                                    'label' => esc_html__('Overlay opacity', 'wp-load-gallery'),
                                    'name' => 'overlay_opacity',
                                    'class' => 'overlay_opacity',
                                    'min' => 0,
                                    'max' => 1,
                                    'step' => 0.1
                                )
                            );
                            ?>

                            <div class="wplg-panel" style="text-align: center">
                                <div class="wplg-panel__main-title">
                                    <?php esc_html_e('Overlay color', 'wp-load-gallery') ?>
                                </div>

                                <div class="wplg-panel__header-options wplg-color-main">
                                    <div class="wplg-color-wrapper overlay_color_wrap">
                                        <?php foreach ($colors as $color => $label_color) : ?>
                                            <?php if ($color === '#ffffff'): ?>
                                                <div data-color="<?php echo esc_attr($color) ?>"
                                                     title="<?php echo esc_attr($label_color) ?>"
                                                     class="wplg-color wplg-overlay-color"
                                                     style="background: <?php echo esc_attr($color) ?>; border: #ccc 1px solid"></div>
                                            <?php else : ?>
                                                <div data-color="<?php echo esc_attr($color) ?>"
                                                     title="<?php echo esc_attr($label_color) ?>"
                                                     class="wplg-color wplg-overlay-color"
                                                     style="background: <?php echo esc_attr($color) ?>"></div>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </div>

                                    <div class="wplg-color-action">
                                        <input type="text" size="7"
                                               placeholder="<?php esc_attr_e('Custom color', 'wp-load-gallery') ?>"
                                               data-type="overlay_color_wrap" name="overlay_color"
                                               class="overlay_color wplg-input-color wplg-input">
                                        <button class="wplg-clear-color wplg-button" type="button"
                                                data-field="overlay_color_wrap"><?php esc_html_e('Clear', 'wp-load-gallery') ?></button>
                                    </div>
                                </div>
                            </div>

                            <?php
                            \WPLG\Helper::renderFields('radio', array('label' => esc_html__('Enable image effect', 'wp-load-gallery'), 'name' => 'enable_image_effect', 'class' => 'enable_image_effect'));
                            \WPLG\Helper::renderFields('select', array(
                                    'label' => esc_html__('Image effect type', 'wp-load-gallery'),
                                    'name' => 'image_effect_type',
                                    'class' => 'image_effect_type',
                                    'lists' => array(
                                        array('value' => 'bw', 'label' => esc_html__('Grayscale', 'wp-load-gallery')),
                                        array('value' => 'brightness', 'label' => esc_html__('Brightness', 'wp-load-gallery')),
                                        array('value' => 'blur', 'label' => esc_html__('Blur', 'wp-load-gallery')),
                                        array('value' => 'sepia', 'label' => esc_html__('Sepia', 'wp-load-gallery'))
                                    )
                                )
                            );
                            ?>
                        </div>
                    </div>

                    <div class="wplg-panel-wrap wplg-panel-options-wrap wplg_box_closed">
                        <div class="main_option_title wplg_box">
                            <span class="dashicons dashicons-arrow-down wplg_box_icon"></span>
                            <span class="material-icons wplg_box_icon_before"> all_out </span>
                            <span class="main_option_title_title"><?php esc_html_e('On Hover', 'wp-load-gallery') ?></span>
                        </div>

                        <div class="wplg_box_content">
                            <?php
                            \WPLG\Helper::renderFields('radio', array('label' => esc_html__('Skin', 'wp-load-gallery'), 'name' => 'skin_on_hover', 'class' => 'skin_on_hover'));
                            \WPLG\Helper::renderFields('select', array(
                                    'label' => esc_html__('Skin type', 'wp-load-gallery'),
                                    'name' => 'skin_type',
                                    'class' => 'skin_type',
                                    'lists' => array(
                                        array('value' => 'bg-transition', 'label' => esc_html__('BG Transition', 'wp-load-gallery')),
                                        array('value' => 'border-transition', 'label' => esc_html__('Border Transition', 'wp-load-gallery'))
                                    )
                                )
                            );
                            \WPLG\Helper::renderFields('radio', array('label' => esc_html__('Enable icons', 'wp-load-gallery'), 'name' => 'enable_icons', 'class' => 'enable_icons'));
                            \WPLG\Helper::renderFields('radio', array('label' => esc_html__('Scale image', 'wp-load-gallery'), 'name' => 'scale_image', 'class' => 'scale_image'));
                            ?>
                        </div>
                    </div>

                    <div class="wplg-panel-wrap wplg-panel-options-wrap wplg_box_closed">
                        <div class="main_option_title wplg_box">
                            <span class="dashicons dashicons-arrow-down wplg_box_icon"></span>
                            <span class="material-icons-outlined wplg_box_icon_before"> add_box </span>
                            <span class="main_option_title_title"><?php esc_html_e('Lightbox', 'wp-load-gallery') ?></span>
                        </div>

                        <div class="wplg_box_content">
                            <?php
                            \WPLG\Helper::renderFields('select', array(
                                    'label' => esc_html__('Action', 'wp-load-gallery'),
                                    'name' => 'link',
                                    'class' => 'link',
                                    'lists' => array(
                                        array('value' => 'file', 'label' => esc_html__('Lightbox', 'wp-load-gallery')),
                                        array('value' => 'link', 'label' => esc_html__('Link', 'wp-load-gallery')),
                                        array('value' => 'none', 'label' => esc_html__('None', 'wp-load-gallery'))
                                    )
                                )
                            );
                            \WPLG\Helper::renderFields('select', array(
                                    'label' => esc_html__('Lightbox size', 'wp-load-gallery'),
                                    'name' => 'targetsize',
                                    'class' => 'targetsize',
                                    'lists' => $sizes_list
                                )
                            );
                            \WPLG\Helper::renderFields('select', array(
                                    'label' => esc_html__('Lightbox source', 'wp-load-gallery'),
                                    'name' => 'lightbox_source',
                                    'class' => 'lightbox_source',
                                    'lists' => array(
                                        array('value' => 'title', 'label' => esc_html__('Title', 'wp-load-gallery')),
                                        array('value' => 'caption', 'label' => esc_html__('Caption', 'wp-load-gallery')),
                                        array('value' => 'title_caption', 'label' => esc_html__('Title & Caption', 'wp-load-gallery'))
                                    )
                                )
                            );
                            ?>
                        </div>
                    </div>

                    <div class="wplg-panel-wrap wplg-panel-options-wrap wplg_box_closed">
                        <div class="main_option_title wplg_box">
                            <span class="dashicons dashicons-arrow-down wplg_box_icon"></span>
                            <span class="material-icons wplg_box_icon_before"> border_style </span>
                            <span class="main_option_title_title"><?php esc_html_e('Border', 'wp-load-gallery') ?></span>
                        </div>

                        <div class="wplg_box_content">
                            <?php \WPLG\Helper::renderFields('radio', array('label' => esc_html__('Enable border', 'wp-load-gallery'), 'name' => 'enable_border', 'class' => 'enable_border')) ?>
                            <?php \WPLG\Helper::renderFields('number', array(
                                    'label' => esc_html__('Border width (px)', 'wp-load-gallery'),
                                    'name' => 'border_width',
                                    'class' => 'border_width',
                                    'min' => 0,
                                    'max' => 20,
                                    'step' => 1
                                )
                            ) ?>

                            <div class="wplg-panel" style="text-align: center">
                                <div class="wplg-panel__main-title">
                                    <?php esc_html_e('Border color', 'wp-load-gallery') ?>
                                </div>

                                <div class="wplg-panel__header-options wplg-color-main">
                                    <div class="wplg-color-wrapper border_color_wrap">
                                        <?php foreach ($colors as $color => $label_color) : ?>
                                            <?php if ($color === '#ffffff'): ?>
                                                <div data-color="<?php echo esc_attr($color) ?>"
                                                     title="<?php echo esc_attr($label_color) ?>"
                                                     class="wplg-color wplg-border-color"
                                                     style="background: <?php echo esc_attr($color) ?>; border: #ccc 1px solid"></div>
                                            <?php else : ?>
                                                <div data-color="<?php echo esc_attr($color) ?>"
                                                     title="<?php echo esc_attr($label_color) ?>"
                                                     class="wplg-color wplg-border-color"
                                                     style="background: <?php echo esc_attr($color) ?>"></div>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </div>

                                    <div class="wplg-color-action">
                                        <input type="text" size="7"
                                               placeholder="<?php esc_attr_e('Custom color', 'wp-load-gallery') ?>"
                                               data-type="border_color_wrap" name="border_color"
                                               class="border_color wplg-input-color wplg-input">
                                        <button class="wplg-clear-color wplg-button" type="button"
                                                data-field="border_color_wrap"><?php esc_html_e('Clear', 'wp-load-gallery') ?></button>
                                    </div>
                                </div>
                            </div>

                            <?php \WPLG\Helper::renderFields('number', array(
                                    'label' => esc_html__('Border radius (px)', 'wp-load-gallery'),
                                    'name' => 'border_radius',
                                    'class' => 'border_radius',
                                    'min' => 0,
                                    'max' => 500,
                                    'step' => 1
                                )
                            );
                            ?>
                        </div>
                    </div>

                    <div class="wplg-panel-wrap wplg-panel-options-wrap wplg_box_closed">
                        <div class="main_option_title wplg_box">
                            <span class="dashicons dashicons-arrow-down wplg_box_icon"></span>
                            <span class="material-icons wplg_box_icon_before"> text_rotation_none </span>
                            <span class="main_option_title_title"><?php esc_html_e('Textpanel', 'wp-load-gallery') ?></span>
                        </div>

                        <div class="wplg_box_content">
                            <?php
                            \WPLG\Helper::renderFields('radio', array('label' => esc_html__('Enable', 'wp-load-gallery'), 'name' => 'enable_textpanel', 'class' => 'enable_textpanel'));
                            \WPLG\Helper::renderFields('radio', array('label' => esc_html__('Always on', 'wp-load-gallery'), 'name' => 'textpanel_always_on', 'class' => 'textpanel_always_on'));
                            \WPLG\Helper::renderFields('select', array(
                                    'label' => esc_html__('Source', 'wp-load-gallery'),
                                    'name' => 'textpanel_source',
                                    'class' => 'textpanel_source',
                                    'lists' => array(
                                        array('value' => 'title', 'label' => esc_html__('Title', 'wp-load-gallery')),
                                        array('value' => 'desc', 'label' => esc_html__('Caption', 'wp-load-gallery')),
                                        array('value' => 'title_and_desc', 'label' => esc_html__('Title & Caption', 'wp-load-gallery'))
                                    )
                                )
                            );
                            \WPLG\Helper::renderFields('select', array(
                                    'label' => esc_html__('Appear type', 'wp-load-gallery'),
                                    'name' => 'textpanel_appear_type',
                                    'class' => 'textpanel_appear_type',
                                    'lists' => array(
                                        array('value' => 'slide', 'label' => esc_html__('Slide', 'wp-load-gallery')),
                                        array('value' => 'fade', 'label' => esc_html__('Fade', 'wp-load-gallery'))
                                    )
                                )
                            );
                            \WPLG\Helper::renderFields('select', array(
                                    'label' => esc_html__('Position', 'wp-load-gallery'),
                                    'name' => 'textpanel_position',
                                    'class' => 'textpanel_position',
                                    'lists' => array(
                                        array('value' => 'inside_bottom', 'label' => esc_html__('Bottom', 'wp-load-gallery')),
                                        array('value' => 'inside_top', 'label' => esc_html__('Top', 'wp-load-gallery')),
                                        array('value' => 'middle', 'label' => esc_html__('Middle', 'wp-load-gallery'))
                                    )
                                )
                            );
                            \WPLG\Helper::renderFields('select', array(
                                    'label' => esc_html__('Align', 'wp-load-gallery'),
                                    'name' => 'textpanel_align',
                                    'class' => 'textpanel_align',
                                    'lists' => array(
                                        array('value' => 'left', 'label' => esc_html__('Left', 'wp-load-gallery')),
                                        array('value' => 'right', 'label' => esc_html__('Right', 'wp-load-gallery')),
                                        array('value' => 'center', 'label' => esc_html__('Center', 'wp-load-gallery'))
                                    )
                                )
                            );
                            \WPLG\Helper::renderFields('number', array(
                                    'label' => esc_html__('Title max length', 'wp-load-gallery'),
                                    'name' => 'textpanel_title_length',
                                    'class' => 'textpanel_title_length',
                                    'min' => 0
                                )
                            );
                            \WPLG\Helper::renderFields('number', array(
                                    'label' => esc_html__('Desc max length', 'wp-load-gallery'),
                                    'name' => 'textpanel_desc_length',
                                    'class' => 'textpanel_desc_length',
                                    'min' => 0
                                )
                            );
                            \WPLG\Helper::renderFields('number', array(
                                    'label' => esc_html__('Title size (px)', 'wp-load-gallery'),
                                    'name' => 'title_font_size',
                                    'class' => 'title_font_size',
                                    'min' => 8
                                )
                            );
                            \WPLG\Helper::renderFields('number', array(
                                    'label' => esc_html__('Desc size (px)', 'wp-load-gallery'),
                                    'name' => 'desc_font_size',
                                    'class' => 'desc_font_size',
                                    'min' => 8
                                )
                            );
                            \WPLG\Helper::renderFields('radio', array('label' => esc_html__('Enable background', 'wp-load-gallery'), 'name' => 'textpanel_enable_bg', 'class' => 'textpanel_enable_bg'));
                            ?>

                            <div class="wplg-panel" style="text-align: center">
                                <div class="wplg-panel__main-title">
                                    <?php esc_html_e('Background color', 'wp-load-gallery') ?>
                                </div>

                                <div class="wplg-panel__header-options wplg-color-main">
                                    <div class="wplg-color-wrapper textpanel_bg_color_wrap">
                                        <?php foreach ($colors as $color => $label_color) : ?>
                                            <?php if ($color === '#ffffff'): ?>
                                                <div data-color="<?php echo esc_attr($color) ?>"
                                                     title="<?php echo esc_attr($label_color) ?>"
                                                     class="wplg-color wplg-textpanel-bg-color"
                                                     style="background: <?php echo esc_attr($color) ?>; border: #ccc 1px solid"></div>
                                            <?php else : ?>
                                                <div data-color="<?php echo esc_attr($color) ?>"
                                                     title="<?php echo esc_attr($label_color) ?>"
                                                     class="wplg-color wplg-textpanel-bg-color"
                                                     style="background: <?php echo esc_attr($color) ?>"></div>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </div>

                                    <div class="wplg-color-action">
                                        <input type="text" size="7"
                                               placeholder="<?php esc_attr_e('Custom color', 'wp-load-gallery') ?>"
                                               data-type="textpanel_bg_color_wrap" name="textpanel_bg_color"
                                               class="textpanel_bg_color wplg-input-color wplg-input">
                                        <button class="wplg-clear-color wplg-button" type="button"
                                                data-field="textpanel_bg_color_wrap"><?php esc_html_e('Clear', 'wp-load-gallery') ?></button>
                                    </div>
                                </div>
                            </div>

                            <?php \WPLG\Helper::renderFields('number', array(
                                    'label' => esc_html__('Background opacity', 'wp-load-gallery'),
                                    'name' => 'textpanel_bg_opacity',
                                    'class' => 'textpanel_bg_opacity',
                                    'min' => 0,
                                    'max' => 1,
                                    'step' => 0.1
                                )
                            ) ?>

                            <div class="wplg-panel" style="text-align: center">
                                <div class="wplg-panel__main-title">
                                    <?php esc_html_e('Title color', 'wp-load-gallery') ?>
                                </div>

                                <div class="wplg-panel__header-options wplg-color-main">
                                    <div class="wplg-color-wrapper textpanel_title_color_wrap">
                                        <?php foreach ($colors as $color => $label_color) : ?>
                                            <?php if ($color === '#ffffff'): ?>
                                                <div data-color="<?php echo esc_attr($color) ?>"
                                                     title="<?php echo esc_attr($label_color) ?>"
                                                     class="wplg-color wplg-textpanel-title-color"
                                                     style="background: <?php echo esc_attr($color) ?>; border: #ccc 1px solid"></div>
                                            <?php else : ?>
                                                <div data-color="<?php echo esc_attr($color) ?>"
                                                     title="<?php echo esc_attr($label_color) ?>"
                                                     class="wplg-color wplg-textpanel-title-color"
                                                     style="background: <?php echo esc_attr($color) ?>"></div>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </div>

                                    <div class="wplg-color-action">
                                        <input type="text" size="7"
                                               placeholder="<?php esc_attr_e('Custom color', 'wp-load-gallery') ?>"
                                               data-type="textpanel_title_color_wrap" name="textpanel_title_color"
                                               class="textpanel_title_color wplg-input-color wplg-input">
                                        <button class="wplg-clear-color wplg-button" type="button"
                                                data-field="textpanel_title_color_wrap"><?php esc_html_e('Clear', 'wp-load-gallery') ?></button>
                                    </div>
                                </div>
                            </div>

                            <div class="wplg-panel" style="text-align: center">
                                <div class="wplg-panel__main-title">
                                    <?php esc_html_e('Description color', 'wp-load-gallery') ?>
                                </div>

                                <div class="wplg-panel__header-options wplg-color-main">
                                    <div class="wplg-color-wrapper textpanel_desc_color_wrap">
                                        <?php foreach ($colors as $color => $label_color) : ?>
                                            <?php if ($color === '#ffffff'): ?>
                                                <div data-color="<?php echo esc_attr($color) ?>"
                                                     title="<?php echo esc_attr($label_color) ?>"
                                                     class="wplg-color wplg-textpanel-desc-color"
                                                     style="background: <?php echo esc_attr($color) ?>; border: #ccc 1px solid"></div>
                                            <?php else : ?>
                                                <div data-color="<?php echo esc_attr($color) ?>"
                                                     title="<?php echo esc_attr($label_color) ?>"
                                                     class="wplg-color wplg-textpanel-desc-color"
                                                     style="background: <?php echo esc_attr($color) ?>"></div>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </div>

                                    <div class="wplg-color-action">
                                        <input type="text" size="7"
                                               placeholder="<?php esc_attr_e('Custom color', 'wp-load-gallery') ?>"
                                               data-type="textpanel_desc_color_wrap" name="textpanel_desc_color"
                                               class="textpanel_desc_color wplg-input-color wplg-input">
                                        <button class="wplg-clear-color wplg-button" type="button"
                                                data-field="textpanel_desc_color_wrap"><?php esc_html_e('Clear', 'wp-load-gallery') ?></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="wplg-panel-wrap wplg-panel-options-wrap wplg_box_closed">
                        <div class="main_option_title wplg_box">
                            <span class="dashicons dashicons-arrow-down wplg_box_icon"></span>
                            <span class="material-icons wplg_box_icon_before"> view_carousel </span>
                            <span class="main_option_title_title"><?php esc_html_e('Slider', 'wp-load-gallery') ?></span>
                        </div>

                        <div class="wplg_box_content">
                            <?php \WPLG\Helper::renderFields('radio', array('label' => esc_html__('Autoplay', 'wp-load-gallery'), 'name' => 'auto_animation', 'class' => 'auto_animation')) ?>
                            <?php \WPLG\Helper::renderFields('radio', array('label' => esc_html__('Navigation', 'wp-load-gallery'), 'name' => 'enable_navigation', 'class' => 'enable_navigation')) ?>
                            <?php \WPLG\Helper::renderFields('radio', array('label' => esc_html__('Arrows', 'wp-load-gallery'), 'name' => 'slider_arrows', 'class' => 'slider_arrows')) ?>
                            <?php \WPLG\Helper::renderFields('radio', array('label' => esc_html__('Vertical', 'wp-load-gallery'), 'name' => 'slider_vertical', 'class' => 'slider_vertical')) ?>
                            <?php \WPLG\Helper::renderFields('radio', array('label' => esc_html__('Center mode', 'wp-load-gallery'), 'name' => 'center_mode', 'class' => 'center_mode')) ?>
                            <?php \WPLG\Helper::renderFields('number', array(
                                    'label' => esc_html__('Duration (ms)', 'wp-load-gallery'),
                                    'name' => 'duration',
                                    'class' => 'duration',
                                    'min' => 500,
                                    'max' => 9000
                                )
                            );
                            \WPLG\Helper::renderFields('number', array(
                                    'label' => esc_html__('Num rows', 'wp-load-gallery'),
                                    'name' => 'grid_num_rows',
                                    'class' => 'grid_num_rows',
                                    'min' => 1
                                )
                            );
                            ?>
                        </div>
                    </div>

                    <div class="wplg-panel-wrap wplg-panel-options-wrap wplg_box_closed">
                        <div class="main_option_title wplg_box">
                            <span class="dashicons dashicons-arrow-down wplg_box_icon"></span>
                            <span class="material-icons wplg_box_icon_before"> view_quilt </span>
                            <span class="main_option_title_title"><?php esc_html_e('Justified Grid', 'wp-load-gallery') ?></span>
                            <span class="wplg_pro_version"><?php esc_html_e(' (Pro)', 'wp-load-gallery') ?></span>
                        </div>

                        <div class="wplg_box_content">
                            <?php \WPLG\Helper::renderFields('number', array(
                                    'label' => esc_html__('Row height (px)', 'wp-load-gallery'),
                                    'name' => 'justified_row_height',
                                    'class' => 'justified_row_height',
                                    'min' => 100
                                )
                            ) ?>
                        </div>
                    </div>

                    <div class="wplg-panel-wrap wplg-panel-options-wrap wplg_box_closed">
                        <div class="main_option_title wplg_box">
                            <span class="dashicons dashicons-arrow-down wplg_box_icon"></span>
                            <span class="material-icons-outlined wplg_box_icon_before"> view_compact </span>
                            <span class="main_option_title_title"><?php esc_html_e('Compact Slider', 'wp-load-gallery') ?></span>
                            <span class="wplg_pro_version"><?php esc_html_e(' (Pro)', 'wp-load-gallery') ?></span>
                        </div>

                        <div class="wplg_box_content">
                            <?php
                            \WPLG\Helper::renderFields('number', array(
                                    'label' => esc_html__('Gallery height', 'wp-load-gallery'),
                                    'name' => 'compact_height',
                                    'class' => 'compact_height',
                                    'min' => 100
                                )
                            );

                            \WPLG\Helper::renderFields('select', array(
                                    'label' => esc_html__('Panel Position', 'wp-load-gallery'),
                                    'name' => 'theme_panel_position',
                                    'class' => 'theme_panel_position',
                                    'lists' => array(
                                        array('value' => 'left', 'label' => esc_html__('Left', 'wp-load-gallery')),
                                        array('value' => 'right', 'label' => esc_html__('Right', 'wp-load-gallery')),
                                        array('value' => 'top', 'label' => esc_html__('Top', 'wp-load-gallery')),
                                        array('value' => 'bottom', 'label' => esc_html__('Bottom', 'wp-load-gallery')),
                                    )
                                )
                            );

                            \WPLG\Helper::renderFields('number', array(
                                    'label' => esc_html__('Number thumbnails', 'wp-load-gallery'),
                                    'name' => 'number_thumbnails',
                                    'class' => 'number_thumbnails',
                                    'min' => 2
                                )
                            );
                            ?>
                        </div>
                    </div>

                    <div class="wplg-panel-wrap wplg-panel-options-wrap wplg_box_closed">
                        <div class="main_option_title wplg_box">
                            <span class="dashicons dashicons-arrow-down wplg_box_icon"></span>
                            <span class="material-icons wplg_box_icon_before"> view_quilt </span>
                            <span class="main_option_title_title"><?php esc_html_e('Post Grid', 'wp-load-gallery') ?></span>
                            <span class="wplg_pro_version"><?php esc_html_e(' (Pro)', 'wp-load-gallery') ?></span>
                        </div>

                        <div class="wplg_box_content">
                            <?php
                            \WPLG\Helper::renderFields('number', array(
                                    'label' => esc_html__('Thumb width (px)', 'wp-load-gallery'),
                                    'name' => 'post_grid_thumb_width',
                                    'class' => 'post_grid_thumb_width',
                                    'min' => 50
                                )
                            );
                            \WPLG\Helper::renderFields('radio', array('label' => esc_html__('Show author', 'wp-load-gallery'), 'name' => 'show_author', 'class' => 'show_author'));
                            \WPLG\Helper::renderFields('radio', array('label' => esc_html__('Show date', 'wp-load-gallery'), 'name' => 'show_date', 'class' => 'show_date'));
                            ?>
                        </div>
                    </div>

                    <div class="wplg-panel-wrap wplg-panel-options-wrap wplg_box_closed">
                        <div class="main_option_title wplg_box">
                            <span class="dashicons dashicons-arrow-down wplg_box_icon"></span>
                            <span class="material-icons-outlined wplg_box_icon_before"> storefront </span>
                            <span class="main_option_title_title"><?php esc_html_e('On Product', 'wp-load-gallery') ?></span>
                            <span class="wplg_pro_version"><?php esc_html_e(' (Pro)', 'wp-load-gallery') ?></span>
                        </div>

                        <div class="wplg_box_content">
                            <?php \WPLG\Helper::renderFields('radio', array('label' => esc_html__('Enable price', 'wp-load-gallery'), 'name' => 'enable_price', 'class' => 'enable_price')) ?>
                        </div>
                    </div>

                    <div class="wplg-panel-wrap wplg-panel-options-wrap wplg_box_closed">
                        <div class="main_option_title wplg_box">
                            <span class="dashicons dashicons-arrow-down wplg_box_icon"></span>
                            <span class="material-icons-outlined wplg_box_icon_before"> folder </span>
                            <span class="main_option_title_title"><?php esc_html_e('On Category', 'wp-load-gallery') ?></span>
                            <span class="wplg_pro_version"><?php esc_html_e(' (Pro)', 'wp-load-gallery') ?></span>
                        </div>

                        <div class="wplg_box_content">
                            <?php \WPLG\Helper::renderFields('radio', array('label' => esc_html__('Include children', 'wp-load-gallery'), 'name' => 'include_children', 'class' => 'include_children')) ?>
                        </div>
                    </div>
                </form>
                <button type="button" class="wplg_save wplg-button" style="width: 100%">
                    <span class="material-icons-outlined">check</span>
                    <span>Save</span>
                </button>
            </div>
        </div>
    </div>
</div>