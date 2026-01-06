<?php
get_header();
?>

<?php
if (function_exists('load_hero_templates')) {
    load_hero_templates();
}
?>

<?php
// Keep Flexible Content loader after the main content if your theme requires it
if (function_exists('load_flexible_content_templates')) {
    load_flexible_content_templates();
} ?>
<?php get_footer(); ?>
