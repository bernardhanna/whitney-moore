<?php

use StoutLogic\AcfBuilder\FieldsBuilder;

$related_content = new FieldsBuilder('related_content', [
    'label' => 'Related Content',
]);

$related_content
    ->addTab('Content', ['label' => 'Content'])
        ->addText('heading', [
            'label'         => 'Heading Text',
            'instructions'  => 'Set the text for the main heading.',
            'default_value' => 'Related articles and events',
        ])
        ->addSelect('heading_tag', [
            'label'         => 'Heading Tag',
            'instructions'  => 'Choose the HTML tag for the heading.',
            'choices'       => [
                'h1' => 'H1','h2' => 'H2','h3' => 'H3','h4' => 'H4','h5' => 'H5','h6' => 'H6','p' => 'Paragraph','span' => 'Span',
            ],
            'default_value' => 'h4',
        ])

        // ---------------------- Filter selection ----------------------
        ->addSelect('filter_type', [
            'label'         => 'Filter By',
            'ui'            => 1,
            'choices'       => [
                'category'        => 'Category',
                'author'          => 'Author',
                'category_author' => 'Category + Author',
                'tag'             => 'Tag',
                'none'            => 'No filter (latest)',
            ],
            'default_value' => 'category',
        ])

        // Categories (multi)
        ->addTaxonomy('categories', [
            'label'            => 'Select Categories',
            'taxonomy'         => 'category',
            'field_type'       => 'multi_select',
            'return_format'    => 'id',
            'allow_null'       => 0,
            'add_term'         => 0,
            'conditional_logic'=> [
                [
                    ['field' => 'filter_type', 'operator' => '==', 'value' => 'category'],
                ],
                [
                    ['field' => 'filter_type', 'operator' => '==', 'value' => 'category_author'],
                ],
            ],
        ])

        // Authors (multi)
        ->addUser('authors', [
            'label'            => 'Select Authors',
            'instructions'     => 'Pick one or more authors.',
            'multiple'         => 1,
            'return_format'    => 'id',
            'conditional_logic'=> [
                [
                    ['field' => 'filter_type', 'operator' => '==', 'value' => 'author'],
                ],
                [
                    ['field' => 'filter_type', 'operator' => '==', 'value' => 'category_author'],
                ],
            ],
        ])

        // Tags (multi)
        ->addTaxonomy('tags', [
            'label'            => 'Select Tags',
            'taxonomy'         => 'post_tag',
            'field_type'       => 'multi_select',
            'return_format'    => 'id',
            'allow_null'       => 0,
            'add_term'         => 0,
            'conditional_logic'=> [
                [
                    ['field' => 'filter_type', 'operator' => '==', 'value' => 'tag'],
                ],
            ],
        ])

        // Basic query controls
        ->addSelect('post_type', [
            'label'         => 'Post Type',
            'ui'            => 1,
            'choices'       => [
                'post' => 'Posts',
                // Add more if needed (e.g. 'events' => 'Events')
            ],
            'default_value' => 'post',
        ])
        ->addNumber('posts_per_page', [
            'label'         => 'How many?',
            'default_value' => 3,
            'min'           => 1,
            'max'           => 12,
            'step'          => 1,
        ])
        ->addSelect('orderby', [
            'label'         => 'Order By',
            'choices'       => [
                'date'     => 'Date',
                'title'    => 'Title',
                'rand'     => 'Random',
                'modified' => 'Last Modified',
            ],
            'default_value' => 'date',
        ])
        ->addSelect('order', [
            'label'         => 'Order',
            'choices'       => ['DESC' => 'DESC', 'ASC' => 'ASC'],
            'default_value' => 'DESC',
        ])

    ->addTab('Design', ['label' => 'Design'])
        ->addColorPicker('background_color', [
            'label'         => 'Background Color',
            'instructions'  => 'Set the background color for the section.',
            'default_value' => '#f5f5f5',
        ])

    ->addTab('Layout', ['label' => 'Layout'])
        ->addRepeater('padding_settings', [
            'label'         => 'Padding Settings',
            'instructions'  => 'Customize padding for different screen sizes.',
            'button_label'  => 'Add Screen Size Padding',
        ])
            ->addSelect('screen_size', [
                'label'   => 'Screen Size',
                'choices' => [
                    'xxs'=>'xxs','xs'=>'xs','mob'=>'mob','sm'=>'sm','md'=>'md','lg'=>'lg','xl'=>'xl','xxl'=>'xxl','ultrawide'=>'ultrawide',
                ],
            ])
            ->addNumber('padding_top', [
                'label'         => 'Padding Top',
                'instructions'  => 'Top padding in rem.',
                'min'           => 0, 'max' => 20, 'step' => 0.1, 'append' => 'rem',
                'default_value' => 5,
            ])
            ->addNumber('padding_bottom', [
                'label'         => 'Padding Bottom',
                'instructions'  => 'Bottom padding in rem.',
                'min'           => 0, 'max' => 20, 'step' => 0.1, 'append' => 'rem',
                'default_value' => 6,
            ])
        ->endRepeater();

return $related_content;
