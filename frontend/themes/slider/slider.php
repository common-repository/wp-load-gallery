<?php
defined('ABSPATH') || die('No direct script access allowed!');
use WPLG\Helper;
$class = array('wplg-gallery', 'wplg-gallery-slider', 'wplg-overlay-box-hover');
$class[] = (!empty($params['scale_image'])) ? 'wplg-scale' : '';
$class[] = ($is_max_width) ? 'is_max_width' : '';
$class[] = (!empty($params['textpanel_align']) && in_array($params['textpanel_align'], array('left', 'right', 'center'))) ? 'wplg-textpanel-align-' . $params['textpanel_align'] : 'center';
$class[] = 'wplg-wrap-action-' . $link;
$class[] = 'wplg-ratio-' . $item_ratio;
$lazyload = ((int)$enable_pagination === 1 && $pagination_type === 'lazyload');
?>
<div class="<?php echo wplgSanitizeHtmlClasses($class) ?>"
     data-params="<?php echo esc_attr(json_encode($params)) ?>"
     data-theme="slider"
     style="clear: both; margin: 10px 0; <?php echo (!$is_max_width) ? 'width: ' . (int)$gallery_width . 'px' : '' ?>">
    <?php
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

    $class_image_effect = array();
    if (empty($params['enable_overlay'])) {
        if (!empty($params['enable_image_effect'])) {
            switch ($params['image_effect_type']) {
                case 'bw':
                    $class_image_effect[] = 'wplg-bw-effect';
                    break;
                case 'blur':
                    $class_image_effect[] = 'wplg-blur-effect';
                    break;
                case 'sepia':
                    $class_image_effect[] = 'wplg-sepia-effect';
                    break;
                case 'brightness':
                    $class_image_effect[] = 'wplg-brightness-effect';
                    break;
            }
        }
    }

    $style_overlay = array();
    $style_overlay[] = 'background-color: rgba(' . $overlay_color['red'] . ', ' . $overlay_color['green'] . ', ' . $overlay_color['blue'] . ', ' . $overlay_opacity . ')';
    $style_overlay[] = 'opacity: 0; z-index: 3; width: 100%; height: 100%; left: 0; top: 0; position: absolute; margin: 0';
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
        $enable_icon = (($attachment->post_title === '' && $attachment->post_excerpt === '' && !empty($params['enable_icons'])) || (($attachment->post_title !== '' || $attachment->post_excerpt !== '') && $params['textpanel_position'] !== 'middle' && !empty($params['enable_icons'])));
        ?>
        <div class="wplg-gallery-item"
             style="display: none; padding: <?php echo (int)$params['gutterwidth'] / 2 . 'px' ?>">
            <div class="wplg-gallery-box" style="<?php echo esc_attr($border_style) ?>">
                <div class="square_thumbnail">
                    <div class="img_centered">
                        <img data-lazy="<?php echo ($lazyload) ? esc_url($image_url) : '' ?>"
                             class="wplg-default-thumbnail wplg-gallery-thumbnail <?php echo implode(' ', $class_image_effect) ?>"
                             alt="<?php echo esc_attr($attachment->post_title) ?>"
                             src="<?php echo (!$lazyload) ? esc_url($image_url) : '' ?>">
                    </div>
                </div>

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
                <?php if (!empty($params['enable_overlay']) || !isset($params['enable_overlay'])) : ?>
                    <a class="wplg-thumb-overlay <?php echo ($is_video) ? 'isvideo' : '' ?>" <?php echo ($link === 'none') ? '' : 'href="' . esc_url($lightbox_url) . '"' ?>
                       target="_blank" data-title="<?php echo esc_attr($lightbox_text) ?>"
                       style="<?php echo esc_attr($style_overlay) ?>"></a>
                <?php else : ?>
                    <a class="wplg-thumb-overlay <?php echo ($is_video) ? 'isvideo' : '' ?>" <?php echo ($link === 'none') ? '' : 'href="' . esc_url($lightbox_url) . '"' ?>
                       target="_blank" data-title="<?php echo esc_attr($lightbox_text) ?>"
                       style="opacity: 0; z-index: 3; width: 100%; height: 100%; left: 0; top: 0; position: absolute; margin: 0; background-color: transparent !important;"></a>
                <?php endif; ?>

                <?php
                if ($enable_icon) {
                    switch ($link) {
                        case 'file':
                            if ($type === 'image') {
                                echo '<div class="wplg-gallery-icon wplg-icon-type wplg-icon-zoom"></div>';
                            } else {
                                echo '<div class="wplg-gallery-icon wplg-icon-type wplg-icon-play"></div>';
                            }
                            break;
                        case 'link':
                            echo '<div data-href="' . esc_url($lightbox_url) . '" class="wplg-gallery-icon wplg-icon-link"></div>';
                            break;
                        case 'none':
                            break;
                    }
                }
                ?>

                <?php if (isset($params['enable_textpanel']) && (int)$params['enable_textpanel'] === 1) : ?>
                    <div class="wplg-textpanel <?php echo (isset($params['textpanel_always_on']) && (int)$params['textpanel_always_on'] === 1) ? 'wplg-textpanel-always' : '' ?>"
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
    <?php
    endforeach;
    ?>
</div>