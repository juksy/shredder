<?php

namespace Juksy\Shredder\Entities;

class SchedulePost implements Plate
{
    public $page_id;

    public $message;

    public $link;

    public $name;

    public $description;

    public $published;

    public $scheduled_publish_time;

    public $caption;

    public function getEndpoint()
    {
        return '/' . $this->page_id;
    }

    public function getMessages()
    {
        return [
            'description' => $this->description
        ];
    }

}
