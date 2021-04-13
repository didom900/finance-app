<?php
    /**
     * Cerrar sesión en la aplicación
     *
     * Cierre de sesión de un usuario en la aplicación
     *
     * @copyright  2017 - Diego Soba.
     * @author     Diego Soba <didom900@gmail.com>
     * @version    1.0
     */
    
    require_once '../../../bootstrap/autoload.php';

    $sesionAplicacion->cerrarSesion();
    if (isset($_REQUEST['tipoSesion'])) {
        header("Location: $rutaAplicacion->rutaAbsoluta"."ciudadano.php");
    } else {
        header("Location: $rutaAplicacion->rutaAbsoluta");
    }
?>