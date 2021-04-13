<?php
    /**
     * Lista de control de acceso (ACL) de la aplicación
     *
     * Control de acceso que utiliza la aplicación para un usuario.
     *
     * @copyright  2017 - Diego Soba.
     * @author     Diego Soba <didom900@gmail.com>
     * @version    1.0
     */
    namespace contSoft\Finanzas\Clases;
    class Acl {

        private $conexion;
        private $usuario;
        private $usuarioRol;
        private $permiso;

        public function __construct($conexion) {
            $this->conexion = $conexion;
            $this->usuarioRol = Array();
            $this->permiso = Array();

            if (isset($_SESSION['usuario'])) {
                $this->usuario = $_SESSION['usuario'];

            } else {
                $this->usuario = 0;
            }

            $this->usuarioRol = $this->usuarioRol();
            $this->compilarAcl();

        }

        private function usuarioRol() {
            $datos = Array();

            $sql = "SELECT ur.rol
                    FROM usuario u, usuario_rol ur
                    WHERE u.usuario = ur.usuario
                    AND u.usuario = '$this->usuario'";
            $resultado = $this->conexion->getDBCon()->Execute($sql);

            $respuesta = $resultado->GetArray();

            foreach ($respuesta as $fila) {
                $datos[] = $fila['rol'];
            }

            return $datos;
        }

        private function compilarAcl() {

            if (sizeof($this->usuarioRol) > 0) {

                // Permisos del rol.
                $this->permiso = array_merge($this->permiso, $this->permisoRol($this->usuarioRol));

            }

            // Permisos del usuario individual.
            $this->permiso = array_merge($this->permiso, $this->permisoUsuario($this->usuario));

        }

        private function permisoRol($usuarioRol) {
            $datos = Array();

            if (is_array($usuarioRol)) {
                $sql = "SELECT p.permiso, p.llave, pr.estado, p.nombre
                        FROM permiso p, permiso_rol pr
                        WHERE p.permiso = pr.permiso
                        AND pr.rol IN (" . implode(",", $usuarioRol) . ")";

            } else {
                $sql = "SELECT p.permiso, p.llave, pr.estado, p.nombre
                        FROM permiso p, permiso_rol pr
                        WHERE p.permiso = pr.permiso
                        AND pr.rol = $usuarioRol";
            }

            $resultado = $this->conexion->getDBCon()->Execute($sql);

            $respuesta = $resultado->GetArray();

            foreach ($respuesta as $fila) {
                $datos[$fila['llave']] = Array('permiso' => $fila['permiso'],
                                               'llave_permiso' => $fila['llave'],
                                               'nombre_permiso' => $fila['nombre'],
                                               'estado' => $fila['estado'],
                                               'heredado' => 1
                                              );
            }

            return $datos;
        }

        private function permisoUsuario($usuario) {
            $datos = Array();

            $sql = "SELECT p.permiso, p.llave, up.estado, p.nombre
                    FROM permiso p, usuario_permiso up
                    WHERE p.permiso = up.permiso
                    AND up.usuario = '$usuario'";
            $resultado = $this->conexion->getDBCon()->Execute($sql);

            $respuesta = $resultado->GetArray();

            foreach ($respuesta as $fila) {
                $datos[$fila['llave']] = Array('permiso' => $fila['permiso'],
                                               'llave_permiso' => $fila['llave'],
                                               'nombre_permiso' => $fila['nombre'],
                                               'estado' => $fila['estado'],
                                               'heredado' => 0
                                              );
            }

            return $datos;
        }

        public function permiso() {
            $permiso = Array();

            foreach ($this->permiso as $clave => $valor) {

                // Solo activos.
                if ($valor['estado'] == 'a') {
                    $permiso[$clave] = $valor;
                }

            }

            return $permiso;
        }

    }


?>