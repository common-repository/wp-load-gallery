<?php
/* Prohibit direct script loading */
defined('ABSPATH') || die('No direct script access allowed!');
require_once(WPLG_PLUGIN_DIR . 'admin/class/main-google.php');
use WPLG\Helper;
use WPLG\Encoding;

/**
 * Class WpLoadGalleryAdmin
 * This class that holds most of the admin functionality for WP Load Gallery
 */
class WpLoadGalleryAdmin
{
    /**
     * WpLoadGalleryAdmin constructor.
     */
    public function __construct()
    {
        Helper::createTable();
        add_action('init', array($this, 'init'), 1);
        add_action('widgets_init', array($this, 'wpb_load_widget'));
        add_action('admin_init', array($this, 'adminRedirects'));
        add_action('admin_menu', array($this, 'addMenuPage'));
        add_action('admin_enqueue_scripts', array($this, 'register'));
        if (is_admin()) {
            add_action('enqueue_block_editor_assets', array($this, 'addEditorAssets'));
        }
        add_action('wp_ajax_wpgallery', array($this, 'startProcess'));
        add_action('wp_ajax_wplg_edit_gallery', array($this, 'editGallery'));
    }

    /**
     * Load plugin text domain
     *
     * @return void
     */
    public function init()
    {
        load_plugin_textdomain(
            'wp-load-gallery',
            false,
            dirname(plugin_basename(WPLG_FILE)) . '/languages/'
        );
    }

    public function wpb_load_widget() {
        register_widget( 'wplg_widget' );
    }

    /**
     * Handle redirects
     *
     * @return void
     */
    public function adminRedirects()
    {
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- View request, no action
        if ((!empty($_GET['action']))) {
            switch ($_GET['action']) {
                case 'google_drive_connect':
                    $google_drive = new mainGoogleDrive();
                    $google_drive->ggAuthenticated();
                    break;
                case 'google_drive_disconnect':
                    $google_drive = new mainGoogleDrive();
                    $google_drive->disConnect();
                    break;
            }

        }
    }

    /**
     * Run ajax
     *
     * @return void
     */
    public function startProcess()
    {
        if (empty($_POST['wplg_nonce'])
            || !wp_verify_nonce($_POST['wplg_nonce'], 'wplg_nonce')) {
            die();
        }

        if (isset($_REQUEST['task'])) {
            switch ($_REQUEST['task']) {
                case 'change_gallery':
                    $this->changeGallery();
                    break;
                case 'import_media_library':
                    $this->importMediaLibrary();
                    break;
                case 'create_gallery':
                    $this->createGallery();
                    break;
                case 'delete_gallery':
                    $this->deleteGallery();
                    break;
                case 'delete_imgs':
                    $this->deleteImgs();
                    break;
                case 'item_details':
                    $this->itemDetails();
                    break;
                case 'update_item':
                    $this->updateItem();
                    break;
                case 'update_parent_gallery':
                    $this->updateParentGallery();
                    break;
                case 'load_server_folder':
                    $this->loadServerFolder();
                    break;

                case 'server_folder_add':
                    $this->serverFolderAdd();
                    break;

                case 'server_folder_remove':
                    $this->serverFolderRemove();
                    break;

                case 'load_server_folder_preview':
                    $this->loadServerFolderPreview();
                    break;
                case 'add_video':
                    $this->addVideo();
                    break;
                case 'load_default_options':
                    $this->loadDefaultOptions();
                    break;

                case 'loadGoogleDrive':
                    $this->loadGoogleDrive();
                    break;

                case 'add_google_drive_file_to_gallery':
                    $this->addGoogleDriveFileToGallery();
                    break;

                case 'wplg_upload':
                    $this->uploadImages();
                    break;

                case 'load_categories_by_post_type':
                    $this->loadCategoriesByPostType();
                    break;

                case 'load_post_list':
                    $this->loadPostList();
                    break;

                case 'add_posts_to_gallery':
                    $this->addPostsToGallery();
                    break;

                case 'add_category_to_gallery':
                    $this->addCategoryToGallery();
                    break;

                case 'load_media_categories_list':
                    $this->loadMediaCategoriesList();
                    break;

                case 'add_media_categories':
                    $this->addMediaCategories();
                    break;

                case 'move_gallery':
                    $this->moveGallery();
                    break;
            }
        }
    }

    /**
     * Enqueue styles and scripts for gutenberg
     *
     * @return void
     */
    public function addEditorAssets()
    {
        wp_enqueue_script(
            'wplg_blocks',
            WPLG_PLUGIN_URL . '/admin/blocks/insert-gallery/block.js',
            array('wp-blocks', 'wp-i18n', 'wp-element', 'wp-data', 'wp-editor'),
            WPLG_VERSION
        );

        wp_enqueue_style(
            'wplg_blocks',
            WPLG_PLUGIN_URL . '/admin/blocks/insert-gallery/style.css',
            array(),
            WPLG_VERSION
        );

        $galleries = get_categories(
            array(
                'hide_empty' => false,
                'taxonomy' => WPLG_TAXONOMY
            )
        );
        $galleries = $this->parentSort($galleries);
        $galleries_select = array();
        foreach ($galleries as &$gallery) {
            $spaces = '';
            for ($i = 0; $i<= $gallery->depth; $i++) {
                $spaces .= '--';
            }
            $gallery->name = $spaces . $gallery->name;
            $galleries_select[] = array('label' => $gallery->name, 'value' => $gallery->term_id);
        }

        $params = array(
            'l18n' => array(
                'block_title'   => esc_html__('WP Load Gallery', 'wp-load-gallery'),
                'no_post_found' => esc_html__('No post found', 'wp-load-gallery'),
                'select_label'  => esc_html__('Select a News Block', 'wp-load-gallery')
            ),
            'vars' => array(
                'galleries_select' => $galleries_select,
                'block_cover' => WPLG_PLUGIN_URL . '/admin/blocks/insert-gallery/wp-load-gallery.png',
            )
        );

        wp_localize_script('wplg_blocks', 'wplg_blocks', $params);
    }

    /**
     * Load scripts and style
     *
     * @return void
     */
    public function register()
    {
        global $current_screen;
        if ($current_screen->base === 'wp-load-gallery_page_load_gallery_cloud') {
            wp_enqueue_style(
                'wplg-cloud-style',
                WPLG_PLUGIN_URL . '/assets/css/admin-cloud.css',
                array(),
                WPLG_VERSION
            );

            wp_enqueue_script(
                'wplg-mdl',
                WPLG_PLUGIN_URL . '/assets/js/admin/admin-cloud.js',
                array('jquery'),
                WPLG_VERSION
            );
        }

        if ($current_screen->base === 'toplevel_page_wp-load-gallery') {
            wp_enqueue_media();
            wp_enqueue_script('jquery');
            wp_enqueue_script('jquery-ui-draggable');
            wp_enqueue_script('jquery-ui-droppable');
            wp_enqueue_style(
                'wp-material-icon',
                'https://fonts.googleapis.com/css?family=Material+Icons|Material+Icons+Outlined'
            );

            wp_enqueue_style(
                'wplg-mdl',
                WPLG_PLUGIN_URL . '/assets/js/modal-dialog/jquery-modal-dialog.css',
                array(),
                WPLG_VERSION
            );

            wp_enqueue_style(
                'wplg-deep_orange',
                WPLG_PLUGIN_URL . '/assets/js/modal-dialog/material.deep_orange-amber.min.css',
                array(),
                WPLG_VERSION
            );

            wp_enqueue_script(
                'wplg-material',
                WPLG_PLUGIN_URL . '/assets/js/modal-dialog/material.min.js',
                array('jquery'),
                WPLG_VERSION
            );

            wp_enqueue_script(
                'wplg-mdl',
                WPLG_PLUGIN_URL . '/assets/js/modal-dialog/jquery-modal-dialog.js',
                array('jquery'),
                WPLG_VERSION
            );

            wp_enqueue_script(
                'wplg-magnific-popup-script',
                WPLG_PLUGIN_URL . '/assets/js/magnific-popup/jquery.magnific-popup.min.js',
                array('jquery'),
                '0.9.9',
                true
            );

            wp_enqueue_style(
                'wplg-magnific-popup-style',
                WPLG_PLUGIN_URL . '/assets/js/magnific-popup/magnific-popup.css',
                array(),
                '0.9.9'
            );

            wp_enqueue_script(
                'wplg-jquery-form',
                WPLG_PLUGIN_URL . 'assets/js/jquery.form.js',
                array('jquery'),
                WPLG_VERSION
            );

            wp_enqueue_script(
                'wplg-snackbar',
                WPLG_PLUGIN_URL . '/assets/js/snackbar.js',
                array('jquery'),
                WPLG_VERSION
            );

            wp_enqueue_script(
                'wplg-server-folder-tree',
                WPLG_PLUGIN_URL . '/assets/js/admin/server_folder_tree.js',
                array('jquery'),
                WPLG_VERSION
            );

            wp_enqueue_script(
                'wplg-script',
                WPLG_PLUGIN_URL . '/assets/js/admin/script.js',
                array('jquery', 'plupload'),
                WPLG_VERSION
            );

            wp_enqueue_script(
                'wplg-tree',
                WPLG_PLUGIN_URL . '/assets/js/admin/gallery_tree.js',
                array('jquery', 'wplg-script'),
                WPLG_VERSION
            );

            wp_enqueue_script(
                'wplg-post-tree',
                WPLG_PLUGIN_URL . '/assets/js/admin/post_categories_tree.js',
                array('jquery', 'wplg-script'),
                WPLG_VERSION
            );

            wp_enqueue_style(
                'wplg-style',
                WPLG_PLUGIN_URL . '/assets/css/style.css',
                array(),
                WPLG_VERSION
            );

            wp_localize_script(
                'wplg-script',
                'wplg_objects',
                $this->localizeScript()
            );
        }
    }

    /**
     * Get all gallery
     *
     * @param string $taxonomy Taxonomy
     *
     * @return array
     */
    public function getAllGalleries($taxonomy, $rootLabel = '')
    {
        $terms = get_categories(
            array(
                'hide_empty' => false,
                'taxonomy' => $taxonomy
            )
        );

        $terms = $this->parentSort($terms);

        $terms_order = array();
        $attachment_terms[] = array(
            'id' => 0,
            'label' => (!empty($rootLabel)) ? $rootLabel : esc_html__('My Gallery', 'wp-load-gallery'),
            'slug' => '',
            'parent_id' => 0
        );
        $terms_order[] = 0;

        foreach ($terms as $term) {
            $attachment_terms[$term->term_id] = array(
                'id' => $term->term_id,
                'label' => $term->name,
                'slug' => $term->slug,
                'parent_id' => $term->category_parent,
                'depth' => $term->depth,
            );
            $terms_order[] = $term->term_id;
        }

        return array(
            'terms_order' => $terms_order,
            'attachment_terms' => $attachment_terms
        );
    }

    /**
     * Localize a script
     *
     * @return array
     */
    public function localizeScript()
    {
        // get all gallery
        $terms = $this->getAllGalleries(WPLG_TAXONOMY);
        $attachment_terms = $terms['attachment_terms'];
        $terms_order = $terms['terms_order'];

        $all_taxonomies = Helper::getAllTerms();
        $l18n = array(
            'gallery_moving' => esc_html__('Gallery moving...', 'wp-load-gallery'),
            'cancel' => esc_html__('Cancel', 'wp-load-gallery'),
            'delete' => esc_html__('Delete', 'wp-load-gallery'),
            'add' => esc_html__('Add', 'wp-load-gallery'),
            'create' => esc_html__('Create', 'wp-load-gallery'),
            'theme_label' => esc_html__('Gallery Theme', 'wp-load-gallery'),
            'iframe_import_label' => esc_html__('Select or upload image to import them to image gallery selection', 'wp-load-gallery'),
            'import' => esc_html__('Import images', 'wp-load-gallery'),
            'edit_gallery' => esc_html__('Edit gallery', 'wp-load-gallery'),
            'error' => esc_html__('Error', 'wp-load-gallery'),
            'save' => esc_html__('Save', 'wp-load-gallery'),
            'delete_selected_image' => esc_html__('Are you sure to want to delete these items?', 'wp-load-gallery'),
            'delete_gallery' => esc_html__('Are you sure you want to remove this gallery?', 'wp-load-gallery'),
            'add_gallery' => esc_html__('Gallery added', 'wp-load-gallery'),
            'save_img' => esc_html__('Items saved', 'wp-load-gallery'),
            'delete_img' => esc_html__('Items removed', 'wp-load-gallery'),
            'save_glr' => esc_html__('Gallery saved', 'wp-load-gallery'),
            'delete_glr' => esc_html__('Gallery removed', 'wp-load-gallery'),
            'gallery_saving' => esc_html__('Gallery saving...', 'wp-load-gallery'),
            'gallery_removing' => esc_html__('Gallery removing...', 'wp-load-gallery'),
            'creating_gallery' => esc_html__('Gallery creating...', 'wp-load-gallery'),
            'loading_item_details' => esc_html__('Loading item details...', 'wp-load-gallery'),
            'server_folders' => esc_html__('Server Folders', 'wp-load-gallery'),
            'uploading_img_process' => esc_html__('Image(s) uploading...', 'wp-load-gallery'),
            'shortcode_copied' => esc_html__('Shortcode copied!', 'wp-load-gallery'),
            'shortcode_failed' => esc_html__('Shortcode failed!', 'wp-load-gallery'),
            'import_media_library' => esc_html__('Item(s) importing...', 'wp-load-gallery')
        );

        $vars = array(
            'wplg_nonce' => wp_create_nonce('wplg_nonce'),
            'categories' => $attachment_terms,
            'categories_order' => $terms_order,
            'plugin_url_image' => WPLG_PLUGIN_URL . 'assets/images/',
            'all_taxonomies' => $all_taxonomies
        );

        return array(
            'l18n' => $l18n,
            'vars' => $vars
        );
    }

    /**
     * Sort parents before children
     * http://stackoverflow.com/questions/6377147/sort-an-array-placing-children-beneath-parents
     *
     * @param array $objects Input objects with attributes 'id' and 'parent'
     * @param array $result Optional, reference) internal
     * @param integer $parent Parent of gallery
     * @param integer $depth Depth of gallery
     *
     * @return array           output
     */
    public function parentSort(array $objects, array &$result = array(), $parent = 0, $depth = 0)
    {
        foreach ($objects as $key => $object) {
            if ((int)$object->parent === (int)$parent) {
                $object->depth = $depth;
                array_push($result, $object);
                unset($objects[$key]);
                $this->parentSort($objects, $result, $object->term_id, $depth + 1);
            }
        }
        return $result;
    }

    /**
     * Add menu media page
     *
     * @return void
     */
    public function addMenuPage()
    {
        add_menu_page(
            'WP Load Gallery',
            'WP Load Gallery',
            'upload_files',
            'wp-load-gallery',
            array($this, 'loadPage'),
            'dashicons-format-gallery',
            10
        );

        $submenu_pages = array();
        $submenu_pages[] = array(
            'wp-load-gallery',
            '',
            esc_html__('Galleries', 'wp-load-gallery'),
            'upload_files',
            'wp-load-gallery',
            array($this, 'loadPage'),
            null
        );

        $submenu_pages[] = array(
            'wp-load-gallery',
            '',
            esc_html__('Cloud', 'wp-load-gallery'),
            'upload_files',
            'load_gallery_cloud',
            array($this, 'loadPage'),
            null
        );

        if (count($submenu_pages)) {
            foreach ($submenu_pages as $submenu_page) {
                // Add submenu page
                $admin_page = add_submenu_page(
                    $submenu_page[0],
                    $submenu_page[2],
                    $submenu_page[2],
                    $submenu_page[3],
                    $submenu_page[4],
                    $submenu_page[5]
                );

                // Check if we need to hook
                if (isset($submenu_page[6]) && null !== $submenu_page[6]
                    && is_array($submenu_page[6]) && count($submenu_page[6]) > 0) {
                    foreach ($submenu_page[6] as $submenu_page_action) {
                        add_action('load-' . $admin_page, $submenu_page_action);
                    }
                }
            }
        }
    }

    /**
     * Load the form for a WPSEO admin page
     *
     * @return void
     */
    public function loadPage()
    {
        if (isset($_GET['page'])) {
            // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- No action, nonce is not required
            switch ($_GET['page']) {
                case 'load_gallery_cloud':
                    // google drive setup
                    $gdconfigs = get_option('wplg_google_drive_configs');
                    $googleDrive  = new mainGoogleDrive();
                    if (!is_array($gdconfigs) || (is_array($gdconfigs) && empty($gdconfigs))) {
                        $gdconfigs = array(
                            'ClientId'     => '',
                            'ClientSecret' => '',
                            'drive_type' => 'my_drive',
                            'connected' => 0
                        );
                    }

                    if (isset($_POST['wpgc_save_cloud_configs_btn'])) {
                        // save google params
                        if (isset($_POST['wplg_google_drive'])) {
                            if (isset($_POST['wplg_google_drive']['ClientId']) && isset($_POST['wplg_google_drive']['ClientSecret'])) {
                                $gdconfigs['ClientId']     = sanitize_text_field(trim($_POST['wplg_google_drive']['ClientId']));
                                $gdconfigs['ClientSecret'] = sanitize_text_field(trim($_POST['wplg_google_drive']['ClientSecret']));
                            }

                            if (isset($_POST['wplg_google_drive']['drive_type'])) {
                                $gdconfigs['drive_type'] = sanitize_text_field($_POST['wplg_google_drive']['drive_type']);
                            }

                            update_option('wplg_google_drive_configs', $gdconfigs);
                            $gdconfigs = get_option('wplg_google_drive_configs');
                            $googleDrive  = new mainGoogleDrive();
                        }
                    }

                    require_once(WPLG_PLUGIN_DIR . '/admin/pages/cloud_auth.php');
                    break;
                default:
                    require_once(WPLG_PLUGIN_DIR . '/admin/pages/trees.php');
            }
        }
    }

    /**
     * Get count image selection
     *
     * @param integer $gallery_id Id of gallery
     *
     * @return integer
     */
    public function getCountImages($gallery_id)
    {
        $params = array(
            'taxonomy' => WPLG_TAXONOMY,
            'field' => 'term_id',
            'terms' => (int)$gallery_id,
            'include_children' => false
        );

        $args = array(
            'posts_per_page' => -1,
            'post_status' => 'any',
            'post_type' => array('attachment'),
            'tax_query' => array(
                $params
            )
        );
        $querycount = new WP_Query($args);
        $post_count = $querycount->post_count;
        return $post_count;
    }

    /**
     * Update item
     *
     * @return void
     */
    public function updateItem()
    {
        if (empty($_POST['wplg_nonce'])
            || !wp_verify_nonce($_POST['wplg_nonce'], 'wplg_nonce')) {
            wp_send_json(
                array(
                    'status' => false
                )
            );
        }

        if (isset($_POST['id'])) {
            $id = sanitize_text_field($_POST['id']);
            $status = (isset($_POST['status'])) ? (int) $_POST['status'] : 1;
            $params = array(
                'title' => sanitize_text_field($_POST['title']),
                'desc' => sanitize_text_field($_POST['excerpt']),
                'link' => sanitize_text_field($_POST['link_to']),
                'status' => $status
            );

            if (strpos($id, 'not_wp') !== false) {
                $id = str_replace('not_wp-', '', $id);
                $details = Helper::getGalleryItemsDetails($id);
                $source_id = $details->source_id;
                Helper::updateItemBySourceId($source_id, $params);
            } else {
                // Update post
                $args = array(
                    'ID' => (int) $id,
                    'post_title' => $params['title'],
                    'post_excerpt' => $params['desc']
                );

                // Update the post into the database
                wp_update_post($args);
                update_post_meta(
                    (int) $id,
                    'wplg_image_custom_link',
                    sanitize_text_field($params['link'])
                );

                update_post_meta(
                    (int) $id,
                    'wplg_image_status',
                    $status
                );
            }

            wp_send_json(array('status' => true));
        }
        wp_send_json(array('status' => false));
    }

    /**
     * Get item details
     *
     * @return void
     */
    public function itemDetails()
    {
        if (empty($_POST['wplg_nonce'])
            || !wp_verify_nonce($_POST['wplg_nonce'], 'wplg_nonce')) {
            die();
        }

        if (isset($_POST['id'])) {
            ob_start();
            $id = $_POST['id'];
            if (strpos($id, 'not_wp') !== false) {
                $id = str_replace('not_wp-', '', $id);
                $details = Helper::getGalleryItemsDetails($id);
                $link_to = $details->custom_link;
                $status = $details->status;
            } else {
                $id = (int) $_POST['id'];
                $details = get_post($id);
                $link_to = get_post_meta($id, 'wplg_image_custom_link', true);
                $status = get_post_meta($id, 'wplg_image_status', true);
            }

            if ($status === '') {
                $status = 1;
            }

            require_once(WPLG_PLUGIN_DIR . '/admin/pages/image_details.php');
            $images_html = ob_get_contents();
            ob_end_clean();
            wp_send_json(array('status' => true, 'html' => $images_html));
        }
        wp_send_json(array('status' => false, 'html' => ''));
    }

    public function loadDefaultOptions()
    {
        if (empty($_POST['wplg_nonce'])
            || !wp_verify_nonce($_POST['wplg_nonce'], 'wplg_nonce')) {
            die();
        }

        $theme = Helper::getTheme($_POST['theme']);
        $options = Helper::getDefaultOptions($theme);
        wp_send_json(
            array(
                'status' => true,
                'options' => $options
            )
        );
    }

    /**
     * Change gallery
     *
     * @return void
     */
    public function changeGallery()
    {
        if (empty($_POST['wplg_nonce'])
            || !wp_verify_nonce($_POST['wplg_nonce'], 'wplg_nonce')) {
            die();
        }

        $gallery_id = (!empty($_POST['id'])) ? (int)$_POST['id'] : 0;
        $gallery = get_term($gallery_id, WPLG_TAXONOMY);

        // get params
        $theme = get_term_meta($gallery_id, 'wplg_theme', true);
        $options = get_term_meta($gallery_id, 'wplg_options', true);
        $default_options = Helper::getDefaultOptions($theme);

        if (!empty($options) && is_array($options)) {
            $options = array_merge($default_options, $options);
        } else {
            $options = $default_options;
        }

        // get images html
        $args = array(
            'posts_per_page' => -1,
            'post_status' => 'any',
            'post_type' => array('attachment', 'post', 'product', 'page'),
            'tax_query' => array(
                array(
                    'taxonomy' => WPLG_TAXONOMY,
                    'field' => 'term_id',
                    'terms' => $gallery_id,
                    'include_children' => false
                )
            ),
            'orderby' => $options['wp_orderby'],
            'order' => $options['wp_order']
        );
        $query = new WP_Query($args);
        $imageIDs = $query->get_posts();
        $images_html = '';
        $folders_html = '';
        ob_start();
        foreach ($imageIDs as $image) {
            $media_type = 'wp';
            $id_item = $image->ID;
            $post_type = $image->post_type;
            if ($image->post_type === 'attachment') {
                $thumnailUrl = wp_get_attachment_image_url($id_item, 'medium');
            } else {
                $thumnailUrl = get_the_post_thumbnail_url($image->ID);
                if (!$thumnailUrl) {
                    $thumnailUrl = WPLG_PLUGIN_URL . 'assets/images/icons8-news.svg';
                }
            }
            if ($thumnailUrl) {
                require(WPLG_PLUGIN_DIR . '/admin/pages/thumbnail.php');
            }
        }

        $images_html .= ob_get_contents();
        ob_end_clean();

        // get server folder list
        $folders_path = get_term_meta($gallery_id, 'wplg_gallery_server_folder', true);
        if (!empty($folders_path) && is_array($folders_path)) {
            $folders_html .= $this->getServerFolderHtml($folders_path);
        }

        // get gallery category item
        $categories = Helper::getGalleryCateogryItems($gallery_id);
        if (!empty($categories)) {
            $folders_html .= $this->getCategoriesHtml($categories);
        }
        // Get gallery item
        $items = Helper::getGalleryItems($gallery_id, $options['wp_orderby'], $options['wp_order']);
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

        ob_start();
        foreach ($items as $item) {
            $id_item = $item->id;
            $media_type = $item->type;
            switch ($item->type) {
                case 'upload':
                    $thumnailUrl = str_replace(get_home_path(), site_url('/'), $item->thumbnail);
                    if ($thumnailUrl) {
                        require(WPLG_PLUGIN_DIR . '/admin/pages/thumbnail.php');
                    }
                    break;
                case 'google_drive_video':
                case 'google_drive_image':
                    if (!empty($configs['connected']) && !empty($configs['googleCredentials'])) {
                        if ($google_connect) {
                            $thumnailUrl = 'https://drive.google.com/thumbnail?id=' . $item->source_id;
                            if ($thumnailUrl) {
                                require(WPLG_PLUGIN_DIR . '/admin/pages/thumbnail.php');
                            }
                        }
                    }
                    break;
                case 'youtube':
                    $thumnailUrl = $item->thumbnail;
                    if ($thumnailUrl) {
                        require(WPLG_PLUGIN_DIR . '/admin/pages/thumbnail.php');
                    }
                    break;
                case 'vimeo':
                    $thumnailUrl = $item->thumbnail;
                    if ($thumnailUrl) {
                        require(WPLG_PLUGIN_DIR . '/admin/pages/thumbnail.php');
                    }
                    break;
            }
        }
        $images_html .= ob_get_contents();
        ob_end_clean();

        wp_send_json(
            array(
                'status' => true,
                'name' => $gallery->name,
                'theme' => $theme,
                'options' => $options,
                'images_html' => $images_html,
                'folders_html' => $folders_html,
            )
        );
    }

    /**
     * Ajax import images from wordpress
     *
     * @return void
     */
    public function importMediaLibrary()
    {
        if (empty($_POST['wplg_nonce'])
            || !wp_verify_nonce($_POST['wplg_nonce'], 'wplg_nonce')) {
            wp_send_json(
                array(
                    'status' => false
                )
            );
        }

        if (empty($_POST['files'])) {
            wp_send_json(array('status' => false));
        }

        $files = explode(',', $_POST['files']);
        foreach ($files as $fileID) {
            wp_set_object_terms((int) $fileID, (int) $_POST['gallery_id'], WPLG_TAXONOMY, true);
        }
        wp_send_json(array('status' => true));
    }

    /**
     * Generate attachment html
     *
     * @param string $title Title of image
     * @param integer $id Id of image
     *
     * @return void
     */
    public function generateAttachmentHtml($title, $id)
    {
        ?>
        <li aria-label="<?php echo esc_html($title) ?>" aria-checked="false" data-id="<?php echo esc_html($id) ?>"
            class="attachment">
            <div class="wplg-image-preview">
                <?php
                $url = wp_get_attachment_image_url($id, 'medium');
                ?>
                <img src="<?php echo esc_html($url) ?>" draggable="false" alt="">
                <div class="action_images">
                    <span data-id="<?php echo esc_html($id) ?>"
                          class="edit_image_selection dashicons dashicons-edit"></span>
                    <span data-id="<?php echo esc_html($id) ?>"
                          class="delete_image_selection dashicons dashicons-trash"></span>
                </div>
            </div>
            <button type="button" class="check" tabindex="-1"><span class="media-modal-icon"></span><span
                        class="screen-reader-text">Deselect</span></button>
        </li>
        <?php
    }

    /**
     * Ajax create gallery
     *
     * @return void
     */
    public function createGallery()
    {
        if (empty($_POST['wplg_nonce'])
            || !wp_verify_nonce($_POST['wplg_nonce'], 'wplg_nonce')) {
            wp_send_json(
                array(
                    'status' => false
                )
            );
        }

        // get gallery info
        $title = (isset($_POST['title'])) ? $_POST['title'] : esc_html__('New gallery', 'wp-load-gallery');
        $title = sanitize_text_field($title);
        $parent = (isset($_POST['parent'])) ? (int) $_POST['parent'] : 0;
        $theme = 'default';
        // add new gallery
        $inserted = wp_insert_term(
            $title,
            WPLG_TAXONOMY,
            array('parent' => $parent)
        );

        if (is_wp_error($inserted)) {
            wp_send_json(array('status' => false, 'msg' => $inserted->get_error_message()));
        }

        update_term_meta((int)$inserted['term_id'], 'wplg_theme', $theme);
        $termInfos = get_term($inserted['term_id'], WPLG_TAXONOMY);
        $termInfos->theme = $theme;
        // get all gallery
        $terms = $this->getAllGalleries(WPLG_TAXONOMY);
        $attachment_terms = $terms['attachment_terms'];
        $terms_order = $terms['terms_order'];

        wp_send_json(
            array(
                'items' => $termInfos,
                'status' => true,
                'categories' => $attachment_terms,
                'categories_order' => $terms_order
            )
        );
    }

    /**
     * Ajax delete gallery
     *
     * @return void
     */
    public function deleteGallery()
    {
        if (empty($_POST['wplg_nonce'])
            || !wp_verify_nonce($_POST['wplg_nonce'], 'wplg_nonce')) {
            wp_send_json(
                array(
                    'status' => false
                )
            );
        }

        $gallery_id = (int) $_POST['id'];
        if (wp_delete_term($gallery_id, WPLG_TAXONOMY)) {
            Helper::deleteGalleryItemsByGalleryID($gallery_id);
            wp_send_json(array('status' => true));
        } else {
            wp_send_json(array('status' => false));
        }
    }

    /**
     * Ajax edit gallery
     *
     * @return void
     */
    public function editGallery()
    {
        if (empty($_POST['wplg_nonce'])
            || !wp_verify_nonce($_POST['wplg_nonce'], 'wplg_nonce')) {
            wp_send_json(
                array(
                    'status' => false,
                    'msg' => esc_html__('Edit failed. Please try again.', 'wp-load-gallery')
                )
            );
        }

        if (isset($_POST['id'])) {
            // get theme
            $theme = Helper::getTheme($_POST['theme']);
            $params = array(
                'name' => sanitize_text_field($_POST['title'])
            );

            $termInfos = wp_update_term((int)$_POST['id'], WPLG_TAXONOMY, $params);
            if ($termInfos instanceof WP_Error) {
                wp_send_json(array('status' => false, 'msg' => $termInfos->get_error_messages()));
            } else {
                /* update theme for this gallery */
                $options = array();
                parse_str($_POST['options'], $options);
                $radio_fields = Helper::getRadioOptions();
                $options = array_merge($radio_fields, $options);
                //sanitize options
                $options = Helper::sanitizeOptions($options);
                update_term_meta((int)$_POST['id'], 'wplg_theme', $theme);
                update_term_meta((int)$_POST['id'], 'wplg_options', $options);

                /* set images to gallery */
                $images = get_objects_in_term((int)$_POST['id'], WPLG_TAXONOMY);
                $termInfos = get_term((int)$_POST['id'], WPLG_TAXONOMY);
                $termInfos->theme = $theme;
                $termInfos->images = $images;
                $json = array(
                    'status' => true,
                    'items' => $termInfos,
                );

                wp_send_json(
                    $json
                );
            }
        }
        wp_send_json(
            array(
                'status' => false,
                'msg' => esc_html__('This gallery does not exist!', 'wp-load-gallery')
            )
        );
    }

    /**
     * Remove selected images from media selection
     *
     * @return void
     */
    public function deleteImgs()
    {
        if (empty($_POST['wplg_nonce'])
            || !wp_verify_nonce($_POST['wplg_nonce'], 'wplg_nonce')) {
            wp_send_json(
                array(
                    'status' => false
                )
            );
        }

        if (isset($_POST['ids'])) {
            $ids = explode(',', $_POST['ids']);
            $gallery_id = (int) $_POST['id_gallery'];
            if (!empty($ids)) {
                foreach ($ids as $id) {
                    if (strpos($id, 'not_wp') !== false) {
                        $id = str_replace('not_wp-', '', $id);
                        Helper::deleteGalleryItem($id);
                    } else {
                        wp_remove_object_terms((int)$id, $gallery_id, WPLG_TAXONOMY);
                    }
                }
            }

            wp_send_json(array('status' => true));
        }
        wp_send_json(array('status' => false));
    }

    /**
     * Update gallery parent when draggable gallery on folder tree
     *
     * @return void
     */
    public function updateParentGallery()
    {
        if (empty($_POST['wplg_nonce'])
            || !wp_verify_nonce($_POST['wplg_nonce'], 'wplg_nonce')) {
            wp_send_json(
                array(
                    'status' => false
                )
            );
        }

        if (isset($_POST['id_gallery']) && isset($_POST['parent'])) {
            $r = wp_update_term(
                (int)$_POST['id_gallery'],
                WPLG_TAXONOMY,
                array('parent' => (int)$_POST['parent'])
            );
            if ($r instanceof WP_Error) {
                wp_send_json(array('status' => false));
            } else {
                // get all gallery
                $terms = $this->getAllGalleries(WPLG_TAXONOMY);
                $attachment_terms = $terms['attachment_terms'];
                $terms_order = $terms['terms_order'];

                wp_send_json(
                    array(
                        'status' => true,
                        'categories' => $attachment_terms,
                        'categories_order' => $terms_order
                    )
                );
            }
        }
    }

    public function loadServerFolder()
    {
        if (empty($_POST['wplg_nonce'])
            || !wp_verify_nonce($_POST['wplg_nonce'], 'wplg_nonce')) {
            die();
        }

        $path = rtrim(str_replace(DIRECTORY_SEPARATOR, '/', ABSPATH), '/');
        $dir = $_REQUEST['dir'];
        $return = array();
        $dirs = array();
        if (file_exists($path . $dir)) {
            $files = scandir($path . $dir);
            $files = array_diff($files, array('..', '.'));
            natcasesort($files);
            if (count($files) > 0) {
                foreach ($files as $file) {
                    if (file_exists($path . $dir . $file) && is_dir($path . $dir . $file)) {
                        $file = Encoding::toUTF8($file);
                        $dirs[] = array('dir' => $dir, 'file' => $file);
                    }
                }
                $return = $dirs;
            }
        }
        wp_send_json($return);
    }

    /**
     * Get vimeo video ID from URL
     *
     * @param string $url URl of video
     *
     * @return mixed|string
     */
    public function getVimeoVideoIdFromUrl($url = '')
    {
        $regs = array();
        $id   = '';
        $pattern = '%^https?:\/\/(?:www\.|player\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|video\/|)(\d+)(?:$|\/|\?)(?:[?]?.*)$%im';
        if (preg_match($pattern, $url, $regs)) {
            $id = $regs[3];
        }

        return $id;
    }

    public function addVideo()
    {
        if (empty($_POST['wplg_nonce'])
            || !wp_verify_nonce($_POST['wplg_nonce'], 'wplg_nonce')) {
            die();
        }

        if (empty($_POST['url'])) {
            wp_send_json(array('status' => false, 'msg' => esc_html__('Sorry, video not found', 'wp-load-gallery')));
        }

        $gallery_id = (int) $_POST['gallery_id'];
        $url  = sanitize_text_field($_POST['url']);
        switch ($_POST['type']) {
            case 'youtube':
                $parts = parse_url($url);
                if ($parts['host'] === 'youtu.be') {
                    $id = trim($parts['path'], '/');
                } else {
                    parse_str($parts['query'], $query);
                    $id = $query['v'];
                }

                $exist = Helper::checkGalleryItemExist($id, $gallery_id, 'youtube');
                if (!$exist) {
                    $json_datas = wp_remote_get('http://www.youtube.com/oembed?url=https://www.youtube.com/watch?v=' . $id . '&format=json');
                    $infos = json_decode($json_datas['body'], true);
                    if (!empty($infos)) {
                        $thumb = 'http://img.youtube.com/vi/' . $id . '/maxresdefault.jpg';
                        $gets = wp_remote_get($thumb);
                        if (!empty($gets) && $gets['response']['code'] !== 200) {
                            $thumb = 'http://img.youtube.com/vi/' . $id . '/sddefault.jpg';
                            $gets = wp_remote_get($thumb);
                        }

                        if (!empty($gets) && $gets['response']['code'] !== 200) {
                            $thumb = 'http://img.youtube.com/vi/' . $id . '/mqdefault.jpg';
                            $gets = wp_remote_get($thumb);
                        }

                        if (!empty($gets) && $gets['response']['code'] !== 200) {
                            $thumb = 'http://img.youtube.com/vi/' . $id . '/hqdefault.jpg';
                            $gets = wp_remote_get($thumb);
                        }

                        if (!empty($gets) && $gets['response']['code'] !== 200) {
                            $thumb = 'http://img.youtube.com/vi/' . $id . '/default.jpg';
                            $gets = wp_remote_get($thumb);
                        }

                        if (empty($gets)) {
                            wp_send_json(array('status' => false, 'msg' => esc_html__('Sorry, video not found', 'wp-load-gallery')));
                        }

                        $item = Helper::insertGalleryItem(
                                $id,
                                $gallery_id,
                                'youtube',
                                $infos['title'],
                                $infos['author_name'],
                                $thumb,
                                'https://www.youtube.com/watch?v=' . $id
                            );
                        if ($item) {
                            wp_send_json(array('status' => true));
                        }
                    }
                }
                break;
            case 'vimeo':
                $id = $this->getVimeoVideoIdFromUrl($url);
                $exist = Helper::checkGalleryItemExist($id, $gallery_id, 'vimeo');
                if (!$exist) {
                    $videos = wp_remote_get('https://player.vimeo.com/video/' . $id . '/config');
                    $body = json_decode($videos['body']);
                    if (!empty($body->video->thumbs->base)) {
                        $item = Helper::insertGalleryItem(
                            $id,
                            $gallery_id,
                            'vimeo',
                            $body->video->title,
                            $body->video->owner->name,
                            $body->video->thumbs->base,
                            'https://vimeo.com/' . $id
                        );
                        if ($item) {
                            wp_send_json(array('status' => true));
                        }
                    }
                }
                break;
        }

        wp_send_json(array('status' => false, 'msg' => esc_html__('Sorry, video not found', 'wp-load-gallery')));
    }

    public function serverFolderAdd()
    {
        if (empty($_POST['wplg_nonce'])
            || !wp_verify_nonce($_POST['wplg_nonce'], 'wplg_nonce')) {
            die();
        }

        if (empty($_POST['gallery_id'])) {
            wp_send_json(array('status' => false, 'msg' => esc_html__('Gallery not exist!', 'wp-load-gallery')));
        }

        $gallery_id = (int)$_POST['gallery_id'];
        $gallery_server_folder = get_term_meta($gallery_id, 'wplg_gallery_server_folder', true);
        $folders_path = (isset($_POST['folders_path'])) ? explode(',', $_POST['folders_path']) : array();
        if (!empty($gallery_server_folder) && is_array($gallery_server_folder)) {
            $folders_path = array_merge($gallery_server_folder, $folders_path);
            $folders_path = array_unique($folders_path);
        }

        update_term_meta($gallery_id, 'wplg_gallery_server_folder', $folders_path);
        $folders_html = $this->getServerFolderHtml($folders_path);

        // get category item
        $categories = Helper::getGalleryCateogryItems($gallery_id);
        if (!empty($categories)) {
            $folders_html .= $this->getCategoriesHtml($categories);
        }
        wp_send_json(array('status' => true, 'folders_html' => $folders_html));
    }

    public function serverFolderRemove()
    {
        if (empty($_POST['wplg_nonce'])
            || !wp_verify_nonce($_POST['wplg_nonce'], 'wplg_nonce')) {
            die();
        }

        if (empty($_POST['gallery_id'])) {
            wp_send_json(array('status' => false, 'msg' => esc_html__('Gallery not exist!', 'wp-load-gallery')));
        }

        $gallery_id = (int)$_POST['gallery_id'];
        $folders_path = get_term_meta($gallery_id, 'wplg_gallery_server_folder', true);
        if (!empty($folders_path) && is_array($folders_path)) {
            $folder_path = $_POST['folder_path'];
            $key = array_search($folder_path, $folders_path);
            if ($key !== false) {
                unset($folders_path[$key]);
            }

            update_term_meta($gallery_id, 'wplg_gallery_server_folder', $folders_path);
            wp_send_json(array('status' => true));
        }
        wp_send_json(array('status' => false, 'msg' => esc_html__('Folder not exist!', 'wp-load-gallery')));
    }

    /**
     * Get category item in gallery
     *
     * @param array $categories List categories
     *
     * @return string
     */
    public function getCategoriesHtml($categories)
    {
        $html = '';
        foreach ($categories as $category) {
            $sources = explode('***', $category->source_id);
            $category_id = (int) $sources[0];
            if (empty($sources[1])) {
                continue;
            }
            $taxonomy = $sources[1];
            if ($category_id !== 0) {
                $cat = get_term($category_id, $taxonomy);
                if (is_wp_error($cat)) {
                    continue;
                }
                $cat_name = $cat->name;
            } else {
                switch ($taxonomy) {
                    case 'category':
                        $cat_name = esc_html__('Latest Posts', 'wp-load-gallery');
                        break;
                    case 'product_cat':
                        $cat_name = esc_html__('Latest Products', 'wp-load-gallery');
                        break;
                    default:
                        $cat_name = esc_html__('Root', 'wp-load-gallery');
                }
            }

            $html .= '<div class="wplg_folder_output_item wplg_category_output_item">';
            $html .= '<span class="list-item__start-detail">';
            switch ($taxonomy) {
                case 'category':
                    $html .= '<div class="dashicons-before dashicons-admin-post" style="color: #47A4A5"></div>';
                    break;
                case 'product_cat':
                    $html .= '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="24px" height="24px"><path fill="#a64a7b" d="M43,11H5c-2.209,0-4,1.791-4,4v16c0,2.209,1.791,4,4,4h19l8,4l-2-4h13c2.209,0,4-1.791,4-4V15 C47,12.791,45.209,11,43,11z"/><path fill="#fff" d="M40.443 19c.041 0 .132.005.277.038.342.077.559.198.82.686C41.85 20.283 42 21.007 42 21.939c0 1.398-.317 2.639-.973 3.802C40.321 27 39.805 27 39.557 27c-.041 0-.132-.005-.277-.038-.342-.077-.559-.198-.809-.666C38.158 25.722 38 24.963 38 24.043c0-1.399.314-2.63.963-3.765C39.691 19 40.218 19 40.443 19M40.443 16c-1.67 0-3.026.931-4.087 2.793C35.452 20.375 35 22.125 35 24.043c0 1.434.278 2.662.835 3.686.626 1.173 1.548 1.88 2.783 2.16C38.948 29.963 39.261 30 39.557 30c1.687 0 3.043-.931 4.087-2.793C44.548 25.606 45 23.856 45 21.939c0-1.452-.278-2.662-.835-3.668-.626-1.173-1.548-1.88-2.783-2.16C41.052 16.037 40.739 16 40.443 16L40.443 16zM28.443 19c.041 0 .132.005.268.036.333.076.571.207.829.689C29.85 20.283 30 21.007 30 21.939c0 1.398-.317 2.639-.973 3.802C28.321 27 27.805 27 27.557 27c-.041 0-.132-.005-.277-.038-.342-.077-.559-.198-.809-.666C26.158 25.722 26 24.963 26 24.043c0-1.399.314-2.63.963-3.765C27.691 19 28.218 19 28.443 19M28.443 16c-1.67 0-3.026.931-4.087 2.793C23.452 20.375 23 22.125 23 24.043c0 1.434.278 2.662.835 3.686.626 1.173 1.548 1.88 2.783 2.16C26.948 29.963 27.261 30 27.557 30c1.687 0 3.043-.931 4.087-2.793C32.548 25.606 33 23.856 33 21.939c0-1.452-.278-2.662-.835-3.668-.626-1.173-1.565-1.88-2.783-2.16C29.052 16.037 28.739 16 28.443 16L28.443 16zM18.5 32c-.421 0-.832-.178-1.123-.505-2.196-2.479-3.545-5.735-4.34-8.343-1.144 2.42-2.688 5.515-4.251 8.119-.309.515-.894.792-1.491.715-.596-.083-1.085-.513-1.242-1.093-2.212-8.127-3.007-13.95-3.039-14.194-.11-.82.466-1.575 1.286-1.686.831-.108 1.576.465 1.687 1.286.007.049.571 4.177 2.033 10.199 2.218-4.208 4.078-8.535 4.102-8.59.267-.62.919-.989 1.58-.895.668.09 1.194.615 1.285 1.283.007.052.542 3.825 2.245 7.451.719-7.166 2.873-10.839 2.982-11.021.427-.711 1.35-.941 2.058-.515.711.426.941 1.348.515 2.058C22.762 16.313 20 21.115 20 30.5c0 .623-.386 1.182-.968 1.402C18.858 31.968 18.679 32 18.5 32z"/></svg>';
                    break;
                default:
                    $html .= '<i class="material-icons-outlined">folder</i>';
            }
            $html .= '</span>';
            $html .= '<span class="list-item__text" title="' . esc_attr($cat_name) . '">
              ' . esc_html($cat_name) . '
            </span>';
            $html .= '<i class="material-icons wplg-delete-icon-folder wplg-delete-category-item" data-id="not_wp-' . esc_attr($category->id) . '"> delete_outline </i>';
            $html .= '</div>';
        }

        return $html;
    }

    /**
     * Get folder item in gallery
     *
     * @param array $folders_path List folder
     *
     * @return string
     */
    public function getServerFolderHtml($folders_path)
    {
        $html = '';
        foreach ($folders_path as $folder_path) {
            $info = pathinfo($folder_path);
            $full_path = ABSPATH . trim($folder_path, '/');
            if (!file_exists($full_path)) {
                continue;
            }

            $html .= '<div class="wplg_server_folder_item wplg_folder_output_item" data-path="' . esc_attr($folder_path) . '">';
            $html .= '<span class="list-item__start-detail">
              <i class="material-icons">folder</i>
            </span>';
            $html .= '<span class="list-item__text" title="' . esc_attr($full_path) . '">
              ' . esc_html($info['filename']) . '
            </span>';
            $html .= '<i class="material-icons wplg-delete-icon-folder wplg-delete-gallery-folder" data-path="' . esc_attr($folder_path) . '"> delete_outline </i>';
            $html .= '</div>';
        }

        return $html;
    }

    /**
     * Load server folder preview
     *
     * @return void
     */
    public function loadServerFolderPreview()
    {
        if (empty($_POST['wplg_nonce'])
            || !wp_verify_nonce($_POST['wplg_nonce'], 'wplg_nonce')) {
            die();
        }
        $path = $_POST['path'];
        if (file_exists(ABSPATH . $path)) {
            $ftp_files = glob(ABSPATH . $path . '/*');
            $html = '';
            foreach ($ftp_files as $ftp_file) {
                if (is_file($ftp_file)) {
                    $types = wp_check_filetype($ftp_file);
                    if (strpos($types['type'], 'image/') !== false) {
                        $url = str_replace(ABSPATH, site_url('/'), $ftp_file);
                        $html .= '<div class="server_folder_preview_item wplg_flex_item">';
                        $html .= '<div class="wplg-image-preview"> <div class="square_thumbnail"> <div class="img_centered">';
                        $html .= '<img src="'. esc_url($url) .'">';
                        $html .= '</div>';
                        $html .= '</div>';
                        $html .= '<div class="wplg_flex_item_text" title="'. esc_attr(basename($url)) .'">';
                        $html .= '<img src="'. esc_url(WPLG_PLUGIN_URL . '/assets/images/jpeg.png') .'">';
                        $html .= '<span>' . esc_html(basename($url)) .'</span>';
                        $html .= '</div>';
                        $html .= '</div>';
                        $html .= '</div>';
                    }
                }
            }

            wp_send_json(array('status' => true, 'html' => $html));
        }

        wp_send_json(array('status' => false));
    }

    /**
     * Load google drive library
     *
     * @return void
     */
    public function loadGoogleDrive()
    {
        if (empty($_POST['wplg_nonce'])
            || !wp_verify_nonce($_POST['wplg_nonce'], 'wplg_nonce')) {
            die();
        }
        set_time_limit(0);
        $childs     = array();
        $pageToken  = null;
        $configs = get_option('wplg_google_drive_configs');
        if (empty($configs) || empty($configs['connected'])) {
            $html = '<p style="color: #f00; font-size: 15px">'. esc_html__('Please connect google drive before use') .'</p>';
            $html .= '<a href="'. esc_url(admin_url('admin.php?page=load_gallery_cloud')) .'" style="text-decoration: none; font-size: 14px; color: #5f81ff !important;">'. esc_html__('Go to connect') .'</a>';
            wp_send_json(array('status' => true, 'html' => $html));
        }

        $folderID = (isset($_POST['folderID'])) ? $_POST['folderID'] : 'root';
        $googleDrive  = new mainGoogleDrive();
        do {
            try {
                $params = array(
                    'q'          => "'" . $folderID . "' in parents and trashed = false and (mimeType contains 'image/' or mimeType contains 'video/' or mimeType = 'application/vnd.google-apps.folder')",
                    'supportsAllDrives' => $googleDrive->isTeamDrives($configs),
                    'orderBy' => 'folder',
                    'fields' => 'nextPageToken, files(id, name, parents, mimeType, thumbnailLink)'
                );

                if ($googleDrive->isTeamDrives($configs)) {
                    $params['corpora'] = 'drive';
                    $params['driveId'] = $configs['googleBaseFolder'];
                    $params['includeItemsFromAllDrives'] = true;
                }

                if ($pageToken) {
                    $params['pageToken'] = $pageToken;
                }

                $client = $googleDrive->getClient($configs);
                $service     = new Google_Service_Drive($client);
                $files     = $service->files->listFiles($params);
                $childs    = array_merge($childs, $files->getFiles());
                $pageToken = $files->getNextPageToken();
            } catch (Exception $e) {
                print 'An error occurred: ' . esc_html($e->getMessage());
                $pageToken = null;
            }
        } while ($pageToken);

        // get html of the folders
        $html = '<div class="wplg_folder_output">';
        $back = true;
        foreach ($childs as $child) {
            if ($back) {
                $current_folder = $service->files->get($folderID, array('fields' => 'id, name, parents', 'supportsAllDrives' => $googleDrive->isTeamDrives($configs)));
                if (!empty($current_folder->parents[0])) {
                    $html .= '<div class="wplg_folder_output_item wplg_google_drive_folder_item" data-id="' . esc_attr($current_folder->parents[0]) . '"><span class="list-item__start-detail">
                  <i class="material-icons">keyboard_arrow_left</i>
                </span><span class="list-item__text">
                  ' . esc_html__('Back', 'wp-load-gallery') . '
                </span></div>';
                    $back = false;
                }
            }

            if ($child->mimeType === 'application/vnd.google-apps.folder') {
                $html .= '<div class="wplg_folder_output_item wplg_google_drive_folder_item" data-id="'. esc_attr($child->id) .'"><span class="list-item__start-detail">
                  <i class="material-icons">folder</i>
                </span><span class="list-item__text" title="'. esc_attr($child->name) .'">
                  '. esc_html($child->name) .'
                </span></div>';
            }
        }
        $html .= '</div>';

        // get html of the files
        $html .= '<div class="wplg_file_output">';
        foreach ($childs as $child) {
            if (strpos($child->mimeType, 'image/') !== false || strpos($child->mimeType, 'video/') !== false) {
                $html .= '<div class="wplg_google_drive_file_item wplg_flex_item" data-id="'. $child->id .'">';
                if (strpos($child->mimeType, 'video/') !== false) {
                    $html .= '<span class="material-icons" style="position: absolute;top: 10px;right: 10px; color: #ffffff;font-size: 30px;">play_circle_outline</span>';
                }

                $html .= '<div class="wplg_google_drive_file_img wplg_flex_item_img">';
                $html .= '<img src="'. esc_url('https://drive.google.com/thumbnail?id=' . $child->id) .'">';
                $html .= '</div>';
                $html .= '<div class="wplg_google_drive_file_text wplg_flex_item_text" title="'. esc_attr($child->name) .'">';
                if (strpos($child->mimeType, 'video/') !== false) {
                    $html .= '<img src="'. esc_url(WPLG_PLUGIN_URL . '/assets/images/mp4.png') .'">';
                } else {
                    $html .= '<img src="'. esc_url(WPLG_PLUGIN_URL . '/assets/images/jpeg.png') .'">';
                }
                $html .= '<span>' . esc_html($child->name) .'</span>';
                $html .= '</div></div>';
            }
        }
        $html .= '</div>';
        wp_send_json(array('status' => true, 'html' => $html));
    }

    /**
     * Add google drive item to Gallery
     */
    public function addGoogleDriveFileToGallery()
    {
        if (empty($_POST['wplg_nonce'])
            || !wp_verify_nonce($_POST['wplg_nonce'], 'wplg_nonce')) {
            die();
        }

        if (empty($_POST['gallery_id'])) {
            wp_send_json(array('status' => false, 'msg' => esc_html__('Gallery not exist!', 'wp-load-gallery')));
        }

        $gallery_id = (int)$_POST['gallery_id'];
        $google_file_ids = (isset($_POST['google_file_ids'])) ? explode(',', $_POST['google_file_ids']) : array();

        // public google file
        if (!empty($google_file_ids)) {
            try {
                $configs = get_option('wplg_google_drive_configs');
                $googleDrive  = new mainGoogleDrive();
                $client = $googleDrive->getClient($configs);
                $service     = new Google_Service_Drive($client);
                foreach ($google_file_ids as $google_file_id) {
                    $userPermission = new Google_Service_Drive_Permission(array(
                        'type' => 'anyone',
                        'role' => 'reader',
                    ));
                    $service->permissions->create($google_file_id, $userPermission, array('fields' => 'id', 'supportsAllDrives' => $googleDrive->isTeamDrives($configs)));
                    $google_file = $service->files->get($google_file_id, array('fields' => 'id, name, description, mimeType, thumbnailLink', 'supportsAllDrives' => $googleDrive->isTeamDrives($configs)));
                    if (strpos($google_file->mimeType, 'video/') !== false) {
                        $type = 'google_drive_video';
                    } else {
                        $type = 'google_drive_image';
                    }

                    $exist = Helper::checkGalleryItemExist($google_file_id, $gallery_id, $type);
                    if (!$exist) {
                        $infos = pathinfo($google_file->name);
                        $item = Helper::insertGalleryItem(
                            $google_file_id,
                            $gallery_id,
                            $type,
                            $infos['filename'],
                            $google_file->description,
                            '',
                            'https://drive.google.com/uc?id=' . $google_file_id
                        );
                    }
                }

                wp_send_json(array('status' => true));
            } catch (Exception $e) {
                wp_send_json(array('status' => false, 'msg' => esc_html__('Have an error when public file', 'wp-load-gallery')));
            }
        }

        wp_send_json(array('status' => true));
    }


    /**
     * Load categories by post type
     *
     * @return void
     */
    public function loadCategoriesByPostType()
    {
        if (empty($_POST['wplg_nonce'])
            || !wp_verify_nonce($_POST['wplg_nonce'], 'wplg_nonce')) {
            die();
        }

        if (empty($_POST['post_type'])) {
            wp_send_json(array('status' => false, 'msg' => esc_html__(' not exist', 'wp-load-gallery')));
        }

        $taxonomy = (isset($_POST['taxonomy'])) ? $_POST['taxonomy'] : 'category';
        if ($taxonomy === '') {
            $categories = array('attachment_terms' => array(),'terms_order' => array());
        } else {
            $categories = $this->getAllGalleries($taxonomy, esc_html__('All posts', 'wp-load-gallery'));
        }

        wp_send_json(array('status' => true, 'categories' => $categories['attachment_terms'], 'categories_order' => $categories['terms_order']));
    }

    /**
     * Load post list by category ID
     *
     * @return void
     */
    public function loadPostList()
    {
        if (empty($_POST['wplg_nonce'])
            || !wp_verify_nonce($_POST['wplg_nonce'], 'wplg_nonce')) {
            die();
        }
        set_time_limit(0);
        $post_type = (isset($_POST['post_type'])) ? $_POST['post_type'] : 'post';
        $taxonomy = (isset($_POST['taxonomy'])) ? $_POST['taxonomy'] : 'category';
        $paged = (isset($_POST['paged'])) ? (int) $_POST['paged'] : 1;
        $limit = 20;
        $offset = ($paged - 1) * $limit;
        $category_id = (isset($_POST['category_id'])) ? $_POST['category_id'] : 0;
        $args = array(
            'posts_per_page' => $limit,
            'offset' => $offset,
            'post_status' => 'publish',
            'post_type' => $post_type,
        );

        if ((int) $category_id !== 0) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => $taxonomy,
                    'field' => 'term_id',
                    'terms' => (int) $category_id,
                    'include_children' => false
                )
            );
        }
        $new_query = new WP_Query($args);
        $postLists = $new_query->get_posts();
        $html = '';
        foreach ($postLists as $postList) {
            $img = get_the_post_thumbnail_url($postList->ID);
            $html .= '<div class="wplg_post_item wplg_flex_item" data-id="'. esc_attr($postList->ID) .'">';
            $html .= '<div class="wplg_flex_item_img">';
            if (!$img) {
                $html .= '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="48px" height="48px" class="img_post_item">
                    <linearGradient id="eo9Iz~gJX5QQxF9vIcujya" x1="41.018" x2="45.176" y1="26" y2="26" gradientUnits="userSpaceOnUse">
                        <stop offset="0" stop-color="#3537b0"></stop>
                        <stop offset="1" stop-color="#4646cf"></stop>
                    </linearGradient>
                    <path fill="url(#eo9Iz~gJX5QQxF9vIcujya)" d="M43,11h-3v30h3c1.105,0,2-0.895,2-2V13C45,11.895,44.105,11,43,11z"></path>
                    <path fill="#5286ff" d="M41,39V9c0-1.105-0.895-2-2-2H5C3.895,7,3,7.895,3,9v30c0,1.105,0.895,2,2,2h38	C41.895,41,41,40.105,41,39z"></path>
                    <path fill="#fff" d="M37,17H7c-0.552,0-1-0.448-1-1v-2c0-0.552,0.448-1,1-1h30c0.552,0,1,0.448,1,1v2	C38,16.552,37.552,17,37,17z"></path>
                    <path fill="#fff" d="M19,36H7c-0.552,0-1-0.448-1-1V22c0-0.552,0.448-1,1-1h12c0.552,0,1,0.448,1,1v13	C20,35.552,19.552,36,19,36z"></path>
                    <path fill="#fff" d="M38,24H24v-2c0-0.552,0.448-1,1-1h12c0.552,0,1,0.448,1,1V24z"></path>
                    <rect width="14" height="3" x="24" y="24" fill="#e6eeff"></rect>
                    <rect width="14" height="3" x="24" y="27" fill="#ccdcff"></rect>
                    <rect width="14" height="3" x="24" y="30" fill="#b3cbff"></rect>
                    <path fill="#9abaff" d="M37,36H25c-0.552,0-1-0.448-1-1v-2h14v2C38,35.552,37.552,36,37,36z"></path>
                </svg>';
            } else {
                $html .= '<img src="'. esc_url($img) .'">';
            }
            $html .= '</div>';
            $html .= '<div class="wplg_flex_item_text" title="'. esc_attr($postList->post_title) .'">';
            $html .= '<span>' . esc_html($postList->post_title) .'</span>';
            $html .= '</div></div>';
        }

        wp_send_json(array('status' => true, 'postListHtml' => $html, 'count' => count($postLists)));
    }

    /**
     * Add posts to gallery
     *
     * @return void
     */
    public function addPostsToGallery()
    {
        if (empty($_POST['wplg_nonce'])
            || !wp_verify_nonce($_POST['wplg_nonce'], 'wplg_nonce')) {
            die();
        }

        $gallery_id = (int)$_POST['gallery_id'];
        $posts_ids = (isset($_POST['posts_ids'])) ? explode(',', $_POST['posts_ids']) : array();

        if (empty($posts_ids)) {
            wp_send_json(array('status' => false));
        }
        foreach ($posts_ids as $post_id) {
            wp_set_object_terms((int) $post_id, (int) $gallery_id, WPLG_TAXONOMY, true);
        }
        wp_send_json(array('status' => true));
    }

    /**
     * Add category to gallery
     *
     * @return void
     */
    public function addCategoryToGallery()
    {
        if (empty($_POST['wplg_nonce'])
            || !wp_verify_nonce($_POST['wplg_nonce'], 'wplg_nonce')) {
            die();
        }

        $gallery_id = (int) $_POST['gallery_id'];
        $category_id = (isset($_POST['category_id'])) ? $_POST['category_id'] : 0;
        $post_type = (isset($_POST['post_type'])) ? $_POST['post_type'] : 'post';
        if ((int) $gallery_id === 0) {
            wp_send_json(array('status' => false));
        }

        switch ($post_type) {
            case 'post':
                $taxonomy = 'category';
                break;
            case 'product':
                $taxonomy = 'product_cat';
                break;
            default:
                $taxonomy = 'category';
        }

        $source_id = $category_id . '***' . $taxonomy;
        $type = 'category';
        $exist = Helper::checkGalleryItemExist($source_id, $gallery_id, $type);
        if (!$exist) {
            $item = Helper::insertGalleryItem(
                $source_id,
                $gallery_id,
                $type
            );

            if ($item) {
                wp_send_json(array('status' => true));
            }
        }
        wp_send_json(array('status' => false));
    }

    /**
     * Add category to gallery
     *
     * @return void
     */
    public function addMediaCategories()
    {
        if (empty($_POST['wplg_nonce'])
            || !wp_verify_nonce($_POST['wplg_nonce'], 'wplg_nonce')) {
            die();
        }

        $gallery_id = (int) $_POST['gallery_id'];
        $category_id = (isset($_POST['category_id'])) ? $_POST['category_id'] : 0;
        $taxonomy = (isset($_POST['taxonomy'])) ? $_POST['taxonomy'] : '';
        if ((int) $gallery_id === 0 || $taxonomy === '') {
            wp_send_json(array('status' => false));
        }

        $source_id = $category_id . '***' . $taxonomy;
        $type = 'category';
        $exist = Helper::checkGalleryItemExist($source_id, $gallery_id, $type);
        if (!$exist) {
            $item = Helper::insertGalleryItem(
                $source_id,
                $gallery_id,
                $type
            );

            if ($item) {
                wp_send_json(array('status' => true));
            }
        }
        wp_send_json(array('status' => false));
    }

    /**
     * Upload image to gallery
     *
     * @return void
     */
    public function uploadImages()
    {
        if (empty($_POST['wplg_nonce'])
            || !wp_verify_nonce($_POST['wplg_nonce'], 'wplg_nonce')) {
            die();
        }

        if (empty($_POST['wplg_gallery_id'])) {
            wp_send_json(array('status' => false, 'msg' => esc_html__('Gallery not exist', 'wp-load-gallery')));
        }

        $gallery_id = (int) $_POST['wplg_gallery_id'];
        if (!empty($_FILES['wplg_gallery_file'])) {
            $upload_dir = wp_upload_dir();
            foreach ($_FILES['wplg_gallery_file']['name'] as $i => $file) {
                if (empty($_FILES['wplg_gallery_file']['error'][$i])) {
                    $infopath = pathinfo($_FILES['wplg_gallery_file']['name'][$i]);
                    $file = sanitize_file_name($_FILES['wplg_gallery_file']['name'][$i]);
                    $content = file_get_contents($_FILES['wplg_gallery_file']['tmp_name'][$i]);
                    $attach_id = $this->insertAttachmentMetadata(
                        $upload_dir['path'],
                        $upload_dir['url'],
                        $_FILES['wplg_gallery_file']['name'][$i],
                        $file,
                        $content,
                        $_FILES['wplg_gallery_file']['type'][$i],
                        $infopath['extension']
                    );

                    if ($attach_id) {
                        wp_set_object_terms((int)$attach_id, $gallery_id, WPLG_TAXONOMY, true);
                    }
                }
            }
            wp_send_json(array('status' => true));
        } else {
            wp_send_json(array('status' => false, 'msg' => esc_html__('File not exist', 'wp-load-gallery')));
        }
    }

    /**
     * Insert a attachment to database
     *
     * @param string $upload_path Path of file
     * @param string $upload_url  URL of file
     * @param string $file_title  Title of tile
     * @param string $file        File name
     * @param string $content     Content of file
     * @param string $mime_type   Mime type of file
     * @param string $ext         Extension of file
     *
     * @return boolean|integer|WP_Error
     */
    public function insertAttachmentMetadata($upload_path, $upload_url, $file_title, $file, $content, $mime_type, $ext)
    {
        $file = wp_unique_filename($upload_path, $file);
        $image_path = $upload_path . '/' . $file;
        $upload = file_put_contents($image_path, $content);
        $xmp_list = wp_read_image_metadata($image_path);
        $title = (isset($xmp_list['title']) && $xmp_list['title'] !== '') ? $xmp_list['title'] : str_replace('.' . $ext, '', $file_title);
        $desc = (isset($xmp_list['caption']) && $xmp_list['caption'] !== '') ? $xmp_list['caption'] : '';
        if ($upload) {
            $attachment = array(
                'guid' => $upload_url . '/' . $file,
                'post_mime_type' => $mime_type,
                'post_title' => $title,
                'post_excerpt' => $desc,
                'post_status' => 'inherit'
            );

            // Insert attachment
            $attach_id = wp_insert_attachment($attachment, $image_path);
            $attach_data = wp_generate_attachment_metadata($attach_id, $image_path);
            wp_update_attachment_metadata($attach_id, $attach_data);
            return $attach_id;
        }
        return false;
    }

    /**
     * Load media categories list
     *
     * @return void
     */
    public function loadMediaCategoriesList()
    {
        if (empty($_POST['wplg_nonce'])
            || !wp_verify_nonce($_POST['wplg_nonce'], 'wplg_nonce')) {
            die();
        }

        if (empty($_POST['taxonomy'])) {
            wp_send_json(array('status' => false));
        }

        $taxonomy = $_POST['taxonomy'];
        $dropdown_options = array(
            'show_option_none'  => esc_html__('Select a category', 'wp-load-gallery'),
            'option_none_value' => 0,
            'hide_empty'        => false,
            'echo' => 0,
            'hierarchical'      => true,
            'taxonomy'          => $taxonomy,
            'class'             => 'wplg-select wplg_media_categories_list',
            'name'              => 'wplg_media_categories_list',
            'id'                => 'wplg_media_categories_list',
        );

        $dropdown = wp_dropdown_categories($dropdown_options);
        wp_send_json(array('status' => true, 'dropdown' => $dropdown));
    }

    /**
     * Move gallery
     *
     * @return void
     */
    public function moveGallery()
    {
        if (empty($_POST['wplg_nonce'])
            || !wp_verify_nonce($_POST['wplg_nonce'], 'wplg_nonce')) {
            die();
        }

        $gallery_id = (int) $_POST['gallery_id'];
        $target_gallery_id = (isset($_POST['target_gallery_id'])) ? (int) $_POST['target_gallery_id'] : 0;
        $r = wp_update_term($gallery_id, WPLG_TAXONOMY, array('parent' => $target_gallery_id));
        if ($r instanceof WP_Error) {
            wp_send_json(array('status' => false, 'msg' => 'Error, can\'t move'));
        } else {
            // Retrieve the updated folders hierarchy
            $terms = $this->getAllGalleries(WPLG_TAXONOMY);
            wp_send_json(
                array(
                    'status'           => true,
                    'categories'       => $terms['attachment_terms'],
                    'categories_order' => $terms['terms_order']
                )
            );
        }
    }
}
