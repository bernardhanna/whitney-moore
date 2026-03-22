<?php
if (!defined('ABSPATH')) {
    exit;
}

get_header();

/**
 * Theme options (ACF Options)
 * Group: blog_settings
 */
$blog_settings = get_field('blog_settings', 'option');
$hero_bg       = !empty($blog_settings['hero_background_image']) ? $blog_settings['hero_background_image'] : null;

$hero_tag      = !empty($blog_settings['hero_heading_tag']) ? $blog_settings['hero_heading_tag'] : 'h1';
$hero_heading  = !empty($blog_settings['hero_heading_text']) ? $blog_settings['hero_heading_text'] : "What's new";
$hero_kicker   = !empty($blog_settings['hero_kicker_text']) ? $blog_settings['hero_kicker_text'] : get_bloginfo('name');
$hero_sub      = !empty($blog_settings['hero_subheading_text']) ? $blog_settings['hero_subheading_text'] : 'Latest and greatest.';
$hero_body     = !empty($blog_settings['hero_body_text']) ? $blog_settings['hero_body_text'] : '';
$hero_sub_string = is_string($hero_sub) ? $hero_sub : '';
if ($hero_sub_string !== '' && stripos($hero_sub_string, 'lorem ipsum') !== false) {
    $hero_sub = 'Explore the latest legal updates, practical insights, and firm news from Whitney Moore.';
}
$visible_filter_category_ids = !empty($blog_settings['visible_filter_categories']) ? (array) $blog_settings['visible_filter_categories'] : array();
$visible_filter_category_ids = array_values(array_filter(array_map('intval', $visible_filter_category_ids)));

$allowed_heading_tags = array('h1','h2','h3','h4','h5','h6','span','p');
if (!in_array($hero_tag, $allowed_heading_tags, true)) {
    $hero_tag = 'h1';
}

// ---------- Filters (category pills + search) ----------
$blog_cat    = isset($_GET['blog_cat']) ? sanitize_text_field(wp_unslash($_GET['blog_cat'])) : 'all';
$blog_search = isset($_GET['blog_search']) ? sanitize_text_field(wp_unslash($_GET['blog_search'])) : '';

$paged = (int) get_query_var('paged');
if ($paged < 1) {
    $paged = 1;
}

/**
 * Base query args shared across queries
 */
$base_query_args = array(
    'post_type'           => 'post',
    'post_status'         => 'publish',
    'ignore_sticky_posts' => true,
);

if (!empty($blog_search)) {
    $base_query_args['s'] = $blog_search;
}

if ($blog_cat !== 'all') {
    $base_query_args['category_name'] = $blog_cat; // category slug
}

// ---------- Dynamic Filter Pills (only categories with posts) ----------
$filter_pills = array(
    array('label' => 'All articles', 'slug' => 'all'),
);

/**
 * If searching: only show categories that occur in the search results.
 * If not searching: show all non-empty categories site-wide.
 */
if (!empty($blog_search)) {

    // Build a query to fetch all matching post IDs (search only; do NOT restrict by selected category)
    $pill_posts_args = $base_query_args;
    unset($pill_posts_args['category_name']);

    $pill_posts_args['posts_per_page'] = -1;
    $pill_posts_args['fields']         = 'ids';
    $pill_posts_args['no_found_rows']  = true;

    $pill_posts_query = new WP_Query($pill_posts_args);
    $post_ids = $pill_posts_query->posts;

    if (!empty($post_ids)) {
        $cats = wp_get_object_terms($post_ids, 'category', array(
            'hide_empty' => true,
        ));

        if (!is_wp_error($cats) && !empty($cats)) {
            if (!empty($visible_filter_category_ids)) {
                $cats = array_values(array_filter($cats, static function ($cat) use ($visible_filter_category_ids) {
                    return in_array((int) $cat->term_id, $visible_filter_category_ids, true);
                }));
            }

            // Sort by name for stable UI
            usort($cats, function($a, $b){
                return strcasecmp($a->name, $b->name);
            });

            foreach ($cats as $cat) {
                $filter_pills[] = array(
                    'label' => $cat->name,
                    'slug'  => $cat->slug,
                );
            }
        }
    }

} else {

    $category_query_args = array(
        'taxonomy'   => 'category',
        'hide_empty' => true,
        'orderby'    => 'name',
        'order'      => 'ASC',
    );
    if (!empty($visible_filter_category_ids)) {
        $category_query_args['include'] = $visible_filter_category_ids;
    }

    $cats = get_terms($category_query_args);

    if (!is_wp_error($cats) && !empty($cats)) {
        foreach ($cats as $cat) {
            $filter_pills[] = array(
                'label' => $cat->name,
                'slug'  => $cat->slug,
            );
        }
    }
}

// If current blog_cat isn't valid anymore (e.g. no posts), fall back to all
$valid_slugs = wp_list_pluck($filter_pills, 'slug');
if (!in_array($blog_cat, $valid_slugs, true)) {
    $blog_cat = 'all';
    // also remove category filter from base args if we invalidated it
    if (isset($base_query_args['category_name'])) {
        unset($base_query_args['category_name']);
    }
}
$show_filter_arrows = count($filter_pills) > 5;

// ---------- Featured 3 (ONLY on page 1) ----------
$featured_query = null;
$featured_ids   = array();
$show_featured_section = ($paged === 1 && $blog_cat === 'all' && $blog_search === '');

if ($show_featured_section) {
    $featured_args = $base_query_args;
    $featured_args['posts_per_page'] = 3;
    $featured_args['paged']          = 1;

    $featured_query = new WP_Query($featured_args);

    if (!empty($featured_query->posts)) {
        $featured_ids = wp_list_pluck($featured_query->posts, 'ID');
    }
}

// ---------- Main grid (paged, excludes featured IDs) ----------
$posts_per_page = 15;

$main_args = $base_query_args;
$main_args['posts_per_page'] = $posts_per_page;
$main_args['paged']          = $paged;

if (!empty($featured_ids)) {
    $main_args['post__not_in'] = array_map('intval', $featured_ids);
}

$main_query  = new WP_Query($main_args);
$total_pages = (int) $main_query->max_num_pages;

// Hero bg fallback (if no ACF image)
$hero_bg_url = !empty($hero_bg['url']) ? $hero_bg['url'] : '';
$hero_bg_id  = !empty($hero_bg['ID']) ? (int) $hero_bg['ID'] : 0;
$hero_bg_alt = $hero_bg_id ? get_post_meta($hero_bg_id, '_wp_attachment_image_alt', true) : '';
if (empty($hero_bg_alt)) {
    $hero_bg_alt = 'Hero background image';
}

$section_id = 'hero_' . wp_generate_uuid4();

// Blog card image fallback order:
// 1) Featured image
// 2) First inline image found in post body
// 3) Hard fallback image URL
$default_blog_fallback_image = home_url('/wp-content/uploads/2025/12/lawyers-meeting-with-chef-in-office-2022-02-07-21-47-35-utc-1.png');

$get_blog_card_image_data = static function ($post_id) use ($default_blog_fallback_image) {
    $post_id = (int) $post_id;
    $title   = get_the_title($post_id);
    $alt     = $title ? $title : __('Blog image', 'matrix-starter');

    $thumb_id = get_post_thumbnail_id($post_id);
    if ($thumb_id) {
        $thumb_alt = get_post_meta($thumb_id, '_wp_attachment_image_alt', true);
        if (!empty($thumb_alt)) {
            $alt = $thumb_alt;
        }

        return array(
            'source' => 'attachment',
            'id'     => $thumb_id,
            'url'    => '',
            'alt'    => $alt,
        );
    }

    $content = (string) get_post_field('post_content', $post_id);
    if ($content && preg_match('/<img[^>]+src=[\'"]([^\'"]+)[\'"]/i', $content, $matches)) {
        return array(
            'source' => 'url',
            'id'     => 0,
            'url'    => $matches[1],
            'alt'    => $alt,
        );
    }

    return array(
        'source' => 'url',
        'id'     => 0,
        'url'    => $default_blog_fallback_image,
        'alt'    => $alt,
    );
};
?>

<main class="w-full overflow-hidden site-main max-sm:mt-[2.5rem] mt-[5rem]">

    <!-- HERO -->
    <section id="<?php echo esc_attr($section_id); ?>" data-matrix-block="<?php echo esc_attr(str_replace('_', '-', get_row_layout()) . '-' . get_row_index()); ?>"  class="flex overflow-hidden relative max-sm:flex-col" role="banner" aria-labelledby="<?php echo esc_attr($section_id); ?>-heading">
        <?php if (!empty($hero_bg_url)) : ?>
            <img
                src="<?php echo esc_url($hero_bg_url); ?>"
                class="object-cover relative inset-0 sm:absolute size-full"
                alt=""
                aria-hidden="true"
                decoding="async"
                fetchpriority="high"
            >
        <?php endif; ?>

        <div class="gap-2 items-center flex justify-end max-w-container mx-auto lg:min-h-[878px] sm:min-h-[600px] md:min-h-[800px] max-sm:py-5 px-5 w-full">
            <div class="flex relative flex-col justify-center self-stretch p-[2px] my-auto border-solid min-w-60 w-full md:w-[627px] max-w-full" style="border-color:#0902a4;border-width:5px;">
                <div class="flex flex-col p-16 w-full border-solid max-md:p-5 max-md:max-w-full" style="border-color:#0902a4;border-width:3px;background-color:#ffffff;">
                    <div class="w-full tracking-wider max-md:max-w-full">
                        <div class="w-full max-md:max-w-full">

                            <p class="max-md:text-[1rem] text-lg font-medium tracking-[1px]" style="color:#000000;">
                                <?php echo esc_html($hero_kicker); ?>
                            </p>

                            <<?php echo esc_attr($hero_tag); ?>
                                id="<?php echo esc_attr($section_id); ?>-heading"
                                class="mt-2 text-[68px] font-bold leading-[78px] max-md:max-w-full max-md:text-4xl max-md:leading-[51px] break-words overflow-wrap-anywhere max-mob:text-[2rem] max-mob:tracking-[1px] max-mob:leading-[2.625rem]"
                                style="color:#0902a4;"
                            >
                                <?php echo wp_kses_post($hero_heading); ?>
                            </<?php echo esc_attr($hero_tag); ?>>

                        </div>

                        <?php if (!empty($hero_body)) : ?>
                            <div class="mt-4 text-2xl font-medium max-md:max-w-full wp_editor max-sm:text-[1.125rem] tracking-[1px] leading-[1.625rem]" style="color:#000000;">
                                <?php echo wp_kses_post($hero_body); ?>
                            </div>
                        <?php else : ?>
                            <div class="mt-4 text-2xl font-medium max-md:max-w-full wp_editor max-sm:text-[1.125rem] tracking-[1px] leading-[1.625rem]" style="color:#000000;">
                                <p><?php echo esc_html($hero_sub); ?></p>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FILTERS (Alpine pills + search) -->
    <section class="flex overflow-hidden relative">
        <div class="flex flex-col items-center pt-5 pb-5 mx-auto w-full max-w-container max-xxl:px-[1rem]">
            <div class="px-0 pt-0 pb-0 w-full md:pt-8 md:pb-0">
                <div class="max-w-[1728px] mx-auto">
                    <form
                        method="get"
                        action="<?php echo esc_url(get_permalink(get_option('page_for_posts')) ?: home_url('/')); ?>"
                        class="w-full"
                        x-data="{
                            cat: '<?php echo esc_js($blog_cat); ?>',
                            search: '<?php echo esc_js($blog_search); ?>',
                            dragging: false,
                            didDrag: false,
                            dragThreshold: 12,
                            dragStartX: 0,
                            dragStartScrollLeft: 0,
                            setCat(slug){ this.cat = slug; this.$nextTick(() => this.$root.submit()); },
                            startDrag(e){
                                if (e.pointerType !== 'mouse') return;
                                this.dragging = true;
                                this.didDrag = false;
                                this.dragStartX = e.clientX;
                                this.dragStartScrollLeft = this.$refs.pillScroller.scrollLeft;
                                this.$refs.pillScroller.setPointerCapture(e.pointerId);
                            },
                            onDrag(e){
                                if (!this.dragging) return;
                                const dx = e.clientX - this.dragStartX;
                                if (Math.abs(dx) > this.dragThreshold) this.didDrag = true;
                                this.$refs.pillScroller.scrollLeft = this.dragStartScrollLeft - dx;
                            },
                            endDrag(e){
                                if (!this.dragging) return;
                                const dx = Math.abs(e.clientX - this.dragStartX);
                                this.didDrag = dx > this.dragThreshold;
                                this.dragging = false;
                                if (this.$refs.pillScroller.hasPointerCapture(e.pointerId)) {
                                    this.$refs.pillScroller.releasePointerCapture(e.pointerId);
                                }
                            }
                        }"
                        aria-label="Article filters and search"
                    >
                        <input type="hidden" name="blog_cat" :value="cat">
                        <input type="hidden" name="paged" value="1">

                        <nav class="flex gap-10 justify-between items-start pt-10 w-full max-lg:flex-col" aria-label="Article filters and search">

                            <!-- Filter Pills -->
                            <div class="w-full min-w-0 lg:flex-1">
                                <div
                                    x-ref="pillScroller"
                                    class="flex overflow-x-auto overflow-y-hidden flex-nowrap gap-4 items-center w-full min-w-0 text-lg font-semibold leading-none select-none no-scrollbar text-primary cursor-grab"
                                    :class="{ 'cursor-grabbing': dragging }"
                                    role="group"
                                    aria-label="Article category filters"
                                    @wheel.prevent="$el.scrollLeft += ($event.deltaY || $event.deltaX)"
                                    @pointerdown="startDrag($event)"
                                    @pointermove="onDrag($event)"
                                    @pointerup="endDrag($event)"
                                    @pointercancel="endDrag($event)"
                                    @pointerleave="endDrag($event)"
                                >
                                    <?php foreach ($filter_pills as $pill) : ?>
                                        <?php
                                        $is_active = ($blog_cat === $pill['slug']) || ($pill['slug'] === 'all' && $blog_cat === 'all');
                                        ?>
                                        <button
                                            type="button"
                                            class="<?php echo esc_attr($is_active
                                                ? 'btn wm-filter-pill flex shrink-0 gap-2 justify-center items-center px-6 py-2.5 text-primary bg-indigo-400 bg-opacity-30 rounded-full w-fit whitespace-nowrap transition-colors duration-200 max-md:px-5'
                                                : 'btn wm-filter-pill flex shrink-0 gap-2 justify-center items-center px-6 py-2.5 border border-primary border-solid rounded-full w-fit whitespace-nowrap transition-colors duration-200 max-md:px-5'
                                            ); ?>"
                                            :aria-pressed="cat === '<?php echo esc_js($pill['slug']); ?>' ? 'true' : 'false'"
                                            aria-label="<?php echo esc_attr($pill['label']); ?>"
                                            @click="if (didDrag) { didDrag = false; return; } setCat('<?php echo esc_js($pill['slug']); ?>')"
                                        >
                                            <span><?php echo esc_html($pill['label']); ?></span>
                                        </button>
                                    <?php endforeach; ?>
                                </div>

                                <?php if ($show_filter_arrows) : ?>
                                    <div class="flex gap-3 justify-start mt-3 first-line:items-center">
                                        <button
                                            type="button"
                                            class="btn flex justify-center items-center w-12 h-12 md:w-14 md:h-14 rounded-full transition-all bg-[#e2e2e2] text-primary hover:opacity-90 shadow shrink-0"
                                            aria-label="Scroll categories left"
                                            @click="$refs.pillScroller.scrollBy({ left: -320, behavior: 'smooth' })"
                                        >
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                <path d="M19 12H5M5 12L12 19M5 12L12 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                            </svg>
                                        </button>
                                        <button
                                            type="button"
                                            class="flex justify-center items-center w-12 h-12 text-white rounded-full shadow transition-all btn md:w-14 md:h-14 bg-primary hover:opacity-90 shrink-0"
                                            aria-label="Scroll categories right"
                                            @click="$refs.pillScroller.scrollBy({ left: 320, behavior: 'smooth' })"
                                        >
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                <path d="M5 12H19M19 12L12 5M19 12L12 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                            </svg>
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Search -->
                            <div class="text-base text-black w-full lg:shrink-0 lg:min-w-60 lg:w-[296px]">
                                <div class="w-full lg:max-w-[296px]">
                                    <div class="w-full">
                                        <div class="flex justify-between items-center px-4 w-full h-12 bg-white border border-solid transition-all duration-200 border-primary focus-within:ring-2 focus-within:ring-indigo-600 focus-within:ring-offset-2">
                                            <div class="flex flex-1 gap-2 items-center">
                                                <label for="article-search" class="sr-only">Search articles</label>

                                                <input
                                                    type="search"
                                                    id="article-search"
                                                    name="blog_search"
                                                    x-model="search"
                                                    value="<?php echo esc_attr($blog_search); ?>"
                                                    class="flex-1 h-6 leading-6 placeholder-gray-500 text-black bg-transparent border-none outline-none focus:outline-none"
                                                    placeholder="Search article"
                                                    aria-label="Search articles"
                                                    autocomplete="off"
                                                >
                                            </div>

                                            <button
                                                type="submit"
                                                class="flex justify-center items-center w-6 h-6 transition-opacity duration-200 hover:opacity-70"
                                                aria-label="Submit search"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                                    <path d="M19 19L14.65 14.65M17 9C17 13.4183 13.4183 17 9 17C4.58172 17 1 13.4183 1 9C1 4.58172 4.58172 1 9 1C13.4183 1 17 4.58172 17 9Z" stroke="#0902A4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </nav>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- FEATURED 3 (only page 1) -->
    <?php if ($show_featured_section && $featured_query instanceof WP_Query && $featured_query->have_posts()) : ?>
        <section id="blog-cards-9682" class="flex overflow-hidden relative" style="background-color:#FFFFFF;" aria-labelledby="blog-cards-9682-heading">
            <div class="flex flex-col items-center pt-5 pb-5 mx-auto w-full lg:pb-12 max-w-container max-xxl:px-5">

                <div class="pt-8 pb-0 w-full">
                    <div class="flex flex-col gap-8 items-start mx-auto max-w-container lg:flex-row">

                        <?php
                        $featured_posts = $featured_query->posts;
                        $featured_left  = array_slice($featured_posts, 0, 2);
                        $featured_right = array_slice($featured_posts, 2, 1);
                        ?>

                        <!-- LEFT COLUMN (FULL-CARD CLICKABLE) -->
                        <div class="flex flex-col gap-8 max-lg:w-full lg:flex-1">
                            <?php foreach ($featured_left as $p) : ?>
                                <?php
                                $image_data = $get_blog_card_image_data($p->ID);
                                $permalink = get_permalink($p->ID);
                                ?>
                                <article class="relative max-lg:h-[294px] lg:h-[332px] overflow-hidden group">
                                    <a href="<?php echo esc_url($permalink); ?>"
                                       class="absolute inset-0 z-10 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-600 hover:underline"
                                       aria-label="<?php echo esc_attr(sprintf('Read: %s', get_the_title($p->ID))); ?>">
                                        <span class="sr-only"><?php echo esc_html(get_the_title($p->ID)); ?></span>
                                    </a>

                                    <?php if ($image_data['source'] === 'attachment' && !empty($image_data['id'])) : ?>
                                        <?php echo wp_get_attachment_image((int) $image_data['id'], 'large', false, array(
                                            'class'    => 'absolute inset-0 w-full h-full object-cover transition-transform duration-500 ease-out group-hover:scale-105',
                                            'alt'      => esc_attr($image_data['alt']),
                                            'loading'  => 'lazy',
                                            'decoding' => 'async',
                                        )); ?>
                                    <?php else : ?>
                                        <img
                                            src="<?php echo esc_url($image_data['url']); ?>"
                                            class="object-cover absolute inset-0 w-full h-full transition-transform duration-500 ease-out group-hover:scale-105"
                                            alt="<?php echo esc_attr($image_data['alt']); ?>"
                                            loading="lazy"
                                            decoding="async"
                                        >
                                    <?php endif; ?>

                                    <div class="absolute inset-0 m-auto max-lg:w-[80%] max-lg:h-[80%] max-lg:justify-between lg:justify-start lg:inset-auto lg:bottom-6 lg:left-6 lg:right-6 backdrop-blur-[15px] bg-[#ffffff85] p-6 flex flex-col gap-6">
                                        <div class="flex flex-col gap-2">
                                            <div class="text-primary uppercase text-[16px] font-medium tracking-[1px]">
                                                <?php
                                                $cats = get_the_category($p->ID);
                                                echo !empty($cats) ? esc_html($cats[0]->name) : esc_html__('Uncategorized', 'matrix-starter');
                                                ?>
                                            </div>
                                            <h3 class="text-[20px] font-semibold leading-6 text-primary">
                                                <?php echo esc_html(get_the_title($p->ID)); ?>
                                            </h3>
                                            <p class="text-[18px] font-medium text-black">
                                                <?php echo esc_html(get_the_date('F j, Y', $p->ID)); ?>
                                            </p>
                                        </div>

                                        <span class="text-[16px] underline pointer-events-none select-none text-black/60 hover:text-black">
                                            <?php echo esc_html__('Discover', 'matrix-starter'); ?>
                                        </span>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>

                        <!-- RIGHT COLUMN (FULL-CARD CLICKABLE) -->
                        <?php if (!empty($featured_right)) : ?>
                            <?php $p = $featured_right[0]; ?>
                            <?php
                            $image_data = $get_blog_card_image_data($p->ID);
                            $permalink = get_permalink($p->ID);
                            ?>
                            <div class="max-lg:w-full lg:flex-1">
                                <article class="relative max-lg:h-[294px] lg:h-[696px] overflow-hidden group">
                                    <a href="<?php echo esc_url($permalink); ?>"
                                       class="absolute inset-0 z-10 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-600 hover:underline"
                                       aria-label="<?php echo esc_attr(sprintf('Read: %s', get_the_title($p->ID))); ?>">
                                        <span class="sr-only"><?php echo esc_html(get_the_title($p->ID)); ?></span>
                                    </a>

                                    <?php if ($image_data['source'] === 'attachment' && !empty($image_data['id'])) : ?>
                                        <?php echo wp_get_attachment_image((int) $image_data['id'], 'large', false, array(
                                            'class'    => 'absolute inset-0 w-full h-full object-cover transition-transform duration-500 ease-out group-hover:scale-105',
                                            'alt'      => esc_attr($image_data['alt']),
                                            'loading'  => 'lazy',
                                            'decoding' => 'async',
                                        )); ?>
                                    <?php else : ?>
                                        <img
                                            src="<?php echo esc_url($image_data['url']); ?>"
                                            class="object-cover absolute inset-0 w-full h-full transition-transform duration-500 ease-out group-hover:scale-105"
                                            alt="<?php echo esc_attr($image_data['alt']); ?>"
                                            loading="lazy"
                                            decoding="async"
                                        >
                                    <?php endif; ?>

                                    <div class="absolute inset-0 m-auto max-lg:w-[80%] max-lg:h-[80%] max-lg:justify-between lg:justify-start lg:inset-auto lg:bottom-6 lg:left-6 lg:right-6 backdrop-blur-[15px] bg-[#ffffff85] p-6 flex flex-col gap-6">
                                        <div>
                                            <div class="text-[16px] uppercase font-medium text-primary">
                                                <?php
                                                $cats = get_the_category($p->ID);
                                                echo !empty($cats) ? esc_html($cats[0]->name) : esc_html__('Uncategorized', 'matrix-starter');
                                                ?>
                                            </div>
                                            <h3 class="text-[20px] font-semibold text-primary">
                                                <?php echo esc_html(get_the_title($p->ID)); ?>
                                            </h3>
                                            <p class="text-[18px] font-medium text-black">
                                                <?php echo esc_html(get_the_date('F j, Y', $p->ID)); ?>
                                            </p>
                                        </div>

                                        <span class="text-[16px] underline pointer-events-none select-none text-black/60 hover:text-black">
                                            <?php echo esc_html__('Read more', 'matrix-starter'); ?>
                                        </span>
                                    </div>
                                </article>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>

            </div>
        </section>
    <?php endif; ?>

    <!-- CONTINUE LOOP (GRID, FULL-CARD CLICKABLE) -->
    <section class="flex overflow-hidden relative pb-[56px] lg:pb-[72px]">
        <div class="flex flex-col items-center pt-5 pb-5 mx-auto w-full max-w-container max-xxl:px-5">

            <div class="w-full max-w-[1728px] mx-auto">
                <div class="grid grid-cols-3 gap-6 w-full max-lg:grid-cols-2 max-sm:grid-cols-1" role="list">

                    <?php if ($main_query->have_posts()) : ?>
                        <?php while ($main_query->have_posts()) : $main_query->the_post(); ?>
                            <?php
                            $post_id  = get_the_ID();
                            $image_data = $get_blog_card_image_data($post_id);

                            $cats     = get_the_category($post_id);
                            $cat_name = !empty($cats) ? $cats[0]->name : __('Uncategorized', 'matrix-starter');
                            ?>
                            <article class="overflow-hidden relative group" role="listitem">
                                <a href="<?php the_permalink(); ?>"
                                   class="absolute inset-0 z-10 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-600 hover:underline"
                                   aria-label="<?php echo esc_attr(sprintf('Read: %s', get_the_title())); ?>">
                                    <span class="sr-only"><?php the_title(); ?></span>
                                </a>

                                <div class="w-full h-[280px] overflow-hidden">
                                    <?php if ($image_data['source'] === 'attachment' && !empty($image_data['id'])) : ?>
                                        <?php echo wp_get_attachment_image((int) $image_data['id'], 'large', false, array(
                                            'class'    => 'w-full h-full object-cover max-sm:object-contain',
                                            'alt'      => esc_attr($image_data['alt']),
                                            'loading'  => 'lazy',
                                            'decoding' => 'async',
                                        )); ?>
                                    <?php else : ?>
                                        <img
                                            src="<?php echo esc_url($image_data['url']); ?>"
                                            class="object-cover w-full h-full max-sm:object-contain"
                                            alt="<?php echo esc_attr($image_data['alt']); ?>"
                                            loading="lazy"
                                            decoding="async"
                                        >
                                    <?php endif; ?>
                                </div>

                                <div class="flex overflow-hidden flex-col px-4 mt-4 w-full max-sm:px-0">
                                    <div class="text-lg font-medium tracking-wider max-sm:text-[14px]" aria-label="Category">
                                        <?php echo esc_html($cat_name); ?>
                                    </div>

                                    <h3 class="mt-2 text-2xl font-semibold leading-7 text-primary max-sm:text-[22px]">
                                        <?php the_title(); ?>
                                    </h3>

                                    <time class="mt-2 tracking-wider" datetime="<?php echo esc_attr(get_the_date(DATE_W3C)); ?>">
                                        <?php echo esc_html(get_the_date('F j, Y') . ' | ' . get_the_time('g:i A')); ?>
                                    </time>

                                    <div class="flex gap-2 items-center self-start mt-2 tracking-tight leading-none text-primary">
                                        <span class="flex gap-2 items-center whitespace-nowrap pointer-events-none select-none text-primary">
                                            <span class="self-stretch my-auto"><?php echo esc_html__('Read more', 'matrix-starter'); ?></span>
                                            <svg class="object-contain self-stretch my-auto w-6 shrink-0" width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                <path d="M5 12H19M19 12L12 5M19 12L12 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                            </svg>
                                        </span>
                                    </div>
                                </div>
                            </article>

                        <?php endwhile; wp_reset_postdata(); ?>
                    <?php else : ?>
                        <p class="w-full text-black/70">No posts found.</p>
                    <?php endif; ?>

                </div>
            </div>

            <!-- PAGINATION (15 per page, preserves filters/search) -->
            <?php if ($total_pages > 1) : ?>
                <?php
                $base_args = array(
                    'blog_cat'    => $blog_cat,
                    'blog_search' => $blog_search,
                );

                $current_page = $paged;

                $prev_page = ($current_page > 1) ? ($current_page - 1) : 0;
                $next_page = ($current_page < $total_pages) ? ($current_page + 1) : 0;

                $prev_url = $prev_page ? add_query_arg(array_merge($base_args, array('paged' => $prev_page))) : '';
                $next_url = $next_page ? add_query_arg(array_merge($base_args, array('paged' => $next_page))) : '';

                // Mobile pagination: keep it compact (max 3 numeric items).
                $mobile_start = max(1, $current_page - 1);
                $mobile_end   = min($total_pages, $current_page + 1);
                if (($mobile_end - $mobile_start) < 2) {
                    if ($mobile_start === 1) {
                        $mobile_end = min($total_pages, 3);
                    } elseif ($mobile_end === $total_pages) {
                        $mobile_start = max(1, $total_pages - 2);
                    }
                }

                // Desktop pagination: max 4 numeric buttons,
                // while always including first and last page.
                $desktop_pages = array();
                if ($total_pages <= 4) {
                    for ($i = 1; $i <= $total_pages; $i++) {
                        $desktop_pages[] = $i;
                    }
                } else {
                    $middle_start = max(2, $current_page - 1);
                    $middle_end   = min($total_pages - 1, $middle_start + 1);

                    if (($middle_end - $middle_start) < 1) {
                        $middle_start = max(2, $middle_end - 1);
                    }

                    $desktop_pages = array_merge(
                        array(1),
                        range($middle_start, $middle_end),
                        array($total_pages)
                    );
                    $desktop_pages = array_values(array_unique($desktop_pages));
                }
                ?>

                <nav aria-label="Pagination Navigation"
                    class="flex flex-nowrap gap-3 justify-center items-center mt-12 text-base font-semibold leading-none whitespace-nowrap md:gap-8">

                    <!-- PREVIOUS -->
                    <div class="flex gap-1 items-center py-1 pr-4 pl-1">
                        <?php if ($prev_page) : ?>
                            <a href="<?php echo esc_url($prev_url); ?>"
                                class="flex gap-2 items-center transition-colors text-primary btn hover:text-indigo-600 w-fit"
                                aria-label="Go to previous page">
                                <svg class="w-6 h-6 md:w-8 md:h-8" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                    <path d="M12.5 4.5L7.5 10l5 5.5"
                                        stroke="currentColor" stroke-width="1.8"
                                        stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <span class="hidden md:inline">Previous</span>
                            </a>
                        <?php else : ?>
                            <button class="flex gap-2 items-center text-gray-400 cursor-not-allowed btn" disabled>
                                <svg class="w-6 h-6 md:w-8 md:h-8" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                    <path d="M12.5 4.5L7.5 10l5 5.5"
                                        stroke="currentColor" stroke-width="1.8"
                                        stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <span class="hidden md:inline">Previous</span>
                            </button>
                        <?php endif; ?>
                    </div>

                    <!-- PAGE NUMBERS (mobile) -->
                    <div class="flex gap-2 items-center text-base leading-none md:hidden" role="group" aria-label="Page numbers">
                        <?php for ($i = $mobile_start; $i <= $mobile_end; $i++) : ?>
                            <?php if ($i === $current_page) : ?>
                                <span class="flex justify-center items-center w-10 h-10 rounded-full border text-primary border-primary btn"
                                    aria-current="page">
                                    <?php echo esc_html((string) $i); ?>
                                </span>
                            <?php else : ?>
                                <a href="<?php echo esc_url(add_query_arg(array_merge($base_args, array('paged' => $i)))); ?>"
                                    class="wm-pagination-number flex justify-center items-center w-10 h-10 text-white rounded-full transition-colors btn bg-primary"
                                    aria-label="<?php echo esc_attr('Go to page ' . $i); ?>">
                                    <?php echo esc_html((string) $i); ?>
                                </a>
                            <?php endif; ?>
                        <?php endfor; ?>
                    </div>

                    <!-- PAGE NUMBERS (desktop) -->
                    <div class="hidden gap-4 items-center text-lg leading-none md:flex" role="group" aria-label="Page numbers">
                        <?php foreach ($desktop_pages as $i) : ?>
                            <?php if ($i === $current_page) : ?>
                                <span class="flex justify-center items-center w-12 h-12 rounded-full border text-primary border-primary btn"
                                    aria-current="page">
                                    <?php echo esc_html((string) $i); ?>
                                </span>
                            <?php else : ?>
                                <a href="<?php echo esc_url(add_query_arg(array_merge($base_args, array('paged' => $i)))); ?>"
                                    class="wm-pagination-number flex justify-center items-center w-12 h-12 text-white rounded-full transition-colors btn bg-primary"
                                    aria-label="<?php echo esc_attr('Go to page ' . $i); ?>">
                                    <?php echo esc_html((string) $i); ?>
                                </a>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>

                    <!-- NEXT -->
                    <div class="flex gap-1 items-center py-1 pr-1 pl-4">
                        <?php if ($next_page) : ?>
                            <a href="<?php echo esc_url($next_url); ?>"
                                class="flex gap-2 items-center transition-colors text-primary btn hover:text-indigo-600 w-fit"
                                aria-label="Go to next page">
                                <span class="hidden md:inline">Next</span>
                                <svg class="w-6 h-6 md:w-8 md:h-8" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                    <path d="M7.5 4.5L12.5 10l-5 5.5"
                                        stroke="currentColor" stroke-width="1.8"
                                        stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </a>
                        <?php else : ?>
                            <button class="flex gap-2 items-center text-gray-400 cursor-not-allowed btn" disabled>
                                <span class="hidden md:inline">Next</span>
                                <svg class="w-6 h-6 md:w-8 md:h-8" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                    <path d="M7.5 4.5L12.5 10l-5 5.5"
                                        stroke="currentColor" stroke-width="1.8"
                                        stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        <?php endif; ?>
                    </div>

                </nav>
            <?php endif; ?>

        </div>
    </section>

</main>

<?php get_footer(); ?>