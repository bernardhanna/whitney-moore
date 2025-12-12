<?php

use StoutLogic\AcfBuilder\FieldsBuilder;

$cta = new FieldsBuilder('cta', [
    'label' => 'CTA Section',
]);

$cta
    ->addTab('Content', [
        'label' => 'Content',
        'placement' => 'top',
    ])
    ->addText('heading', [
        'label' => 'Heading Text',
        'instructions' => 'Enter the main heading text for the CTA section.',
        'default_value' => 'What\'s new in the legal space',
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
            'p' => 'Paragraph',
            'span' => 'Span',
        ],
        'default_value' => 'h2',
        'required' => 1,
    ])
    ->addLink('primary_button', [
        'label' => 'Primary Button',
        'instructions' => 'Configure the primary call-to-action button.',
        'return_format' => 'array',
        'required' => 0,
    ])
    ->addLink('secondary_button', [
        'label' => 'Secondary Button',
        'instructions' => 'Configure the secondary call-to-action button.',
        'return_format' => 'array',
        'required' => 0,
    ])

    ->addTab('Design', [
        'label' => 'Design',
        'placement' => 'top',
    ])
    ->addColorPicker('background_color', [
        'label' => 'Background Color',
        'instructions' => 'Set the background color for the CTA section.',
        'default_value' => '#FFFFFF',
    ])

    ->addTab('Layout', [
        'label' => 'Layout',
        'placement' => 'top',
    ])
    ->addSelect('visibility_mode', [
        'label' => 'Section Visibility',
        'instructions' => 'Choose whether to hide this section below or above a breakpoint.',
        'choices' => [
            'none'       => 'Show on all screens',
            'hide_below' => 'Hide below breakpoint',
            'hide_above' => 'Hide above breakpoint',
        ],
        'default_value' => 'none',
        'wrapper' => ['width' => 50],
    ])
    ->addSelect('visibility_breakpoint', [
        'label' => 'Visibility Breakpoint',
        'instructions' => 'Select the breakpoint used for the visibility rule.',
        'choices' => [
            'xxs'       => 'xxs',
            'xs'        => 'xs',
            'mob'       => 'mob',
            'sm'        => 'sm',
            'md'        => 'md',
            'lg'        => 'lg',
            'xl'        => 'xl',
            'xxl'       => 'xxl',
            'ultrawide' => 'ultrawide',
        ],
        'default_value' => 'md',
        'conditional_logic' => [
            [
                [
                    'field'    => 'visibility_mode',
                    'operator' => '!=',
                    'value'    => 'none',
                ],
            ],
        ],
        'wrapper' => ['width' => 50],
    ])
    ->addRepeater('padding_settings', [
        'label' => 'Padding Settings',
        'instructions' => 'Customize padding for different screen sizes.',
        'button_label' => 'Add Screen Size Padding',
        'min' => 0,
        'max' => 10,
        'layout' => 'table',
    ])
        ->addSelect('screen_size', [
            'label' => 'Screen Size',
            'instructions' => 'Select the screen size for this padding setting.',
            'choices' => [
                'xxs' => 'XXS',
                'xs' => 'XS',
                'mob' => 'Mobile',
                'sm' => 'Small',
                'md' => 'Medium',
                'lg' => 'Large',
                'xl' => 'Extra Large',
                'xxl' => 'XXL',
                'ultrawide' => 'Ultrawide',
            ],
            'required' => 1,
        ])
        ->addNumber('padding_top', [
            'label' => 'Padding Top',
            'instructions' => 'Set the top padding in rem units.',
            'min' => 0,
            'max' => 20,
            'step' => 0.1,
            'append' => 'rem',
            'default_value' => 5,
        ])
        ->addNumber('padding_bottom', [
            'label' => 'Padding Bottom',
            'instructions' => 'Set the bottom padding in rem units.',
            'min' => 0,
            'max' => 20,
            'step' => 0.1,
            'append' => 'rem',
            'default_value' => 5,
        ])
    ->endRepeater();

return $cta;
