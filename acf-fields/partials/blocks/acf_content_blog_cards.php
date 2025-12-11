<?php
use StoutLogic\AcfBuilder\FieldsBuilder;

$blog_cards = new FieldsBuilder('blog_cards', [
    'label' => 'Blog Cards',
]);

$blog_cards
    ->addTab('Content', ['label' => 'Content'])
        ->addText('section_heading', [
            'label'         => 'Section Heading',
            'default_value' => 'Latest from the blog',
        ])
        ->addSelect('section_heading_tag', [
            'label'   => 'Section Heading Tag',
            'choices' => [
                'h1'=>'h1','h2'=>'h2','h3'=>'h3','h4'=>'h4','h5'=>'h5','h6'=>'h6','span'=>'span','p'=>'p',
            ],
            'default_value' => 'h2',
        ])
        ->addNumber('posts_per_page', [
            'label'         => 'Posts Per Page',
            'default_value' => 3,
            'min' => 1, 'max' => 12, 'step' => 1,
        ])
        // CTAs (use link arrays; fallback to post links)
        ->addLink('small_cta', [
            'label'         => 'Small Cards – Override CTA Link (optional)',
            'return_format' => 'array',
        ])
        ->addText('small_cta_text', [
            'label'         => 'Small Cards – CTA Text',
            'default_value' => 'Discover',
        ])
        ->addLink('big_cta', [
            'label'         => 'Big Card – Override CTA Link (optional)',
            'return_format' => 'array',
        ])
        ->addText('big_cta_text', [
            'label'         => 'Big Card – CTA Text',
            'default_value' => 'Read more',
        ])

    ->addTab('Design', ['label' => 'Design'])
        ->addColorPicker('section_bg_color', [
            'label'         => 'Section Background Color',
            'default_value' => '#FFFFFF',
        ])
        // Tailwind utility classes as choices for overlay/typography colors
        ->addSelect('overlay_bg_class', [
            'label'   => 'Overlay Background Class',
            'choices' => [
                'bg-[#ffffff85]'  => 'bg-[#ffffff85]',
                'bg-[#ffffff85]'  => 'bg-[#ffffff85]',
                'bg-black/20'  => 'bg-black/20',
                'bg-black/30'  => 'bg-black/30',
            ],
            'default_value' => 'bg-[#ffffff85]',
            'ui' => 1,
        ])
        ->addSelect('overlay_blur_class', [
            'label'   => 'Overlay Blur Class',
            'choices' => [
                'backdrop-blur'        => 'backdrop-blur',
                'backdrop-blur-sm'     => 'backdrop-blur-sm',
                'backdrop-blur-md'     => 'backdrop-blur-md',
                'backdrop-blur-lg'     => 'backdrop-blur-lg',
                'backdrop-blur-[15px]' => 'backdrop-blur-[15px]',
            ],
            'default_value' => 'backdrop-blur-[15px]',
            'ui' => 1,
        ])
        ->addSelect('text_color_class', [
            'label'   => 'Heading/Text Color Class',
            'choices' => [
                'text-primary' => 'text-primary',
                'text-white'        => 'text-white',
                'text-black'        => 'text-black',
            ],
            'default_value' => 'text-primary',
            'ui' => 1,
        ])
        ->addSelect('date_color_class', [
            'label'   => 'Date Color Class',
            'choices' => [
                'text-black'   => 'text-black',
                'text-white'   => 'text-white',
                'text-slate-300' => 'text-slate-300',
            ],
            'default_value' => 'text-black',
            'ui' => 1,
        ])
        ->addSelect('link_color_class', [
            'label'   => 'Link Color Class',
            'choices' => [
                'text-black/60 hover:text-black' => 'Dark (default)',
                'text-white/80 hover:text-white' => 'Light',
            ],
            'default_value' => 'text-black/60 hover:text-black',
            'ui' => 1,
        ])

    ->addTab('Layout', ['label' => 'Layout'])
        ->addRepeater('padding_settings', [
            'label'        => 'Padding Settings',
            'instructions' => 'Customize padding for different screen sizes.',
            'button_label' => 'Add Screen Size Padding',
        ])
            ->addSelect('screen_size', [
                'label'   => 'Screen Size',
                'choices' => [
                    'xxs' => 'xxs','xs'=>'xs','mob'=>'mob','sm'=>'sm','md'=>'md','lg'=>'lg','xl'=>'xl','xxl'=>'xxl','ultrawide'=>'ultrawide',
                ],
            ])
            ->addNumber('padding_top', [
                'label' => 'Padding Top', 'min'=>0, 'max'=>20, 'step'=>0.1, 'append'=>'rem',
            ])
            ->addNumber('padding_bottom', [
                'label' => 'Padding Bottom', 'min'=>0, 'max'=>20, 'step'=>0.1, 'append'=>'rem',
            ])
        ->endRepeater();

return $blog_cards;
