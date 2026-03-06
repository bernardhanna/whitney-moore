<?php
/**
 * Plugin Name: CPT – Case Studies
 */

if (!defined('ABSPATH')) exit;

add_action('init', function () {
    register_extended_post_type(
        'sectors',
        [
            'menu_icon'       => 'dashicons-category',
            'supports'        => ['title','editor','excerpt','thumbnail','revisions'],
            'taxonomies'      => ['what_we_do_category'],
            'public'          => true,
            'show_ui'         => true,
            'show_in_menu'    => true,
            'show_in_rest'    => true,
            'has_archive'     => true,
            'rewrite'         => ['slug' => 'sectors', 'with_front' => false],
            'menu_position'   => 23,
            'capability_type' => 'post',
            'map_meta_cap'    => true,
        ],
        [
            'singular' => 'Sector',
            'plural'   => 'Sectors',
            'slug'     => 'sectors',
        ]
    );
});
