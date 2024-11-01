<?php
/**
 * Plugin Name: WP Load Gallery
 * Plugin URI: https://wordpress.org/plugins/wp-load-gallery
 * Description: Display image and video gallery from media library, server folder, Youtube, Vimeo
 * Author: NgocCode
 * Version: 2.1.6
 * Author URI: http://www.gridgallerys.com
 * Text Domain: wp-load-gallery
 * License: GPL2
 */
// Prohibit direct script loading
defined('ABSPATH') || die('No direct script access allowed!');
if (!defined('WPLG_PLUGIN_DIR')) {
    define('WPLG_PLUGIN_DIR', plugin_dir_path(__FILE__));
}

if (!defined('WPLG_PLUGIN_URL')) {
    define('WPLG_PLUGIN_URL', plugin_dir_url(__FILE__));
}

if (!defined('WPLG_FILE')) {
    define('WPLG_FILE', __FILE__);
}

if (!defined('WPLG_DOMAIN')) {
    define('WPLG_DOMAIN', 'wp-load-gallery');
}

if (!defined('WPLG_VERSION')) {
    define('WPLG_VERSION', '2.1.5');
}

if (!defined('WPLG_TAXONOMY')) {
    define('WPLG_TAXONOMY', 'wplg-category');
}
/**
 * Sort parents before children
 * http://stackoverflow.com/questions/6377147/sort-an-array-placing-children-beneath-parents
 *
 * @param array   $objects List folder
 * @param array   $result  Result
 * @param integer $parent  Parent of folder
 * @param integer $depth   Depth of folder
 *
 * @return array           output
 */
function wpParentSort(array $objects, array &$result = array(), $parent = 0, $depth = 0)
{
    foreach ($objects as $key => $object) {
        if ((int) $object->parent === (int) $parent) {
            $object->depth = $depth;
            array_push($result, $object);
            unset($objects[$key]);
            wpParentSort($objects, $result, $object->term_id, $depth + 1);
        }
    }
    return $result;
}

if( ! function_exists("wplgSanitizeHtmlClasses") ){
    function wplgSanitizeHtmlClasses($classes, $sep = " "){
        $return = "";

        if(!is_array($classes)) {
            $classes = explode($sep, $classes);
        }

        if(!empty($classes)){
            foreach($classes as $class){
                $return .= sanitize_html_class($class) . " ";
            }
        }

        return $return;
    }
}

/* Register WPLG_TAXONOMY taxonomy */
add_action('init', 'wplgRegisterTaxonomy', 0);
/**
 * Register gallery taxonomy
 *
 * @return void
 */
function wplgRegisterTaxonomy()
{
    register_taxonomy(WPLG_TAXONOMY, 'attachment', array(
        'hierarchical' => true,
        'show_in_nav_menus' => false,
        'show_ui' => false,
        'public' => false,
        'labels' => array(
            'name' => __('WP Load Gallery Categories', 'wp-load-gallery'),
            'singular_name' => __('WP Load Gallery Category', 'wp-load-gallery'),
            'menu_name' => __('WP Load Gallery Categories', 'wp-load-gallery'),
            'all_items' => __('All WP Load Gallery Categories', 'wp-load-gallery'),
            'edit_item' => __('Edit WP Load Gallery Category', 'wp-load-gallery'),
            'view_item' => __('View WP Load Gallery Category', 'wp-load-gallery'),
            'update_item' => __('Update WP Load Gallery Category', 'wp-load-gallery'),
            'add_new_item' => __('Add New WP Load Gallery Category', 'wp-load-gallery'),
            'new_item_name' => __('New WP Load Gallery Category Name', 'wp-load-gallery'),
            'parent_item' => __('Parent WP Load Gallery Category', 'wp-load-gallery'),
            'parent_item_colon' => __('Parent WP Load Gallery Category:', 'wp-load-gallery'),
            'search_items' => __('Search WP Load Gallery Categories', 'wp-load-gallery'),
        ),
    ));
}

require_once(WPLG_PLUGIN_DIR . 'helper.php');
require_once(WPLG_PLUGIN_DIR . 'admin/class/main-admin.php');
new WpLoadGalleryAdmin;
require_once(WPLG_PLUGIN_DIR . 'admin/class/widget.php');
require_once(WPLG_PLUGIN_DIR . 'frontend/class/main-front.php');
new WpLoadGalleryFront;
