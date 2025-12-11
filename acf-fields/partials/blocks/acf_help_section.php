<?php
/**
 * ACF Builder group for: Help Section (custom dropdown; button navigates to selected item)
 * Location: e.g. theme/inc/acf/blocks/acf_help_section.php
 */

use StoutLogic\AcfBuilder\FieldsBuilder;

$help_section = new FieldsBuilder('help_section', [
    'label' => 'Help Section with Practice Area Dropdown',
]);

$help_section
    ->addTab('content_tab', ['label' => 'Content'])
        ->addText('heading', [
            'label'         => 'Heading',
            'instructions'  => 'Main heading for this section.',
            'default_value' => 'How can we help?',
        ])
        ->addSelect('heading_tag', [
            'label'         => 'Heading Tag',
            'instructions'  => 'Choose the semantic tag to render.',
            'choices'       => [
                'h1' => 'h1', 'h2' => 'h2', 'h3' => 'h3',
                'h4' => 'h4', 'h5' => 'h5', 'h6' => 'h6',
                'span' => 'span', 'p' => 'p',
            ],
            'default_value' => 'h2',
        ])
        ->addText('dropdown_placeholder', [
            'label'         => 'Dropdown Placeholder',
            'default_value' => 'Select a practice area',
        ])

        // NEW: where options come from (default = CPT practice_areas, sorted A–Z on the frontend)
        ->addSelect('source_mode', [
            'label'         => 'Options Source',
            'instructions'  => 'Choose what fills the dropdown.',
            'choices'       => [
                'practice_areas_cpt' => 'CPT: practice_areas (alphabetical)',
                'pages'               => 'All Pages (alphabetical)',
                'posts'               => 'All Posts (alphabetical)',
                'relationship_manual' => 'Manual (relationship)',
            ],
            'default_value' => 'practice_areas_cpt',
        ])

        // Only used when "Manual (relationship)" is selected
        ->addRelationship('dropdown_options', [
            'label'         => 'Manual Options',
            'instructions'  => 'Choose posts/pages/CPT that appear in the dropdown (keeps editor order).',
            'return_format' => 'object',
            'post_type'     => ['post','page','practice_areas'],
            'filters'       => ['search','post_type','taxonomy'],
            'min'           => 0,
        ])
        ->conditional('source_mode', '==', 'relationship_manual')

        // Optional: keep the icon toggle (button still navigates to selected item)
        ->addTrueFalse('enable_icon', [
            'label'         => 'Show Icon Inside Button',
            'ui'            => 1,
            'default_value' => 0,
        ])
        ->addSelect('dropdown_ui', [
            'label'         => 'Dropdown UI',
            'instructions'  => 'Choose the dropdown presentation.',
            'choices'       => [
                'custom_listbox' => 'Custom (Tailwind/ARIA listbox)',
                'nice_select'    => 'Nice Select (enhanced native select)',
            ],
            'default_value' => 'custom_listbox',
        ])

    ->addTab('design_tab', ['label' => 'Design'])
        ->addText('background_color', [
            'label'         => 'Background Color',
            'instructions'  => 'CSS color value (e.g. #ffffff, rgb(), hsl())',
            'default_value' => '#ffffff',
        ])
        ->addText('text_color', [
            'label'         => 'Text Color',
            'instructions'  => 'CSS color value used for text in the section.',
            'default_value' => '#0f172a',
        ])
        ->addSelect('border_radius', [
            'label'         => 'Border Radius (container)',
            'choices'       => [
                'rounded-none' => 'rounded-none',
                'rounded-sm'   => 'rounded-sm',
                'rounded'      => 'rounded',
                'rounded-md'   => 'rounded-md',
                'rounded-lg'   => 'rounded-lg',
                'rounded-xl'   => 'rounded-xl',
                'rounded-2xl'  => 'rounded-2xl',
                'rounded-3xl'  => 'rounded-3xl',
            ],
            'default_value' => 'rounded-none',
        ])
        ->addText('button_bg_color', [
            'label'         => 'Button BG Color',
            'default_value' => '#0902A4',
        ])
        ->addText('button_text_color', [
            'label'         => 'Button Text Color',
            'default_value' => '#ffffff',
        ])
        ->addText('button_border_color', [
            'label'         => 'Button Border Color',
            'default_value' => '#0902A4',
        ])
        ->addText('button_hover_bg_color', [
            'label'         => 'Button Hover/Focus BG',
            'default_value' => '#1e1b4b',
        ])
        ->addText('button_hover_text_color', [
            'label'         => 'Button Hover/Focus Text',
            'default_value' => '#ffffff',
        ])
        ->addText('button_hover_border_color', [
            'label'         => 'Button Hover/Focus Border',
            'default_value' => '#1e1b4b',
        ])

    ->addTab('layout_tab', ['label' => 'Layout'])
        ->addRepeater('padding_settings', [
            'label'         => 'Padding Settings',
            'instructions'  => 'Customize padding for different screen sizes.',
            'button_label'  => 'Add Screen Size Padding',
        ])
            ->addSelect('screen_size', [
                'label'   => 'Screen Size',
                'choices' => [
                    'xxs' => 'xxs', 'xs' => 'xs', 'mob' => 'mob',
                    'sm'  => 'sm',  'md' => 'md',  'lg' => 'lg',
                    'xl'  => 'xl',  'xxl'=> 'xxl', 'ultrawide' => 'ultrawide',
                ],
            ])
            ->addNumber('padding_top', [
                'label'        => 'Padding Top',
                'instructions' => 'Set the top padding in rem.',
                'min' => 0, 'max' => 20, 'step' => 0.1, 'append' => 'rem',
            ])
            ->addNumber('padding_bottom', [
                'label'        => 'Padding Bottom',
                'instructions' => 'Set the bottom padding in rem.',
                'min' => 0, 'max' => 20, 'step' => 0.1, 'append' => 'rem',
            ])
        ->endRepeater();

return $help_section;
