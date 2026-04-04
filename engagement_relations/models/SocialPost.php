<?php
namespace App\Models;

class SocialPost extends BaseModel
{
    private function getAuthorTypeColumn()
    {
        $column = $this->execute("SHOW COLUMNS FROM eer_social_posts LIKE 'author_type'")->fetch();
        return $column ? 'author_type' : 'user_type';
    }

    public function getPosts()
    {
        $typeCol = $this->getAuthorTypeColumn();
        $nameSql = "COALESCE(e.full_name, u.full_name, u.username, p.employee_id, 'hr_engagement') AS author_name";

        $sql = "SELECT p.*, $nameSql,
                SUM(CASE WHEN r.type = 'like' THEN 1 ELSE 0 END) AS like_count,
                SUM(CASE WHEN r.type = 'heart' THEN 1 ELSE 0 END) AS heart_count,
                SUM(CASE WHEN r.type = 'wow' THEN 1 ELSE 0 END) AS wow_count
                FROM eer_social_posts p
                LEFT JOIN employees e ON p.employee_id = e.employee_id AND p.$typeCol = 'employee'
                LEFT JOIN users u ON p.$typeCol = 'user' AND (CAST(p.employee_id AS UNSIGNED) = u.user_id OR p.employee_id = u.username)
                LEFT JOIN eer_reactions r ON p.eer_social_post_id = r.post_id
                GROUP BY p.eer_social_post_id
                ORDER BY p.created_at DESC";

        return $this->execute($sql)->fetchAll();
    }

    public function createPost($author_id, $content, $author_type = 'employee')
    {
        $typeCol = $this->getAuthorTypeColumn();
        $sql = "INSERT INTO eer_social_posts (employee_id, content, created_at, $typeCol) VALUES (:employee_id, :content, NOW(), :author_type)";
        $this->execute($sql, ['employee_id' => $author_id, 'content' => $content, 'author_type' => $author_type]);
        return $this->db->lastInsertId();
    }

    public function deletePost($post_id)
    {
        $sql = 'DELETE FROM eer_social_posts WHERE eer_social_post_id = :post_id';
        $this->execute($sql, ['post_id' => $post_id]);
    }

    public function editPost($post_id, $content)
    {
        $sql = 'UPDATE eer_social_posts SET content = :content WHERE eer_social_post_id = :post_id';
        $this->execute($sql, ['post_id' => $post_id, 'content' => $content]);
    }
}
