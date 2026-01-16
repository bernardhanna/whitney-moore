<?php
/**
 * Flexi Block: Team Carousel
 * Template: team_carousel.php
 */

if (!defined('ABSPATH')) exit;

// Unique IDs
$section_id = 'team-carousel-' . wp_generate_uuid4();
$track_id   = $section_id . '-track';
$dots_id    = $section_id . '-dots';

// Content
$heading          = get_sub_field('heading');
$heading_tag      = get_sub_field('heading_tag') ?: 'h2';
$background_color = get_sub_field('background_color') ?: '#FFFFFF';

// Source & query defaults
$source_mode    = get_sub_field('source_mode') ?: 'taxonomy';
$taxonomy_type  = get_sub_field('taxonomy_type') ?: 'team_practice_area';
$practice_terms = get_sub_field('practice_area_terms'); // term IDs
$sector_terms   = get_sub_field('sector_terms');        // term IDs
$posts_per_page = (int) get_sub_field('posts_per_page');
$posts_per_page = ($posts_per_page === 0) ? -1 : $posts_per_page; // 0 => ALL
$order_by       = get_sub_field('order_by') ?: 'menu_order';
$order          = get_sub_field('order') ?: 'ASC';

// Display toggles
$show_name      = (int) get_sub_field('show_name') === 1;
$show_job_title = (int) get_sub_field('show_job_title') === 1;

// Slider toggles
$enable_slider     = (int) get_sub_field('enable_slider') === 1;
$arrow_enabled     = (int) get_sub_field('arrows') === 1;
$dots_enabled      = (int) get_sub_field('dots') === 1;
$autoplay_enabled  = (int) get_sub_field('autoplay') === 1;
$autoplay_speed    = (int) get_sub_field('autoplay_speed') ?: 5000;
$slides_xl         = (int) get_sub_field('slides_xl'); if ($slides_xl <= 0) $slides_xl = 4; // desktop default 4
$slides_lg         = (int) get_sub_field('slides_lg'); if ($slides_lg <= 0) $slides_lg = 3;
$slides_md         = (int) get_sub_field('slides_md'); if ($slides_md <= 0) $slides_md = 2;
$slides_sm         = (int) get_sub_field('slides_sm'); if ($slides_sm <= 0) $slides_sm = 1;

// Padding classes
$padding_classes = [];
if (have_rows('padding_settings')) {
    while (have_rows('padding_settings')) {
        the_row();
        $screen = get_sub_field('screen_size');
        $pt     = get_sub_field('padding_top');
        $pb     = get_sub_field('padding_bottom');
        if ($screen !== '' && $pt !== '' && $pt !== null) {
            $padding_classes[] = "{$screen}:pt-[{$pt}rem]";
        }
        if ($screen !== '' && $pb !== '' && $pb !== null) {
            $padding_classes[] = "{$screen}:pb-[{$pb}rem]";
        }
    }
}
$padding_classes_string = !empty($padding_classes) ? ' ' . implode(' ', $padding_classes) : '';

// Validate heading tag
$allowed_tags = ['h1','h2','h3','h4','h5','h6','span','p'];
if (!in_array($heading_tag, $allowed_tags, true)) {
    $heading_tag = 'h2';
}

// Build items: manual OR taxonomy query
$items = [];
$default_profile = '/wp-content/uploads/2025/12/image-2-1.png';

if ($source_mode === 'manual') {
    $images = get_sub_field('images');
    if (!empty($images) && is_array($images)) {
        foreach ($images as $row) {
            $img_id  = isset($row['image']) ? (int) $row['image'] : 0;
            $img_url = $img_id ? wp_get_attachment_image_url($img_id, 'large') : $default_profile;
            $img_alt = $img_id ? (get_post_meta($img_id, '_wp_attachment_image_alt', true) ?: get_the_title($img_id)) : 'Profile image';
            $items[] = [
                'img_url'   => $img_url,
                'img_alt'   => $img_alt,
                'title'     => '',
                'subtitle'  => '',
                'permalink' => '',
            ];
        }
    }
} else {
    $tax_query = [];
    if ($taxonomy_type === 'team_practice_area') {
        $terms = is_array($practice_terms) ? array_filter(array_map('intval', $practice_terms)) : [];
        if (!empty($terms)) {
            $tax_query[] = [
                'taxonomy' => 'team_practice_area',
                'field'    => 'term_id',
                'terms'    => $terms,
            ];
        }
    } elseif ($taxonomy_type === 'team_sector') {
        $terms = is_array($sector_terms) ? array_filter(array_map('intval', $sector_terms)) : [];
        if (!empty($terms)) {
            $tax_query[] = [
                'taxonomy' => 'team_sector',
                'field'    => 'term_id',
                'terms'    => $terms,
            ];
        }
    }

    $args = [
        'post_type'      => 'team',
        'post_status'    => 'publish',
        'posts_per_page' => $posts_per_page,
        'orderby'        => $order_by,
        'order'          => $order,
        'no_found_rows'  => true,
    ];
    if (!empty($tax_query)) {
        $args['tax_query'] = $tax_query;
    }

    $q = new WP_Query($args);
    if ($q->have_posts()) {
        while ($q->have_posts()) {
            $q->the_post();
            $pid       = get_the_ID();

            $image_id  = get_post_thumbnail_id($pid);
            $img_url   = $image_id ? wp_get_attachment_image_url($image_id, 'large') : $default_profile;
            $img_alt   = $image_id ? (get_post_meta($image_id, '_wp_attachment_image_alt', true) ?: get_the_title($image_id)) : get_the_title($pid);

            $title     = get_the_title($pid);
            $job_title = function_exists('get_field') ? (string) get_field('job_title', $pid) : '';
            $permalink = get_permalink($pid);

            $items[] = [
                'img_url'   => $img_url,
                'img_alt'   => $img_alt,
                'title'     => $title,
                'subtitle'  => $job_title,
                'permalink' => $permalink,
            ];
        }
        wp_reset_postdata();
    }
}
?>
<section
  id="<?php echo esc_attr($section_id); ?>"
  class="flex overflow-hidden relative"
  style="background-color: <?php echo esc_attr($background_color); ?>;"
  role="region"
  aria-labelledby="<?php echo esc_attr($section_id); ?>-heading"
>
  <div class="flex flex-col items-center w-full mx-auto max-w-container pt-5 max-md:pb-10 pb-20 max-lg:px-5<?php echo esc_attr($padding_classes_string); ?>">

    <?php if (!empty($heading)) : ?>
      <header class="px-12 pt-14 w-full max-md:px-5">
        <<?php echo esc_html($heading_tag); ?>
          id="<?php echo esc_attr($section_id); ?>-heading"
          class="w-full text-3xl font-bold tracking-wider leading-none text-primary max-md:max-w-full"
        >
          <?php echo esc_html($heading); ?>
        </<?php echo esc_html($heading_tag); ?>>
      </header>
    <?php endif; ?>

    <?php if (!empty($items)) : ?>
      <div class="relative mt-8 w-full max-md:mt-0">

        <?php if ($enable_slider) : ?>
          <!-- Slider shell -->
          <div class="relative w-full" data-slick-shell="<?php echo esc_attr($section_id); ?>">
            <div class="matrix-slick" id="<?php echo esc_attr($track_id); ?>" data-slick-root="<?php echo esc_attr($section_id); ?>">
              <?php foreach ($items as $it) :
                $img_url   = $it['img_url'] ?? $default_profile;
                $img_alt   = $it['img_alt'] ?? 'Profile image';
                $title     = $it['title'] ?? '';
                $subtitle  = $it['subtitle'] ?? '';
                $permalink = $it['permalink'] ?? '';
              ?>
                <div class="px-2">
                  <article class="relative h-[520px] overflow-hidden group t-card">
                    <!-- Photo -->
                    <img
                      src="<?php echo esc_url($img_url); ?>"
                      alt="<?php echo esc_attr($img_alt); ?>"
                      class="object-cover absolute inset-0 w-full h-full"
                      loading="lazy"
                      decoding="async"
                    />

                    <!-- Overlay card -->
                    <div class="absolute right-6 bottom-6 left-6">
                      <div class="bg-white shadow-[0_8px_24px_rgba(0,0,0,0.15)] px-6 py-5 w-[320px] max-w-[85vw] w-[-webkit-fill-available]">
                        <?php if ($show_name && !empty($title)) : ?>
                          <div class="text-base font-semibold tracking-normal text-black">
                            <?php echo esc_html($title); ?>
                          </div>
                        <?php endif; ?>

                        <?php if ($show_job_title && !empty($subtitle)) : ?>
                          <div class="mt-1 text-sm leading-5 text-black/70">
                            <?php echo esc_html($subtitle); ?>
                          </div>
                        <?php endif; ?>

                        <?php if (!empty($permalink)) : ?>
                          <a href="<?php echo esc_url($permalink); ?>" class="inline-flex gap-2 items-center mt-4 text-indigo-700 hover:opacity-90">
                            <span class="text-base leading-5">Get in touch</span>
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                              <path d="M5 12H19M19 12L12 5M19 12L12 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                          </a>
                        <?php endif; ?>
                      </div>
                    </div>
                  </article>
                </div>
              <?php endforeach; ?>
            </div>
          </div>

          <!-- Arrows -->
          <?php if ($arrow_enabled): ?>
            <div class="absolute inset-0 pointer-events-none">
              <div class="absolute left-2 top-1/2 z-20 -translate-y-1/2 pointer-events-auto md:left-3 lg:left-4 xl:left-6">
                <button type="button" aria-label="<?php esc_attr_e('Previous team', 'matrix-starter'); ?>"
                  class="flex justify-center items-center w-12 h-12 md:w-14 md:h-14 rounded-full transition-all matrix-prev bg-[#e2e2e2] hover:opacity-90 shadow">
                  <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M19 12H5M5 12L12 19M5 12L12 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
              </div>
              <div class="absolute right-2 top-1/2 z-20 -translate-y-1/2 pointer-events-auto md:right-3 lg:right-4 xl:right-6">
                <button type="button" aria-label="<?php esc_attr_e('Next team', 'matrix-starter'); ?>"
                  class="flex justify-center items-center w-12 h-12 rounded-full shadow transition-all md:w-14 md:h-14 bg-primary matrix-next hover:opacity-90">
                  <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M5 12H19M19 12L12 5M19 12L12 19" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
              </div>
            </div>
          <?php endif; ?>

          <?php if ($dots_enabled): ?>
            <div class="flex gap-4 justify-center items-center mt-6" id="<?php echo esc_attr($dots_id); ?>"></div>
          <?php endif; ?>

          <!-- Peek/clip + opacity dimming -->
          <style>
            #<?php echo esc_attr($section_id); ?> .slick-list { overflow: visible; padding-right: 2rem; }
            @media (min-width:1536px){
              #<?php echo esc_attr($section_id); ?> .slick-list { padding-right: 2.5rem; }
            }
            #<?php echo esc_attr($section_id); ?> [data-slick-shell] {
              clip-path: inset(0 -100vw 0 24px);
              -webkit-clip-path: inset(0 -100vw 0 24px);
            }
            /* Opacity dimming, same behavior as Testimonials */
            #<?php echo esc_attr($section_id); ?> .t-card { opacity: 1; transition: opacity .25s ease; will-change: opacity; }
            #<?php echo esc_attr($section_id); ?> .is-dim .t-card { opacity: .35; }
          </style>

          <script>
          jQuery(function($){
            var $root  = $('#<?php echo esc_js($section_id); ?>');
            var $track = $root.find('[data-slick-root="<?php echo esc_js($section_id); ?>"]');
            var $prev  = $root.find('.matrix-prev');
            var $next  = $root.find('.matrix-next');
            var $dots  = $('#<?php echo esc_js($dots_id); ?>');

            function updateDimming(slick){
              // All slides (real + clones)
              var $all    = $(slick.$slideTrack).children('.slick-slide');
              var $active = $all.filter('.slick-active');
              var isDesktop = window.matchMedia('(min-width: 1280px)').matches;

              // Dim everything, then clear first N visible
              $all.addClass('is-dim');
              var clearCount = isDesktop ? 3 : $active.length;
              $active.slice(0, clearCount).removeClass('is-dim');
            }

            if (!$track.hasClass('slick-initialized') && typeof $track.slick === 'function') {
              $track.on('init reInit afterChange setPosition', function(e, slick){
                updateDimming(slick);
              });

              $track.slick({
                slidesToShow: <?php echo (int) $slides_xl; ?>,
                slidesToScroll: 1,
                infinite: true,
                arrows: <?php echo $arrow_enabled ? 'true' : 'false'; ?>,
                prevArrow: $prev,
                nextArrow: $next,
                dots: <?php echo $dots_enabled ? 'true' : 'false'; ?>,
                appendDots: $dots.length ? $dots : undefined,
                autoplay: <?php echo $autoplay_enabled ? 'true' : 'false'; ?>,
                autoplaySpeed: <?php echo (int) $autoplay_speed; ?>,
                adaptiveHeight: false,
                responsive: [
                  { breakpoint: 1280, settings: { slidesToShow: <?php echo (int) $slides_lg; ?> } },
                  { breakpoint: 1024, settings: { slidesToShow: <?php echo (int) $slides_md; ?> } },
                  { breakpoint: 640,  settings: { slidesToShow: <?php echo (int) $slides_sm; ?> } },
                ]
              });

              // Keep it in sync on resize/orientation change
              $(window).on('resize orientationchange', function(){
                if ($track.hasClass('slick-initialized')) {
                  $track.slick('setPosition');
                }
              });
            }
          });
          </script>

        <?php else : ?>
          <!-- Non-slider grid fallback -->
          <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-4">
            <?php foreach ($items as $it) : ?>
              <article class="relative h-[520px] overflow-hidden group">
                <img
                  src="<?php echo esc_url($it['img_url'] ?? $default_profile); ?>"
                  alt="<?php echo esc_attr($it['img_alt'] ?? 'Profile image'); ?>"
                  class="object-cover absolute inset-0 w-full h-full"
                  loading="lazy"
                  decoding="async"
                />
                <div class="absolute bottom-6 left-6">
                  <div class="bg-white shadow-[0_8px_24px_rgba(0,0,0,0.15)] px-6 py-5 w-[320px] max-w-[85vw]">
                    <?php if ($show_name && !empty($it['title'])) : ?>
                      <div class="text-base font-semibold tracking-normal text-black">
                        <?php echo esc_html($it['title']); ?>
                      </div>
                    <?php endif; ?>
                    <?php if ($show_job_title && !empty($it['subtitle'])) : ?>
                      <div class="mt-1 text-sm leading-5 text-black/70">
                        <?php echo esc_html($it['subtitle']); ?>
                      </div>
                    <?php endif; ?>
                    <?php if (!empty($it['permalink'])) : ?>
                      <a href="<?php echo esc_url($it['permalink']); ?>" class="inline-flex gap-2 items-center mt-4 text-indigo-700 hover:opacity-90">
                        <span class="text-base leading-5">Get in touch</span>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                          <path d="M5 12H19M19 12L12 5M19 12L12 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                      </a>
                    <?php endif; ?>
                  </div>
                </div>
              </article>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

      </div>
    <?php else : ?>
      <p class="mt-8 text-black/70">No team members found.</p>
    <?php endif; ?>

  </div>
</section>
