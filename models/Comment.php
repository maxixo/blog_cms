<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

class Comment
{
    /**
     * Get approved comments for a post
     */
    public function getByPostId($post_id)
    {
        $sql = "SELECT c.*, 
                       u.username as author_name,
                       u.avatar as author_avatar
                FROM comments c
                LEFT JOIN users u ON c.user_id = u.id
                WHERE c.post_id = ? AND c.status = 'approved'
                ORDER BY c.created_at ASC";
        
        return db_fetch_all($sql, 'i', [$post_id]);
    }

    /**
     * Create a new comment (auto-approved for logged-in users)
     */
    public function create($data)
    {
        $sql = "INSERT INTO comments (
                    post_id, user_id, content, status
                ) VALUES (?, ?, ?, 'approved')";
        
        $params = [
            $data['post_id'],
            $data['user_id'],
            $data['content']
        ];
        
        $result = db_execute($sql, 'iis', $params);
        return $result['insert_id'];
    }

    /**
     * Delete a comment
     */
    public function delete($id)
    {
        return db_execute("DELETE FROM comments WHERE id = ?", 'i', [$id]);
    }

    /**
     * Get all comments (for admin moderation)
     */
    public function getAll($status = null, $page = 1, $limit = 20)
    {
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT c.*, 
                       p.title as post_title, p.slug as post_slug,
                       u.username as author_name,
                       u.avatar as author_avatar
                FROM comments c
                LEFT JOIN posts p ON c.post_id = p.id
                LEFT JOIN users u ON c.user_id = u.id";
        
        $params = [];
        $types = '';
        
        if ($status) {
            $sql .= " WHERE c.status = ?";
            $params[] = $status;
            $types .= 's';
        }
        
        $sql .= " ORDER BY c.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        $types .= 'ii';
        
        return db_fetch_all($sql, $types, $params);
    }

    /**
     * Get count of comments (for pagination)
     */
    public function getCount($status = null)
    {
        $sql = "SELECT COUNT(*) as total FROM comments";
        $params = [];
        $types = '';
        
        if ($status) {
            $sql .= " WHERE status = ?";
            $params[] = $status;
            $types .= 's';
        }
        
        $result = db_fetch($sql, $types, $params);
        return $result ? $result['total'] : 0;
    }

    /**
     * Get comment by ID
     */
    public function getById($id)
    {
        $sql = "SELECT c.*, 
                       p.title as post_title, p.slug as post_slug,
                       u.username as author_name
                FROM comments c
                LEFT JOIN posts p ON c.post_id = p.id
                LEFT JOIN users u ON c.user_id = u.id
                WHERE c.id = ?";
        
        return db_fetch($sql, 'i', [$id]);
    }

    /**
     * Get comments by user ID
     */
    public function getByUserId($user_id, $limit = 10)
    {
        $sql = "SELECT c.*, 
                       p.title as post_title, p.slug as post_slug
                FROM comments c
                LEFT JOIN posts p ON c.post_id = p.id
                WHERE c.user_id = ? AND c.status = 'approved'
                ORDER BY c.created_at DESC
                LIMIT ?";
        
        return db_fetch_all($sql, 'ii', [$user_id, $limit]);
    }

    /**
     * Update comment status
     */
    public function updateStatus($id, $status)
    {
        $sql = "UPDATE comments SET status = ? WHERE id = ?";
        return db_execute($sql, 'si', [$status, $id]);
    }
}