<?php

namespace contSoft\Finanzas\Listeners;

use contSoft\Finanzas\Facades\Log;
use contSoft\Finanzas\Events\UserLogin;
use contSoft\Finanzas\Events\UserHasRecordDatabase;

class SendMessageToLog
{
    /**
     * [handle description]
     * @param  contSoft\Finanzas\Events $event
     */
    public function handle($event)
    {
        if ($event instanceof UserHasRecordDatabase) {
            Log::info($event->getMessage(), []);
        } elseif ($event instanceof UserLogin) {
            Log::info($event->getMessage(), []);
        }
    }
}
