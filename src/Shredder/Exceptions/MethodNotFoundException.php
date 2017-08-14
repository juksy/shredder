<?php

namespace Juksy\Shredder\Exceptions;

use Exception;

class MethodNotFoundException extends Exception
{
	/**
     * The requested method.
     *
     * @var string
     */
    protected $method;

    /**
     * Create a new method not found exception.
     *
     * @param string      $class
     * @param string      $method
     * @param string|null $message
     *
     * @return void
     */
    public function __construct($class, $method, $message = null)
    {
        $this->method = $method;

        if (!$message) {
            $message = "The method '$method' was not found on the presenter class '$class'.";
        }

        parent::__construct($class, $message);
    }

    /**
     * Get the requested method.
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }
}
