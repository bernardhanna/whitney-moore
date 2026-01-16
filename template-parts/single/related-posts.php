<?php
/**
 * Template Part: Single – Related Posts (by category, no ACF)
 * Usage in single.php: get_template_part('template-parts/single/related-posts');
 */

if (!defined('ABSPATH')) exit;

$post_id = get_the_ID();

/** Resolve category (Yoast Primary -> first category -> none) */
$cat_id = 0;
if (class_exists('WPSEO_Primary_Term')) {
    $primary    = new WPSEO_Primary_Term('category', $post_id);
    $primary_id = (int) $primary->get_primary_term();
    if ($primary_id) {
        $cat_id = $primary_id;
    }
}
if (!$cat_id) {
    $cats = get_the_category($post_id);
    if (!empty($cats) && !is_wp_error($cats)) {
        $cat_id = (int) $cats[0]->term_id;
    }
}

/** Query: 3 latest from same category (exclude current) */
$q_args = [
    'post_type'           => 'post',
    'post_status'         => 'publish',
    'posts_per_page'      => 3,
    'ignore_sticky_posts' => true,
    'orderby'             => 'date',
    'order'               => 'DESC',
    'post__not_in'        => [$post_id],
];
if ($cat_id) {
    $q_args['cat'] = $cat_id;
}

$related = new WP_Query($q_args);

// Section IDs
$section_id  = 'related-content-' . wp_rand(1000, 9999);
$heading     = __('Related articles', 'matrix-starter');
$heading_tag = 'h2';
?>

<?php if ($related->have_posts()) : ?>
<section
    id="<?php echo esc_attr($section_id); ?>"
    class="flex overflow-hidden relative bg-[#F5F5F5]"
    aria-labelledby="<?php echo esc_attr($section_id); ?>-heading"
>
    <div class="flex flex-col items-center pt-20 pb-24 mx-auto w-full max-xl:px-5 max-w-container">

        <header class="flex flex-col justify-center items-center w-full text-center text-primary max-md:max-w-full">
            <<?php echo esc_attr($heading_tag); ?>
                id="<?php echo esc_attr($section_id); ?>-heading"
                class="max-md:max-w-full text-[2rem] font-bold leading-[2.5rem] tracking-[0.0625rem] max-md:text-[1.625rem] max-md:font-bold max-md:leading-8 max-md:tracking-[0.0625rem]"
            >
                <?php echo esc_html($heading); ?>
            </<?php echo esc_attr($heading_tag); ?>>
        </header>

        <div class="mt-14 w-full text-base text-black max-md:mt-10 max-md:max-w-full">
            <div class="grid grid-cols-3 gap-6 w-full max-lg:grid-cols-2 max-sm:grid-cols-1" role="list">
                <?php while ($related->have_posts()) : $related->the_post(); ?>
                    <?php
                    $pid          = get_the_ID();
                    $permalink    = get_permalink($pid);
                    $title        = get_the_title($pid);

                    $featured_id  = get_post_thumbnail_id($pid);
                    $featured_alt = $featured_id
                        ? (get_post_meta($featured_id, '_wp_attachment_image_alt', true) ?: $title)
                        : $title;

                    $cats     = get_the_category($pid);
                    $cat_name = (!empty($cats) && !is_wp_error($cats)) ? $cats[0]->name : __('Uncategorized', 'matrix-starter');

                    $date_iso  = get_the_date('c', $pid);
                    $date_disp = get_the_date('F j, Y', $pid);
                    $time_disp = get_the_time('g:i A', $pid);

                    $meta_id = $section_id . '-article-' . $pid . '-meta';
                    ?>
                    <article class="overflow-hidden relative w-full group" role="listitem">
                        <!-- Full-card clickable overlay -->
                        <a
                            href="<?php echo esc_url($permalink); ?>"
                            class="absolute inset-0 z-10 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-600"
                            aria-label="<?php echo esc_attr(sprintf(__('Read: %s', 'matrix-starter'), $title)); ?>"
                            aria-describedby="<?php echo esc_attr($meta_id); ?>"
                        >
                            <span class="sr-only"><?php echo esc_html($title); ?></span>
                        </a>

                        <?php if ($featured_id) : ?>
                            <div class="w-full">
                                <?php echo wp_get_attachment_image($featured_id, 'large', false, [
                                    'alt'     => esc_attr($featured_alt),
                                    'title'   => esc_attr(get_the_title($featured_id)),
                                    'class'   => 'object-cover w-full h-auto',
                                    'loading' => 'lazy',
                                    'decoding'=> 'async',
                                ]); ?>
                            </div>
                        <?php endif; ?>

                        <div class="flex overflow-hidden flex-col px-4 mt-4 w-full">
                            <div class="text-lg font-medium tracking-wider" aria-label="<?php esc_attr_e('Category', 'matrix-starter'); ?>">
                                <?php echo esc_html($cat_name); ?>
                            </div>

                            <!-- Title no longer a link (overlay link handles click) -->
                            <h3 class="mt-2 text-2xl font-semibold leading-7 text-primary">
                                <span class="text-primary"><?php echo esc_html($title); ?></span>
                            </h3>

                            <time
                                class="mt-2 tracking-wider"
                                datetime="<?php echo esc_attr($date_iso); ?>"
                                id="<?php echo esc_attr($meta_id); ?>"
                            >
                                <?php echo esc_html($date_disp); ?> | <?php echo esc_html($time_disp); ?>
                            </time>

                            <!-- Read more converted to non-interactive (overlay link is primary target) -->
                            <div class="flex gap-2 items-center self-start mt-2 tracking-tight leading-none text-primary">
                                <span class="flex gap-2 items-center whitespace-nowrap pointer-events-none select-none text-primary">
                                    <span class="self-stretch my-auto"><?php esc_html_e('Read more', 'matrix-starter'); ?></span>
                                    <svg class="object-contain self-stretch my-auto w-6 transition-colors duration-200 shrink-0" width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M5 12H19M19 12L12 5M19 12L12 19"
                                              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</section>
<?php
wp_reset_postdata();
endif;
?>