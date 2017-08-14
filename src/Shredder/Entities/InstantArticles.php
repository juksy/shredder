<?php

namespace Juksy\Shredder\Entities;

class InstantArticles implements Plate
{
    public $page_id;

    public $html_source;

    public $published = true;

    public function getEndpoint()
    {
        return '/' . $this->page_id;
    }

    public function getMessages()
    {
        return [
            'html_source' => $this->html_source,
            'published' => $this->published,
        ];
    }
}
