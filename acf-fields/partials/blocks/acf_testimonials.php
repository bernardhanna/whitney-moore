<?php
use StoutLogic\AcfBuilder\FieldsBuilder;

$testimonials_slider = new FieldsBuilder('testimonials_slider', [
  'label' => 'Testimonials Slider',
]);

$testimonials_slider
  ->addTab('Content', ['label' => 'Content'])
    ->addText('subheading', ['label' => 'Subheading', 'default_value' => 'SUBHEADING'])
    ->addText('main_heading', ['label' => 'Heading', 'default_value' => 'Testimonials'])
    ->addSelect('main_heading_tag', [
      'label' => 'Heading Tag',
      'choices' => ['h1'=>'h1','h2'=>'h2','h3'=>'h3','h4'=>'h4','h5'=>'h5','h6'=>'h6','span'=>'span','p'=>'p'],
      'default_value' => 'h2',
    ])
    ->addWysiwyg('intro_text', ['label' => 'Intro Text', 'toolbar' => 'basic', 'media_upload' => 0])

    ->addSelect('data_source', [
      'label' => 'Data Source',
      'choices' => ['latest'=>'Latest Testimonials','select'=>'Select Testimonials','manual'=>'Add Manually'],
      'default_value' => 'latest',
      'ui' => 1,
    ])
    ->addNumber('posts_per_page', [
      'label' => 'How many (latest)', 'default_value' => 12, 'min'=>1, 'max'=>24, 'step'=>1,
      'conditional_logic' => [['field'=>'data_source','operator'=>'==','value'=>'latest']],
    ])
    ->addRelationship('selected_testimonials', [
      'label' => 'Select Testimonials',
      'post_type' => ['testimonial'],
      'return_format' => 'id',
      'filters' => ['search','post_type','taxonomy'],
      'conditional_logic' => [['field'=>'data_source','operator'=>'==','value'=>'select']],
    ])
    ->addRepeater('manual_testimonials', [
      'label' => 'Manual Testimonials', 'button_label' => 'Add Testimonial', 'layout' => 'row',
      'conditional_logic' => [['field'=>'data_source','operator'=>'==','value'=>'manual']],
    ])
      ->addText('name', ['label' => 'Name', 'required' => 1])
      ->addText('role_title', ['label' => 'Position / Title'])
      ->addWysiwyg('testimonial_html', ['label'=>'Testimonial', 'toolbar'=>'basic', 'media_upload'=>0, 'required'=>1])
      ->addImage('photo', ['label'=>'Photo', 'return_format'=>'id', 'preview_size'=>'medium'])
      ->addImage('logo_image', ['label'=>'Company Logo (Image)', 'return_format'=>'id', 'preview_size'=>'thumbnail'])
      ->addTextarea('logo_svg', ['label'=>'Company Logo (Inline SVG)', 'rows'=>5])
    ->endRepeater()

  // Keep functional controls (not visual styles)
  ->addTrueFalse('arrows', ['label'=>'Show Arrows','ui'=>1,'default_value'=>1])
  ->addTrueFalse('dots', ['label'=>'Show Dots','ui'=>1,'default_value'=>0])
  ->addTrueFalse('autoplay', ['label'=>'Autoplay','ui'=>1,'default_value'=>0])
  ->addNumber('autoplay_speed', ['label'=>'Autoplay Speed (ms)','default_value'=>5000,'min'=>1000,'step'=>500])
  ->addNumber('slides_xl', ['label'=>'Slides on ≥1280px','default_value'=>3,'min'=>1,'max'=>6])
  ->addNumber('slides_lg', ['label'=>'Slides on ≥1024px','default_value'=>3,'min'=>1,'max'=>6])
  ->addNumber('slides_md', ['label'=>'Slides on ≥640px', 'default_value'=>2,'min'=>1,'max'=>6])
  ->addNumber('slides_sm', ['label'=>'Slides on <640px', 'default_value'=>1,'min'=>1,'max'=>6])

  ->addTab('Layout', ['label' => 'Layout'])
    ->addRepeater('padding_settings', [
      'label' => 'Padding Settings',
      'instructions' => 'Customize padding for different screen sizes.',
      'button_label' => 'Add Screen Size Padding',
    ])
      ->addSelect('screen_size', [
        'label' => 'Screen Size',
        'choices' => [
          'xxs'=>'xxs','xs'=>'xs','mob'=>'mob','sm'=>'sm','md'=>'md','lg'=>'lg','xl'=>'xl','xxl'=>'xxl','ultrawide'=>'ultrawide',
        ],
      ])
      ->addNumber('padding_top', ['label'=>'Padding Top','min'=>0,'max'=>20,'step'=>0.1,'append'=>'rem'])
      ->addNumber('padding_bottom', ['label'=>'Padding Bottom','min'=>0,'max'=>20,'step'=>0.1,'append'=>'rem'])
    ->endRepeater();

return $testimonials_slider;
