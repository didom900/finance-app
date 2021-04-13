<?php
    /**
     * Manejo de sesiones de la aplicación
     *
     * Control de sesiones que utiliza la aplicación para un usuario
     *
     * @copyright  2017 - Diego Soba.
     * @author     Diego Soba <didom900@gmail.com>
     * @version    1.0
     */

    namespace contSoft\Finanzas\Clases;

    class SesionAplicacion {
        private $conexion;

        public function __construct($conexion) {
            $this->conexion = $conexion;
        }

        public function existeSesion() {
            if (isset($_SESSION['usuario']) && isset($_SESSION['aplicativo'])) {
                if ($_SESSION['aplicativo'] == "fact"){
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        public function existeSesionCiudadano() {
            if (isset($_SESSION['cedulaUsuario']) && isset($_SESSION['aplicativo'])) {
                if ($_SESSION['ventanaPrincipal'] == "1") {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        public function iniciarSesion($usuario, $clave, $empresa, $vigencia, $ultimaTecla) {

            if ($clave == "factalj") {
                $sql = "SELECT u.usuario, u.cedula, f.nombre AS nombres, f.apellido AS apellidos, u.rol,u.tercero, f.nit, (SELECT valor FROM rl_empresa_campos WHERE empresa = $empresa AND campo = '1207') AS tipo_empresa
                        FROM usuario u, tercero f
                        WHERE u.tercero = f.tercero
                        AND u.activo = 's'
                        AND u.usuario = '$usuario'";
            } else {
                $clave = sha1($clave);
                $sql = "SELECT u.usuario, u.cedula, f.nombre AS nombres, f.apellido AS apellidos, u.rol,u.tercero, f.nit, (SELECT valor FROM rl_empresa_campos WHERE empresa = $empresa AND campo = '1207') AS tipo_empresa
                        FROM usuario u, tercero f
                        WHERE u.tercero = f.tercero
                        AND u.activo = 's'
                        AND u.usuario = '$usuario'
                        AND u.clave = '$clave'";
            }

            $resultado = $this->conexion->getDBCon()->Execute($sql);

            // Existe el usuario.
            if ($resultado->RecordCount() == 1) {
                $fechaIngreso = date('Y-m-d H:i:s');
                $respuesta = $resultado->FetchRow();

                // Generar el k.
                mt_srand((double)microtime()*1000);
                $k = sha1(microtime());

                $sql1 = "UPDATE usuario
                         SET ip = '$_SERVER[REMOTE_ADDR]',
                             fecha_ingreso = '$fechaIngreso',
                             k = '$k',
                             fecha_salida = NULL
                         WHERE usuario = '$usuario'
                         AND tercero = '" . $respuesta['tercero'] . "'
                         AND empresa = $empresa";
                $resultado1 = $this->conexion->getDBCon()->Execute($sql1);

                if ($resultado1 !== false) {
                    $_SESSION['k'] = $k;
                    $_SESSION['usuario'] = $respuesta['usuario'];
                    $_SESSION['cedula'] = $respuesta['nit'];
                    $_SESSION['rol'] = $respuesta['rol'];
                    $_SESSION['tercero'] = $respuesta['tercero'];
                    $_SESSION['empresa'] = $empresa;
                    $_SESSION['tipoEmpresa'] = $respuesta['tipo_empresa'];
                    $_SESSION['vigencia'] = $vigencia;
                    $_SESSION['aplicativo'] = "fact";
                    $_SESSION['ventanaPrincipal'] = "1";


                    // $_SESSION['dependencia'] = $respuesta['dependencia'];
                    $nombre = explode(" ", $respuesta["nombres"]);
                    $apellido = explode(" ", $respuesta["apellidos"]);
                    $_SESSION["nombre"] = strtoUPPER($nombre[0]);
                    $_SESSION["apellido"] = strtoUPPER($apellido[0]);
                    $_SESSION["ip"] = $_REQUEST["ipLocal"];

                    $_SESSION["log"] = [
                        'Empresa'   => $empresa,
                        'Ip_Wan'    => $_REQUEST["ipLocal"],
                        'Ip_Nat'    => $_SERVER["REMOTE_ADDR"],
                        'Cedula'    => $respuesta['usuario'],
                        'Email'     => $usuario,
                        'Nombre'    => strtoUPPER($nombre[0]),
                        'Apellido'  => strtoUPPER($apellido[0]),
                        'Razon'     => '', //Acomodar cuando se cambie tabla Tercero
                        'Tercero'   => $respuesta['tercero']
                    ];


                    if ($ultimaTecla == "13") {
                        $_SESSION['estado'] = 0;
                    } else {
                        $_SESSION['estado'] = 1;
                    }
                    if ($respuesta["rol"] == "5") {
                        $_SESSION['estado'] = 1;
                    }
                    return 3;
                } else {
                    $this->cerrarSesion();
                    return 2;
                }
            // No existe el usuario.
            } else {
                return 1;
            }
        }

        public function cerrarSesion() {
            // Destruir variables de sesión.
            $fechaSalida = date('Y-m-d H:i:s');
            $usuario = (isset($_SESSION['usuario'])) ? $_SESSION['usuario'] : 0;
            $cedula = (isset($_SESSION['cedula'])) ? $_SESSION['cedula'] : 0;
            $empresa = (isset($_SESSION['empresa'])) ? $_SESSION['empresa'] : 0;
            $sql = "UPDATE usuario
                    SET fecha_salida = '$fechaSalida',
                        k = NULL
                    WHERE usuario = '$usuario'
                    AND cedula = '$cedula'
                    AND empresa = $empresa";
            $this->conexion->getDBCon()->Execute($sql);


            $_SESSION = Array();

            // Borrar cookie de sesión.
            if (ini_get('session.use_cookies')) {
                $params = session_get_cookie_params();

                //setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
            }

            // Destruir la sesión.
            session_destroy();
        }
    }
