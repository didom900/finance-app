<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Mail Driver
    | -------------------------------------------------------------------------
    |
    | Aquí puede especificar las variables de configuracion para el envio
    | de correos electronicos en la aplicacion.
    */

    'driver'    => env('MAIL_DRIVER', 'smtp'),
    'host'      => env('MAIL_HOST','mg.contSoft.com.co'),
    'port'      => env('MAIL_PORT',587),
    'username'  => env('MAIL_USERNAME','80'),
    'password'  => env('MAIL_PASSWORD','80'),
];