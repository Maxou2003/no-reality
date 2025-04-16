<?php

namespace App\Model\Entity;

use App\Model\Entity\Post;
use App\Model\Entity\Comment;

use JsonSerializable;


class CommentModal implements JsonSerializable
{
    public GroupPost $group_post;

    /**
     * @var GroupComment[]
     */
    public array $group_comments;

    public array $taggedUsers;

    public function jsonSerialize(): mixed
    {
        return [
            'group_post' => $this->group_post->jsonSerialize(),
            'group_comments' => array_map(
                fn(GroupComment $group_comments) => $group_comments->jsonSerialize(),
                $this->group_comments
            ),
            'taggedUsers' => $this->taggedUsers,
        ];
    }
}
