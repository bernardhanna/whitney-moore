<?php
/**
 * Flexi Block: Blog Cards (2 small left + 1 big right)
 */

// ---------------------------------
// IDs
// ---------------------------------
$section_id = 'blog-cards-' . wp_rand(1000, 9999);

// ---------------------------------
// Content
// ---------------------------------
$section_heading     = get_sub_field('section_heading');
$section_heading_tag = get_sub_field('section_heading_tag') ?: 'h2';

// Header CTAs (top right buttons)
$header_primary_button   = get_sub_field('header_primary_button');
$header_secondary_button = get_sub_field('header_secondary_button');

// Always enforce minimum of 3 posts
$posts_per_page = max(3, (int) get_sub_field('posts_per_page'));

// Card CTAs (text only now; visual only – no inner <a>)
$small_cta      = get_sub_field('small_cta');
$small_cta_text = get_sub_field('small_cta_text') ?: 'Discover';

$big_cta      = get_sub_field('big_cta');
$big_cta_text = get_sub_field('big_cta_text') ?: 'Read more';

// ---------------------------------
// Design
// ---------------------------------
$section_bg_color   = get_sub_field('section_bg_color') ?: '#FFFFFF';
$overlay_bg_class   = get_sub_field('overlay_bg_class') ?: 'bg-[#ffffff85]';
$overlay_blur_class = get_sub_field('overlay_blur_class') ?: 'backdrop-blur-[15px]';
$text_color_class   = get_sub_field('text_color_class') ?: 'text-primary';
$date_color_class   = get_sub_field('date_color_class') ?: 'text-black';
$link_color_class   = get_sub_field('link_color_class') ?: 'text-black/60 hover:text-black';

// ---------------------------------
// Layout: Responsive padding (Tailwind classes)
// ---------------------------------
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

// ---------------------------------
// Query
// ---------------------------------
$query = new WP_Query([
    'post_type'      => 'post',
    'posts_per_page' => $posts_per_page,
    'post_status'    => 'publish',
]);

$left_posts  = [];
$right_posts = [];

if ($query->have_posts()) {
    $i = 0;
    while ($query->have_posts()) {
        $query->the_post();

        if ($i < 2) {
            $left_posts[] = get_the_ID();
        } elseif ($i === 2) {
            $right_posts[] = get_the_ID();
        }

        $i++;
    }
}
wp_reset_postdata();

// ---------------------------------
// Layout logic
// ---------------------------------
$has_right_post = !empty($right_posts);
$layout_class   = $has_right_post ? 'lg:flex-row' : 'lg:flex-col';
?>

<section
    id="<?php echo esc_attr($section_id); ?>"
    class="relative flex overflow-hidden py-12 sm:py-12 lg:py-16 <?php echo esc_attr(implode(' ', $padding_classes)); ?>"
    style="background-color: <?php echo esc_attr($section_bg_color); ?>;"
    aria-labelledby="<?php echo esc_attr($section_id); ?>-heading"
>

    <div class="pt-5 pb-5 lg:pb-12 mx-auto w-full max-w-container max-xxl:px-[1rem]">

        <?php
        /**
         * Layout requirement:
         * Mobile: Heading -> Grid -> Buttons (DOM order)
         * Desktop: Heading (left) + Buttons (right) on same row -> Grid below (full width)
         *
         * We implement a grid wrapper:
         * - mobile: grid-cols-1 (natural DOM order)
         * - desktop: grid-cols-2 with two rows; grid spans both columns in row 2
         */
        ?>

        <div class="grid grid-cols-1 gap-4 lg:grid-cols-2 lg:grid-rows-[auto_1fr] lg:gap-6">

            <!-- Heading (always first in DOM + top-left on desktop) -->
            <div class="lg:col-start-1 lg:row-start-1">
                <?php if ($section_heading) : ?>
                    <<?php echo esc_attr($section_heading_tag); ?>
                        id="<?php echo esc_attr($section_id); ?>-heading"
                        class="text-3xl font-bold tracking-wider leading-10 text-primary max-sm:text-2xl"
                    >
                        <?php echo esc_html($section_heading); ?>
                    </<?php echo esc_attr($section_heading_tag); ?>>
                <?php else : ?>
                    <span id="<?php echo esc_attr($section_id); ?>-heading" class="sr-only">Blog section</span>
                <?php endif; ?>
            </div>

            <!-- Grid (second in DOM; full width on desktop row 2) -->
            <div class="lg:col-span-2 lg:row-start-2">
                <div class="flex flex-col gap-4 <?php echo esc_attr($layout_class); ?>">

                    <?php if (!empty($left_posts)) : ?>
                        <div class="flex flex-col gap-4 w-full lg:w-1/2">
                            <?php foreach ($left_posts as $post_id) :
                                $title   = get_the_title($post_id);
                                $image   = get_post_thumbnail_id($post_id);
                                $link    = get_permalink($post_id);
                                $date    = get_the_date('', $post_id);
                                $type    = get_field('post_type_label', $post_id);
                                $time    = get_field('event_time', $post_id); // optional
                                $cta_lbl = $small_cta_text;

                                $override_link = (is_array($small_cta) && !empty($small_cta['url'])) ? $small_cta['url'] : $link;
                                ?>
                                <article class="relative max-lg:h-[332px] lg:h-[332px] overflow-hidden group rounded">
                                    <a
                                        href="<?php echo esc_url($override_link); ?>"
                                        class="absolute inset-0 z-10 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-600"
                                        aria-label="<?php echo esc_attr(sprintf('Read: %s', $title)); ?>"
                                    >
                                        <span class="sr-only"><?php echo esc_html($title); ?></span>
                                    </a>

                                    <?php
                                    if ($image) {
                                        echo wp_get_attachment_image(
                                            $image,
                                            'large',
                                            false,
                                            ['class' => 'absolute inset-0 w-full h-full object-cover']
                                        );
                                    }
                                    ?>

                                    <div class="absolute inset-0 transition-colors duration-200 bg-black/10 group-hover:bg-black/20"></div>

                                    <div class="absolute left-6 bottom-6 right-6 z-20 <?php echo esc_attr($overlay_bg_class); ?> <?php echo esc_attr($overlay_blur_class); ?> p-5 rounded">
                                        <?php if ($type) : ?>
                                            <p class="text-xs font-semibold tracking-widest uppercase <?php echo esc_attr($text_color_class); ?>">
                                                <?php echo esc_html($type); ?>
                                            </p>
                                        <?php endif; ?>

                                        <h3 class="mt-2 text-lg font-semibold leading-snug <?php echo esc_attr($text_color_class); ?>">
                                            <?php echo esc_html($title); ?>
                                        </h3>

                                        <?php if ($date) : ?>
                                            <p class="mt-2 text-sm <?php echo esc_attr($date_color_class); ?>">
                                                <?php echo esc_html($date); ?>
                                                <?php if (!empty($time)) : ?>
                                                    <span class="mx-1">|</span><?php echo esc_html($time); ?>
                                                <?php endif; ?>
                                            </p>
                                        <?php endif; ?>

                                        <div class="mt-4">
                                            <span class="<?php echo esc_attr($link_color_class); ?> underline pointer-events-none select-none">
                                                <?php echo esc_html($cta_lbl); ?>
                                            </span>
                                        </div>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($right_posts)) : ?>
                        <div class="w-full lg:w-1/2">
                            <?php foreach ($right_posts as $post_id) :
                                $title   = get_the_title($post_id);
                                $image   = get_post_thumbnail_id($post_id);
                                $link    = get_permalink($post_id);
                                $date    = get_the_date('', $post_id);
                                $type    = get_field('post_type_label', $post_id);
                                $time    = get_field('event_time', $post_id); // optional
                                $cta_lbl = $big_cta_text;

                                $override_link = (is_array($big_cta) && !empty($big_cta['url'])) ? $big_cta['url'] : $link;
                                ?>
                                <article class="relative max-lg:h-[332px] lg:h-[696px] overflow-hidden group rounded">
                                    <a
                                        href="<?php echo esc_url($override_link); ?>"
                                        class="absolute inset-0 z-10 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-600"
                                        aria-label="<?php echo esc_attr(sprintf('Read: %s', $title)); ?>"
                                    >
                                        <span class="sr-only"><?php echo esc_html($title); ?></span>
                                    </a>

                                    <?php
                                    if ($image) {
                                        echo wp_get_attachment_image(
                                            $image,
                                            'large',
                                            false,
                                            ['class' => 'absolute inset-0 w-full h-full object-cover']
                                        );
                                    }
                                    ?>

                                    <div class="absolute inset-0 transition-colors duration-200 bg-black/10 group-hover:bg-black/20"></div>

                                    <div class="absolute left-6 bottom-6 right-6 z-20 <?php echo esc_attr($overlay_bg_class); ?> <?php echo esc_attr($overlay_blur_class); ?> p-6 rounded">
                                        <?php if ($type) : ?>
                                            <p class="text-xs font-semibold tracking-widest uppercase <?php echo esc_attr($text_color_class); ?>">
                                                <?php echo esc_html($type); ?>
                                            </p>
                                        <?php endif; ?>

                                        <h3 class="mt-2 text-xl font-semibold leading-snug <?php echo esc_attr($text_color_class); ?>">
                                            <?php echo esc_html($title); ?>
                                        </h3>

                                        <?php if ($date) : ?>
                                            <p class="mt-2 text-sm <?php echo esc_attr($date_color_class); ?>">
                                                <?php echo esc_html($date); ?>
                                                <?php if (!empty($time)) : ?>
                                                    <span class="mx-1">|</span><?php echo esc_html($time); ?>
                                                <?php endif; ?>
                                            </p>
                                        <?php endif; ?>

                                        <div class="mt-4">
                                            <span class="<?php echo esc_attr($link_color_class); ?> underline pointer-events-none select-none">
                                                <?php echo esc_html($cta_lbl); ?>
                                            </span>
                                        </div>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                </div>
            </div>

            <!-- Buttons (third in DOM; top-right on desktop row 1) -->
            <?php if ($header_primary_button || $header_secondary_button) : ?>
                <div class="lg:col-start-2 lg:row-start-1 lg:justify-self-end">
                    <div class="flex flex-col gap-3 w-full lg:w-auto lg:flex-row lg:flex-nowrap lg:gap-4 lg:items-center lg:justify-end">
                        <?php if (is_array($header_primary_button) && !empty($header_primary_button['url']) && !empty($header_primary_button['title'])) : ?>
                            <a
                                href="<?php echo esc_url($header_primary_button['url']); ?>"
                                class="flex justify-center items-center px-8 py-4 w-full h-14 text-white whitespace-nowrap border-0 transition-colors duration-200 cursor-pointer btn bg-primary lg:w-fit hover:bg-primary-dark"
                                target="<?php echo esc_attr($header_primary_button['target'] ?? '_self'); ?>"
                                aria-label="<?php echo esc_attr($header_primary_button['title']); ?>"
                            >
                                <span class="text-xl tracking-wide leading-5 text-center max-md:text-lg max-sm:text-base max-sm:leading-5">
                                    <?php echo esc_html($header_primary_button['title']); ?>
                                </span>
                            </a>
                        <?php endif; ?>

                        <?php if (is_array($header_secondary_button) && !empty($header_secondary_button['url']) && !empty($header_secondary_button['title'])) : ?>
                            <a
                                href="<?php echo esc_url($header_secondary_button['url']); ?>"
                                class="flex justify-center items-center px-8 py-4 w-full whitespace-nowrap border border-solid transition-colors duration-200 cursor-pointer btn border-primary text-primary lg:w-fit hover:bg-primary-light hover:text-white"
                                target="<?php echo esc_attr($header_secondary_button['target'] ?? '_self'); ?>"
                                aria-label="<?php echo esc_attr($header_secondary_button['title']); ?>"
                            >
                                <span class="text-xl leading-5 max-md:text-lg max-sm:text-base max-sm:leading-5">
                                    <?php echo esc_html($header_secondary_button['title']); ?>
                                </span>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>
</section>
