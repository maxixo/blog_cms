<?php
require_once __DIR__ . '/../config/database.php';

class Post
{
    public function getPaginated($page = 1, $perPage = 10, $categorySlug = null, $tagSlug = null)
    {
        $offset = max(0, ($page - 1) * $perPage);

        $sql = "SELECT p.id, p.title, p.slug, p.excerpt, p.content, p.featured_image, p.published_at, p.created_at,
                       u.id AS author_id, u.name AS author_name,
                       c.name AS category_name, c.slug AS category_slug
                FROM posts p
                LEFT JOIN users u ON u.id = p.author_id
                LEFT JOIN categories c ON c.id = p.category_id
                WHERE p.status = 'published' AND (p.published_at IS NULL OR p.published_at <= NOW())";

        $params = [];
        $types = '';

        if (!empty($categorySlug)) {
            $sql .= ' AND c.slug = ?';
            $params[] = $categorySlug;
            $types .= 's';
        }

        if (!empty($tagSlug)) {
            $sql .= " AND EXISTS (
                SELECT 1 FROM post_tags pt
                JOIN tags t ON t.id = pt.tag_id
                WHERE pt.post_id = p.id AND t.slug = ?
            )";
            $params[] = $tagSlug;
            $types .= 's';
        }

        $sql .= ' ORDER BY p.published_at DESC, p.id DESC LIMIT ? OFFSET ?';
        $params[] = (int) $perPage;
        $params[] = (int) $offset;
        $types .= 'ii';

        $stmt = db_query($sql, $types, $params);
        return db_fetch_all($stmt);
    }

    public function getTotal($categorySlug = null, $tagSlug = null)
    {
        $sql = "SELECT COUNT(DISTINCT p.id) AS total
                FROM posts p
                LEFT JOIN categories c ON c.id = p.category_id
                WHERE p.status = 'published' AND (p.published_at IS NULL OR p.published_at <= NOW())";

        $params = [];
        $types = '';

        if (!empty($categorySlug)) {
            $sql .= ' AND c.slug = ?';
            $params[] = $categorySlug;
            $types .= 's';
        }

        if (!empty($tagSlug)) {
            $sql .= " AND EXISTS (
                SELECT 1 FROM post_tags pt
                JOIN tags t ON t.id = pt.tag_id
                WHERE pt.post_id = p.id AND t.slug = ?
            )";
            $params[] = $tagSlug;
            $types .= 's';
        }

        $stmt = db_query($sql, $types, $params);
        $row = db_fetch_one($stmt);
        return (int) ($row['total'] ?? 0);
    }

    public function getBySlug($slug)
    {
        $sql = "SELECT p.*, u.id AS author_id, u.name AS author_name, u.avatar AS author_avatar,
                       c.name AS category_name, c.slug AS category_slug
                FROM posts p
                LEFT JOIN users u ON u.id = p.author_id
                LEFT JOIN categories c ON c.id = p.category_id
                WHERE p.slug = ?
                  AND p.status = 'published'
                  AND (p.published_at IS NULL OR p.published_at <= NOW())
                LIMIT 1";

        $stmt = db_query($sql, 's', [$slug]);
        return db_fetch_one($stmt);
    }

    public function incrementViews($postId)
    {
        $stmt = db_query('UPDATE posts SET views = views + 1 WHERE id = ?', 'i', [$postId]);
        return db_affected_rows($stmt);
    }

    public function getRecent($limit = 5)
    {
        $limit = (int) $limit;
        $sql = "SELECT p.id, p.title, p.slug, p.published_at
                FROM posts p
                WHERE p.status = 'published' AND (p.published_at IS NULL OR p.published_at <= NOW())
                ORDER BY p.published_at DESC, p.id DESC
                LIMIT ?";

        $stmt = db_query($sql, 'i', [$limit]);
        return db_fetch_all($stmt);
    }
}
