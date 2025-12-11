<?php
$heading = get_sub_field('heading');
$heading_tag = get_sub_field('heading_tag');
$decorative_image = get_sub_field('decorative_image');
$decorative_image_alt = get_post_meta($decorative_image, '_wp_attachment_image_alt', true) ?: 'Decorative underline';
$solutions = get_sub_field('solutions');
$background_color = get_sub_field('background_color');

$padding_classes = [];
if (have_rows('padding_settings')) {
    while (have_rows('padding_settings')) {
        the_row();
        $screen_size   = get_sub_field('screen_size');
        $padding_top   = get_sub_field('padding_top');
        $padding_bottom= get_sub_field('padding_bottom');
        $padding_classes[] = "{$screen_size}:pt-[{$padding_top}rem]";
        $padding_classes[] = "{$screen_size}:pb-[{$padding_bottom}rem]";
    }
}

$section_id = 'solutions_' . wp_rand(1000, 9999);
?>

<section
    id="<?php echo esc_attr($section_id); ?>"
    class="flex overflow-hidden relative"
    style="background-color: <?php echo esc_attr($background_color); ?>;"
    aria-labelledby="<?php echo esc_attr($section_id); ?>_heading"
>
    <div class="flex flex-col items-center w-full mx-auto max-w-container max-lg:px-5 <?php echo esc_attr(implode(' ', $padding_classes)); ?>">
        <div class="gap-12 py-12 my-auto w-full lg:py-20 max-md:px-5 max-md:max-w-full">
            <?php if (!empty($heading)): ?>
                <header class="w-full text-3xl font-semibold tracking-normal leading-none text-center text-slate-950 max-md:max-w-full">
                    <div class="flex flex-col gap-6 items-center w-full max-md:max-w-full">
                        <<?php echo esc_attr($heading_tag); ?>
                            id="<?php echo esc_attr($section_id); ?>_heading"
                            class="tracking-normal leading-10 text-slate-950"
                        >
                            <?php echo esc_html($heading); ?>
                        </<?php echo esc_attr($heading_tag); ?>>

                        <?php if ($decorative_image): ?>
                            <div class="mt-6" role="presentation" aria-hidden="true">
                                <?php echo wp_get_attachment_image($decorative_image, 'full', false, [
                                    'alt'   => esc_attr($decorative_image_alt),
                                    'class' => 'object-contain w-[71px] h-auto', // removed aspect ratio class
                                ]); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </header>
            <?php endif; ?>

            <?php if ($solutions && is_array($solutions)): ?>
                <div class="grid grid-cols-1 gap-8 items-stretch mt-12 w-full md:grid-cols-3 max-md:mt-10 max-md:max-w-full" role="list">
                    <?php foreach ($solutions as $index => $solution):
                        $action_word    = $solution['action_word'] ?? '';
                        $description    = $solution['description'] ?? '';
                        $button_link    = $solution['button_link'] ?? '';
                        $underline_color= $solution['underline_color'] ?? '#0ea5e9';
                        $card_id        = $section_id . '_card_' . ($index + 1);
                    ?>
                    <article
                        class="flex flex-col gap-4 p-8 h-full bg-gray-200 max-md:px-5"
                        role="listitem"
                        aria-labelledby="<?php echo esc_attr($card_id); ?>_heading"
                    >
                        <div class="flex flex-col justify-center w-full text-center">
                            <div class="flex flex-col items-center w-full text-2xl font-semibold tracking-normal leading-none text-slate-950">
                                <p class="tracking-normal leading-7 text-slate-950 font-primary">
                                    I want to
                                </p>

                                <div class="flex justify-center items-center text-7xl font-bold leading-none whitespace-nowrap border-b-8 border-solid max-md:text-4xl"
                                     style="border-bottom-color: <?php echo esc_attr($underline_color); ?>;">
                                    <h3
                                        id="<?php echo esc_attr($card_id); ?>_heading"
                                        class="self-stretch my-auto text-7xl font-primary tracking-normal leading-[92px] text-slate-950 max-md:text-4xl"
                                    >
                                        <?php echo esc_html($action_word); ?>
                                    </h3>
                                </div>

                                <p class="tracking-normal leading-7 text-slate-950 font-primary">
                                    my property
                                </p>
                            </div>

                            <?php if (!empty($description)): ?>
                                <div class="mt-4 text-base tracking-normal leading-7 text-black font-primary wp_editor">
                                    <?php echo wp_kses_post($description); ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <?php if ($button_link && is_array($button_link) && isset($button_link['url'], $button_link['title'])): ?>
                            <div class="flex justify-center mt-4">
                                <a
                                    href="<?php echo esc_url($button_link['url']); ?>"
                                    class="flex justify-center items-center w-12 h-12 transition-colors duration-300 bg-slate-950 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-950 hover:bg-slate-800"
                                    target="<?php echo esc_attr($button_link['target'] ?? '_self'); ?>"
                                    aria-label="<?php echo esc_attr($button_link['title'] . ' - ' . $action_word . ' property'); ?>"
                                >
                                    <span class="text-2xl text-white fa-solid fa-plus" aria-hidden="true"></span>
                                </a>
                            </div>
                        <?php endif; ?>
                    </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
