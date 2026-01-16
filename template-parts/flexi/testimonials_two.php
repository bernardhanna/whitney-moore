<?php
if (!defined('ABSPATH')) {
    exit;
}

$background_image_id = get_sub_field('background_image');

// Colors
$background_color       = get_sub_field('background_color') ? get_sub_field('background_color') : '#FFFFFF';
$quote_text_color       = get_sub_field('quote_text_color') ? get_sub_field('quote_text_color') : '#4338ca';
$attribution_text_color = get_sub_field('attribution_text_color') ? get_sub_field('attribution_text_color') : '#000000';

// IDs
$section_id = 'testimonials-' . wp_generate_uuid4();
$prev_id   = $section_id . '-prev';
$next_id   = $section_id . '-next';
$slider_id = $section_id . '-slider';

/**
 * Background image: use default when none is set
 */
$default_bg_url         = '/wp-content/uploads/2025/12/image-1-1.png';
$background_image_alt   = '';
$background_image_title = '';
$background_image_src   = '';

if (!empty($background_image_id)) {
    $background_image_alt   = get_post_meta($background_image_id, '_wp_attachment_image_alt', true);
    $background_image_title = get_the_title($background_image_id);
    $background_image_src   = wp_get_attachment_image_url($background_image_id, 'full');
}

// Fallbacks for alt/title/src
if (empty($background_image_alt)) {
    $background_image_alt = 'Testimonial image';
}
if (empty($background_image_title)) {
    $background_image_title = 'Testimonial image';
}
if (empty($background_image_src)) {
    $background_image_src = $default_bg_url;
}

/**
 * Padding classes (apply to max-w-container wrapper div)
 */
$padding_classes = array();

if (have_rows('padding_settings')) {
    while (have_rows('padding_settings')) {
        the_row();

        $screen_size    = get_sub_field('screen_size');
        $padding_top    = get_sub_field('padding_top');
        $padding_bottom = get_sub_field('padding_bottom');

        if (!empty($screen_size) && $padding_top !== null && $padding_top !== '') {
            $padding_classes[] = "{$screen_size}:pt-[{$padding_top}rem]";
        }
        if (!empty($screen_size) && $padding_bottom !== null && $padding_bottom !== '') {
            $padding_classes[] = "{$screen_size}:pb-[{$padding_bottom}rem]";
        }
    }
}

$padding_classes_string = !empty($padding_classes) ? ' ' . implode(' ', $padding_classes) : '';

/**
 * Build slides (TEXT ONLY)
 */
$slides = array();

$manual_slides = get_sub_field('manual_slides');
$selected_testimonials = get_sub_field('selected_testimonials');

// 1) Manual slides
if (!empty($manual_slides) && is_array($manual_slides)) {
    foreach ($manual_slides as $row) {
        $slides[] = array(
            'testimonial_text'   => isset($row['testimonial_text']) ? $row['testimonial_text'] : '',
            'attribution_source' => isset($row['attribution_source']) ? $row['attribution_source'] : '',
            'attribution_year'   => isset($row['attribution_year']) ? $row['attribution_year'] : '',
        );
    }
}

// 2) Selected CPT posts (fallback if no manual slides)
if (empty($slides) && !empty($selected_testimonials) && is_array($selected_testimonials)) {
    foreach ($selected_testimonials as $post_obj) {
        $post_id = isset($post_obj->ID) ? (int) $post_obj->ID : 0;
        if (!$post_id) {
            continue;
        }

        $quote  = has_excerpt($post_id) ? get_the_excerpt($post_id) : wp_trim_words(wp_strip_all_tags(get_post_field('post_content', $post_id)), 40);
        $source = get_the_title($post_id);
        $year   = get_the_date('Y', $post_id);

        $slides[] = array(
            'testimonial_text'   => $quote,
            'attribution_source' => $source,
            'attribution_year'   => $year,
        );
    }
}

// 3) Single fallback fields (if nothing else)
if (empty($slides)) {
    $testimonial_text   = get_sub_field('testimonial_text');
    $attribution_source = get_sub_field('attribution_source');
    $attribution_year   = get_sub_field('attribution_year');

    if (!empty($testimonial_text) || !empty($attribution_source) || !empty($attribution_year)) {
        $slides[] = array(
            'testimonial_text'   => $testimonial_text ? $testimonial_text : '',
            'attribution_source' => $attribution_source ? $attribution_source : '',
            'attribution_year'   => $attribution_year ? $attribution_year : '',
        );
    }
}

$slide_count = count($slides);
?>

<section
    id="<?php echo esc_attr($section_id); ?>"
    class="relative overflow-hidden md:min-h-[450px]"
    style="background-color: <?php echo esc_attr($background_color); ?>;"
>
    <div class="relative w-full mx-auto max-w-[108rem]">
        <div class="absolute inset-y-0 left-0 w-1/2 lg:min-h-[450px] max-lg:relative max-lg:inset-auto max-lg:w-full max-lg:h-[320px]">
            <img
                src="<?php echo esc_url($background_image_src); ?>"
                alt="<?php echo esc_attr($background_image_alt); ?>"
                title="<?php echo esc_attr($background_image_title); ?>"
                class="object-cover w-full h-full bg-white"
                loading="lazy"
                decoding="async"
            />
        </div>
        <div class="flex flex-col w-full mx-auto max-w-container pt-5 pb-5 max-lg:px-5<?php echo esc_attr($padding_classes_string); ?>">
            <div class="flex items-stretch w-full max-lg:flex-col">

                <div class="hidden w-1/2 md:block"></div>
                <div class="flex flex-col justify-between px-12 py-12 w-full bg-transparent md:w-1/2 max-md:px-6 max-md:py-10 relative -left-[3rem] ">

                    <div id="<?php echo esc_attr($slider_id); ?>" class="w-full" role="region" aria-label="Testimonials">

                        <?php if ($slide_count > 0) : ?>
                            <?php foreach ($slides as $slide) : ?>
                                <?php
                                $quote  = isset($slide['testimonial_text']) ? $slide['testimonial_text'] : '';
                                $source = isset($slide['attribution_source']) ? $slide['attribution_source'] : '';
                                $year   = isset($slide['attribution_year']) ? $slide['attribution_year'] : '';
                                ?>

                                <div class="pl-12 w-full max-md:pl-0">
                                    <div class="relative max-w-[36rem] w-full">
                                        <svg class="absolute -top-6 -left-24 pointer-events-none text-[120px] leading-none font-black" width="86" height="68" viewBox="0 0 86 68" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M36.8 1.57356e-05L23.6 48L19.2 30C24.9333 30 29.6 31.6667 33.2 35C36.8 38.3333 38.6 42.9333 38.6 48.8C38.6 54.5334 36.7333 59.2 33 62.8C29.4 66.2667 24.8666 68 19.4 68C13.8 68 9.13329 66.2667 5.39995 62.8C1.79995 59.2 -4.84586e-05 54.5334 -4.84586e-05 48.8C-4.84586e-05 47.0667 0.133285 45.4 0.399952 43.8C0.666618 42.0667 1.19995 40.0667 1.99995 37.8C2.79995 35.5333 3.93328 32.5333 5.39995 28.8L17 1.57356e-05H36.8ZM84 1.57356e-05L70.8 48L66.4 30C72.1333 30 76.8 31.6667 80.4 35C84 38.3333 85.8 42.9333 85.8 48.8C85.8 54.5334 83.9333 59.2 80.2 62.8C76.6 66.2667 72.0666 68 66.6 68C61 68 56.3333 66.2667 52.6 62.8C49 59.2 47.2 54.5334 47.2 48.8C47.2 47.0667 47.3333 45.4 47.6 43.8C47.8666 42.0667 48.4 40.0667 49.2 37.8C50 35.5333 51.1333 32.5333 52.6 28.8L64.2 1.57356e-05H84Z" fill="#0902A4"/>
                                        </svg>

                                       

                                        <?php if (!empty($quote)) : ?>
                                            <blockquote
                                                class="relative z-10 text-4xl italic font-light leading-tight max-md:text-2xl"
                                                style="color: <?php echo esc_attr($quote_text_color); ?>;"
                                            >
                                                <?php echo esc_html($quote); ?>
                                            </blockquote>
                                        <?php endif; ?>

                                        <?php if (!empty($source) || !empty($year)) : ?>
                                            <div class="mt-8 text-lg tracking-wider leading-none" style="color: <?php echo esc_attr($attribution_text_color); ?>;">
                                                <?php if (!empty($source)) : ?>
                                                    <div class="font-semibold"><?php echo esc_html($source); ?></div>
                                                <?php endif; ?>
                                                <?php if (!empty($year)) : ?>
                                                    <div class="mt-2"><?php echo esc_html($year); ?></div>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                            <?php endforeach; ?>
                        <?php else : ?>
                            <p class="text-black/70">No testimonials found.</p>
                        <?php endif; ?>

                    </div>

                    <?php if ($slide_count > 1) : ?>
                        <div class="flex relative gap-3 items-center mt-10" aria-label="Testimonial navigation" role="navigation">
                            <div class="pointer-events-auto">
                                <button
                                    id="<?php echo esc_attr($prev_id); ?>"
                                    type="button"
                                    aria-label="Previous testimonial"
                                    class="flex justify-center items-center w-12 h-12 rounded-full shadow transition-all md:w-14 md:h-14 bg-primary matrix-prev hover:opacity-90 slick-arrow"
                                >
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M19 12H5M5 12L12 19M5 12L12 5" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                    </svg>
                                </button>
                            </div>

                            <div class="pointer-events-auto">
                                <button
                                    id="<?php echo esc_attr($next_id); ?>"
                                    type="button"
                                    aria-label="Next testimonial"
                                    class="flex justify-center items-center w-12 h-12 rounded-full shadow transition-all md:w-14 md:h-14 bg-primary matrix-next hover:opacity-90 slick-arrow"
                                >
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M5 12H19M19 12L12 5M19 12L12 19" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>

    </div>

    <?php if ($slide_count > 1) : ?>
        <script>
            (function($) {
                $(document).ready(function() {
                    var $slider = $('#<?php echo esc_js($slider_id); ?>');

                    if (!$slider.length || typeof $slider.slick !== 'function') {
                        return;
                    }

                    if ($slider.hasClass('slick-initialized')) {
                        return;
                    }

                    $slider.slick({
                        slidesToShow: 1,
                        slidesToScroll: 1,
                        infinite: true,
                        arrows: true,
                        prevArrow: $('#<?php echo esc_js($prev_id); ?>'),
                        nextArrow: $('#<?php echo esc_js($next_id); ?>'),
                        dots: false,
                        adaptiveHeight: true,
                        variableWidth: false,
                        autoplay: false,
                        pauseOnHover: true,
                        pauseOnFocus: true
                    });
                });
            })(jQuery);
        </script>
    <?php endif; ?>
</section>

<style>
  #<?php echo esc_attr($section_id); ?> #<?php echo esc_attr($slider_id); ?> .slick-list {
    overflow: hidden !important;
  }
  #<?php echo esc_attr($section_id); ?> #<?php echo esc_attr($slider_id); ?> .slick-track {
    display: flex;
  }
  #<?php echo esc_attr($section_id); ?> #<?php echo esc_attr($slider_id); ?> .slick-slide {
    height: auto;
  }
  #<?php echo esc_attr($section_id); ?> #<?php echo esc_attr($slider_id); ?> blockquote {
    max-width: 100%;
    overflow-wrap: anywhere;
    word-break: break-word;
  }
</style>
