<?php
/**
 * Flexi Block: Sectors Grid
 * - Tablet & below: simple grid (1 / 2 / 3 cols)
 * - Desktop (lg+): 33/33/33 then 50/25/25 per 6 items
 * - Integrates Metronet Reorder Posts when active
 */

$section_heading      = get_sub_field('section_heading');
$section_heading_tag  = get_sub_field('section_heading_tag') ?: 'p';

$main_heading         = get_sub_field('main_heading');
$main_heading_tag     = get_sub_field('main_heading_tag') ?: 'h2';

$background_color     = get_sub_field('background_color');
$text_color           = get_sub_field('text_color');
$underline_color      = get_sub_field('underline_color');

$posts_per_page       = (int) get_sub_field('posts_per_page') ?: 6;

$image_radius         = get_sub_field('image_radius') ?: 'rounded-none';
$tile_radius          = get_sub_field('tile_radius')  ?: 'rounded-none';

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

/**
 * Build posts array:
 * - If Metronet is active: use get_posts() with menu_order ASC (up to 50)
 * - Else: use original WP_Query with ACF posts_per_page
 */
$posts_array   = [];
$sectors_query = null;

if ($metronet_active) {
    $posts_array = get_posts([
        'orderby'        => 'menu_order',
        'order'          => 'ASC',
        'post_status'    => 'publish',
        'post_type'      => 'sectors',
        'posts_per_page' => 50,
        'suppress_filters' => false,
    ]);
} else {
    $sectors_query = new WP_Query([
        'post_type'      => 'sectors',
        'posts_per_page' => $posts_per_page,
        'post_status'    => 'publish',
        'orderby'        => 'menu_order',
        'order'          => 'ASC',
    ]);
    $posts_array = $sectors_query->posts;
}

$total      = is_array($posts_array) ? count($posts_array) : 0;
$section_id = 'sectors-grid-' . wp_rand(1000, 9999);
?>
<section
    id="<?php echo esc_attr($section_id); ?>"
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
                    <?php foreach ($posts_array as $post): ?>
                        <?php
                        $post_id  = $post->ID;
                        $title    = get_the_title($post_id);
                        $thumb_id = get_post_thumbnail_id($post_id);
                        $img_alt  = $thumb_id ? get_post_meta($thumb_id, '_wp_attachment_image_alt', true) : '';
                        $img_alt  = $img_alt ?: $title;

                        $override_link = get_sub_field('override_link');
                        $item_link     = $override_link && !empty($override_link['url'])
                            ? $override_link
                            : ['url' => get_permalink($post_id), 'title' => $title, 'target' => ''];
                        $link_url    = esc_url($item_link['url']);
                        $link_title  = esc_attr($item_link['title'] ?: $title);
                        $link_target = !empty($item_link['target']) ? ' target="'.esc_attr($item_link['target']).'" rel="noopener"' : '';
                        ?>
                        <li class="m-0 p-0 overflow-hidden bg-transparent <?php echo esc_attr($tile_radius); ?>">
                            <article class="h-full">
                                <a class="block focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-current"
                                   href="<?php echo $link_url; ?>" aria-label="<?php echo $link_title; ?>"<?php echo $link_target; ?>>
                                    <div class="relative w-full overflow-hidden <?php echo esc_attr($image_radius); ?>">
                                        <?php
                                        if ($thumb_id) {
                                            echo wp_get_attachment_image(
                                                $thumb_id,
                                                'large',
                                                false,
                                                [
                                                    'alt'     => esc_attr($img_alt),
                                                    'title'   => esc_attr(get_the_title($thumb_id) ?: $title),
                                                    'class'   => 'w-full object-cover min-h-[275px] h-[275px] sm:h-[340px] sm:min-h-[340px]',
                                                    'loading' => 'lazy',
                                                ]
                                            );
                                        }
                                        ?>
                                    </div>
                                    <div class="px-4 py-3" style="<?php echo $underline_color ? 'border-top:2px solid ' . esc_attr($underline_color) . ';' : ''; ?>">
                                        <span class="w-full relative text-[1.25rem] tracking-[2px] font-semibold font-primary text-gray text-left inline-block"><?php echo esc_html($title); ?></span>
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

                            $post    = $posts_array[$idx];
                            $post_id = $post->ID;
                            $title   = get_the_title($post_id);
                            $thumb   = get_post_thumbnail_id($post_id);
                            $img_alt = $thumb ? get_post_meta($thumb, '_wp_attachment_image_alt', true) : '';
                            $img_alt = $img_alt ?: $title;

                            $override_link = get_sub_field('override_link');
                            $item_link     = $override_link && !empty($override_link['url'])
                                ? $override_link
                                : ['url' => get_permalink($post_id), 'title' => $title, 'target' => ''];
                            $link_url    = esc_url($item_link['url']);
                            $link_title  = esc_attr($item_link['title'] ?: $title);
                            $link_target = !empty($item_link['target']) ? ' target="'.esc_attr($item_link['target']).'" rel="noopener"' : '';
                            ?>
                            <li class="m-0 p-0 overflow-hidden bg-transparent <?php echo esc_attr($tile_radius); ?>">
                                <article class="h-full">
                                    <a class="block focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-current"
                                       href="<?php echo $link_url; ?>" aria-label="<?php echo $link_title; ?>"<?php echo $link_target; ?>>
                                        <div class="relative w-full overflow-hidden <?php echo esc_attr($image_radius); ?>">
                                            <?php
                                            if ($thumb) {
                                                echo wp_get_attachment_image(
                                                    $thumb,
                                                    'large',
                                                    false,
                                                    [
                                                        'alt'     => esc_attr($img_alt),
                                                        'title'   => esc_attr(get_the_title($thumb) ?: $title),
                                                        'class'   => 'w-full h-full object-cover lg:min-h-[340px] lg:max-h-[340px]',
                                                        'loading' => 'lazy',
                                                    ]
                                                );
                                            }
                                            ?>
                                        </div>
                                        <div class="px-4 py-3" style="<?php echo $underline_color ? 'border-top:2px solid ' . esc_attr($underline_color) . ';' : ''; ?>">
                                            <h3 class="w-full relative text-[1.25rem] tracking-[2px] font-semibold font-primary text-gray text-left inline-block"><?php echo esc_html($title); ?></h3>
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
                                $post    = $posts_array[$idx];
                                $post_id = $post->ID;
                                $title   = get_the_title($post_id);
                                $thumb   = get_post_thumbnail_id($post_id);
                                $img_alt = $thumb ? get_post_meta($thumb, '_wp_attachment_image_alt', true) : '';
                                $img_alt = $img_alt ?: $title;

                                $override_link = get_sub_field('override_link');
                                $item_link     = $override_link && !empty($override_link['url'])
                                    ? $override_link
                                    : ['url' => get_permalink($post_id), 'title' => $title, 'target' => ''];
                                $link_url    = esc_url($item_link['url']);
                                $link_title  = esc_attr($item_link['title'] ?: $title);
                                $link_target = !empty($item_link['target']) ? ' target="'.esc_attr($item_link['target']).'" rel="noopener"' : '';
                                ?>
                                <article class="m-0 p-0 overflow-hidden bg-transparent <?php echo esc_attr($tile_radius); ?> w-1/2">
                                    <a class="block focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-current"
                                       href="<?php echo $link_url; ?>" aria-label="<?php echo $link_title; ?>"<?php echo $link_target; ?>>
                                        <div class="relative w-full overflow-hidden <?php echo esc_attr($image_radius); ?>">
                                            <?php
                                            if ($thumb) {
                                                echo wp_get_attachment_image(
                                                    $thumb,
                                                    'large',
                                                    false,
                                                    [
                                                        'alt'     => esc_attr($img_alt),
                                                        'title'   => esc_attr(get_the_title($thumb) ?: $title),
                                                        'class'   => 'w-full h-full object-cover lg:min-h-[340px] lg:max-h-[340px]',
                                                        'loading' => 'lazy',
                                                    ]
                                                );
                                            }
                                            ?>
                                        </div>
                                        <div class="px-4 py-3" style="<?php echo $underline_color ? 'border-top:2px solid ' . esc_attr($underline_color) . ';' : ''; ?>">
                                            <h3 class="w-full relative text-[1.25rem] tracking-[2px] font-semibold font-primary text-gray text-left inline-block"><?php echo esc_html($title); ?></h3>
                                        </div>
                                    </a>
                                </article>
                            <?php endif; ?>

                            <?php
                            // 25% + 25%
                            for ($j = 4; $j <= 5; $j++) :
                                $idx = $group_start + $j;
                                if ($idx >= $total) { break; }

                                $post    = $posts_array[$idx];
                                $post_id = $post->ID;
                                $title   = get_the_title($post_id);
                                $thumb   = get_post_thumbnail_id($post_id);
                                $img_alt = $thumb ? get_post_meta($thumb, '_wp_attachment_image_alt', true) : '';
                                $img_alt = $img_alt ?: $title;

                                $override_link = get_sub_field('override_link');
                                $item_link     = $override_link && !empty($override_link['url'])
                                    ? $override_link
                                    : ['url' => get_permalink($post_id), 'title' => $title, 'target' => ''];
                                $link_url    = esc_url($item_link['url']);
                                $link_title  = esc_attr($item_link['title'] ?: $title);
                                $link_target = !empty($item_link['target']) ? ' target="'.esc_attr($item_link['target']).'" rel="noopener"' : '';
                                ?>
                                <article class="m-0 p-0 overflow-hidden bg-transparent <?php echo esc_attr($tile_radius); ?> w-1/4">
                                    <a class="block focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-current"
                                       href="<?php echo $link_url; ?>" aria-label="<?php echo $link_title; ?>"<?php echo $link_target; ?>>
                                        <div class="relative w-full overflow-hidden <?php echo esc_attr($image_radius); ?>">
                                            <?php
                                            if ($thumb) {
                                                echo wp_get_attachment_image(
                                                    $thumb,
                                                    'large',
                                                    false,
                                                    [
                                                        'alt'     => esc_attr($img_alt),
                                                        'title'   => esc_attr(get_the_title($thumb) ?: $title),
                                                        'class'   => 'w-full h-full object-cover lg:min-h-[340px] lg:max-h-[340px]',
                                                        'loading' => 'lazy',
                                                    ]
                                                );
                                            }
                                            ?>
                                        </div>
                                        <div class="px-4 py-3" style="<?php echo $underline_color ? 'border-top:2px solid ' . esc_attr($underline_color) . ';' : ''; ?>">
                                            <h3 class="w-full relative text-[1.25rem] tracking-[2px] font-semibold font-primary text-gray text-left inline-block"><?php echo esc_html($title); ?></h3>
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
                <p class="text-lg opacity-70">No sectors found. Please add some sectors in the WordPress admin.</p>
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
