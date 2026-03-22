<?php
// === Variables (always use get_sub_field) ===
$heading = get_sub_field('heading') ?: 'Get in touch';
$heading_tag = get_sub_field('heading_tag') ?: 'h2';
$description = get_sub_field('description') ?: 'Our team is here to help. Share your query and we will connect you with the right lawyer.';

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

<section id="<?php echo esc_attr($section_id); ?>" data-matrix-block="<?php echo esc_attr(str_replace('_', '-', get_row_layout()) . '-' . get_row_index()); ?>" 
         class="relative flex overflow-hidden <?php echo esc_attr(implode(' ', $padding_classes)); ?>"
         style="background-color: <?php echo esc_attr($background_color); ?>; color: <?php echo esc_attr($text_color); ?>;">
    <div class="flex flex-col items-center pt-0 pb-8 mx-auto w-full md:pt-20 md:pb-24 max-w-container max-xxl:px-5">
        <div class="flex overflow-hidden flex-wrap gap-10 justify-center items-start w-full">

            <!-- Contact Details Section -->
            <div class="flex-1 pt-12 shrink basis-28 min-w-60 max-md:max-w-full">
                <div class="w-full max-md:max-w-full">
                    <?php if ($heading): ?>
                        <<?php echo esc_attr($heading_tag); ?> class="text-3xl font-semibold leading-tight text-primary max-md:max-w-full">
                            <?php echo esc_html($heading); ?>
                        </<?php echo esc_attr($heading_tag); ?>>
                    <?php endif; ?>

                    <?php if ($description): ?>
                        <div class="mt-4 text-lg tracking-wider leading-7 text-black wp_editor max-w-[697px] w-full">
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
                            <div class="flex flex-wrap gap-4 items-center mt-4 w-full text-2xl leading-none text-primary max-md:max-w-full">
                                <?php if ($phone_icon): ?>
                                    <?php echo wp_get_attachment_image($phone_icon, 'full', false, [
                                        'class' => 'object-contain shrink-0 self-stretch my-auto w-8 aspect-square',
                                        'alt' => 'Phone icon'
                                    ]); ?>
                                <?php else: ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32" fill="none">
                                    <path d="M29.3339 22.56V26.56C29.3354 26.9313 29.2593 27.2989 29.1106 27.6391C28.9618 27.9793 28.7436 28.2848 28.47 28.5358C28.1964 28.7868 27.8733 28.9779 27.5216 29.0969C27.1698 29.2159 26.7971 29.2601 26.4272 29.2266C22.3243 28.7808 18.3832 27.3788 14.9206 25.1333C11.699 23.0862 8.96768 20.3549 6.92056 17.1333C4.6672 13.6549 3.26489 9.69463 2.82723 5.5733C2.79391 5.20459 2.83773 4.83298 2.9559 4.48213C3.07406 4.13128 3.26399 3.80889 3.51358 3.53546C3.76318 3.26204 4.06697 3.04358 4.40562 2.894C4.74426 2.74441 5.11035 2.66698 5.48056 2.66663H9.48056C10.1276 2.66026 10.755 2.8894 11.2456 3.31134C11.7362 3.73328 12.0567 4.31923 12.1472 4.95997C12.3161 6.24006 12.6292 7.49694 13.0806 8.70663C13.26 9.18387 13.2988 9.70252 13.1924 10.2011C13.0861 10.6998 12.839 11.1574 12.4806 11.52L10.7872 13.2133C12.6853 16.5514 15.4492 19.3152 18.7872 21.2133L20.4806 19.52C20.8431 19.1615 21.3008 18.9144 21.7994 18.8081C22.298 18.7018 22.8167 18.7406 23.2939 18.92C24.5036 19.3714 25.7605 19.6845 27.0406 19.8533C27.6883 19.9447 28.2798 20.2709 28.7026 20.77C29.1254 21.269 29.3501 21.9061 29.3339 22.56Z" stroke="#0902A4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                <?php endif; ?>
                                <a href="tel:<?php echo esc_attr(preg_replace('/[^+\d]/', '', $phone_number)); ?>"
                                   class="flex-1 self-stretch my-auto text-2xl font-normal tracking-wide leading-7 text-primary shrink basis-0 max-md:max-w-full hover:underline focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2">
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
                            <div class="flex flex-wrap gap-4 items-center mt-4 w-full text-2xl leading-none whitespace-nowrap text-primary max-md:max-w-full">
                                <?php if ($email_icon): ?>
                                    <?php echo wp_get_attachment_image($email_icon, 'full', false, [
                                        'class' => 'object-contain shrink-0 self-stretch my-auto w-8 aspect-square',
                                        'alt' => 'Email icon'
                                    ]); ?>
                                <?php else: ?>
                                   <svg xmlns="http://www.w3.org/2000/svg" width="29" height="24" viewBox="0 0 29 24" fill="none">
                                    <path d="M27.6667 3.66667C27.6667 2.2 26.4667 1 25 1H3.66667C2.2 1 1 2.2 1 3.66667M27.6667 3.66667V19.6667C27.6667 21.1333 26.4667 22.3333 25 22.3333H3.66667C2.2 22.3333 1 21.1333 1 19.6667V3.66667M27.6667 3.66667L14.3333 13L1 3.66667" stroke="#0902A4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                <?php endif; ?>
                                <a href="mailto:<?php echo esc_attr($email_address); ?>"
                                   class="flex-1 self-stretch my-auto text-2xl font-normal tracking-wide leading-7 text-primary shrink basis-0 max-md:max-w-full hover:underline focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2">
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
                            <div class="flex flex-wrap gap-4 items-start mt-4 w-full leading-6 text-primary max-md:max-w-full">
                                <?php if ($address_icon): ?>
                                    <?php echo wp_get_attachment_image($address_icon, 'full', false, [
                                        'class' => 'object-contain shrink-0 w-8 aspect-square',
                                        'alt' => 'Location icon'
                                    ]); ?>
                                <?php else: ?>
                                   <svg xmlns="http://www.w3.org/2000/svg" width="29" height="24" viewBox="0 0 29 24" fill="none">
                                    <path d="M27.6667 3.66667C27.6667 2.2 26.4667 1 25 1H3.66667C2.2 1 1 2.2 1 3.66667M27.6667 3.66667V19.6667C27.6667 21.1333 26.4667 22.3333 25 22.3333H3.66667C2.2 22.3333 1 21.1333 1 19.6667V3.66667M27.6667 3.66667L14.3333 13L1 3.66667" stroke="#0902A4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                <?php endif; ?>
                                <div class="flex-1 text-primary shrink basis-0 max-md:max-w-full">
                                    <span class="text-black text-[16px]font-normal leading-6 tracking-[1px]">
                                        <?php echo esc_html($address_text); ?>
                                    </span>
                                    <?php if ($address_link && is_array($address_link) && isset($address_link['url'], $address_link['title'])): ?>
                                        <br />
                                        <a href="<?php echo esc_url($address_link['url']); ?>"
                                           class="text-primary hover:underline focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
                                           target="<?php echo esc_attr($address_link['target'] ?? '_self'); ?>">
                                            <?php echo esc_html($address_link['title']); ?>
                                        </a>
                                    <?php elseif ($address_link_text): ?>
                                        <br />
                                        <span class="text-primary"><?php echo esc_html($address_link_text); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Social Media Section -->
                    <?php if (have_rows('social_links')): ?>
                        <div class="flex flex-col mt-10 w-full max-md:max-w-full">
                            <div class="font-medium text-[18px] leading-normal tracking-[1px] text-black max-md:max-w-full">
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
                                       class="transition-opacity focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 hover:opacity-80"
                                       target="<?php echo esc_attr($social_url['target'] ?? '_blank'); ?>"
                                       aria-label="<?php echo esc_attr($social_label_text); ?>">
                                        <?php if ($social_icon): ?>
                                            <?php echo wp_get_attachment_image($social_icon, 'full', false, [
                                                'class' => 'object-contain shrink-0 self-stretch my-auto w-8 aspect-square',
                                                'alt' => esc_attr($social_label_text)
                                            ]); ?>
                                        <?php else: ?>
                                          <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32" fill="none">
                                            <path d="M21.334 10.6666C23.4557 10.6666 25.4905 11.5095 26.9908 13.0098C28.4911 14.5101 29.334 16.5449 29.334 18.6666V28H24.0007V18.6666C24.0007 17.9594 23.7197 17.2811 23.2196 16.781C22.7195 16.2809 22.0412 16 21.334 16C20.6267 16 19.9485 16.2809 19.4484 16.781C18.9483 17.2811 18.6673 17.9594 18.6673 18.6666V28H13.334V18.6666C13.334 16.5449 14.1768 14.5101 15.6771 13.0098C17.1774 11.5095 19.2123 10.6666 21.334 10.6666Z" stroke="#0902A4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M7.99935 12H2.66602V28H7.99935V12Z" stroke="#0902A4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M5.33268 7.99996C6.80544 7.99996 7.99935 6.80605 7.99935 5.33329C7.99935 3.86053 6.80544 2.66663 5.33268 2.66663C3.85992 2.66663 2.66602 3.86053 2.66602 5.33329C2.66602 6.80605 3.85992 7.99996 5.33268 7.99996Z" stroke="#0902A4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
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
                    <<?php echo esc_attr($form_heading_tag); ?> class="text-3xl font-semibold leading-tight text-primary max-md:max-w-full">
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
