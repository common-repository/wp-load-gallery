<?php
defined('ABSPATH') || die('No direct script access allowed!');
?>
<div class="wplg_admin_wrap">
    <div class="wplg-left-panel">
        <ul class="wplg-cloud-tab-list">
            <li class="active" data-tab="google_drive">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="18px" height="18px">
                    <path fill="#FFC107" d="M17 6L31 6 45 30 31 30z"/>
                    <path fill="#1976D2" d="M9.875 42L16.938 30 45 30 38 42z"/>
                    <path fill="#4CAF50" d="M3 30.125L9.875 42 24 18 17 6z"/>
                </svg>
                <a><?php esc_html_e('Google Drive', 'wp-load-gallery') ?></a>
            </li>
        </ul>
    </div>

    <div class="edit_wrap">
        <form method="post" action="">
            <div class="wplg-cloud-tab-content" data-tab="google_drive" style="display: block">
                <h1 style="padding: 10px 20px"><?php esc_html_e('Google Drive', 'wp-load-gallery') ?></h1>
                <div class="wplg-cloud-setting-row" style="width: calc(100% - 40px); margin-bottom: 20px">
                    <?php
                    if (isset($gdconfigs['ClientId']) && $gdconfigs['ClientId'] !== ''
                        && isset($gdconfigs['ClientSecret']) && $gdconfigs['ClientSecret'] !== '') {
                        if (empty($gdconfigs['connected'])) {
                            $urlGoogle = $googleDrive->getAuthorisationUrl();
                            ?>
                            <a class="wplg-button wplg-button-colored" href="#"
                               onclick="window.location.assign('<?php echo esc_html($urlGoogle); ?>','foo','width=600,height=600');return false;"
                               style="margin-left: 5px">
                                <?php esc_html_e('Connect Google Drive', 'wp-load-gallery') ?>
                            </a>

                            <?php
                        } else {
                            ?>
                            <a class="wplg-button"
                               href="<?php echo esc_url('admin.php?page=load_gallery_cloud&action=google_drive_disconnect') ?>">
                                <?php esc_html_e('Disconnect Google Drive', 'wp-load-gallery') ?></a>
                            <?php
                        }
                    }
                    ?>

                    <a class="wplg-button" href="http://gridgallerys.com/document/"
                       target="_blank"><?php esc_html_e('Read online document', 'wp-load-gallery') ?></a>
                </div>
                <div class="wplg-cloud-settings">
                    <div class="wplg-cloud-setting-row" style="width: 100%">
                        <h4><?php esc_html_e('Drive type', 'wp-load-gallery') ?></h4>
                        <div>
                            <select name="wplg_google_drive[drive_type]" class="wplg-select">
                                <option value="my_drive" <?php selected($gdconfigs['drive_type'], 'my_drive') ?>><?php esc_html_e('My Drive', 'wp-load-gallery') ?></option>
                                <option value="team_drive" <?php selected($gdconfigs['drive_type'], 'team_drive') ?>><?php esc_html_e('Shared drives', 'wp-load-gallery') ?></option>
                            </select>
                        </div>
                    </div>

                    <div class="wplg-cloud-setting-row">
                        <h4><?php esc_html_e('Google Client ID', 'wp-load-gallery') ?></h4>
                        <div>
                            <input title="" name="wplg_google_drive[ClientId]" type="text" class="wplg-input"
                                   value="<?php echo esc_attr($gdconfigs['ClientId']) ?>">
                            <p class="description" id="tagline-description"><?php esc_html_e('The Client ID for Web application available in your google Developers Console.
                            Click on documentation link below for more info', 'wp-load-gallery') ?>
                            </p>
                        </div>
                    </div>

                    <div class="wplg-cloud-setting-row">
                        <h4><?php esc_html_e('Google Client Secret', 'wp-load-gallery') ?></h4>
                        <div>
                            <input title="" name="wplg_google_drive[ClientSecret]" type="text"
                                   class="wplg-input" value="<?php echo esc_attr($gdconfigs['ClientSecret']) ?>">
                            <p class="description" id="tagline-description">
                                <?php esc_html_e('The Client secret for Web application available in your google Developers Console.
                            Click on documentation link below for more info', 'wp-load-gallery') ?></p>
                        </div>
                    </div>

                    <div class="wplg-cloud-setting-row">
                        <h4><?php esc_html_e('JavaScript origins', 'wp-load-gallery') ?></h4>
                        <div>
                            <input title="" name="javaScript_origins" type="text" readonly
                                   value="<?php echo esc_attr(site_url()); ?>" class="wplg-input">
                        </div>
                    </div>

                    <div class="wplg-cloud-setting-row">
                        <h4><?php esc_html_e('Redirect URIs', 'wp-load-gallery') ?></h4>
                        <div>
                            <input title="" name="redirect_uris" type="text" readonly=""
                                   value="<?php echo esc_url(admin_url('admin.php?page=load_gallery_cloud&action=google_drive_connect')) ?>"
                                   class="wplg-input">
                        </div>
                    </div>
                </div>
            </div>

            <div class="wplg-cloud-setting-row">
                <button type="submit" name="wpgc_save_cloud_configs_btn"
                        class="wplg-button wplg-button-colored"><?php esc_html_e('Save changes', 'wp-load-gallery') ?></button>
            </div>
        </form>
    </div>
</div>