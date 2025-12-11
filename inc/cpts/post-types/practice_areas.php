<?php
/**
 * Plugin Name: CPT – Case Studies
 */

if (!defined('ABSPATH')) exit;

add_action('init', function () {
    register_extended_post_type(
        'practice_areas',
        [
            'menu_icon'       => 'dashicons-awards',
            'supports'        => ['title','editor','excerpt','thumbnail','revisions'],
            'public'          => true,
            'show_ui'         => true,
            'show_in_menu'    => true,
            'show_in_rest'    => true,
            'has_archive'     => true,
            'rewrite'         => ['slug' => 'practice_areas', 'with_front' => false],
            'menu_position'   => 23,
            'capability_type' => 'post',
            'map_meta_cap'    => true,
        ],
        [
            'singular' => 'practice-area',
            'plural'   => 'practice-areas',
            'slug'     => 'practice_areas',
        ]
    );
});
