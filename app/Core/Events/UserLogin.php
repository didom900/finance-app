<?php

namespace contSoft\Finanzas\Events;

class UserLogin
{
    protected $message;

    public function __construct($message)
    {
        $this->message = $message;
    }

    public function getMessage()
    {
        return $this->message;
    }
}
