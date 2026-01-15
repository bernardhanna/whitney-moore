<?php
/**
 * Flexi Block: Partners
 * Template: partners.php
 */

if (!defined('ABSPATH')) {
    exit;
}

$section_id = 'partners-' . wp_generate_uuid4();

$heading_text = get_sub_field('heading_text') ? get_sub_field('heading_text') : 'They trust us';
$heading_tag  = get_sub_field('heading_tag') ? get_sub_field('heading_tag') : 'h2';
$subheading   = get_sub_field('subheading') ? get_sub_field('subheading') : 'Our team advises leading companies in the real estate field';

$background_color = get_sub_field('background_color') ? get_sub_field('background_color') : '#FFFFFF';

$selected_partners = get_sub_field('selected_partners');

$allowed_heading_tags = array('h1','h2','h3','h4','h5','h6','span','p');
if (!in_array($heading_tag, $allowed_heading_tags, true)) {
    $heading_tag = 'h2';
}

/**
 * Padding classes from repeater
 */
$padding_classes = array();

if (have_rows('padding_settings')) {
    while (have_rows('padding_settings')) {
        the_row();

        $screen_size    = get_sub_field('screen_size');
        $padding_top    = get_sub_field('padding_top');
        $padding_bottom = get_sub_field('padding_bottom');

        if ($screen_size && $padding_top !== '' && $padding_top !== null) {
            $padding_classes[] = "{$screen_size}:pt-[{$padding_top}rem]";
        }

        if ($screen_size && $padding_bottom !== '' && $padding_bottom !== null) {
            $padding_classes[] = "{$screen_size}:pb-[{$padding_bottom}rem]";
        }
    }
}

$padding_classes_string = !empty($padding_classes) ? ' ' . implode(' ', $padding_classes) : '';

/**
 * Get partners posts
 */
$partners_posts = array();

if (!empty($selected_partners) && is_array($selected_partners)) {
    // ACF Post Object (multiple) returns array of WP_Post objects
    $partners_posts = $selected_partners;
} else {
    $partners_query = new WP_Query(array(
        'post_type'      => 'partners',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'orderby'        => 'menu_order',
        'order'          => 'ASC',
        'no_found_rows'  => true,
    ));

    if ($partners_query->have_posts()) {
        $partners_posts = $partners_query->posts;
    }
    wp_reset_postdata();
}

// Slider selectors (unique per block)
$slider_id = $section_id . '-slider';
$prev_id   = $section_id . '-prev';
$next_id   = $section_id . '-next';
?>

<section id="<?php echo esc_attr($section_id); ?>" class="flex overflow-hidden relative" style="background-color: <?php echo esc_attr($background_color); ?>;">
    <div class="flex flex-col items-center w-full mx-auto max-w-[1400px] pt-5 pb-5 max-lg:px-5<?php echo esc_attr($padding_classes_string); ?>">

        <header class="flex justify-between items-center mb-8 w-full max-md:flex-col max-md:gap-6 max-md:items-start max-md:mb-6 max-sm:gap-5 max-sm:mb-5">
            <div class="flex flex-col items-start">
                <?php if (!empty($subheading)) : ?>
                    <p class="mb-2 text-lg font-medium tracking-wider text-black max-md:text-base max-sm:text-sm">
                        <?php echo esc_html($subheading); ?>
                    </p>
                <?php endif; ?>

                <<?php echo esc_html($heading_tag); ?> class="text-3xl font-bold tracking-wider leading-10 text-indigo-800 max-md:text-3xl max-md:leading-9 max-sm:text-2xl max-sm:leading-8">
                    <?php echo esc_html($heading_text); ?>
                </<?php echo esc_html($heading_tag); ?>>
            </div>

            <!-- Arrow controls (positioned like your example) -->
            <div class="flex relative gap-3 items-center max-md:self-end w-full justify-between sm:w-auto sm:justify-center">
                <div class="pointer-events-auto">
                    <button
                        id="<?php echo esc_attr($prev_id); ?>"
                        type="button"
                        aria-label="Previous partners"
                        class="flex justify-center items-center w-12 h-12 rounded-full shadow transition-all md:w-14 md:h-14 bg-primary matrix-prev hover:opacity-90"
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
                        aria-label="Next partners"
                        class="flex justify-center items-center w-12 h-12 rounded-full shadow transition-all md:w-14 md:h-14 bg-primary matrix-next hover:opacity-90"
                    >
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M5 12H19M19 12L12 5M19 12L12 19" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </header>

        <div role="region" aria-label="Trusted partner logos" class="w-full">
            <div id="<?php echo esc_attr($slider_id); ?>" class="w-full">
                <?php if (!empty($partners_posts)) : ?>
                    <?php foreach ($partners_posts as $partner_post) : ?>
                        <?php
                        $partner_id = isset($partner_post->ID) ? (int) $partner_post->ID : 0;

                        $thumb_id = $partner_id ? get_post_thumbnail_id($partner_id) : 0;

                        $img_url   = $thumb_id ? wp_get_attachment_image_url($thumb_id, 'large') : '';
                        $img_alt   = $thumb_id ? get_post_meta($thumb_id, '_wp_attachment_image_alt', true) : '';
                        $img_title = $thumb_id ? get_the_title($thumb_id) : '';

                        $fallback_text = $partner_id ? get_the_title($partner_id) : 'Partner logo';

                        if (empty($img_alt)) {
                            $img_alt = $fallback_text;
                        }

                        if (empty($img_title)) {
                            $img_title = $fallback_text;
                        }
                        ?>

                        <div class="px-4">
                            <div class="flex justify-center items-center">
                                <?php if (!empty($img_url)) : ?>
                                    <img
                                        src="<?php echo esc_url($img_url); ?>"
                                        alt="<?php echo esc_attr($img_alt); ?>"
                                        title="<?php echo esc_attr($img_title); ?>"
                                        class="h-auto w-auto max-h-[103px] mix-blend-multiply max-md:max-w-[200px] max-sm:max-w-[160px]"
                                        loading="lazy"
                                    />
                                <?php else : ?>
                                    <span class="text-sm text-black/70">
                                        <?php echo esc_html($fallback_text); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                    <?php endforeach; ?>
                <?php else : ?>
                    <p class="text-black/70">No partners found.</p>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <style>
      #<?php echo esc_attr($slider_id); ?> .slick-track {
       display: flex;
      align-items: center;
      }
       #<?php echo esc_attr($slider_id); ?> .slick-list {
        overflow: hidden !important;
      }
    </style>

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
                    slidesToShow: 5,
                    slidesToScroll: 1,
                    infinite: true,
                    arrows: true,
                    prevArrow: $('#<?php echo esc_js($prev_id); ?>'),
                    nextArrow: $('#<?php echo esc_js($next_id); ?>'),
                    dots: false,
                    autoplay: true,
                    autoplaySpeed: 3500,
                    pauseOnHover: true,
                    pauseOnFocus: true,
                    responsive: [
                        {
                            breakpoint: 1024,
                            settings: {
                                slidesToShow: 3
                            }
                        },
                        {
                            breakpoint: 640,
                            settings: {
                                slidesToShow: 2
                            }
                        }
                    ]
                });
            });
        })(jQuery);
    </script>
</section>
