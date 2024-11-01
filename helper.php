<?php

namespace WPLG;
class Helper
{
    /**
     * Check if the plugin need to run an update of db or options
     *
     * @return void
     */
    public static function createTable()
    {
        $db_installed = get_option('wplg_version_1_0_0', false);
        if (!$db_installed) {
            global $wpdb;
            $wpdb->query('CREATE TABLE `' . $wpdb->prefix . 'wplg_gallery_items` (
                      `id` int(20) unsigned NOT NULL AUTO_INCREMENT,
                      `source_id` text NOT NULL,
                      `gallery_id` int(20) NOT NULL,
                      `type` text NOT NULL,
                      `post_title` longtext DEFAULT NULL,
                      `post_excerpt` longtext DEFAULT NULL,
                      `post_date` datetime DEFAULT NULL,
                      `tags` text DEFAULT NULL,
                      `thumbnail` varchar(255) DEFAULT NULL,
                      `custom_link` varchar(255) DEFAULT NULL,
                      `options` longtext DEFAULT NULL,
                      `status` tinyint(1) NOT NULL DEFAULT 1,
                      PRIMARY KEY  (id)
                    ) ENGINE=InnoDB CHARSET=utf8');
            update_option('wplg_version_1_0_0', WPLG_VERSION);
        }
    }

    /**
     * Get size list
     *
     * @return array
     */
    public static function getSizesList()
    {
        $sizes = apply_filters('image_size_names_choose', array(
            'thumbnail' => __('Thumbnail', 'wp-load-gallery'),
            'medium' => __('Medium', 'wp-load-gallery'),
            'large' => __('Large', 'wp-load-gallery'),
            'full' => __('Full Size', 'wp-load-gallery'),
        ));

        return $sizes;
    }

    /**
     * Get themes list
     *
     * @return array
     */
    public static function getThemesList()
    {
        $themes_array = array(
            'default' => array('label' => esc_html__('Default', 'wp-load-gallery'), 'img' => WPLG_PLUGIN_URL . 'assets/images/demo/gallery-default.jpg', 'version' => 'free'),
            'masonry' => array('label' => esc_html__('Masonry', 'wp-load-gallery'), 'img' => WPLG_PLUGIN_URL . 'assets/images/demo/gallery-masonry.jpg', 'version' => 'free'),
            'slider' => array('label' => esc_html__('Slider', 'wp-load-gallery'), 'img' => WPLG_PLUGIN_URL . 'assets/images/demo/gallery-slider.jpg', 'version' => 'free'),
            'flex' => array('label' => esc_html__('Flex', 'wp-load-gallery'), 'img' => WPLG_PLUGIN_URL . 'assets/images/demo/gallery-flex.jpg', 'version' => 'free'),
            'justified_grid' => array('label' => esc_html__('Justified grid', 'wp-load-gallery'), 'img' => WPLG_PLUGIN_URL . 'assets/images/demo/gallery-justified.jpg', 'version' => 'pro'),
            'compact' => array('label' => esc_html__('Compact Slider', 'wp-load-gallery'), 'img' => WPLG_PLUGIN_URL . 'assets/images/demo/gallery-compact.jpg', 'version' => 'pro'),
            'tiles' => array('label' => esc_html__('Nested grid', 'wp-load-gallery'), 'img' => WPLG_PLUGIN_URL . 'assets/images/demo/gallery-nested.jpg', 'version' => 'pro'),
            'portfolio' => array('label' => esc_html__('Portfolio', 'wp-load-gallery'), 'img' => WPLG_PLUGIN_URL . 'assets/images/demo/gallery-portfolio.jpg', 'version' => 'pro'),
            'flip_box' => array('label' => esc_html__('Flip box', 'wp-load-gallery'), 'img' => WPLG_PLUGIN_URL . 'assets/images/demo/gallery-flipbox.jpg', 'version' => 'pro'),
            'square_grid' => array('label' => esc_html__('Square grid', 'wp-load-gallery'), 'img' => WPLG_PLUGIN_URL . 'assets/images/demo/gallery-square_grid.jpg', 'version' => 'pro'),
            'post_grid' => array('label' => esc_html__('Post grid', 'wp-load-gallery'), 'img' => WPLG_PLUGIN_URL . 'assets/images/demo/gallery-post_grid.jpg', 'version' => 'pro'),
        );

        return $themes_array;
    }

    /**
     * Get theme, if not exist return default theme
     *
     * @param string $theme Theme name
     *
     * @return string
     */
    public static function getTheme($theme)
    {
        $allow_themes = array(
            'flex',
            'compact',
            'square_grid',
            'flip_box',
            'default',
            'masonry',
            'slider',
            'tiles',
            'justified_grid',
            'portfolio',
            'post_grid'
        );

        if (!in_array($theme, $allow_themes)) {
            $theme = 'default';
        }

        return $theme;
    }

    public static function getDefaultOptions($theme = 'default')
    {
        $options = array(
            'background_color' => 'transparent',
            'item_ratio' => 'default',
            'animationDuration' => 200,
            'gallery_navigation' => 0,
            'navigation_type' => 'menu',
            'gallery_full_width' => 1,
            'gallery_width' => 1200,
            'gutterwidth' => 10,
            'columns' => 3,
            'max_items' => 50,
            'size' => 'medium',
            'targetsize' => 'large',
            'link' => 'file',
            'lightbox_source' => 'title',
            'wp_orderby' => 'title',
            'wp_order' => 'ASC',
            'slider_vertical' => 0,
            'center_mode' => 0,
            'slider_arrows' => 1,
            'duration' => 4000,
            'enable_navigation' => 1,
            'enable_pagination' => 0,
            'pagination_type' => 'loadmore',
            'items_per_page' => 9,
            'auto_animation' => 1,
            'enable_overlay' => 1,
            'overlay_opacity' => 0.4,
            'overlay_color' => '#000000',
            'enable_image_effect' => 0,
            'image_effect_type' => 'bw',
            'enable_textpanel' => 1,
            'textpanel_always_on' => 0,
            'textpanel_source' => 'title_and_desc',
            'textpanel_appear_type' => 'slide',
            'textpanel_position' => 'inside_bottom',
            'textpanel_align' => 'center',
            'textpanel_enable_bg' => 1,
            'textpanel_bg_color' => 'transparent',
            'textpanel_bg_opacity' => '0.4',
            'textpanel_title_color' => '#ffffff',
            'textpanel_desc_color' => '#ffffff',
            'textpanel_desc_length' => 90,
            'textpanel_title_length' => 60,
            'enable_icons' => 1,
            'enable_border' => 0,
            'border_width' => 3,
            'border_color' => '#cabdbf',
            'border_radius' => 0,
            'scale_image' => 1,
            'justified_row_height' => 170,
            'tile_width' => 300,
            'tile_height' => 180,
            'grid_num_rows' => 1,
            'theme_panel_position' => 'bottom',
            'number_thumbnails' => 6,
            'compact_height' => 500,
            'enable_price' => 0,
            'include_children' => 0,
            'skin_on_hover' => 1,
            'skin_type' => 'bg-transition',
            'post_grid_thumb_width' => 100,
            'title_font_size' => 14,
            'desc_font_size' => 14,
            'show_author' => 0,
            'show_date' => 1
        );

        if ($theme === 'default' || $theme === 'portfolio' || $theme === 'post_grid') {
            $options['textpanel_title_color'] = '#111111';
            $options['textpanel_desc_color'] = '#868990';
        }

        if ($theme === 'post_grid') {
            $options['textpanel_align'] = 'left';
            $options['gutterwidth'] = 20;
            $options['item_ratio'] = '3-2';
            $options['desc_font_size'] = 13;
        }
        return $options;
    }

    public static function getFloatOptions()
    {
        $fields = array(
            'border_width',
            'border_radius'
        );

        return $fields;
    }

    public static function getIntegerOptions()
    {
        $fields = array(
            'gallery_width',
            'columns',
            'max_items',
            'gutterwidth',
            'items_per_page',
            'textpanel_desc_length',
            'duration',
            'grid_num_rows',
            'justified_row_height',
            'number_thumbnails',
            'compact_height',
            'post_grid_thumb_width',
            'title_font_size',
            'desc_font_size'
        );

        return $fields;
    }

    public static function getRadioOptions()
    {
        $radio_fields = array(
            'gallery_navigation' => 0,
            'gallery_full_width' => 0,
            'auto_animation' => 0,
            'scale_image' => 0,
            'enable_icons' => 0,
            'enable_border' => 0,
            'enable_overlay' => 0,
            'enable_image_effect' => 0,
            'enable_textpanel' => 0,
            'textpanel_always_on' => 0,
            'textpanel_enable_bg' => 0,
            'enable_navigation' => 0,
            'enable_pagination' => 0,
            'enable_price' => 0,
            'include_children' => 0,
            'slider_vertical' => 0,
            'center_mode' => 0,
            'slider_arrows' => 0,
            'show_author' => 0,
            'show_date' => 0,
            'skin_on_hover' => 0
        );

        return $radio_fields;
    }

    public static function sanitizeOptions($params_request)
    {
        $options = array();
        $radio_options = self::getRadioOptions();
        $integer_options = self::getIntegerOptions();
        $float_options = self::getFloatOptions();
        foreach ($params_request as $param_key => $param_value) {
            if (in_array($param_key, $integer_options) || isset($radio_options[$param_key])) {
                $options[$param_key] = (int) $param_value;
            } elseif (in_array($param_key, $float_options)) {
                $options[$param_key] = (float) $param_value;
            } else {
                $options[$param_key] = sanitize_text_field($param_value);
            }
        }

        return $options;
    }

    public static function getAllPostTypes() {
        $builtin_post_types = array(
            'post' => 'post',
            'page' => 'page'
        );

        $custom_post_types = get_post_types(
            array('_builtin' => false)
        );

        unset($custom_post_types['the_grid']);
        if ( class_exists( 'WooCommerce' ) ) {
            unset($custom_post_types['shop_order']);
        }

        $post_types = array_merge($builtin_post_types, $custom_post_types);
        foreach($post_types as $key => $type){
            $post_type_object = get_post_type_object($type);
            if(empty($post_type_object)){
                $post_types[$key] = $type;
                continue;
            }
            $post_types[$key] = $post_type_object->labels->name;
        }
        return($post_types);
    }

    /**
     * Get all taxonomy terms
     */
    public static function getAllTerms() {

        // store all terms (from all taxonomies and post types)
        $terms_list = array();
        // store each taxonomy terms list
        $post_terms = array();
        // get all post types
        $post_types = self::getAllPostTypes();

        foreach ($post_types as $post_type => $name) {

            // get taxonomies from post type
            $taxonomies = get_object_taxonomies($post_type, 'objects');

            // if there are some taxonomies
            if ($taxonomies) {

                $taxonomies_slug = array();

                // for each taxonomy slug
                foreach ($taxonomies as $taxonomy => $settings) {

                    if ( ! $settings->publicly_queryable && ! $settings->public && ! $settings->show_ui && ! $settings->show_tagcloud ) {
                        unset( $taxonomies[ $taxonomy ] );
                        continue;
                    }

                    // if this taxonomy was already proceeded
                    if (isset($post_terms[$taxonomy])) {
                        // store terms array from previous get_terms result
                        $terms_list[$post_type]['taxonomies'][$taxonomy] = $post_terms[$taxonomy];

                    } else {
                        // start building post type taxonomy data
                        $taxonomies_slug[] = $taxonomy;
                        $terms_list[$post_type]['taxonomies'][$taxonomy] = array(
                            'name'  => $taxonomy,
                            'title' => isset($settings->label) ? $settings->label : $taxonomy
                        );

                    }

                }
            }
        }

        return $terms_list;
        // prepare array to json (with escape)
        return htmlspecialchars(json_encode($terms_list), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Get all taxonomy terms
     */
    public static function sort_terms_hierarchically($array, $id = 0, $level = 0) {
        $orderedArray = array();
        foreach($array as $k=>$arr) {
            if($arr['parent'] == $id) {

                $arr['title'] = str_repeat('&#8212; ', $level) . ' ' . $arr['title'];
                $arr['depth'] = $level;
                $orderedArray[] = $arr;
                $children = self::sort_terms_hierarchically($array, $arr['id'], $level + 1);
                foreach($children as $child) {
                    $orderedArray[] = $child;
                }
            }

        }
        return $orderedArray;
    }

    public static function renderFields($type, $params = array(), $pro = false)
    {
        switch ($type) {
            case 'radio':
                ?>
                <div class="wplg-panel">
                    <div class="wplg-panel__header wplg-panel__header-status">
                        <div class="wplg-panel__status-info">
                            <div class="wplg-panel__main-title">
                                <?php if ($pro) : ?>
                                    <span class="wplg_pro_version"><?php esc_html_e(' (Pro)', 'wp-load-gallery') ?></span>
                                <?php endif; ?>
                                <span><?php echo esc_html($params['label']) ?></span>
                            </div>
                        </div>
                        <div class="wplg-panel__header-options">
                            <div class="wplg-switch-button">
                                <label class="switch">
                                    <input type="checkbox" name="<?php echo esc_attr($params['name']) ?>"
                                           class="<?php echo esc_attr($params['class']) ?>" value="1">
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                break;
            case 'number':
                ?>
                <div class="wplg-panel">
                    <div class="wplg-panel__header wplg-panel__header-status">
                        <div class="wplg-panel__status-info">
                            <div class="wplg-panel__main-title">
                                <?php if ($pro) : ?>
                                    <span class="wplg_pro_version"><?php esc_html_e(' (Pro)', 'wp-load-gallery') ?></span>
                                <?php endif; ?>
                                <span><?php echo esc_html($params['label']) ?></span>
                            </div>
                        </div>
                        <div class="wplg-panel__header-options">
                            <input type="number" min="<?php echo (isset($params['min']) ? esc_attr($params['min']) : 0) ?>"
                                   max="<?php echo (isset($params['max']) ? esc_attr($params['max']) : 10000000) ?>"
                                   step="<?php echo (isset($params['step']) ? esc_attr($params['step']) : 1) ?>" name="<?php echo esc_attr($params['name']) ?>"
                                   class="<?php echo esc_attr($params['class']) ?> wplg-input">
                        </div>
                    </div>
                </div>
                <?php
                break;
            case 'select':
                ?>
                <div class="wplg-panel">
                    <div class="wplg-panel__header wplg-panel__header-status">
                        <div class="wplg-panel__status-info">
                            <div class="wplg-panel__main-title">
                                <?php if ($pro) : ?>
                                    <span class="wplg_pro_version"><?php esc_html_e(' (Pro)', 'wp-load-gallery') ?></span>
                                <?php endif; ?>
                                <span><?php echo esc_html($params['label']) ?></span>
                            </div>
                        </div>
                        <div class="wplg-panel__header-options">
                            <select name="<?php echo esc_attr($params['name']) ?>" class="<?php echo esc_attr($params['class']) ?> wplg-select">
                                <?php foreach ($params['lists'] as $list) : ?>
                                <option value="<?php echo esc_attr($list['value']) ?>">
                                    <?php echo esc_attr($list['label']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <?php
                break;
        }
    }

    /**
     * Check gallery item exist
     *
     * @param string  $source_id  Item ID
     * @param integer $gallery_id Gallery ID
     * @param string  $type       Item type
     *
     * @return boolean
     */
    public static function checkGalleryItemExist($source_id, $gallery_id, $type)
    {
        global $wpdb;
        $result = $wpdb->get_row($wpdb->prepare('SELECT * FROM ' . $wpdb->prefix . 'wplg_gallery_items WHERE source_id = %s AND gallery_id = %d AND type = %s', array($source_id, (int)$gallery_id, $type)));
        if (!empty($result)) {
            return true;
        }

        return false;
    }

    /**
     * Check gallery item exist
     *
     * @param string $id  Item ID
     *
     * @return boolean
     */
    public static function deleteGalleryItem($id)
    {
        global $wpdb;
        $result = $wpdb->delete($wpdb->prefix . 'wplg_gallery_items', array('ID' => (int)$id), array('%d'));
        if (!empty($result)) {
            return true;
        }

        return false;
    }

    /**
     * Update item
     *
     * @param string $source_id Item source ID
     * @param array  $params    Params list
     *
     * @return void
     */
    public static function updateItemBySourceId($source_id, $params)
    {
        global $wpdb;
        $wpdb->update(
            $wpdb->prefix . 'wplg_gallery_items',
            array(
                'post_title' => $params['title'],
                'post_excerpt' => $params['desc'],
                'custom_link' => $params['link'],
                'status' => (int)$params['status']
            ),
            array( 'source_id' => $source_id ),
            array(
                '%s',
                '%s',
                '%s',
                '%d'
            ),
            array('%s')
        );
    }

    /**
     * Delete gallery items by gallery ID
     *
     * @param $gallery_id
     *
     * @return boolean
     */
    public static function deleteGalleryItemsByGalleryID($gallery_id)
    {
        global $wpdb;
        $result = $wpdb->delete($wpdb->prefix . 'wplg_gallery_items', array('gallery_id' => (int)$gallery_id), array('%d'));
        if (!empty($result)) {
            return true;
        }

        return false;
    }

    /**
     * Get gallery items by gallery id
     *
     * @param integer $gallery_id Gallery ID
     *
     * @return array|object|null
     */
    public static function getGalleryItems($gallery_id, $orderby, $order)
    {
        global $wpdb;
        switch ($orderby) {
            case 'title':
                $results = $wpdb->get_results($wpdb->prepare('SELECT * FROM ' . $wpdb->prefix . 'wplg_gallery_items WHERE gallery_id = %d AND type != "category" AND status = 1 ORDER BY post_title ' . $order, array((int) $gallery_id)));
                break;
            case 'date':
                $results = $wpdb->get_results($wpdb->prepare('SELECT * FROM ' . $wpdb->prefix . 'wplg_gallery_items WHERE gallery_id = %d AND type != "category" AND status = 1 ORDER BY post_date ' . $order, array((int) $gallery_id)));
                break;
            default:
                $results = $wpdb->get_results($wpdb->prepare('SELECT * FROM ' . $wpdb->prefix . 'wplg_gallery_items WHERE gallery_id = %d AND type != "category" AND status = 1', array((int) $gallery_id)));
        }
        return $results;
    }

    /**
     * Get gallery category items by gallery id
     *
     * @param integer $gallery_id Gallery ID
     *
     * @return array|object|null
     */
    public static function getGalleryCateogryItems($gallery_id)
    {
        global $wpdb;
        $results = $wpdb->get_results($wpdb->prepare('SELECT * FROM ' . $wpdb->prefix . 'wplg_gallery_items WHERE gallery_id = %d AND type = "category" AND status = 1', array((int) $gallery_id)));
        return $results;
    }

    /**
     * Get gallery item by id
     *
     * @param integer $id Item ID
     *
     * @return array|object|void|null
     */
    public static function getGalleryItemsDetails($id)
    {
        global $wpdb;
        $results = $wpdb->get_row($wpdb->prepare('SELECT * FROM ' . $wpdb->prefix . 'wplg_gallery_items WHERE id = %d', array((int) $id)));
        return $results;
    }

    /**
     * Get gallery items by gallery id
     *
     * @param integer $gallery_id Gallery ID
     *
     * @return integer
     */
    public static function getGalleryGoogleItems($gallery_id)
    {
        global $wpdb;
        $results = $wpdb->get_var($wpdb->prepare('SELECT COUNT(*) FROM ' . $wpdb->prefix . 'wplg_gallery_items WHERE gallery_id = %d AND status = 1 AND (type = "google_drive_video" OR type = "google_drive_image")', array((int) $gallery_id)));
        return $results;
    }

    /**
     * Insert gallery item
     *
     * @param string  $source_id    Item ID
     * @param integer $gallery_id   Gallery ID
     * @param string  $type         Item type
     * @param string  $post_title   Item title
     * @param string  $post_excerpt Item description
     * @param string  $thumbnail    Item thumbnail
     * @param string  $custom_link  Item custom link
     * @param string  $tags         Item tags
     *
     * @return false|integer
     */
    public static function insertGalleryItem($source_id, $gallery_id, $type, $post_title = '', $post_excerpt = '', $thumbnail = '', $custom_link = '', $tags = '')
    {
        global $wpdb;
        $table = $wpdb->prefix.'wplg_gallery_items';
        $data = array(
            'source_id' => $source_id,
            'gallery_id' => (int) $gallery_id,
            'type' => $type,
            'post_title' => $post_title,
            'post_excerpt' => $post_excerpt,
            'post_date' => current_time('mysql', 1),
            'thumbnail' => $thumbnail,
            'custom_link' => $custom_link,
            'tags' => $tags
        );

        return $wpdb->insert($table, $data);
    }

    public static function hex2rgb($color)
    {
        if ($color[0] == '#') {
            $color = substr($color, 1);
        }
        if (strlen($color) == 6) {
            list($r, $g, $b) = array($color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5]);
        } elseif (strlen($color) == 3) {
            list($r, $g, $b) = array($color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2]);
        } else {
            return array('red' => 0, 'green' => 0, 'blue' => 0);
        }
        $r = hexdec($r);
        $g = hexdec($g);
        $b = hexdec($b);
        return array('red' => $r, 'green' => $g, 'blue' => $b);
    }
}


/*
Copyright (c) 2008 Sebastián Grignoli
All rights reserved.

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions
are met:
1. Redistributions of source code must retain the above copyright
   notice, this list of conditions and the following disclaimer.
2. Redistributions in binary form must reproduce the above copyright
   notice, this list of conditions and the following disclaimer in the
   documentation and/or other materials provided with the distribution.
3. Neither the name of copyright holders nor the names of its
   contributors may be used to endorse or promote products derived
   from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
``AS IS'' AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED
TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
PURPOSE ARE DISCLAIMED.  IN NO EVENT SHALL COPYRIGHT HOLDERS OR CONTRIBUTORS
BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
POSSIBILITY OF SUCH DAMAGE.
*/

/**
 * @author   "Sebastián Grignoli" <grignoli@gmail.com>
 * @package  Encoding
 * @version  2.0
 * @link     https://github.com/neitanod/forceutf8
 * @example  https://github.com/neitanod/forceutf8
 * @license  Revised BSD
 */
//namespace ForceUTF8;

class Encoding
{

    const ICONV_TRANSLIT = "TRANSLIT";
    const ICONV_IGNORE = "IGNORE";
    const WITHOUT_ICONV = "";

    protected static $win1252ToUtf8 = array(
        128 => "\xe2\x82\xac",

        130 => "\xe2\x80\x9a",
        131 => "\xc6\x92",
        132 => "\xe2\x80\x9e",
        133 => "\xe2\x80\xa6",
        134 => "\xe2\x80\xa0",
        135 => "\xe2\x80\xa1",
        136 => "\xcb\x86",
        137 => "\xe2\x80\xb0",
        138 => "\xc5\xa0",
        139 => "\xe2\x80\xb9",
        140 => "\xc5\x92",

        142 => "\xc5\xbd",


        145 => "\xe2\x80\x98",
        146 => "\xe2\x80\x99",
        147 => "\xe2\x80\x9c",
        148 => "\xe2\x80\x9d",
        149 => "\xe2\x80\xa2",
        150 => "\xe2\x80\x93",
        151 => "\xe2\x80\x94",
        152 => "\xcb\x9c",
        153 => "\xe2\x84\xa2",
        154 => "\xc5\xa1",
        155 => "\xe2\x80\xba",
        156 => "\xc5\x93",

        158 => "\xc5\xbe",
        159 => "\xc5\xb8"
    );

    protected static $brokenUtf8ToUtf8 = array(
        "\xc2\x80" => "\xe2\x82\xac",

        "\xc2\x82" => "\xe2\x80\x9a",
        "\xc2\x83" => "\xc6\x92",
        "\xc2\x84" => "\xe2\x80\x9e",
        "\xc2\x85" => "\xe2\x80\xa6",
        "\xc2\x86" => "\xe2\x80\xa0",
        "\xc2\x87" => "\xe2\x80\xa1",
        "\xc2\x88" => "\xcb\x86",
        "\xc2\x89" => "\xe2\x80\xb0",
        "\xc2\x8a" => "\xc5\xa0",
        "\xc2\x8b" => "\xe2\x80\xb9",
        "\xc2\x8c" => "\xc5\x92",

        "\xc2\x8e" => "\xc5\xbd",


        "\xc2\x91" => "\xe2\x80\x98",
        "\xc2\x92" => "\xe2\x80\x99",
        "\xc2\x93" => "\xe2\x80\x9c",
        "\xc2\x94" => "\xe2\x80\x9d",
        "\xc2\x95" => "\xe2\x80\xa2",
        "\xc2\x96" => "\xe2\x80\x93",
        "\xc2\x97" => "\xe2\x80\x94",
        "\xc2\x98" => "\xcb\x9c",
        "\xc2\x99" => "\xe2\x84\xa2",
        "\xc2\x9a" => "\xc5\xa1",
        "\xc2\x9b" => "\xe2\x80\xba",
        "\xc2\x9c" => "\xc5\x93",

        "\xc2\x9e" => "\xc5\xbe",
        "\xc2\x9f" => "\xc5\xb8"
    );

    protected static $utf8ToWin1252 = array(
        "\xe2\x82\xac" => "\x80",

        "\xe2\x80\x9a" => "\x82",
        "\xc6\x92" => "\x83",
        "\xe2\x80\x9e" => "\x84",
        "\xe2\x80\xa6" => "\x85",
        "\xe2\x80\xa0" => "\x86",
        "\xe2\x80\xa1" => "\x87",
        "\xcb\x86" => "\x88",
        "\xe2\x80\xb0" => "\x89",
        "\xc5\xa0" => "\x8a",
        "\xe2\x80\xb9" => "\x8b",
        "\xc5\x92" => "\x8c",

        "\xc5\xbd" => "\x8e",


        "\xe2\x80\x98" => "\x91",
        "\xe2\x80\x99" => "\x92",
        "\xe2\x80\x9c" => "\x93",
        "\xe2\x80\x9d" => "\x94",
        "\xe2\x80\xa2" => "\x95",
        "\xe2\x80\x93" => "\x96",
        "\xe2\x80\x94" => "\x97",
        "\xcb\x9c" => "\x98",
        "\xe2\x84\xa2" => "\x99",
        "\xc5\xa1" => "\x9a",
        "\xe2\x80\xba" => "\x9b",
        "\xc5\x93" => "\x9c",

        "\xc5\xbe" => "\x9e",
        "\xc5\xb8" => "\x9f"
    );

    static function toUTF8($text)
    {
        /**
         * Function \ForceUTF8\Encoding::toUTF8
         *
         * This function leaves UTF8 characters alone, while converting almost all non-UTF8 to UTF8.
         *
         * It assumes that the encoding of the original string is either Windows-1252 or ISO 8859-1.
         *
         * It may fail to convert characters to UTF-8 if they fall into one of these scenarios:
         *
         * 1) when any of these characters:   ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖ×ØÙÚÛÜÝÞß
         *    are followed by any of these:  ("group B")
         *                                    ¡¢£¤¥¦§¨©ª«¬­®¯°±²³´µ¶•¸¹º»¼½¾¿
         * For example:   %ABREPRESENT%C9%BB. «REPRESENTÉ»
         * The "«" (%AB) character will be converted, but the "É" followed by "»" (%C9%BB)
         * is also a valid unicode character, and will be left unchanged.
         *
         * 2) when any of these: àáâãäåæçèéêëìíîï  are followed by TWO chars from group B,
         * 3) when any of these: ðñòó  are followed by THREE chars from group B.
         *
         * @name toUTF8
         * @param string $text Any string.
         * @return string  The same string, UTF8 encoded
         *
         */

        if(is_array($text))
        {
            foreach($text as $k => $v)
            {
                $text[$k] = self::toUTF8($v);
            }
            return $text;
        }

        if(!is_string($text)) {
            return $text;
        }

        $max = self::strlen($text);

        $buf = "";
        for($i = 0; $i < $max; $i++){
            $c1 = $text[$i];
            if($c1>="\xc0"){ //Should be converted to UTF8, if it's not UTF8 already
                $c2 = $i+1 >= $max? "\x00" : $text[$i+1];
                $c3 = $i+2 >= $max? "\x00" : $text[$i+2];
                $c4 = $i+3 >= $max? "\x00" : $text[$i+3];
                if($c1 >= "\xc0" & $c1 <= "\xdf"){ //looks like 2 bytes UTF8
                    if($c2 >= "\x80" && $c2 <= "\xbf"){ //yeah, almost sure it's UTF8 already
                        $buf .= $c1 . $c2;
                        $i++;
                    } else { //not valid UTF8.  Convert it.
                        $cc1 = (chr(ord($c1) / 64) | "\xc0");
                        $cc2 = ($c1 & "\x3f") | "\x80";
                        $buf .= $cc1 . $cc2;
                    }
                } elseif($c1 >= "\xe0" & $c1 <= "\xef"){ //looks like 3 bytes UTF8
                    if($c2 >= "\x80" && $c2 <= "\xbf" && $c3 >= "\x80" && $c3 <= "\xbf"){ //yeah, almost sure it's UTF8 already
                        $buf .= $c1 . $c2 . $c3;
                        $i = $i + 2;
                    } else { //not valid UTF8.  Convert it.
                        $cc1 = (chr(ord($c1) / 64) | "\xc0");
                        $cc2 = ($c1 & "\x3f") | "\x80";
                        $buf .= $cc1 . $cc2;
                    }
                } elseif($c1 >= "\xf0" & $c1 <= "\xf7"){ //looks like 4 bytes UTF8
                    if($c2 >= "\x80" && $c2 <= "\xbf" && $c3 >= "\x80" && $c3 <= "\xbf" && $c4 >= "\x80" && $c4 <= "\xbf"){ //yeah, almost sure it's UTF8 already
                        $buf .= $c1 . $c2 . $c3 . $c4;
                        $i = $i + 3;
                    } else { //not valid UTF8.  Convert it.
                        $cc1 = (chr(ord($c1) / 64) | "\xc0");
                        $cc2 = ($c1 & "\x3f") | "\x80";
                        $buf .= $cc1 . $cc2;
                    }
                } else { //doesn't look like UTF8, but should be converted
                    $cc1 = (chr(ord($c1) / 64) | "\xc0");
                    $cc2 = (($c1 & "\x3f") | "\x80");
                    $buf .= $cc1 . $cc2;
                }
            } elseif(($c1 & "\xc0") == "\x80"){ // needs conversion
                if(isset(self::$win1252ToUtf8[ord($c1)])) { //found in Windows-1252 special cases
                    $buf .= self::$win1252ToUtf8[ord($c1)];
                } else {
                    $cc1 = (chr(ord($c1) / 64) | "\xc0");
                    $cc2 = (($c1 & "\x3f") | "\x80");
                    $buf .= $cc1 . $cc2;
                }
            } else { // it doesn't need conversion
                $buf .= $c1;
            }
        }
        return $buf;
    }

    static function toWin1252($text, $option = self::WITHOUT_ICONV)
    {
        if (is_array($text)) {
            foreach ($text as $k => $v) {
                $text[$k] = self::toWin1252($v, $option);
            }
            return $text;
        } elseif (is_string($text)) {
            return static::utf8_decode($text, $option);
        } else {
            return $text;
        }
    }

    static function toISO8859($text)
    {
        return self::toWin1252($text);
    }

    static function toLatin1($text)
    {
        return self::toWin1252($text);
    }

    static function fixUTF8($text, $option = self::WITHOUT_ICONV)
    {
        if (is_array($text)) {
            foreach ($text as $k => $v) {
                $text[$k] = self::fixUTF8($v, $option);
            }
            return $text;
        }

        $last = "";
        while ($last <> $text) {
            $last = $text;
            $text = self::toUTF8(static::utf8_decode($text, $option));
        }
        $text = self::toUTF8(static::utf8_decode($text, $option));
        return $text;
    }

    static function UTF8FixWin1252Chars($text)
    {
        // If you received an UTF-8 string that was converted from Windows-1252 as it was ISO8859-1
        // (ignoring Windows-1252 chars from 80 to 9F) use this function to fix it.
        // See: http://en.wikipedia.org/wiki/Windows-1252

        return str_replace(array_keys(self::$brokenUtf8ToUtf8), array_values(self::$brokenUtf8ToUtf8), $text);
    }

    static function removeBOM($str = "")
    {
        if (substr($str, 0, 3) == pack("CCC", 0xef, 0xbb, 0xbf)) {
            $str = substr($str, 3);
        }
        return $str;
    }

    protected static function strlen($text)
    {
        return (function_exists('mb_strlen')) ?
            mb_strlen($text, '8bit') : strlen($text);
    }

    public static function normalizeEncoding($encodingLabel)
    {
        $encoding = strtoupper($encodingLabel);
        $encoding = preg_replace('/[^a-zA-Z0-9\s]/', '', $encoding);
        $equivalences = array(
            'ISO88591' => 'ISO-8859-1',
            'ISO8859' => 'ISO-8859-1',
            'ISO' => 'ISO-8859-1',
            'LATIN1' => 'ISO-8859-1',
            'LATIN' => 'ISO-8859-1',
            'UTF8' => 'UTF-8',
            'UTF' => 'UTF-8',
            'WIN1252' => 'ISO-8859-1',
            'WINDOWS1252' => 'ISO-8859-1'
        );

        if (empty($equivalences[$encoding])) {
            return 'UTF-8';
        }

        return $equivalences[$encoding];
    }

    public static function encode($encodingLabel, $text)
    {
        $encodingLabel = self::normalizeEncoding($encodingLabel);
        if ($encodingLabel == 'ISO-8859-1') return self::toLatin1($text);
        return self::toUTF8($text);
    }

    protected static function utf8_decode($text, $option)
    {
        if ($option == self::WITHOUT_ICONV || !function_exists('iconv')) {
            $o = utf8_decode(
                str_replace(array_keys(self::$utf8ToWin1252), array_values(self::$utf8ToWin1252), self::toUTF8($text))
            );
        } else {
            $o = iconv("UTF-8", "Windows-1252" . ($option == self::ICONV_TRANSLIT ? '//TRANSLIT' : ($option == self::ICONV_IGNORE ? '//IGNORE' : '')), $text);
        }
        return $o;
    }
}

