<?php
// =========================================================
// CTA – Frontend (Flexi Block)
// =========================================================

// ---- Content ----
$heading          = get_sub_field('heading');
$heading_tag      = get_sub_field('heading_tag') ?: 'h2';
$primary_button   = get_sub_field('primary_button');   // ACF link array
$secondary_button = get_sub_field('secondary_button'); // ACF link array

// ---- Design (keep only background) ----
$background_color = get_sub_field('background_color') ?: '#FFFFFF';

// ---- Visibility (section & elements) ----
$visibility_mode       = get_sub_field('visibility_mode');        // none|hide_below|hide_above
$visibility_breakpoint = get_sub_field('visibility_breakpoint');  // xxs|xs|mob|sm|md|lg|xl|xxl|ultrawide

$heading_visibility_mode       = get_sub_field('heading_visibility_mode');
$heading_visibility_breakpoint = get_sub_field('heading_visibility_breakpoint');

$primary_visibility_mode       = get_sub_field('primary_button_visibility_mode');
$primary_visibility_breakpoint = get_sub_field('primary_button_visibility_breakpoint');

$secondary_visibility_mode       = get_sub_field('secondary_button_visibility_mode');
$secondary_visibility_breakpoint = get_sub_field('secondary_button_visibility_breakpoint');

// ---- Padding controls ----
$padding_classes = [];
if (have_rows('padding_settings')) {
    while (have_rows('padding_settings')) {
        the_row();
        $screen_size    = get_sub_field('screen_size');
        $padding_top    = get_sub_field('padding_top');
        $padding_bottom = get_sub_field('padding_bottom');
        if ($screen_size !== '' && $padding_top !== '' && $padding_top !== null) {
            $padding_classes[] = "{$screen_size}:pt-[{$padding_top}rem]";
        }
        if ($screen_size !== '' && $padding_bottom !== '' && $padding_bottom !== null) {
            $padding_classes[] = "{$screen_size}:pb-[{$padding_bottom}rem]";
        }
    }
}

// ---- Unique section ID ----
$section_id = 'cta-' . uniqid();

// ---- Visibility class map ----
$visibility_map = [
    'xxs' => ['hide_below' => 'hidden xxs:flex',       'hide_above' => 'flex xxs:hidden'],
    'xs'  => ['hide_below' => 'hidden xs:flex',        'hide_above' => 'flex xs:hidden'],
    'mob' => ['hide_below' => 'hidden mob:flex',       'hide_above' => 'flex mob:hidden'],
    'sm'  => ['hide_below' => 'hidden sm:flex',        'hide_above' => 'flex sm:hidden'],
    'md'  => ['hide_below' => 'hidden md:flex',        'hide_above' => 'flex md:hidden'],
    'lg'  => ['hide_below' => 'hidden lg:flex',        'hide_above' => 'flex lg:hidden'],
    'xl'  => ['hide_below' => 'hidden xl:flex',        'hide_above' => 'flex xl:hidden'],
    'xxl' => ['hide_below' => 'hidden xxl:flex',       'hide_above' => 'flex xxl:hidden'],
    'ultrawide' => ['hide_below' => 'hidden ultrawide:flex', 'hide_above' => 'flex ultrawide:hidden'],
];
$resolve_visibility = function ($mode, $bp) use ($visibility_map) {
    $classes = 'flex';
    if (!empty($mode) && $mode !== 'none' && !empty($bp) && isset($visibility_map[$bp][$mode])) {
        $classes = $visibility_map[$bp][$mode];
    }
    return $classes;
};

$section_display_classes   = $resolve_visibility($visibility_mode, $visibility_breakpoint);
$heading_display_classes   = $resolve_visibility($heading_visibility_mode, $heading_visibility_breakpoint);
$primary_display_classes   = $resolve_visibility($primary_visibility_mode, $primary_visibility_breakpoint);
$secondary_display_classes = $resolve_visibility($secondary_visibility_mode, $secondary_visibility_breakpoint);
?>
<section
    id="<?php echo esc_attr($section_id); ?>"
    class="relative flex overflow-hidden <?php echo esc_attr($section_display_classes); ?>"
    role="region"
    aria-labelledby="<?php echo esc_attr($section_id); ?>-heading"
    style="background-color: <?php echo esc_attr($background_color); ?>;"
>
    <div class="flex flex-col items-center w-full mx-auto max-w-container pt-5 pb-5 max-lg:px-5 <?php echo esc_attr(implode(' ', $padding_classes)); ?>">
        <header class="box-border flex relative justify-between items-center p-2.5 w-full max-md:flex-col max-md:gap-5 max-md:items-center max-md:px-2.5 max-md:py-5 max-sm:px-2.5 max-sm:py-4" role="banner">
            <?php if (!empty($heading)) : ?>
                <div class="relative text-3xl font-bold tracking-wider leading-10 text-center text-indigo-800 <?php echo esc_attr($heading_display_classes); ?>">
                    <<?php echo esc_attr($heading_tag); ?>
                        id="<?php echo esc_attr($section_id); ?>-heading"
                        class="text-3xl font-bold text-indigo-800 max-sm:text-2xl"
                    >
                        <?php echo esc_html($heading); ?>
                    </<?php echo esc_attr($heading_tag); ?>>
                </div>
            <?php endif; ?>

            <nav class="flex relative gap-4 items-start max-md:flex-col max-md:gap-3 max-md:w-full max-sm:gap-2.5" role="navigation" aria-label="CTA buttons">
                <?php if (is_array($primary_button) && isset($primary_button['url'], $primary_button['title'])) : ?>
                    <a
                        href="<?php echo esc_url($primary_button['url']); ?>"
                        class="btn flex relative gap-2 justify-center items-center px-8 py-4 h-14 bg-indigo-800 text-white cursor-pointer border-0 w-fit whitespace-nowrap hover:bg-indigo-900 transition-colors duration-200 <?php echo esc_attr($primary_display_classes); ?>"
                        target="<?php echo esc_attr(isset($primary_button['target']) ? $primary_button['target'] : '_self'); ?>"
                        aria-label="<?php echo esc_attr($primary_button['title']); ?>"
                    >
                        <span class="relative text-xl tracking-wide leading-5 text-center max-md:text-lg max-sm:text-base max-sm:leading-5">
                            <?php echo esc_html($primary_button['title']); ?>
                        </span>
                    </a>
                <?php endif; ?>

                <?php if (is_array($secondary_button) && isset($secondary_button['url'], $secondary_button['title'])) : ?>
                    <a
                        href="<?php echo esc_url($secondary_button['url']); ?>"
                        class="btn flex relative gap-2 justify-center items-center px-8 py-4 border border-indigo-800 text-indigo-800 border-solid cursor-pointer w-fit whitespace-nowrap hover:bg-indigo-50 transition-colors duration-200 <?php echo esc_attr($secondary_display_classes); ?>"
                        target="<?php echo esc_attr(isset($secondary_button['target']) ? $secondary_button['target'] : '_self'); ?>"
                        aria-label="<?php echo esc_attr($secondary_button['title']); ?>"
                    >
                        <span class="relative text-xl leading-5 max-md:text-lg max-sm:text-base max-sm:leading-5">
                            <?php echo esc_html($secondary_button['title']); ?>
                        </span>
                    </a>
                <?php endif; ?>
            </nav>
        </header>
    </div>
</section>
