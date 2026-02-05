<?php
if (!defined('APP_STARTED')) {
    http_response_code(403);
    exit('Forbidden');
}

function get_query_value($key)
{
    if (!isset($_GET[$key])) {
        return '';
    }

    $value = $_GET[$key];
    if (is_array($value)) {
        $value = reset($value);
    }

    return trim((string) $value);
}

function truncate_text($text, $limit = 160)
{
    $text = trim((string) $text);
    if ($text === '') {
        return '';
    }

    $length = function_exists('mb_strlen') ? mb_strlen($text) : strlen($text);
    if ($length <= $limit) {
        return $text;
    }

    $slice = function_exists('mb_substr') ? mb_substr($text, 0, $limit) : substr($text, 0, $limit);
    $slice = rtrim($slice, " \t\n\r\0\x0B,.;:-");
    return $slice . '...';
}

function make_excerpt($post, $limit = 160)
{
    $excerpt = trim((string) ($post['excerpt'] ?? ''));
    if ($excerpt === '') {
        $excerpt = trim(strip_tags((string) ($post['content'] ?? '')));
    }

    $excerpt = preg_replace('/\s+/', ' ', $excerpt);
    return truncate_text($excerpt, $limit);
}

function resolve_image_url($path)
{
    $path = trim((string) $path);
    if ($path === '') {
        return DEFAULT_OG_IMAGE;
    }

    if (preg_match('#^https?://#i', $path)) {
        return $path;
    }

    $path = ltrim($path, '/');
    if (strpos($path, 'public/') !== 0 && strpos($path, 'uploads/') === 0) {
        $path = 'public/' . $path;
    }
    return BASE_URL . '/' . $path;
}

function format_post_date($post)
{
    $value = $post['published_at'] ?? $post['created_at'] ?? '';
    if ($value === '' || $value === null) {
        return '';
    }

    return date('M j, Y', strtotime($value));
}

function create_slug($text)
{
    // Convert to lowercase
    $slug = mb_strtolower($text, 'UTF-8');
    
    // Replace spaces and special characters with hyphens
    $slug = preg_replace('/[^\p{L}\p{N}\s-]/u', '', $slug);
    $slug = preg_replace('/[\s-]+/', '-', $slug);
    
    // Remove leading/trailing hyphens
    $slug = trim($slug, '-');
    
    // Remove multiple consecutive hyphens
    $slug = preg_replace('/-+/', '-', $slug);
    
    return $slug;
}
