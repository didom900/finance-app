<?php
/**
 * Cuenta de usuario
 *
 * Cuenta de usuario de la aplicación. Recuperar clave, cambio de clave, perfil del funcionario.
 *
 * @copyright  2017 - Diego Soba.
 * @author     Diego Soba <didom900@gmail.com>
 * @version    2.0
 */
namespace contSoft\Finanzas\Clases;

class CuentaUsuario {

    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    public function verificarCorreo($usuario, $empresa) {
        $sql = "SELECT u.usuario
                FROM usuario u
                WHERE u.usuario = '$usuario'
                AND u.empresa = $empresa
                AND u.activo = 's'";
        $resultado = $this->conexion->getDBCon()->Execute($sql);

        if ($resultado->RecordCount() == 1) {
            return 1;

        } else {
            return 0;

        }
    }

    public function UrlToken() {
        $cadena = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
        $longitudCadena = strlen($cadena);
        $url = '';
        $longitudurl = 10;

        for ($i = 1; $i <= $longitudurl; $i++) {
            $pos = rand(0, $longitudCadena - 1);
            $url .= substr($cadena, $pos, 1);
        }

        return md5($url);
    }

    public function comprobarToken($usuario, $sede, $token) {
        $sql = "SELECT u.usuario
                FROM usuario u
                WHERE u.usuario = '$usuario'
                AND u.empresa = $sede
                AND u.token = '$token'
                AND u.activo = 's'";
        $resultado = $this->conexion->getDBCon()->Execute($sql);

        if ($resultado->RecordCount() == 1) {
            return 1;

        } else {

            return 0;
        }
    }
}
