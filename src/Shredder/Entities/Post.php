<?php

namespace Juksy\Shredder\Entities;

class Post implements Plate
{
    public $post_id;

    public function getEndpoint()
    {
        return '/' . $this->post_id;
    }

    public function getMessages()
    {
        return [];
    }
}
