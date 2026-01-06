<?php

use StoutLogic\AcfBuilder\FieldsBuilder;

$contact_form = new FieldsBuilder('contact_form', [
    'label' => 'Contact Form with Details',
]);

$contact_form
    ->addTab('Content')
        ->addText('heading', [
            'label' => 'Main Heading',
            'default_value' => 'Lorem ipsum'
        ])
        ->addSelect('heading_tag', [
            'label' => 'Main Heading Tag',
            'choices' => [
                'h1' => 'H1',
                'h2' => 'H2',
                'h3' => 'H3',
                'h4' => 'H4',
                'h5' => 'H5',
                'h6' => 'H6',
                'p' => 'Paragraph',
                'span' => 'Span'
            ],
            'default_value' => 'h2',
        ])
        ->addWysiwyg('description', [
            'label' => 'Description',
            'default_value' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit ut aliquam, purus sit amet luctus venenatis, lectus magna fringilla urna, porttitor rhoncus dolor purus non enim praesent elementum facilisis leo, vel fringilla est ullamcorper eget nulla facilisi etiam dignissim diam quis.',
            'wrapper' => ['class' => 'wp_editor'],
            'media_upload' => 0,
            'tabs' => 'visual',
        ])

        // Phone Section
        ->addText('phone_label', [
            'label' => 'Phone Label',
            'default_value' => 'CALL US'
        ])
        ->addText('phone_number', [
            'label' => 'Phone Number',
            'default_value' => '+353 1 611 0000'
        ])
        ->addImage('phone_icon', [
            'label' => 'Phone Icon',
            'return_format' => 'id',
            'preview_size' => 'thumbnail',
        ])

        // Email Section
        ->addText('email_label', [
            'label' => 'Email Label',
            'default_value' => 'EMAIL US'
        ])
        ->addEmail('email_address', [
            'label' => 'Email Address',
            'default_value' => 'connect@whitneymoore.ie'
        ])
        ->addImage('email_icon', [
            'label' => 'Email Icon',
            'return_format' => 'id',
            'preview_size' => 'thumbnail',
        ])

        // Address Section
        ->addText('address_label', [
            'label' => 'Address Label',
            'default_value' => 'COME TO FIND US'
        ])
        ->addTextarea('address_text', [
            'label' => 'Address Text',
            'default_value' => 'Whitney Moore LLP, 2 Shelbourne Buildings, Crampton Avenue, Shelbourne Road, Ballsbridge, Dublin 4, D04 W3V6, Ireland.'
        ])
        ->addText('address_link_text', [
            'label' => 'Address Link Text',
            'default_value' => 'Get directions'
        ])
        ->addLink('address_link', [
            'label' => 'Address Link',
            'return_format' => 'array',
        ])
        ->addImage('address_icon', [
            'label' => 'Address Icon',
            'return_format' => 'id',
            'preview_size' => 'thumbnail',
        ])

        // Social Media Section
        ->addText('social_label', [
            'label' => 'Social Media Label',
            'default_value' => 'FOLLOW US'
        ])
        ->addRepeater('social_links', [
            'label' => 'Social Media Links',
            'layout' => 'table',
            'button_label' => 'Add Social Link',
        ])
            ->addText('social_label', ['label' => 'Platform Name'])
            ->addLink('social_url', [
                'label' => 'Social URL',
                'return_format' => 'array',
            ])
            ->addImage('social_icon', [
                'label' => 'Social Icon',
                'return_format' => 'id',
                'preview_size' => 'thumbnail',
            ])
        ->endRepeater()

        // Form Section
        ->addText('form_heading', [
            'label' => 'Form Heading',
            'default_value' => 'Send us a message'
        ])
        ->addSelect('form_heading_tag', [
            'label' => 'Form Heading Tag',
            'choices' => [
                'h1' => 'H1',
                'h2' => 'H2',
                'h3' => 'H3',
                'h4' => 'H4',
                'h5' => 'H5',
                'h6' => 'H6',
                'p' => 'Paragraph',
                'span' => 'Span'
            ],
            'default_value' => 'h2',
        ])
        ->addWysiwyg('form_markup', [
            'label' => 'Form HTML (paste static form here)',
            'instructions' => 'Paste the static HTML form code here.',
            'toolbar' => 'basic',
            'media_upload' => 0,
            'wrapper' => ['class' => 'wp_editor'],
        ])
        ->addUrl('privacy_policy_url', [
            'label' => 'Privacy Policy URL',
            'default_value' => '#'
        ])

    ->addTab('Email')
        ->addText('form_name', [
            'label' => 'Internal Form Name',
            'instructions' => 'Saved with each entry & used in email subject.',
            'default_value' => 'Contact Form'
        ])
        ->addText('from_name', [
            'label' => 'From Name (override)',
            'instructions' => 'Optional. Leave empty to use Theme Options.',
        ])
        ->addEmail('from_email', [
            'label' => 'From Email (override)',
            'instructions' => 'Use an address on your domain. Leave empty to use Theme Options.',
        ])
        ->addText('email_to', [
            'label' => 'Send To',
            'instructions' => 'One or more addresses. Separate with commas or semicolons.',
            'placeholder' => 'name@domain.ie, other@domain.ie',
            'default_value' => get_option('admin_email'),
        ])
        ->addText('email_bcc', [
            'label' => 'BCC',
            'instructions' => 'Optional. Separate multiple with commas or semicolons.',
            'placeholder' => 'first@domain.ie; second@domain.ie',
        ])
        ->addText('email_subject', [
            'label' => 'Subject',
            'default_value' => 'Website contact form enquiry'
        ])
        ->addTrueFalse('save_entries_to_db', [
            'label' => 'Save to DB?',
            'ui' => 1,
            'default_value' => 1
        ])

    ->addTab('Autoresponder')
        ->addTrueFalse('enable_autoresponder', [
            'label' => 'Enable?',
            'ui' => 1
        ])
        ->addText('autoresponder_subject', [
            'label' => 'Autoresponder Subject',
            'conditional_logic' => [[['field' => 'enable_autoresponder', 'operator' => '==', 'value' => 1]]],
            'default_value' => 'Thank you for your message'
        ])
        ->addWysiwyg('autoresponder_message', [
            'label' => 'Autoresponder Message',
            'conditional_logic' => [[['field' => 'enable_autoresponder', 'operator' => '==', 'value' => 1]]],
            'wrapper' => ['class' => 'wp_editor'],
            'default_value' => '<p>Thank you for contacting us. We will get back to you as soon as possible.</p>'
        ])

    ->addTab('Design')
        ->addColorPicker('background_color', [
            'label' => 'Background Color',
            'default_value' => '#ffffff'
        ])
        ->addColorPicker('text_color', [
            'label' => 'Text Color',
            'default_value' => '#000000'
        ])
        ->addColorPicker('contact_bg_color', [
            'label' => 'Contact Details Background Color',
            'default_value' => 'rgba(229, 229, 229, 0.3)'
        ])
        ->addColorPicker('form_bg_color', [
            'label' => 'Form Background Color',
            'default_value' => 'rgba(99, 102, 241, 0.3)'
        ])

    ->addTab('Layout')
        ->addRepeater('padding_settings', [
            'label' => 'Padding Settings',
            'instructions' => 'Customize padding for different screen sizes.',
            'button_label' => 'Add Padding',
        ])
            ->addSelect('screen_size', [
                'label' => 'Screen Size',
                'choices' => [
                    'xxs' => 'xxs',
                    'xs' => 'xs',
                    'mob' => 'mob',
                    'sm' => 'sm',
                    'md' => 'md',
                    'lg' => 'lg',
                    'xl' => 'xl',
                    'xxl' => 'xxl',
                    'ultrawide' => 'ultrawide',
                ],
            ])
            ->addNumber('padding_top', [
                'label' => 'Padding Top',
                'min' => 0,
                'max' => 20,
                'step' => 0.1,
                'append' => 'rem',
            ])
            ->addNumber('padding_bottom', [
                'label' => 'Padding Bottom',
                'min' => 0,
                'max' => 20,
                'step' => 0.1,
                'append' => 'rem',
            ])
        ->endRepeater();

return $contact_form;
