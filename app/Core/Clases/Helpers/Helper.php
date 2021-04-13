<?php

namespace contSoft\Finanzas\Clases\Helpers;

use Carbon\Carbon;

/**
 * Class Helper
 * Funciones de ayuda para el sistema.
 * @package contSoft\Finanzas\Clases\Helpers
 */
class Helper
{
    /**
     * Reemplaza las comas por punto para guardar en BD.
     * @param  string $value [description]
     * @return [type]        [description]
     */
    public static function formatearMoneda($value = '')
    {
        return str_replace(',', '.', str_replace('.', '', $value));
    }

	/**
	 * Formatea un número con los millares agrupados
	 * @param  string $valor [description]
	 * @param int $decimal
	 * @param string $currency
	 * @return string [type]          [description]
	 * @internal param $ [type] $decimal [description]
	 */
    public static function monedaColombia($valor = '', $decimal = 0,$currency='')
    {
        return $currency .number_format($valor, $decimal, ',', '.');
    }

	/**
	 * Formatea un numero sin millares.
	 * @param string $value
	 * @param int $decimal
	 * @param string $currency
	 * @return string [type]          [description]
	 * @internal param string $valor [description]
	 * @internal param $ [type] $decimal [description]
	 */
    public static function monedaCop($value = '', $decimal = 0, $currency='')
    {
        $valor = str_replace(',', '.', str_replace('.', '', $value));
        return $currency .number_format($valor, $decimal, '.', '');
    }

    /**
     * Devuelve el año en curso.
     * @return string
     */
    public static function vigencia()
    {
        $now = Carbon::now();
        return $now->year;
    }

	/**
	 * Agrega los dias indicados para generar una nueva fecha
	 * a partir de la actual.
	 * @param $dias
	 * @return string
	 */
    public static function fechaPago($dias)
    {
        $now = Carbon::now();
        return $now->addDay($dias)->toDateString();
    }

    /**
     * Devuelve la fecha actual.
     * Formato: 2015-05-07
     * @return string
     */
    public static function fechaActual()
    {
        $now = Carbon::now();
        return $now->toDateString();
    }

     /**
     * Devuelve la fecha actual del sistema.
     * Formato: 2015-05-07 13:08:50.000000
     * @return mixed
     */
    public static function fechaSistema()
    {
        $fecha = Carbon::now();
        return $fecha->now();
    }

	/**
	 * Trasnforma los array en un objeto.
	 * @param array $array
	 * @return mixed
	 */
	public static function arrayToObject(array $array)
    {
        return json_decode(json_encode($array));
    }
}
