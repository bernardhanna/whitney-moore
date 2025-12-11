<?php
use StoutLogic\AcfBuilder\FieldsBuilder;

$navigationFields = new FieldsBuilder('navigation_settings', [
    'title' => 'Navbar',
]);

/*
|--------------------------------------------------------------------------
| Navigation Settings (ACF Options)
|--------------------------------------------------------------------------
*/
$navigationFields
  ->addGroup('navigation_settings_start', [
      'label' => 'Navigation Settings',
  ])

    // Phone + CTA Link
    ->addText('phone_number', [
        'label'       => 'Phone Number',
        'placeholder' => '+353 1 283 2967',
    ])
    ->addLink('contact_button', [
        'label' => 'Contact Button',
    ])


  ->addAccordion('navigation_settings_end')->endpoint();

return $navigationFields;
