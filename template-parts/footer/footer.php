<?php
/**
 * Footer Template — 5 columns with menu/CPT fallbacks
 */

function matrix_footer_render_image($image_field, $size = 'full', $fallback_alt = '', $attrs = []) {
    if (!$image_field) return '';
    $id  = is_array($image_field) ? ($image_field['id'] ?? $image_field['ID'] ?? null) : (int) $image_field;
    if (!$id) return '';
    $alt = get_post_meta($id, '_wp_attachment_image_alt', true);
    $attrs = array_merge(['alt' => esc_attr($alt ?: $fallback_alt)], $attrs);
    return wp_get_attachment_image($id, $size, false, $attrs);
}

// Options
$col1_heading = get_field('footer_col1_heading', 'option') ?: 'About us';
$col2_heading = get_field('footer_col2_heading', 'option') ?: 'Sectors';
$col3_heading = get_field('footer_col3_heading', 'option') ?: 'Practice Areas';
$col4_heading = get_field('footer_col4_heading', 'option') ?: 'Knowledge & Insights';

$phone_number  = get_field('phone_number', 'option');
$email_address = get_field('email_address', 'option');
$address       = get_field('address', 'option');
$social_icons  = get_field('social_icons', 'option') ?: [];
$partner_logos = get_field('partner_logos', 'option') ?: [];

$attr_text = get_field('attribution_text', 'option') ?: 'Designed and Developed by';
$attr_link = get_field('attribution_link', 'option');

$bg  = get_field('footer_bg_color', 'option') ?: '#0902A4';

// Padding repeater -> classes
$padding_classes = [];
if (have_rows('padding_settings', 'option')) {
    while (have_rows('padding_settings', 'option')) {
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
$footer_id = 'site-footer-' . wp_rand(1000, 9999);
?>

<footer
  id="<?php echo esc_attr($footer_id); ?>"
  class="flex overflow-hidden relative text-white"
  style="background-color: <?php echo esc_attr($bg); ?>;"
  role="contentinfo"
  aria-label="<?php esc_attr_e('Site footer', 'matrix-starter'); ?>"
>
  <div class="flex flex-col gap-12 justify-center items-center w-full mx-auto max-w-container  py-12 max-md:gap-8 max-md:py-8 max-sm:gap-6 max-xxl:px-5 max-sm:py-6 <?php echo esc_attr(implode(' ', $padding_classes)); ?>">

    <div class="h-px w-[100px] bg-white/20" aria-hidden="true"></div>

    <!-- Top Columns (5) -->
    <nav class="flex gap-8 items-start self-stretch max-md:flex-wrap max-md:gap-6 max-sm:flex-col max-sm:gap-8" aria-label="<?php esc_attr_e('Footer navigation', 'matrix-starter'); ?>">

      <!-- Column 1 -->
      <section class="flex flex-col gap-8 items-start self-stretch flex-[1_0_0] max-md:flex-[1_1_calc(50%_-_12px)] max-md:min-w-[250px] max-sm:flex-none max-sm:w-full">
        <h2 class="text-xl font-semibold tracking-wide leading-5 max-sm:text-lg max-sm:leading-5">
          <?php echo esc_html($col1_heading); ?>
        </h2>
        <div class="flex flex-col gap-6">
          <?php
          wp_nav_menu([
            'theme_location' => 'footer_one',
            'container'      => false,
            'menu_class'     => 'flex flex-col gap-6',
            'fallback_cb'    => false,
            'link_before'    => '<span class="text-base font-light transition-colors duration-200 hover:text-gray-200">',
            'link_after'     => '</span>',
            'depth'          => 1,
          ]);
          ?>
        </div>
      </section>

      <!-- Column 2 (menu or Sectors CPT) -->
      <section class="flex flex-col gap-8 items-start self-stretch flex-[1_0_0] max-md:flex-[1_1_calc(50%_-_12px)] max-md:min-w-[250px] max-sm:flex-none max-sm:w-full">
        <h2 class="text-xl font-semibold tracking-wide leading-5 max-sm:text-lg max-sm:leading-5">
          <?php echo esc_html($col2_heading); ?>
        </h2>
        <div class="flex flex-col gap-6">
          <?php if ( has_nav_menu('footer_two') ) :
            wp_nav_menu([
              'theme_location' => 'footer_two',
              'container'      => false,
              'menu_class'     => 'flex flex-col gap-6',
              'fallback_cb'    => false,
              'link_before'    => '<span class="text-base font-light transition-colors duration-200 hover:text-gray-200">',
              'link_after'     => '</span>',
              'depth'          => 1,
            ]);
          else :
            $sectors_fallback = new WP_Query([
              'post_type'      => 'sectors',
              'post_status'    => 'publish',
              'orderby'        => 'menu_order title',
              'order'          => 'ASC',
              'posts_per_page' => 7,
            ]);
            if ($sectors_fallback->have_posts()) : ?>
              <ul class="flex flex-col gap-6" role="list">
                <?php while ($sectors_fallback->have_posts()) : $sectors_fallback->the_post(); ?>
                  <li><a class="text-base font-light transition-colors duration-200 hover:text-gray-200" href="<?php echo esc_url(get_permalink()); ?>"><?php echo esc_html(get_the_title()); ?></a></li>
                <?php endwhile; ?>
              </ul>
            <?php else : ?>
              <p class="text-base font-light opacity-80"><?php esc_html_e('No sectors available right now.', 'matrix-starter'); ?></p>
            <?php endif; wp_reset_postdata(); endif; ?>
        </div>
      </section>

      <!-- Column 3 (menu or Practice Areas CPT) -->
      <section class="flex flex-col gap-8 items-start self-stretch flex-[1_0_0] max-md:flex-[1_1_calc(50%_-_12px)] max-md:min-w-[250px] max-sm:flex-none max-sm:w-full">
        <h2 class="text-xl font-semibold tracking-wide leading-5 max-sm:text-lg max-sm:leading-5">
          <?php echo esc_html($col3_heading); ?>
        </h2>
        <div class="flex flex-col gap-6">
          <?php if ( has_nav_menu('footer_three') ) :
            wp_nav_menu([
              'theme_location' => 'footer_three',
              'container'      => false,
              'menu_class'     => 'flex flex-col gap-6',
              'fallback_cb'    => false,
              'link_before'    => '<span class="text-base font-light transition-colors duration-200 hover:text-gray-200">',
              'link_after'     => '</span>',
              'depth'          => 1,
            ]);
          else :
            $practice_fallback = new WP_Query([
              'post_type'      => 'practice_areas',
              'post_status'    => 'publish',
              'orderby'        => 'menu_order title',
              'order'          => 'ASC',
              'posts_per_page' => 8,
            ]);
            if ($practice_fallback->have_posts()) : ?>
              <ul class="flex flex-col gap-6" role="list">
                <?php while ($practice_fallback->have_posts()) : $practice_fallback->the_post(); ?>
                  <li><a class="text-base font-light transition-colors duration-200 hover:text-gray-200" href="<?php echo esc_url(get_permalink()); ?>"><?php echo esc_html(get_the_title()); ?></a></li>
                <?php endwhile; ?>
              </ul>
            <?php else : ?>
              <p class="text-base font-light opacity-80"><?php esc_html_e('No practice areas available right now.', 'matrix-starter'); ?></p>
            <?php endif; wp_reset_postdata(); endif; ?>
        </div>
      </section>

      <!-- Column 4 (menu or default KI links) -->
      <section class="flex flex-col gap-8 items-start self-stretch flex-[1_0_0] max-md:flex-[1_1_calc(50%_-_12px)] max-md:min-w-[250px] max-sm:flex-none max-sm:w-full">
        <h2 class="text-xl font-semibold tracking-wide leading-5 max-sm:text-lg max-sm:leading-5">
          <?php echo esc_html($col4_heading); ?>
        </h2>
        <div class="flex flex-col gap-6">
          <?php if ( has_nav_menu('footer_four') ) :
            wp_nav_menu([
              'theme_location' => 'footer_four',
              'container'      => false,
              'menu_class'     => 'flex flex-col gap-6',
              'fallback_cb'    => false,
              'link_before'    => '<span class="text-base font-light transition-colors duration-200 hover:text-gray-200">',
              'link_after'     => '</span>',
              'depth'          => 1,
            ]);
          else : ?>
            <ul class="flex flex-col gap-6" role="list">
              <li><a class="text-base font-light transition-colors duration-200 hover:text-gray-200" href="<?php echo esc_url( home_url('/insights/') ); ?>">Insights</a></li>
              <li><a class="text-base font-light transition-colors duration-200 hover:text-gray-200" href="<?php echo esc_url( home_url('/news/') ); ?>">News</a></li>
              <li><a class="text-base font-light transition-colors duration-200 hover:text-gray-200" href="<?php echo esc_url( home_url('/events/') ); ?>">Events &amp; Webinar</a></li>
              <li><a class="text-base font-light transition-colors duration-200 hover:text-gray-200" href="<?php echo esc_url( home_url('/press-releases/') ); ?>">Press Releases</a></li>
            </ul>
          <?php endif; ?>
        </div>
      </section>

      <!-- Column 5: Get in touch -->
      <section class="flex flex-col gap-8 items-start self-stretch flex-[1_0_0] max-md:flex-[1_1_calc(50%_-_12px)] max-md:min-w-[250px] max-sm:flex-none max-sm:w-full">
        <h2 class="text-xl font-semibold tracking-wide leading-5 max-sm:text-lg max-sm:leading-5">
          <?php esc_html_e('Get in touch', 'matrix-starter'); ?>
        </h2>

        <div class="flex flex-col gap-6">
          <?php if ($phone_number): ?>
            <div class="flex gap-2 items-center">
              <span aria-hidden="true" class="flex-shrink-0">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M12.96 8.666a6.8 6.8 0 0 1-1.32-.34 1.33 1.33 0 0 0-1.64.8l-.147.3c-1.35-.743-2.486-1.879-3.226-3.226l.28-.187a1.33 1.33 0 0 0 .308-1.64 6.98 6.98 0 0 1-.34-1.32A2 2 0 0 0 5.34 1.36h-2a1.99 1.99 0 0 0-2 2.273c.355 2.793 1.63 5.388 3.624 7.375 1.994 1.988 4.594 3.254 7.388 3.6a2 2 0 0 0 2.287-2V10.6a2 2 0 0 0-1.68-1.934z" fill="currentColor"/></svg>
              </span>
              <a href="tel:<?php echo esc_attr(preg_replace('/\s+/', '', $phone_number)); ?>" class="text-base font-light transition-colors duration-200 hover:text-gray-200">
                <?php echo esc_html($phone_number); ?>
              </a>
            </div>
          <?php endif; ?>

          <?php if ($email_address): ?>
            <div class="flex gap-2 items-center">
              <span aria-hidden="true" class="flex-shrink-0">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><rect x="1.6" y="3" width="12.8" height="10.4" rx="1.4" stroke="currentColor" stroke-width="1.2"/><path d="M14 5.2l-5.23 3.34c-.46.294-1.08.294-1.54 0L2 5.2" stroke="currentColor" stroke-width="1.2"/></svg>
              </span>
              <a href="mailto:<?php echo esc_attr($email_address); ?>" class="text-base font-light transition-colors duration-200 hover:text-gray-200">
                <?php echo esc_html($email_address); ?>
              </a>
            </div>
          <?php endif; ?>

          <?php if ($address): ?>
            <div class="flex gap-2 items-start">
              <span aria-hidden="true" class="flex-shrink-0 mt-0.5">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M8 1.6a5.4 5.4 0 0 0-5.4 5.4C2.6 11 8 14.4 8 14.4S13.4 11 13.4 7a5.4 5.4 0 0 0-5.4-5.4Zm0 7.4a2 2 0 1 1 0-4 2 2 0 0 1 0 4Z" fill="currentColor"/></svg>
              </span>
              <address class="text-base not-italic font-light">
                <?php echo wp_kses_post($address); ?>
              </address>
            </div>
          <?php endif; ?>

          <?php if (!empty($social_icons)) : ?>
            <div class="flex flex-col gap-6">
              <h3 class="text-xl font-semibold tracking-wide leading-5"><?php esc_html_e('Follow Us', 'matrix-starter'); ?></h3>
              <div class="flex gap-4 items-center" role="list" aria-label="<?php esc_attr_e('Social media links', 'matrix-starter'); ?>">
                <?php foreach ($social_icons as $social) :
                  $label  = $social['social_label'] ?? 'Social';
                  $link   = $social['social_link'] ?? null;
                  $icon   = $social['social_icon'] ?? 0;
                  $url    = (is_array($link) && !empty($link['url'])) ? $link['url'] : '';
                  $target = (is_array($link) && !empty($link['target'])) ? $link['target'] : '_self';
                  if (!$url || !$icon) continue; ?>
                  <a href="<?php echo esc_url($url); ?>" target="<?php echo esc_attr($target); ?>" rel="noopener"
                     class="relative w-8 h-8 transition-opacity duration-200 btn hover:opacity-80"
                     aria-label="<?php echo esc_attr($label); ?>" role="listitem">
                    <?php echo matrix_footer_render_image($icon, 'thumbnail', $label, ['class' => 'absolute inset-0 w-8 h-8 object-contain']); ?>
                  </a>
                <?php endforeach; ?>
              </div>
            </div>
          <?php endif; ?>
        </div>
      </section>
    </nav>

    <div class="h-px w-[100px] bg-white/20" aria-hidden="true"></div>

    <!-- Partner logos -->
    <?php if (!empty($partner_logos)) : ?>
      <div class="flex gap-14 items-center max-md:flex-wrap max-md:gap-8 max-md:justify-center max-sm:flex-col max-sm:gap-6" role="list" aria-label="<?php esc_attr_e('Partner organizations', 'matrix-starter'); ?>">
        <?php foreach ($partner_logos as $pl) :
          $img = $pl['logo_image'] ?? 0;
          $lnk = $pl['logo_link'] ?? null;
          $alt = $pl['logo_alt'] ?? 'Partner logo';
          $url = (is_array($lnk) && !empty($lnk['url'])) ? $lnk['url'] : '';
          $tgt = (is_array($lnk) && !empty($lnk['target'])) ? $lnk['target'] : '_self';
          if (!$img) continue; ?>
          <div role="listitem">
            <?php if ($url): ?><a href="<?php echo esc_url($url); ?>" target="<?php echo esc_attr($tgt); ?>" rel="noopener"><?php endif; ?>
              <?php echo matrix_footer_render_image($img, 'full', $alt, ['class' => 'h-[58px] w-auto max-sm:h-auto max-sm:max-w-[150px]']); ?>
            <?php if ($url): ?></a><?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <!-- Bottom bar -->
    <div class="flex flex-col gap-6 justify-center items-center self-stretch">
      <div class="flex justify-between items-start w-full max-md:flex-col max-md:gap-4 max-md:items-center">
        <nav class="flex relative gap-7 justify-center items-start max-md:flex-wrap max-md:gap-4 max-md:justify-center max-sm:flex-col max-sm:gap-3 max-sm:text-center" aria-label="<?php esc_attr_e('Legal and policy links', 'matrix-starter'); ?>">
          <p class="text-sm font-medium">
            <?php echo esc_html( sprintf('© %s Whitney Moore. All rights reserved', date('Y')) ); ?>
          </p>
          <?php
          wp_nav_menu([
            'theme_location' => 'copyright',
            'container'      => false,
            'menu_class'     => 'flex gap-7 items-start max-md:flex-wrap max-md:gap-4',
            'fallback_cb'    => false,
            'depth'          => 1,
            'link_before'    => '<span class="text-sm font-medium transition-colors duration-200 hover:text-gray-200">',
            'link_after'     => '</span>',
          ]);
          ?>
        </nav>

        <?php
        $attr_url = (is_array($attr_link) && !empty($attr_link['url'])) ? $attr_link['url'] : '';
        $attr_tgt = (is_array($attr_link) && !empty($attr_link['target'])) ? $attr_link['target'] : '_self';
        $attr_lbl = (is_array($attr_link) && !empty($attr_link['title'])) ? $attr_link['title'] : 'Matrix Internet';
        ?>
        <p class="text-sm font-medium">
          <?php echo esc_html($attr_text); ?>
          <?php if ($attr_url): ?>
            <a href="<?php echo esc_url($attr_url); ?>" target="<?php echo esc_attr($attr_tgt); ?>" rel="noopener" class="underline transition-opacity duration-200 btn hover:opacity-90"><?php echo esc_html($attr_lbl); ?></a>
          <?php else: ?>
            <span class="underline">Matrix Internet</span>
          <?php endif; ?>
        </p>
      </div>
    </div>

  </div>
</footer>

<?php wp_footer(); ?>
