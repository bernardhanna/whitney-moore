<?php

use StoutLogic\AcfBuilder\FieldsBuilder;

$hero_001 = new FieldsBuilder('hero_001', [
  'label' => 'Hero Section with Content Box',
]);

$hero_001
  ->addTab('Content', ['label' => 'Content'])
  ->addText('small_heading', [
      'label' => 'Small Heading Text',
      'instructions' => 'Enter the small heading text that appears above the main heading.',
      'default_value' => 'WHITNEY MOORE',
      'required' => 0,
  ])
  ->addSelect('small_heading_tag', [
      'label' => 'Small Heading Tag',
      'instructions' => 'Select the HTML tag for the small heading.',
      'choices' => [
          'h1' => 'H1',
          'h2' => 'H2',
          'h3' => 'H3',
          'h4' => 'H4',
          'h5' => 'H5',
          'h6' => 'H6',
          'p' => 'Paragraph',
          'span' => 'Span',
      ],
      'default_value' => 'p',
      'required' => 1,
  ])
  ->addText('main_heading', [
      'label' => 'Main Heading Text',
      'instructions' => 'Enter the main heading text for the hero section.',
      'default_value' => 'Experience.Clarity.Results.',
      'required' => 1,
  ])
  ->addSelect('main_heading_tag', [
      'label' => 'Main Heading Tag',
      'instructions' => 'Select the HTML tag for the main heading.',
      'choices' => [
          'h1' => 'H1',
          'h2' => 'H2',
          'h3' => 'H3',
          'h4' => 'H4',
          'h5' => 'H5',
          'h6' => 'H6',
          'p' => 'Paragraph',
          'span' => 'Span',
      ],
      'default_value' => 'h1',
      'required' => 1,
  ])
  ->addWysiwyg('description', [
      'label' => 'Description Text',
      'instructions' => 'Enter the description text that appears below the main heading.',
      'default_value' => 'Driven by values since 1882.',
      'media_upload' => 0,
      'tabs' => 'all',
      'toolbar' => 'full',
  ])
  ->addLink('primary_button', [
      'label' => 'Primary Button',
      'instructions' => 'Configure the primary call-to-action button.',
      'return_format' => 'array',
  ])
  ->addLink('secondary_button', [
      'label' => 'Secondary Button',
      'instructions' => 'Configure the secondary call-to-action button.',
      'return_format' => 'array',
  ])
  ->addImage('background_image', [
      'label' => 'Background Image',
      'instructions' => 'Upload the background image for the hero section.',
      'return_format' => 'id',
      'preview_size' => 'medium',
  ])

  ->addTab('Design', ['label' => 'Design'])
  ->addColorPicker('content_box_bg_color', [
      'label' => 'Content Box Background Color',
      'instructions' => 'Set the background color for the content box.',
      'default_value' => '#ffffff',
  ])
  ->addColorPicker('content_box_border_color', [
      'label' => 'Content Box Border Color',
      'instructions' => 'Set the border color for the content box.',
      'default_value' => '#0902A4',
  ])
  ->addNumber('outer_border_width', [
      'label' => 'Outer Border Width',
      'instructions' => 'Set the outer border width in pixels.',
      'min' => 0,
      'max' => 20,
      'step' => 1,
      'default_value' => 5,
      'append' => 'px',
  ])
  ->addNumber('inner_border_width', [
      'label' => 'Inner Border Width',
      'instructions' => 'Set the inner border width in pixels.',
      'min' => 0,
      'max' => 20,
      'step' => 1,
      'default_value' => 3,
      'append' => 'px',
  ])
  ->addColorPicker('small_heading_color', [
      'label' => 'Small Heading Color',
      'instructions' => 'Set the color for the small heading text.',
      'default_value' => '#000000',
  ])
  ->addColorPicker('main_heading_color', [
      'label' => 'Main Heading Color',
      'instructions' => 'Set the color for the main heading text.',
      'default_value' => '#0902A4',
  ])
  ->addColorPicker('description_color', [
      'label' => 'Description Color',
      'instructions' => 'Set the color for the description text.',
      'default_value' => '#000000',
  ])

  ->addTab('Layout', ['label' => 'Layout'])
  ->addRepeater('padding_settings', [
      'label' => 'Padding Settings',
      'instructions' => 'Customize padding for different screen sizes.',
      'button_label' => 'Add Screen Size Padding',
      'min' => 0,
      'max' => 10,
  ])
  ->addSelect('screen_size', [
      'label' => 'Screen Size',
      'instructions' => 'Select the screen size for this padding setting.',
      'choices' => [
          'xxs' => 'XXS',
          'xs' => 'XS',
          'mob' => 'Mobile',
          'sm' => 'Small',
          'md' => 'Medium',
          'lg' => 'Large',
          'xl' => 'Extra Large',
          'xxl' => 'XXL',
          'ultrawide' => 'Ultrawide',
      ],
      'required' => 1,
  ])
  ->addNumber('padding_top', [
      'label' => 'Padding Top',
      'instructions' => 'Set the top padding in rem.',
      'min' => 0,
      'max' => 20,
      'step' => 0.1,
      'default_value' => 10,
      'append' => 'rem',
  ])
  ->addNumber('padding_bottom', [
      'label' => 'Padding Bottom',
      'instructions' => 'Set the bottom padding in rem.',
      'min' => 0,
      'max' => 20,
      'step' => 0.1,
      'default_value' => 10,
      'append' => 'rem',
  ])
  ->endRepeater();

return $hero_001;
