<?php

namespace App\Model\Entity;

use App\Model\Entity\Post;
use App\Model\Entity\Comment;

use JsonSerializable;


class ModalPost implements JsonSerializable
{
    public Post $post;

    /**
     * @var Comment[]
     */
    public array $comments;

    public array $taggedUsers;

    public function jsonSerialize(): mixed
    {
        return [
            'post' => $this->post->jsonSerialize(),
            'comments' => array_map(
                fn(Comment $comment) => $comment->jsonSerialize(),
                $this->comments
            ),
            'taggedUsers' => $this->taggedUsers,
        ];
    }
}
