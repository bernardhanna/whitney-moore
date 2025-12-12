<?php
use StoutLogic\AcfBuilder\FieldsBuilder;

$callout = new FieldsBuilder('callout', [
    'label' => 'Our Story',
]);

$callout
    ->addTab('Content', ['label' => 'Content'])
        // Section heading (small label above main heading)
        ->addText('section_heading', [
            'label' => 'Section Heading',
            'instructions' => 'Small text above the main heading (e.g., "OUR STORY").',
            'default_value' => 'OUR STORY',
        ])
        ->addSelect('section_heading_tag', [
            'label' => 'Section Heading Tag',
            'choices' => [
                'h1'=>'h1','h2'=>'h2','h3'=>'h3','h4'=>'h4','h5'=>'h5','h6'=>'h6','p'=>'p','span'=>'span'
            ],
            'default_value' => 'p',
            'ui' => 1,
        ])

        // Main heading
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

        // Body copy
        ->addWysiwyg('description', [
            'label' => 'Description',
            'media_upload' => 0,
            'tabs' => 'all',
            'toolbar' => 'full',
        ])

        // CTA link (ACF link array)
        ->addLink('cta_button', [
            'label' => 'Call to Action Button',
            'return_format' => 'array',
        ])

        // Left/Right images for desktop/tablet
        ->addImage('left_image', [
            'label' => 'Left Side Image',
            'instructions' => 'Shown on desktop/tablet (hidden on some smaller breakpoints per template).',
            'return_format' => 'id',
            'preview_size' => 'medium',
        ])
        ->addImage('right_image', [
            'label' => 'Right Side Image (SVG supported)',
            'instructions' => 'Upload a single image (SVG recommended).',
            'return_format' => 'id',
            'preview_size' => 'medium',
        ])

        // NEW: mobile-only image that sits directly below the CTA button
        ->addImage('mobile_below_button_image', [
            'label' => 'Mobile Image (below CTA, < md only)',
            'instructions' => 'Appears directly under the button on screens below md only.',
            'return_format' => 'id',
            'preview_size'  => 'medium',
        ])

    ->addTab('Design', ['label' => 'Design'])
        // Background color used by the section wrapper
        ->addColorPicker('background_color', [
            'label' => 'Section Background Color',
            'default_value' => '#FFFFFF',
        ])
        // Border radius for the mobile-only image (Tailwind classes)
        ->addSelect('mobile_below_button_image_radius', [
            'label' => 'Mobile Image Border Radius',
            'choices' => [
                'rounded-none' => 'rounded-none',
                'rounded-sm'   => 'rounded-sm',
                'rounded'      => 'rounded',
                'rounded-md'   => 'rounded-md',
                'rounded-lg'   => 'rounded-lg',
                'rounded-xl'   => 'rounded-xl',
                'rounded-2xl'  => 'rounded-2xl',
                'rounded-3xl'  => 'rounded-3xl',
                'rounded-full' => 'rounded-full',
            ],
            'default_value' => 'rounded-none',
            'instructions'  => 'Tailwind border radius for the mobile-only image.',
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
                    'xxs'=>'xxs','xs'=>'xs','mob'=>'mob','sm'=>'sm','md'=>'md','lg'=>'lg','xl'=>'xl','xxl'=>'xxl','ultrawide'=>'ultrawide',
                ],
                'ui' => 1,
            ])
            ->addNumber('padding_top', [
                'label' => 'Padding Top',
                'min' => 0,
                'max' => 20,
                'step' => 0.1,
                'append' => 'rem',
                'default_value' => 5,
            ])
            ->addNumber('padding_bottom', [
                'label' => 'Padding Bottom',
                'min' => 0,
                'max' => 20,
                'step' => 0.1,
                'append' => 'rem',
                'default_value' => 5,
            ])
        ->endRepeater();

return $callout;
