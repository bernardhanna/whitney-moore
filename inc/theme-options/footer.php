<?php
// File: inc/acf/options/footer.php
use StoutLogic\AcfBuilder\FieldsBuilder;

$footer = new FieldsBuilder('footer', [
    'title'     => 'Footer Settings',
    'menu_slug' => 'theme-footer-settings',
    'post_id'   => 'option',
]);

$footer
    // Column headings (1–4)
    ->addText('footer_col1_heading', [
        'label'         => 'Column 1 Heading',
        'default_value' => 'About us',
    ])
    ->addText('footer_col2_heading', [
        'label'         => 'Column 2 Heading',
        'default_value' => 'Sectors',
    ])
    ->addText('footer_col3_heading', [
        'label'         => 'Column 3 Heading',
        'default_value' => 'Practice Areas',
    ])
    ->addText('footer_col4_heading', [
        'label'         => 'Column 4 Heading',
        'default_value' => 'Knowledge & Insights',
    ])

    // Get in touch
    ->addText('phone_number', [
        'label'         => 'Phone Number',
        'default_value' => '+353 1 611 0000',
    ])
    ->addText('email_address', [
        'label'       => 'Email Address',
        'placeholder' => 'hello@example.com',
    ])
    ->addTextarea('address', [
        'label'        => 'Address',
        'new_lines'    => 'br',
        'maxlength'    => 500,
        'default_value'=> 'Whitney Moore LLP, 2 Shelbourne Buildings, Crampton Avenue, Shelbourne Road, Ballsbridge, Dublin 4, D04 W3V6, Ireland.',
    ])

    // Socials
    ->addRepeater('social_icons', [
        'label'        => 'Social Icons',
        'button_label' => 'Add Social',
        'layout'       => 'row',
        'min'          => 0,
        'max'          => 10,
        'collapsed'    => 'social_label',
    ])
        ->addText('social_label', ['label' => 'Label (e.g. LinkedIn)'])
        ->addLink('social_link', [
            'label'         => 'Profile (Link Array)',
            'return_format' => 'array',
            'required'      => 1,
        ])
        ->addImage('social_icon', [
            'label'         => 'Icon (SVG/PNG)',
            'return_format' => 'id',
            'preview_size'  => 'thumbnail',
            'required'      => 1,
        ])
    ->endRepeater()

    // Partner / accreditation logos
    ->addRepeater('partner_logos', [
        'label'        => 'Partner/Association Logos',
        'button_label' => 'Add Logo',
        'layout'       => 'table',
        'min'          => 0,
        'max'          => 12,
        'collapsed'    => 'logo_image',
    ])
        ->addImage('logo_image', [
            'label'         => 'Logo Image',
            'return_format' => 'id',
            'preview_size'  => 'thumbnail',
        ])
        ->addLink('logo_link', [
            'label'         => 'Logo Link (optional)',
            'return_format' => 'array',
        ])
        ->addText('logo_alt', ['label' => 'Logo Alt (fallback)'])
    ->endRepeater()

    // Attribution (bottom right)
    ->addText('attribution_text', [
        'label'         => 'Attribution Text',
        'default_value' => 'Designed and Developed by',
    ])
    ->addLink('attribution_link', [
        'label'         => 'Attribution Link',
        'return_format' => 'array',
    ])

    // DESIGN (only background color)
    ->addColorPicker('footer_bg_color', [
        'label'         => 'Footer Background Color',
        'default_value' => '#0902A4', // primary
    ])

    // Layout (Padding repeater — no tabs)
    ->addRepeater('padding_settings', [
        'label'        => 'Padding Settings',
        'instructions' => 'Customize padding for different screen sizes.',
        'button_label' => 'Add Screen Size Padding',
        'layout'       => 'table',
        'collapsed'    => 'screen_size',
    ])
        ->addSelect('screen_size', [
            'label'   => 'Screen Size',
            'choices' => [
                'xxs' => 'xxs','xs' => 'xs','mob' => 'mob','sm' => 'sm','md' => 'md',
                'lg' => 'lg','xl' => 'xl','xxl' => 'xxl','ultrawide' => 'ultrawide',
            ],
        ])
        ->addNumber('padding_top', [
            'label' => 'Padding Top', 'min' => 0, 'max' => 20, 'step' => 0.1, 'append' => 'rem', 'default_value' => 3,
        ])
        ->addNumber('padding_bottom', [
            'label' => 'Padding Bottom', 'min' => 0, 'max' => 20, 'step' => 0.1, 'append' => 'rem', 'default_value' => 3,
        ])
    ->endRepeater();

return $footer;
