<?php
// =========================================================
// CTA – Frontend (Flexi Block)
// =========================================================

// ---- Content ----
$heading               = get_sub_field('heading');
$heading_tag           = get_sub_field('heading_tag');
$primary_button        = get_sub_field('primary_button');   // ACF link array
$secondary_button      = get_sub_field('secondary_button'); // ACF link array

// ---- Design ----
$background_color      = get_sub_field('background_color'); // hex or css color
$text_color            = get_sub_field('text_color');
$underline_color       = get_sub_field('underline_color');
$border_color          = get_sub_field('border_color');

// Button base colors
$primary_btn_bg        = get_sub_field('primary_button_bg_color');
$primary_btn_text      = get_sub_field('primary_button_text_color');
$primary_btn_border    = get_sub_field('primary_button_border_color');

$secondary_btn_bg      = get_sub_field('secondary_button_bg_color');
$secondary_btn_text    = get_sub_field('secondary_button_text_color');
$secondary_btn_border  = get_sub_field('secondary_button_border_color');

// Button hover/focus colors (required WCAG styles)
$primary_btn_hover_bg     = get_sub_field('primary_button_hover_bg_color');
$primary_btn_hover_text   = get_sub_field('primary_button_hover_text_color');
$primary_btn_hover_border = get_sub_field('primary_button_hover_border_color');

$secondary_btn_hover_bg     = get_sub_field('secondary_button_hover_bg_color');
$secondary_btn_hover_text   = get_sub_field('secondary_button_hover_text_color');
$secondary_btn_hover_border = get_sub_field('secondary_button_hover_border_color');

// ---- Section visibility (entire block) ----
$visibility_mode       = get_sub_field('visibility_mode');        // none | hide_below | hide_above
$visibility_breakpoint = get_sub_field('visibility_breakpoint');  // xxs|xs|mob|sm|md|lg|xl|xxl|ultrawide

// ---- Element visibility (heading & buttons) ----
$heading_visibility_mode       = get_sub_field('heading_visibility_mode');        // none|hide_below|hide_above
$heading_visibility_breakpoint = get_sub_field('heading_visibility_breakpoint');

$primary_visibility_mode       = get_sub_field('primary_button_visibility_mode'); // none|hide_below|hide_above
$primary_visibility_breakpoint = get_sub_field('primary_button_visibility_breakpoint');

$secondary_visibility_mode       = get_sub_field('secondary_button_visibility_mode'); // none|hide_below|hide_above
$secondary_visibility_breakpoint = get_sub_field('secondary_button_visibility_breakpoint');

// ---- Padding controls ----
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

// ---- Unique section ID ----
$section_id = 'cta-' . uniqid();

// ---- Visibility class map (literal classes for Tailwind) ----
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

// Helper to resolve display classes
$resolve_visibility = function ($mode, $bp) use ($visibility_map) {
    $classes = 'flex';
    if (!empty($mode) && $mode !== 'none' && !empty($bp)) {
        if (isset($visibility_map[$bp][$mode])) {
            $classes = $visibility_map[$bp][$mode];
        }
    }
    return $classes;
};

$section_display_classes   = $resolve_visibility($visibility_mode, $visibility_breakpoint);
$heading_display_classes   = $resolve_visibility($heading_visibility_mode, $heading_visibility_breakpoint);
$primary_display_classes   = $resolve_visibility($primary_visibility_mode, $primary_visibility_breakpoint);
$secondary_display_classes = $resolve_visibility($secondary_visibility_mode, $secondary_visibility_breakpoint);

// ---- Unique button classes for WCAG hover/focus styles ----
$primary_button_class   = 'btn-' . uniqid('pri-');
$secondary_button_class = 'btn-' . uniqid('sec-');
?>
<section
    id="<?php echo esc_attr($section_id); ?>"
    class="relative flex overflow-hidden <?php echo esc_attr($section_display_classes); ?>"
    role="region"
    aria-labelledby="<?php echo esc_attr($section_id); ?>-heading"
    style="background-color: <?php echo esc_attr($background_color); ?>; color: <?php echo esc_attr($text_color); ?>;"
>
    <div class="flex flex-col items-center w-full mx-auto max-w-container pt-5 pb-5 max-lg:px-5 <?php echo esc_attr(implode(' ', $padding_classes)); ?>">
        <div class="box-border flex relative flex-col md:gap-4 items-center py-0 mx-auto my-0 w-full max-w-[1728px] max-lg:py-0 max-sm:py-0" style="<?php echo $underline_color ? 'text-decoration-color:' . esc_attr($underline_color) . ';' : ''; ?>">
            <div class="flex relative justify-between items-center w-full max-w-[1568px] max-lg:flex-col max-lg:gap-6 max-lg:items-center">
                
                <?php if (!empty($heading)) : ?>
                    <div class="relative text-3xl font-bold tracking-wider leading-10 text-center max-lg:text-center <?php echo esc_attr($heading_display_classes); ?>">
                        <<?php echo esc_attr($heading_tag); ?>
                            id="<?php echo esc_attr($section_id); ?>-heading"
                            class="text-3xl font-bold max-lg:text-3xl max-lg:leading-9 max-sm:text-2xl max-sm:tracking-wide max-sm:leading-8"
                        >
                            <?php echo esc_html($heading); ?>
                        </<?php echo esc_attr($heading_tag); ?>>
                    </div>
                <?php endif; ?>

                <div class="flex relative gap-4 items-start max-lg:flex-col max-lg:w-full max-lg:max-w-[400px] max-sm:gap-3" role="group" aria-label="Call to action buttons">
                    
                    <?php if (is_array($primary_button) && isset($primary_button['url'], $primary_button['title'])) : ?>
                        <a
                            href="<?php echo esc_url($primary_button['url']); ?>"
                            class="flex relative gap-2 justify-center items-center px-8 py-4 h-14 whitespace-nowrap border transition-colors duration-300 cursor-pointer max-lg:justify-center max-lg:w-full max-sm:px-6 max-sm:py-3.5 max-sm:h-auto w-fit <?php echo esc_attr($primary_button_class); ?> <?php echo esc_attr($primary_display_classes); ?>"
                            target="<?php echo esc_attr(isset($primary_button['target']) ? $primary_button['target'] : '_self'); ?>"
                            aria-label="<?php echo esc_attr($primary_button['title']); ?>"
                            style="background-color: <?php echo esc_attr($primary_btn_bg); ?>; color: <?php echo esc_attr($primary_btn_text); ?>; border-color: <?php echo esc_attr($primary_btn_border ?: $border_color); ?>;"
                        >
                            <span class="relative text-xl tracking-wide leading-5 text-center max-sm:text-lg max-sm:leading-5">
                                <?php echo esc_html($primary_button['title']); ?>
                            </span>
                        </a>
                        <style>
                            .<?php echo esc_attr($primary_button_class); ?>:hover,
                            .<?php echo esc_attr($primary_button_class); ?>:focus {
                                background-color: <?php echo esc_attr($primary_btn_hover_bg ?: $primary_btn_bg); ?> !important;
                                color: <?php echo esc_attr($primary_btn_hover_text ?: $primary_btn_text); ?> !important;
                                border-color: <?php echo esc_attr($primary_btn_hover_border ?: ($primary_btn_border ?: $border_color)); ?> !important;
                                outline: 2px solid <?php echo esc_attr($primary_btn_hover_border ?: ($primary_btn_border ?: $border_color)); ?>;
                                outline-offset: 2px;
                            }
                            .<?php echo esc_attr($primary_button_class); ?>:hover svg path,
                            .<?php echo esc_attr($primary_button_class); ?>:focus svg path {
                                stroke: <?php echo esc_attr($primary_btn_hover_text ?: $primary_btn_text); ?>;
                            }
                        </style>
                    <?php endif; ?>

                    <?php if (is_array($secondary_button) && isset($secondary_button['url'], $secondary_button['title'])) : ?>
                        <a
                            href="<?php echo esc_url($secondary_button['url']); ?>"
                            class="flex relative gap-2 justify-center items-center px-8 py-4 whitespace-nowrap border transition-colors duration-300 cursor-pointer max-lg:justify-center max-lg:w-full max-sm:px-6 max-sm:py-3.5 max-sm:h-auto w-fit <?php echo esc_attr($secondary_button_class); ?> <?php echo esc_attr($secondary_display_classes); ?>"
                            target="<?php echo esc_attr(isset($secondary_button['target']) ? $secondary_button['target'] : '_self'); ?>"
                            aria-label="<?php echo esc_attr($secondary_button['title']); ?>"
                            style="background-color: <?php echo esc_attr($secondary_btn_bg); ?>; color: <?php echo esc_attr($secondary_btn_text); ?>; border-color: <?php echo esc_attr($secondary_btn_border ?: $border_color); ?>;"
                        >
                            <span class="relative text-xl leading-5 max-sm:text-lg max-sm:leading-5">
                                <?php echo esc_html($secondary_button['title']); ?>
                            </span>
                        </a>
                        <style>
                            .<?php echo esc_attr($secondary_button_class); ?>:hover,
                            .<?php echo esc_attr($secondary_button_class); ?>:focus {
                                background-color: <?php echo esc_attr($secondary_btn_hover_bg ?: $secondary_btn_bg); ?> !important;
                                color: <?php echo esc_attr($secondary_btn_hover_text ?: $secondary_btn_text); ?> !important;
                                border-color: <?php echo esc_attr($secondary_btn_hover_border ?: ($secondary_btn_border ?: $border_color)); ?> !important;
                                outline: 2px solid <?php echo esc_attr($secondary_btn_hover_border ?: ($secondary_btn_border ?: $border_color)); ?>;
                                outline-offset: 2px;
                            }
                            .<?php echo esc_attr($secondary_button_class); ?>:hover svg path,
                            .<?php echo esc_attr($secondary_button_class); ?>:focus svg path {
                                stroke: <?php echo esc_attr($secondary_btn_hover_text ?: $secondary_btn_text); ?>;
                            }
                        </style>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</section>
