<?php

use StoutLogic\AcfBuilder\FieldsBuilder;

$not_found = new FieldsBuilder('not_found', [
  'label' => '404 Page',
]);

// Attach to ACF Options page (adjust slug if you use a custom one)
$not_found->setLocation('options_page', '==', 'acf-options');

$not_found
  ->addGroup('not_found_settings', [
    'label' => '404 Page Settings',
  ])

    ->addTab('Content', ['placement' => 'top'])

      ->addImage('hero_background', [
        'label'        => 'Hero Background Image',
        'instructions' => 'Background image for the 404 hero.',
        'return_format'=> 'id',
        'preview_size' => 'large',
      ])

      ->addText('eyebrow', [
        'label'         => 'Eyebrow (Small Title)',
        'default_value' => 'PAGE NOT FOUND',
      ])

      ->addTextarea('hero_big_heading', [
        'label'        => 'Big Heading (supports <br>)',
        'instructions' => 'Use <br> for line breaks.',
        'rows'         => 3,
        'new_lines'    => '', // do NOT auto-wrap in <p>
        'default_value'=> "Problems<br>happen",
      ])

      ->addTextarea('hero_intro', [
        'label'         => 'Intro Text',
        'rows'          => 3,
        'default_value' => 'We can help.',
      ])

      ->addLink('primary_button', [
        'label'         => 'Primary Button',
        'return_format' => 'array',
        'default_value' => [
          'url'    => home_url('/'),
          'title'  => 'Back to homepage',
          'target' => '',
        ],
      ])

      ->addLink('secondary_button', [
        'label'         => 'Secondary Button',
        'return_format' => 'array',
        'default_value' => [
          'url'    => '',
          'title'  => '',
          'target' => '',
        ],
      ])

  ->endGroup();

return $not_found;
