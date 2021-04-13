<?php
/**
 * Interfaz para los conectores de Autenticacion.
 *
 * @copyright  2017 - Diego Soba.
 * @author     Diego Soba <didom900@gmail.com>
 * @version    v1.0.0
 */

namespace contSoft\Finanzas\Clases\Auth;

interface ConectorInterface
{
    /**
     *
     *
     */
    public function conectar();

    /**
     * Devuelve true si el usuario existe o false en caso
     * contrario.
     *
     * @param  string $usuario
     * @param  string $password
     * @param  int $empresa
     * @return bool
     */
    public function authUser($usuario, $password, $empresa);
}
