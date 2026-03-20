<?php
namespace App\Controllers;

use App\Models\SocialPost;
use App\Models\Comment;

class SocialController
{
    private $post;
    private $comment;

    public function __construct()
    {
        $this->post = new SocialPost();
        $this->comment = new Comment();
    }

    public function getPosts()
    {
        $posts = $this->post->getPosts();
        foreach ($posts as &$p) {
            $p['comments'] = $this->comment->getComments($p['id']);
        }
        return $posts;
    }

    public function createPost($employee_id, $content)
    {
        return $this->post->createPost($employee_id, $content);
    }

    public function addComment($post_id, $employee_id, $comment)
    {
        return $this->comment->addComment($post_id, $employee_id, $comment);
    }
}
