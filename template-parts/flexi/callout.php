<?php
// Get ACF fields
$section_heading      = get_sub_field('section_heading');
$section_heading_tag  = get_sub_field('section_heading_tag');
$main_heading         = get_sub_field('main_heading');
$main_heading_tag     = get_sub_field('main_heading_tag');
$description          = get_sub_field('description');
$cta_button           = get_sub_field('cta_button');
$left_image           = get_sub_field('left_image');
$right_image          = get_sub_field('right_image');
$background_color     = get_sub_field('background_color');

// NEW: mobile-only image below button + radius control
$mobile_below_button_image        = get_sub_field('mobile_below_button_image');
$mobile_below_button_image_radius = get_sub_field('mobile_below_button_image_radius') ?: 'rounded-none';

// Get image alt text
$left_image_alt  = $left_image ? (get_post_meta($left_image, '_wp_attachment_image_alt', true) ?: 'Our people - left image') : '';
$right_image_alt = $right_image ? (get_post_meta($right_image, '_wp_attachment_image_alt', true) ?: 'Our people - right image') : '';
$mobile_below_button_image_alt = $mobile_below_button_image
    ? (get_post_meta($mobile_below_button_image, '_wp_attachment_image_alt', true) ?: 'Our people - mobile image')
    : '';

// Generate padding classes
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

// Generate unique section ID
$section_id = 'our-people-' . wp_rand(1000, 9999);
?>

<section
    id="<?php echo esc_attr($section_id); ?>"
    class="relative flex overflow-hidden <?php echo esc_attr(implode(' ', $padding_classes)); ?>"
    style="background-color: <?php echo esc_attr($background_color); ?>;"
    aria-labelledby="<?php echo esc_attr($section_id); ?>-heading"
>
    <div class="flex flex-row justify-center items-center mx-auto w-full max-w-full max-sm:px-0 max-xxl:px-[1rem]">

        <!-- Left Image -->
        <?php if ($left_image): ?>
            <div class="flex-shrink-0 w-full max-w-[589px] max-md:max-w-full max-sm:px-6 max-tab:hidden">
                <?php echo wp_get_attachment_image($left_image, 'full', false, [
                    'alt'     => esc_attr($left_image_alt),
                    'class'   => 'object-cover w-full h-auto',
                    'loading' => 'lazy'
                ]); ?>
            </div>
        <?php endif; ?>

        <!-- Center Content -->
        <div class="flex flex-col items-center my-auto w-full max-md:max-w-full max-tab:pb-12 max-sm:pb-0">
            <div class="flex flex-col items-center max-w-full tracking-wider text-center lg:w-[581px] max-sm:px-5">

                <!-- Headings Container -->
                <div class="flex flex-col items-start w-full max-w-[flex flex-col items-start max-w-[580px]">

                    <!-- Section Heading -->
                    <?php if (!empty($section_heading)): ?>
                        <<?php echo esc_attr($section_heading_tag); ?>
                            class="w-full text-lg font-medium text-black max-md:max-w-full"
                            id="<?php echo esc_attr($section_id); ?>-heading"
                        >
                            <?php echo esc_html($section_heading); ?>
                        </<?php echo esc_attr($section_heading_tag); ?>>
                    <?php endif; ?>

                    <!-- Main Heading -->
                    <?php if (!empty($main_heading)): ?>
                        <<?php echo esc_attr($main_heading_tag); ?>
                            class="mt-2 text-[1.625rem] sm:text-4xl md:text-5xl font-bold text-primary leading-[52px] max-md:max-w-full  max-sm:tracking-[1px] max-sm:leading-[2rem]"
                        >
                            <?php echo esc_html($main_heading); ?>
                        </<?php echo esc_attr($main_heading_tag); ?>>
                    <?php endif; ?>

                </div>

                <!-- Description -->
                <?php if (!empty($description)): ?>
                    <div class="mt-6 text-[1.125rem] max-sm:tracking-[1px] max-sm:leading-[1.75rem] sm:text-lg leading-7 text-black max-md:max-w-full wp_editor">
                        <?php echo wp_kses_post($description); ?>
                    </div>
                <?php endif; ?>

            </div>

            <!-- CTA Button -->
            <?php if ($cta_button && is_array($cta_button) && isset($cta_button['url'], $cta_button['title'])): ?>
                <a
                    href="<?php echo esc_url($cta_button['url']); ?>"
                    class="flex gap-2 justify-center items-center px-16 py-4 mt-14 text-xl leading-none whitespace-nowrap border border-indigo-800 border-solid transition-colors duration-300 text-primary hover:bg-primary hover:text-white focus:bg-primary focus:text-white max-md:px-5 max-md:mt-10 w-fit btn max-sm:text-[1.25rem] max-sm:leading-[1.25rem]"
                    target="<?php echo esc_attr($cta_button['target'] ?? '_self'); ?>"
                    aria-label="<?php echo esc_attr($cta_button['title']); ?>"
                >
                    <span class="my-auto">
                        <?php echo esc_html($cta_button['title']); ?>
                    </span>
                </a>
            <?php endif; ?>

            <!-- NEW: Mobile-only image below CTA (hidden on md and up) -->
            <?php if (!empty($mobile_below_button_image)): ?>
                <div class="mt-6 w-full md:hidden">
                    <?php
                    echo wp_get_attachment_image(
                        $mobile_below_button_image,
                        'full',
                        false,
                        [
                            'alt'     => esc_attr($mobile_below_button_image_alt),
                            'class'   => esc_attr('w-full h-auto object-cover ' . $mobile_below_button_image_radius),
                            'loading' => 'lazy'
                        ]
                    );
                    ?>
                </div>
            <?php endif; ?>

        </div>

        <!-- Right Image -->
        <?php if ($right_image): ?>
            <div class="flex-shrink-0 w-full max-w-[551px] max-md:max-w-full max-tab:hidden">
                <?php echo wp_get_attachment_image($right_image, 'full', false, [
                    'alt'     => esc_attr($right_image_alt),
                    'class'   => 'object-cover w-full h-auto',
                    'loading' => 'lazy'
                ]); ?>
            </div>
        <?php endif; ?>

    </div>
</section>
