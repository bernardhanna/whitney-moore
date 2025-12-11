<?php
// File: acf-fields/partials/post-types/testimonial-meta.php
if (!defined('ABSPATH')) exit;

use StoutLogic\AcfBuilder\FieldsBuilder;

$testimonial_meta = new FieldsBuilder('testimonial_meta', [
    'label' => 'Testimonial Meta',
]);

$testimonial_meta
    ->setLocation('post_type', '==', 'testimonial')

    ->addMessage('howto', 'Use <strong>Title</strong> for Name, <strong>Excerpt</strong> for Position, and <strong>Content</strong> for the testimonial text.', [
        'esc_html' => 0,
    ])

    ->addImage('logo_image', [
        'label'         => 'Company Logo (Image)',
        'return_format' => 'id',
        'preview_size'  => 'medium',
    ])

    ->addTextarea('logo_svg', [
        'label' => 'Company Logo (Inline SVG)',
        'rows'  => 5,
    ]);

return $testimonial_meta;
