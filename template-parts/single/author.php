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
  <div class="flex flex-col items-center w-full mx-auto max-w-[1360px] pt-8 pb-5 lg:pb-32 max-lg:px-5">
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
            <a href="<?php echo esc_url($team_link); ?>" class="hover:opacity-50">
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
          class="whitespace-nowrap btn w-fit hover:opacity-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary"
          aria-label="<?php esc_attr_e('Share on Facebook', 'matrix-starter'); ?>"
          target="_blank" rel="noopener"
        >
          <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32" fill="none">
          <path d="M21.333 10.6667C23.4547 10.6667 25.4896 11.5096 26.9899 13.0099C28.4902 14.5102 29.333 16.545 29.333 18.6667V28.0001H23.9997V18.6667C23.9997 17.9595 23.7187 17.2812 23.2186 16.7811C22.7185 16.281 22.0403 16.0001 21.333 16.0001C20.6258 16.0001 19.9475 16.281 19.4474 16.7811C18.9473 17.2812 18.6663 17.9595 18.6663 18.6667V28.0001H13.333V18.6667C13.333 16.545 14.1759 14.5102 15.6762 13.0099C17.1764 11.5096 19.2113 10.6667 21.333 10.6667Z" stroke="#0902A4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M8.00033 12H2.66699V28H8.00033V12Z" stroke="#0902A4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M5.33366 8.00008C6.80642 8.00008 8.00033 6.80617 8.00033 5.33341C8.00033 3.86066 6.80642 2.66675 5.33366 2.66675C3.8609 2.66675 2.66699 3.86066 2.66699 5.33341C2.66699 6.80617 3.8609 8.00008 5.33366 8.00008Z" stroke="#0902A4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </a>

        <a
          href="<?php echo esc_url($share_twitter); ?>"
          class="whitespace-nowrap btn w-fit hover:opacity-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary"
          aria-label="<?php esc_attr_e('Share on X (Twitter)', 'matrix-starter'); ?>"
          target="_blank" rel="noopener"
        >
         <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32" fill="none">
          <path d="M21.7508 7H24.8175L18.1175 14.6255L26 25H19.8283L14.995 18.7071L9.46333 25H6.395L13.5617 16.8435L6 7.00083H12.3283L16.6975 12.7527L21.7508 7ZM20.675 23.1729H22.3742L11.405 8.73171H9.58167L20.675 23.1729Z" fill="#0902A4"/>
        </svg>
        </a>

        <a
          href="<?php echo esc_url($share_linkedin); ?>"
          class="whitespace-nowrap btn w-fit hover:opacity-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary"
          aria-label="<?php esc_attr_e('Share on LinkedIn', 'matrix-starter'); ?>"
          target="_blank" rel="noopener"
        >
          <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32" fill="none">
          <path d="M23.9997 2.66675H19.9997C18.2316 2.66675 16.5359 3.36913 15.2856 4.61937C14.0354 5.86961 13.333 7.5653 13.333 9.33341V13.3334H9.33301V18.6667H13.333V29.3334H18.6663V18.6667H22.6663L23.9997 13.3334H18.6663V9.33341C18.6663 8.97979 18.8068 8.64065 19.0569 8.39061C19.3069 8.14056 19.6461 8.00008 19.9997 8.00008H23.9997V2.66675Z" stroke="#0902A4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </a>
      </div>
    </div>
  </div>
</section>
