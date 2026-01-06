<?php
// === Variables (always use get_sub_field) ===
$heading = get_sub_field('heading') ?: 'Lorem ipsum';
$heading_tag = get_sub_field('heading_tag') ?: 'h2';
$description = get_sub_field('description') ?: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit ut aliquam, purus sit amet luctus venenatis, lectus magna fringilla urna, porttitor rhoncus dolor purus non enim praesent elementum facilisis leo, vel fringilla est ullamcorper eget nulla facilisi etiam dignissim diam quis.';

// Contact details
$phone_label = get_sub_field('phone_label') ?: 'CALL US';
$phone_number = get_sub_field('phone_number') ?: '+353 1 611 0000';
$phone_icon = get_sub_field('phone_icon');

$email_label = get_sub_field('email_label') ?: 'EMAIL US';
$email_address = get_sub_field('email_address') ?: 'connect@whitneymoore.ie';
$email_icon = get_sub_field('email_icon');

$address_label = get_sub_field('address_label') ?: 'COME TO FIND US';
$address_text = get_sub_field('address_text') ?: 'Whitney Moore LLP, 2 Shelbourne Buildings, Crampton Avenue, Shelbourne Road, Ballsbridge, Dublin 4, D04 W3V6, Ireland.';
$address_link_text = get_sub_field('address_link_text') ?: 'Get directions';
$address_link = get_sub_field('address_link');
$address_icon = get_sub_field('address_icon');

$social_label = get_sub_field('social_label') ?: 'FOLLOW US';

// Form settings
$form_heading = get_sub_field('form_heading') ?: 'Send us a message';
$form_heading_tag = get_sub_field('form_heading_tag') ?: 'h2';
$form_markup = get_sub_field('form_markup', false, false);
if ($form_markup) {
    $form_markup = preg_replace('#</?p[^>]*>#i', '', $form_markup);
    $form_markup = preg_replace('#<br\s*/?>#i', '', $form_markup);
}

$privacy_policy_url = get_sub_field('privacy_policy_url') ?: '#';

// Colors and styling
$background_color = get_sub_field('background_color') ?: '#ffffff';
$text_color = get_sub_field('text_color') ?: '#000000';
$contact_bg_color = get_sub_field('contact_bg_color') ?: 'rgba(229, 229, 229, 0.3)';
$form_bg_color = get_sub_field('form_bg_color') ?: 'rgba(99, 102, 241, 0.3)';

// Padding classes
$padding_classes = [''];
if (have_rows('padding_settings')) {
    while (have_rows('padding_settings')) {
        the_row();
        $screen_size = get_sub_field('screen_size');
        $padding_top = (string) get_sub_field('padding_top');
        $padding_bottom = (string) get_sub_field('padding_bottom');
        if ($screen_size !== '') {
            $padding_classes[] = "{$screen_size}:pt-[{$padding_top}rem]";
            $padding_classes[] = "{$screen_size}:pb-[{$padding_bottom}rem]";
        }
    }
}

// Unique section id
$section_id = 'contact-form-' . esc_attr(wp_generate_uuid4());

// ===== Form plumbing: inject action, nonce, posted mail config, privacy link =====
if ($form_markup) {
    $form_markup = str_replace(
        '<form',
        sprintf(
            '<form action="%1$s" method="post" enctype="multipart/form-data" data-theme-form="%2$s"',
            esc_url(admin_url('admin-post.php')),
            esc_attr(get_row_index())
        ),
        $form_markup
    );

    $hidden = sprintf(
        '<input type="hidden" name="action" value="theme_form_submit">
        <input type="hidden" name="theme_form_nonce" value="%1$s">
        <input type="hidden" name="_theme_form_id" value="%2$s">
        <input type="hidden" name="_submission_uid" value="%3$s">',
        esc_attr(wp_create_nonce('theme_form_submit')),
        esc_attr(get_row_index()),
        esc_attr(wp_generate_uuid4())
    );

    if ($name = get_sub_field('form_name')) {
        $hidden .= '<input type="hidden" name="_theme_form_name" value="' . esc_attr($name) . '">';
    }
    if (get_sub_field('save_entries_to_db')) {
        $hidden .= '<input type="hidden" name="_theme_save_to_db" value="1">';
    }

    // Mail config (posted)
    $cfg_to = get_sub_field('email_to') ?: get_option('admin_email');
    $cfg_bcc = get_sub_field('email_bcc') ?: '';
    $cfg_subject = get_sub_field('email_subject') ?: '';
    $cfg_from_name = get_sub_field('from_name') ?: '';
    $cfg_from_email = get_sub_field('from_email') ?: '';

    $hidden_cfg = '';
    $hidden_cfg .= '<input type="hidden" name="_cfg_to" value="' . esc_attr($cfg_to) . '">';
    $hidden_cfg .= '<input type="hidden" name="_cfg_bcc" value="' . esc_attr($cfg_bcc) . '">';
    $hidden_cfg .= '<input type="hidden" name="_cfg_subject" value="' . esc_attr($cfg_subject) . '">';
    $hidden_cfg .= '<input type="hidden" name="_cfg_from_name" value="' . esc_attr($cfg_from_name) . '">';
    $hidden_cfg .= '<input type="hidden" name="_cfg_from_email" value="' . esc_attr($cfg_from_email) . '">';

    if (get_sub_field('enable_autoresponder')) {
        $hidden_cfg .= '<input type="hidden" name="_cfg_auto_enabled" value="1">';
        $hidden_cfg .= '<input type="hidden" name="_cfg_auto_subject" value="' . esc_attr(get_sub_field('autoresponder_subject') ?: '') . '">';
        $hidden_cfg .= '<input type="hidden" name="_cfg_auto_message" value="' . esc_attr(get_sub_field('autoresponder_message') ?: '') . '">';
    }

    $form_markup = str_replace('</form>', ($hidden . $hidden_cfg) . '</form>', $form_markup);
    $form_markup = str_replace('href="#"', 'href="' . esc_url($privacy_policy_url) . '"', $form_markup);
}
?>

<section id="<?php echo esc_attr($section_id); ?>"
         class="relative flex overflow-hidden <?php echo esc_attr(implode(' ', $padding_classes)); ?>"
         style="background-color: <?php echo esc_attr($background_color); ?>; color: <?php echo esc_attr($text_color); ?>;">
    <div class="flex flex-col items-center pt-20 pb-24 mx-auto w-full max-w-container max-lg:px-5">
        <div class="flex overflow-hidden flex-wrap gap-10 justify-center items-start w-full">

            <!-- Contact Details Section -->
            <div class="flex-1 pt-12 shrink basis-28 min-w-60 max-md:max-w-full">
                <div class="w-full max-md:max-w-full">
                    <?php if ($heading): ?>
                        <<?php echo esc_attr($heading_tag); ?> class="text-3xl font-semibold leading-tight text-indigo-800 max-md:max-w-full">
                            <?php echo esc_html($heading); ?>
                        </<?php echo esc_attr($heading_tag); ?>>
                    <?php endif; ?>

                    <?php if ($description): ?>
                        <div class="mt-4 text-lg tracking-wider leading-7 text-black max-md:max-w-full wp_editor">
                            <?php echo wp_kses_post($description); ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="px-12 py-10 mt-8 w-full max-md:px-5 max-md:max-w-full"
                     style="background-color: <?php echo esc_attr($contact_bg_color); ?>;">

                    <!-- Phone Section -->
                    <?php if ($phone_number): ?>
                        <div class="w-full tracking-wider max-md:max-w-full">
                            <div class="text-lg font-medium text-black max-md:max-w-full">
                                <?php echo esc_html($phone_label); ?>
                            </div>
                            <div class="flex flex-wrap gap-4 items-center mt-4 w-full text-2xl leading-none text-indigo-800 max-md:max-w-full">
                                <?php if ($phone_icon): ?>
                                    <?php echo wp_get_attachment_image($phone_icon, 'full', false, [
                                        'class' => 'object-contain shrink-0 self-stretch my-auto w-8 aspect-square',
                                        'alt' => 'Phone icon'
                                    ]); ?>
                                <?php else: ?>
                                    <img src="https://api.builder.io/api/v1/image/assets/f35586c581c84ecf82b6de32c55ed39e/fb9d93512043514ef80e18a9a169ba6eccf9cc38?placeholderIfAbsent=true"
                                         class="object-contain self-stretch my-auto w-8 shrink-0 aspect-square"
                                         alt="Phone icon" />
                                <?php endif; ?>
                                <a href="tel:<?php echo esc_attr(preg_replace('/[^+\d]/', '', $phone_number)); ?>"
                                   class="flex-1 self-stretch my-auto text-indigo-800 shrink basis-0 max-md:max-w-full hover:underline focus:outline-none focus:ring-2 focus:ring-indigo-800 focus:ring-offset-2">
                                    <?php echo esc_html($phone_number); ?>
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Email Section -->
                    <?php if ($email_address): ?>
                        <div class="mt-10 w-full tracking-wider max-md:max-w-full">
                            <div class="text-lg font-medium text-black max-md:max-w-full">
                                <?php echo esc_html($email_label); ?>
                            </div>
                            <div class="flex flex-wrap gap-4 items-center mt-4 w-full text-2xl leading-none text-indigo-800 whitespace-nowrap max-md:max-w-full">
                                <?php if ($email_icon): ?>
                                    <?php echo wp_get_attachment_image($email_icon, 'full', false, [
                                        'class' => 'object-contain shrink-0 self-stretch my-auto w-8 aspect-square',
                                        'alt' => 'Email icon'
                                    ]); ?>
                                <?php else: ?>
                                    <img src="https://api.builder.io/api/v1/image/assets/f35586c581c84ecf82b6de32c55ed39e/fef238ead5417dc207bf11cd8bdec3132e2d8ada?placeholderIfAbsent=true"
                                         class="object-contain self-stretch my-auto w-8 shrink-0 aspect-square"
                                         alt="Email icon" />
                                <?php endif; ?>
                                <a href="mailto:<?php echo esc_attr($email_address); ?>"
                                   class="flex-1 self-stretch my-auto text-indigo-800 shrink basis-0 max-md:max-w-full hover:underline focus:outline-none focus:ring-2 focus:ring-indigo-800 focus:ring-offset-2">
                                    <?php echo esc_html($email_address); ?>
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Address Section -->
                    <?php if ($address_text): ?>
                        <div class="mt-10 w-full text-lg tracking-wider max-md:max-w-full">
                            <div class="font-medium text-black max-md:max-w-full">
                                <?php echo esc_html($address_label); ?>
                            </div>
                            <div class="flex flex-wrap gap-4 items-start mt-4 w-full leading-6 text-indigo-800 max-md:max-w-full">
                                <?php if ($address_icon): ?>
                                    <?php echo wp_get_attachment_image($address_icon, 'full', false, [
                                        'class' => 'object-contain shrink-0 w-8 aspect-square',
                                        'alt' => 'Location icon'
                                    ]); ?>
                                <?php else: ?>
                                    <img src="https://api.builder.io/api/v1/image/assets/f35586c581c84ecf82b6de32c55ed39e/fef238ead5417dc207bf11cd8bdec3132e2d8ada?placeholderIfAbsent=true"
                                         class="object-contain w-8 shrink-0 aspect-square"
                                         alt="Location icon" />
                                <?php endif; ?>
                                <div class="flex-1 text-indigo-800 shrink basis-0 max-md:max-w-full">
                                    <span class="text-black">
                                        <?php echo esc_html($address_text); ?>
                                    </span>
                                    <?php if ($address_link && is_array($address_link) && isset($address_link['url'], $address_link['title'])): ?>
                                        <br />
                                        <a href="<?php echo esc_url($address_link['url']); ?>"
                                           class="text-indigo-800 hover:underline focus:outline-none focus:ring-2 focus:ring-indigo-800 focus:ring-offset-2"
                                           target="<?php echo esc_attr($address_link['target'] ?? '_self'); ?>">
                                            <?php echo esc_html($address_link['title']); ?>
                                        </a>
                                    <?php elseif ($address_link_text): ?>
                                        <br />
                                        <span class="text-indigo-800"><?php echo esc_html($address_link_text); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Social Media Section -->
                    <?php if (have_rows('social_links')): ?>
                        <div class="flex flex-col mt-10 w-full max-md:max-w-full">
                            <div class="text-lg font-medium tracking-wider text-black max-md:max-w-full">
                                <?php echo esc_html($social_label); ?>
                            </div>
                            <div class="flex gap-4 items-center self-start mt-4">
                                <?php while (have_rows('social_links')): the_row();
                                    $social_icon = get_sub_field('social_icon');
                                    $social_url = get_sub_field('social_url');
                                    $social_label_text = get_sub_field('social_label') ?: 'Social media link';

                                    if ($social_url && is_array($social_url) && isset($social_url['url'])):
                                ?>
                                    <a href="<?php echo esc_url($social_url['url']); ?>"
                                       class="transition-opacity focus:outline-none focus:ring-2 focus:ring-indigo-800 focus:ring-offset-2 hover:opacity-80"
                                       target="<?php echo esc_attr($social_url['target'] ?? '_blank'); ?>"
                                       aria-label="<?php echo esc_attr($social_label_text); ?>">
                                        <?php if ($social_icon): ?>
                                            <?php echo wp_get_attachment_image($social_icon, 'full', false, [
                                                'class' => 'object-contain shrink-0 self-stretch my-auto w-8 aspect-square',
                                                'alt' => esc_attr($social_label_text)
                                            ]); ?>
                                        <?php else: ?>
                                            <img src="https://api.builder.io/api/v1/image/assets/f35586c581c84ecf82b6de32c55ed39e/fe3c245fc56e8bfbc242773908253ed6c3314887?placeholderIfAbsent=true"
                                                 class="object-contain self-stretch my-auto w-8 shrink-0 aspect-square"
                                                 alt="<?php echo esc_attr($social_label_text); ?>" />
                                        <?php endif; ?>
                                    </a>
                                <?php endif; endwhile; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Contact Form Section -->
            <div class="p-14 min-w-60 w-[703px] max-md:px-5 max-md:max-w-full"
                 style="background-color: <?php echo esc_attr($form_bg_color); ?>;">

                <?php if ($form_heading): ?>
                    <<?php echo esc_attr($form_heading_tag); ?> class="text-3xl font-semibold leading-tight text-indigo-800 max-md:max-w-full">
                        <?php echo esc_html($form_heading); ?>
                    </<?php echo esc_attr($form_heading_tag); ?>>
                <?php endif; ?>

                <?php if ($form_markup): ?>
                    <?php
                    echo wp_kses(
                        $form_markup,
                        [
                            'form' => ['class' => [], 'role' => [], 'aria-labelledby' => [], 'novalidate' => [], 'action' => [], 'method' => [], 'enctype' => [], 'data-theme-form' => []],
                            'div' => ['class' => [], 'id' => [], 'role' => [], 'aria-live' => [], 'aria-describedby' => []],
                            'label' => ['for' => [], 'class' => [], 'id' => []],
                            'input' => ['type' => [], 'id' => [], 'name' => [], 'placeholder' => [], 'required' => [], 'aria-required' => [], 'aria-describedby' => [], 'autocomplete' => [], 'class' => [], 'value' => []],
                            'textarea' => ['id' => [], 'name' => [], 'placeholder' => [], 'required' => [], 'aria-required' => [], 'aria-describedby' => [], 'rows' => [], 'class' => []],
                            'button' => ['type' => [], 'class' => [], 'aria-describedby' => []],
                            'span' => ['class' => [], 'id' => []],
                            'img' => ['src' => [], 'alt' => [], 'class' => [], 'width' => [], 'height' => []],
                            'a' => ['href' => [], 'class' => [], 'target' => [], 'aria-label' => []],
                        ]
                    );
                    ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
