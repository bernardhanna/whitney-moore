<?php
get_header();
?>
<?php
if (function_exists('load_hero_templates')) {
    load_hero_templates();
}
?>
<main class="overflow-hidden w-full site-main">

  <?php if (have_posts()) : ?>
    <?php while (have_posts()) : the_post(); ?>

      <?php
      // Featured image
      $thumb_id  = get_post_thumbnail_id(get_the_ID());
      $hero_src  = $thumb_id ? wp_get_attachment_image_url($thumb_id, 'full') : '';
      $hero_alt  = $thumb_id ? get_post_meta($thumb_id, '_wp_attachment_image_alt', true) : '';
      if (!$hero_alt) { $hero_alt = get_the_title(); }

      // Breadcrumbs
      $home_url      = home_url('/');
      $home_label    = __('HOME', 'matrix-starter');

      $posts_page_id = (int) get_option('page_for_posts');
      if ($posts_page_id) {
          $news_url   = get_permalink($posts_page_id);
          $news_label = get_the_title($posts_page_id);
      } else {
          // Fallback to PT archive or a simple label
          $news_url   = get_post_type_archive_link('post');
          $news_label = $news_url ? __('News', 'matrix-starter') : __('NEWS', 'matrix-starter');
      }

      // Primary category: Yoast primary category if present, else first category
      $cat_current = null;
      if (class_exists('WPSEO_Primary_Term')) {
          $primary_term = new WPSEO_Primary_Term('category', get_the_ID());
          $primary_id   = (int) $primary_term->get_primary_term();
          if ($primary_id) { $cat_current = get_term($primary_id); }
      }
      if (!$cat_current) {
          $cats = get_the_category();
          if (!empty($cats) && !is_wp_error($cats)) {
              $cat_current = $cats[0];
          }
      }
      $cat_name = $cat_current ? $cat_current->name : '';
      $cat_url  = $cat_current ? get_category_link($cat_current) : '';

      // Intro: use excerpt if available
      $intro = has_excerpt() ? get_the_excerpt() : '';
      ?>

      <div class="flex flex-col justify-center self-stretch px-48 pt-20 pb-32 bg-white max-md:px-5 max-md:pb-24">
        <article class="w-full bg-white shadow-lg max-md:max-w-full max-w-[1360px] mx-auto">

          <header>
            <?php if (!empty($hero_src)) : ?>
              <img
                src="<?php echo esc_url($hero_src); ?>"
                alt="<?php echo esc_attr($hero_alt); ?>"
                class="object-contain w-full aspect-[2.27] max-md:max-w-full"
                loading="eager"
                decoding="async"
              />
            <?php endif; ?>
          </header>

          <div class="px-24 pt-14 pb-20 w-full bg-white max-md:px-5 max-md:max-w-full">
            <div class="flex flex-col w-full max-md:max-w-full">

              <!-- Breadcrumb -->
              <nav aria-label="<?php esc_attr_e('Breadcrumb', 'matrix-starter'); ?>"
                   class="flex gap-2 items-center self-start text-base font-medium tracking-wider text-black whitespace-nowrap">
                <a href="<?php echo esc_url($home_url); ?>"
                   class="self-stretch my-auto text-black hover:underline focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                  <?php echo esc_html($home_label); ?>
                </a>

                <!-- divider -->
                <svg class="object-contain self-stretch my-auto w-6 h-6 shrink-0" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                  <path d="M9 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>

                <?php if (!empty($news_url)) : ?>
                  <a href="<?php echo esc_url($news_url); ?>"
                     class="self-stretch my-auto text-black hover:underline focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                    <?php echo esc_html(mb_strtoupper($news_label)); ?>
                  </a>
                <?php else : ?>
                  <span class="self-stretch my-auto text-black">
                    <?php echo esc_html(mb_strtoupper($news_label)); ?>
                  </span>
                <?php endif; ?>

                <?php if (!empty($cat_name)) : ?>
                  <svg class="object-contain self-stretch my-auto w-6 h-6 shrink-0" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M9 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>

                  <?php if (!empty($cat_url)) : ?>
                    <a href="<?php echo esc_url($cat_url); ?>"
                       class="self-stretch my-auto text-black hover:underline focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary"
                       aria-current="page">
                      <?php echo esc_html($cat_name); ?>
                    </a>
                  <?php else : ?>
                    <span class="self-stretch my-auto text-black" aria-current="page">
                      <?php echo esc_html($cat_name); ?>
                    </span>
                  <?php endif; ?>
                <?php endif; ?>
              </nav>

              <!-- Title -->
              <h1 class="mt-4 text-6xl font-bold text-primary leading-[68px] max-md:max-w-full max-md:text-4xl max-md:leading-[52px]">
                <?php the_title(); ?>
              </h1>

              <!-- Intro (optional excerpt) -->
              <?php if (!empty($intro)) : ?>
                <p class="mt-4 text-lg tracking-wider leading-7 text-black max-md:max-w-full">
                  <?php echo esc_html($intro); ?>
                </p>
              <?php endif; ?>
            </div>

            <!-- Post Content -->
            <div class="mt-12 w-full max-md:mt-10 max-md:max-w-full wp_editor">
              <?php the_content(); ?>
            </div>
          </div>
        </article>
      </div>

    <?php endwhile; ?>
  <?php else : ?>
    <p><?php esc_html_e('No content found', 'matrix-starter'); ?></p>
  <?php endif; ?>

  <?php 
  // Only show author + related on posts
  if (get_post_type() === 'post') : 
      get_template_part('template-parts/single/author');
  endif; 
  ?>
</main>
  <?php 
  // Only show author + related on posts
  if (get_post_type() === 'post') : 
      get_template_part('template-parts/single/related-posts');
  endif; 
  ?>
<?php
// Keep Flexible Content loader after the main content if your theme requires it
if (function_exists('load_flexible_content_templates')) {
    load_flexible_content_templates();
}
get_footer();
