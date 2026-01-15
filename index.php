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
 * Define pill categories (labels + slugs).
 * These should match your WP Category slugs.
 */
$filter_pills = array(
    array('label' => 'All articles',      'slug' => 'all'),
    array('label' => 'News',              'slug' => 'news'),
    array('label' => 'Events & Webinar',  'slug' => 'events-webinar'),
    array('label' => 'Insights',          'slug' => 'insights'),
    array('label' => 'Press Releases',    'slug' => 'press-releases'),
);

// Build query args shared by both featured + main query
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

// ---------- Featured 3 ----------
$featured_args = $base_query_args;
$featured_args['posts_per_page'] = 3;
$featured_args['paged'] = 1;

$featured_query = new WP_Query($featured_args);
$total_found    = (int) $featured_query->found_posts;

// ---------- Main grid (next posts after featured) ----------
$posts_per_page  = 15;
$featured_offset = 3;

$remaining   = max(0, $total_found - $featured_offset);
$total_pages = (int) ceil($remaining / $posts_per_page);

// Offset: skip the featured 3 always, then paginate the rest
$offset = $featured_offset + (($paged - 1) * $posts_per_page);

$main_args = $base_query_args;
$main_args['posts_per_page'] = $posts_per_page;
$main_args['offset']         = $offset;
$main_args['no_found_rows']  = true;

$main_query = new WP_Query($main_args);

// Search icon (from your example)
$search_icon = 'https://api.builder.io/api/v1/image/assets/f35586c581c84ecf82b6de32c55ed39e/e78553e3c5cc215eba71c71fa3d0d28d7e38d9dd?placeholderIfAbsent=true';

// Hero bg fallback (if no ACF image)
$hero_bg_url = !empty($hero_bg['url']) ? $hero_bg['url'] : '';
$hero_bg_id  = !empty($hero_bg['ID']) ? (int) $hero_bg['ID'] : 0;
$hero_bg_alt = $hero_bg_id ? get_post_meta($hero_bg_id, '_wp_attachment_image_alt', true) : '';
if (empty($hero_bg_alt)) {
    $hero_bg_alt = 'Hero background image';
}

$section_id = 'hero_' . wp_generate_uuid4();

// Helpers
function _matrix_first_cat_name($post_id) {
    $terms = get_the_terms($post_id, 'category');
    if (is_wp_error($terms) || empty($terms)) return '';
    return $terms[0]->name ?? '';
}
?>

<main class="overflow-hidden w-full min-h-screen site-main">

    <!-- HERO -->
    <section id="<?php echo esc_attr($section_id); ?>" class="flex overflow-hidden relative max-sm:flex-col" role="banner" aria-labelledby="<?php echo esc_attr($section_id); ?>-heading">
        <?php if (!empty($hero_bg_url)) : ?>
            <img
                src="<?php echo esc_url($hero_bg_url); ?>"
                class="object-cover relative inset-0 sm:absolute size-full"
                alt="<?php echo esc_attr($hero_bg_alt); ?>"
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
    <!-- FILTERS (Alpine pills + search) -->
<section class="flex overflow-hidden relative">
  <div class="flex flex-col items-center pt-5 pb-5 mx-auto w-full max-w-container max-xxl:px-[1rem]">

    <div class="w-full px-0 pt-0 pb-0 md:px-8 md:pt-8 md:pb-0 lg:px-0">
      <div class="max-w-[1728px] mx-auto">
        <form
          method="get"
          action="<?php echo esc_url(get_permalink(get_option('page_for_posts')) ?: home_url('/')); ?>"
          class="w-full"
          x-data="{
              cat: '<?php echo esc_js($blog_cat); ?>',
              search: '<?php echo esc_js($blog_search); ?>',
              setCat(slug){ this.cat = slug; this.$nextTick(() => this.$root.submit()); }
          }"
          aria-label="Article filters and search"
        >
          <input type="hidden" name="blog_cat" :value="cat">
          <input type="hidden" name="paged" value="1">

          <nav class="flex gap-10 justify-between items-start pt-10 w-full max-md:flex-col"
               aria-label="Article filters and search">

            <!-- Filter Pills -->
            <div class="flex flex-wrap gap-4 items-center text-lg font-semibold leading-none text-indigo-800 min-w-60"
                 role="group" aria-label="Article category filters">
              <?php foreach ($filter_pills as $pill) : ?>
                <?php $is_active = ($blog_cat === $pill['slug']) || ($pill['slug'] === 'all' && $blog_cat === 'all'); ?>
                <button
                  type="button"
                  class="<?php echo esc_attr($is_active
                    ? 'btn flex gap-2 justify-center items-center px-6 py-2.5 text-indigo-800 bg-indigo-400 bg-opacity-30 rounded-full w-fit whitespace-nowrap hover:bg-opacity-40 transition-colors duration-200 max-md:px-5'
                    : 'btn flex gap-2 justify-center items-center px-6 py-2.5 border border-indigo-800 border-solid rounded-full w-fit whitespace-nowrap hover:bg-indigo-50 transition-colors duration-200 max-md:px-5'
                  ); ?>"
                  :aria-pressed="cat === '<?php echo esc_js($pill['slug']); ?>' ? 'true' : 'false'"
                  aria-label="<?php echo esc_attr($pill['label']); ?>"
                  @click="setCat('<?php echo esc_js($pill['slug']); ?>')"
                >
                  <span><?php echo esc_html($pill['label']); ?></span>
                </button>
              <?php endforeach; ?>
            </div>

            <!-- Search -->
            <div class="text-base text-black min-w-60 w-[296px]">
              <div class="max-w-full w-[296px]">
                <div class="w-full">
                  <div
                    class="flex justify-between items-center h-12 px-4 w-full bg-white border border-indigo-800 border-solid
                           transition-all duration-200
                           focus-within:ring-2 focus-within:ring-indigo-600 focus-within:ring-offset-2"
                  >
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
                      <img
                        src="<?php echo esc_url($search_icon); ?>"
                        alt=""
                        class="object-contain w-6 h-6"
                        role="presentation"
                      >
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


    <!-- FEATURED 3 -->
    <section id="blog-cards-9682" class="flex overflow-hidden relative" style="background-color:#FFFFFF;" aria-labelledby="blog-cards-9682-heading">
        <div class="flex flex-col items-center pt-5 pb-5 lg:pb-12 mx-auto w-full max-w-container">

            <div class="px-4 pt-8 pb-0 w-full md:px-8 lg:px-0">
                <div class="max-w-[1728px] mx-auto flex flex-col lg:flex-row items-start gap-8">

                    <?php
                    $featured_posts = $featured_query->posts;
                    $featured_left  = array_slice($featured_posts, 0, 2);
                    $featured_right = array_slice($featured_posts, 2, 1);
                    ?>

                    <!-- LEFT COLUMN (FULL-CARD CLICKABLE) -->
                    <div class="flex flex-col gap-8 max-lg:w-full lg:flex-1">
                        <?php foreach ($featured_left as $p) : ?>
                            <?php
                            $thumb_id  = get_post_thumbnail_id($p->ID);
                            $img_alt   = $thumb_id ? get_post_meta($thumb_id, '_wp_attachment_image_alt', true) : '';
                            if (empty($img_alt)) $img_alt = get_the_title($p->ID);
                            $permalink = get_permalink($p->ID);
                            ?>
                            <article class="relative max-lg:h-[294px] lg:h-[332px] overflow-hidden group">
                                <a href="<?php echo esc_url($permalink); ?>"
                                   class="absolute inset-0 z-10 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-600"
                                   aria-label="<?php echo esc_attr(sprintf('Read: %s', get_the_title($p->ID))); ?>">
                                    <span class="sr-only"><?php echo esc_html(get_the_title($p->ID)); ?></span>
                                </a>

                                <?php if ($thumb_id) : ?>
                                    <?php echo wp_get_attachment_image($thumb_id, 'large', false, [
                                        'class' => 'absolute inset-0 w-full h-full object-cover',
                                        'alt'   => esc_attr($img_alt),
                                        'loading' => 'lazy',
                                        'decoding' => 'async',
                                    ]); ?>
                                <?php endif; ?>

                                <div class="absolute inset-0 m-auto max-lg:w-[80%] max-lg:h-[80%] max-lg:justify-between lg:justify-start lg:inset-auto lg:bottom-6 lg:left-6 lg:right-6 backdrop-blur-[15px] bg-[#ffffff85] p-6 flex flex-col gap-6">


                                    <div class="flex flex-col gap-2">
                                        <div class="text-primary text-base font-medium tracking-[1px]">
                                            <?php
                                            $cats = get_the_category($p->ID);
                                            echo !empty($cats) ? esc_html($cats[0]->name) : esc_html__('Uncategorized', 'matrix-starter');
                                            ?>
                                        </div>
                                        <h3 class="text-xl font-semibold leading-6 text-primary">
                                            <?php echo esc_html(get_the_title($p->ID)); ?>
                                        </h3>
                                        <p class="text-lg font-medium text-black">
                                            <?php echo esc_html(get_the_date('F j, Y', $p->ID)); ?>
                                        </p>
                                    </div>

                                    <span class="underline pointer-events-none select-none text-black/60 hover:text-black">
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
                        $thumb_id  = get_post_thumbnail_id($p->ID);
                        $img_alt   = $thumb_id ? get_post_meta($thumb_id, '_wp_attachment_image_alt', true) : '';
                        if (empty($img_alt)) $img_alt = get_the_title($p->ID);
                        $permalink = get_permalink($p->ID);
                        ?>
                        <div class="max-lg:w-full lg:flex-1">
                            <article class="relative max-lg:h-[294px] lg:h-[696px] overflow-hidden group">
                                <a href="<?php echo esc_url($permalink); ?>"
                                   class="absolute inset-0 z-10 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-600"
                                   aria-label="<?php echo esc_attr(sprintf('Read: %s', get_the_title($p->ID))); ?>">
                                    <span class="sr-only"><?php echo esc_html(get_the_title($p->ID)); ?></span>
                                </a>

                                <?php if ($thumb_id) : ?>
                                    <?php echo wp_get_attachment_image($thumb_id, 'large', false, [
                                        'class' => 'absolute inset-0 w-full h-full object-cover',
                                        'alt'   => esc_attr($img_alt),
                                        'loading' => 'lazy',
                                        'decoding' => 'async',
                                    ]); ?>
                                <?php endif; ?>

                                <div class="absolute inset-0 m-auto max-lg:w-[80%] max-lg:h-[80%] max-lg:justify-between lg:justify-start lg:inset-auto lg:bottom-6 lg:left-6 lg:right-6 backdrop-blur-[15px] bg-[#ffffff85] p-6 flex flex-col gap-6">

                                    <div>
                                        <div class="text-base font-medium text-primary">
                                            <?php
                                            $cats = get_the_category($p->ID);
                                            echo !empty($cats) ? esc_html($cats[0]->name) : esc_html__('Uncategorized', 'matrix-starter');
                                            ?>
                                        </div>
                                        <h3 class="text-xl font-semibold text-primary">
                                            <?php echo esc_html(get_the_title($p->ID)); ?>
                                        </h3>
                                        <p class="text-base font-medium text-black">
                                            <?php echo esc_html(get_the_date('F j, Y', $p->ID)); ?>
                                        </p>
                                    </div>

                                    <span class="underline pointer-events-none select-none text-black/60 hover:text-black">
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

    <!-- CONTINUE LOOP (GRID, FULL-CARD CLICKABLE) -->
    <section class="flex overflow-hidden relative pb-[56px] lg:pb-[72px]">
        <div class="flex flex-col items-center pt-5 pb-5 mx-auto w-full max-w-container max-lg:px-5">

            <div class="w-full max-w-[1728px] mx-auto">
                <div class="grid grid-cols-3 gap-6 w-full max-lg:grid-cols-2 max-sm:grid-cols-1" role="list">

                    <?php if ($main_query->have_posts()) : ?>
                        <?php while ($main_query->have_posts()) : $main_query->the_post(); ?>
                            <?php
                            $post_id  = get_the_ID();
                            $thumb_id = get_post_thumbnail_id($post_id);
                            $img_alt  = $thumb_id ? get_post_meta($thumb_id, '_wp_attachment_image_alt', true) : '';
                            if (empty($img_alt)) $img_alt = get_the_title($post_id);

                            $cats     = get_the_category($post_id);
                            $cat_name = !empty($cats) ? $cats[0]->name : __('Uncategorized', 'matrix-starter');
                            ?>
                            <article class="overflow-hidden relative group" role="listitem">
                                <a href="<?php the_permalink(); ?>"
                                   class="absolute inset-0 z-10 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-600"
                                   aria-label="<?php echo esc_attr(sprintf('Read: %s', get_the_title())); ?>">
                                    <span class="sr-only"><?php the_title(); ?></span>
                                </a>

                                <?php if ($thumb_id) : ?>
                                    <div class="w-full">
                                        <?php echo wp_get_attachment_image($thumb_id, 'large', false, [
                                            'class'    => 'w-full h-auto object-cover',
                                            'alt'      => esc_attr($img_alt),
                                            'loading'  => 'lazy',
                                            'decoding' => 'async',
                                        ]); ?>
                                    </div>
                                <?php endif; ?>

                                <div class="flex overflow-hidden flex-col px-4 mt-4 w-full">
                                    <div class="text-lg font-medium tracking-wider" aria-label="Category">
                                        <?php echo esc_html($cat_name); ?>
                                    </div>

                                    <h3 class="mt-2 text-2xl font-semibold leading-7 text-indigo-800">
                                        <?php the_title(); ?>
                                    </h3>

                                    <time class="mt-2 tracking-wider" datetime="<?php echo esc_attr(get_the_date(DATE_W3C)); ?>">
                                        <?php echo esc_html(get_the_date('F j, Y') . ' | ' . get_the_time('g:i A')); ?>
                                    </time>

                                    <div class="flex gap-2 items-center self-start mt-2 tracking-tight leading-none text-indigo-800">
                                        <span class="flex gap-2 items-center text-indigo-800 whitespace-nowrap pointer-events-none select-none">
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

    $page_numbers = paginate_links(array(
        'total'     => $total_pages,
        'current'   => $current_page,
        'type'      => 'array',
        'prev_next' => false,
        'end_size'  => 1,
        'mid_size'  => 2,
        'base'      => esc_url_raw(add_query_arg(array_merge($base_args, array('paged' => '%#%')))),
        'format'    => '',
    ));

    $prev_page = ($current_page > 1) ? ($current_page - 1) : 0;
    $next_page = ($current_page < $total_pages) ? ($current_page + 1) : 0;

    $prev_url = $prev_page ? add_query_arg(array_merge($base_args, array('paged' => $prev_page))) : '';
    $next_url = $next_page ? add_query_arg(array_merge($base_args, array('paged' => $next_page))) : '';
    ?>

    <nav aria-label="Pagination Navigation"
         class="flex flex-wrap gap-8 justify-center items-center mt-12 text-base font-semibold leading-none whitespace-nowrap">

        <!-- PREVIOUS -->
        <div class="flex gap-1 items-center py-1 pr-4 pl-1">
            <?php if ($prev_page) : ?>
                <a href="<?php echo esc_url($prev_url); ?>"
                   class="flex gap-2 items-center text-indigo-800 transition-colors btn hover:text-indigo-600 w-fit"
                   aria-label="Go to previous page">
                    <svg class="w-8 h-8" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                        <path d="M12.5 4.5L7.5 10l5 5.5"
                              stroke="currentColor" stroke-width="1.8"
                              stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span>Previous</span>
                </a>
            <?php else : ?>
                <button class="flex gap-2 items-center text-gray-400 cursor-not-allowed btn" disabled>
                    <svg class="w-8 h-8" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                        <path d="M12.5 4.5L7.5 10l5 5.5"
                              stroke="currentColor" stroke-width="1.8"
                              stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span>Previous</span>
                </button>
            <?php endif; ?>
        </div>

        <!-- PAGE NUMBERS -->
        <div class="flex gap-4 items-center text-lg leading-none min-w-60" role="group" aria-label="Page numbers">
            <?php if (!empty($page_numbers) && is_array($page_numbers)) : ?>
                <?php foreach ($page_numbers as $link_html) : ?>
                    <?php
                    $is_current = (strpos($link_html, 'current') !== false);
                    $page_num = (int) wp_strip_all_tags($link_html);
                    preg_match('/href=["\']([^"\']+)["\']/', $link_html, $matches);
                    $url = !empty($matches[1]) ? $matches[1] : '';
                    ?>

                    <?php if ($is_current) : ?>
                        <span class="flex justify-center items-center w-12 h-12 text-indigo-800 rounded-full border border-indigo-800 btn"
                              aria-current="page">
                            <?php echo esc_html((string) $page_num); ?>
                        </span>
                    <?php else : ?>
                        <a href="<?php echo esc_url($url); ?>"
                           class="flex justify-center items-center w-12 h-12 text-white rounded-full transition-colors btn bg-primary hover:bg-opacity-80"
                           aria-label="<?php echo esc_attr('Go to page ' . $page_num); ?>">
                            <?php echo esc_html((string) $page_num); ?>
                        </a>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- NEXT -->
        <div class="flex gap-1 items-center py-1 pl-4 pr-1">
            <?php if ($next_page) : ?>
                <a href="<?php echo esc_url($next_url); ?>"
                   class="flex gap-2 items-center text-indigo-800 transition-colors btn hover:text-indigo-600 w-fit"
                   aria-label="Go to next page">
                    <span>Next</span>
                    <svg class="w-8 h-8" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                        <path d="M7.5 4.5L12.5 10l-5 5.5"
                              stroke="currentColor" stroke-width="1.8"
                              stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
            <?php else : ?>
                <button class="flex gap-2 items-center text-gray-400 cursor-not-allowed btn" disabled>
                    <span>Next</span>
                    <svg class="w-8 h-8" viewBox="0 0 20 20" fill="none" aria-hidden="true">
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
