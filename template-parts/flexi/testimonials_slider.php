<?php
/**
 * Flexi Block: Testimonials Slider (Slick)
 */

$block_context = isset($args['matrix_flexi_context']) ? (string) $args['matrix_flexi_context'] : '';

$section_id = 'testimonials-' . wp_rand(1000, 9999);
$layout_name = get_row_layout();
if (!is_string($layout_name) || $layout_name === '') {
  $layout_name = 'testimonials_slider';
}
$row_index = get_row_index();
if (!is_numeric($row_index)) {
  $row_index = 0;
}

/** Content */
$subheading       = get_sub_field('subheading');
$main_heading     = get_sub_field('main_heading');
$main_heading_tag = get_sub_field('main_heading_tag') ?: 'h2';
$intro_text       = get_sub_field('intro_text');

$data_source      = get_sub_field('data_source') ?: 'latest';
$posts_per_page   = (int) get_sub_field('posts_per_page') ?: 12;
$selected_posts   = (array) get_sub_field('selected_testimonials');
$manual_items     = (array) get_sub_field('manual_testimonials');

/** Slick options */
$arrow_enabled    = (bool) get_sub_field('arrows');
$dots_enabled     = (bool) get_sub_field('dots');
$autoplay_enabled = (bool) get_sub_field('autoplay');
$autoplay_speed   = (int) get_sub_field('autoplay_speed') ?: 5000;

/** Desktop shows 4; smaller breakpoints use ACF values */
$slides_xl = 4;
$slides_lg = (int) get_sub_field('slides_lg') ?: 3;
$slides_md = (int) get_sub_field('slides_md') ?: 2;
$slides_sm = (int) get_sub_field('slides_sm') ?: 1;

if ($block_context === 'why_us_fallback') {
  if (!is_string($subheading) || trim($subheading) === '') {
    $subheading = 'Client feedback';
  }
  if (!is_string($main_heading) || trim($main_heading) === '') {
    $main_heading = 'What our clients say';
  }
  if (!is_string($intro_text) || trim(wp_strip_all_tags($intro_text)) === '') {
    $intro_text = '<p>We are proud to be a long-term partner to leading organisations across Ireland and beyond.</p>';
  }
  $arrow_enabled = true;
  $dots_enabled = true;
  $autoplay_enabled = true;
}

/** Padding classes */
$padding_classes = [];
if (have_rows('padding_settings')) {
  while (have_rows('padding_settings')) {
    the_row();
    $screen = get_sub_field('screen_size');
    $pt     = get_sub_field('padding_top');
    $pb     = get_sub_field('padding_bottom');
    if ($screen !== '' && $pt !== '' && $pb !== '') {
      $padding_classes[] = "{$screen}:pt-[{$pt}rem]";
      $padding_classes[] = "{$screen}:pb-[{$pb}rem]";
    }
  }
}

/** Helper: logo fields on CPT */
function _matrix_t_logo($post_id) {
  $logo_img_id = get_field('logo_image', $post_id);
  $logo_svg    = get_field('logo_svg', $post_id);
  return [$logo_img_id, $logo_svg];
}

/** Collect slides */
$slides = [];

if ($data_source === 'latest') {
  $q = new WP_Query([
    'post_type' => 'testimonial',
    'post_status' => 'publish',
    'posts_per_page' => $posts_per_page,
    'ignore_sticky_posts' => true,
    'orderby' => 'date',
    'order' => 'DESC',
  ]);
  while ($q->have_posts()) { $q->the_post();
    $pid       = get_the_ID();
    $name      = get_the_title($pid);
    $position  = has_excerpt($pid) ? wp_strip_all_tags((string) get_post_field('post_excerpt', $pid)) : '';
    $text_html = apply_filters('the_content', get_post_field('post_content', $pid));
    $image_id  = get_post_thumbnail_id($pid);
    $person_id   = (int) get_post_meta($pid, 'mentioned_person_id', true);
    $person_name = $person_id ? get_the_title($person_id) : '';
    $person_link = $person_id ? get_permalink($person_id) : '';
    [$logo_img_id, $logo_svg] = _matrix_t_logo($pid);
    $slides[] = compact('name','position','text_html','image_id','logo_img_id','logo_svg','person_name','person_link');
  }
  wp_reset_postdata();

} elseif ($data_source === 'select' && !empty($selected_posts)) {
  foreach ($selected_posts as $pid) {
    $pid       = (int) $pid;
    $name      = get_the_title($pid);
    $position  = has_excerpt($pid) ? wp_strip_all_tags((string) get_post_field('post_excerpt', $pid)) : '';
    $text_html = apply_filters('the_content', get_post_field('post_content', $pid));
    $image_id  = get_post_thumbnail_id($pid);
    $person_id   = (int) get_post_meta($pid, 'mentioned_person_id', true);
    $person_name = $person_id ? get_the_title($person_id) : '';
    $person_link = $person_id ? get_permalink($person_id) : '';
    [$logo_img_id, $logo_svg] = _matrix_t_logo($pid);
    $slides[] = compact('name','position','text_html','image_id','logo_img_id','logo_svg','person_name','person_link');
  }

} elseif ($data_source === 'manual' && !empty($manual_items)) {
  foreach ($manual_items as $row) {
    $name        = $row['name'] ?? '';
    $position    = $row['role_title'] ?? '';
    $text_html   = wp_kses_post($row['testimonial_html'] ?? '');
    $image_id    = (int) ($row['photo'] ?? 0);
    $logo_img_id = (int) ($row['logo_image'] ?? 0);
    $logo_svg    = $row['logo_svg'] ?? '';
    $person_name = '';
    $person_link = '';
    $slides[]    = compact('name','position','text_html','image_id','logo_img_id','logo_svg','person_name','person_link');
  }
}

if (empty($slides)) return;

/** Allowed tags for inline SVG logos */
$allowed_svg = [
  'svg'  => ['xmlns'=>true,'xmlns:xlink'=>true,'viewBox'=>true,'width'=>true,'height'=>true,'fill'=>true,'class'=>true],
  'path' => ['d'=>true,'fill'=>true,'stroke'=>true,'stroke-width'=>true,'fill-rule'=>true,'clip-rule'=>true,'stroke-linecap'=>true,'stroke-linejoin'=>true,'opacity'=>true],
  'g'    => ['clip-path'=>true,'opacity'=>true],
  'defs' => [],
  'clipPath'=>['id'=>true],
  'pattern' => ['id'=>true,'patternContentUnits'=>true,'width'=>true,'height'=>true],
  'use' => ['xlink:href'=>true,'href'=>true,'transform'=>true],
  'image' => ['id'=>true,'width'=>true,'height'=>true,'preserveAspectRatio'=>true,'xlink:href'=>true,'href'=>true],
  'rect' => ['width'=>true,'height'=>true,'fill'=>true,'x'=>true,'y'=>true,'rx'=>true],
];
?>

<section
  id="<?php echo esc_attr($section_id); ?>" data-matrix-block="<?php echo esc_attr(str_replace('_', '-', $layout_name) . '-' . $row_index); ?>" 
  class="relative flex overflow-hidden <?php echo esc_attr(implode(' ', $padding_classes)); ?>"
  aria-labelledby="<?php echo esc_attr($section_id); ?>-heading"
>
  <div class="flex flex-col items-center py-10 mx-auto w-full lg:py-20 max-w-container max-xxl:px-5">

    <!-- Headings -->
    <div class="flex flex-col gap-4 items-start w-full">
      <?php if ($subheading) : ?>
        <div class="text-lg font-medium tracking-wide text-black"><?php echo esc_html($subheading); ?></div>
      <?php endif; ?>

      <?php if ($main_heading) : ?>
        <<?php echo esc_attr($main_heading_tag); ?> id="<?php echo esc_attr($section_id); ?>-heading"
          class="text-primary text-3xl md:text-[32px] font-bold leading-10 tracking-wide">
          <?php echo esc_html($main_heading); ?>
        </<?php echo esc_attr($main_heading_tag); ?>>
      <?php endif; ?>

      <?php if ($intro_text) : ?>
        <div class="text-lg tracking-tight leading-6 text-navy-text wp_editor">
          <?php echo wp_kses_post($intro_text); ?>
        </div>
      <?php endif; ?>
    </div>

    <!-- Slider -->
    <div class="relative mt-8 w-full">
      <div class="overflow-visible relative w-full" data-slick-shell="<?php echo esc_attr($section_id); ?>">
        <div class="matrix-slick" data-slick-root="<?php echo esc_attr($section_id); ?>">
          <?php foreach ($slides as $s) :
            $name        = $s['name'];
            $position    = $s['position'];
            $company     = !empty($position) ? $position : $name;
            $text_html   = $s['text_html'];
            $image_id    = (int) $s['image_id'];
            $logo_img_id = (int) $s['logo_img_id'];
            $logo_svg    = $s['logo_svg'];
            $person_name = isset($s['person_name']) ? (string) $s['person_name'] : '';
            $person_link = isset($s['person_link']) ? (string) $s['person_link'] : '';
            $has_logo    = $logo_img_id || !empty($logo_svg);
            $img_alt     = $image_id ? (get_post_meta($image_id, '_wp_attachment_image_alt', true) ?: $name) : $name;
            $img_title   = $image_id ? (get_the_title($image_id) ?: $name) : $name;
            $has_person_link = !empty($person_link);
          ?>
            <div class="px-4">
              <?php if ($has_person_link) : ?>
                <a href="<?php echo esc_url($person_link); ?>" class="block focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/50">
              <?php endif; ?>
              <article class="relative h-[480px] overflow-hidden group t-card flex justify-center items-end p-4">
                <?php if ($image_id) :
                  echo wp_get_attachment_image($image_id, 'full', false, [
                    'alt' => esc_attr($img_alt), 'title' => esc_attr($img_title),
                    'class' => 'absolute inset-0 w-full h-full object-cover object-top', 'loading' => 'lazy',
                  ]);
                endif; ?>

                <div class="relative z-20 w-full">
                  <div class="relative w-full backdrop-blur-lg bg-[#ffffff85] shadow-[0_4px_16px_0_rgba(0,0,0,0.12),0_2px_4px_0_rgba(0,0,0,0.12)] p-6 flex flex-col gap-4">
                    <?php if ($has_logo) : ?>
                      <div class="flex items-start">
                        <?php
                        if ($logo_img_id) {
                          echo wp_get_attachment_image($logo_img_id, 'medium', false, [
                            'alt' => esc_attr($name . ' logo'), 'class' => 'h-6 md:h-7 w-auto object-contain', 'loading' => 'lazy',
                          ]);
                        } elseif (!empty($logo_svg)) {
                          // Some legacy inline SVG logos embed base64 image data, which wp_kses strips.
                          // Render the uploaded file directly for that signature so the logo remains visible.
                          if (strpos($logo_svg, 'image0_3257_141') !== false || strpos($logo_svg, 'data:image/png;base64') !== false) {
                            echo '<img src="' . esc_url('/wp-content/uploads/2025/12/legal-500.svg') . '" alt="' . esc_attr($name . ' logo') . '" class="h-6 md:h-7 w-auto object-contain" style="filter: brightness(0) saturate(100%);" loading="lazy" />';
                          } elseif (strpos($logo_svg, 'width="219"') !== false && strpos($logo_svg, 'height="58"') !== false) {
                            // Chambers logo: prefer file-based SVG here to avoid occasional inline clipping.
                            echo '<img src="' . esc_url('/wp-content/uploads/2025/12/chambers-and-partners-logo.svg') . '" alt="' . esc_attr($name . ' logo') . '" class="h-6 md:h-7 w-auto object-contain" style="filter: brightness(0) saturate(100%);" loading="lazy" />';
                          } else {
                            $render_logo_svg = $logo_svg;
                            // Normalize inline logo visual size across providers.
                            $render_logo_svg = preg_replace('/<svg\b(?![^>]*\bclass=)/i', '<svg class="h-6 md:h-7 w-auto object-contain" ', $render_logo_svg, 1);
                            echo wp_kses($render_logo_svg, $allowed_svg);
                          }
                        }
                        ?>
                      </div>
                    <?php endif; ?>

                    <div class="text-neutral-900 text-base font-medium leading-[22px] tracking-tight">
                      <?php echo $text_html; ?>
                    </div>

                    <?php if (!empty($company)) : ?>
                      <div class="flex flex-col">
                        <div class="text-neutral-900 text-base font-medium leading-[22px] tracking-tight">
                          <?php echo esc_html($company); ?>
                        </div>
                        <?php if (!empty($person_name)) : ?>
                          <div class="mt-1 text-sm tracking-tight leading-5 text-black/70">
                            <?php echo esc_html('About ' . $person_name); ?>
                          </div>
                        <?php endif; ?>
                      </div>
                    <?php endif; ?>
                  </div>
                </div>
              </article>
              <?php if ($has_person_link) : ?>
                </a>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <?php if ($arrow_enabled): ?>
        <div class="absolute inset-0 pointer-events-none -left-[1rem] md:-left-[1rem] xxl:-left-[2.5rem] xl:-left-[1.5rem]">
          <div class="absolute left-2 top-1/2 z-20 -translate-y-1/2 pointer-events-auto md:left-3 lg:left-4 xl:left-6">
            <button type="button" aria-label="<?php esc_attr_e('Previous testimonials', 'matrix-starter'); ?>"
              class="flex justify-center items-center w-12 h-12 md:w-14 md:h-14 rounded-full transition-all matrix-prev bg-[#e2e2e2] hover:opacity-90 shadow">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M19 12H5M5 12L12 19M5 12L12 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
          </div>
          <div class="absolute right-2 top-1/2 z-20 -translate-y-1/2 pointer-events-auto md:right-3 lg:right-4 xl:right-6">
            <button type="button" aria-label="<?php esc_attr_e('Next testimonials', 'matrix-starter'); ?>"
              class="flex justify-center items-center w-12 h-12 rounded-full shadow transition-all md:w-14 md:h-14 bg-primary matrix-next hover:opacity-90">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M5 12H19M19 12L12 5M19 12L12 19" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
          </div>
        </div>
      <?php endif; ?>

      <?php if ($dots_enabled): ?>
        <div class="flex gap-4 justify-center items-center mt-6" data-slick-dots="<?php echo esc_attr($section_id); ?>"></div>
      <?php endif; ?>
    </div>
  </div>

  <style>
    /* Let next slide peek on the right */
    #<?php echo esc_attr($section_id); ?> .slick-list { overflow: visible; padding-right: 2rem; }
    @media (min-width:1536px){
      #<?php echo esc_attr($section_id); ?> .slick-list { padding-right: 2.5rem; }
    }

    /* Hide only the left side via clip-path */
    #<?php echo esc_attr($section_id); ?> [data-slick-shell] {
      clip-path: inset(0 -100vw 0 24px);
      -webkit-clip-path: inset(0 -100vw 0 0px);
    }

    /* --- Opacity dimming ---
       Default: every slide starts dimmed. JS removes dim from the first 3 visible on desktop.
       This fades the ENTIRE slide (image + content card).
    */
    #<?php echo esc_attr($section_id); ?> .t-card { opacity: 1; transition: opacity .25s ease; will-change: opacity; }
    #<?php echo esc_attr($section_id); ?> .is-dim .t-card { opacity: .35; }
  </style>

<script>
jQuery(function($){
  var $root   = $('#<?php echo esc_js($section_id); ?>');
  var $track  = $root.find('[data-slick-root="<?php echo esc_js($section_id); ?>"]');
  var $prev   = $root.find('.matrix-prev');
  var $next   = $root.find('.matrix-next');
  var $dots   = $root.find('[data-slick-dots="<?php echo esc_js($section_id); ?>"]');

  function updateDimming(slick){
    // Select ALL slides in the track (real + cloned), not just slick.$slides
    var $all    = $(slick.$slideTrack).children('.slick-slide');
    var $active = $all.filter('.slick-active');
    var isDesktop = window.matchMedia('(min-width: 1280px)').matches;

    // Start with everything dim
    $all.addClass('is-dim');

    // Desktop: first 3 visible are clear. Smaller screens: all visible are clear.
    var clearCount = isDesktop ? 3 : $active.length;
    $active.slice(0, clearCount).removeClass('is-dim');
  }

  if (!$track.hasClass('slick-initialized')) {
    $track.on('init reInit afterChange setPosition', function(e, slick){
      updateDimming(slick);
    });

    $track.slick({
      slidesToShow: <?php echo (int) $slides_xl; ?>, // 4 on desktop
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
  }

  // Recalc on resize/orientation changes
  $(window).on('resize orientationchange', function(){
    if ($track.hasClass('slick-initialized')) {
      $track.slick('setPosition'); // triggers setPosition -> updateDimming
    }
  });
});
</script>
</section>