<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Variables de configuración de la aplicacion.
    | -------------------------------------------------------------------------
    |
    | Aquí puede especificar las variables de entorno de la aplicacion.
    |
    */
    'PROTOCOLO' => env('APP_PROTOCOLO', 'http'),
    'RAIZ'      => env('APP_RAIZ', '/'),
    'DOCUMENTO' => env('APP_DOCS', ''),
    'PUERTO'    => env('APP_PUERTO', '80'),
    'APP_URL'    => env('APP_URL', 'https://finanzas.contSoft.com.co/'),
    'API_URL'    => env('API_URL', 'https://api.contSoft.com.co/')
];
