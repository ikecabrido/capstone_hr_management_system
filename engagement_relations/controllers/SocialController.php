<?php
namespace App\Controllers;

use App\Models\SocialPost;
use App\Models\Comment;
use App\Models\Reaction;

class SocialController
{
    private $post;
    private $comment;
    private $reaction;

    public function __construct()
    {
        $this->post = new SocialPost();
        $this->comment = new Comment();
        $this->reaction = new Reaction();
    }

    public function getPosts()
    {
        $posts = $this->post->getPosts();
        foreach ($posts as &$p) {
            $p['comments'] = $this->comment->getComments($p['eer_social_post_id']);
        }
        return $posts;
    }

    public function createPost($author_id, $content, $author_type = 'employee')
    {
        return $this->post->createPost($author_id, $content, $author_type);
    }

    public function addReaction($post_id, $employee_id, $user_id, $reaction_type)
    {
        return $this->reaction->addReaction($post_id, $employee_id, $user_id, $reaction_type);
    }

    public function addComment($post_id, $author_id, $comment, $author_type = 'employee')
    {
        return $this->comment->addComment($post_id, $author_id, $comment, $author_type);
    }

    public function deletePost($post_id)
    {
        $this->post->deletePost($post_id);
    }

    public function editPost($post_id, $content)
    {
        $this->post->editPost($post_id, $content);
    }

    public function getPostAnalytics($postId)
    {
        return $this->post->getAnalytics($postId);
    }
}
