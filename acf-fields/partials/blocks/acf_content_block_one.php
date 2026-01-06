<?php

use StoutLogic\AcfBuilder\FieldsBuilder;

$content_block_one = new FieldsBuilder('content_block_one', [
    'label' => 'Content Block One',
]);

$content_block_one
    ->addTab('Content', ['label' => 'Content'])
    ->addText('section_name', [
        'label' => 'Section Name',
        'instructions' => 'Enter the section name that appears above the main heading.',
        'default_value' => 'SECTION NAME',
    ])
    ->addText('heading', [
        'label' => 'Main Heading',
        'instructions' => 'Enter the main heading text.',
        'default_value' => 'Lorem ipsum dolor sit amet is a placeholder',
    ])
    ->addSelect('heading_tag', [
        'label' => 'Heading Tag',
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
        'default_value' => 'h2',
    ])
    ->addWysiwyg('content', [
        'label' => 'Content',
        'instructions' => 'Enter the main content text. Use line breaks and formatting as needed.',
        'default_value' => 'Lorem ipsum dolor sit amet consectetur. Feugiat vitae cursus tempus nibh. Amet odio malesuada id pharetra turpis tellus purus non facilisis. Varius est quis auctor.<br><br>Lorem ipsum dolor sit amet consectetur. Feugiat vitae cursus tempus nibh. Amet odio malesuada id pharetra turpis tellus purus non facilisis. Varius est quis auctor.<br><br>Acquisition and Disposal<br>Banking and Finance<br>Commercial Landlord and Tenant<br>Development<br>Distressed Properties<br>Property Finance<br>New Homes<br>Residential Property<br>Social Housing',
        'media_upload' => 0,
        'tabs' => 'all',
        'toolbar' => 'full',
    ])
    ->addImage('image', [
        'label' => 'Image',
        'instructions' => 'Upload an image to display alongside the content.',
        'return_format' => 'id',
        'preview_size' => 'medium',
    ])

    ->addTab('Design', ['label' => 'Design'])
    ->addColorPicker('background_color', [
        'label' => 'Background Color',
        'instructions' => 'Choose the background color for this section.',
        'default_value' => '#FFFFFF',
    ])

    ->addTab('Layout', ['label' => 'Layout'])
    ->addTrueFalse('reverse_layout', [
        'label' => 'Reverse Layout',
        'instructions' => 'Toggle to switch the image and text positions (image on left, text on right).',
        'default_value' => 0,
    ])
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
            'xxs' => 'XXS',
            'xs' => 'XS',
            'mob' => 'Mobile',
            'sm' => 'SM',
            'md' => 'MD',
            'lg' => 'LG',
            'xl' => 'XL',
            'xxl' => 'XXL',
            'ultrawide' => 'Ultrawide',
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

return $content_block_one;
