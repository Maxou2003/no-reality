<?php

namespace App\Model\Entity;

use App\Model\Entity\Post;
use App\Model\Entity\Comment;

use JsonSerializable;


class GroupCommentModal implements JsonSerializable
{
    public GroupPost $group_post;

    /**
     * @var Comment[]
     */
    public array $comments;

    public array $taggedUsers;

    public function jsonSerialize(): mixed
    {
        return [
            'group_post' => $this->group_post->jsonSerialize(),
            'comments' => array_map(
                fn(Comment $comments) => $comments->jsonSerialize(),
                $this->comments
            ),
            'taggedUsers' => $this->taggedUsers,
        ];
    }
}
