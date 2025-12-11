<?php
use StoutLogic\AcfBuilder\FieldsBuilder;

$our_story = new FieldsBuilder('our_story', [
    'label' => 'Our Story',
]);

$our_story
    ->addTab('Content', ['label' => 'Content'])
        ->addText('section_label', [
            'label' => 'Section Label',
            'instructions' => 'Small text above the main heading (e.g., "OUR STORY").',
            'default_value' => 'OUR STORY',
        ])
        ->addText('main_heading', [
            'label' => 'Main Heading',
            'default_value' => 'A full-service law firm',
        ])
        ->addSelect('main_heading_tag', [
            'label' => 'Main Heading Tag',
            'choices' => [
                'h1'=>'h1','h2'=>'h2','h3'=>'h3','h4'=>'h4','h5'=>'h5','h6'=>'h6','p'=>'p','span'=>'span'
            ],
            'default_value' => 'h2',
            'ui' => 1,
        ])
        ->addWysiwyg('description', [
            'label' => 'Description',
            'media_upload' => 0,
            'tabs' => 'all',
            'toolbar' => 'full',
        ])
        ->addLink('cta_button', [
            'label' => 'Call to Action Button',
            'return_format' => 'array',
        ])
        ->addImage('right_image', [
            'label' => 'Right Side Image (SVG supported)',
            'instructions' => 'Upload a single image (SVG recommended).',
            'return_format' => 'id',
            'preview_size' => 'medium',
        ])

    ->addTab('Design', ['label' => 'Design'])
        ->addColorPicker('section_background_color', [
            'label' => 'Section Background Color',
            'default_value' => '#FFFFFF',
        ])
        ->addColorPicker('content_background_color', [
            'label' => 'Content Background Color',
            'default_value' => 'rgba(67,56,202,0.9)',
        ])
        ->addColorPicker('text_color', [
            'label' => 'Text Color',
            'default_value' => '#FFFFFF',
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
                    'xxs'=>'xxs','xs'=>'xs','mob'=>'mob','sm'=>'sm','md'=>'md','lg'=>'lg','xl'=>'xl','xxl'=>'xxl','ultrawide'=>'ultrawide',
                ],
            ])
            ->addNumber('padding_top', [
                'label' => 'Padding Top',
                'min' => 0, 'max' => 20, 'step' => 0.1, 'append' => 'rem', 'default_value' => 5,
            ])
            ->addNumber('padding_bottom', [
                'label' => 'Padding Bottom',
                'min' => 0, 'max' => 20, 'step' => 0.1, 'append' => 'rem', 'default_value' => 5,
            ])
        ->endRepeater();

return $our_story;
