<?php
/**
 * Clase Log.
 *
 * Fachada para el acceso a la Clase Illuminate\Log\Writer de laravel.
 *
 * @copyright  2017 - Diego Soba.
 * @author     Diego Soba <didom900@gmail.com>
 * @version    v.1.0.0
 */
namespace contSoft\Finanzas\Facades;

use Monolog\Logger;
use Illuminate\Log\Writer;

/**
 * Class Log
 * @package contSoft\Finanzas\Facades
 */
class Log
{
    public static $instance;


	/**
	 * @param $name
	 * @param $args
	 * @return mixed
	 */
	public static function __callStatic($name, $args)
    {
        if (! static::$instance) {
            static::$instance = new Writer(new Logger('Log Usuario'));
        }

        return static::$instance->{$name}(...$args);
    }
}
