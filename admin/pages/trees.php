<?php
/* Prohibit direct script loading */
defined('ABSPATH') || die('No direct script access allowed!');
?>
<div class="wplg_admin_wrap">
    <?php wp_nonce_field('wpgallery', '_wpnonce', true, true); ?>
    <div id="gallerylist" class="gallerylist wplg-left-panel">
        <div class="wplg-admin-overlay-tree"></div>
        <div class="topbtn">
            <a href="#add-gallery-box" class="add-gallery-box wplg-button wplg-button-colored">
                <i class="zmdi zmdi-plus"></i>
                <span><?php esc_html_e('Create', 'wp-load-gallery') ?></span>
            </a>
        </div>
        <div class="gallery_tree wplg_tree"></div>

        <div class="wplg_upload_image_wrap wplg_dl white-popup mfp-hide" id="wplg_upload_image_from_pc">
            <div class="wplg_dl_header">
                <label><?php esc_html_e('Choose the files from your PC', 'wp-load-gallery') ?></label>
            </div>
            <form id="wplg_form_upload" method="post"
                  action="<?php echo esc_html(admin_url('admin-ajax.php')) ?>"
                  enctype="multipart/form-data">
                <div class="wplg_dl_content">
                    <input class="wplg_gallery_file" type="file" name="wplg_gallery_file[]" accept="image/*" multiple
                           style="display: none">
                    <input type="hidden" name="wplg_nonce"
                           value="<?php echo esc_html(wp_create_nonce('wplg_nonce')) ?>">
                    <input type="hidden" name="action" value="wpgallery">
                    <input type="hidden" name="wplg_gallery_id" class="wplg_gallery_id" value="0">
                    <input type="hidden" name="task" value="wplg_upload">

                    <div class="wplg-file-lists">
                        <div class="template-row">
                            <div class="upload-thumbnail">
                                <img class="" src="">
                            </div>

                            <div class="upload-file-info">
                                <div class="upload-img-defails">
                                    <div class="file-name"></div>
                                    <div class="file-size"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="wplg-upload-process-wrap" style="display: none">
                        <div class="wplg-upload-progress">
                            <div
                                    class="progress progress-striped active ui-progressbar
                             ui-widget ui-widget-content ui-corner-all"
                                    role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
                                <div class="ui-progressbar-value ui-widget-header ui-corner-left"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="wplg_dl_buttons">
                    <span class="spinner" style="float: none;"></span>
                    <button class="wplg-button wplg_dl_cancel"
                            type="button"><?php esc_html_e('Cancel', 'wp-load-gallery') ?></button>
                    <button type="button" class="wplg-button wplg-select-files-btn">
                        <span><?php esc_html_e('Select files', 'wp-media-folder-gallery-addon') ?></span>
                        <span class="count_files"></span>
                    </button>
                    <button class="wplg-button wplg-button-colored wplg_upload_img_button"
                            type="button"><?php esc_html_e('Upload', 'wp-load-gallery') ?></button>
                </div>
            </form>
        </div>

        <div class="wplg_server_folder wplg_dl white-popup mfp-hide" id="wplg_server_folder">
            <div class="wplg_dl_buttons">
                <span class="spinner" style="float: none;"></span>
                <button class="wplg-button wplg_dl_cancel"
                        type="button"><?php esc_html_e('Cancel', 'wp-load-gallery') ?></button>
                <button class="wplg-button wplg-button-colored wplg_server_folder_button_add"
                        type="button"><?php esc_html_e('Add folder(s)', 'wp-load-gallery') ?></button>
            </div>
        </div>

        <div class="wplg_youtube wplg_dl white-popup mfp-hide" id="wplg_youtube">
            <div style="width: 100%; display: inline-block; box-sizing: border-box">
                <input type="text" class="wplg-input wplg-video-url" style="width: 100%"
                       placeholder="<?php esc_html_e('Youtube URL', 'wp-load-gallery') ?>">
            </div>
            <div class="wplg_dl_buttons">
                <span class="spinner" style="float: none;"></span>
                <button class="wplg-button wplg_dl_cancel"
                        type="button"><?php esc_html_e('Cancel', 'wp-load-gallery') ?></button>
                <button class="wplg-button wplg-button-colored wplg_video_button_add" type="button"
                        data-type="youtube"><?php esc_html_e('Add video', 'wp-load-gallery') ?></button>
            </div>
        </div>

        <div class="wplg_vimeo wplg_dl white-popup mfp-hide" id="wplg_vimeo">
            <div style="width: 100%; display: inline-block; box-sizing: border-box">
                <input type="text" class="wplg-input wplg-video-url" style="width: 100%"
                       placeholder="<?php esc_html_e('Vimeo URL', 'wp-load-gallery') ?>">
            </div>
            <div class="wplg_dl_buttons">
                <span class="spinner" style="float: none;"></span>
                <button class="wplg-button wplg_dl_cancel"
                        type="button"><?php esc_html_e('Cancel', 'wp-load-gallery') ?></button>
                <button class="wplg-button wplg-button-colored wplg_video_button_add" type="button"
                        data-type="vimeo"><?php esc_html_e('Add video', 'wp-load-gallery') ?></button>
            </div>
        </div>

        <div class="wplg_google_drive wplg_dl white-popup mfp-hide" id="wplg_google_drive">
            <div class="wplg_dl_header">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="48px" height="48px">
                    <path fill="#FFC107" d="M17 6L31 6 45 30 31 30z"/>
                    <path fill="#1976D2" d="M9.875 42L16.938 30 45 30 38 42z"/>
                    <path fill="#4CAF50" d="M3 30.125L9.875 42 24 18 17 6z"/>
                </svg>
                <label><?php esc_html_e('Google Drive', 'wp-load-gallery') ?></label>
            </div>

            <div class="wplg_dl_content">
                <div class="google_drive_results">
                    <div class="google_drive_results_loading"
                         style="width: 100%; display: inline-block; text-align: center">
                        <img src="<?php echo esc_url(WPLG_PLUGIN_URL . '/assets/images/loader_skype_trans.gif') ?>">
                    </div>
                </div>
            </div>

            <div class="wplg_dl_buttons">
                <span class="spinner" style="float: none;"></span>
                <button class="wplg-button wplg_dl_cancel"
                        type="button"><?php esc_html_e('Cancel', 'wp-load-gallery') ?></button>
                <button class="wplg-button wplg-button-colored wplg_add_google_drive_file" type="button"
                        data-type="google_drive"><?php esc_html_e('Add', 'wp-load-gallery') ?></button>
            </div>
        </div>

        <div class="wplg_posts wplg_dl white-popup mfp-hide" id="wplg_posts">
            <div class="wplg_dl_header">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="48px"
                     height="48px">
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
                <label><?php esc_html_e('Select post type to display inside the current gallery', 'wp-load-gallery') ?></label>
            </div>

            <div class="wplg_dl_content">
                <?php
                $all_post_types = \WPLG\Helper::getAllPostTypes();
                ?>
                <div class="wplg_filter_post_type_wrap">
                    <div class="wplg_filter_post_type">
                        <label><b><?php esc_html_e('Post types', 'wp-load-gallery') ?></b></label>
                        <select class="wplg-select wplg-select-post-type">
                            <?php foreach ($all_post_types as $_post_type => $post_type_label) : ?>
                                <option value="<?php echo esc_attr($_post_type) ?>"><?php echo esc_html($post_type_label); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="wplg_filter_post_type">
                        <label><b><?php esc_html_e('Category/Taxonomy terms', 'wp-load-gallery') ?></b></label>
                        <select class="wplg-select wplg-select-taxonomy"></select>
                    </div>
                </div>

                <div class="posts_results">
                    <div class="posts_results_overlay"></div>
                    <div class="wplg-categories-tree wplg_tree">

                    </div>

                    <div class="wplg-posts-item-wrap">
                        <div class="wplg_post_output">

                        </div>
                        <div class="wplg-pages-wrap">
                            <button type="button"
                                    class="wplg-button wplg-loadmore-posts"><?php esc_html_e('Load more', 'wp-load-gallery') ?></button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="wplg_dl_buttons">
                <span class="spinner" style="float: none;"></span>
                <button class="wplg-button wplg_dl_cancel"
                        type="button"><?php esc_html_e('Cancel', 'wp-load-gallery') ?></button>
                <button class="wplg-button wplg-button-colored wplg_add_this_category_btn" type="button"
                        data-type="posts"><?php esc_html_e('Add this category', 'wp-load-gallery') ?></button>
                <button class="wplg-button wplg-button-colored wplg_add_posts_btn" type="button"
                        data-type="posts"><?php esc_html_e('Add posts', 'wp-load-gallery') ?></button>
            </div>
        </div>

        <div class="wplg_media_category wplg_dl white-popup mfp-hide" id="wplg_media_category">
            <div class="wplg_dl_header">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="48px" height="48px">
                    <linearGradient id="eo9Iz~gJX5QQxF9vIcujya" x1="41.018" x2="45.176" y1="26" y2="26"
                                    gradientUnits="userSpaceOnUse">
                        <stop offset="0" stop-color="#3537b0"></stop>
                        <stop offset="1" stop-color="#4646cf"></stop>
                    </linearGradient>
                    <path fill="url(#eo9Iz~gJX5QQxF9vIcujya)"
                          d="M43,11h-3v30h3c1.105,0,2-0.895,2-2V13C45,11.895,44.105,11,43,11z"></path>
                    <path fill="#5286ff"
                          d="M41,39V9c0-1.105-0.895-2-2-2H5C3.895,7,3,7.895,3,9v30c0,1.105,0.895,2,2,2h38	C41.895,41,41,40.105,41,39z"></path>
                    <path fill="#fff"
                          d="M37,17H7c-0.552,0-1-0.448-1-1v-2c0-0.552,0.448-1,1-1h30c0.552,0,1,0.448,1,1v2	C38,16.552,37.552,17,37,17z"></path>
                    <path fill="#fff"
                          d="M19,36H7c-0.552,0-1-0.448-1-1V22c0-0.552,0.448-1,1-1h12c0.552,0,1,0.448,1,1v13	C20,35.552,19.552,36,19,36z"></path>
                    <path fill="#fff" d="M38,24H24v-2c0-0.552,0.448-1,1-1h12c0.552,0,1,0.448,1,1V24z"></path>
                    <rect width="14" height="3" x="24" y="24" fill="#e6eeff"></rect>
                    <rect width="14" height="3" x="24" y="27" fill="#ccdcff"></rect>
                    <rect width="14" height="3" x="24" y="30" fill="#b3cbff"></rect>
                    <path fill="#9abaff" d="M37,36H25c-0.552,0-1-0.448-1-1v-2h14v2C38,35.552,37.552,36,37,36z"></path>
                </svg>
                <label><?php esc_html_e('Media category', 'wp-load-gallery') ?></label>
            </div>

            <div class="wplg_dl_content">
                <?php
                $media_taxonomies = get_taxonomies_for_attachments();
                ?>
                <select class="wplg-select wplg-select-media-taxonomy">
                    <option value="0"><?php esc_html_e('Choose a taxonomy', 'wp-load-gallery') ?></option>
                    <?php
                    foreach ($media_taxonomies as $media_taxonomy) {
                        if ($media_taxonomy === WPLG_TAXONOMY) {
                            continue;
                        }
                        $taxonomy_details = get_taxonomy($media_taxonomy);
                        echo '<option value="' . esc_attr($media_taxonomy) . '">' . esc_html($taxonomy_details->label) . '</option>';
                    }
                    ?>
                </select>
            </div>

            <div class="wplg_dl_buttons">
                <span class="spinner" style="float: none;"></span>
                <button class="wplg-button wplg_dl_cancel"
                        type="button"><?php esc_html_e('Cancel', 'wp-load-gallery') ?></button>
                <button class="wplg-button wplg-button-colored wplg_add_media_category_btn"
                        type="button"><?php esc_html_e('Add', 'wp-load-gallery') ?></button>
            </div>
        </div>
    </div>

    <div class="wplg-tree-toggle">
        <i class="dashicons dashicons-leftright wplg-tree-toggle-icon"></i>
    </div>

    <?php
    require_once(WPLG_PLUGIN_DIR . '/admin/pages/gallery_details.php');
    ?>
</div>