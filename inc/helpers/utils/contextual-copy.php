<?php
/**
 * Copy helpers for context-aware replacements.
 */

if (!function_exists('matrix_get_contextual_subject')) {
    /**
     * Get the current single-page subject for practice areas and sectors.
     */
    function matrix_get_contextual_subject()
    {
        $post_id = get_queried_object_id();
        if (!$post_id) {
            return '';
        }

        $post_type = get_post_type($post_id);
        if (!in_array($post_type, array('practice_areas', 'sectors'), true)) {
            return '';
        }

        $title = get_the_title($post_id);
        if (!is_string($title)) {
            return '';
        }

        return trim(wp_strip_all_tags($title));
    }
}

if (!function_exists('matrix_replace_real_estate_copy')) {
    /**
     * Replace legacy "real estate" text with the current page subject.
     */
    function matrix_replace_real_estate_copy($text)
    {
        if (!is_string($text) || stripos($text, 'real estate') === false) {
            return $text;
        }

        $subject = matrix_get_contextual_subject();
        if ($subject === '') {
            return $text;
        }

        return str_ireplace('real estate', $subject, $text);
    }
}

if (!function_exists('matrix_contains_lorem_placeholder')) {
    /**
     * Check whether text contains lorem ipsum placeholder copy.
     */
    function matrix_contains_lorem_placeholder($text)
    {
        if (!is_string($text)) {
            return false;
        }

        return stripos(wp_strip_all_tags($text), 'lorem ipsum') !== false;
    }
}

if (!function_exists('matrix_replace_lorem_copy')) {
    /**
     * Replace lorem ipsum copy with contextual fallback text.
     */
    function matrix_replace_lorem_copy($text, $fallback = '')
    {
        if (!matrix_contains_lorem_placeholder($text)) {
            return $text;
        }

        if (is_string($fallback) && trim($fallback) !== '') {
            return $fallback;
        }

        $subject = matrix_get_contextual_subject();
        if ($subject === '') {
            return '';
        }

        return sprintf('Learn how our %s team supports clients with clear, practical advice.', $subject);
    }
}
