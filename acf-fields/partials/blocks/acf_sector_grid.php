<?php
use StoutLogic\AcfBuilder\FieldsBuilder;

$sectors_grid = new FieldsBuilder('sectors_grid', [
    'label' => 'Sectors Grid',
]);

$sectors_grid
    ->addTab('Content', ['placement' => 'top'])
        ->addText('section_heading', [
            'label' => 'Section Eyebrow',
            'instructions' => 'Small eyebrow above the main heading.',
            'default_value' => 'Our Sectors',
        ])
        ->addSelect('section_heading_tag', [
            'label' => 'Eyebrow Tag',
            'choices' => [
                'h1'=>'h1','h2'=>'h2','h3'=>'h3','h4'=>'h4','h5'=>'h5','h6'=>'h6','span'=>'span','p'=>'p'
            ],
            'default_value' => 'p',
            'ui' => 1,
        ])
        ->addText('main_heading', [
            'label' => 'Main Heading',
            'default_value' => 'Strong knowledge in the following sectors:',
        ])
        ->addSelect('main_heading_tag', [
            'label' => 'Main Heading Tag',
            'choices' => [
                'h1'=>'h1','h2'=>'h2','h3'=>'h3','h4'=>'h4','h5'=>'h5','h6'=>'h6','span'=>'span','p'=>'p'
            ],
            'default_value' => 'h2',
            'ui' => 1,
        ])
        ->addNumber('posts_per_page', [
            'label' => 'Posts to Show',
            'default_value' => 6,
            'min' => 1,
            'max' => 24,
            'step' => 1,
        ])
        ->addLink('override_link', [
            'label' => 'Override Link (optional)',
            'instructions' => 'If set, all cards will use this link (ACF link array).',
            'return_format' => 'array',
        ])

    ->addTab('Design', ['placement' => 'top'])
        ->addColorPicker('background_color', [
            'label' => 'Background Color',
            'default_value' => '#F4F4F4',
        ])
        ->addColorPicker('text_color', [
            'label' => 'Text Color',
            'default_value' => '#0A0A0A',
        ])
        ->addColorPicker('underline_color', [
            'label' => 'Caption Top Rule Color',
            'default_value' => '#2F5FFF',
        ])
        ->addSelect('image_radius', [
            'label' => 'Image Border Radius',
            'choices' => [
                'rounded-none'=>'rounded-none',
                'rounded'=>'rounded',
                'rounded-md'=>'rounded-md',
                'rounded-lg'=>'rounded-lg',
                'rounded-xl'=>'rounded-xl',
                'rounded-2xl'=>'rounded-2xl',
            ],
            'default_value' => 'rounded-none',
            'ui' => 1,
        ])
        ->addSelect('tile_radius', [
            'label' => 'Tile Border Radius',
            'choices' => [
                'rounded-none'=>'rounded-none',
                'rounded'=>'rounded',
                'rounded-md'=>'rounded-md',
                'rounded-lg'=>'rounded-lg',
                'rounded-xl'=>'rounded-xl',
                'rounded-2xl'=>'rounded-2xl',
            ],
            'default_value' => 'rounded-none',
            'ui' => 1,
        ])

    ->addTab('Layout', ['placement' => 'top'])
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
                'instructions' => 'Set the top padding in rem.',
                'min' => 0, 'max' => 20, 'step' => 0.1, 'append' => 'rem',
            ])
            ->addNumber('padding_bottom', [
                'label' => 'Padding Bottom',
                'instructions' => 'Set the bottom padding in rem.',
                'min' => 0, 'max' => 20, 'step' => 0.1, 'append' => 'rem',
            ])
        ->endRepeater();

return $sectors_grid;
