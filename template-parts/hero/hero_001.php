<?php
// Get all field values
$small_heading     = get_sub_field('small_heading');
$small_heading_tag = get_sub_field('small_heading_tag');
$main_heading      = get_sub_field('main_heading');
$main_heading_tag  = get_sub_field('main_heading_tag');
$description       = get_sub_field('description');
$primary_button    = get_sub_field('primary_button');   // ACF link array
$secondary_button  = get_sub_field('secondary_button'); // ACF link array

// Background and design settings
$background_image         = get_sub_field('background_image');
$background_image_alt_raw = $background_image ? get_post_meta($background_image, '_wp_attachment_image_alt', true) : '';
$background_image_alt     = $background_image_alt_raw ?: 'Hero background image';
$content_box_bg_color     = get_sub_field('content_box_bg_color');
$content_box_border_color = get_sub_field('content_box_border_color');
$outer_border_width       = get_sub_field('outer_border_width');
$inner_border_width       = get_sub_field('inner_border_width');

// Text colors
$small_heading_color = get_sub_field('small_heading_color');
$main_heading_color  = get_sub_field('main_heading_color');
$description_color   = get_sub_field('description_color');

// Allowed heading tags
$allowed_heading_tags = ['h1','h2','h3','h4','h5','h6','span','p'];
if (empty($small_heading_tag) || !in_array($small_heading_tag, $allowed_heading_tags, true)) {
    $small_heading_tag = 'p';
}
if (empty($main_heading_tag) || !in_array($main_heading_tag, $allowed_heading_tags, true)) {
    $main_heading_tag = 'h1';
}

// Padding settings
$padding_classes = [];
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

// Unique ID for this section
$section_id = 'hero_' . wp_generate_uuid4();

// Prepare content box styles
$content_box_style = '';
if (!empty($content_box_bg_color)) {
    $content_box_style .= "background-color: {$content_box_bg_color};";
}

$outer_border_style = '';
$inner_border_style = '';
if (!empty($content_box_border_color)) {
    if ($outer_border_width !== null && $outer_border_width !== '') {
        $outer_border_style = "border-color: {$content_box_border_color}; border-width: {$outer_border_width}px;";
    }
    if ($inner_border_width !== null && $inner_border_width !== '') {
        $inner_border_style = "border-color: {$content_box_border_color}; border-width: {$inner_border_width}px;";
    }
}

// Determine if buttons are valid
$has_primary   = (is_array($primary_button) && !empty($primary_button['url']) && !empty($primary_button['title']));
$has_secondary = (is_array($secondary_button) && !empty($secondary_button['url']) && !empty($secondary_button['title']));

// Allow <br> in headings
$allowed_br = ['br' => []];

// Simple content checks (trim to avoid stray whitespace)
$has_small   = !empty(trim((string) wp_strip_all_tags($small_heading)));
$has_main    = !empty(trim((string) wp_strip_all_tags($main_heading)));
$has_desc    = !empty(trim((string) wp_strip_all_tags($description)));
$has_bg      = !empty($background_image);

// If there's no background and no real content (no headings, no desc, no buttons), don't render anything
if (!$has_bg && !$has_small && !$has_main && !$has_desc && !$has_primary && !$has_secondary) {
    return;
}

// For accessibility: only add aria-labelledby when we actually render a main heading
$has_labelledby = $has_main;
$aria_labelledby_attr = $has_labelledby ? 'aria-labelledby="' . esc_attr($section_id) . '-heading"' : 'aria-label="Page hero"';
?>
<section
    id="<?php echo esc_attr($section_id); ?>"
    class="flex overflow-hidden relative max-sm:flex-col"
    role="banner"
    <?php echo $aria_labelledby_attr; ?>
>
    <?php if ($has_bg) : ?>
        <?php
        echo wp_get_attachment_image($background_image, 'full', false, [
            'alt'           => esc_attr($background_image_alt),
            'class'         => 'object-cover relative sm:absolute inset-0 size-full',
            'aria-hidden'   => 'true',
            'decoding'      => 'async',
            'fetchpriority' => 'high',
        ]);
        ?>
    <?php endif; ?>

    <div class="gap-2 items-center flex justify-end max-w-container mx-auto lg:min-h-[878px] sm:min-h-[600px] md:min-h-[800px] max-sm:py-5 px-5 w-full <?php echo esc_attr(implode(' ', $padding_classes)); ?>">
        <div
            class="flex relative flex-col justify-center self-stretch p-[2px] my-auto border-solid min-w-60 w-full md:w-[627px] max-w-full"
            style="<?php echo esc_attr($outer_border_style); ?>"
        >
            <div
                class="flex flex-col p-16 w-full border-solid max-md:p-5 max-md:max-w-full"
                style="<?php echo esc_attr($inner_border_style . $content_box_style); ?>"
            >
                <div class="w-full tracking-wider max-md:max-w-full">
                    <div class="w-full max-md:max-w-full">

                        <?php if ($has_small) : ?>
                            <<?php echo esc_attr($small_heading_tag); ?>
                                class="max-md:text-[1rem] text-lg font-medium tracking-[1px]"
                                style="color: <?php echo esc_attr($small_heading_color); ?>;"
                            >
                                <?php echo wp_kses($small_heading, $allowed_br); ?>
                            </<?php echo esc_attr($small_heading_tag); ?>>
                        <?php endif; ?>

                        <?php if ($has_main) : ?>
                            <<?php echo esc_attr($main_heading_tag); ?>
                                id="<?php echo esc_attr($section_id); ?>-heading"
                                class="mt-2 text-[68px] font-bold leading-[78px] max-md:max-w-full max-md:text-4xl max-md:leading-[51px] break-words overflow-wrap-anywhere max-mob:text-[2rem] max-mob:tracking-[1px] max-mob:leading-[2.625rem]"
                                style="color: <?php echo esc_attr($main_heading_color); ?>;"
                            >
                                <?php echo wp_kses($main_heading, $allowed_br); ?>
                            </<?php echo esc_attr($main_heading_tag); ?>>
                        <?php endif; ?>

                    </div>

                    <?php if ($has_desc) : ?>
                        <div
                            class="mt-4 text-2xl font-medium max-md:max-w-full wp_editor max-sm:text-[1.125rem] tracking-[1px] leading-[1.625rem]"
                            style="color: <?php echo esc_attr($description_color); ?>;"
                        >
                            <?php echo wp_kses_post($description); ?>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if ($has_primary || $has_secondary) : ?>
                    <div
                        class="flex gap-3 items-start self-start mt-14 text-xl leading-none max-md:mt-10 max-md:max-w-full max-sm:flex-col max-sm:w-full"
                        role="group"
                        aria-label="Call to action buttons"
                    >
                        <?php if ($has_primary) : ?>
                            <a
                                href="<?php echo esc_url($primary_button['url']); ?>"
                                class="flex gap-2 justify-center items-center px-8 py-5 tracking-wide text-center text-white bg-primary min-h-14 w-fit whitespace-nowrap shadow-[10px_14px_24px_rgba(0,0,0,0.25)] max-md:px-5 btn hover:opacity-90 transition-opacity duration-200 max-sm:w-full hover:bg-primary-dark"
                                target="<?php echo esc_attr($primary_button['target'] ?? '_self'); ?>"
                                aria-label="<?php echo esc_attr($primary_button['title']); ?>"
                            >
                                <span class="self-stretch my-auto">
                                    <?php echo esc_html($primary_button['title']); ?>
                                </span>
                                <svg class="object-contain self-stretch my-auto w-4 shrink-0" width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M3 8H13M13 8L8 3M13 8L8 13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </a>
                        <?php endif; ?>

                        <?php if ($has_secondary) : ?>
                            <a
                                href="<?php echo esc_url($secondary_button['url']); ?>"
                                class="flex gap-2 justify-center items-center px-8 py-5 text-[#1D4ED8] bg-[#DBEAFE] min-h-14 w-fit whitespace-nowrap shadow-[20px_14px_24px_rgba(0,0,0,0.08)] max-md:px-5 btn hover:bg-primary-dark transition-opacity duration-200 max-sm:w-full hover:text-white"
                                target="<?php echo esc_attr($secondary_button['target'] ?? '_self'); ?>"
                                aria-label="<?php echo esc_attr($secondary_button['title']); ?>"
                            >
                                <span class="self-stretch my-auto">
                                    <?php echo esc_html($secondary_button['title']); ?>
                                </span>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</section>
