<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Variables de Configuracion Para Autenticacion LDAP.
    | -------------------------------------------------------------------------
    |
    | Aquí puede especificar las variables para protocolo ldap.
    |
    */

    'LDAP_HOST'   => env('LDAP_HOST', '127.0.0.1'),
    'LDAP_PORT'   => env('LDAP_PORT', '389'),
    'LDAP_DOMAIN' => env('LDAP_DOMAIN', 'CN=Users,DC=domain,DC=com,DC=lan'),
    'LDAP_ADMIN'  => env('LDAP_ADMIN', 'my_user'),
    'LDAP_PASS'   => env('LDAP_PASS', 'my_pass')
];
