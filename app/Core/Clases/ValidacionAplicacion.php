<?php
/**
 * Validaciones de la aplicación
 *
 * Validaciones de formularios que maneja la aplicación
 *
 * @copyright  2017 - Diego Soba.
 * @author     Diego Soba <didom900@gmail.com>
 * @version    2.0
 */

namespace contSoft\Finanzas\Clases;

class ValidacionAplicacion
{
    public function __construct()
    {
        // To do
    }

    public function validaRequerido($valor)
    {
        if (trim($valor) == '') {
            return false;
        } else {
            return true;
        }
    }

    public function validaEntero($valor, $opciones = null)
    {
        if (filter_var($valor, FILTER_VALIDATE_INT, $opciones) === false) {
            return false;
        } else {
            return true;
        }
    }

    public function sanarDatos($valor)
    {
        $datos;

        foreach ($valor as $key => $value) {
            if (!is_array($value)) {
                $datos[$key] = htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
            } else {
                foreach ($value as $key1 => $value1) {
                    $datos[$key][$key1] = htmlspecialchars(trim($value1), ENT_QUOTES, 'UTF-8');
                }
            }
        }

        return $datos;
    }

    public function sanarDato($valor)
    {
        $valor = trim($valor);
        $valor = htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');

        return $valor;
    }
}
