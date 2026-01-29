<?php
require_once __DIR__ . '/../config/database.php';

class Category
{
    public function getAllWithCounts()
    {
        $sql = "SELECT c.name, c.slug, COUNT(p.id) AS post_count
                FROM categories c
                LEFT JOIN posts p ON p.category_id = c.id
                    AND p.status = 'published'
                    AND (p.published_at IS NULL OR p.published_at <= NOW())
                GROUP BY c.id
                ORDER BY c.name ASC";

        return db_fetch_all($sql);
    }
}
