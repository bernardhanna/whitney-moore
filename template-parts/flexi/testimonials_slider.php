<?php
/**
 * Flexi Block: Testimonials Slider (Slick)
 * - Name = post title, Position = post excerpt, Testimonial = post content (HTML)
 * - Modes: latest | select | manual
 * - No design options pulled; defaults are hard-coded
 */

$section_id = 'testimonials-' . wp_rand(1000, 9999);

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

$slides_xl = (int) get_sub_field('slides_xl') ?: 3;
$slides_lg = (int) get_sub_field('slides_lg') ?: 3;
$slides_md = (int) get_sub_field('slides_md') ?: 2;
$slides_sm = (int) get_sub_field('slides_sm') ?: 1;

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

/** Helper: fetch logo fields from CPT */
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
    $name      = get_the_title($pid);                                          // name
    $position  = get_the_excerpt($pid);                                        // position
    $text_html = apply_filters('the_content', get_post_field('post_content', $pid)); // testimonial
    $image_id  = get_post_thumbnail_id($pid);
    [$logo_img_id, $logo_svg] = _matrix_t_logo($pid);
    $slides[] = compact('name','position','text_html','image_id','logo_img_id','logo_svg');
  }
  wp_reset_postdata();

} elseif ($data_source === 'select' && !empty($selected_posts)) {
  foreach ($selected_posts as $pid) {
    $pid       = (int) $pid;
    $name      = get_the_title($pid);
    $position  = get_the_excerpt($pid);
    $text_html = apply_filters('the_content', get_post_field('post_content', $pid));
    $image_id  = get_post_thumbnail_id($pid);
    [$logo_img_id, $logo_svg] = _matrix_t_logo($pid);
    $slides[] = compact('name','position','text_html','image_id','logo_img_id','logo_svg');
  }

} elseif ($data_source === 'manual' && !empty($manual_items)) {
  foreach ($manual_items as $row) {
    $name        = $row['name'] ?? '';
    $position    = $row['role_title'] ?? '';
    $text_html   = wp_kses_post($row['testimonial_html'] ?? '');
    $image_id    = (int) ($row['photo'] ?? 0);
    $logo_img_id = (int) ($row['logo_image'] ?? 0);
    $logo_svg    = $row['logo_svg'] ?? '';
    $slides[]    = compact('name','position','text_html','image_id','logo_img_id','logo_svg');
  }
}

if (empty($slides)) return;

/** Allowed tags for inline SVG logos */
$allowed_svg = [
  'svg'  => ['xmlns'=>true,'viewBox'=>true,'width'=>true,'height'=>true,'fill'=>true],
  'path' => ['d'=>true,'fill'=>true,'stroke'=>true,'stroke-width'=>true,'fill-rule'=>true,'clip-rule'=>true,'stroke-linecap'=>true,'stroke-linejoin'=>true,'opacity'=>true],
  'g'    => ['clip-path'=>true,'opacity'=>true],
  'defs' => [],
  'clipPath'=>['id'=>true],
  'rect' => ['width'=>true,'height'=>true,'fill'=>true,'x'=>true,'y'=>true,'rx'=>true],
];
?>

<section
  id="<?php echo esc_attr($section_id); ?>"
  class="relative flex overflow-hidden <?php echo esc_attr(implode(' ', $padding_classes)); ?>"
  aria-labelledby="<?php echo esc_attr($section_id); ?>-heading"
>
  <div class="flex flex-col items-center pt-5 pb-5 mx-auto w-full max-w-container max-lg:px-5">

    <!-- Headings -->
    <div class="flex flex-col gap-4 items-start w-full">
      <?php if ($subheading) : ?>
        <div class="text-lg font-medium tracking-wide text-black"><?php echo esc_html($subheading); ?></div>
      <?php endif; ?>

      <?php if ($main_heading) : ?>
        <<?php echo esc_attr($main_heading_tag); ?> id="<?php echo esc_attr($section_id); ?>-heading"
          class="text-primary-blue text-3xl md:text-[32px] font-bold leading-10 tracking-wide">
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
    <div class="mt-8 w-full">
      <div class="matrix-slick" data-slick-root="<?php echo esc_attr($section_id); ?>">
        <?php foreach ($slides as $s) :
          $name        = $s['name'];
          $position    = $s['position'];
          $text_html   = $s['text_html'];
          $image_id    = (int) $s['image_id'];
          $logo_img_id = (int) $s['logo_img_id'];
          $logo_svg    = $s['logo_svg'];
          $img_alt     = $image_id ? (get_post_meta($image_id, '_wp_attachment_image_alt', true) ?: $name) : $name;
          $img_title   = $image_id ? (get_the_title($image_id) ?: $name) : $name;
        ?>
          <div class="px-2">
            <article class="relative h-[456px] rounded-lg overflow-hidden group">
              <?php if ($image_id) :
                echo wp_get_attachment_image($image_id, 'large', false, [
                  'alt' => esc_attr($img_alt), 'title' => esc_attr($img_title),
                  'class' => 'absolute inset-0 w-full h-full object-cover', 'loading' => 'lazy',
                ]);
              endif; ?>

              <div class="absolute right-6 bottom-6 left-6">
                <!-- Fixed overlay defaults (no options) -->
                <div class="relative backdrop-blur-lg bg-white/20 rounded-lg shadow-[0_4px_16px_0_rgba(0,0,0,0.12),0_2px_4px_0_rgba(0,0,0,0.12)] p-6 flex flex-col gap-6">

                  <!-- Company logo -->
                  <div class="flex items-start">
                    <?php
                    if ($logo_img_id) {
                      echo wp_get_attachment_image($logo_img_id, 'medium', false, [
                        'alt' => esc_attr($name . ' logo'), 'class' => 'h-6 w-auto', 'loading' => 'lazy',
                      ]);
                    } elseif (!empty($logo_svg)) {
                      echo wp_kses($logo_svg, $allowed_svg);
                    }
                    ?>
                  </div>

                  <!-- Testimonial text (content) -->
                  <div class="text-neutral-900 text-base font-medium leading-[22px] tracking-tight">
                    <?php echo $text_html; ?>
                  </div>

                  <!-- Name + Position -->
                  <div class="flex flex-col">
                    <div class="text-neutral-900 text-base font-medium leading-[22px] tracking-tight">
                      <?php echo esc_html($name); ?>
                    </div>
                    <?php if (!empty($position)) : ?>
                      <div class="text-sm tracking-tight leading-5 text-black/60">
                        <?php echo esc_html($position); ?>
                      </div>
                    <?php endif; ?>
                  </div>

                </div>
              </div>
            </article>
          </div>
        <?php endforeach; ?>
      </div>

      <?php if ($arrow_enabled || $dots_enabled): ?>
        <div class="flex gap-4 justify-center items-center mt-8" data-slick-controls="<?php echo esc_attr($section_id); ?>">
          <?php if ($arrow_enabled): ?>
            <button type="button" class="flex justify-center items-center w-16 h-16 rounded-full transition-all matrix-prev bg-neutral-grey hover:opacity-90">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M19 12H5M5 12L12 19M5 12L12 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
            <button type="button" class="flex justify-center items-center w-16 h-16 bg-blue-600 rounded-full transition-all matrix-next hover:opacity-90">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M5 12H19M19 12L12 5M19 12L12 19" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
          <?php endif; ?>
          <?php if ($dots_enabled): ?><div class="matrix-dots"></div><?php endif; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <script>
  jQuery(function($){
    var $root  = $('#<?php echo esc_js($section_id); ?>');
    var $track = $root.find('[data-slick-root="<?php echo esc_js($section_id); ?>"]');
    var $ctrls = $root.find('[data-slick-controls="<?php echo esc_js($section_id); ?>"]');
    if (!$track.hasClass('slick-initialized')) {
      $track.slick({
        slidesToShow: <?php echo (int) $slides_xl; ?>,
        slidesToScroll: 1,
        infinite: false,
        arrows: <?php echo $arrow_enabled ? 'true' : 'false'; ?>,
        prevArrow: $ctrls.find('.matrix-prev'),
        nextArrow: $ctrls.find('.matrix-next'),
        dots: <?php echo $dots_enabled ? 'true' : 'false'; ?>,
        appendDots: $ctrls.find('.matrix-dots'),
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
  });
  </script>
</section>
