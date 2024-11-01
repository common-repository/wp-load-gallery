<?php
defined('ABSPATH') || die('No direct script access allowed!');
use WPLG\Helper;
?>
<?php if (empty($load_more)) :
    $class = array('wplg-gallery-default', 'wplg-gallery');
    $class[] = ($is_max_width) ? 'is_max_width' : '';
    $class[] = (!empty($params['textpanel_align']) && in_array($params['textpanel_align'], array('left', 'right', 'center'))) ? 'wplg-textpanel-align-' . $params['textpanel_align'] : 'center';
    $class[] = 'wplg-wrap-action-' . $link;
    if (isset($params['item_ratio']) && $params['item_ratio'] !== 'default') {
        $ratios = explode('-', $params['item_ratio']);
        $ratio = (int)$ratios[0] / (int)$ratios[1];
    } else {
        $ratio = 4 / 3;
    }
    ?>
    <div class="<?php echo wplgSanitizeHtmlClasses($class) ?>"
    data-params="<?php echo esc_attr(json_encode($params)) ?>"
    data-ratio="<?php echo esc_attr($ratio) ?>"
    data-theme="flip_box"
    style="clear: both; margin: 10px 0; <?php echo (!$is_max_width) ? 'width: ' . (int)$gallery_width . 'px' : '' ?>">
    <img style="margin: 0 auto !important;" class="wplg-gallery-loader"
         src="<?php echo esc_url(WPLG_PLUGIN_URL . '/assets/images/loader_skype_trans.gif') ?>">
<?php endif; ?>
<?php
$item_width = 'calc(100%/' . (int)$params['columns'] . ')';
$border_style = array();
if (!empty($params['enable_border'])) {
    if (isset($params['border_width'])) {
        $border_style[] = 'border-width: ' . $params['border_width'] . 'px';
    } else {
        $border_style[] = 'border-width: 3px';
    }

    if (isset($params['border_width'])) {
        $border_style[] = 'border-color: ' . $params['border_color'];
    } else {
        $border_style[] = 'border-color: #cabdbf';
    }

    $border_style[] = 'border-style: solid';

    if (isset($params['border_radius'])) {
        $border_style[] = 'border-radius: ' . $params['border_radius'] . 'px';
    } else {
        $border_style[] = 'border-radius: 0';
    }
}
$border_style = implode(';', $border_style);
$overlay_color = (isset($params['overlay_color']) && $params['overlay_color'] !== '') ? esc_attr($params['overlay_color']) : '#000000';
$overlay_color = Helper::hex2rgb($overlay_color);
$overlay_opacity = isset($params['overlay_opacity']) ? esc_attr($params['overlay_opacity']) : 0.4;

$textpanel_bg_color = (isset($params['textpanel_bg_color']) && $params['textpanel_bg_color'] !== '') ? esc_attr($params['textpanel_bg_color']) : '#000000';
if ($textpanel_bg_color === 'transparent') {
    $textpanel_bg_opacity = 0;
    $textpanel_bg_color = '#000000';
} else {
    $textpanel_bg_opacity = isset($params['textpanel_bg_opacity']) ? esc_attr($params['textpanel_bg_opacity']) : 0.4;
}

$textpanel_bg_color = Helper::hex2rgb($textpanel_bg_color);
$style_overlay = array();
$style_overlay[] = 'background-color: rgba(' . $overlay_color['red'] . ', ' . $overlay_color['green'] . ', ' . $overlay_color['blue'] . ', ' . $overlay_opacity . ')';
$style_overlay = implode(';', $style_overlay);

$style_panel = array();
$style_panel[] = 'background-color: rgba(' . $textpanel_bg_color['red'] . ', ' . $textpanel_bg_color['green'] . ', ' . $textpanel_bg_color['blue'] . ', ' . $textpanel_bg_opacity . ')';
$style_panel = implode(';', $style_panel);
$title_color = isset($params['textpanel_title_color']) ? esc_attr($params['textpanel_title_color']) : '#ffffff';
$desc_color = isset($params['textpanel_desc_color']) ? esc_attr($params['textpanel_desc_color']) : '#ffffff';
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
    <div class="wplg-gallery-item wplg-gallery-box wplg-flip-item"
         style="width: <?php echo $item_width . ';' ?> padding: <?php echo (int)$params['gutterwidth'] / 2 . 'px' ?> ">
        <div class="wplg-flip-item-inner"
             style="width: calc(100% - <?php echo (int)$params['gutterwidth'] . 'px' ?>); height: calc(100% - <?php echo (int)$params['gutterwidth'] . 'px' ?>); left: <?php echo ((int)$params['gutterwidth']) / 2 . 'px' ?>; top: <?php echo ((int)$params['gutterwidth']) / 2 . 'px' ?>">
            <div class="wplg-flip-item-media-holder tg-light wplg-flip-item-front">
                <div class="wplg-flip-item-front-inner" style="<?php echo esc_attr($style_overlay) ?>">
                    <div class="wplg-flip-item-media-inner">
                        <img class="flip-box-front wplg-default-thumbnail wplg-gallery-thumbnail"
                             alt="<?php echo esc_attr($attachment->post_title) ?>"
                             src="<?php echo esc_url($image_url) ?>"
                             style="<?php echo esc_attr($border_style) ?>"
                        >
                    </div>
                </div>
            </div>
            <div class="wplg-flip-item-content-holder tg-light image-format wplg-flip-item-back">
                <div class="wplg-flip-item-back-inner">
                    <?php
                    switch ($params['lightbox_source']) {
                        case 'title':
                            $lightbox_text = $attachment->post_title;
                            break;
                        case 'caption':
                            $lightbox_text = $attachment->post_excerpt;
                            break;
                        case 'title_caption':
                            $lightbox_text = ($attachment->post_excerpt !== '') ? $attachment->post_title . ' - ' . $attachment->post_excerpt : $attachment->post_title;
                            break;
                    }
                    ?>
                    <a class="wplg-flip-item-overlay wplg-thumb-overlay <?php echo ($is_video) ? 'isvideo' : '' ?>" <?php echo ($link === 'none') ? '' : 'href="' . esc_url($lightbox_url) . '"' ?>
                       target="_blank" data-title="<?php echo esc_attr($lightbox_text) ?>"
                       style="<?php echo esc_attr($style_overlay) ?>"></a>
                    <div class="tg-center-holder">
                        <div class="tg-center-inner">

                            <?php if (isset($params['enable_textpanel']) && (int)$params['enable_textpanel'] === 1) : ?>
                                <div class="flip-box-back wplg-textpanel wplg-textpanel-always"
                                     style="<?php echo esc_attr($style_panel) ?>">
                                    <div class="wplg-textpanel-textwrapper">
                                        <?php
                                        switch ($params['textpanel_source']) {
                                            case 'title':
                                                echo '<div class="wplg-textpanel-title" style="color: ' . esc_attr($title_color) . '; font-size: ' . esc_attr($title_font_size) . 'px">' . esc_html($attachment->post_title) . '</div>';
                                                break;
                                            case 'desc':
                                                echo '<div class="wplg-textpanel-description" style="color: ' . esc_attr($desc_color) . '; font-size: ' . esc_attr($desc_font_size) . 'px">' . esc_html($attachment->post_excerpt) . '</div>';
                                                break;
                                            case 'title_and_desc':
                                                echo '<div class="wplg-textpanel-title" style="color: ' . esc_attr($title_color) . '; font-size: ' . esc_attr($title_font_size) . 'px">' . esc_html($attachment->post_title) . '</div>';
                                                echo '<div class="wplg-textpanel-description" style="color: ' . esc_attr($desc_color) . '; font-size: ' . esc_attr($desc_font_size) . 'px">' . esc_html($attachment->post_excerpt) . '</div>';
                                                break;
                                        }
                                        ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
endforeach;
?>
<?php if (empty($load_more)) : ?>
    </div>
<?php endif; ?>