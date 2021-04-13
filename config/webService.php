<?php

use contSoft\Finanzas\Facades\Crypt;

return [

    /*
    |--------------------------------------------------------------------------
    | Variables de conexión para los Webservices
    | -------------------------------------------------------------------------
    |
    | Aquí puede especificar las variables de conexion a la base de datos
    | predeterminada.
    |
    */

    'DB_CONN' => Crypt::encrypt(env('DB_CONN_WS','postgres')),
    'DB_HOST' => env('DB_HOST_WS','localhost'),
    'DB_PORT' => env('DB_PORT_WS','5432'),
    'DB_NAME' => env('DB_NAME_WS','forge'),
    'DB_USER' => env('DB_USER_WS','forge'),
    'DB_PASS' => env('DB_PASS_WS',''),

];