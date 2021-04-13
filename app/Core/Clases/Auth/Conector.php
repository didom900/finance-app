<?php
/**
 *
 * @copyright  2017 - Diego Soba.
 * @author     Diego Soba <didom900@gmail.com>
 * @version    v1.0.0
 */

namespace contSoft\Finanzas\Clases\Auth;

use InvalidArgumentException;
use Exception;
use contSoft\Finanzas\Clases\Auth\LdapConnector;
use contSoft\Finanzas\Clases\Auth\DatabaseConnector;

/**
 * Class Conector
 * @package contSoft\Finanzas\Clases\Auth
 */
class Conector
{
    /**
     * Crea una instancia de Conexion basado en la configuracion.
     *
     * @param  array $config
     * @return \contSoft\Finanzas\Clases\Auth\DatabaseConnector
     *
     * @throws \Exception
     */
    public function crearConector(array $config)
    {
        if ($config['auth']['driver'] === '') {
            throw new InvalidArgumentException('Se debe Especificar un Driver de Autenticación');
        }

        switch ($config['auth']['driver']) {
            case 'db':
                return new DatabaseConnector($config['database']);
            case 'ldap':
                return new LdapConnector($config['ldap']);
        }

        throw new Exception('Driver ['. $config['auth']['driver'].'] No Soportado');
    }
}
