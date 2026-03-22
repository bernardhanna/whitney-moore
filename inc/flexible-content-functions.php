<?php
// File: inc/flexible-content-functions.php

/**
 * Load Flexible Content Templates
 * 
 * Automatically loads flexible content templates based on the layout name
 */
function load_flexible_content_templates($post_id = null)
{
  // If no post_id is provided, use the current page's ID.
  if (!$post_id) {
    $post_id = is_home() ? get_option('page_for_posts') : get_the_ID();
  }

  error_log("Loading Flexible Content for Post ID: " . $post_id);
  $is_about_why_page = $post_id && matrix_is_about_or_why_us_page($post_id);
  $flex_rows = $post_id ? get_field('flexible_content_blocks', $post_id) : [];
  $row_count = is_array($flex_rows) ? count($flex_rows) : 0;

  if ($post_id && $row_count > 0 && have_rows('flexible_content_blocks', $post_id)) {
    while (have_rows('flexible_content_blocks', $post_id)) : the_row();
      $layout = get_row_layout();
      $template_path = get_template_directory() . '/template-parts/flexi/' . $layout . '.php';

      if (file_exists($template_path)) {
        get_template_part('template-parts/flexi/' . $layout);
      } else {
        error_log("Missing flexible content template file: {$layout}.php");
      }
    endwhile;
    return true;
  }

  if ($is_about_why_page) {
    matrix_render_about_why_us_fallback_flexi();
    return true;
  }

  error_log("No ACF Flexible Content Blocks found for Post ID: " . $post_id);
  return false;
}

/**
 * Detect "About Us" / "Why Us" pages by slug.
 */
function matrix_is_about_or_why_us_page($post_id)
{
  $slug = sanitize_title((string) get_post_field('post_name', (int) $post_id));
  if ($slug === '') {
    return false;
  }

  return in_array($slug, ['about-us', 'about', 'why-us'], true);
}

/**
 * Curated fallback stack for About/Why Us pages when ACF rows are empty.
 */
function matrix_render_about_why_us_fallback_flexi()
{
  $context = ['matrix_flexi_context' => 'why_us_fallback'];
  $layouts = matrix_get_about_why_us_default_layouts();

  foreach ($layouts as $layout) {
    get_template_part('template-parts/flexi/' . $layout, null, $context);
  }
}

/**
 * Default layout order for About/Why Us page composition.
 */
function matrix_get_about_why_us_default_layouts()
{
  return [
    'title_001',
    'content_block_one',
    'sectors_grid',
    'team_carousel',
    'testimonials_slider',
    'partners',
    'cta_two',
  ];
}

/**
 * Build empty ACF flexible rows from layout names.
 */
function matrix_get_about_why_us_seed_rows()
{
  return [
    [
      'acf_fc_layout' => 'title_001',
      'heading' => 'Why clients choose Whitney Moore',
      'heading_tag' => 'h2',
      'description' => '<p>We combine deep sector insight, responsive service, and clear commercial advice to help clients move with confidence.</p>',
    ],
    [
      'acf_fc_layout' => 'content_block_one',
      'section_name' => 'About us',
      'heading' => 'Built on trusted relationships and practical advice',
      'heading_tag' => 'h2',
      'content' => '<p>For over a century, our team has supported businesses, institutions, and investors with clear legal guidance grounded in commercial reality.</p><p>We focus on outcomes, stay close to your priorities, and deliver advice that is straightforward to act on.</p>',
      'reverse_layout' => 0,
    ],
    [
      'acf_fc_layout' => 'sectors_grid',
      'section_heading' => 'Our sectors',
      'section_heading_tag' => 'p',
      'main_heading' => 'Experience across the markets that matter',
      'main_heading_tag' => 'h2',
      'items_source' => 'post_type',
      'query_post_type' => 'sectors',
      'posts_per_page' => 6,
    ],
    [
      'acf_fc_layout' => 'team_carousel',
      'heading' => 'Meet our people',
      'heading_tag' => 'h2',
      'source_mode' => 'taxonomy',
      'taxonomy_type' => 'team_practice_area',
      'posts_per_page' => 0,
      'order_by' => 'menu_order',
      'order' => 'ASC',
      'show_name' => 1,
      'show_job_title' => 1,
      'enable_slider' => 1,
      'arrows' => 1,
      'dots' => 1,
      'autoplay' => 1,
      'autoplay_speed' => 5000,
      'slides_xl' => 4,
      'slides_lg' => 3,
      'slides_md' => 2,
      'slides_sm' => 1,
    ],
    [
      'acf_fc_layout' => 'testimonials_slider',
      'subheading' => 'Client feedback',
      'main_heading' => 'What our clients say',
      'main_heading_tag' => 'h2',
      'intro_text' => '<p>We are proud to be a long-term partner to leading organisations across Ireland and beyond.</p>',
      'data_source' => 'latest',
      'posts_per_page' => 12,
      'arrows' => 1,
      'dots' => 1,
      'autoplay' => 1,
      'autoplay_speed' => 5000,
      'slides_xl' => 4,
      'slides_lg' => 3,
      'slides_md' => 2,
      'slides_sm' => 1,
    ],
    [
      'acf_fc_layout' => 'partners',
      'heading_text' => 'Trusted by leading organisations',
      'heading_tag' => 'h2',
      'subheading' => 'We support clients across sectors with practical, commercially focused advice.',
    ],
    [
      'acf_fc_layout' => 'cta_two',
      'heading' => 'Ready to talk through your next move?',
      'heading_tag' => 'h2',
      'button' => [
        'title' => 'Make an Inquiry',
        'url' => home_url('/contact-us/'),
        'target' => '_self',
      ],
    ],
  ];
}

/**
 * Seed About/Why Us pages with real editable flexible rows (once).
 */
function matrix_seed_about_why_us_flexi_blocks($post_id)
{
  $post_id = (int) $post_id;
  if ($post_id <= 0) {
    return false;
  }

  if (get_post_type($post_id) !== 'page' || !matrix_is_about_or_why_us_page($post_id)) {
    return false;
  }

  $is_seeded = get_post_meta($post_id, '_matrix_why_us_flexi_seeded', true) === '1';
  if ($is_seeded) {
    return false;
  }

  $existing_rows = get_field('flexible_content_blocks', $post_id, false);
  $has_existing_rows = is_array($existing_rows) && !empty($existing_rows);
  if ($has_existing_rows) {
    return false;
  }

  if (!function_exists('update_field')) {
    return false;
  }

  $did_update = update_field('flexible_content_blocks', matrix_get_about_why_us_seed_rows(), $post_id);
  if ($did_update) {
    update_post_meta($post_id, '_matrix_why_us_flexi_seeded', '1');
    clean_post_cache($post_id);
  }

  return (bool) $did_update;
}

/**
 * Ensure Why Us / About Us pages show editable flexi rows in wp-admin.
 */
function matrix_maybe_seed_about_why_us_flexi_for_editor()
{
  if (!is_admin() || !current_user_can('edit_pages')) {
    return;
  }

  $post_id = isset($_GET['post']) ? absint($_GET['post']) : 0;
  if ($post_id <= 0 || !current_user_can('edit_post', $post_id)) {
    return;
  }

  matrix_seed_about_why_us_flexi_blocks($post_id);
}
add_action('load-post.php', 'matrix_maybe_seed_about_why_us_flexi_for_editor');
/**
 * Get Available Flexible Content Layouts
 * 
 * Returns an array of available layout names based on template files
 */
function get_available_flexi_layouts()
{
  $flexi_path = get_template_directory() . '/template-parts/flexi/';
  $files = glob($flexi_path . '*.php');

  return array_map(function ($file) {
    return basename($file, '.php');
  }, $files);
}

/**
 * Validate Flexible Content Layout
 * 
 * Ensures that ACF field definitions have corresponding template files
 */
function validate_flexi_layout($layout_name)
{
  $available_layouts = get_available_flexi_layouts();
  if (!in_array($layout_name, $available_layouts)) {
    error_log("Warning: ACF flexible content layout '{$layout_name}' has no corresponding template file");
    return false;
  }
  return true;
}

function force_hero_as_first_block($value, $post_id, $field)
{
  if ($field['name'] === 'flexible_content_layout') {
    $hero_block = [];
    $other_blocks = [];

    foreach ($value as $block) {
      if ($block['acf_fc_layout'] === 'hero_001') {
        $hero_block = $block;
      } else {
        $other_blocks[] = $block;
      }
    }

    // Always place hero first
    if (!empty($hero_block)) {
      array_unshift($other_blocks, $hero_block);
    }

    return $other_blocks;
  }
  return $value;
}
add_filter('acf/update_value/name=flexible_content_layout', 'force_hero_as_first_block', 10, 3);

function apply_acf_to_blog_page($query)
{
  if (!is_admin() && $query->is_home() && $query->is_main_query()) {
    $query->set('page_id', get_option('page_for_posts'));
  }
}
add_action('pre_get_posts', 'apply_acf_to_blog_page');
