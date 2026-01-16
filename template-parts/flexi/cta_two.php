<?php
// Get ACF fields
$heading          = get_sub_field('heading');
$heading_tag      = get_sub_field('heading_tag');
$profile_image    = get_sub_field('profile_image');
$button           = get_sub_field('button');
$background_color = get_sub_field('background_color');
$background_image = get_sub_field('background_image');

// -------------------------------------------------
// Make image fields robust (ACF can return ID or Array)
// -------------------------------------------------
$profile_image_id = 0;
if (is_array($profile_image) && !empty($profile_image['ID'])) {
    $profile_image_id = (int) $profile_image['ID'];
} elseif (is_numeric($profile_image)) {
    $profile_image_id = (int) $profile_image;
}

$background_image_id = 0;
if (is_array($background_image) && !empty($background_image['ID'])) {
    $background_image_id = (int) $background_image['ID'];
} elseif (is_numeric($background_image)) {
    $background_image_id = (int) $background_image;
}

// -------------------------------------------------
// Alts (derive from media where available)
// -------------------------------------------------
$profile_image_alt = $profile_image_id
    ? (get_post_meta($profile_image_id, '_wp_attachment_image_alt', true) ?: 'Profile image')
    : 'Profile image';

$background_image_alt = $background_image_id
    ? (get_post_meta($background_image_id, '_wp_attachment_image_alt', true) ?: 'Background decoration')
    : 'Background decoration';

// Generate unique section ID
$section_id = 'cta-two-' . uniqid();

// Build padding classes
$padding_classes = [];
if (have_rows('padding_settings')) {
    while (have_rows('padding_settings')) {
        the_row();
        $screen_size    = get_sub_field('screen_size');
        $padding_top    = get_sub_field('padding_top');
        $padding_bottom = get_sub_field('padding_bottom');

        if ($screen_size !== '' && $screen_size !== null) {
            if ($padding_top !== '' && $padding_top !== null) {
                $padding_classes[] = "{$screen_size}:pt-[{$padding_top}rem]";
            }
            if ($padding_bottom !== '' && $padding_bottom !== null) {
                $padding_classes[] = "{$screen_size}:pb-[{$padding_bottom}rem]";
            }
        }
    }
}

// ---- FALLBACKS (do not change design) ----

// Default profile image (only if no image set) — DYNAMIC (works on local/staging/prod + subfolders)
$default_profile_url = home_url('/wp-content/uploads/2025/12/image-2-1.png');

$profile_img_src = $profile_image_id
    ? wp_get_attachment_image_url($profile_image_id, 'full')
    : $default_profile_url;

// Default CTA link (only if empty)
if (empty($button) || !is_array($button) || empty($button['url']) || empty($button['title'])) {
    $button = [
        'url'    => home_url('/contact-us/'),
        'title'  => 'Make an Inquiry',
        'target' => '_self',
    ];
}

// Safe heading tag
$allowed_heading_tags = ['h1','h2','h3','h4','h5','h6','p','span'];
if (!in_array($heading_tag, $allowed_heading_tags, true)) {
    $heading_tag = 'h2';
}
?>

<section
    id="<?php echo esc_attr($section_id); ?>"
    class="relative flex overflow-hidden <?php echo esc_attr(implode(' ', $padding_classes)); ?>"
    style="background-color: <?php echo esc_attr($background_color); ?>;"
    role="region"
    aria-labelledby="<?php echo esc_attr($section_id); ?>-heading"
>
    <?php if ($background_image_id): ?>
        <img
            src="<?php echo esc_url(wp_get_attachment_image_url($background_image_id, 'full')); ?>"
            alt="<?php echo esc_attr($background_image_alt); ?>"
            class="object-contain absolute right-0 bottom-0 z-0 shrink-0 self-start h-52 w-[171px]"
            role="presentation"
            aria-hidden="true"
        />
    <?php else: ?>
        <svg class="absolute right-0 bottom-0" xmlns="http://www.w3.org/2000/svg" width="171" height="208" viewBox="0 0 171 208" fill="none" aria-hidden="true" role="presentation">
            <!-- (kept your decorative SVG exactly) -->
            <g opacity="0.4">
                <foreignObject x="-14" y="-166" width="295" height="696"><div xmlns="http://www.w3.org/1999/xhtml" style="backdrop-filter:blur(7px);clip-path:url(#bgblur_0_3163_2528_clip_path);height:100%;width:100%"></div></foreignObject>
                <path data-figma-bg-blur-radius="14" d="M177.899 46.329C180.691 48.0259 182.689 49.2888 184.724 50.4727C203.9 61.5815 221.846 74.4169 235.4 92.7179C257.311 122.315 264.798 155.81 256.895 192.165C251.926 215.024 237.766 232.457 219.82 245.716C205.982 255.947 190.696 264.037 176.262 272.946C178.013 273.923 181.023 275.294 183.711 277.168C199.006 287.833 215.211 297.373 229.285 309.577C286.435 359.113 274.812 449.445 220.085 489.411C170.791 525.401 92.1645 524.839 43.5329 487.822C-7.79631 448.744 -20.224 369.206 41.8386 320.607C56.0741 309.459 72.4298 301.221 87.7916 291.661C86.9682 291.128 84.9899 289.767 82.9549 288.514C64.4602 277.08 47.4987 263.672 34.2287 245.854C10.6133 214.116 2.9371 178.757 14.0775 139.985C21.8484 112.943 40.0876 94.6319 63.154 81.2539C72.2026 76.0053 81.857 71.911 92.1645 66.8301C89.8739 65.3996 87.9336 64.1466 85.9459 62.9627C67.6972 52.061 50.7453 39.4328 37.3995 22.2368C-3.80205 -30.8509 15.2228 -110.142 76.6134 -138.309C115.912 -156.334 156.48 -156.432 195.949 -139.365C262.621 -110.547 273.714 -25.4938 217.35 22.0987C207.298 30.5833 195.154 36.3942 183.938 43.3792C182.291 44.4052 180.464 45.0958 177.899 46.3488V46.329ZM57.7778 394.571C57.7967 422.915 62.34 445.557 76.0928 465.18C94.0764 490.841 119.036 500.49 148.567 495.853C177.852 491.246 196.725 472.54 204.222 443.15C213.772 405.719 206.096 372.58 177.199 346.357C158.089 329.013 135.931 316.809 113.139 305.809C111.17 304.862 107.536 305.838 105.453 307.21C73.1018 328.48 57.3803 359.232 57.7778 394.571ZM202.736 180.336C203.663 151.439 193.185 127.83 170.289 111.897C152.836 99.7522 133.849 89.9949 115.468 79.3202C114.142 78.5506 111.814 78.2547 110.555 78.9354C91.5587 89.255 74.8339 102.307 69.0318 125.097C60.9108 157.013 65.454 186.571 86.883 212.133C104.791 233.493 127.734 247.482 151.813 259.667C153.64 260.594 157 259.597 159.064 258.404C188.481 241.405 203.729 215.675 202.736 180.336ZM197.587 -49.9905C197.937 -68.696 195.779 -86.928 186.21 -103.335C173.858 -124.507 155.278 -133.919 131.889 -132.814C109.419 -131.758 91.7575 -121.823 81.3743 -100.237C64.7631 -65.7166 72.3067 -23.491 101.45 0.206543C117.029 12.884 135.344 21.8915 152.315 32.7537C156.12 35.1906 158.449 34.056 161.496 31.2344C184.847 9.5889 197.076 -17.3348 197.587 -49.9905Z" fill="#86A8FF"/>
            </g>
            <defs>
                <clipPath id="bgblur_0_3163_2528_clip_path" transform="translate(14 166)"><path d="M177.899 46.329C180.691 48.0259 182.689 49.2888 184.724 50.4727C203.9 61.5815 221.846 74.4169 235.4 92.7179C257.311 122.315 264.798 155.81 256.895 192.165C251.926 215.024 237.766 232.457 219.82 245.716C205.982 255.947 190.696 264.037 176.262 272.946C178.013 273.923 181.023 275.294 183.711 277.168C199.006 287.833 215.211 297.373 229.285 309.577C286.435 359.113 274.812 449.445 220.085 489.411C170.791 525.401 92.1645 524.839 43.5329 487.822C-7.79631 448.744 -20.224 369.206 41.8386 320.607C56.0741 309.459 72.4298 301.221 87.7916 291.661C86.9682 291.128 84.9899 289.767 82.9549 288.514C64.4602 277.08 47.4987 263.672 34.2287 245.854C10.6133 214.116 2.9371 178.757 14.0775 139.985C21.8484 112.943 40.0876 94.6319 63.154 81.2539C72.2026 76.0053 81.857 71.911 92.1645 66.8301C89.8739 65.3996 87.9336 64.1466 85.9459 62.9627C67.6972 52.061 50.7453 39.4328 37.3995 22.2368C-3.80205 -30.8509 15.2228 -110.142 76.6134 -138.309C115.912 -156.334 156.48 -156.432 195.949 -139.365C262.621 -110.547 273.714 -25.4938 217.35 22.0987C207.298 30.5833 195.154 36.3942 183.938 43.3792C182.291 44.4052 180.464 45.0958 177.899 46.3488V46.329Z"/>
                </clipPath>
            </defs>
        </svg>
    <?php endif; ?>

    <div class="flex flex-col items-center px-20 py-10 mx-auto w-full max-w-container max-md:px-5">
        <div class="flex z-0 flex-wrap flex-1 gap-8 items-center self-stretch my-auto text-3xl font-bold tracking-wider leading-none text-white shrink basis-16 max-md:max-w-full">

            <!-- Profile image OR default -->
            <img
                src="<?php echo esc_url($profile_img_src); ?>"
                alt="<?php echo esc_attr($profile_image_alt); ?>"
                class="object-contain self-stretch my-auto w-32 rounded-full shrink-0 aspect-square"
            />

            <div class="flex-1 self-stretch my-auto shrink basis-0 max-md:max-w-full">
                <?php if (!empty($heading)): ?>
                    <<?php echo esc_attr($heading_tag); ?>
                        id="<?php echo esc_attr($section_id); ?>-heading"
                        class="text-3xl font-bold tracking-wider leading-none text-white max-md:max-w-full"
                    >
                        <?php echo esc_html($heading); ?>
                    </<?php echo esc_attr($heading_tag); ?>>
                <?php endif; ?>
            </div>

            <!-- CTA link OR default -->
            <a
                href="<?php echo esc_url($button['url']); ?>"
                class="flex z-0 gap-2 justify-center items-center self-stretch px-8 py-5 my-auto text-xl tracking-wide leading-none text-center text-primary bg-white shadow-[10px_14px_24px_rgba(0,0,0,0.25)] max-md:px-5 w-fit whitespace-nowrap btn transition-colors duration-300 hover:bg-primary hover:text-white focus:ring-2 focus:ring-offset-2 focus:ring-primary max-md:w-full"
                target="<?php echo esc_attr($button['target'] ?? '_self'); ?>"
                aria-label="<?php echo esc_attr($button['title']); ?>"
            >
                <span class="self-stretch my-auto">
                    <?php echo esc_html($button['title']); ?>
                </span>
            </a>

        </div>
    </div>
</section>
