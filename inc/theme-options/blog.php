<?php
// File: theme-options/blog.php

use StoutLogic\AcfBuilder\FieldsBuilder;

$blogFields = new FieldsBuilder('blog_fields');

$blogFields
  ->addGroup('blog_settings', [
    'label' => 'Blog Settings',
  ])

    ->addImage('hero_background_image', [
      'label'         => 'Hero Background Image',
      'instructions'  => 'Upload a hero background image.',
      'return_format' => 'array',
      'preview_size'  => 'medium',
    ])

    ->addText('hero_kicker_text', [
      'label'        => 'Hero Kicker Text',
      'instructions' => 'Small text above the hero heading (e.g. WHITNEY MOORE).',
      'default_value'=> 'WHITNEY MOORE',
    ])

    ->addSelect('hero_heading_tag', [
      'label'        => 'Hero Heading Tag',
      'choices'      => [
        'h1'   => '<h1>',
        'h2'   => '<h2>',
        'h3'   => '<h3>',
        'h4'   => '<h4>',
        'h5'   => '<h5>',
        'h6'   => '<h6>',
        'span' => '<span>',
        'p'    => '<p>',
      ],
      'default_value'=> 'h1',
      'ui'           => 1,
    ])

    ->addText('hero_heading_text', [
      'label'        => 'Hero Heading Text',
      'instructions' => 'Main hero heading. You can include <br> for line breaks.',
      'default_value'=> "Experience.<br>Clarity.<br>Results.",
    ])

    ->addText('hero_subheading_text', [
      'label'        => 'Hero Sub-heading Text (Fallback)',
      'instructions' => 'Used if Hero Body Text is empty.',
      'default_value'=> 'Driven by values since 1882.',
    ])

    ->addWysiwyg('hero_body_text', [
      'label'        => 'Hero Body Text',
      'instructions' => 'Text below the heading (uses wp_editor styling).',
      'required'     => 0,
    ])

    ->addText('filter_section_title', [
      'label'        => 'Filter Section Title',
      'default_value'=> 'Filter by',
    ])

  ->endGroup();

return $blogFields;
