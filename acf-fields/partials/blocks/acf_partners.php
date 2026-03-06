<?php

use StoutLogic\AcfBuilder\FieldsBuilder;

$partners = new FieldsBuilder('partners', [
    'label' => 'Partners Section',
]);

$partners
    ->addTab('Content', ['label' => 'Content'])
    ->addText('heading_text', [
        'label' => 'Heading Text',
        'instructions' => 'Enter the main heading text.',
        'default_value' => 'They trust us',
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
    ])
    ->addText('subheading', [
        'label' => 'Subheading',
        'instructions' => 'Enter the subheading text that appears above the main heading.',
        'default_value' => 'Our team advises leading companies in the real estate field',
    ])
    ->addPostObject('selected_partners', [
        'label' => 'Select Partners',
        'instructions' => 'Choose which partners to display. Leave empty to show all partners. You can reorder by dragging.',
        'post_type' => ['partners'],
        'return_format' => 'object',
        'multiple' => 1,
        'allow_null' => 1,
        'ui' => 1,
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

return $partners;
