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
            'taxonomies'      => ['what_we_do_category'],
            'public'          => true,
            'show_ui'         => true,
            'show_in_menu'    => true,
            'show_in_rest'    => true,
            'has_archive'     => true,
            'rewrite'         => ['slug' => 'practice-areas', 'with_front' => false],
            'menu_position'   => 23,
            'capability_type' => 'post',
            'map_meta_cap'    => true,
        ],
        [
            'singular' => 'practice-area',
            'plural'   => 'practice-areas',
            'slug'     => 'practice-areas',
        ]
    );
});

// Back-compat: permanently redirect old underscore URLs to the hyphenated slug.
add_action('template_redirect', function () {
    $request_uri = isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '';
    if ($request_uri === '' || strpos($request_uri, '/practice_areas/') !== 0) {
        return;
    }

    $target = home_url(str_replace('/practice_areas/', '/practice-areas/', $request_uri));
    wp_safe_redirect($target, 301);
    exit;
});
