<?php

use contSoft\Finanzas\Clases\Auth\Autenticacion;
use contSoft\Finanzas\Clases\Auth\Conector;
/**
 * Iniciar sesión en la aplicación
 *
 * Inicio de sesión de un usuario en la aplicación
 *
 * @copyright  2017 - Diego Soba.
 * @author     Diego Soba <didom900@gmail.com> 
 * @version    v.1.0.1
 */

require_once '../../../bootstrap/autoload.php';

if (isset($_REQUEST['tipoSesion'])) {

    if($_REQUEST['tipoSesion'] == "1"){

        $autenticacion = new Autenticacion($_REQUEST['usuario'], $_REQUEST['clave'], $_REQUEST['empresas'], new Conector());

        if($autenticacion->autenticacion()) {
            //Usuario LDAP o Database Existe
            echo json_encode(Array('estado' => $sesionAplicacion->iniciarSesion($_REQUEST['usuario'], $_REQUEST['clave'], $_REQUEST['empresas'], $_REQUEST['vigencia'], $_REQUEST['ultimaTecla'])));
        } else {
            //Usuario LDAP o Database No Existe
            echo json_encode(Array('estado' => 1));
        }
    }
} else {
    if ($sesionAplicacion->existeSesion()) {
        echo json_encode(Array('estado' => 0));
    } else {
        echo json_encode(Array('estado' => 1));
    }
}
?>