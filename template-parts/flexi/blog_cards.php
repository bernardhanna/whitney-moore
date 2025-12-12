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

// CTAs
$small_cta      = get_sub_field('small_cta');
$small_cta_text = get_sub_field('small_cta_text') ?: 'Discover';

$big_cta      = get_sub_field('big_cta');
$big_cta_text = get_sub_field('big_cta_text') ?: 'Read more';

// ---------------------------------
// Design
// ---------------------------------
$section_bg_color   = get_sub_field('section_bg_color');
$overlay_bg_class   = get_sub_field('overlay_bg_class') ?: 'bg-[#ffffff85]';
$overlay_blur_class = get_sub_field('overlay_blur_class') ?: 'backdrop-blur-[15px]';

$text_color_class = get_sub_field('text_color_class') ?: 'text-primary';
$date_color_class = get_sub_field('date_color_class') ?: 'text-black';
$link_color_class = get_sub_field('link_color_class') ?: 'text-black/60 hover:text-black';

// ---------------------------------
// Padding (Repeater)
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

// ---------------------------------
// Helpers
// ---------------------------------
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
        if ($i < 2) {
            $left_posts[] = get_post();
        } else {
            $right_posts[] = get_post();
        }
        $i++;
    }
}
wp_reset_postdata();

// ---------------------------------
// Layout logic
// ---------------------------------
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
            <div class="max-w-[1728px] mx-auto flex flex-col <?php echo esc_attr($layout_class); ?> items-start gap-8">

                <!-- LEFT COLUMN -->
                <div class="flex flex-col gap-8 max-lg:w-full lg:flex-1">
                    <?php foreach ($left_posts as $post_obj) :
                        $pid     = $post_obj->ID;
                        $title   = get_the_title($pid);
                        $link    = get_permalink($pid);
                        $image   = get_post_thumbnail_id($pid);
                        $img_alt = $image ? (get_post_meta($image, '_wp_attachment_image_alt', true) ?: $title) : $title;
                        $cat     = _matrix_first_cat_name($pid);
                        $date    = get_the_date(get_option('date_format'), $pid);
                    ?>
                        <article class="flex max-lg:justify-center max-lg:items-center relative max-lg:h-[294px] lg:h-[332px] overflow-hidden group">
                            <?php
                            if ($image) {
                                echo wp_get_attachment_image(
                                    $image,
                                    'large',
                                    false,
                                    [
                                        'alt' => esc_attr($img_alt),
                                        'class' => 'absolute inset-0 w-full h-full object-cover',
                                        'loading' => 'lazy'
                                    ]
                                );
                            }
                            ?>
                            <div class="absolute lg:bottom-6 lg:left-6 lg:right-6 <?php echo esc_attr("$overlay_blur_class $overlay_bg_class"); ?> p-6 flex flex-col gap-6 max-lg:h-[80%] max-lg:w-[80%]">
                                <div class="flex flex-col gap-2">
                                    <?php if ($cat) : ?>
                                        <div class="<?php echo esc_attr($text_color_class); ?> text-base font-medium tracking-[1px]">
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

                                <?php
                                $href   = !empty($small_cta['url']) ? esc_url($small_cta['url']) : esc_url($link);
                                $target = !empty($small_cta['target']) ? ' target="'.esc_attr($small_cta['target']).'" rel="noopener"' : '';
                                $label  = !empty($small_cta['title']) ? esc_html($small_cta['title']) : esc_html($small_cta_text);
                                ?>
                                <a href="<?php echo $href; ?>" class="<?php echo esc_attr($link_color_class); ?> underline"<?php echo $target; ?>>
                                    <?php echo $label; ?>
                                </a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>

                <!-- RIGHT COLUMN (only if exists) -->
                <?php if ($has_right_post) : ?>
                    <div class="max-lg:w-full lg:flex-1">
                        <?php
                        $post_obj = $right_posts[0];
                        $pid     = $post_obj->ID;
                        $title   = get_the_title($pid);
                        $link    = get_permalink($pid);
                        $image   = get_post_thumbnail_id($pid);
                        $img_alt = $image ? (get_post_meta($image, '_wp_attachment_image_alt', true) ?: $title) : $title;
                        $cat     = _matrix_first_cat_name($pid);
                        $date    = get_the_date(get_option('date_format'), $pid);
                        ?>
                        <article class="flex max-lg:justify-center max-lg:items-center  relative max-lg:h-[294px] lg:h-[696px] overflow-hidden group">
                            <?php
                            if ($image) {
                                echo wp_get_attachment_image(
                                    $image,
                                    'full',
                                    false,
                                    [
                                        'alt' => esc_attr($img_alt),
                                        'class' => 'absolute inset-0 w-full h-full object-cover',
                                        'loading' => 'lazy'
                                    ]
                                );
                            }
                            ?>
                            <div class="absolute lg:bottom-6 lg:left-6 lg:right-6 <?php echo esc_attr("$overlay_blur_class $overlay_bg_class"); ?> p-6 flex flex-col gap-6 max-lg:h-[80%] max-lg:w-[80%]">
                                <div>
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

                                <?php
                                $href   = !empty($big_cta['url']) ? esc_url($big_cta['url']) : esc_url($link);
                                $target = !empty($big_cta['target']) ? ' target="'.esc_attr($big_cta['target']).'" rel="noopener"' : '';
                                $label  = !empty($big_cta['title']) ? esc_html($big_cta['title']) : esc_html($big_cta_text);
                                ?>
                                <a href="<?php echo $href; ?>" class="<?php echo esc_attr($link_color_class); ?> underline"<?php echo $target; ?>>
                                    <?php echo $label; ?>
                                </a>
                            </div>
                        </article>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</section>
