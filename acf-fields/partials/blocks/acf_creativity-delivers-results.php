<?php

use StoutLogic\AcfBuilder\FieldsBuilder;

$creativity_delivers_results = new FieldsBuilder('creativity-delivers-results', [
    'label' => 'Creativity-Delivers-Results',
]);

$creativity_delivers_results
    ->addTab('content_tab', ['label' => 'Content'])
        ->addText('section_label', [
            'label' => 'Admin Label',
            'instructions' => 'Internal only.',
        ])
        ->addText('heading_text', [
            'label' => 'Heading',
            'default_value' => 'Heading',
        ])
        ->addSelect('heading_tag', [
            'label' => 'Heading Tag',
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
        ->addWysiwyg('subcopy', [
            'label' => 'Subcopy',
            'media_upload' => 0,
            'tabs' => 'visual',
            'delay' => 0,
        ])

    ->addTab('layout_tab', ['label' => 'Layout'])
        ->addSelect('image_radius', [
            'label' => 'Image Border Radius',
            'choices' => [
                'rounded-none' => 'rounded-none',
                'rounded'      => 'rounded',
                'rounded-md'   => 'rounded-md',
                'rounded-lg'   => 'rounded-lg',
                'rounded-xl'   => 'rounded-xl',
                'rounded-full' => 'rounded-full',
            ],
            'default_value' => 'rounded-none',
        ])
        ->addRepeater('padding_settings', [
            'label' => 'Padding Settings',
            'instructions' => 'Customize padding for different screen sizes.',
            'button_label' => 'Add Screen Size Padding',
        ])
            ->addSelect('screen_size', [
                'label' => 'Screen Size',
                'choices' => [
                    'xxs'       => 'xxs',
                    'xs'        => 'xs',
                    'mob'       => 'mob',
                    'sm'        => 'sm',
                    'md'        => 'md',
                    'lg'        => 'lg',
                    'xl'        => 'xl',
                    'xxl'       => 'xxl',
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

return $creativity_delivers_results;