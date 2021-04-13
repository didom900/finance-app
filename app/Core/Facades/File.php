<?php
/**
 * Clase File.
 *
 * Fachada para el acceso a la Clase Illuminate\Filesystem\Filesystem de laravel.
 *
 * @copyright  2017 - Diego Soba.
 * @author     Diego Soba <didom900@gmail.com>
 * @version    v.1.0.0
 */
namespace contSoft\Finanzas\Facades;

use Illuminate\Filesystem\Filesystem;

/**
 * Class File
 * @package contSoft\Finanzas\Facades
 */
class File
{
	/**
	 * @param $name
	 * @param $args
	 * @return mixed
	 */
	public static function __callStatic($name, $args)
    {
        $self = new Filesystem;

        return $self->{$name}(...$args);
    }
}
