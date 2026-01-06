<?php

use StoutLogic\AcfBuilder\FieldsBuilder;

$team_index = new FieldsBuilder('team_index', [
    'label' => 'Team Index',
]);

$team_index
    ->addTab('Content', ['label' => 'Content'])
    ->addText('section_heading', [
        'label' => 'Section Heading',
        'instructions' => 'Optional heading above the team grid.',
        'default_value' => 'Our Team',
    ])
    ->addSelect('heading_tag', [
        'label' => 'Heading Tag',
        'instructions' => 'Select the HTML tag for the heading.',
        'choices' => [
            'h1' => 'h1',
            'h2' => 'h2',
            'h3' => 'h3',
            'h4' => 'h4',
            'h5' => 'h5',
            'h6' => 'h6',
            'span' => 'span',
            'p' => 'p',
        ],
        'default_value' => 'h2',
    ])
    ->addWysiwyg('section_intro', [
        'label' => 'Intro Text',
        'instructions' => 'Optional intro text. Uses the WordPress editor.',
        'required' => 0,
    ])
    ->addPostObject('selected_team', [
        'label' => 'Select Team Members',
        'instructions' => 'Optional: Choose specific team members to show. Leave empty to show all team members.',
        'post_type' => ['team'],
        'return_format' => 'object',
        'multiple' => 1,
        'allow_null' => 1,
        'ui' => 1,
    ])
    ->addTrueFalse('enable_pagination', [
        'label' => 'Enable Pagination',
        'instructions' => 'Toggle pagination on/off. Pagination is 16 items per page.',
        'default_value' => 1,
    ])

    ->addTab('Design', ['label' => 'Design'])
    ->addColorPicker('background_color', [
        'label' => 'Background Color',
        'instructions' => 'Section background color.',
        'default_value' => '#FFFFFF',
    ])

    ->addTab('Layout', ['label' => 'Layout'])
    ->addRepeater('padding_settings', [
        'label' => 'Padding Settings',
        'instructions' => 'Customize padding for different screen sizes.',
        'button_label' => 'Add Screen Size Padding',
        'layout' => 'table',
        'min' => 0,
        'max' => 10,
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
            'required' => 1,
        ])
        ->addNumber('padding_top', [
            'label' => 'Padding Top',
            'instructions' => 'Set the top padding in rem.',
            'min' => 0,
            'max' => 20,
            'step' => 0.1,
            'append' => 'rem',
        ])
        ->addNumber('padding_bottom', [
            'label' => 'Padding Bottom',
            'instructions' => 'Set the bottom padding in rem.',
            'min' => 0,
            'max' => 20,
            'step' => 0.1,
            'append' => 'rem',
        ])
    ->endRepeater();

return $team_index;
