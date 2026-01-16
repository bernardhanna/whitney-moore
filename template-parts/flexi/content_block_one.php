<?php
// Get ACF fields
$section_name   = get_sub_field('section_name');
$heading        = get_sub_field('heading');
$heading_tag    = get_sub_field('heading_tag');
$content        = get_sub_field('content');
$reverse_layout = get_sub_field('reverse_layout');

// Background color
$background_color = get_sub_field('background_color');

// Padding settings (only add classes when values exist)
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

// Generate unique ID for accessibility
$section_id = 'content-block-' . wp_rand(1000, 9999);

// Enforce allowed heading tags (include span & p)
$allowed_heading_tags = ['h1','h2','h3','h4','h5','h6','span','p'];
if (!in_array($heading_tag, $allowed_heading_tags, true)) {
    $heading_tag = 'h2';
}

// Image handling with placeholder fallback
$image_id        = get_sub_field('image');
$placeholder_url = 'https://placeholdit.com/696x464/dddddd/999999';

if ($image_id) {
    $image_alt    = get_post_meta($image_id, '_wp_attachment_image_alt', true) ?: 'Content image';
    $image_markup = wp_get_attachment_image(
        $image_id,
        'full',
        false,
        [
            'alt'     => esc_attr($image_alt),
            // NOTE: removed any aspect-[...] utilities per spec; keep responsive sizing
            'class'   => 'object-contain w-full h-auto max-md:max-w-full',
            'loading' => 'lazy',
        ]
    );
} else {
    $image_alt    = 'Placeholder image';
    $image_markup = sprintf(
        '<img src="%s" alt="%s" class="object-contain w-full h-auto max-md:max-w-full" loading="lazy" />',
        esc_url($placeholder_url),
        esc_attr($image_alt)
    );
}
?>

<section
    id="<?php echo esc_attr($section_id); ?>"
    class="relative flex overflow-hidden <?php echo esc_attr(implode(' ', $padding_classes)); ?>"
    style="background-color: <?php echo esc_attr($background_color); ?>;"
    aria-labelledby="<?php echo esc_attr($section_id); ?>-heading"
>
    <div class="flex flex-col items-center pt-5 pb-5 mx-auto w-full max-w-container max-lg:px-5">
        <div class="flex overflow-hidden gap-10 justify-center items-start
            flex-wrap max-md:flex-col-reverse
            w-full px-3 sm:px-0 lg:px-32
            py-4 sm:py-8 lg:pt-20 lg:pb-20">




            <?php if ($reverse_layout): ?>
                <!-- Image first when reversed -->
                <div class="flex-1 shrink basis-0 min-w-60 max-md:max-w-full">
                    <?php echo $image_markup; ?>
                </div>

                <!-- Text content second when reversed -->
                <div class="flex-1 shrink basis-0 min-w-60 max-md:max-w-full">
                    <div class="w-full max-md:max-w-full">
                        <?php if (!empty($section_name)): ?>
                            <div class="text-lg font-medium tracking-wider text-black">
                                <?php echo esc_html($section_name); ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($heading)): ?>
                            <<?php echo esc_attr($heading_tag); ?>
                                id="<?php echo esc_attr($section_id); ?>-heading"
                                class="mt-1 text-5xl font-bold text-primary leading-[58px] max-md:max-w-full max-md:text-4xl max-md:leading-[54px]"
                            >
                                <?php echo esc_html($heading); ?>
                            </<?php echo esc_attr($heading_tag); ?>>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($content)): ?>
                        <div class="mt-4 text-lg tracking-wider leading-7 text-black max-md:max-w-full wp_editor">
                            <?php echo wp_kses_post($content); ?>
                        </div>
                    <?php endif; ?>
                </div>

            <?php else: ?>
                <!-- Default layout: Text first, image second -->
                <div class="flex-1 shrink basis-0 min-w-60 max-md:max-w-full">
                    <div class="w-full max-md:max-w-full">
                        <?php if (!empty($section_name)): ?>
                            <div class="text-lg font-medium tracking-wider text-black">
                                <?php echo esc_html($section_name); ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($heading)): ?>
                            <<?php echo esc_attr($heading_tag); ?>
                                id="<?php echo esc_attr($section_id); ?>-heading"
                                class="mt-1 text-5xl font-bold text-primary leading-[58px] max-md:max-w-full max-md:text-4xl max-md:leading-[54px]"
                            >
                                <?php echo esc_html($heading); ?>
                            </<?php echo esc_attr($heading_tag); ?>>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($content)): ?>
                        <div class="mt-4 text-lg tracking-wider leading-7 text-black max-md:max-w-full wp_editor">
                            <?php echo wp_kses_post($content); ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="flex-1 shrink basis-0 min-w-60 max-md:max-w-full">
                    <?php echo $image_markup; ?>
                </div>
            <?php endif; ?>

        </div>
    </div>
</section>
