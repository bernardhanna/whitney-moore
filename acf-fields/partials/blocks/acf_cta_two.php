<?php

use StoutLogic\AcfBuilder\FieldsBuilder;

$cta_two = new FieldsBuilder('cta_two', [
    'label' => 'CTA Two',
]);

$cta_two
    ->addTab('Content', ['label' => 'Content'])
    ->addText('heading', [
        'label' => 'Heading Text',
        'instructions' => 'Main heading for the CTA section.',
        'default_value' => 'Looking for expert advice in real estate?',
        'required' => 1,
    ])
    ->addSelect('heading_tag', [
        'label' => 'Heading Tag',
        'instructions' => 'Select the HTML tag for the heading.',
        'choices' => [
            'h1' => 'H1',
            'h2' => 'H2',
            'h3' => 'H3',
            'h4' => 'H4',
            'h5' => 'H5',
            'h6' => 'H6',
            'p'  => 'Paragraph',
            'span'=> 'Span',
        ],
        'default_value' => 'h2',
        'required' => 1,
    ])
    ->addImage('profile_image', [
        'label' => 'Profile Image',
        'instructions' => 'Circular profile image on the left. If empty, a default image will be used.',
        'return_format' => 'id',
        'preview_size' => 'medium',
        'library' => 'all',
    ])
    ->addLink('button', [
        'label' => 'CTA Button',
        'instructions' => 'If empty, defaults to “Make an Inquiry” linking to /contact-us/ (handled in template).',
        'return_format' => 'array',
        'required' => 0,
    ])

    ->addTab('Design', ['label' => 'Design'])
    ->addColorPicker('background_color', [
        'label' => 'Background Color',
        'instructions' => 'Background color for the CTA section.',
        'default_value' => '#0902A4',
    ])
    ->addImage('background_image', [
        'label' => 'Background Overlay Image',
        'instructions' => 'Decorative image on the right (optional).',
        'return_format' => 'id',
        'preview_size' => 'medium',
        'library' => 'all',
    ])

    ->addTab('Layout', ['label' => 'Layout'])
    ->addRepeater('padding_settings', [
        'label' => 'Padding Settings',
        'instructions' => 'Customize padding for different screen sizes.',
        'button_label' => 'Add Screen Size Padding',
        'layout' => 'table',
    ])
        ->addSelect('screen_size', [
            'label' => 'Screen Size',
            'instructions' => 'Select the screen size for this padding setting.',
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
            'required' => 1,
        ])
        ->addNumber('padding_top', [
            'label' => 'Padding Top',
            'instructions' => 'Top padding in rem.',
            'min' => 0,
            'max' => 20,
            'step' => 0.1,
            'append' => 'rem',
            'default_value' => 2.5,
        ])
        ->addNumber('padding_bottom', [
            'label' => 'Padding Bottom',
            'instructions' => 'Bottom padding in rem.',
            'min' => 0,
            'max' => 20,
            'step' => 0.1,
            'append' => 'rem',
            'default_value' => 2.5,
        ])
    ->endRepeater();

return $cta_two;
