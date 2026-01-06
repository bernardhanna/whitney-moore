<?php
/**
 * 404 Template – Hero Card Design
 */
if (!defined('ABSPATH')) exit;

get_header();

// Pull entire option group
$opts = get_field('not_found_settings', 'option') ?: [];

/** Content with sensible defaults (matches your design) */
$eyebrow        = $opts['eyebrow'] ?? 'WHITNEY MOORE';
$big_heading    = $opts['hero_big_heading'] ?? "Experience.<br>Clarity.<br>Results.";
$intro_text     = $opts['hero_intro'] ?? 'Driven by values since 1882.';

// Buttons (ACF Link array) with defaults
$btn_primary    = $opts['primary_button'] ?? [
    'url' => home_url('/practice-areas/'),
    'title' => 'View Practice Areas',
    'target' => '',
];
$btn_secondary  = $opts['secondary_button'] ?? [
    'url' => home_url('/about-us/'),
    'title' => 'About Us',
    'target' => '',
];

// Background image (ACF image ID). Fallback to your provided path.
$bg_id          = isset($opts['hero_background']) ? (int) $opts['hero_background'] : 0;
$bg_alt         = 'Hero background image';
$bg_url         = '';
$bg_srcset      = '';
$bg_sizes       = '(max-width: 1580px) 100vw, 1580px';

// Default fallback URL from your example
$bg_fallback = '/wp-content/uploads/2025/12/hero-background.png';

// Get WP image if set
if ($bg_id) {
    $bg_url   = wp_get_attachment_image_url($bg_id, 'full');
    $bg_srcset= wp_get_attachment_image_srcset($bg_id, 'full') ?: '';
    $meta_alt = get_post_meta($bg_id, '_wp_attachment_image_alt', true);
    if (!empty($meta_alt)) $bg_alt = $meta_alt;
}

// Unique ID
$section_id = 'hero_' . wp_generate_uuid4();
?>
<section
  id="<?php echo esc_attr($section_id); ?>"
  class="flex overflow-hidden relative max-sm:flex-col"
  role="banner"
  aria-labelledby="<?php echo esc_attr($section_id); ?>-heading"
>

  <?php if ($bg_id && $bg_url) : ?>
    <img
      src="<?php echo esc_url($bg_url); ?>"
      alt="<?php echo esc_attr($bg_alt); ?>"
      class="object-cover relative inset-0 sm:absolute size-full"
      decoding="async"
      fetchpriority="high"
      srcset="<?php echo esc_attr($bg_srcset); ?>"
      sizes="<?php echo esc_attr($bg_sizes); ?>"
    />
  <?php else : ?>
    <img
      width="1580" height="889"
      src="<?php echo esc_url($bg_fallback); ?>"
      class="object-cover relative inset-0 sm:absolute size-full"
      alt="<?php echo esc_attr($bg_alt); ?>"
      aria-hidden="true"
      decoding="async"
      fetchpriority="high"
    />
  <?php endif; ?>

  <div class="gap-2 items-center flex justify-end max-w-container mx-auto lg:min-h-[878px] sm:min-h-[600px] md:min-h-[800px] max-sm:py-5 px-5 w-full ">
    <div class="flex relative flex-col justify-center self-stretch p-[2px] my-auto border-solid min-w-60 w-full md:w-[627px] max-w-full"
         style="border-color:#0902a4;border-width:5px;">
      <div class="flex flex-col p-16 w-full border-solid max-md:p-5 max-md:max-w-full"
           style="border-color:#0902a4;border-width:3px;background-color:#ffffff;">
        <div class="w-full tracking-wider max-md:max-w-full">
          <div class="w-full max-md:max-w-full">
            <?php if (!empty($eyebrow)) : ?>
              <p class="max-md:text-[1rem] text-lg font-medium tracking-[1px]" style="color:#000000;">
                <?php echo esc_html($eyebrow); ?>
              </p>
            <?php endif; ?>

            <h1 id="<?php echo esc_attr($section_id); ?>-heading"
                class="mt-2 text-[68px] font-bold leading-[78px] max-md:max-w-full max-md:text-4xl max-md:leading-[51px] break-words overflow-wrap-anywhere max-mob:text-[2rem] max-mob:tracking-[1px] max-mob:leading-[2.625rem]"
                style="color:#0902a4;">
              <?php echo wp_kses_post($big_heading); ?>
            </h1>
          </div>

          <?php if (!empty($intro_text)) : ?>
            <div class="mt-4 text-2xl font-medium max-md:max-w-full wp_editor max-sm:text-[1.125rem] tracking-[1px] leading-[1.625rem]" style="color:#000000;">
              <?php echo wp_kses_post(wpautop($intro_text)); ?>
            </div>
          <?php endif; ?>
        </div>

        <div class="flex gap-3 items-start self-start mt-14 text-xl leading-none max-md:mt-10 max-md:max-w-full max-sm:flex-col max-sm:w-full"
             role="group" aria-label="Call to action buttons">

          <?php if (!empty($btn_primary['url']) && !empty($btn_primary['title'])) : ?>
            <a href="<?php echo esc_url($btn_primary['url']); ?>"
               class="flex gap-2 justify-center items-center px-8 py-5 tracking-wide text-center text-white bg-primary min-h-14 w-fit whitespace-nowrap shadow-[10px_14px_24px_rgba(0,0,0,0.25)] max-md:px-5 btn hover:opacity-90 transition-opacity duration-200 max-sm:w-full"
               target="<?php echo esc_attr($btn_primary['target'] ?? ''); ?>"
               aria-label="<?php echo esc_attr($btn_primary['title']); ?>">
              <span class="self-stretch my-auto">
                <?php echo esc_html($btn_primary['title']); ?>
              </span>
              <svg class="object-contain self-stretch my-auto w-4 shrink-0" width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                <path d="M3 8H13M13 8L8 3M13 8L8 13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
              </svg>
            </a>
          <?php endif; ?>

          <?php if (!empty($btn_secondary['url']) && !empty($btn_secondary['title'])) : ?>
            <a href="<?php echo esc_url($btn_secondary['url']); ?>"
               class="flex gap-2 justify-center items-center px-8 py-5 text-[#1D4ED8] bg-[#DBEAFE] min-h-14 w-fit whitespace-nowrap shadow-[20px_14px_24px_rgba(0,0,0,0.08)] max-md:px-5 btn hover:opacity-90 transition-opacity duration-200 max-sm:w-full"
               target="<?php echo esc_attr($btn_secondary['target'] ?? ''); ?>"
               aria-label="<?php echo esc_attr($btn_secondary['title']); ?>">
              <span class="self-stretch my-auto">
                <?php echo esc_html($btn_secondary['title']); ?>
              </span>
            </a>
          <?php endif; ?>

        </div>
      </div>
    </div>
  </div>
</section>

<?php
get_footer(); ?>
