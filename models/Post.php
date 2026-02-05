<?php
require_once __DIR__ . '/../config/database.php';

// models/Post.php

class Post {
    
    public function getPaginated($page = 1, $limit = 10, $category_slug = null, $tag_slug = null) {
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT p.id, p.title, p.slug, p.excerpt, p.featured_image, 
                       p.published_at, p.views, p.status,
                       u.username as author_name,
                       c.name as category_name, c.slug as category_slug
                FROM posts p
                LEFT JOIN users u ON p.author_id = u.id
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.status = 'published'";
        
        $params = [];
        $types = '';
        
        if ($category_slug) {
            $sql .= " AND c.slug = ?";
            $params[] = $category_slug;
            $types .= 's';
        }
        
        if ($tag_slug) {
            $sql .= " AND p.id IN (
                SELECT pt.post_id FROM post_tags pt
                JOIN tags t ON pt.tag_id = t.id
                WHERE t.slug = ?
            )";
            $params[] = $tag_slug;
            $types .= 's';
        }
        
        $sql .= " ORDER BY p.published_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        $types .= 'ii';
        
        // CORRECT ORDER: sql, types, params
        return db_fetch_all($sql, $types, $params);
    }
    
    public function getBySlug($slug) {
        $sql = "SELECT p.*, 
                       u.username as author_name,
                       u.avatar as author_avatar,
                       c.name as category_name, 
                       c.slug as category_slug
                FROM posts p
                LEFT JOIN users u ON p.author_id = u.id
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.slug = ? AND p.status = 'published'";
        
        return db_fetch($sql, 's', [$slug]);
    }
    
    public function getById($id) {
        $sql = "SELECT p.*, 
                       u.username as author_name,
                       u.avatar as author_avatar,
                       c.name as category_name, 
                       c.slug as category_slug
                FROM posts p
                LEFT JOIN users u ON p.author_id = u.id
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.id = ?";
        
        return db_fetch($sql, 'i', [$id]);
    }
    
    public function getTotal($category_slug = null, $tag_slug = null) {
        $sql = "SELECT COUNT(*) as total FROM posts p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.status = 'published'";
        
        $params = [];
        $types = '';
        
        if ($category_slug) {
            $sql .= " AND c.slug = ?";
            $params[] = $category_slug;
            $types .= 's';
        }
        
        if ($tag_slug) {
            $sql .= " AND p.id IN (
                SELECT pt.post_id FROM post_tags pt
                JOIN tags t ON pt.tag_id = t.id
                WHERE t.slug = ?
            )";
            $params[] = $tag_slug;
            $types .= 's';
        }
        
        $result = db_fetch($sql, $types, $params);
        return $result ? $result['total'] : 0;
    }
    
    public function getRecent($limit = 5) {
        $limit = (int) $limit;
        $sql = "SELECT p.id, p.title, p.slug, p.excerpt, p.featured_image, 
                       p.published_at, p.views,
                       u.username as author_name,
                       c.name as category_name, c.slug as category_slug
                FROM posts p
                LEFT JOIN users u ON p.author_id = u.id
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.status = 'published'
                ORDER BY p.published_at DESC
                LIMIT ?";
        
        return db_fetch_all($sql, 'i', [$limit]);
    }
    
    public function create($data) {
        $sql = "INSERT INTO posts (
                    title, slug, content, excerpt, featured_image,
                    author_id, category_id, status, 
                    canonical_url, meta_description, meta_keywords,
                    published_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $slug = $this->generateUniqueSlug($data['title']);
        $published_at = ($data['status'] ?? 'draft') === 'published' ? date('Y-m-d H:i:s') : null;
        
        $params = [
            $data['title'],
            $slug,
            $data['content'],
            $data['excerpt'] ?? '',
            $data['featured_image'] ?? null,
            $data['author_id'],
            $data['category_id'] ?? null,
            $data['status'] ?? 'draft',
            $data['canonical_url'] ?? null,
            $data['meta_description'] ?? '',
            $data['meta_keywords'] ?? '',
            $published_at
        ];
        
        $result = db_execute($sql, 'sssssiisssss', $params);
        return $result['insert_id'];
    }
    
    public function update($id, $data) {
        $fields = [];
        $params = [];
        $types = '';
        
        foreach ($data as $key => $value) {
            $fields[] = "$key = ?";
            $params[] = $value;
            $types .= is_int($value) ? 'i' : 's';
        }
        
        $sql = "UPDATE posts SET " . implode(', ', $fields) . " WHERE id = ?";
        $params[] = $id;
        $types .= 'i';
        
        return db_execute($sql, $types, $params);
    }
    
    public function delete($id) {
        return db_execute("DELETE FROM posts WHERE id = ?", 'i', [$id]);
    }
    
    public function incrementViews($post_id) {
        return db_execute("UPDATE posts SET views = views + 1 WHERE id = ?", 'i', [$post_id]);
    }
    
    private function generateUniqueSlug($title) {
        require_once __DIR__ . '/../includes/helpers.php';
        
        $slug = create_slug($title);
        $original_slug = $slug;
        $counter = 1;
        
        while ($this->slugExists($slug)) {
            $slug = $original_slug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }
    
    private function slugExists($slug) {
        $result = db_fetch("SELECT id FROM posts WHERE slug = ?", 's', [$slug]);
        return $result !== null;
    }
}