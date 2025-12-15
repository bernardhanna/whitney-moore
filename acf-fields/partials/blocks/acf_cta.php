<?php
use StoutLogic\AcfBuilder\FieldsBuilder;

$cta = new FieldsBuilder('cta', [
    'label' => 'CTA Section',
]);

$cta
    // -------------------- Content --------------------
    ->addTab('Content', ['label' => 'Content', 'placement' => 'top'])
        ->addText('heading', [
            'label' => 'Heading Text',
            'instructions' => 'Enter the main heading text for the CTA section.',
            'default_value' => 'What\'s new in the legal space',
            'required' => 1,
        ])
        ->addSelect('heading_tag', [
            'label' => 'Heading Tag',
            'instructions' => 'Select the HTML tag for the heading.',
            'choices' => [
                'h1' => 'H1', 'h2' => 'H2', 'h3' => 'H3', 'h4' => 'H4', 'h5' => 'H5', 'h6' => 'H6',
                'p'  => 'Paragraph', 'span' => 'Span',
            ],
            'default_value' => 'h2',
            'required' => 1,
        ])
        ->addLink('primary_button', [
            'label' => 'Primary Button',
            'instructions' => 'Configure the primary call-to-action button (ACF Link array).',
            'return_format' => 'array',
        ])
        ->addLink('secondary_button', [
            'label' => 'Secondary Button',
            'instructions' => 'Configure the secondary call-to-action button (ACF Link array).',
            'return_format' => 'array',
        ])

    // -------------------- Design --------------------
    ->addTab('Design', ['label' => 'Design', 'placement' => 'top'])
        ->addColorPicker('background_color', [
            'label' => 'Background Color',
            'instructions' => 'Background color of the CTA section.',
            'default_value' => '#FFFFFF',
        ])
        ->addColorPicker('text_color', [
            'label' => 'Text Color',
            'instructions' => 'Base text color for the section.',
            'default_value' => '#111111',
        ])
        ->addColorPicker('underline_color', [
            'label' => 'Underline Color',
            'instructions' => 'Optional underline color for text decoration.',
        ])
        ->addColorPicker('border_color', [
            'label' => 'Default Border Color',
            'instructions' => 'Used when a button border color is not specified.',
        ])

        // Primary button colors
        ->addGroup('primary_button_colors', ['label' => 'Primary Button Colors'])
            ->addColorPicker('primary_button_bg_color', ['label' => 'Background', 'default_value' => '#111827'])
            ->addColorPicker('primary_button_text_color', ['label' => 'Text', 'default_value' => '#FFFFFF'])
            ->addColorPicker('primary_button_border_color', ['label' => 'Border', 'default_value' => '#111827'])
            ->addColorPicker('primary_button_hover_bg_color', ['label' => 'Hover/Focus Background', 'default_value' => '#1f2937'])
            ->addColorPicker('primary_button_hover_text_color', ['label' => 'Hover/Focus Text', 'default_value' => '#FFFFFF'])
            ->addColorPicker('primary_button_hover_border_color', ['label' => 'Hover/Focus Border', 'default_value' => '#1f2937'])
        ->endGroup()

        // Secondary button colors
        ->addGroup('secondary_button_colors', ['label' => 'Secondary Button Colors'])
            ->addColorPicker('secondary_button_bg_color', ['label' => 'Background', 'default_value' => '#FFFFFF'])
            ->addColorPicker('secondary_button_text_color', ['label' => 'Text', 'default_value' => '#111827'])
            ->addColorPicker('secondary_button_border_color', ['label' => 'Border', 'default_value' => '#111827'])
            ->addColorPicker('secondary_button_hover_bg_color', ['label' => 'Hover/Focus Background', 'default_value' => '#111827'])
            ->addColorPicker('secondary_button_hover_text_color', ['label' => 'Hover/Focus Text', 'default_value' => '#FFFFFF'])
            ->addColorPicker('secondary_button_hover_border_color', ['label' => 'Hover/Focus Border', 'default_value' => '#111827'])
        ->endGroup()

    // -------------------- Layout --------------------
    ->addTab('Layout', ['label' => 'Layout', 'placement' => 'top'])
        // Section-level visibility
        ->addSelect('visibility_mode', [
            'label' => 'Section Visibility',
            'instructions' => 'Hide the entire section below or above a breakpoint.',
            'choices' => [
                'none'       => 'Show on all screens',
                'hide_below' => 'Hide below breakpoint',
                'hide_above' => 'Hide above breakpoint',
            ],
            'default_value' => 'none',
            'wrapper' => ['width' => 50],
        ])
        ->addSelect('visibility_breakpoint', [
            'label' => 'Visibility Breakpoint',
            'instructions' => 'Breakpoint used for the section-visibility rule.',
            'choices' => [
                'xxs' => 'xxs','xs' => 'xs','mob' => 'mob','sm' => 'sm','md' => 'md',
                'lg' => 'lg','xl' => 'xl','xxl' => 'xxl','ultrawide' => 'ultrawide',
            ],
            'default_value' => 'md',
            'conditional_logic' => [[['field' => 'visibility_mode','operator' => '!=','value' => 'none']]],
            'wrapper' => ['width' => 50],
        ])

        // Element-level visibility: Heading
        ->addSelect('heading_visibility_mode', [
            'label' => 'Heading Visibility',
            'instructions' => 'Show/Hide heading based on a breakpoint.',
            'choices' => [
                'none'       => 'Show on all screens',
                'hide_below' => 'Hide below breakpoint',
                'hide_above' => 'Hide above breakpoint',
            ],
            'default_value' => 'none',
            'wrapper' => ['width' => 50],
        ])
        ->addSelect('heading_visibility_breakpoint', [
            'label' => 'Heading Breakpoint',
            'choices' => [
                'xxs' => 'xxs','xs' => 'xs','mob' => 'mob','sm' => 'sm','md' => 'md',
                'lg' => 'lg','xl' => 'xl','xxl' => 'xxl','ultrawide' => 'ultrawide',
            ],
            'default_value' => 'md',
            'conditional_logic' => [[['field' => 'heading_visibility_mode','operator' => '!=','value' => 'none']]],
            'wrapper' => ['width' => 50],
        ])

        // Element-level visibility: Primary Button
        ->addSelect('primary_button_visibility_mode', [
            'label' => 'Primary Button Visibility',
            'choices' => [
                'none'       => 'Show on all screens',
                'hide_below' => 'Hide below breakpoint',
                'hide_above' => 'Hide above breakpoint',
            ],
            'default_value' => 'none',
            'wrapper' => ['width' => 50],
        ])
        ->addSelect('primary_button_visibility_breakpoint', [
            'label' => 'Primary Button Breakpoint',
            'choices' => [
                'xxs' => 'xxs','xs' => 'xs','mob' => 'mob','sm' => 'sm','md' => 'md',
                'lg' => 'lg','xl' => 'xl','xxl' => 'xxl','ultrawide' => 'ultrawide',
            ],
            'default_value' => 'md',
            'conditional_logic' => [[['field' => 'primary_button_visibility_mode','operator' => '!=','value' => 'none']]],
            'wrapper' => ['width' => 50],
        ])

        // Element-level visibility: Secondary Button
        ->addSelect('secondary_button_visibility_mode', [
            'label' => 'Secondary Button Visibility',
            'choices' => [
                'none'       => 'Show on all screens',
                'hide_below' => 'Hide below breakpoint',
                'hide_above' => 'Hide above breakpoint',
            ],
            'default_value' => 'none',
            'wrapper' => ['width' => 50],
        ])
        ->addSelect('secondary_button_visibility_breakpoint', [
            'label' => 'Secondary Button Breakpoint',
            'choices' => [
                'xxs' => 'xxs','xs' => 'xs','mob' => 'mob','sm' => 'sm','md' => 'md',
                'lg' => 'lg','xl' => 'xl','xxl' => 'xxl','ultrawide' => 'ultrawide',
            ],
            'default_value' => 'md',
            'conditional_logic' => [[['field' => 'secondary_button_visibility_mode','operator' => '!=','value' => 'none']]],
            'wrapper' => ['width' => 50],
        ])

        // Padding repeater (applied on inner wrapper)
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
                    'xxs' => 'xxs','xs' => 'xs','mob' => 'mob','sm' => 'sm','md' => 'md',
                    'lg' => 'lg','xl' => 'xl','xxl' => 'xxl','ultrawide' => 'ultrawide',
                ],
                'required' => 1,
            ])
            ->addNumber('padding_top', [
                'label' => 'Padding Top',
                'instructions' => 'Top padding in rem.',
                'min' => 0, 'max' => 20, 'step' => 0.1, 'append' => 'rem',
                'default_value' => 5,
            ])
            ->addNumber('padding_bottom', [
                'label' => 'Padding Bottom',
                'instructions' => 'Bottom padding in rem.',
                'min' => 0, 'max' => 20, 'step' => 0.1, 'append' => 'rem',
                'default_value' => 5,
            ])
        ->endRepeater();

return $cta;
