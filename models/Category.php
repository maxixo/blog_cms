<?php
require_once __DIR__ . '/../config/database.php';

class Category
{
    /**
     * Get all categories with post counts
     */
    public function getAllWithCounts()
    {
        $sql = "SELECT c.id, c.name, c.slug, c.description, COUNT(p.id) AS post_count
                FROM categories c
                LEFT JOIN posts p ON p.category_id = c.id
                    AND p.status = 'published'
                    AND (p.published_at IS NULL OR p.published_at <= NOW())
                GROUP BY c.id
                ORDER BY c.name ASC";

        return db_fetch_all($sql);
    }

    /**
     * Get all categories (without counts)
     */
    public function getAll()
    {
        $sql = "SELECT * FROM categories ORDER BY name ASC";
        return db_fetch_all($sql);
    }

    /**
     * Get category by ID
     */
    public function getById($id)
    {
        $sql = "SELECT * FROM categories WHERE id = ?";
        return db_fetch($sql, 'i', [$id]);
    }

    /**
     * Get category by slug
     */
    public function getBySlug($slug)
    {
        $sql = "SELECT * FROM categories WHERE slug = ?";
        return db_fetch($sql, 's', [$slug]);
    }

    /**
     * Get post count for a category
     */
    public function getPostCount($id)
    {
        $sql = "SELECT COUNT(*) as count FROM posts 
                WHERE category_id = ? AND status = 'published'";
        $result = db_fetch($sql, 'i', [$id]);
        return $result ? $result['count'] : 0;
    }

    /**
     * Create a new category
     */
    public function create($data)
    {
        $slug = $this->generateUniqueSlug($data['name']);
        
        $sql = "INSERT INTO categories (name, slug, description) VALUES (?, ?, ?)";
        $params = [
            $data['name'],
            $slug,
            $data['description'] ?? ''
        ];
        
        $result = db_execute($sql, 'sss', $params);
        return $result['insert_id'];
    }

    /**
     * Update a category
     */
    public function update($id, $data)
    {
        $sql = "UPDATE categories SET name = ?, description = ?";
        $params = [$data['name'], $data['description'] ?? ''];
        $types = 'ss';

        // Update slug only if name changed
        if (isset($data['name']) && $this->shouldUpdateSlug($id, $data['name'])) {
            $slug = $this->generateUniqueSlug($data['name']);
            $sql .= ", slug = ?";
            $params[] = $slug;
            $types .= 's';
        }

        $sql .= " WHERE id = ?";
        $params[] = $id;
        $types .= 'i';

        return db_execute($sql, $types, $params);
    }

    /**
     * Delete a category
     */
    public function delete($id)
    {
        return db_execute("DELETE FROM categories WHERE id = ?", 'i', [$id]);
    }

    /**
     * Check if slug exists
     */
    public function slugExists($slug, $excludeId = null)
    {
        $sql = "SELECT id FROM categories WHERE slug = ?";
        $params = [$slug];
        $types = 's';

        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
            $types .= 'i';
        }

        $result = db_fetch($sql, $types, $params);
        return $result !== null;
    }

    /**
     * Generate unique slug for category
     */
    private function generateUniqueSlug($name)
    {
        require_once __DIR__ . '/../includes/helpers.php';
        
        $slug = create_slug($name);
        $original_slug = $slug;
        $counter = 1;

        while ($this->slugExists($slug)) {
            $slug = $original_slug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Check if slug should be updated (name changed)
     */
    private function shouldUpdateSlug($id, $newName)
    {
        $category = $this->getById($id);
        return $category && $category['name'] !== $newName;
    }
}
