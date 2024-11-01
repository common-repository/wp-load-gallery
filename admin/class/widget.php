<?php
defined('ABSPATH') || die('No direct script access allowed!');

class wplg_widget extends WP_Widget
{
    function __construct()
    {
        parent::__construct(
            'wplg_widget',
            esc_html__('WP Load Gallery', 'wp-load-gallery'),
            array('description' => esc_html__('Show the WP Load Gallery', 'wp-load-gallery'))
        );
    }

    public function widget($args, $instance)
    {
        echo do_shortcode('[wplg_gallery gallery_id="' . (int)$instance['wplg_gallery_widget'] . '"]');
    }

    public function form($instance)
    {
        if (isset($instance['wplg_gallery_widget'])) {
            $id = $instance['wplg_gallery_widget'];
        } else {
            $id = 0;
        }
        ?>
        <p>
            <?php
            $dropdown_options = array(
                'show_option_none' => esc_html__('Select a gallery', 'wp-load-gallery'),
                'option_none_value' => 0,
                'hide_empty' => false,
                'hierarchical' => true,
                'taxonomy' => WPLG_TAXONOMY,
                'class' => 'widefat wplg-select',
                'name' => $this->get_field_name('wplg_gallery_widget'),
                'child_of' => 0,
                'id' => $this->get_field_id('wplg_gallery_widget'),
                'selected' => (int)$id
            );
            wp_dropdown_categories($dropdown_options);
            ?>
        </p>
        <?php
    }

    public function update($new_instance, $old_instance)
    {
        $instance = array();
        $instance = array_merge($instance, $new_instance);
        $instance['wplg_gallery_widget'] = (!empty($new_instance['wplg_gallery_widget'])) ? $new_instance['wplg_gallery_widget'] : 0;
        return $instance;
    }
}
