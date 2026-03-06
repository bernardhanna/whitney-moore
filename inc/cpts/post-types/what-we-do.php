<?php
/**
 * Plugin Name: CPT – What We Do
 */

if (!defined('ABSPATH')) exit;

add_action('init', function () {
    register_extended_post_type(
        'what_we_do',
        [
            'menu_icon'       => 'dashicons-portfolio',
            'supports'        => ['title','editor','excerpt','thumbnail','revisions'],
            'taxonomies'      => ['what_we_do_category'],
            'public'          => true,
            'show_ui'         => true,
            'show_in_menu'    => true,
            'show_in_rest'    => true,
            'has_archive'     => true,
            'rewrite'         => ['slug' => 'what-we-do', 'with_front' => false],
            'menu_position'   => 23,
            'capability_type' => 'post',
            'map_meta_cap'    => true,
        ],
        [
            'singular' => 'What We Do Item',
            'plural'   => 'What We Do',
            'slug'     => 'what-we-do',
        ]
    );
});

register_taxonomy(
    'what_we_do_category',
    array('what_we_do', 'team', 'sectors', 'practice_areas'),
    array(
        'labels' => array(
            'name'          => __('Categories', 'matrix-starter'),
            'singular_name' => __('Category', 'matrix-starter'),
        ),
        'public'            => true,
        'show_ui'           => true,
        'show_in_menu'      => true,
        'show_in_rest'      => true,
        'hierarchical'      => true,
        'rewrite'           => array('slug' => 'what-we-do-category', 'with_front' => false),
        'show_admin_column' => true,
    )
);

if (!function_exists('matrix_ensure_what_we_do_category_term_from_category')) {
    /**
     * Ensure a term from default "category" exists in "what_we_do_category".
     * Returns the target term_id or 0 on failure.
     */
    function matrix_ensure_what_we_do_category_term_from_category($source_term_id, &$map = []) {
        $source_term_id = (int) $source_term_id;
        if ($source_term_id <= 0) {
            return 0;
        }
        if (isset($map[$source_term_id])) {
            return (int) $map[$source_term_id];
        }

        $source = get_term($source_term_id, 'category');
        if (!$source || is_wp_error($source)) {
            return 0;
        }

        $parent_target_id = 0;
        if (!empty($source->parent)) {
            $parent_target_id = matrix_ensure_what_we_do_category_term_from_category((int) $source->parent, $map);
        }

        $existing = term_exists($source->slug, 'what_we_do_category');
        if ($existing) {
            $target_id = (int) (is_array($existing) ? $existing['term_id'] : $existing);
        } else {
            $inserted = wp_insert_term($source->name, 'what_we_do_category', [
                'slug'        => $source->slug,
                'description' => $source->description,
                'parent'      => $parent_target_id,
            ]);
            if (is_wp_error($inserted)) {
                return 0;
            }
            $target_id = (int) $inserted['term_id'];
        }

        $map[$source_term_id] = $target_id;
        return $target_id;
    }
}

if (!function_exists('matrix_sync_what_we_do_post_categories')) {
    /**
     * Sync wp "category" terms from a what_we_do post into what_we_do_category.
     */
    function matrix_sync_what_we_do_post_categories($post_id) {
        $post_id = (int) $post_id;
        if ($post_id <= 0) {
            return;
        }

        $category_term_ids = wp_get_post_terms($post_id, 'category', ['fields' => 'ids']);
        if (is_wp_error($category_term_ids)) {
            return;
        }

        $map = [];
        $target_term_ids = [];
        foreach ($category_term_ids as $category_term_id) {
            $target_id = matrix_ensure_what_we_do_category_term_from_category((int) $category_term_id, $map);
            if ($target_id > 0) {
                $target_term_ids[] = $target_id;
            }
        }

        if (!empty($target_term_ids)) {
            wp_set_post_terms($post_id, array_values(array_unique($target_term_ids)), 'what_we_do_category', false);
        }
    }
}

/**
 * One-time backfill:
 * Mirror existing what_we_do post categories into what_we_do_category.
 */
add_action('init', function () {
    if (get_option('matrix_backfilled_what_we_do_category_terms')) {
        return;
    }

    $ids = get_posts([
        'post_type'      => 'what_we_do',
        'post_status'    => 'any',
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'no_found_rows'  => true,
    ]);

    if (!empty($ids) && is_array($ids)) {
        foreach ($ids as $id) {
            matrix_sync_what_we_do_post_categories((int) $id);
        }
    }

    update_option('matrix_backfilled_what_we_do_category_terms', 1);
}, 30);

/**
 * Keep taxonomy terms in sync for future edits.
 */
add_action('save_post_what_we_do', function ($post_id) {
    if (wp_is_post_autosave($post_id) || wp_is_post_revision($post_id)) {
        return;
    }
    matrix_sync_what_we_do_post_categories((int) $post_id);
}, 20, 1);

/**
 * Import compatibility:
 * Some import files use "what-we-do" as post_type, but WP post type keys
 * only allow lowercase alphanumeric characters + underscores.
 */
add_filter('wp_import_post_data_raw', function ($postdata) {
    if (!empty($postdata['post_type']) && $postdata['post_type'] === 'what-we-do') {
        $postdata['post_type'] = 'what_we_do';
    }
    return $postdata;
}, 10, 1);

/**
 * One-time migration: move old case_study posts to what_we_do.
 */
add_action('init', function () {
    if (get_option('matrix_migrated_case_study_to_what_we_do')) {
        return;
    }

    global $wpdb;
    $updated = $wpdb->update(
        $wpdb->posts,
        ['post_type' => 'what_we_do'],
        ['post_type' => 'case_study'],
        ['%s'],
        ['%s']
    );

    // Mark migration complete even if there were no rows to update.
    if ($updated !== false) {
        update_option('matrix_migrated_case_study_to_what_we_do', 1);
    }
}, 20);
