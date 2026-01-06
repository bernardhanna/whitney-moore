<?php
use StoutLogic\AcfBuilder\FieldsBuilder;

$team_single_fields = new FieldsBuilder('team_single_fields', [
    'label' => 'Team — Single Fields',
]);

$team_single_fields->setLocation('post_type', '==', 'team');

$team_single_fields
    ->addTab('Profile', ['label' => 'Profile'])
        ->addText('job_title', [
            'label' => 'Position / Job Title',
            'instructions' => 'Shown near the name.',
        ])
        ->addImage('headshot', [
            'label' => 'Headshot',
            'instructions' => 'If empty, template falls back to featured image or default.',
            'return_format' => 'id',
            'preview_size'  => 'medium',
            'library'       => 'all',
        ])

    ->addTab('Contact', ['label' => 'Contact'])
        ->addEmail('contact_email', [
            'label' => 'Email',
        ])
        ->addText('contact_phone', [
            'label' => 'Phone',
        ])
        ->addUrl('twitter_url', [
            'label' => 'Twitter/X URL',
            'instructions' => 'Full URL, e.g. https://twitter.com/username or https://x.com/username',
        ])
        ->addText('twitter_handle', [
            'label' => 'Twitter/X Handle',
            'instructions' => 'Shown as link text (e.g. @john1982). Leave empty to auto-derive from the URL.',
        ])
        ->addUrl('linkedin_url', [
            'label' => 'LinkedIn URL',
            'instructions' => 'Full URL, e.g. https://www.linkedin.com/in/username',
        ])
        ->addText('linkedin_handle', [
            'label' => 'LinkedIn Handle',
            'instructions' => 'Shown as link text (e.g. john.lynch). Leave empty to auto-derive from the URL.',
        ])

    ->addTab('Assets', ['label' => 'Assets'])
        ->addFile('vcard_file', [
            'label' => 'vCard (.vcf)',
            'instructions' => 'Upload a .vcf for “Download vCard”. If your server blocks .vcf, use the External URL below.',
            'return_format' => 'array', // url, title, etc.
            'library'       => 'all',
            'mime_types'    => 'vcf',
        ])
        ->addUrl('vcard_external_url', [
            'label' => 'vCard External URL',
            'instructions' => 'Optional: link to a .vcf hosted elsewhere (Dropbox, CDN, etc.). Used if no file uploaded.',
        ])

    ->addTab('Education', ['label' => 'Education'])
        ->addWysiwyg('education', [
            'label' => 'Education',
            'tabs'          => 'all',
            'toolbar'       => 'full',
            'media_upload'  => 0,
            'delay'         => 0,
        ])

    ->addTab('Testimonials', ['label' => 'Testimonials'])
        ->addTrueFalse('enable_testimonials', [
            'label' => 'Enable Testimonials',
            'ui' => 1,
            'default_value' => 1,
        ])
        ->addText('testimonials_heading', [
            'label' => 'Heading (optional)',
            'instructions' => 'Defaults to “What they say about {Member Name}” when empty.',
            'default_value' => '',
        ])
        ->addRepeater('team_testimonials', [
            'label' => 'Testimonials',
            'instructions' => 'Used by the Slick slider under the main content.',
            'button_label' => 'Add Testimonial',
            'layout' => 'block',
            'min' => 0,
            'collapsed' => 'testimonial_text',
        ])
            ->addTextarea('testimonial_text', [
                'label' => 'Testimonial Text',
                'rows' => 4,
            ])
            ->addText('attribution_source', [
                'label' => 'Source (e.g., Legal 500 EMEA)',
            ])
            ->addText('attribution_year', [
                'label' => 'Year (e.g., 2022)',
            ])
        ->endRepeater();

return $team_single_fields;
