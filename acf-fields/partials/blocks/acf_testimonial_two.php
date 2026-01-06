<?php

use StoutLogic\AcfBuilder\FieldsBuilder;

$testimonials_two = new FieldsBuilder('testimonials_two', [
    'label' => 'Testimonials Two',
]);

$testimonials_two
    ->addTab('Content', ['label' => 'Content'])

    ->addMessage('slides_info', 'Slides', [
        'label' => 'Slides',
        'message' => 'Add manual slides below, or select Testimonials from the Testimonials CPT. If neither is provided, the single fields will be used as a fallback.',
    ])

    /**
     * Static image for the whole section (NOT part of slides)
     */
    ->addImage('background_image', [
        'label' => 'Static Background Image',
        'instructions' => 'This image stays fixed. Only the testimonial text slides.',
        'return_format' => 'id',
        'preview_size' => 'medium',
        'required' => 0,
    ])

    /**
     * Manual slides (TEXT ONLY)
     */
    ->addRepeater('manual_slides', [
        'label' => 'Manual Slides',
        'instructions' => 'Add slides manually. If you add slides here, these will be used first.',
        'button_label' => 'Add Slide',
        'layout' => 'block',
        'min' => 0,
    ])
        ->addTextarea('testimonial_text', [
            'label' => 'Testimonial Text',
            'instructions' => 'Enter the testimonial quote text.',
            'rows' => 4,
            'required' => 0,
        ])
        ->addText('attribution_source', [
            'label' => 'Attribution Source',
            'instructions' => 'Enter the source of the testimonial (e.g., Legal 500).',
            'required' => 0,
        ])
        ->addText('attribution_year', [
            'label' => 'Attribution Year',
            'instructions' => 'Enter the year of the testimonial.',
            'required' => 0,
        ])
    ->endRepeater()

    /**
     * Select from Testimonials CPT (TEXT ONLY comes from post)
     */
    ->addPostObject('selected_testimonials', [
        'label' => 'Select Testimonials (CPT)',
        'instructions' => 'Select testimonials to display. Leave empty to use manual slides or the single fallback fields.',
        'post_type' => ['testimonial'],
        'return_format' => 'object',
        'multiple' => 1,
        'allow_null' => 1,
        'ui' => 1,
    ])

    /**
     * Single fallback (TEXT ONLY)
     */
    ->addTextarea('testimonial_text', [
        'label' => 'Single Testimonial Text (Fallback)',
        'instructions' => 'Used only if no manual slides or selected testimonials are provided.',
        'default_value' => 'A friendly and helpful team who strive to outwit the other side, with better quality arguments and submissions.',
        'rows' => 4,
        'required' => 0,
    ])
    ->addText('attribution_source', [
        'label' => 'Single Attribution Source (Fallback)',
        'instructions' => 'Used only if no manual slides or selected testimonials are provided.',
        'default_value' => 'Legal 500',
        'required' => 0,
    ])
    ->addText('attribution_year', [
        'label' => 'Single Attribution Year (Fallback)',
        'instructions' => 'Used only if no manual slides or selected testimonials are provided.',
        'default_value' => '2022',
        'required' => 0,
    ])

    ->addTab('Design', ['label' => 'Design'])
    ->addColorPicker('background_color', [
        'label' => 'Section Background Color',
        'instructions' => 'Choose the background color for the entire section.',
        'default_value' => '#FFFFFF',
    ])
    ->addColorPicker('quote_text_color', [
        'label' => 'Quote Text Color',
        'instructions' => 'Choose the color for the testimonial quote text.',
        'default_value' => '#4338ca',
    ])
    ->addColorPicker('attribution_text_color', [
        'label' => 'Attribution Text Color',
        'instructions' => 'Choose the color for the attribution text.',
        'default_value' => '#000000',
    ])

    ->addTab('Layout', ['label' => 'Layout'])
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
    ->endRepeater()

    ->addTrueFalse('enable_carousel', [
        'label' => 'Enable Carousel Navigation',
        'instructions' => 'Toggle to show/hide the carousel navigation arrows.',
        'default_value' => 1,
    ]);

return $testimonials_two;
