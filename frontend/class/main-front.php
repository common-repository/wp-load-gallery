<?php
/* Prohibit direct script loading */
defined('ABSPATH') || die('No direct script access allowed!');

use WPLG\Helper;
require_once ABSPATH . '/wp-admin/includes/image.php';
/**
 * Class WpLoadGalleryFront
 * This class that holds most of the front-end functionality for WP Media Folder Gallery
 */
class WpLoadGalleryFront
{
    /**
     * WpLoadGalleryFront constructor.
     */
    public function __construct()
    {
        if (!is_admin()) {
            add_shortcode('wplg_gallery', array($this, 'galleryShortcode'));
        }
        add_action('wp_ajax_wplg_navigation_gallery', array($this, 'navigationGallery'));
        add_action('wp_ajax_nopriv_wplg_navigation_gallery', array($this, 'navigationGallery'));
        add_action('wp_ajax_wplg_loadmore_gallery', array($this, 'loadmoreGallery'));
        add_action('wp_ajax_nopriv_wplg_loadmore_gallery', array($this, 'loadmoreGallery'));
    }

    /**
     * Run shortcode gallery
     *
     * @param array $attr Params of gallery
     *
     * @return string
     */
    public function galleryShortcode($attr)
    {
        /* Get all params */
        if (empty($attr['gallery_id'])) {
            return '<p style="color: #f00">' . esc_html__('Gallery not found', 'wp-load-gallery') . '</p>';
        }

        $term_exist = get_term((int) $attr['gallery_id'], WPLG_TAXONOMY);
        if (empty($term_exist)) {
            return '<h3>' . __('Gallery not found!', 'wp-load-gallery') . '</h3>';
        }

        static $instance = 0;
        $instance++;
        $theme = get_term_meta((int) $attr['gallery_id'], 'wplg_theme', true);
        $options = get_term_meta((int) $attr['gallery_id'], 'wplg_options', true);
        $params = $this->getSettingsFront($attr['gallery_id'], $attr, $options);
        foreach ($params as $attr_key => $attr_value) {
            ${$attr_key} = $attr_value;
        }

        $theme = Helper::getTheme($theme);
        // ============================
        $is_max_width = (!empty($params['gallery_full_width']) || !isset($params['gallery_full_width'])) ? true : false;
        $gallery_width = (isset($params['gallery_width'])) ? $params['gallery_width'] : 1200;
        $textpanel_desc_length = (isset($params['textpanel_desc_length'])) ? (int) $params['textpanel_desc_length'] : 120;
        $isPagination = ($this->isPagination($enable_pagination, $theme)) ? true : false;
        $galleryItems = $this->getGalleryItems($gallery_id, $params, $isPagination, 0);
        $attachments = $galleryItems['items'];
        $total_items = $galleryItems['total_items'];
        if (empty($attachments)) {
            return '<h3>' . __('Gallery empty items!', 'wp-load-gallery') . '</h3>';
        }
        /* Create output html */
        if (!is_admin()) {
            $this->enqueue($theme, $gallery_navigation);
        }

        $output = '';
        ob_start();
        $output .= $this->renderNavigation($gallery_id, $is_max_width, $instance, $params);
        $transition_class = '';
        if ((int)$skin_on_hover === 1) {
            if ($skin_type === 'border-transition') {
                $transition_class .= ' wplg-border-gallery-box';
            } else {
                $transition_class .= ' wplg-bg-animate';
            }
        }
        $output .= '<div data-total_items="'. esc_attr($total_items) .'" class="wplg-wrap '. $transition_class .' '. (($is_max_width) ? 'is_max_width' : '') .'" style="clear: both; margin: 10px 0; '. ((!$is_max_width) ? 'width: ' . (int) $gallery_width . 'px' : '') .'; background: '. $background_color .'">';
        if (isset($theme)) {
            if (file_exists(WPLG_PLUGIN_DIR . 'frontend/themes/' . $theme . '/' . $theme . '.php')) {
                require(WPLG_PLUGIN_DIR . 'frontend/themes/' . $theme . '/' . $theme . '.php');
            } else {
                require(WPLG_PLUGIN_DIR . 'frontend/themes/default/default.php');
            }
        } else {
            require(WPLG_PLUGIN_DIR . 'frontend/themes/default/default.php');
        }
        $output .= ob_get_contents();
        ob_end_clean();
        $output .= $this->renderPagination($gallery_id, $is_max_width, $params, $total_items);
        $output .= '</div>';
        return $output;
    }

    public function loadmoreGallery()
    {
        $params_request = json_decode(stripslashes($_REQUEST['options']), true);
        // sanitize options
        $options = Helper::sanitizeOptions($params_request);
        $params = $this->getSettingsFront($options['gallery_id'], array(), $options);
        if (empty($params['theme'])) {
            $theme = get_term_meta((int) $options['gallery_id'], 'wplg_theme', true);
        } else {
            $theme = $params['theme'];
        }

        foreach ($params as $attr_key => $attr_value) {
            ${$attr_key} = $attr_value;
        }
        $theme = Helper::getTheme($theme);
        $paged = (isset($_REQUEST['paged'])) ? (int) $_REQUEST['paged'] : 1;
        $offset = ($paged - 1) * (int) $items_per_page;
        $isPagination = ($this->isPagination($enable_pagination, $theme)) ? true : false;
        $galleryItems = $this->getGalleryItems($gallery_id, $params, $isPagination, $offset);
        $attachments = $galleryItems['items'];
        if (empty($attachments)) {
            wp_send_json(array('status' => false, 'paged' => $paged));
        }

        $load_more = 1;
        ob_start();
        if (isset($theme)) {
            if (file_exists(WPLG_PLUGIN_DIR . 'frontend/themes/' . $theme . '/' . $theme . '.php')) {
                require(WPLG_PLUGIN_DIR . 'frontend/themes/' . $theme . '/' . $theme . '.php');
            } else {
                require(WPLG_PLUGIN_DIR . 'frontend/themes/default/default.php');
            }
        } else {
            require(WPLG_PLUGIN_DIR . 'frontend/themes/default/default.php');
        }
        $output = ob_get_contents();
        ob_end_clean();
        wp_send_json(array('status' => true, 'items' => $output, 'paged' => $paged));
    }

    /**
     * Ajax load gallery by ID
     *
     * @return void
     */
    public function navigationGallery()
    {
        if (empty($_POST['gallery_id'])) {
            wp_send_json(array('status' => false));
        }
        $gallery_id = (int) $_POST['gallery_id'];
        $root_gallery_id = (isset($_POST['root_gallery_id'])) ? $_POST['root_gallery_id'] : 0;
        $instance = (isset($_POST['instance'])) ? $_POST['instance'] : time();
        $gallery_infos = get_term($gallery_id, WPLG_TAXONOMY);
        $theme = get_term_meta((int) $gallery_id, 'wplg_theme', true);
        $options = get_term_meta((int) $gallery_id, 'wplg_options', true);
        if ((int) $gallery_id === (int) $root_gallery_id) {
            $params_request = json_decode(stripslashes($_POST['root_params']), true);
            // sanitize options
            $params = Helper::sanitizeOptions($params_request);
        } else {
            $params = $this->getSettingsFront($gallery_id, array(), $options);
        }

        foreach ($params as $attr_key => $attr_value) {
            ${$attr_key} = $attr_value;
        }
        $navigation_type = (isset($_POST['navigation_type'])) ? $_POST['navigation_type'] : 'menu';
        $theme = Helper::getTheme($theme);
        // ============================
        $is_max_width = (!empty($params['gallery_full_width']) || !isset($params['gallery_full_width'])) ? true : false;
        $gallery_width = (isset($params['gallery_width'])) ? $params['gallery_width'] : 1200;
        $textpanel_desc_length = (isset($params['textpanel_desc_length'])) ? (int) $params['textpanel_desc_length'] : 120;
        $isPagination = ($this->isPagination($enable_pagination, $theme)) ? true : false;
        $galleryItems = $this->getGalleryItems($gallery_id, $params, $isPagination, 0);
        $attachments = $galleryItems['items'];
        $total_items = $galleryItems['total_items'];
        if ($navigation_type === 'folder') {
            $navigation = $this->renderGalleryNavigationFolder($gallery_id, $root_gallery_id);
        }

        if (empty($attachments)) {
            wp_send_json(array('status' => true, 'name' => $gallery_infos->name, 'navigation_html' => $navigation, 'items_html' => ''));
        }

        ob_start();
        $transition_class = '';
        if ((int)$skin_on_hover === 1) {
            if ($skin_type === 'border-transition') {
                $transition_class .= ' wplg-border-gallery-box';
            } else {
                $transition_class .= ' wplg-bg-animate';
            }
        }
        $output = '<div data-total_items="'. esc_attr($total_items) .'"  class="wplg-wrap '. $transition_class .' '. (($is_max_width) ? 'is_max_width' : '') .'" style="clear: both; margin: 10px 0; '. ((!$is_max_width) ? 'width: ' . (int)$gallery_width . 'px' : '') .'; background: '. $background_color .'">';
        if (isset($theme)) {
            if (file_exists(WPLG_PLUGIN_DIR . 'frontend/themes/' . $theme . '/' . $theme . '.php')) {
                require(WPLG_PLUGIN_DIR . 'frontend/themes/' . $theme . '/' . $theme . '.php');
            } else {
                require(WPLG_PLUGIN_DIR . 'frontend/themes/default/default.php');
            }
        } else {
            require(WPLG_PLUGIN_DIR . 'frontend/themes/default/default.php');
        }
        $output .= ob_get_contents();
        $output .= $this->renderPagination($gallery_id, $is_max_width, $params, $total_items);
        $output .= '</div>';
        ob_end_clean();
        wp_send_json(array('status' => true, 'name' => $gallery_infos->name, 'navigation_html' => $navigation, 'items_html' => $output));
    }

    /**
     * @param $enable_pagination
     * @param $theme
     * @return bool
     */
    public function isPagination($enable_pagination, $theme)
    {
        return ((int) $enable_pagination === 1 && ($theme === 'flex' || $theme === 'square_grid' || $theme === 'default' || $theme === 'post_grid' || $theme === 'flip_box' || $theme === 'masonry' || $theme === 'portfolio'));
    }

    /**
     * Render load more
     *
     * @param integer $gallery_id
     * @param boolean $is_max_width
     * @param array   $params
     * @param integer $total_items
     *
     * @return string
     */
    public function renderPagination($gallery_id, $is_max_width, $params, $total_items = 0)
    {
        $pagination = '';
        if ($params['pagination_type'] === 'lazyload') {
            return $pagination;
        }
        if (empty($params['theme'])) {
            $theme = get_term_meta((int) $gallery_id, 'wplg_theme', true);
        } else {
            $theme = $params['theme'];
        }

        if ($this->isPagination($params['enable_pagination'], $theme)) {
            $total_pages = ceil((int)$total_items / (int)$params['items_per_page']);
            if ($is_max_width) {
                $pagination .= '<div class="wplg-pagnination-wrap" style="margin: 10px auto; width: 100%" data-gallery_id="' . esc_attr($gallery_id) . '" data-params="' . esc_attr(json_encode($params)) . '" data-total_pages="' . esc_attr($total_pages) . '">';
            } else {
                $pagination .= '<div class="wplg-pagnination-wrap" style="margin: 10px auto; width: ' . (int)$params['gallery_width'] . 'px" data-gallery_id="' . esc_attr($gallery_id) . '" data-params="' . esc_attr(json_encode($params)) . '" data-total_pages="' . esc_attr($total_pages) . '">';
            }

            if (isset($params['pagination_type']) && $params['pagination_type'] === 'number_page') {
                if ($total_pages > 1) {
                    $start = 1;
                    $end = 4;
                    if ($total_pages > 7) {
                        for ($i = $start; $i <= $end; $i++) {
                            if ((int)$i === 1) {
                                $pagination .= '<span class="wplg-number-page active-page" data-paged="' . esc_attr($i) . '">' . esc_html($i) . '</span>';
                            } else {
                                $pagination .= '<span class="wplg-number-page" data-paged="' . esc_attr($i) . '">' . esc_html($i) . '</span>';
                            }
                        }
                        $pagination .= '<span class="material-icons wplg-next-page" data-paged="' . esc_attr(((int)$end + 1)) . '"> navigate_next </span>';
                        $pagination .= '<span class="material-icons wplg-first-page" data-paged="' . esc_attr($total_pages) . '"> last_page </span>';
                    } else {
                        for ($i = 1; $i <= $total_pages; $i++) {
                            if ((int)$i === 1) {
                                $pagination .= '<span class="wplg-number-page active-page" data-paged="' . esc_attr($i) . '">' . esc_html($i) . '</span>';
                            } else {
                                $pagination .= '<span class="wplg-number-page" data-paged="' . esc_attr($i) . '">' . esc_html($i) . '</span>';
                            }
                        }
                    }
                }
            } else {
                $pagination .= '<button class="wplg_gallery_loadmore" data-gallery_id="' . esc_attr($gallery_id) . '" data-paged="2"><label>' . esc_html__('Load more', 'wp-load-gallery') . '</label><span class="material-icons-outlined wplg-loadmore-loader">sync</span></button>';
            }
            $pagination .= '</div>';
        }

        return $pagination;
    }

    /**
     * Render navigation
     *
     * @param integer $gallery_id   ID of gallery
     * @param boolean $is_max_width Is max width
     * @param integer $instance     Instance
     * @param array   $params       Params
     *
     * @return string
     */
    public function renderNavigation($gallery_id, $is_max_width, $instance, $params)
    {
        $navigation = '';
        if ((int) $params['gallery_navigation'] === 1) {
            if ($is_max_width) {
                $navigation .= '<div class="wplg-menu-navigation-wrap '. esc_attr($params['navigation_type']) .'" data-navigation_type="'. esc_attr($params['navigation_type']) .'" style="margin: 10px auto; width: 100%" data-instance="'. esc_attr($instance) .'" data-gallery_id="' . esc_attr($gallery_id) . '" data-params="'. esc_attr(json_encode($params)) .'">';
            } else {
                $navigation .= '<div class="wplg-menu-navigation-wrap '. esc_attr($params['navigation_type']) .'" data-navigation_type="'. esc_attr($params['navigation_type']) .'" style="margin: 10px auto; width: '. (int) $params['gallery_width'] .'px" data-instance="'. esc_attr($instance) .'" data-gallery_id="' . esc_attr($gallery_id) . '" data-params="'. esc_attr(json_encode($params)) .'">';
            }
            $gallery = get_term((int) $gallery_id, WPLG_TAXONOMY);
            switch ($params['navigation_type']) {
                case 'menu':
                    $navigation .= '<ul class="wplg-menu-navigation wplg-menu-root">';
                    $navigation .= $this->renderGalleryNavigationMenu($gallery_id);
                    $navigation .= '</ul>';
                    $navigation .= '<span class="wplg_gallery_name">'. esc_html($gallery->name) .'</span>';
                    break;
                case 'folder':
                    $navigation .= '<ul class="wplg-menu-navigation-folder">';
                    $navigation .= $this->renderGalleryNavigationFolder($gallery_id, $gallery_id);
                    $navigation .= '</ul>';
                    $navigation .= '<span class="wplg_gallery_name">'. esc_html($gallery->name) .'</span>';
                    break;
                case 'tree':
                    $navigation .= '<div class="wplg-menu-navigation-tree">';
                    $navigation .= $this->renderGalleryNavigationTree($gallery_id, true);
                    $navigation .= '</div>';
                    break;
                default:
                    $navigation .= '<ul class="wplg-menu-navigation wplg-menu-root">';
                    $navigation .= $this->renderGalleryNavigationMenu($gallery_id);
                    $navigation .= '</ul>';
                    $navigation .= '<span class="wplg_gallery_name">'. esc_html($gallery->name) .'</span>';
            }
            $navigation .= '</div>';
        }

        return $navigation;
    }

    /**
     * Render gallery navigation tree
     *
     * @param integer $gallery_id Gallery ID
     * @param boolean $selected   Is selected
     *
     * @return string
     */
    public function renderGalleryNavigationTree($gallery_id, $selected = false)
    {
        $gallery = get_term($gallery_id, WPLG_TAXONOMY);
        $gallery_childs      = get_categories(array(
            'hide_empty' => false,
            'taxonomy' => WPLG_TAXONOMY,
            'parent' => (int) $gallery_id
        ));

        if (!empty($gallery_childs)) {
            $navigation = '<div class="wplg-navigation-tree-item '. (($selected) ? 'wplg-navigation-tree-item-selected' : '') .'" data-id="'. esc_attr($gallery_id) .'">';
        } else {
            $navigation = '<div class="wplg-navigation-tree-item wplg-item-nochild '. (($selected) ? 'selected' : '') .'" data-id="'. esc_attr($gallery_id) .'">';
        }
        $navigation .= '<div class="wplg-item-inside" data-id="'. esc_attr($gallery_id) .'">';
        $navigation .= '<a class="wplg-toggle-icon"><span class="material-icons"> chevron_right </span></a>';
        $navigation .= '<a class="wplg-text-item" data-id="'. esc_attr($gallery_id) .'"><i class="material-icons-outlined wplg-item-icon" style="color: #b2b2b2">folder</i><span class="wplg-item-title">'. esc_html($gallery->name) .'</span></a>';
        $navigation .= '</div></div>';
        $gallery_childs      = get_categories(array(
            'hide_empty' => false,
            'taxonomy' => WPLG_TAXONOMY,
            'parent' => (int) $gallery_id
        ));
        if (!empty($gallery_childs)) {
            $navigation .= '<div class="wplg-menu-navigation-tree-child">';
            foreach ($gallery_childs as $gallery_child) {
                $navigation .= $this->renderGalleryNavigationTree($gallery_child->term_id, false);
            }
            $navigation .= '</div>';
        }

        return $navigation;
    }

    /**
     * Render gallery navigation menu
     *
     * @param integer $gallery_id Gallery ID
     * @param boolean $root       Is root
     *
     * @return string
     */
    public function renderGalleryNavigationMenu($gallery_id, $root = true)
    {
        $gallery = get_term($gallery_id, WPLG_TAXONOMY);
        $navigation = '<li><span data-gallery_id="'. esc_attr($gallery->term_id) .'" class="wplg-navigation-title">'. esc_html($gallery->name) .'</span>';
        $gallery_childs      = get_categories(array(
            'hide_empty' => false,
            'taxonomy' => WPLG_TAXONOMY,
            'parent' => (int) $gallery_id
        ));
        if (!empty($gallery_childs)) {
            if ($root) {
                $navigation .= '<span class="material-icons">arrow_drop_down</span>';
            } else {
                $navigation .= '<span class="material-icons">arrow_right</span>';
            }

            $navigation .= '<ul class="wplg-menu-navigation wplg-menu-child">';
            foreach ($gallery_childs as $gallery_child) {
                $navigation .= $this->renderGalleryNavigationMenu($gallery_child->term_id, false);
            }
            $navigation .= '</ul>';
        }

        return $navigation;
    }

    /**
     * Render gallery navigation folder
     *
     * @param integer $gallery_id Gallery ID
     *
     * @return string
     */
    public function renderGalleryNavigationFolder($gallery_id, $root_gallery_id = 0)
    {
        $current = get_term((int)$gallery_id, WPLG_TAXONOMY);
        $gallery_childs = get_categories(array(
            'hide_empty' => false,
            'taxonomy' => WPLG_TAXONOMY,
            'parent' => (int)$gallery_id
        ));
        $navigation = '';
        if ((int) $root_gallery_id !== (int) $gallery_id) {
            $navigation .= '<li class="wplg_navigation_folder" data-gallery_id="' . esc_attr($current->parent) . '">';
            $navigation .= '<span class="wplg_navigation_folder_details">
                      <i class="material-icons wplg_navigation_icon">keyboard_arrow_left</i>
                    </span>';
            $navigation .= '<span class="wplg_navigation_title" title="' . esc_attr__('Back', 'wp-load-gallery') . '">
                      ' . esc_html__('Back', 'wp-load-gallery') . '
                    </span>
            </li>';
        }
        if (!empty($gallery_childs)) {
            foreach ($gallery_childs as $folder) {
                $navigation .= '<li class="wplg_navigation_folder" data-gallery_id="' . esc_attr($folder->term_id) . '">';
                $navigation .= '<span class="wplg_navigation_folder_details">
                      <i class="material-icons wplg_navigation_icon">folder</i>
                    </span>';
                $navigation .= '<span class="wplg_navigation_title" title="' . esc_attr($folder->name) . '">
                      ' . esc_html($folder->name) . '
                    </span>
                </li>';
            }
        }
        return $navigation;
    }

    /**
     * Get gallery items by gallery ID
     *
     * @param integer $gallery_id   Gallery ID
     * @param array   $params       Params
     * @param string  $isPagination Is pagination
     * @param string  $offset       Offset
     *
     * @return array|int[]|WP_Post[]
     */
    public function getGalleryItems($gallery_id, $params = array(), $isPagination = false, $offset = 0)
    {
        $attachments = array();
        $ftp_sources = get_term_meta($gallery_id, 'wplg_gallery_server_folder', true);
        // get images from server folder
        if (!empty($ftp_sources) && is_array($ftp_sources)) {
            foreach ($ftp_sources as $ftp_source) {
                if (file_exists(ABSPATH . $ftp_source)) {
                    $ftp_files1 = glob(ABSPATH . $ftp_source . '/*.jpg');
                    $ftp_files2 = glob(ABSPATH . $ftp_source . '/*.JPG');
                    $ftp_files3 = glob(ABSPATH . $ftp_source . '/*.png');
                    $ftp_files4 = glob(ABSPATH . $ftp_source . '/*.PNG');
                    $ftp_files5 = glob(ABSPATH . $ftp_source . '/*.webp');
                    $ftp_files6 = glob(ABSPATH . $ftp_source . '/*.WEBP');
                    $ftp_files = array_merge($ftp_files1, $ftp_files2, $ftp_files3, $ftp_files4, $ftp_files5, $ftp_files6);
                    foreach ($ftp_files as $ftp_file) {
                        if (is_file($ftp_file)) {
                            $types = wp_check_filetype($ftp_file);
                            if (strpos($types['type'], 'image/') !== false) {
                                $infos = pathinfo($ftp_file);
                                $xmp_list = wp_read_image_metadata($ftp_file);
                                $title = (isset($xmp_list['title']) && $xmp_list['title'] !== '') ? $xmp_list['title'] : $infos['filename'];
                                $desc = (isset($xmp_list['caption']) && $xmp_list['caption'] !== '') ? $xmp_list['caption'] : '';

                                $file = new stdClass();
                                $file->post_excerpt = $desc;
				$file->post_content = $desc;
                                $file->post_title = $title;
                                $file->post_date = filemtime($ftp_file);
                                $file->url = str_replace(ABSPATH, site_url('/'), $ftp_file);
                                $file->path = $ftp_file;
                                $file->type = 'server_file';
                                $attachments[] = $file;
                            }
                        }
                    }
                }
            }
        }

        $items = Helper::getGalleryItems($gallery_id, $params['wp_orderby'], $params['wp_order']);
        $google_connect = false;
        if (!empty(Helper::getGalleryGoogleItems($gallery_id))) {
            $configs = get_option('wplg_google_drive_configs');
            if (!empty($configs['connected']) && !empty($configs['googleCredentials'])) {
                try {
                    $googleDrive  = new mainGoogleDrive();
                    $client = $googleDrive->getClient($configs);
                    $google_connect = true;
                } catch (Exception $e) {
                    $google_connect = false;
                }
            }
        }

        foreach ($items as $item) {
            switch ($item->type) {
                case 'google_drive_video':
                case 'google_drive_image':
                    if (!empty($configs['connected']) && !empty($configs['googleCredentials'])) {
                        if ($google_connect) {
                            $file = new stdClass();
                            $file->ID = $item->source_id;
                            $file->post_excerpt = $item->post_excerpt;
                            $file->post_title = $item->post_title;
                            $file->post_date = ($item->post_date !== '') ? strtotime($item->post_date) : time();
                            $file->url = 'https://drive.google.com/thumbnail?id=' . $item->source_id;
                            $file->lightbox_url = 'https://drive.google.com/uc?id=' . $item->source_id;
                            $file->path = '';
                            $file->type = $item->type;
                            $attachments[] = $file;
                        }
                    }
                    break;
                case 'youtube':
                    $file = new stdClass();
                    $file->ID = $item->source_id;
                    $file->post_excerpt = $item->post_excerpt;
                    $file->post_title = $item->post_title;
                    $file->post_date = ($item->post_date !== '') ? strtotime($item->post_date) : time();
                    $file->url = $item->thumbnail;
                    $file->path = '';
                    $file->type = 'youtube';
                    $attachments[] = $file;
                    break;
                case 'vimeo':
                    $file = new stdClass();
                    $file->ID = $item->source_id;
                    $file->post_excerpt = $item->post_excerpt;
                    $file->post_title = $item->post_title;
                    $file->post_date = ($item->post_date !== '') ? strtotime($item->post_date) : time();
                    $file->url = $item->thumbnail;
                    $file->path = '';
                    $file->type = 'vimeo';
                    $attachments[] = $file;
                    break;
            }
        }

        // get item from post table
        $tax_query = array();
        $tax_query['relation'] = 'OR';
        $tax_query[] = array(
            'taxonomy' => WPLG_TAXONOMY,
            'field' => 'term_id',
            'terms' => $gallery_id,
            'include_children' => false
        );

        $categories = Helper::getGalleryCateogryItems($gallery_id);
        if (!empty($categories)) {
            foreach ($categories as $category) {
                $sources = explode('***', $category->source_id);
                $category_id = (int) $sources[0];
                if (empty($sources[1])) {
                    continue;
                }
                $taxonomy = $sources[1];
                if ($category_id === 0) {
                    $tax_query[] = array(
                        'taxonomy' => $taxonomy,
                        'operator' => 'EXISTS'
                    );
                } else {
                    $tax_query[] = array(
                        'taxonomy' => $taxonomy,
                        'field' => 'term_id',
                        'terms' => $category_id,
                        'include_children' => (isset($params['include_children']) && (int) $params['include_children'] === 1)
                    );
                }

            }
        }

        $max_items = (!empty($params['max_items'])) ? (int) $params['max_items'] : -1;
        /* Query images */
        $args = array(
            'posts_per_page' => $max_items,
            'post_status' => array('publish', 'inherit'),
            'post_type' => 'any',
            'order' => $params['wp_order'],
            'tax_query' => $tax_query,
            'meta_query' => array(
                'relation' => 'OR',
                array(
                    'key'     => 'wplg_image_status',
                    'compare' => 'NOT EXISTS'
                ),
                array(
                    'key'     => 'wplg_image_status',
                    'compare' => '=',
                    'value' => 1
                )
            )
        );

        // only user orderby if not random
        if ($params['wp_orderby'] !== 'rand' || !$isPagination) {
            $args['orderby'] = $params['wp_orderby'];
        }

        $query = new WP_Query($args);
        $medias_wordpress = $query->get_posts();

        // convert date of wordpress media to int
        if ((!empty($items) || !empty($ftp_sources)) && !empty($query->get_posts())) {
            if ($params['wp_orderby'] === 'date') {
                foreach ($medias_wordpress as &$medias_wordpres) {
                    $medias_wordpres->post_date = strtotime($medias_wordpres->post_date);
                }
            }
        }

        // merge all items
        $attachments = array_merge($attachments, $medias_wordpress);
        $total_items = count($attachments);

        // if order by is random and enable pagination, should get item by pagination before
        if ($params['wp_orderby'] === 'rand' && $isPagination) {
            $attachments = array_slice($attachments, $offset, $params['items_per_page']);
        }

        // sort again array item if include online source
        if (!empty($items) || !empty($ftp_sources) || ($isPagination && $params['wp_orderby'] === 'rand')) {
            switch ($params['wp_orderby']) {
                case 'title':
                    if ($params['wp_order'] === 'ASC') {
                        usort($attachments, function($a, $b) {return strcmp($a->post_title, $b->post_title);});
                    } else {
                        usort($attachments, function($a, $b) {return strcmp($b->post_title, $a->post_title);});
                    }
                    break;
                case 'date':
                    if ($params['wp_order'] === 'ASC') {
                        usort($attachments, function($a, $b) {return $a->post_date - $b->post_date;});
                    } else {
                        usort($attachments, function($a, $b) {return $b->post_date - $a->post_date;});
                    }
                    break;
                case 'rand':
                    shuffle($attachments);
                    break;
            }
        }

        // if order by isn't random and enable pagination, should get item by pagination after
        if ($params['wp_orderby'] !== 'rand' && $isPagination) {
            $attachments = array_slice($attachments, $offset, $params['items_per_page']);
        }
        return array('items' => $attachments, 'total_items' => $total_items);
    }

    /**
     * Load scripts and styles
     *
     * @param string  $theme              Theme name
     * @param integer $gallery_navigation Gallery navigation
     *
     * @return void
     */
    public function enqueue($theme, $gallery_navigation = 0)
    {
        wp_enqueue_script('jquery');
        wp_enqueue_style(
            'wp-material-icon',
            'https://fonts.googleapis.com/css?family=Material+Icons|Material+Icons+Outlined'
        );
        wp_enqueue_script(
            'wplg-magnific-popup-script',
            WPLG_PLUGIN_URL . '/assets/js/magnific-popup/jquery.magnific-popup.min.js',
            array('jquery'),
            '1.1.0',
            true
        );

        wp_enqueue_style(
            'wplg-magnific-popup-style',
            WPLG_PLUGIN_URL . '/assets/js/magnific-popup/magnific-popup.css',
            array(),
            '1.1.0'
        );

        wp_enqueue_script(
            'wplg-imagesloaded',
            WPLG_PLUGIN_URL . 'assets/js/imagesloaded.min.js',
            array(),
            '3.1.5',
            true
        );

        wp_enqueue_script(
            'wplg-autobrower',
            WPLG_PLUGIN_URL . 'assets/js/jquery.esn.autobrowse.js',
            array('jquery'),
            '2.0',
            true
        );

        if ($theme === 'slider' || (int) $gallery_navigation === 1) {
            wp_enqueue_style(
                'wp-slick-style',
                WPLG_PLUGIN_URL . 'assets/js/slick/slick.css',
                array(),
                WPLG_VERSION
            );

            wp_enqueue_style(
                'wp-slick-theme-style',
                WPLG_PLUGIN_URL . 'assets/js/slick/slick-theme.css',
                array(),
                WPLG_VERSION
            );

            wp_enqueue_script(
                'wp-slick-script',
                WPLG_PLUGIN_URL . 'assets/js/slick/slick.min.js',
                array('jquery'),
                WPLG_VERSION
            );
        }

        if (in_array($theme, array('tiles', 'compact', 'justified_grid')) || (int) $gallery_navigation === 1) {
            wp_enqueue_style(
                'wp-unite-style',
                WPLG_PLUGIN_URL . 'assets/css/unite-gallery.css',
                array(),
                WPLG_VERSION
            );

            wp_enqueue_script(
                'wp-unite-script',
                WPLG_PLUGIN_URL . 'assets/js/unitegallery.min.js',
                array('jquery'),
                WPLG_VERSION
            );

            wp_enqueue_script(
                'wp-unite-tiles-js',
                WPLG_PLUGIN_URL . 'frontend/themes/tiles/ug-theme-tiles.js',
                array('jquery'),
                WPLG_VERSION
            );

            wp_enqueue_script(
                'wp-unite-compact-js',
                WPLG_PLUGIN_URL . 'frontend/themes/compact/ug-theme-compact.js',
                array('jquery'),
                WPLG_VERSION
            );
        }

        wp_enqueue_style(
            'wp-gallery-css',
            WPLG_PLUGIN_URL . '/assets/css/gallery.css',
            array(),
            WPLG_VERSION
        );

        wp_enqueue_script(
            'wp-gallery-js',
            WPLG_PLUGIN_URL . '/assets/js/gallery.js',
            array('jquery'),
            WPLG_VERSION,
            true
        );
        wp_localize_script('wp-gallery-js', 'wpgallery', $this->localizeScript());
    }

    /**
     * Localize a script.
     * Works only if the script has already been added.
     *
     * @return array
     */
    public function localizeScript()
    {
        return array(
            'wplg_nonce' => wp_create_nonce('wplg_nonce'),
            'ajaxurl' => admin_url('admin-ajax.php')
        );
    }

    /**
     * Get settings front
     *
     * @param integer $gallery_id Gallery ID
     * @param array $request_settings Request settings
     * @param array $options Gallery configs
     *
     * @return array
     */
    public function getSettingsFront($gallery_id, $request_settings, $options)
    {
        if (empty($options)) {
            $options = array();
        }

        $theme = get_term_meta((int) $gallery_id, 'wplg_theme', true);
        $default = Helper::getDefaultOptions($theme);
        $default['gallery_id'] = (int) $gallery_id;
        $settings = array_merge($default, $options, $request_settings);
        if (empty($request_settings['theme']) && empty($options['theme'])) {
            $theme = get_term_meta((int) $gallery_id, 'wplg_theme', true);
            $settings['theme'] = $theme;
        }

        if ((int)$settings['skin_on_hover'] === 1) {
            if ($settings['skin_type'] === 'border-transition') {
                $settings['textpanel_bg_color'] = 'transparent';
            }
        }
        return $settings;
    }
}
