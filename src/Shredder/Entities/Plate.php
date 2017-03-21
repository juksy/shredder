<?php

namespace Juksy\Shredder\Entities;

interface Plate
{
    /**
     * @return string
     */
    public function getEndpoint();

    /**
     * @return array
     */
    public function getMessages();
}
