<?php
/**
 * Token CSRF
 *
 * Implementacion de Token en formularios.
 *
 * @copyright  2017 - Diego Soba.
 * @author     Diego Soba <didom900@gmail.com>
 * @version    2.0
 */

namespace contSoft\Finanzas\Clases;

class TokenAplicacion
{

    /**
     * Opciones Para Bcrypt
     *
     * @var array
     */
    public static $opciones = [
        'cost' => 12,
    ];


    public function __construct()
    {
    }

    /**
     * Generar CSRF token
     *
     * @author  Diego Soba <didom900@gmail.com>
     * @param   string $formulario
     * @return  string
     */
    public static function generateToken($formulario)
    {
        if (!session_id()) {
            session_start();
        }
        $sessionId = session_id();
        return password_hash($formulario.$sessionId, PASSWORD_BCRYPT, self::$opciones);
    }

    /**
     * Verificar CSRF token
     *
     * @author  Diego Soba <didom900@gmail.com>
     * @param   string $formulario
     * @return  boolean
     */
    public static function checkToken($token, $formulario)
    {
        return $token === self::generateToken($formulario);
    }
}
