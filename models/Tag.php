<?php
require_once __DIR__ . '/../config/database.php';

class Tag
{
    public function getPopular($limit = 20)
    {
        $limit = (int) $limit;
        $sql = "SELECT t.name, t.slug, COUNT(pt.post_id) AS tag_count
                FROM tags t
                JOIN post_tags pt ON pt.tag_id = t.id
                JOIN posts p ON p.id = pt.post_id
                    AND p.status = 'published'
                    AND (p.published_at IS NULL OR p.published_at <= NOW())
                GROUP BY t.id
                ORDER BY tag_count DESC, t.name ASC
                LIMIT ?";

        $stmt = db_query($sql, 'i', [$limit]);
        return db_fetch_all($stmt);
    }
}
