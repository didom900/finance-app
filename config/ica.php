<?php

use contSoft\Finanzas\Facades\Crypt;

return [

    /*
    |--------------------------------------------------------------------------
    | Variables de conexión para modulo ICA
    | -------------------------------------------------------------------------
    |
    | Aquí puede especificar las variables de conexion a la base de datos
    | predeterminada.
    |
    */

    'DB_CONN' => Crypt::encrypt(env('DB_CONN_ICA','postgres')),
    'DB_HOST' => env('DB_HOST_ICA','localhost'),
    'DB_PORT' => env('DB_PORT_ICA','5432'),
    'DB_NAME' => env('DB_NAME_ICA','forge'),
    'DB_USER' => env('DB_USER_ICA','forge'),
    'DB_PASS' => env('DB_PASS_ICA',''),

];