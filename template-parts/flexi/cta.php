<?php
// Get ACF fields
$heading              = get_sub_field('heading');
$heading_tag          = get_sub_field('heading_tag');
$primary_button       = get_sub_field('primary_button');
$secondary_button     = get_sub_field('secondary_button');
$background_color     = get_sub_field('background_color');

// NEW: visibility controls
$visibility_mode       = get_sub_field('visibility_mode');        // none | hide_below | hide_above
$visibility_breakpoint = get_sub_field('visibility_breakpoint');  // xxs|xs|mob|sm|md|lg|xl|xxl|ultrawide

// Generate unique section ID
$section_id = 'cta-' . uniqid();

// Build padding classes
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

// Build visibility classes (always appended after base classes to ensure correct override order)
$visibility_classes = '';
if ($visibility_mode && $visibility_mode !== 'none' && $visibility_breakpoint) {
    if ($visibility_mode === 'hide_below') {
        // Hidden by default; becomes flex at/above breakpoint
        $visibility_classes = "hidden {$visibility_breakpoint}:flex";
    } elseif ($visibility_mode === 'hide_above') {
        // Flex by default; hidden at/above breakpoint
        $visibility_classes = "{$visibility_breakpoint}:hidden";
    }
}
?>

<section
    id="<?php echo esc_attr($section_id); ?>"
    class="relative flex overflow-hidden <?php echo esc_attr(implode(' ', $padding_classes)); ?> <?php echo esc_attr($visibility_classes); ?>"
    style="background-color: <?php echo esc_attr($background_color); ?>;"
    role="region"
    aria-labelledby="<?php echo esc_attr($section_id); ?>-heading"
>
    <div class="flex flex-col items-center pt-5 pb-5 mx-auto w-full max-w-container max-xxl:px-[1rem]">
        <div class="box-border flex relative flex-col gap-4 items-center py-0 mx-auto my-0 w-full max-w-[1728px] max-lg:px-10 max-lg:py-0 max-sm:px-5 max-sm:py-0">
            <div class="flex relative justify-between items-center w-full max-w-[1568px] max-lg:flex-col max-lg:gap-6 max-lg:items-center">
                <?php if (!empty($heading)): ?>
                    <div class="relative text-3xl font-bold tracking-wider leading-10 text-center text-primary max-lg:text-center">
                        <<?php echo esc_attr($heading_tag); ?>
                            id="<?php echo esc_attr($section_id); ?>-heading"
                            class="text-3xl font-bold text-primary max-lg:text-3xl max-lg:leading-9 max-sm:text-2xl max-sm:tracking-wide max-sm:leading-8"
                        >
                            <?php echo esc_html($heading); ?>
                        </<?php echo esc_attr($heading_tag); ?>>
                    </div>
                <?php endif; ?>

                <div class="flex relative gap-4 items-start max-lg:flex-col max-lg:w-full max-lg:max-w-[400px] max-sm:gap-3" role="group" aria-label="Call to action buttons">
                    <?php if ($primary_button && is_array($primary_button) && isset($primary_button['url'], $primary_button['title'])): ?>
                        <a
                            href="<?php echo esc_url($primary_button['url']); ?>"
                            class="flex relative gap-2 justify-center items-center px-8 py-4 h-14 whitespace-nowrap border-none transition-colors duration-300 cursor-pointer bg-primary max-lg:justify-center max-lg:w-full max-sm:px-6 max-sm:py-3.5 max-sm:h-auto w-fit btn hover:bg-indigo-900 focus:bg-indigo-900"
                            target="<?php echo esc_attr($primary_button['target'] ?? '_self'); ?>"
                            aria-label="<?php echo esc_attr($primary_button['title']); ?>"
                        >
                            <span class="relative text-xl tracking-wide leading-5 text-center text-white max-sm:text-lg max-sm:leading-5">
                                <?php echo esc_html($primary_button['title']); ?>
                            </span>
                        </a>
                    <?php endif; ?>

                    <?php if ($secondary_button && is_array($secondary_button) && isset($secondary_button['url'], $secondary_button['title'])): ?>
                        <a
                            href="<?php echo esc_url($secondary_button['url']); ?>"
                            class="flex relative gap-2 justify-center items-center px-8 py-4 whitespace-nowrap border border-indigo-800 border-solid transition-colors duration-300 cursor-pointer max-lg:justify-center max-lg:w-full max-sm:px-6 max-sm:py-3.5 max-sm:h-auto w-fit btn hover:bg-primary text-primary hover:text-white focus:bg-primary focus:text-white"
                            target="<?php echo esc_attr($secondary_button['target'] ?? '_self'); ?>"
                            aria-label="<?php echo esc_attr($secondary_button['title']); ?>"
                        >
                            <span class="relative text-xl leading-5 max-sm:text-lg max-sm:leading-5">
                                <?php echo esc_html($secondary_button['title']); ?>
                            </span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>
