<?php

namespace contSoft\Finanzas\Events;

class UserHasRecordDatabase
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
