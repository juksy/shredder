<?php

namespace Juksy\Shredder\Entities;

class SchedulePost implements Plate
{
    public $page_id;

    public $message;

    public $link;

    public $name;

    public $description;

    public $scheduled_publish_time;

    public $caption;

    public function getEndpoint()
    {
        return '/' . $this->page_id;
    }

    public function getMessages()
    {
        return [
            'message' => $this->message,
            'link' => $this->link,
            'name' => $this->name,
            'description' => $this->description,
            'published' => false,
            'scheduled_publish_time' => "{$this->scheduled_publish_time}",
            'caption' => $this->caption,
        ];
    }
}
