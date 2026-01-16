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
if ($filter_type === 'category' || $filter_type === 'category_author') {
    $cat_ids = (array) get_sub_field('categories');
    $cat_ids = array_filter(array_map('intval', $cat_ids));
    if (!empty($cat_ids)) {
        $tax_query[] = [
            'taxonomy' => 'category',
            'field'    => 'term_id',
            'terms'    => $cat_ids,
        ];
    }
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
    if (!empty($author_ids)) {
        $args['author__in'] = $author_ids;
    }
}

$articles_query = new WP_Query($args);
$section_id = 'related-content-' . wp_rand(1000, 9999);
?>

<section
    id="<?php echo esc_attr($section_id); ?>"
    class="relative flex overflow-hidden <?php echo esc_attr(implode(' ', $padding_classes)); ?>"
    style="background-color: <?php echo esc_attr($background_color); ?>;"
    aria-labelledby="<?php echo esc_attr($section_id); ?>-heading"
>
    <div class="flex flex-col items-center px-20 pt-20 pb-24 mx-auto w-full max-w-container max-md:px-5">

        <?php if (!empty($heading)) : ?>
            <header class="flex flex-col justify-center items-center w-full text-3xl font-bold tracking-wider leading-none text-center text-primary max-md:max-w-full">
                <<?php echo esc_attr($heading_tag); ?>
                    id="<?php echo esc_attr($section_id); ?>-heading"
                    class="max-md:max-w-full"
                >
                    <?php echo esc_html($heading); ?>
                </<?php echo esc_attr($heading_tag); ?>>
            </header>
        <?php endif; ?>

        <?php if ($articles_query->have_posts()) : ?>
            <div class="mt-14 w-full text-base text-black max-md:mt-10 max-md:max-w-full">
                <div class="grid grid-cols-3 gap-6 w-full max-lg:grid-cols-2 max-sm:grid-cols-1" role="list">
                    <?php while ($articles_query->have_posts()) : $articles_query->the_post(); ?>
                        <?php
                        $post_id = get_the_ID();

                        $featured_image_id  = get_post_thumbnail_id($post_id);
                        $featured_image_alt = $featured_image_id
                            ? (get_post_meta($featured_image_id, '_wp_attachment_image_alt', true) ?: get_the_title($post_id))
                            : get_the_title($post_id);

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
                                class="absolute inset-0 z-10 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-600"
                                aria-label="<?php echo esc_attr(sprintf('Read: %s', $post_title)); ?>"
                                aria-describedby="<?php echo esc_attr($meta_id); ?>"
                            >
                                <span class="sr-only"><?php echo esc_html($post_title); ?></span>
                            </a>

                            <?php if ($featured_image_id) : ?>
                                <div class="w-full h-[340px] overflow-hidden">
                                      <?php echo wp_get_attachment_image($featured_image_id, 'large', false, [
                                        'alt'     => esc_attr($featured_image_alt),
                                        'title'   => esc_attr(get_the_title($featured_image_id)),
                                        'class'   => 'object-cover w-full h-full block',
                                        'loading' => 'lazy',
                                        'decoding'=> 'async',
                                      ]); ?>
                                </div>
                            <?php endif; ?>

                            <div class="flex overflow-hidden flex-col px-4 mt-4 w-full">
                                <div class="text-lg font-medium tracking-wider" aria-label="Category">
                                    <?php echo esc_html($category_name); ?>
                                </div>

                                <!-- Title is visual only; overlay anchor handles the click -->
                                <h3 class="mt-2 text-2xl font-semibold leading-7 text-primary">
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
                                    <span class="flex gap-2 items-center text-primary whitespace-nowrap pointer-events-none select-none">
                                        <span class="self-stretch my-auto">Read more</span>
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
