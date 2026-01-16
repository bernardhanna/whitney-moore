<?php
$heading = get_sub_field('heading');
$heading_tag = get_sub_field('heading_tag');
$description = get_sub_field('description');

// Padding settings
$padding_classes = [];
if (have_rows('padding_settings')) {
    while (have_rows('padding_settings')) {
        the_row();
        $screen_size = get_sub_field('screen_size');
        $padding_top = get_sub_field('padding_top');
        $padding_bottom = get_sub_field('padding_bottom');
        $padding_classes[] = "{$screen_size}:pt-[{$padding_top}rem]";
        $padding_classes[] = "{$screen_size}:pb-[{$padding_bottom}rem]";
    }
}

// Generate unique section ID
$section_id = 'title_' . uniqid();
?>

<section
    id="<?php echo esc_attr($section_id); ?>"
    class="relative flex overflow-hidden <?php echo esc_attr(implode(' ', $padding_classes)); ?>"
    role="region"
    aria-labelledby="<?php echo esc_attr($section_id); ?>_heading"
>
    <div class="flex flex-col items-center pt-5 pb-5 mx-auto w-full max-w-container max-lg:px-5">
        <div class="box-border flex relative flex-col gap-8 items-center px-20 pt-20 pb-1 mx-auto my-0 w-full max-w-[1723px] max-md:px-10 max-md:pt-12 max-md:pb-1 max-md:max-w-[991px] max-sm:px-5 max-sm:pt-8 max-sm:pb-1 max-sm:max-w-screen-sm">
            <div class="flex relative flex-col gap-4 items-start self-stretch w-full">
                <?php if (!empty($heading)): ?>
                    <<?php echo esc_attr($heading_tag); ?>
                        id="<?php echo esc_attr($section_id); ?>_heading"
                        class="relative w-full text-3xl font-bold tracking-wider leading-10 text-primary max-md:text-3xl max-md:leading-9 max-sm:text-2xl max-sm:leading-8"
                    >
                        <?php echo esc_html($heading); ?>
                    </<?php echo esc_attr($heading_tag); ?>>
                <?php endif; ?>

                <?php if (!empty($description)): ?>
                    <div
                        class="relative self-stretch text-lg leading-7 text-black max-md:text-base max-md:leading-6 max-sm:text-sm max-sm:leading-6 wp_editor"
                        role="text"
                    >
                        <?php echo wp_kses_post($description); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
