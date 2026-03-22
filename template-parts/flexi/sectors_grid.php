<?php
/**
 * Flexi Block: Sectors Grid
 * - Tablet & below: simple grid (1 / 2 / 3 cols)
 * - Desktop (lg+): 33/33/33 then 50/25/25 per 6 items
 * - Integrates Metronet Reorder Posts when active
 */

$block_context = isset($args['matrix_flexi_context']) ? (string) $args['matrix_flexi_context'] : '';

$section_heading      = get_sub_field('section_heading');
$section_heading_tag  = get_sub_field('section_heading_tag') ?: 'p';

$main_heading         = get_sub_field('main_heading');
$main_heading_tag     = get_sub_field('main_heading_tag') ?: 'h2';

$background_color     = get_sub_field('background_color');
$text_color           = get_sub_field('text_color');
$underline_color      = get_sub_field('underline_color');

$posts_per_page       = (int) get_sub_field('posts_per_page') ?: 6;
$items_source         = get_sub_field('items_source') ?: 'post_type';
$manual_items         = get_sub_field('manual_items');
$manual_cards         = get_sub_field('manual_cards');
$query_post_type      = sanitize_key((string) (get_sub_field('query_post_type') ?: 'sectors'));
$query_selected_posts = get_sub_field('query_selected_posts');
$query_categories     = (array) get_sub_field('query_categories');
$override_link        = get_sub_field('override_link');

$image_radius         = get_sub_field('image_radius') ?: 'rounded-none';
$tile_radius          = get_sub_field('tile_radius')  ?: 'rounded-none';
$default_item_description_for = static function($title) {
    $title = trim((string) $title);
    if ($title === '') {
        return 'Strategic, practical legal advice tailored to this area.';
    }
    return sprintf('Strategic, practical legal advice for %s.', $title);
};
$default_card_image_url = home_url('/wp-content/uploads/2025/12/image-2-1.png');

if ($block_context === 'why_us_fallback') {
    if (!is_string($section_heading) || trim($section_heading) === '') {
        $section_heading = 'Our sectors';
    }
    if (!is_string($main_heading) || trim($main_heading) === '') {
        $main_heading = 'Experience across the markets that matter';
    }
}

$allowed_tags = ['h1','h2','h3','h4','h5','h6','span','p'];
if (!in_array($section_heading_tag, $allowed_tags, true)) { $section_heading_tag = 'p'; }
if (!in_array($main_heading_tag,    $allowed_tags, true)) { $main_heading_tag    = 'h2'; }

// padding repeater
$padding_classes = [];
if (have_rows('padding_settings')) {
    while (have_rows('padding_settings')) {
        the_row();
        $screen_size    = get_sub_field('screen_size');
        $padding_top    = get_sub_field('padding_top');
        $padding_bottom = get_sub_field('padding_bottom');
        if ($screen_size !== '' && $padding_top !== '' && $padding_bottom !== '') {
            $padding_classes[] = "{$screen_size}:pt-[{$padding_top}rem]";
            $padding_classes[] = "{$screen_size}:pb-[{$padding_bottom}rem]";
        }
    }
}

// Detect Metronet Reorder Posts
$metronet_active = false;
if (!function_exists('is_plugin_active')) {
    @include_once ABSPATH . 'wp-admin/includes/plugin.php';
}
if (function_exists('is_plugin_active')) {
    $metronet_active = is_plugin_active('metronet-reorder-posts/metronet-reorder-posts.php');
}

// Build normalized card items
$grid_items    = [];
$sectors_query = null;
$allowed_mixed_post_types = ['sectors', 'practice_areas', 'what_we_do'];
$resolve_post_object = static function ($value) {
    if ($value instanceof WP_Post) {
        return $value;
    }

    $post_id = (int) $value;
    if ($post_id <= 0) {
        return null;
    }

    $post = get_post($post_id);
    return ($post instanceof WP_Post) ? $post : null;
};

$build_post_item = static function ($post_obj) use ($override_link) {
    if (!($post_obj instanceof WP_Post)) {
        return null;
    }

    $post_id  = (int) $post_obj->ID;
    $title    = get_the_title($post_id);
    $thumb_id = get_post_thumbnail_id($post_id);
    $img_alt  = $thumb_id ? get_post_meta($thumb_id, '_wp_attachment_image_alt', true) : '';
    $img_alt  = $img_alt ?: $title;

    $item_link = $override_link && !empty($override_link['url'])
        ? $override_link
        : ['url' => get_permalink($post_id), 'title' => $title, 'target' => ''];
    $description = has_excerpt($post_id)
        ? wp_strip_all_tags((string) get_post_field('post_excerpt', $post_id))
        : wp_trim_words(wp_strip_all_tags((string) get_post_field('post_content', $post_id)), 18);

    return [
        'title'       => $title,
        'description' => $description,
        'image_id'    => $thumb_id ? (int) $thumb_id : 0,
        'image_url'   => '',
        'image_alt'   => $img_alt ?: $title,
        'link_url'    => (string) ($item_link['url'] ?? ''),
        'link_title'  => (string) ($item_link['title'] ?: $title),
        'link_target' => (string) ($item_link['target'] ?? ''),
    ];
};

if ($items_source === 'manual_cards') {
    $manual_cards = is_array($manual_cards) ? $manual_cards : [];
    foreach ($manual_cards as $row) {
        $title = isset($row['title']) ? trim((string) $row['title']) : '';
        $link  = isset($row['link']) && is_array($row['link']) ? $row['link'] : [];
        $image = isset($row['image']) && is_array($row['image']) ? $row['image'] : [];

        $link_url = isset($link['url']) ? (string) $link['url'] : '';
        if ($title === '' && !empty($link['title'])) {
            $title = (string) $link['title'];
        }
        if ($title === '') {
            continue;
        }

        $grid_items[] = [
            'title'       => $title,
            'description' => '',
            'image_id'    => !empty($image['ID']) ? (int) $image['ID'] : 0,
            'image_url'   => !empty($image['url']) ? (string) $image['url'] : '',
            'image_alt'   => !empty($image['alt']) ? (string) $image['alt'] : $title,
            'link_url'    => $link_url,
            'link_title'  => !empty($link['title']) ? (string) $link['title'] : $title,
            'link_target' => !empty($link['target']) ? (string) $link['target'] : '',
        ];
    }
} elseif ($items_source === 'manual') {
    $manual_items = is_array($manual_items) ? $manual_items : [];
    foreach ($manual_items as $item) {
        $item = $resolve_post_object($item);
        if (!($item instanceof WP_Post)) {
            continue;
        }
        if (!in_array($item->post_type, $allowed_mixed_post_types, true)) {
            continue;
        }
        $normalized = $build_post_item($item);
        if ($normalized) {
            $grid_items[] = $normalized;
        }
    }
} else {
    if (!in_array($query_post_type, $allowed_mixed_post_types, true)) {
        $query_post_type = 'sectors';
    }

    $query_selected_posts = is_array($query_selected_posts) ? $query_selected_posts : [];
    $selected_ids = [];
    foreach ($query_selected_posts as $selected_post) {
        $selected_post = $resolve_post_object($selected_post);
        if (!($selected_post instanceof WP_Post)) {
            continue;
        }
        if ($selected_post->post_type !== $query_post_type) {
            continue;
        }
        $selected_ids[] = (int) $selected_post->ID;
    }
    $selected_ids = array_values(array_unique(array_filter($selected_ids)));

    $query_args = [
        'post_type'      => $query_post_type,
        'posts_per_page' => $posts_per_page,
        'post_status'    => 'publish',
        'orderby'        => 'menu_order',
        'order'          => 'ASC',
    ];

    if (!empty($selected_ids)) {
        $query_args['post__in'] = $selected_ids;
        $query_args['orderby'] = 'post__in';
        $query_args['posts_per_page'] = count($selected_ids);
    }

    $query_categories = array_values(array_filter(array_map('intval', $query_categories)));
    if (!empty($query_categories)) {
        $query_args['tax_query'] = [[
            'taxonomy' => 'what_we_do_category',
            'field'    => 'term_id',
            'terms'    => $query_categories,
        ]];
    }

    if ($metronet_active && $query_post_type === 'sectors' && empty($query_args['tax_query']) && empty($selected_ids)) {
        $posts_array = get_posts([
            'orderby'          => 'menu_order',
            'order'            => 'ASC',
            'post_status'      => 'publish',
            'post_type'        => 'sectors',
            'posts_per_page'   => max(50, $posts_per_page),
            'suppress_filters' => false,
        ]);
    } else {
        $sectors_query = new WP_Query($query_args);
        $posts_array = $sectors_query->posts;
    }

    if (!empty($posts_array) && is_array($posts_array)) {
        foreach ($posts_array as $post_obj) {
            $normalized = $build_post_item($post_obj);
            if ($normalized) {
                $grid_items[] = $normalized;
            }
        }
    }
}

$section_id = 'sectors-grid-' . wp_rand(1000, 9999);
$layout_name = get_row_layout();
if (!is_string($layout_name) || $layout_name === '') {
    $layout_name = 'sectors_grid';
}
$row_index = get_row_index();
if (!is_numeric($row_index)) {
    $row_index = 0;
}

$total = is_array($grid_items) ? count($grid_items) : 0;
?>
<section
    id="<?php echo esc_attr($section_id); ?>" data-matrix-block="<?php echo esc_attr(str_replace('_', '-', $layout_name) . '-' . $row_index); ?>" 
    class="flex overflow-hidden relative"
    style="<?php
        echo $background_color ? 'background-color:' . esc_attr($background_color) . ';' : '';
        echo $text_color ? ' color:' . esc_attr($text_color) . ';' : '';
    ?>"
    aria-labelledby="<?php echo esc_attr($section_id); ?>-heading"
>
    <div class="flex flex-col items-center w-full mx-auto max-w-container pt-10 pb-5 md:py-12 max-xxl:px-[1rem] <?php echo esc_attr(implode(' ', $padding_classes)); ?>">

        <header class="flex flex-col justify-center items-center w-full text-center">
            <?php if (!empty($section_heading)) : ?>
                <<?php echo esc_attr($section_heading_tag); ?> class="text-[18px] tracking-wide uppercase opacity-80">
                    <?php echo esc_html($section_heading); ?>
                </<?php echo esc_attr($section_heading_tag); ?>>
            <?php endif; ?>

            <?php if (!empty($main_heading)) : ?>
                <<?php echo esc_attr($main_heading_tag); ?>
                    id="<?php echo esc_attr($section_id); ?>-heading"
                    class="mt-2.5 relative text-[2rem] tracking-[1px] leading-[2.5rem] font-primary text-primary text-center font-bold"
                >
                    <?php echo esc_html($main_heading); ?>
                </<?php echo esc_attr($main_heading_tag); ?>>
            <?php endif; ?>
        </header>

        <?php if ($total > 0) : ?>

            <!-- Simple GRID for mobile/tablet: 1 / 2 / 3 cols -->
            <div class="mt-10 w-full max-md:mt-8 lg:hidden">
                <ul role="list" class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-2">
                    <?php foreach ($grid_items as $item): ?>
                        <?php
                        $link_target = !empty($item['link_target']) ? ' target="'.esc_attr($item['link_target']).'" rel="noopener"' : '';
                        ?>
                        <li class="m-0 p-0 overflow-hidden bg-transparent <?php echo esc_attr($tile_radius); ?>">
                            <article class="h-full">
                                <a class="block relative group focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-current"
                                   href="<?php echo esc_url($item['link_url'] ?: '#'); ?>" aria-label="<?php echo esc_attr($item['link_title']); ?>"<?php echo $link_target; ?>>
                                    <div class="relative w-full overflow-hidden <?php echo esc_attr($image_radius); ?>">
                                        <?php
                                        if (!empty($item['image_id'])) {
                                            echo wp_get_attachment_image(
                                                (int) $item['image_id'],
                                                'large',
                                                false,
                                                [
                                                    'alt'     => esc_attr($item['image_alt']),
                                                    'class'   => 'w-full object-cover min-h-[275px] h-[275px] sm:h-[340px] sm:min-h-[340px] transition-transform duration-500 ease-out lg:group-hover:scale-105 lg:group-focus-visible:scale-105',
                                                    'loading' => 'lazy',
                                                ]
                                            );
                                        } elseif (!empty($item['image_url'])) {
                                            ?>
                                            <img
                                                src="<?php echo esc_url($item['image_url']); ?>"
                                                alt="<?php echo esc_attr($item['image_alt']); ?>"
                                                class="w-full object-cover min-h-[275px] h-[275px] sm:h-[340px] sm:min-h-[340px] transition-transform duration-500 ease-out lg:group-hover:scale-105 lg:group-focus-visible:scale-105"
                                                loading="lazy"
                                                decoding="async"
                                            >
                                            <?php
                                        } else {
                                            ?>
                                            <img
                                                src="<?php echo esc_url($default_card_image_url); ?>"
                                                alt="<?php echo esc_attr($item['image_alt'] ?: $item['title']); ?>"
                                                class="w-full object-cover min-h-[275px] h-[275px] sm:h-[340px] sm:min-h-[340px] transition-transform duration-500 ease-out lg:group-hover:scale-105 lg:group-focus-visible:scale-105"
                                                loading="lazy"
                                                decoding="async"
                                            >
                                            <?php
                                        }
                                        ?>
                                    </div>
                                    <div class="relative overflow-hidden px-4 py-3 max-lg:relative max-lg:bg-transparent lg:absolute lg:left-0 lg:right-0 lg:bottom-0 lg:z-20 lg:translate-y-[calc(100%-3.2rem)] lg:transition-all lg:duration-500 lg:ease-out lg:group-hover:translate-y-0 lg:group-focus-visible:translate-y-0 lg:group-hover:bg-[#F5F5F5] lg:group-focus-visible:bg-[#F5F5F5]" style="<?php echo $underline_color ?>">
                                        <span class="w-full relative text-[1.25rem] tracking-[2px] font-semibold font-primary text-gray text-left inline-block max-md:text-[18px] max-md:not-italic max-md:font-semibold max-md:leading-[24px] max-md:tracking-[2px] max-md:whitespace-pre-wrap max-md:text-[#0902A4] lg:transition-colors lg:duration-300 lg:group-hover:text-[#0902A4] lg:group-focus-visible:text-[#0902A4]"><?php echo esc_html($item['title']); ?></span>
                                        <p class="mt-2 text-[14px] leading-[1.35rem] text-black/80 transition-all duration-300 ease-out max-lg:opacity-100 lg:opacity-0 lg:max-h-0 lg:overflow-hidden lg:group-hover:opacity-100 lg:group-hover:max-h-[4.25rem] lg:group-focus-visible:opacity-100 lg:group-focus-visible:max-h-[4.25rem]">
                                            <?php echo esc_html(!empty($item['description']) ? $item['description'] : $default_item_description_for($item['title'] ?? '')); ?>
                                        </p>
                                    </div>
                                </a>
                            </article>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Desktop (lg+): 33/33/33 then 50/25/25 per 6 -->
            <div class="hidden mt-10 w-full max-md:mt-8 lg:block">
                <?php
                $group_start = 0;
                while ($group_start < $total) :
                ?>
                    <!-- Row 1: three equal -->
                    <ul role="list" class="grid grid-cols-3 gap-6">
                        <?php
                        for ($i = 0; $i < 3; $i++) {
                            $idx = $group_start + $i;
                            if ($idx >= $total) { break; }

                            $item = $grid_items[$idx];
                            $link_target = !empty($item['link_target']) ? ' target="'.esc_attr($item['link_target']).'" rel="noopener"' : '';
                            ?>
                            <li class="m-0 p-0 overflow-hidden bg-transparent <?php echo esc_attr($tile_radius); ?>">
                                <article class="h-full">
                                    <a class="block relative group focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-current"
                                       href="<?php echo esc_url($item['link_url'] ?: '#'); ?>" aria-label="<?php echo esc_attr($item['link_title']); ?>"<?php echo $link_target; ?>>
                                        <div class="relative w-full overflow-hidden <?php echo esc_attr($image_radius); ?>">
                                            <?php
                                            if (!empty($item['image_id'])) {
                                                echo wp_get_attachment_image(
                                                    (int) $item['image_id'],
                                                    'large',
                                                    false,
                                                    [
                                                        'alt'     => esc_attr($item['image_alt']),
                                                        'class'   => 'w-full h-full object-cover lg:min-h-[340px] lg:max-h-[340px] transition-transform duration-500 ease-out lg:group-hover:scale-105 lg:group-focus-visible:scale-105',
                                                        'loading' => 'lazy',
                                                    ]
                                                );
                                            } elseif (!empty($item['image_url'])) {
                                                ?>
                                                <img
                                                    src="<?php echo esc_url($item['image_url']); ?>"
                                                    alt="<?php echo esc_attr($item['image_alt']); ?>"
                                                    class="w-full h-full object-cover lg:min-h-[340px] lg:max-h-[340px] transition-transform duration-500 ease-out lg:group-hover:scale-105 lg:group-focus-visible:scale-105"
                                                    loading="lazy"
                                                    decoding="async"
                                                >
                                                <?php
                                            } else {
                                                ?>
                                                <img
                                                    src="<?php echo esc_url($default_card_image_url); ?>"
                                                    alt="<?php echo esc_attr($item['image_alt'] ?: $item['title']); ?>"
                                                    class="w-full h-full object-cover lg:min-h-[340px] lg:max-h-[340px] transition-transform duration-500 ease-out lg:group-hover:scale-105 lg:group-focus-visible:scale-105"
                                                    loading="lazy"
                                                    decoding="async"
                                                >
                                                <?php
                                            }
                                            ?>
                                        </div>
                                        <div class="bg-[#F5F5F5] relative overflow-hidden px-4 py-3 max-lg:relative max-lg:bg-transparent lg:absolute lg:left-0 lg:right-0 lg:bottom-0 lg:z-20 lg:translate-y-[calc(100%-3.2rem)] lg:transition-all lg:duration-500 lg:ease-out lg:group-hover:translate-y-0 lg:group-focus-visible:translate-y-0 lg:group-hover:bg-[#F5F5F5] lg:group-focus-visible:bg-[#F5F5F5]">
                                            <h3 class="w-full relative text-[1.25rem] tracking-[2px] font-semibold font-primary text-gray text-left inline-block lg:transition-colors lg:duration-300 lg:group-hover:text-[#0902A4] lg:group-focus-visible:text-[#0902A4]"><?php echo esc_html($item['title']); ?></h3>
                                            <p class="mt-2 text-[14px] leading-[1.35rem] text-black/80 transition-all duration-300 ease-out max-lg:opacity-100 lg:opacity-0 lg:max-h-0 lg:overflow-hidden lg:group-hover:opacity-100 lg:group-hover:max-h-[4.25rem] lg:group-focus-visible:opacity-100 lg:group-focus-visible:max-h-[4.25rem]">
                                                <?php echo esc_html(!empty($item['description']) ? $item['description'] : $default_item_description_for($item['title'] ?? '')); ?>
                                            </p>
                                        </div>
                                    </a>
                                </article>
                            </li>
                        <?php } ?>
                    </ul>

                    <?php if ($group_start + 3 < $total) : ?>
                        <!-- Row 2: 50% + 25% + 25% -->
                        <div class="flex gap-6 mt-6">
                            <?php
                            // 50%
                            $idx = $group_start + 3;
                            if ($idx < $total) :
                                $item = $grid_items[$idx];
                                $link_target = !empty($item['link_target']) ? ' target="'.esc_attr($item['link_target']).'" rel="noopener"' : '';
                                ?>
                                <article class="m-0 p-0 overflow-hidden bg-transparent <?php echo esc_attr($tile_radius); ?> w-1/2">
                                    <a class="block relative group focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-current"
                                       href="<?php echo esc_url($item['link_url'] ?: '#'); ?>" aria-label="<?php echo esc_attr($item['link_title']); ?>"<?php echo $link_target; ?>>
                                        <div class="relative w-full overflow-hidden <?php echo esc_attr($image_radius); ?>">
                                            <?php
                                            if (!empty($item['image_id'])) {
                                                echo wp_get_attachment_image(
                                                    (int) $item['image_id'],
                                                    'large',
                                                    false,
                                                    [
                                                        'alt'     => esc_attr($item['image_alt']),
                                                        'class'   => 'w-full h-full object-cover lg:min-h-[340px] lg:max-h-[340px] transition-transform duration-500 ease-out lg:group-hover:scale-105 lg:group-focus-visible:scale-105',
                                                        'loading' => 'lazy',
                                                    ]
                                                );
                                            } elseif (!empty($item['image_url'])) {
                                                ?>
                                                <img
                                                    src="<?php echo esc_url($item['image_url']); ?>"
                                                    alt="<?php echo esc_attr($item['image_alt']); ?>"
                                                    class="w-full h-full object-cover lg:min-h-[340px] lg:max-h-[340px] transition-transform duration-500 ease-out lg:group-hover:scale-105 lg:group-focus-visible:scale-105"
                                                    loading="lazy"
                                                    decoding="async"
                                                >
                                                <?php
                                            } else {
                                                ?>
                                                <img
                                                    src="<?php echo esc_url($default_card_image_url); ?>"
                                                    alt="<?php echo esc_attr($item['image_alt'] ?: $item['title']); ?>"
                                                    class="w-full h-full object-cover lg:min-h-[340px] lg:max-h-[340px] transition-transform duration-500 ease-out lg:group-hover:scale-105 lg:group-focus-visible:scale-105"
                                                    loading="lazy"
                                                    decoding="async"
                                                >
                                                <?php
                                            }
                                            ?>
                                        </div>
                                        <div class="bg-[#F5F5F5] relative overflow-hidden px-4 py-3 max-lg:relative max-lg:bg-transparent lg:absolute lg:left-0 lg:right-0 lg:bottom-0 lg:z-20 lg:translate-y-[calc(100%-3.2rem)] lg:transition-all lg:duration-500 lg:ease-out lg:group-hover:translate-y-0 lg:group-focus-visible:translate-y-0 lg:group-hover:bg-[#F5F5F5] lg:group-focus-visible:bg-[#F5F5F5]" style="<?php echo $underline_color; ?>">
                                            <h3 class="w-full relative text-[1.25rem] tracking-[2px] font-semibold font-primary text-gray text-left inline-block lg:transition-colors lg:duration-300 lg:group-hover:text-[#0902A4] lg:group-focus-visible:text-[#0902A4]"><?php echo esc_html($item['title']); ?></h3>
                                            <p class="mt-2 text-[14px] leading-[1.35rem] text-black/80 transition-all duration-300 ease-out max-lg:opacity-100 lg:opacity-0 lg:max-h-0 lg:overflow-hidden lg:group-hover:opacity-100 lg:group-hover:max-h-[4.25rem] lg:group-focus-visible:opacity-100 lg:group-focus-visible:max-h-[4.25rem]">
                                                <?php echo esc_html(!empty($item['description']) ? $item['description'] : $default_item_description_for($item['title'] ?? '')); ?>
                                            </p>
                                        </div>
                                    </a>
                                </article>
                            <?php endif; ?>

                            <?php
                            // 25% + 25%
                            for ($j = 4; $j <= 5; $j++) :
                                $idx = $group_start + $j;
                                if ($idx >= $total) { break; }

                                $item = $grid_items[$idx];
                                $link_target = !empty($item['link_target']) ? ' target="'.esc_attr($item['link_target']).'" rel="noopener"' : '';
                                ?>
                                <article class="m-0 p-0 overflow-hidden bg-transparent <?php echo esc_attr($tile_radius); ?> w-1/4">
                                    <a class="block relative group focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-current"
                                       href="<?php echo esc_url($item['link_url'] ?: '#'); ?>" aria-label="<?php echo esc_attr($item['link_title']); ?>"<?php echo $link_target; ?>>
                                        <div class="relative w-full overflow-hidden <?php echo esc_attr($image_radius); ?>">
                                            <?php
                                            if (!empty($item['image_id'])) {
                                                echo wp_get_attachment_image(
                                                    (int) $item['image_id'],
                                                    'large',
                                                    false,
                                                    [
                                                        'alt'     => esc_attr($item['image_alt']),
                                                        'class'   => 'w-full h-full object-cover lg:min-h-[340px] lg:max-h-[340px] transition-transform duration-500 ease-out lg:group-hover:scale-105 lg:group-focus-visible:scale-105',
                                                        'loading' => 'lazy',
                                                    ]
                                                );
                                            } elseif (!empty($item['image_url'])) {
                                                ?>
                                                <img
                                                    src="<?php echo esc_url($item['image_url']); ?>"
                                                    alt="<?php echo esc_attr($item['image_alt']); ?>"
                                                    class="w-full h-full object-cover lg:min-h-[340px] lg:max-h-[340px] transition-transform duration-500 ease-out lg:group-hover:scale-105 lg:group-focus-visible:scale-105"
                                                    loading="lazy"
                                                    decoding="async"
                                                >
                                                <?php
                                            } else {
                                                ?>
                                                <img
                                                    src="<?php echo esc_url($default_card_image_url); ?>"
                                                    alt="<?php echo esc_attr($item['image_alt'] ?: $item['title']); ?>"
                                                    class="w-full h-full object-cover lg:min-h-[340px] lg:max-h-[340px] transition-transform duration-500 ease-out lg:group-hover:scale-105 lg:group-focus-visible:scale-105"
                                                    loading="lazy"
                                                    decoding="async"
                                                >
                                                <?php
                                            }
                                            ?>
                                        </div>
                                        <div class="bg-[#F5F5F5] relative overflow-hidden px-4 py-3 max-lg:relative max-lg:bg-transparent lg:absolute lg:left-0 lg:right-0 lg:bottom-0 lg:z-20 lg:translate-y-[calc(100%-3.2rem)] lg:transition-all lg:duration-500 lg:ease-out lg:group-hover:translate-y-0 lg:group-focus-visible:translate-y-0 lg:group-hover:bg-[#F5F5F5] lg:group-focus-visible:bg-[#F5F5F5]" style="<?php echo $underline_color ? 'border-top:2px solid ' . esc_attr($underline_color) . ';' : ''; ?>">
                                            <h3 class="w-full relative text-[1.25rem] tracking-[2px] font-semibold font-primary text-gray text-left inline-block lg:transition-colors lg:duration-300 lg:group-hover:text-[#0902A4] lg:group-focus-visible:text-[#0902A4]"><?php echo esc_html($item['title']); ?></h3>
                                            <p class="mt-2 text-[14px] leading-[1.35rem] text-black/80 transition-all duration-300 ease-out max-lg:opacity-100 lg:opacity-0 lg:max-h-0 lg:overflow-hidden lg:group-hover:opacity-100 lg:group-hover:max-h-[4.25rem] lg:group-focus-visible:opacity-100 lg:group-focus-visible:max-h-[4.25rem]">
                                                <?php echo esc_html(!empty($item['description']) ? $item['description'] : $default_item_description_for($item['title'] ?? '')); ?>
                                            </p>
                                        </div>
                                    </a>
                                </article>
                            <?php endfor; ?>
                        </div>
                    <?php endif; ?>

                <?php
                    $group_start += 6;
                endwhile;
                ?>
            </div>
        <?php else : ?>
            <div class="mt-10 w-full text-center">
                <p class="text-lg opacity-70">No items found. Please add manual items or adjust the query filters.</p>
            </div>
        <?php endif; ?>

        <?php
        // Reset only if we used WP_Query
        if ($sectors_query instanceof WP_Query) {
            wp_reset_postdata();
        }
        ?>
    </div>
</section>
