<?php
/**
 * Author Section – Design-matched, with Team featured image > Gravatar fallback
 */

if (!defined('ABSPATH')) exit;

$author_id      = (int) get_post_field('post_author', get_the_ID());
$author_name    = get_the_author_meta('display_name', $author_id);
$post_permalink = get_permalink();
$post_title     = get_the_title();

// Attempt to find Team post where Title === Author Display Name
$team_post      = null;
$team_link      = '';
$profile_img    = ''; // final URL we will render

$team_q = new WP_Query([
    'post_type'           => 'team',
    'posts_per_page'      => 1,
    'post_status'         => 'publish',
    'title'               => $author_name, // exact match param
    'ignore_sticky_posts' => true,
    'no_found_rows'       => true,
]);

if ($team_q->have_posts()) {
    $team_q->the_post();
    $team_post = get_post(get_the_ID());
    $team_link = get_permalink($team_post->ID);

    // Try team featured image first
    $thumb_id = get_post_thumbnail_id($team_post->ID);
    if ($thumb_id) {
        $profile_img = wp_get_attachment_image_url($thumb_id, 'medium_large');
    }
}
wp_reset_postdata();

// Fallbacks
if (empty($team_link)) {
    $team_link = get_author_posts_url($author_id);
}
if (empty($profile_img)) {
    // If no team featured image (or no team match), use author avatar/gravatar
    $profile_img = get_avatar_url($author_id, ['size' => 200]);
}

// Last update date
$modified_ts   = get_post_modified_time('U', true);
$datetime_attr = gmdate('Y-m-d', $modified_ts);
$human_date    = date_i18n('j F Y', $modified_ts);

// Share URLs
$share_url   = rawurlencode($post_permalink);
$share_title = rawurlencode(wp_strip_all_tags($post_title));

$share_facebook = 'https://www.facebook.com/sharer/sharer.php?u=' . $share_url;
$share_twitter  = 'https://twitter.com/intent/tweet?url=' . $share_url . '&text=' . $share_title;
$share_linkedin = 'https://www.linkedin.com/sharing/share-offsite/?url=' . $share_url;
?>

<section class="flex overflow-hidden relative">
  <div class="flex flex-col items-center w-full mx-auto max-w-[1360px] pt-5 pb-5 max-lg:px-5">
    <div class="flex flex-wrap gap-10 justify-between items-center pt-6 w-full text-black">
      <figure class="flex gap-8 items-center min-w-60 max-md:max-w-full">
        <a href="<?php echo esc_url($team_link); ?>" class="block" aria-label="<?php echo esc_attr($author_name); ?>">
          <img
            src="<?php echo esc_url($profile_img); ?>"
            alt="<?php echo esc_attr('Profile photo of ' . $author_name); ?>"
            class="object-cover shrink-0 aspect-square rounded-full w-[140px] h-[140px]"
            loading="lazy"
            decoding="async"
          />
        </a>
        <div class="flex flex-col justify-center min-w-60">
          <h2 class="text-2xl font-semibold leading-none text-black">
            <a href="<?php echo esc_url($team_link); ?>" class="hover:opacity-80">
              <?php echo esc_html(sprintf('by %s', $author_name)); ?>
            </a>
          </h2>
          <time class="mt-2 text-lg leading-none text-black" datetime="<?php echo esc_attr($datetime_attr); ?>">
            <?php echo esc_html(sprintf(__('Last update: %s', 'matrix-starter'), $human_date)); ?>
          </time>
        </div>
      </figure>

      <div class="flex gap-4 items-center text-lg leading-none min-w-60" role="group" aria-label="<?php esc_attr_e('Share this post', 'matrix-starter'); ?>">
        <span class="text-black"><?php esc_html_e('Share this post', 'matrix-starter'); ?></span>

        <a
          href="<?php echo esc_url($share_facebook); ?>"
          class="whitespace-nowrap btn w-fit hover:opacity-80 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-600"
          aria-label="<?php esc_attr_e('Share on Facebook', 'matrix-starter'); ?>"
          target="_blank" rel="noopener"
        >
          <svg class="object-contain w-8 h-8 shrink-0" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
            <path d="M22 12.06C22 6.48 17.52 2 11.94 2 6.36 2 1.88 6.48 1.88 12.06c0 4.99 3.65 9.13 8.43 9.94v-7.03H7.93v-2.91h2.38V9.41c0-2.35 1.4-3.65 3.55-3.65 1.03 0 2.1.18 2.1.18v2.3h-1.18c-1.16 0-1.52.72-1.52 1.46v1.77h2.58l-.41 2.91h-2.17V22c4.78-.81 8.43-4.95 8.43-9.94Z"/>
          </svg>
        </a>

        <a
          href="<?php echo esc_url($share_twitter); ?>"
          class="whitespace-nowrap btn w-fit hover:opacity-80 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-600"
          aria-label="<?php esc_attr_e('Share on X (Twitter)', 'matrix-starter'); ?>"
          target="_blank" rel="noopener"
        >
          <svg class="object-contain w-8 h-8 shrink-0" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
            <path d="M18.244 2H21.5l-7.61 8.71L22.5 22h-6.87l-5.38-6.42L3.98 22H.72l8.17-9.35L1 2h6.98l4.86 5.66L18.25 2Zm-1.2 18.26h1.86L7.04 3.65H5.07l11.97 16.61Z"/>
          </svg>
        </a>

        <a
          href="<?php echo esc_url($share_linkedin); ?>"
          class="whitespace-nowrap btn w-fit hover:opacity-80 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-600"
          aria-label="<?php esc_attr_e('Share on LinkedIn', 'matrix-starter'); ?>"
          target="_blank" rel="noopener"
        >
          <svg class="object-contain w-8 h-8 shrink-0" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
            <path d="M6 9H3v12h3V9Zm.34-6.5A1.84 1.84 0 0 0 4.5 4.34c0 1 .84 1.84 1.84 1.84 1.02 0 1.84-.82 1.84-1.84A1.84 1.84 0 0 0 6.34 2.5ZM21 14.49c0-3.4-1.84-4.99-4.29-4.99-1.98 0-2.87 1.09-3.36 1.86v-1.6H10v12h3.35v-6.68c0-1.76.33-3.46 2.51-3.46 2.16 0 2.19 2.02 2.19 3.56V21H21v-6.51Z"/>
          </svg>
        </a>
      </div>
    </div>
  </div>
</section>
