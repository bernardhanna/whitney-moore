<?php
// Unique section ID
$section_id = 'our-story-' . wp_rand(1000, 9999);

// Content
$section_label     = get_sub_field('section_label');
$main_heading      = get_sub_field('main_heading');
$main_heading_tag  = get_sub_field('main_heading_tag') ?: 'h2';
$description       = get_sub_field('description');
$cta_button        = get_sub_field('cta_button'); // ACF link array

// Right-side single image (SVG or any media)
$right_image_id    = get_sub_field('right_image');

// Design
$content_bg        = get_sub_field('content_background_color');
$text_color        = get_sub_field('text_color');
$section_bg        = get_sub_field('section_background_color');

// Padding repeater → classes
$padding_classes = [];
if (have_rows('padding_settings')) {
    while (have_rows('padding_settings')) {
        the_row();
        $screen_size    = get_sub_field('screen_size');
        $padding_top    = get_sub_field('padding_top');
        $padding_bottom = get_sub_field('padding_bottom');
        $padding_classes[] = "{$screen_size}:pt-[{$padding_top}rem]";
        $padding_classes[] = "{$screen_size}:pb-[{$padding_bottom}rem]";
    }
}

// Image meta
$right_img_alt = '';
$right_img_title = '';
if ($right_image_id) {
    $right_img_alt   = get_post_meta($right_image_id, '_wp_attachment_image_alt', true);
    $right_img_title = get_the_title($right_image_id);
    if (!$right_img_alt)   { $right_img_alt = 'Decorative image'; }
    if (!$right_img_title) { $right_img_title = 'Image'; }
}
?>

<section
    id="<?php echo esc_attr($section_id); ?>"
    class="relative flex overflow-hidden <?php echo esc_attr(implode(' ', $padding_classes)); ?>"
    style="background-color: <?php echo esc_attr($section_bg); ?>;"
    aria-labelledby="<?php echo esc_attr($section_id); ?>-heading"
>
    <div class="flex flex-col items-center pt-5 pb-5 mx-auto w-full max-w-container max-lg:px-5">
        <!-- Two-column layout: left content, right single image -->
        <div class="flex flex-col gap-10 w-full lg:flex-row lg:gap-12">
            <!-- Left content -->
            <div class="w-full lg:flex-1">
                <div
                    class="max-w-[680px] px-8 py-10 bg-opacity-90 wp_editor"
                    style="background-color: <?php echo esc_attr($content_bg); ?>; color: <?php echo esc_attr($text_color); ?>;"
                >
                    <?php if (!empty($section_label)) : ?>
                        <p class="text-sm tracking-wide uppercase opacity-90">
                            <?php echo esc_html($section_label); ?>
                        </p>
                    <?php endif; ?>

                    <?php if (!empty($main_heading)) : ?>
                        <<?php echo esc_attr($main_heading_tag); ?>
                            id="<?php echo esc_attr($section_id); ?>-heading"
                            class="mt-2 text-4xl font-bold leading-tight md:text-5xl"
                            style="color: <?php echo esc_attr($text_color); ?>;"
                        >
                            <?php echo esc_html($main_heading); ?>
                        </<?php echo esc_attr($main_heading_tag); ?>>
                    <?php endif; ?>

                    <?php if (!empty($description)) : ?>
                        <div class="mt-5 text-base leading-7 md:text-lg">
                            <?php echo wp_kses_post($description); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($cta_button && is_array($cta_button) && !empty($cta_button['url'])) : ?>
                        <div class="mt-8">
                            <a
                                href="<?php echo esc_url($cta_button['url']); ?>"
                                class="inline-flex justify-center items-center px-8 py-4 text-lg leading-none text-primary bg-white transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-600 hover:bg-indigo-50"
                                target="<?php echo esc_attr($cta_button['target'] ?? '_self'); ?>"
                                aria-label="<?php echo esc_attr($cta_button['title']); ?>"
                                title="<?php echo esc_attr($cta_button['title']); ?>"
                            >
                                <?php echo esc_html($cta_button['title']); ?>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Right: single image (SVG friendly) -->
            <div class="flex justify-end items-center w-full lg:flex-1">
                <?php if ($right_image_id) : ?>
                    <?php
                    // Use wp_get_attachment_image so SVGs from media render as <img>. No aspect utilities.
                    echo wp_get_attachment_image(
                        $right_image_id,
                        'full',
                        false,
                        [
                            'alt'   => esc_attr($right_img_alt),
                            'title' => esc_attr($right_img_title),
                            'class' => 'w-full h-auto object-contain',
                            'loading' => 'lazy',
                        ]
                    );
                    ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
