<?php
defined('ABSPATH') || die('No direct script access allowed!');
wp_enqueue_script(
    'wp-unite-tiles-js',
    WPLG_PLUGIN_URL . 'frontend/themes/tiles/ug-theme-tiles.js',
    array('jquery'),
    WPLG_VERSION
);

$class = array('wplg-gallery', 'wplg-overlay-box-hover');
$class[] = (!empty($params['scale_image'])) ? 'wplg-scale' : '';
$class[] = ($is_max_width) ? 'is_max_width' : '';
?>
<div id="wplg-gallery-tiles-<?php echo esc_attr($instance) ?>" class="<?php echo wplgSanitizeHtmlClasses($class) ?>"
     data-params="<?php echo esc_attr(json_encode($params)) ?>"
     data-theme="tiles"
     style="clear: both; margin: 10px 0; display:none; <?php echo (!$is_max_width) ? 'width: ' . (int)$gallery_width . 'px' : '' ?>">
    <?php
    foreach ($attachments as &$attachment) :
        $is_video = false;
        switch ($attachment->type) {
            case 'server_file':
                $type = 'image';
                $image_url = $attachment->url;
                $lightbox_url = $attachment->url;
                break;
            case 'youtube':
                $type = 'youtube';
                $is_video = true;
                $image_url = $attachment->url;
                $lightbox_url = 'https://www.youtube.com/watch?v=' . $attachment->ID;
                break;
            case 'vimeo':
                $type = 'vimeo';
                $is_video = true;
                $image_url = $attachment->url;
                $lightbox_url = 'https://player.vimeo.com/video/' . $attachment->ID;
                break;
            case 'google_drive_image':
                $type = 'image';
                $image_url = $attachment->url;
                $lightbox_url = $attachment->lightbox_url;
                break;
            case 'google_drive_video':
                $type = 'google_drive_video';
                $is_video = true;
                $image_url = $attachment->url;
                $lightbox_url = $attachment->lightbox_url;
                break;
            default:
                $type = 'image';
                if ($attachment->post_type === 'attachment') {
                    $image_url = wp_get_attachment_image_url($attachment->ID, $size);
                    switch ($link) {
                        case 'file':
                            $lightbox_url = wp_get_attachment_image_url($attachment->ID, $targetsize);
                            break;
                        case 'link':
                            $lightbox_url = get_post_meta($attachment->ID, 'wplg_image_custom_link', true);
                            if (empty($lightbox_url)) {
                                $lightbox_url = wp_get_attachment_image_url($attachment->ID, $targetsize);
                            }
                            break;
                        case 'none':
                            $lightbox_url = '';
                            break;
                        default:
                            $lightbox_url = wp_get_attachment_image_url($attachment->ID, $targetsize);
                    }
                } else {
                    if ($link === 'link') {
                        $lightbox_url = get_permalink($attachment->ID);
                    } else {
                        $lightbox_url = wp_get_attachment_image_url(get_post_thumbnail_id($attachment->ID), $targetsize);
                    }

                    $image_url = wp_get_attachment_image_url(get_post_thumbnail_id($attachment->ID), $size);
                    if (!$image_url) {
                        $image_url = WPLG_PLUGIN_URL . 'assets/images/icons8-news.svg';
                    }

                    if ($attachment->post_type === 'product' && !empty($params['enable_price'])) {
                        if (function_exists('wc_get_product') && function_exists('get_woocommerce_currency_symbol')) {
                            $product = wc_get_product($attachment->ID);
                            $currency = get_woocommerce_currency_symbol();
                            $attachment->post_excerpt = $product->get_price() . $currency;
                        }
                    }
                }
        }
        $attachment->post_title = esc_html(strip_tags(strip_shortcodes($attachment->post_title)));
        if (strlen($attachment->post_title) > $textpanel_title_length) {
            $attachment->post_title = substr($attachment->post_title, 0, $textpanel_title_length) . '...';
        }

        if (trim($attachment->post_excerpt) === '') {
            $attachment->post_excerpt = esc_html(strip_tags(strip_shortcodes($attachment->post_content)));
        }
        if (strlen($attachment->post_excerpt) > $textpanel_desc_length) {
            $attachment->post_excerpt = substr($attachment->post_excerpt, 0, $textpanel_desc_length) . '...';
        }
        ?>
        <a href="<?php echo esc_url($lightbox_url) ?>">
            <img alt="<?php echo esc_attr($attachment->post_title) ?>"
                 src="<?php echo esc_url($image_url) ?>"
                 data-image="<?php echo esc_url($lightbox_url) ?>"
                 data-type="<?php echo esc_attr($type) ?>"
                 data-description="<?php echo esc_attr($attachment->post_excerpt) ?>"
                <?php echo ($is_video) ? 'data-videoid="' . esc_attr($attachment->ID) . '"' : '' ?>
                 style="display:none">
        </a>
    <?php
    endforeach;
    ?>
</div>