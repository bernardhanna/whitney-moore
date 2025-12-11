<?php

use StoutLogic\AcfBuilder\FieldsBuilder;

$callout = new FieldsBuilder('callout', [
    'label' => 'Call out',
]);

$callout
    ->addTab('Content', ['label' => 'Content'])
    ->addText('section_heading', [
        'label' => 'Section Heading',
        'instructions' => 'Enter the section heading text (e.g., "OUR PEOPLE").',
        'default_value' => 'OUR PEOPLE',
    ])
    ->addSelect('section_heading_tag', [
        'label' => 'Section Heading Tag',
        'instructions' => 'Select the HTML tag for the section heading.',
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
    ])
    ->addText('main_heading', [
        'label' => 'Main Heading',
        'instructions' => 'Enter the main heading text.',
        'default_value' => 'An extension of your close-knit team',
    ])
    ->addSelect('main_heading_tag', [
        'label' => 'Main Heading Tag',
        'instructions' => 'Select the HTML tag for the main heading.',
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
        'default_value' => 'h1',
    ])
    ->addWysiwyg('description', [
        'label' => 'Description',
        'instructions' => 'Enter the description text.',
        'default_value' => 'We recruit and retain talented lawyers who are experts in their fields. Our lawyers are solutions driven and adept at addressing complex and challenging legal issues.',
        'media_upload' => 0,
        'tabs' => 'all',
        'toolbar' => 'full',
    ])
    ->addLink('cta_button', [
        'label' => 'Call to Action Button',
        'instructions' => 'Add the call to action button link.',
        'return_format' => 'array',
    ])
    ->addImage('left_image', [
        'label' => 'Left Image',
        'instructions' => 'Upload the left side image.',
        'return_format' => 'id',
        'preview_size' => 'medium',
    ])
    ->addImage('right_image', [
        'label' => 'Right Image',
        'instructions' => 'Upload the right side image.',
        'return_format' => 'id',
        'preview_size' => 'medium',
    ])

    ->addTab('Design', ['label' => 'Design'])
    ->addColorPicker('background_color', [
        'label' => 'Background Color',
        'instructions' => 'Select the background color for the section.',
        'default_value' => '#FFFFFF',
    ])

    ->addTab('Layout', ['label' => 'Layout'])
    ->addRepeater('padding_settings', [
        'label' => 'Padding Settings',
        'instructions' => 'Customize padding for different screen sizes.',
        'button_label' => 'Add Screen Size Padding',
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
        'instructions' => 'Set the top padding in rem.',
        'min' => 0,
        'max' => 20,
        'step' => 0.1,
        'append' => 'rem',
        'default_value' => 5,
    ])
    ->addNumber('padding_bottom', [
        'label' => 'Padding Bottom',
        'instructions' => 'Set the bottom padding in rem.',
        'min' => 0,
        'max' => 20,
        'step' => 0.1,
        'append' => 'rem',
        'default_value' => 5,
    ])
    ->endRepeater();

return $callout;
