<?php
/**
 *
 * @copyright  2017 - Diego Soba.
 * @author     Diego Soba <didom900@gmail.com>
 * @version    v1.0.0
 */

namespace contSoft\Finanzas\Clases\Auth;

use InvalidArgumentException;
use contSoft\Finanzas\Clases\Auth\Conector;
use contSoft\Finanzas\Facades\Config;

class Autenticacion
{
    protected $autenticacion;
    private $usuario;
    private $clave;
    private $empresa;

    /**
     * @param string $usuario Usuario
     * @param string $clave Password del Usuario
     * @param int $empresa Identificador de la Empresa.
     */
    public function __construct($usuario, $clave, $empresa, Conector $conector)
    {
        $this->usuario = $usuario;
        $this->clave   = $clave;
        $this->empresa = $empresa;
        $this->autenticacion = $conector;
    }

    /**
     * Devuelve si un usuario ldap o de la base de datos existe.
     * @return bool
     */
    public function autenticacion()
    {
        $auth =$this->autenticacion->crearConector(Config::all());
        return $auth->authUser($this->usuario, $this->clave, $this->empresa);
    }
}
