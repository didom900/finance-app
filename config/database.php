<?php

use contSoft\Finanzas\Facades\Crypt;

return [

    /*
    |--------------------------------------------------------------------------
    | Variables de conexión de base de datos.
    | -------------------------------------------------------------------------
    |
    | Aquí puede especificar las variables de conexion a la base de datos
    | predeterminada.
    |
    */

    'DB_CONN' => Crypt::encrypt(env('DB_CONN','postgres')),
    'DB_HOST' => env('DB_HOST','localhost'),
    'DB_PORT' => env('DB_PORT','5432'),
    'DB_NAME' => env('DB_NAME','hcorozal'),
    'DB_USER' => env('DB_USER','postgres'),
    'DB_PASS' => env('DB_PASS','testing'),

];