<?php
/**
 * Team vCard helpers.
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('matrix_vcard_escape')) {
    /**
     * Escape values for use in vCard lines.
     */
    function matrix_vcard_escape($value)
    {
        $value = (string) $value;
        if ($value === '') {
            return '';
        }

        $value = str_replace(array("\r\n", "\r"), "\n", $value);
        $value = str_replace(array('\\', ';', ',', "\n"), array('\\\\', '\;', '\,', '\n'), $value);

        return trim($value);
    }
}

if (!function_exists('matrix_is_probable_vcard_url')) {
    /**
     * Check whether a URL likely points to a vCard file.
     */
    function matrix_is_probable_vcard_url($url)
    {
        if (!is_string($url) || $url === '' || !filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        $path = (string) wp_parse_url($url, PHP_URL_PATH);
        $ext = strtolower((string) pathinfo($path, PATHINFO_EXTENSION));

        return in_array($ext, array('vcf', 'vcard', 'vct'), true);
    }
}

if (!function_exists('matrix_get_valid_uploaded_vcard_url')) {
    /**
     * Resolve a reliable uploaded vCard URL from an ACF file field value.
     */
    function matrix_get_valid_uploaded_vcard_url($vcard_file)
    {
        if (is_array($vcard_file)) {
            if (!empty($vcard_file['ID']) && is_numeric($vcard_file['ID'])) {
                $attachment_id = (int) $vcard_file['ID'];
                $attached_path = get_attached_file($attachment_id);
                $attached_url = wp_get_attachment_url($attachment_id);

                if (is_string($attached_path) && $attached_path !== '' && file_exists($attached_path) && matrix_is_probable_vcard_url((string) $attached_url)) {
                    return (string) $attached_url;
                }
            }

            if (!empty($vcard_file['url']) && matrix_is_probable_vcard_url((string) $vcard_file['url'])) {
                return (string) $vcard_file['url'];
            }
        }

        if (is_numeric($vcard_file)) {
            $attachment_id = (int) $vcard_file;
            $attached_path = get_attached_file($attachment_id);
            $attached_url = wp_get_attachment_url($attachment_id);

            if (is_string($attached_path) && $attached_path !== '' && file_exists($attached_path) && matrix_is_probable_vcard_url((string) $attached_url)) {
                return (string) $attached_url;
            }
        }

        if (is_string($vcard_file) && matrix_is_probable_vcard_url($vcard_file)) {
            return $vcard_file;
        }

        return '';
    }
}

if (!function_exists('matrix_get_team_vcard_data')) {
    /**
     * Build a minimal vCard payload from team post + ACF fields.
     */
    function matrix_get_team_vcard_data($post_id)
    {
        $post_id = (int) $post_id;
        if ($post_id <= 0 || get_post_type($post_id) !== 'team') {
            return '';
        }

        $full_name = trim(wp_strip_all_tags((string) get_the_title($post_id)));
        if ($full_name === '') {
            return '';
        }

        $name_parts = preg_split('/\s+/', $full_name);
        $family_name = '';
        $given_name = '';

        if (is_array($name_parts) && count($name_parts) > 1) {
            $family_name = (string) array_pop($name_parts);
            $given_name = implode(' ', $name_parts);
        } else {
            $given_name = $full_name;
        }

        $job_title = function_exists('get_field') ? (string) get_field('job_title', $post_id) : '';
        $email = function_exists('get_field') ? (string) get_field('contact_email', $post_id) : '';
        $phone = function_exists('get_field') ? (string) get_field('contact_phone', $post_id) : '';
        $linkedin = function_exists('get_field') ? (string) get_field('linkedin_url', $post_id) : '';

        $org_name = wp_strip_all_tags((string) get_bloginfo('name'));
        $profile_url = (string) get_permalink($post_id);

        $lines = array(
            'BEGIN:VCARD',
            'VERSION:3.0',
            'PRODID:-//Matrix Starter//Team vCard//EN',
            'FN:' . matrix_vcard_escape($full_name),
            'N:' . matrix_vcard_escape($family_name) . ';' . matrix_vcard_escape($given_name) . ';;;',
        );

        if ($job_title !== '') {
            $lines[] = 'TITLE:' . matrix_vcard_escape($job_title);
        }
        if ($org_name !== '') {
            $lines[] = 'ORG:' . matrix_vcard_escape($org_name);
        }
        if ($email !== '') {
            $lines[] = 'EMAIL;TYPE=INTERNET:' . matrix_vcard_escape($email);
        }
        if ($phone !== '') {
            $lines[] = 'TEL;TYPE=WORK,VOICE:' . matrix_vcard_escape($phone);
        }
        if ($linkedin !== '') {
            $lines[] = 'URL;TYPE=LinkedIn:' . matrix_vcard_escape($linkedin);
        }
        if ($profile_url !== '') {
            $lines[] = 'URL;TYPE=Profile:' . matrix_vcard_escape($profile_url);
        }

        $lines[] = 'END:VCARD';

        return implode("\r\n", $lines) . "\r\n";
    }
}

if (!function_exists('matrix_get_team_vcard_url')) {
    /**
     * Resolve vCard URL for a team post using generated data only.
     */
    function matrix_get_team_vcard_url($post_id)
    {
        $post_id = (int) $post_id;
        if ($post_id <= 0 || get_post_type($post_id) !== 'team') {
            return '';
        }

        $generated = matrix_get_team_vcard_data($post_id);
        if ($generated === '') {
            return '';
        }

        return add_query_arg(
            array('matrix_team_vcard' => $post_id),
            home_url('/')
        );
    }
}

add_filter('query_vars', static function ($vars) {
    $vars[] = 'matrix_team_vcard';
    return $vars;
});

add_action('template_redirect', static function () {
    $team_id = absint(get_query_var('matrix_team_vcard'));
    if ($team_id <= 0) {
        return;
    }

    if (get_post_type($team_id) !== 'team' || get_post_status($team_id) !== 'publish') {
        status_header(404);
        exit;
    }

    $vcard_data = matrix_get_team_vcard_data($team_id);
    if ($vcard_data === '') {
        status_header(404);
        exit;
    }

    $slug = sanitize_title((string) get_post_field('post_name', $team_id));
    if ($slug === '') {
        $slug = 'team-contact-' . $team_id;
    }
    $filename = $slug . '.vcf';

    while (ob_get_level() > 0) {
        ob_end_clean();
    }

    nocache_headers();
    header('Content-Type: text/x-vcard; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Transfer-Encoding: binary');
    header('X-Robots-Tag: noindex, nofollow', true);

    echo $vcard_data; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    exit;
});
