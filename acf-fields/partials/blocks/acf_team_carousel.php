<?php

use StoutLogic\AcfBuilder\FieldsBuilder;

$team_carousel = new FieldsBuilder('team_carousel', [
    'label' => 'Team Carousel',
]);

$team_carousel
    ->addTab('Content', ['label' => 'Content'])
        ->addText('heading', [
            'label' => 'Heading Text',
            'default_value' => 'More people in real estate',
            'required' => 0,
        ])
        ->addSelect('heading_tag', [
            'label' => 'Heading Tag',
            'choices' => [
                'h1'=>'H1','h2'=>'H2','h3'=>'H3','h4'=>'H4','h5'=>'H5','h6'=>'H6','p'=>'Paragraph','span'=>'Span',
            ],
            'default_value' => 'h4',
            'required' => 0,
        ])

        // Source (default taxonomy; no terms => ALL)
        ->addSelect('source_mode', [
            'label' => 'Source Mode',
            'choices' => [
                'taxonomy' => 'From Team Taxonomies (Practice Area / Sector)',
                'manual'   => 'Manual Images',
            ],
            'default_value' => 'taxonomy',
            'ui' => 1,
            'required' => 1,
        ])

        // Manual images
        ->addRepeater('images', [
            'label' => 'People Images (Manual)',
            'button_label' => 'Add Image',
            'min' => 1, 'max' => 12, 'layout' => 'block',
            'conditional_logic' => [[['field'=>'source_mode','operator'=>'==','value'=>'manual']]],
        ])
            ->addImage('image', [
                'label' => 'Image',
                'return_format' => 'id',
                'preview_size'  => 'medium',
                'required'      => 1,
            ])
        ->endRepeater()

        // Taxonomy mode (defaults)
        ->addSelect('taxonomy_type', [
            'label' => 'Taxonomy Type',
            'choices' => [
                'team_practice_area' => 'Practice Area',
                'team_sector'        => 'Sector',
            ],
            'default_value' => 'team_practice_area',
            'conditional_logic' => [[['field'=>'source_mode','operator'=>'==','value'=>'taxonomy']]],
        ])
        ->addTaxonomy('practice_area_terms', [
            'label' => 'Practice Area Terms',
            'taxonomy' => 'team_practice_area',
            'field_type' => 'multi_select',
            'return_format' => 'id',
            'add_term' => 0, 'save_terms' => 0, 'load_terms' => 0,
            'conditional_logic' => [[
                ['field'=>'source_mode','operator'=>'==','value'=>'taxonomy'],
                ['field'=>'taxonomy_type','operator'=>'==','value'=>'team_practice_area'],
            ]],
        ])
        ->addTaxonomy('sector_terms', [
            'label' => 'Sector Terms',
            'taxonomy' => 'team_sector',
            'field_type' => 'multi_select',
            'return_format' => 'id',
            'add_term' => 0, 'save_terms' => 0, 'load_terms' => 0,
            'conditional_logic' => [[
                ['field'=>'source_mode','operator'=>'==','value'=>'taxonomy'],
                ['field'=>'taxonomy_type','operator'=>'==','value'=>'team_sector'],
            ]],
        ])
        ->addNumber('posts_per_page', [
            'label' => 'How many team members',
            'instructions' => 'Set 0 for ALL (default).',
            'default_value' => 0,
            'min' => 0, 'max' => 100, 'step' => 1,
            'conditional_logic' => [[['field'=>'source_mode','operator'=>'==','value'=>'taxonomy']]],
        ])
        ->addSelect('order_by', [
            'label' => 'Order By',
            'choices' => ['menu_order'=>'Menu Order','date'=>'Date','title'=>'Title'],
            'default_value' => 'menu_order',
            'conditional_logic' => [[['field'=>'source_mode','operator'=>'==','value'=>'taxonomy']]],
        ])
        ->addSelect('order', [
            'label' => 'Order',
            'choices' => ['ASC'=>'ASC','DESC'=>'DESC'],
            'default_value' => 'ASC',
            'conditional_logic' => [[['field'=>'source_mode','operator'=>'==','value'=>'taxonomy']]],
        ])

        // Visibility
        ->addTrueFalse('show_name', ['label'=>'Show Name','ui'=>1,'default_value'=>1])
        ->addTrueFalse('show_job_title', ['label'=>'Show Job Title','ui'=>1,'default_value'=>1])

    ->addTab('Slider', ['label' => 'Slider'])
        ->addTrueFalse('enable_slider', ['label'=>'Enable Slider','ui'=>1,'default_value'=>1])
        ->addTrueFalse('arrows', ['label'=>'Show Arrows','ui'=>1,'default_value'=>1,'conditional_logic'=>[[['field'=>'enable_slider','operator'=>'==','value'=>1]]]])
        ->addTrueFalse('dots', ['label'=>'Show Dots','ui'=>1,'default_value'=>0,'conditional_logic'=>[[['field'=>'enable_slider','operator'=>'==','value'=>1]]]])
        ->addTrueFalse('autoplay', ['label'=>'Autoplay','ui'=>1,'default_value'=>0,'conditional_logic'=>[[['field'=>'enable_slider','operator'=>'==','value'=>1]]]])
        ->addNumber('autoplay_speed', [
            'label'=>'Autoplay Speed (ms)','default_value'=>5000,'min'=>1000,'step'=>500,
            'conditional_logic'=>[[['field'=>'enable_slider','operator'=>'==','value'=>1]]],
        ])
        ->addNumber('slides_xl', ['label'=>'Slides on ≥1280px','default_value'=>4,'min'=>1,'max'=>8,'conditional_logic'=>[[['field'=>'enable_slider','operator'=>'==','value'=>1]]]])
        ->addNumber('slides_lg', ['label'=>'Slides on ≥1024px','default_value'=>3,'min'=>1,'max'=>8,'conditional_logic'=>[[['field'=>'enable_slider','operator'=>'==','value'=>1]]]])
        ->addNumber('slides_md', ['label'=>'Slides on ≥640px','default_value'=>2,'min'=>1,'max'=>8,'conditional_logic'=>[[['field'=>'enable_slider','operator'=>'==','value'=>1]]]])
        ->addNumber('slides_sm', ['label'=>'Slides on <640px','default_value'=>1,'min'=>1,'max'=>8,'conditional_logic'=>[[['field'=>'enable_slider','operator'=>'==','value'=>1]]]])

    ->addTab('Design', ['label' => 'Design'])
        ->addColorPicker('background_color', [
            'label' => 'Background Color',
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
                'required' => 1,
            ])
            ->addNumber('padding_top', [
                'label' => 'Padding Top',
                'min' => 0, 'max' => 20, 'step' => 0.1, 'append' => 'rem',
                'default_value' => 3.5,
            ])
            ->addNumber('padding_bottom', [
                'label' => 'Padding Bottom',
                'min' => 0, 'max' => 20, 'step' => 0.1, 'append' => 'rem',
                'default_value' => 6,
            ])
        ->endRepeater();

return $team_carousel;
