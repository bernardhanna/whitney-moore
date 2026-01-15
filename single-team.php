<?php
/**
 * Template: Single Team Member
 */
if (!defined('ABSPATH')) exit;

get_header();
?>
<main class="overflow-hidden w-full site-main">
    <?php
    if (function_exists('load_hero_templates')) {
        load_hero_templates();
    }

    if (have_posts()) :
        while (have_posts()) : the_post();

            // Taxonomies for the sidebar and hero list
            $practice_terms = get_the_terms(get_the_ID(), 'team_practice_area');
            $sector_terms   = get_the_terms(get_the_ID(), 'team_sector');

            $member_name    = get_the_title();

            // -------- ACF (NOT flexible) --------
            // Top hero fields
            $job_title      = function_exists('get_field') ? get_field('job_title') : '';
            $headshot_id    = function_exists('get_field') ? get_field('headshot') : 0;
            $email          = function_exists('get_field') ? get_field('contact_email') : '';
            $phone          = function_exists('get_field') ? get_field('contact_phone') : '';
            $twitter_url    = function_exists('get_field') ? get_field('twitter_url') : '';
            $twitter_handle = function_exists('get_field') ? get_field('twitter_handle') : '';
            $linkedin_url   = function_exists('get_field') ? get_field('linkedin_url') : '';
            $linkedin_handle= function_exists('get_field') ? get_field('linkedin_handle') : '';
            $vcard_file     = function_exists('get_field') ? get_field('vcard_file') : null;
            $vcard_ext_url  = function_exists('get_field') ? get_field('vcard_external_url') : '';

            // vCard URL: prefer uploaded file, else external URL
            $vcard_url = '';
            if (!empty($vcard_file) && !empty($vcard_file['url'])) {
                $vcard_url = $vcard_file['url'];
            } elseif (!empty($vcard_ext_url)) {
                $vcard_url = $vcard_ext_url;
            }

            // Social handle derivation (if handle not provided)
            $display_twitter = '';
            if (!empty($twitter_url)) {
                if (!empty($twitter_handle)) {
                    $display_twitter = $twitter_handle;
                } else {
                    $parts = parse_url($twitter_url);
                    $path  = isset($parts['path']) ? trim($parts['path'], '/') : '';
                    if ($path !== '') {
                        $seg = basename($path);
                        $display_twitter = (strpos($seg, '@') === 0) ? $seg : '@' . $seg;
                    } else {
                        $display_twitter = $twitter_url;
                    }
                }
            }

            $display_linkedin = '';
            if (!empty($linkedin_url)) {
                if (!empty($linkedin_handle)) {
                    $display_linkedin = $linkedin_handle;
                } else {
                    $parts = parse_url($linkedin_url);
                    $path  = isset($parts['path']) ? trim($parts['path'], '/') : '';
                    if ($path !== '') {
                        $seg = basename($path);
                        $display_linkedin = $seg;
                    } else {
                        $display_linkedin = $linkedin_url;
                    }
                }
            }

            // Education + Testimonials
            $education              = function_exists('get_field') ? get_field('education') : '';
            $enable_testimonials    = function_exists('get_field') ? (int) get_field('enable_testimonials') === 1 : false;
            $testimonials_heading   = function_exists('get_field') ? get_field('testimonials_heading') : '';
            $testimonials_repeater  = function_exists('get_field') ? get_field('team_testimonials') : [];

            // Default profile image if no ACF headshot or featured image
            $default_profile_url = '/wp-content/uploads/2025/12/image-2-1.png';
            $img_id  = $headshot_id ? $headshot_id : get_post_thumbnail_id(get_the_ID());
            $img_src = $img_id ? wp_get_attachment_image_url($img_id, 'full') : $default_profile_url;
            $img_alt = $img_id ? (get_post_meta($img_id, '_wp_attachment_image_alt', true) ?: $member_name) : $member_name;

            // Build testimonial slides
            $slides = [];
            if (!empty($testimonials_repeater) && is_array($testimonials_repeater)) {
                foreach ($testimonials_repeater as $row) {
                    $quote  = isset($row['testimonial_text']) ? wp_strip_all_tags($row['testimonial_text']) : '';
                    $source = isset($row['attribution_source']) ? wp_strip_all_tags($row['attribution_source']) : '';
                    $year   = isset($row['attribution_year']) ? wp_strip_all_tags($row['attribution_year']) : '';
                    if ($quote || $source || $year) {
                        $slides[] = [
                            'quote'  => $quote,
                            'source' => $source,
                            'year'   => $year,
                        ];
                    }
                }
            }

            // Slider IDs
            $section_id = 'team-page-' . wp_generate_uuid4();
            $slider_id  = $section_id . '-testimonials';
            $prev_id    = $section_id . '-prev';
            $next_id    = $section_id . '-next';
            ?>

            <!-- TOP HERO (dynamic) -->
            <section class="flex overflow-hidden relative max-lg:mt-[5rem] mt-[5rem]">
                <div class="flex relative items-center self-stretch w-full max-lg:flex-col max-xl:min-h-[auto] xl:min-h-[700px] xl:max-h-[700px]">
                    <!-- Hero Image -->
                    <div class="relative w-full h-full lg:w-1/2 xl:w-2/3 max-md:w-full">
                        <img
                            src="<?php echo esc_url($img_src); ?>"
                            alt="<?php echo esc_attr($img_alt); ?>"
                            class="object-cover w-full h-full"
                            loading="lazy"
                            decoding="async"
                        />
                    </div>

                    <!-- Content Area -->
                    <div class="flex relative flex-col gap-8 items-start self-stretch px-20 py-14 flex-[1_0_0] min-h-[700px] bg-gradient-to-br from-[#0902A4] to-[#3600CE] max-md:px-8 max-md:py-10 max-md:min-h-[auto] max-sm:gap-6 max-sm:px-6 max-sm:py-8 w-full lg:w-1/2  xl:w-1/3">

                        <!-- Name and Title -->
                        <header class="flex relative flex-col gap-1 items-start">
                            <h1 class="relative text-5xl font-bold tracking-wider text-white leading-[58px] max-md:w-full max-md:text-4xl max-md:leading-10 max-sm:text-3xl max-sm:tracking-wide max-sm:leading-9">
                                <?php echo esc_html($member_name); ?>
                            </h1>
                            <?php if (!empty($job_title)) : ?>
                                <p class="relative text-lg leading-6 text-white max-md:w-full max-sm:text-base max-sm:leading-6">
                                    <?php echo esc_html($job_title); ?>
                                </p>
                            <?php endif; ?>
                        </header>

                        <!-- Expertise Section (team_sector terms) -->
                        <?php if (!empty($sector_terms) && !is_wp_error($sector_terms)) : ?>
                            <section class="flex relative flex-col gap-2 items-start self-stretch max-md:gap-3" aria-labelledby="expertise-heading">
                                <h2 id="expertise-heading" class="relative self-stretch text-2xl font-bold leading-8 text-white max-sm:text-xl max-sm:leading-7">
                                    Expert in
                                </h2>
                                <div class="relative self-stretch text-lg leading-8 text-white max-sm:text-base max-sm:leading-7">
                                    <?php foreach ($sector_terms as $t) : ?>
                                        <p><?php echo esc_html($t->name); ?></p>
                                    <?php endforeach; ?>
                                </div>
                            </section>
                        <?php endif; ?>

                        <!-- Contact Information -->
                        <section class="flex relative flex-col gap-2 items-start self-stretch px-8 pt-6 pb-8 border border-white border-solid max-md:px-6 max-md:pt-5 max-md:pb-6 max-sm:gap-3 max-sm:px-5 max-sm:pt-4 max-sm:pb-5" aria-labelledby="contact-heading">
                            <h2 id="contact-heading" class="relative self-stretch text-2xl font-bold leading-8 text-white max-sm:text-xl max-sm:leading-7">
                                <?php echo esc_html(sprintf('Get in touch with %s', $member_name)); ?>
                            </h2>

                            <?php if (!empty($phone)) : ?>
                            <div class="flex relative gap-4 items-center self-stretch max-sm:gap-3">
                                <div class="flex relative justify-center items-center w-6 h-6 max-sm:w-5 max-sm:h-5" aria-hidden="true">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" class="w-6 h-6 max-sm:w-5 max-sm:h-5" aria-hidden="true">
                                        <path d="M21.9999 16.92V19.92C22.0011 20.1985 21.944 20.4741 21.8324 20.7293C21.7209 20.9845 21.5572 21.2136 21.352 21.4018C21.1468 21.5901 20.9045 21.7335 20.6407 21.8227C20.3769 21.9119 20.0973 21.945 19.8199 21.92C16.7428 21.5856 13.7869 20.5341 11.1899 18.85C8.77376 17.3146 6.72527 15.2661 5.18993 12.85C3.49991 10.2412 2.44818 7.27097 2.11993 4.17997C2.09494 3.90344 2.12781 3.62474 2.21643 3.3616C2.30506 3.09846 2.4475 2.85666 2.6347 2.6516C2.82189 2.44653 3.04974 2.28268 3.30372 2.1705C3.55771 2.05831 3.83227 2.00024 4.10993 1.99997H7.10993C7.59524 1.9952 8.06572 2.16705 8.43369 2.48351C8.80166 2.79996 9.04201 3.23942 9.10993 3.71997C9.23656 4.68004 9.47138 5.6227 9.80993 6.52997C9.94448 6.8879 9.9736 7.27689 9.89384 7.65086C9.81408 8.02482 9.6288 8.36809 9.35993 8.63998L8.08993 9.90997C9.51349 12.4135 11.5864 14.4864 14.0899 15.91L15.3599 14.64C15.6318 14.3711 15.9751 14.1858 16.3491 14.1061C16.723 14.0263 17.112 14.0554 17.4699 14.19C18.3772 14.5285 19.3199 14.7634 20.2799 14.89C20.7657 14.9585 21.2093 15.2032 21.5265 15.5775C21.8436 15.9518 22.0121 16.4296 21.9999 16.92Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                                <a href="tel:<?php echo esc_attr(preg_replace('/\s+/', '', $phone)); ?>" class="relative text-lg leading-8 text-white flex-[1_0_0] max-sm:text-base max-sm:leading-7 contact-link">
                                    <?php echo esc_html($phone); ?>
                                </a>
                            </div>
                            <?php endif; ?>

                            <?php if (!empty($email)) : ?>
                            <div class="flex relative gap-4 items-center self-stretch max-sm:gap-3">
                                <div class="flex relative justify-center items-center w-6 h-6 max-sm:w-5 max-sm:h-5" aria-hidden="true">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" class="w-6 h-6 max-sm:w-5 max-sm:h-5" aria-hidden="true">
                                        <path d="M22 6C22 4.9 21.1 4 20 4H4C2.9 4 2 4.9 2 6M22 6V18C22 19.1 21.1 20 20 20H4C2.9 20 2 19.1 2 18V6M22 6L12 13L2 6" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                                <a href="mailto:<?php echo esc_attr($email); ?>" class="relative text-lg leading-8 text-white flex-[1_0_0] max-sm:text-base max-sm:leading-7 contact-link">
                                    <?php echo esc_html($email); ?>
                                </a>
                            </div>
                            <?php endif; ?>

                            <?php if (!empty($twitter_url)) : ?>
                            <div class="flex relative gap-4 items-center self-stretch max-sm:gap-3">
                                <div class="w-6 h-6 max-sm:w-5 max-sm:h-5" aria-hidden="true">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" class="w-6 h-6 max-sm:w-5 max-sm:h-5" aria-hidden="true">
                                        <path d="M17.7508 3H20.8175L14.1175 10.6255L22 21H15.8283L10.995 14.7071L5.46333 21H2.395L9.56167 12.8435L2 3.00083H8.32833L12.6975 8.75273L17.7508 3ZM16.675 19.1729H18.3742L7.405 4.73171H5.58167L16.675 19.1729Z" fill="white"/>
                                    </svg>
                                </div>
                                <a href="<?php echo esc_url($twitter_url); ?>"
                                   target="_blank" rel="noopener noreferrer"
                                   class="relative text-lg leading-8 text-white flex-[1_0_0] max-sm:text-base max-sm:leading-7 contact-link">
                                   <?php echo esc_html($display_twitter); ?>
                                </a>
                            </div>
                            <?php endif; ?>

                            <?php if (!empty($linkedin_url)) : ?>
                            <div class="flex relative gap-4 items-center self-stretch max-sm:gap-3">
                                <div class="w-6 h-6 max-sm:w-5 max-sm:h-5" aria-hidden="true">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" class="w-6 h-6 max-sm:w-5 max-sm:h-5" aria-hidden="true">
                                        <path d="M16 8C17.5913 8 19.1174 8.63214 20.2426 9.75736C21.3679 10.8826 22 12.4087 22 14V21H18V14C18 13.4696 17.7893 12.9609 17.4142 12.5858C17.0391 12.2107 16.5304 12 16 12C15.4696 12 14.9609 12.2107 14.5858 12.5858C14.2107 12.9609 14 13.4696 14 14V21H10V14C10 12.4087 10.6321 10.8826 11.7574 9.75736C12.8826 8.63214 14.4087 8 16 8Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M6 9H2V21H6V9Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M4 6C5.10457 6 6 5.10457 6 4C6 2.89543 5.10457 2 4 2C2.89543 2 2 2.89543 2 4C2 5.10457 2.89543 6 4 6Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                                <a href="<?php echo esc_url($linkedin_url); ?>"
                                   target="_blank" rel="noopener noreferrer"
                                   class="relative text-lg leading-8 text-white flex-[1_0_0] max-sm:text-base max-sm:leading-7 contact-link">
                                   <?php echo esc_html($display_linkedin); ?>
                                </a>
                            </div>
                            <?php endif; ?>

                            <!-- Download vCard Button -->
                            <?php if (!empty($vcard_url)) : ?>
                                <div class="flex relative flex-col gap-2 items-start pt-4 max-sm:pt-3">
                                    <a href="<?php echo esc_url($vcard_url); ?>"
                                       class="flex relative gap-2 justify-center items-center px-8 py-3 whitespace-nowrap bg-white cursor-pointer btn download-btn w-fit max-md:justify-center max-sm:gap-1.5 max-sm:px-6 max-sm:py-3.5"
                                       aria-label="<?php echo esc_attr(sprintf('Download vCard: %s', $member_name)); ?>"
                                       download>
                                        <span class="relative text-base font-semibold tracking-normal leading-4 text-center text-indigo-800 max-sm:text-sm max-sm:leading-4">
                                            Download vCard
                                        </span>
                                        <div class="flex relative justify-center items-center w-4 h-4 max-sm:w-3.5 max-sm:h-3.5" aria-hidden="true">
                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" class="w-4 h-4 max-sm:w-3.5 max-sm:h-3.5">
                                                <path d="M14 10V12.6667C14 13.0203 13.8595 13.3594 13.6095 13.6095C13.3594 13.8595 13.0203 14 12.6667 14H3.33333C2.97971 14 2.64057 13.8595 2.39052 13.6095C2.14048 13.3594 2 13.0203 2 12.6667V10M4.66667 6.66667L8 10M8 10L11.3333 6.66667M8 10V2" stroke="#0902A4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </div>
                                    </a>
                                </div>
                            <?php endif; ?>
                        </section>
                    </div>
                </div>
            </section>

            <!-- PAGE CONTENT + SIDEBAR -->
            <section class="flex overflow-hidden relative">
                <div class="flex flex-col items-center pt-5 pb-5 mx-auto w-full max-w-[1568px] max-lg:px-5">
                    <div class="flex relative gap-20 justify-center items-start self-stretch px-5 pt-20 pb-24 max-lg:flex-col max-md:gap-12 max-md:pb-20 max-sm:gap-8 max-sm:pt-8 max-sm:pb-12">

                        <!-- Left Column - Main Content -->
                        <article class="flex relative flex-col gap-6 items-start w-2/3 max-md:w-full">

                            <!-- Main Heading -->
                            <header class="flex relative flex-col gap-1 items-start self-stretch">
                                <h1 class="relative self-stretch text-5xl font-bold text-indigo-800 leading-[58px] max-md:text-4xl max-md:leading-10 max-sm:text-3xl max-sm:leading-10">
                                    <?php echo esc_html(sprintf('About %s', $member_name)); ?>
                                </h1>
                            </header>

                            <!-- Main Description (post content) -->
                            <div class="relative self-stretch text-lg tracking-wider leading-7 text-black max-sm:text-base max-sm:leading-6 wp_editor">
                                <?php the_content(); ?>
                            </div>

                            <!-- Education (optional WYSIWYG) -->
                            <?php if (!empty($education)) : ?>
                                <section class="flex relative flex-col gap-4 items-start self-stretch pt-4">
                                    <h2 class="relative self-stretch text-3xl font-semibold leading-9 text-indigo-800 max-sm:text-2xl max-sm:leading-8">
                                        Education
                                    </h2>
                                    <div class="relative self-stretch text-lg tracking-wider leading-7 text-black max-sm:text-base max-sm:leading-6 wp_editor">
                                        <?php echo wp_kses_post($education); ?>
                                    </div>
                                </section>
                            <?php endif; ?>

                            
                            <!-- Testimonials (dynamic Slick slider) -->
                                <?php if ($enable_testimonials && !empty($slides)) : ?>
                                  <section class="relative flex w-full overflow-hidden" aria-label="Client testimonials">
                                    <div class="mx-auto flex w-full min-w-0 max-w-container flex-col items-center pt-5 pb-5 max-lg:px-5">
                                      <div class="w-full min-w-0 pt-6 max-w-[920px]">
                                        <header>
                                          <h2 class="text-2xl font-semibold leading-none text-indigo-800">
                                            <?php
                                            $default_heading = sprintf('What they say about %s', $member_name);
                                            echo esc_html($testimonials_heading ?: $default_heading);
                                            ?>
                                          </h2>
                                        </header>

                                        <article
                                          class="mt-4 flex w-full min-w-0 flex-col bg-indigo-400 bg-opacity-20
                                                 py-14 px-5 lg:pl-20 lg:pr-14 max-md:py-10"
                                          role="region"
                                          aria-label="Customer testimonials"
                                        >
                                          <!-- Slider -->
                                          <div id="<?php echo esc_attr($slider_id); ?>" class="w-full min-w-0">
                                            <?php foreach ($slides as $slide) : ?>
                                              <div class="w-full min-w-0">
                                                <div class="relative w-full min-w-0 text-indigo-800">
                                                  <?php if (!empty($slide['quote'])) : ?>
                                                    <blockquote class="relative z-0 w-full min-w-0 text-3xl font-light leading-[52px] max-md:pl-8 max-md:text-2xl max-md:leading-9">
                                                    <img
                                                      src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/quotes.svg'); ?>"
                                                      alt=""
                                                      aria-hidden="true"
                                                      class="absolute left-[-30px] top-[-35px]
                                                             z-0 h-[48px] w-[48px]
                                                             md:left-[-40px]
                                                             max-md:top-[-18px]"
                                                    />

                                                                                                        </span>
                                                                                                        <?php echo esc_html($slide['quote']); ?>
                                                    </blockquote>
                                                  <?php endif; ?>
                                                </div>

                                                <?php if (!empty($slide['source']) || !empty($slide['year'])) : ?>
                                                  <footer class="mt-6 w-full min-w-0 text-lg tracking-wider leading-none text-black">
                                                    <cite class="not-italic">
                                                      <?php if (!empty($slide['source'])) : ?>
                                                        <div class="font-semibold"><?php echo esc_html($slide['source']); ?></div>
                                                      <?php endif; ?>
                                                      <?php if (!empty($slide['year'])) : ?>
                                                        <time datetime="<?php echo esc_attr($slide['year']); ?>">
                                                          <?php echo esc_html($slide['year']); ?>
                                                        </time>
                                                      <?php endif; ?>
                                                    </cite>
                                                  </footer>
                                                <?php endif; ?>
                                              </div>
                                            <?php endforeach; ?>
                                          </div>

                                          <!-- Arrows -->
                                          <?php if (count($slides) > 1) : ?>
                                            <nav class="mt-6 flex items-center self-start gap-4" aria-label="Testimonial navigation">
                                              <button
                                                id="<?php echo esc_attr($prev_id); ?>"
                                                class="btn carousel-button flex items-center justify-center rounded-[1000px] bg-indigo-600 p-2 hover:bg-indigo-700 focus:bg-indigo-700"
                                                type="button"
                                                aria-label="Previous testimonial"
                                              >
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                  <path d="M19 12H5M5 12L12 19M5 12L12 5" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                              </button>

                                              <button
                                                id="<?php echo esc_attr($next_id); ?>"
                                                class="btn carousel-button flex items-center justify-center rounded-[1000px] bg-indigo-600 p-2 hover:bg-indigo-700 focus:bg-indigo-700"
                                                type="button"
                                                aria-label="Next testimonial"
                                              >
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                  <path d="M5 12H19M19 12L12 5M19 12L12 19" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                              </button>
                                            </nav>
                                          <?php endif; ?>
                                        </article>
                                      </div>
                                    </div>
                                  </section>

                                  <?php if (count($slides) > 1) : ?>
                                    <script>
                                      (function($){
                                        $(document).ready(function(){
                                          var $slider = $('#<?php echo esc_js($slider_id); ?>');
                                          if (!$slider.length || typeof $slider.slick !== 'function') return;
                                          if ($slider.hasClass('slick-initialized')) return;

                                          $slider.slick({
                                            slidesToShow: 1,
                                            slidesToScroll: 1,
                                            infinite: true,
                                            arrows: true,
                                            prevArrow: $('#<?php echo esc_js($prev_id); ?>'),
                                            nextArrow: $('#<?php echo esc_js($next_id); ?>'),
                                            dots: false,
                                            adaptiveHeight: true,
                                            autoplay: false,
                                            pauseOnHover: true,
                                            pauseOnFocus: true
                                          });
                                        });
                                      })(jQuery);
                                    </script>
                                  <?php endif; ?>
                                <?php endif; ?>
                                <!-- /Testimonials -->



                        </article>

                        <!-- Right Column - Practice Areas Sidebar -->
                        <aside class="flex relative flex-col gap-6 items-start p-2 w-1/3 max-md:w-full">
                            <h2 class="relative self-stretch text-3xl font-semibold leading-9 text-indigo-800 max-sm:text-2xl max-sm:leading-8">
                                Practice areas
                            </h2>
                            <nav class="flex relative flex-col gap-3 items-start self-stretch" aria-label="Practice areas">
                                <?php if (!empty($practice_terms) && !is_wp_error($practice_terms)) : ?>
                                    <?php foreach ($practice_terms as $term) :
                                        $term_link = get_term_link($term);
                                        if (is_wp_error($term_link)) { continue; } ?>
                                        <a href="<?php echo esc_url($term_link); ?>"
                                           class="practice-pill btn flex relative gap-2 justify-center items-center px-6 py-2.5 border border-indigo-800 border-solid rounded-[100px] max-sm:px-5 max-sm:py-2 w-fit whitespace-nowrap">
                                            <span class="relative text-lg font-semibold leading-6 text-indigo-800 max-sm:text-base max-sm:leading-6">
                                                <?php echo esc_html($term->name); ?>
                                            </span>
                                        </a>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </nav>
                        </aside>

                    </div>
                </div>
            </section>
            <!-- /PAGE CONTENT -->

        <?php endwhile;
    else :
        echo '<p>No content found</p>';
    endif;
?>
<?php
// ---------------------------------------------
// RELATED SECTORS (Grid, 3 columns on desktop)
// Links to CPT `sectors` and shows its featured image
// ---------------------------------------------
$sector_terms = get_the_terms(get_the_ID(), 'team_sector');

if (!empty($sector_terms) && !is_wp_error($sector_terms)) :

  // Helper: find a single `sectors` post by slug first (fast), then by title
  $find_sector_post = function($term) {
    // 1) Try slug
    $by_slug = get_page_by_path($term->slug, OBJECT, 'sectors');
    if ($by_slug instanceof WP_Post) {
      return $by_slug;
    }

    // 2) Fallback: exact title match (case-insensitive)
    $q = new WP_Query([
      'post_type'      => 'sectors',
      'post_status'    => 'publish',
      'posts_per_page' => 1,
      'title'          => $term->name,
      'orderby'        => 'date',
      'order'          => 'DESC',
      'suppress_filters' => true,
    ]);

    if ($q->have_posts()) {
      $post = $q->posts[0];
      wp_reset_postdata();
      return $post;
    }
    wp_reset_postdata();
    return null;
  };

  $fallback_img = '/wp-content/uploads/2025/12/image-2-1.png'; // change if you prefer
  ?>
  <section class="flex overflow-hidden relative px-20 pt-20 pb-24 tracking-wider bg-neutral-100 max-md:px-5" role="region" aria-labelledby="sectors-heading">
    <div class="flex flex-col items-center pt-5 pb-5 mx-auto w-full max-w-container max-lg:px-5">

      <header class="w-full text-3xl font-bold leading-none text-indigo-800 max-md:max-w-full">
        <h2 id="sectors-heading">Related Sectors</h2>
      </header>

      <div class="mt-14 w-full text-xl font-semibold text-zinc-900 max-md:mt-10 max-md:max-w-full">
        <div class="grid grid-cols-1 gap-6 w-full md:grid-cols-2 lg:grid-cols-4" role="list">
          <?php foreach ($sector_terms as $term) :
            $sector_post = $find_sector_post($term);

            if ($sector_post instanceof WP_Post) {
              $card_link = get_permalink($sector_post);
              $img_id    = get_post_thumbnail_id($sector_post->ID);
              $img_html  = '';

              if ($img_id) {
                $img_alt  = get_the_title($sector_post->ID) ?: $term->name;
                $img_html = wp_get_attachment_image(
                  $img_id,
                  'large',
                  false,
                  [
                    'alt'     => esc_attr($img_alt),
                    'class'   => 'object-cover w-full aspect-[1.1]',
                    'loading' => 'lazy',
                    'decoding'=> 'async',
                  ]
                );
              } else {
                // No featured image on the sector post – fallback image
                $img_html = sprintf(
                  '<img src="%s" alt="%s" class="object-cover w-full aspect-[1.1]" loading="lazy" />',
                  esc_url($fallback_img),
                  esc_attr($term->name)
                );
              }
            } else {
              // No matching sectors CPT found – link to the sectors archive (or skip)
              $card_link = get_post_type_archive_link('sectors') ?: home_url('/sectors/');
              $img_html  = sprintf(
                '<img src="%s" alt="%s" class="object-cover w-full aspect-[1.1]" loading="lazy" />',
                esc_url($fallback_img),
                esc_attr($term->name)
              );
            }
            ?>
            <article class="overflow-hidden flex-1" role="listitem">
              <a href="<?php echo esc_url($card_link); ?>" class="block group">
                <div class="w-full">
                  <?php echo $img_html; ?>
                  <div class="overflow-hidden px-4 mt-4 w-full">
                    <h3 class="text-xl font-semibold text-zinc-900 leading-tight tracking-[1px] group-hover:underline">
                      <?php echo esc_html($term->name); ?>
                    </h3>
                  </div>
                </div>
              </a>
            </article>
          <?php endforeach; ?>
        </div>
      </div>

    </div>
  </section>
<?php endif; ?>
<?php
    if (function_exists('load_flexible_content_templates')) {
        load_flexible_content_templates();
    }
    ?>
</main>


<style>

.slick-slide {
  opacity: 0;
  pointer-events: none;
}

.slick-slide.slick-active {
  opacity: 1;
  pointer-events: auto;
}


</style>

<?php get_footer(); ?>