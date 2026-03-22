<?php
$heading          = get_sub_field('heading');
$heading_tag      = get_sub_field('heading_tag');
$background_color = get_sub_field('background_color');

// Filters
$filter_type = get_sub_field('filter_type') ?: 'category';
$post_type   = get_sub_field('post_type') ?: 'post';
$ppp         = (int) (get_sub_field('posts_per_page') ?: 3);
$orderby     = get_sub_field('orderby') ?: 'date';
$order       = get_sub_field('order') ?: 'DESC';

$allowed_heading_tags = ['h1','h2','h3','h4','h5','h6','span','p'];
if (empty($heading_tag) || !in_array($heading_tag, $allowed_heading_tags, true)) {
    $heading_tag = 'h2';
}

/**
 * Resolve the best-matching WP author for a Team profile.
 */
$resolve_team_author_id = static function ($team_post_id) {
    $team_post_id = (int) $team_post_id;
    if ($team_post_id <= 0 || get_post_type($team_post_id) !== 'team') {
        return 0;
    }

    $team_slug  = sanitize_title((string) get_post_field('post_name', $team_post_id));
    $team_title = trim((string) get_the_title($team_post_id));

    if ($team_slug !== '') {
        $slug_user = get_user_by('slug', $team_slug);
        if ($slug_user instanceof WP_User) {
            return (int) $slug_user->ID;
        }

        $login_user = get_user_by('login', $team_slug);
        if ($login_user instanceof WP_User) {
            return (int) $login_user->ID;
        }
    }

    if ($team_title !== '') {
        $candidate_users = get_users([
            'search'         => $team_title,
            'search_columns' => ['display_name'],
            'number'         => 20,
            'fields'         => ['ID', 'display_name'],
        ]);

        foreach ($candidate_users as $candidate_user) {
            $candidate_name = isset($candidate_user->display_name) ? trim((string) $candidate_user->display_name) : '';
            if ($candidate_name !== '' && strcasecmp($candidate_name, $team_title) === 0) {
                return (int) $candidate_user->ID;
            }
        }
    }

    return 0;
};

/**
 * Resolve blog category IDs from the current Team member taxonomy terms.
 */
$resolve_team_category_ids = static function ($team_post_id) {
    $team_post_id = (int) $team_post_id;
    if ($team_post_id <= 0 || get_post_type($team_post_id) !== 'team') {
        return [];
    }

    $team_taxonomies = ['team_practice_area', 'team_sector', 'team_role'];
    $candidate_terms = [];

    foreach ($team_taxonomies as $team_taxonomy) {
        $terms = get_the_terms($team_post_id, $team_taxonomy);
        if (is_array($terms) && !empty($terms)) {
            foreach ($terms as $term) {
                if ($term instanceof WP_Term) {
                    $candidate_terms[] = $term;
                }
            }
        }
    }

    if (empty($candidate_terms)) {
        return [];
    }

    $category_ids = [];
    foreach ($candidate_terms as $candidate_term) {
        $category_by_slug = get_term_by('slug', (string) $candidate_term->slug, 'category');
        if ($category_by_slug instanceof WP_Term) {
            $category_ids[] = (int) $category_by_slug->term_id;
            continue;
        }

        $category_by_name = get_term_by('name', (string) $candidate_term->name, 'category');
        if ($category_by_name instanceof WP_Term) {
            $category_ids[] = (int) $category_by_name->term_id;
        }
    }

    $category_ids = array_values(array_unique(array_filter(array_map('intval', $category_ids))));
    return $category_ids;
};

/**
 * Resolve blog category IDs from Team sector taxonomy terms only.
 */
$resolve_team_sector_category_ids = static function ($team_post_id) {
    $team_post_id = (int) $team_post_id;
    if ($team_post_id <= 0 || get_post_type($team_post_id) !== 'team') {
        return [];
    }

    $sector_terms = get_the_terms($team_post_id, 'team_sector');
    if (!is_array($sector_terms) || empty($sector_terms)) {
        return [];
    }

    $category_ids = [];
    foreach ($sector_terms as $sector_term) {
        if (!$sector_term instanceof WP_Term) {
            continue;
        }

        $category_by_slug = get_term_by('slug', (string) $sector_term->slug, 'category');
        if ($category_by_slug instanceof WP_Term) {
            $category_ids[] = (int) $category_by_slug->term_id;
            continue;
        }

        $category_by_name = get_term_by('name', (string) $sector_term->name, 'category');
        if ($category_by_name instanceof WP_Term) {
            $category_ids[] = (int) $category_by_name->term_id;
        }
    }

    return array_values(array_unique(array_filter(array_map('intval', $category_ids))));
};

// Padding classes
$padding_classes = [];
if (have_rows('padding_settings')) {
    while (have_rows('padding_settings')) {
        the_row();
        $screen_size    = get_sub_field('screen_size');
        $padding_top    = get_sub_field('padding_top');
        $padding_bottom = get_sub_field('padding_bottom');
        if ($screen_size !== '' && $padding_top !== '' && $padding_top !== null) {
            $padding_classes[] = "{$screen_size}:pt-[{$padding_top}rem]";
        }
        if ($screen_size !== '' && $padding_bottom !== '' && $padding_bottom !== null) {
            $padding_classes[] = "{$screen_size}:pb-[{$padding_bottom}rem]";
        }
    }
}

// Build query
$args = [
    'post_type'           => $post_type,
    'post_status'         => 'publish',
    'posts_per_page'      => $ppp,
    'ignore_sticky_posts' => true,
    'orderby'             => $orderby,
    'order'               => $order,
];

// Avoid showing the current post when used on single
if (is_singular()) {
    $args['post__not_in'] = [ get_the_ID() ];
}

$tax_query = [];
$cat_ids = (array) get_sub_field('categories');
$cat_ids = array_filter(array_map('intval', $cat_ids));

$team_post_id = is_singular('team') ? (int) get_queried_object_id() : 0;
$team_fallback_cat_ids = [];
$team_sector_cat_ids = [];
$team_author_id = 0;
if ($team_post_id > 0) {
    $team_fallback_cat_ids = $resolve_team_category_ids($team_post_id);
    $team_sector_cat_ids = $resolve_team_sector_category_ids($team_post_id);
    $team_author_id = $resolve_team_author_id($team_post_id);
}

if (($filter_type === 'category' || $filter_type === 'category_author') && !empty($cat_ids)) {
        $tax_query[] = [
            'taxonomy' => 'category',
            'field'    => 'term_id',
            'terms'    => $cat_ids,
        ];
}
if ($filter_type === 'tag') {
    $tag_ids = (array) get_sub_field('tags');
    $tag_ids = array_filter(array_map('intval', $tag_ids));
    if (!empty($tag_ids)) {
        $tax_query[] = [
            'taxonomy' => 'post_tag',
            'field'    => 'term_id',
            'terms'    => $tag_ids,
        ];
    }
}
if (!empty($tax_query)) {
    $args['tax_query'] = $tax_query;
}

if ($filter_type === 'author' || $filter_type === 'category_author') {
    $author_ids = (array) get_sub_field('authors');
    $author_ids = array_filter(array_map('intval', $author_ids));

    // On single team pages, "Author" filtering should follow the current team member only.
    if ($team_post_id > 0) {
        if ($team_author_id > 0) {
            $author_ids = [$team_author_id];
        } else {
            // Never fall back to manually selected authors (e.g. Bernard) on team pages.
            $author_ids = [];
        }
    }

    if (!empty($author_ids)) {
        $args['author__in'] = $author_ids;
    }
}

if ($team_post_id > 0 && $team_author_id === 0 && ($filter_type === 'author' || $filter_type === 'category_author')) {
    $fallback_cat_ids = !empty($team_fallback_cat_ids) ? $team_fallback_cat_ids : $cat_ids;
    if (!empty($fallback_cat_ids)) {
        $args['tax_query'] = [
            [
                'taxonomy' => 'category',
                'field'    => 'term_id',
                'terms'    => $fallback_cat_ids,
            ],
        ];
    }
}

$articles_query = null;

if ($team_post_id > 0) {
    // Team member pages always use a fixed fallback chain:
    // 1) same author, 2) team categories, 3) team sector categories, 4) latest.
    $build_team_query_args = static function (array $base_args, array $overrides = []) {
        $query_args = $base_args;
        unset($query_args['author__in'], $query_args['tax_query']);

        foreach ($overrides as $key => $value) {
            $query_args[$key] = $value;
        }

        return $query_args;
    };

    $team_query_candidates = [];

    if ($team_author_id > 0) {
        $team_query_candidates[] = $build_team_query_args($args, [
            'author__in' => [$team_author_id],
        ]);
    }

    if (!empty($team_fallback_cat_ids)) {
        $team_query_candidates[] = $build_team_query_args($args, [
            'tax_query' => [
                [
                    'taxonomy' => 'category',
                    'field'    => 'term_id',
                    'terms'    => $team_fallback_cat_ids,
                ],
            ],
        ]);
    }

    if (!empty($team_sector_cat_ids)) {
        $team_query_candidates[] = $build_team_query_args($args, [
            'tax_query' => [
                [
                    'taxonomy' => 'category',
                    'field'    => 'term_id',
                    'terms'    => $team_sector_cat_ids,
                ],
            ],
        ]);
    }

    $team_query_candidates[] = $build_team_query_args($args);

    foreach ($team_query_candidates as $team_query_args) {
        $candidate_query = new WP_Query($team_query_args);
        if ($candidate_query->have_posts()) {
            $articles_query = $candidate_query;
            break;
        }
    }

    if (!$articles_query instanceof WP_Query) {
        $articles_query = new WP_Query($build_team_query_args($args));
    }
} else {
    $articles_query = new WP_Query($args);
}

$section_id = 'related-content-' . wp_rand(1000, 9999);
$default_related_fallback_image = home_url('/wp-content/uploads/2025/12/lawyers-meeting-with-chef-in-office-2022-02-07-21-47-35-utc-1.png');

$get_related_image_data = static function ($post_id) use ($default_related_fallback_image) {
    $post_id = (int) $post_id;
    $title   = get_the_title($post_id);
    $alt     = $title ? $title : __('Related image', 'matrix-starter');

    $thumb_id = get_post_thumbnail_id($post_id);
    if ($thumb_id) {
        $thumb_alt = get_post_meta($thumb_id, '_wp_attachment_image_alt', true);
        if (!empty($thumb_alt)) {
            $alt = $thumb_alt;
        }

        return [
            'source' => 'attachment',
            'id'     => $thumb_id,
            'url'    => '',
            'alt'    => $alt,
        ];
    }

    $content = (string) get_post_field('post_content', $post_id);
    if ($content && preg_match('/<img[^>]+src=[\'"]([^\'"]+)[\'"]/i', $content, $matches)) {
        return [
            'source' => 'url',
            'id'     => 0,
            'url'    => $matches[1],
            'alt'    => $alt,
        ];
    }

    return [
        'source' => 'url',
        'id'     => 0,
        'url'    => $default_related_fallback_image,
        'alt'    => $alt,
    ];
};
?>

<section
    id="<?php echo esc_attr($section_id); ?>" data-matrix-block="<?php echo esc_attr(str_replace('_', '-', get_row_layout()) . '-' . get_row_index()); ?>" 
    class="relative flex overflow-hidden <?php echo esc_attr(implode(' ', $padding_classes)); ?>"
    style="background-color: <?php echo esc_attr($background_color); ?>;"
    aria-labelledby="<?php echo esc_attr($section_id); ?>-heading"
>
    <div class="flex flex-col items-center pt-20 pb-24 mx-auto w-full max-xxl:px-5 max-w-[1568px] max-sm:py-10">

        <?php if (!empty($heading)) : ?>
            <header class="flex flex-col justify-center items-center w-full text-center text-primary max-md:max-w-full max-md:text-[1.625rem] max-md:font-bold max-md:leading-8 max-md:tracking-[0.0625rem]">
                <<?php echo esc_attr($heading_tag); ?>
                    id="<?php echo esc_attr($section_id); ?>-heading"
                    class="max-md:text-[26px] max-md:font-bold max-md:leading-[32px] max-md:tracking-[1px] w-full text-[2rem] font-bold leading-[2.5rem] tracking-[0.0625rem]"
                >
                    <?php echo esc_html($heading); ?>
                </<?php echo esc_attr($heading_tag); ?>>
            </header>
        <?php endif; ?>

        <?php if ($articles_query->have_posts()) : ?>
            <div class="mt-14 w-full text-base text-black max-md:mt-10 max-md:max-w-full">
                <div class="grid grid-cols-3 gap-6 w-full max-sm:grid-cols-1" role="list">
                    <?php while ($articles_query->have_posts()) : $articles_query->the_post(); ?>
                        <?php
                        $post_id = get_the_ID();

                        $image_data = $get_related_image_data($post_id);

                        $categories    = get_the_category($post_id);
                        $category_name = !empty($categories) ? $categories[0]->name : 'Uncategorized';

                        $post_date      = get_the_date('F j, Y', $post_id);
                        $post_time      = get_the_time('g:i A', $post_id);
                        $post_permalink = get_permalink($post_id);
                        $post_title     = get_the_title($post_id);

                        $meta_id = $section_id . '-article-' . $post_id . '-meta';
                        ?>
                        <article class="overflow-hidden relative w-full group" role="listitem">
                            <!-- Full-card clickable overlay (keyboard focusable) -->
                            <a
                                href="<?php echo esc_url($post_permalink); ?>"
                                class="absolute inset-0 z-10 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-600 hover:underline"
                                aria-label="<?php echo esc_attr(sprintf('Read: %s', $post_title)); ?>"
                                aria-describedby="<?php echo esc_attr($meta_id); ?>"
                            >
                                <span class="sr-only"><?php echo esc_html($post_title); ?></span>
                            </a>

                            <div class="w-full overflow-hidden max-lg:h-[240px] lg:h-[343px]">
                                <?php if ($image_data['source'] === 'attachment' && !empty($image_data['id'])) : ?>
                                    <?php echo wp_get_attachment_image($image_data['id'], 'large', false, [
                                        'alt'      => esc_attr($image_data['alt']),
                                        'title'    => esc_attr(get_the_title((int) $image_data['id'])),
                                        'class'    => 'object-cover w-full h-full block',
                                        'loading'  => 'lazy',
                                        'decoding' => 'async',
                                    ]); ?>
                                <?php else : ?>
                                    <img
                                        src="<?php echo esc_url($image_data['url']); ?>"
                                        alt="<?php echo esc_attr($image_data['alt']); ?>"
                                        class="object-cover w-full h-full block"
                                        loading="lazy"
                                        decoding="async"
                                    >
                                <?php endif; ?>
                            </div>

                            <div class="flex overflow-hidden flex-col px-4 mt-4 w-full max-sm:px-0">
                                <div class="text-lg font-medium tracking-wider max-sm:text-[14px]" aria-label="Category">
                                    <?php echo esc_html($category_name); ?>
                                </div>

                                <!-- Title is visual only; overlay anchor handles the click -->
                                <h3 class="mt-2 text-2xl font-semibold leading-7 text-primary max-sm:text-[22px]">
                                    <span class="text-primary"><?php echo esc_html($post_title); ?></span>
                                </h3>

                                <time
                                    class="mt-2 tracking-wider"
                                    datetime="<?php echo esc_attr(get_the_date('c', $post_id)); ?>"
                                    id="<?php echo esc_attr($meta_id); ?>"
                                >
                                    <?php echo esc_html($post_date); ?> | <?php echo esc_html($post_time); ?>
                                </time>

                                <!-- Read more is non-interactive (overlay link is primary target) -->
                                <div class="flex gap-2 items-center self-start mt-2 tracking-tight leading-none text-primary">
                                    <span class="flex gap-2 items-center whitespace-nowrap pointer-events-none select-none text-primary hover:opacity-50">
                                        <span class="self-stretch my-auto text-base font-normal leading-[1.375rem] tracking-[-0.013rem]">Read more</span>
                                        <svg
                                            class="object-contain self-stretch my-auto w-6 transition-colors duration-200 shrink-0"
                                            width="24"
                                            height="24"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            xmlns="http://www.w3.org/2000/svg"
                                            aria-hidden="true"
                                        >
                                            <path
                                                d="M5 12H19M19 12L12 5M19 12L12 19"
                                                stroke="currentColor"
                                                stroke-width="2"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                            />
                                        </svg>
                                    </span>
                                </div>
                            </div>
                        </article>
                    <?php endwhile; ?>
                </div>
            </div>
        <?php else : ?>
            <div class="mt-14 w-full text-center max-md:mt-10">
                <p class="text-lg text-gray-600">No articles found.</p>
            </div>
        <?php endif; ?>

    </div>
</section>

<?php wp_reset_postdata(); ?>
