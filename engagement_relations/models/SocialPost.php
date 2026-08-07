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
        $nameSql = "COALESCE(e.full_name, u.full_name, u.username, p.employee_id, p.user_id, 'hr_engagement') AS author_name";

        $sql = "SELECT p.*, $nameSql,
                SUM(CASE WHEN r.type = 'like' THEN 1 ELSE 0 END) AS like_count,
                SUM(CASE WHEN r.type = 'heart' THEN 1 ELSE 0 END) AS heart_count,
                SUM(CASE WHEN r.type = 'wow' THEN 1 ELSE 0 END) AS wow_count
                FROM eer_social_posts p
                LEFT JOIN employees e ON p.employee_id = e.employee_id AND p.$typeCol = 'employee'
                LEFT JOIN users u ON p.$typeCol = 'user' AND (p.user_id = u.id OR p.employee_id = u.id)
                LEFT JOIN eer_reactions r ON p.eer_social_post_id = r.post_id
                WHERE p.item_type = 'post'
                GROUP BY p.eer_social_post_id
                ORDER BY p.created_at DESC";

        return $this->execute($sql)->fetchAll();
    }

    public function createPost($author_id, $content, $author_type = 'employee')
    {
        $typeCol = $this->getAuthorTypeColumn();
        if ($author_type === 'user') {
            $sql = "INSERT INTO eer_social_posts (user_id, content, item_type, created_at, $typeCol) VALUES (:user_id, :content, 'post', NOW(), :author_type)";
            $params = ['user_id' => $author_id, 'content' => $content, 'author_type' => $author_type];
        } else {
            $sql = "INSERT INTO eer_social_posts (employee_id, content, item_type, created_at, $typeCol) VALUES (:employee_id, :content, 'post', NOW(), :author_type)";
            $params = ['employee_id' => $author_id, 'content' => $content, 'author_type' => $author_type];
        }
        $this->execute($sql, $params);
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
