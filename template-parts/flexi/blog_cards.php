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

// Always enforce minimum of 3 posts
$posts_per_page = max(3, (int) get_sub_field('posts_per_page'));

// ---------------------------------
// Design
// ---------------------------------
$section_bg_color   = get_sub_field('section_bg_color');
$overlay_bg_class   = get_sub_field('overlay_bg_class') ?: 'bg-[#ffffff85]';
$overlay_blur_class = get_sub_field('overlay_blur_class') ?: 'backdrop-blur-[15px]';

$text_color_class = get_sub_field('text_color_class') ?: 'text-primary';
$date_color_class = get_sub_field('date_color_class') ?: 'text-black';

// ---------------------------------
// Padding
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
    'post_type'           => 'post',
    'post_status'         => 'publish',
    'posts_per_page'      => $posts_per_page,
    'ignore_sticky_posts' => true,
    'no_found_rows'       => true,
]);

function _matrix_first_cat_name($post_id) {
    $terms = get_the_terms($post_id, 'category');
    if (is_wp_error($terms) || empty($terms)) return '';
    return $terms[0]->name ?? '';
}

// ---------------------------------
// Map posts
// ---------------------------------
$left_posts  = [];
$right_posts = [];

if ($query->have_posts()) {
    $i = 0;
    while ($query->have_posts()) {
        $query->the_post();
        if ($i < 2) $left_posts[] = get_post();
        else        $right_posts[] = get_post();
        $i++;
    }
}
wp_reset_postdata();

$has_right_post = !empty($right_posts);
$layout_class  = $has_right_post ? 'lg:flex-row' : 'lg:flex-col';
?>

<section
    id="<?php echo esc_attr($section_id); ?>"
    class="relative flex overflow-hidden <?php echo esc_attr(implode(' ', $padding_classes)); ?>"
    style="background-color: <?php echo esc_attr($section_bg_color); ?>;"
    aria-labelledby="<?php echo esc_attr($section_id); ?>-heading"
>
    <div class="flex flex-col items-center pt-5 pb-5 lg:pb-12 mx-auto w-full max-w-container max-xxl:px-[1rem]">

        <?php if ($section_heading) : ?>
            <<?php echo esc_attr($section_heading_tag); ?>
                id="<?php echo esc_attr($section_id); ?>-heading"
                class="mb-6 text-2xl font-semibold text-center"
            >
                <?php echo esc_html($section_heading); ?>
            </<?php echo esc_attr($section_heading_tag); ?>>
        <?php endif; ?>

        <div class="px-4 py-8 w-full md:px-8 lg:px-0">
            <div class="max-w-[1728px] mx-auto flex flex-col <?php echo esc_attr($layout_class); ?> gap-8">

                <!-- LEFT COLUMN -->
                <div class="flex flex-col gap-8 lg:flex-1">
                    <?php foreach ($left_posts as $post_obj) :
                        $pid   = $post_obj->ID;
                        $link  = get_permalink($pid);
                        $title = get_the_title($pid);
                        $image = get_post_thumbnail_id($pid);
                        $cat   = _matrix_first_cat_name($pid);
                        $date  = get_the_date(get_option('date_format'), $pid);
                        $alt   = $image ? (get_post_meta($image, '_wp_attachment_image_alt', true) ?: $title) : $title;
                    ?>
                        <article class="relative h-[332px] overflow-hidden group">

                            <!-- FULL CLICKABLE LINK -->
                            <a
                                href="<?php echo esc_url($link); ?>"
                                class="absolute inset-0 z-20"
                                aria-label="<?php echo esc_attr($title); ?>"
                            ></a>

                            <?php
                            if ($image) {
                                echo wp_get_attachment_image(
                                    $image,
                                    'large',
                                    false,
                                    [
                                        'alt' => esc_attr($alt),
                                        'class' => 'absolute inset-0 w-full h-full object-cover',
                                        'loading' => 'lazy'
                                    ]
                                );
                            }
                            ?>

                            <div class="relative z-10 absolute bottom-6 left-6 right-6 <?php echo esc_attr("$overlay_blur_class $overlay_bg_class"); ?> p-6 flex flex-col gap-4">
                                <?php if ($cat) : ?>
                                    <div class="<?php echo esc_attr($text_color_class); ?> text-base font-medium tracking-wide">
                                        <?php echo esc_html($cat); ?>
                                    </div>
                                <?php endif; ?>
                                <h3 class="<?php echo esc_attr($text_color_class); ?> text-xl font-semibold leading-6">
                                    <?php echo esc_html($title); ?>
                                </h3>
                                <p class="<?php echo esc_attr($date_color_class); ?> text-lg font-medium">
                                    <?php echo esc_html($date); ?>
                                </p>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>

                <!-- RIGHT COLUMN -->
                <?php if ($has_right_post) :
                    $post = $right_posts[0];
                    $pid  = $post->ID;
                    $link = get_permalink($pid);
                    $title= get_the_title($pid);
                    $image= get_post_thumbnail_id($pid);
                    $cat  = _matrix_first_cat_name($pid);
                    $date = get_the_date(get_option('date_format'), $pid);
                    $alt  = $image ? (get_post_meta($image, '_wp_attachment_image_alt', true) ?: $title) : $title;
                ?>
                    <div class="lg:flex-1">
                        <article class="relative h-[696px] overflow-hidden group">

                            <!-- FULL CLICKABLE LINK -->
                            <a
                                href="<?php echo esc_url($link); ?>"
                                class="absolute inset-0 z-20"
                                aria-label="<?php echo esc_attr($title); ?>"
                            ></a>

                            <?php
                            if ($image) {
                                echo wp_get_attachment_image(
                                    $image,
                                    'full',
                                    false,
                                    [
                                        'alt' => esc_attr($alt),
                                        'class' => 'absolute inset-0 w-full h-full object-cover',
                                        'loading' => 'lazy'
                                    ]
                                );
                            }
                            ?>

                            <div class="relative z-10 absolute bottom-6 left-6 right-6 <?php echo esc_attr("$overlay_blur_class $overlay_bg_class"); ?> p-6 flex flex-col gap-4">
                                <?php if ($cat) : ?>
                                    <div class="<?php echo esc_attr($text_color_class); ?> text-base font-medium">
                                        <?php echo esc_html($cat); ?>
                                    </div>
                                <?php endif; ?>
                                <h3 class="<?php echo esc_attr($text_color_class); ?> text-xl font-semibold">
                                    <?php echo esc_html($title); ?>
                                </h3>
                                <p class="<?php echo esc_attr($date_color_class); ?> text-base font-medium">
                                    <?php echo esc_html($date); ?>
                                </p>
                            </div>
                        </article>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</section>
