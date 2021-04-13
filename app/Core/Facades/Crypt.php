<?php
/**
 * Clase Crypt.
 *
 * Fachada para el acceso a la Clase use Illuminate\Encryption\Encrypter de laravel.
 *
 * @copyright  2017 - Diego Soba.
 * @author     Diego Soba <didom900@gmail.com>
 * @version    v.1.0.0
 */
namespace contSoft\Finanzas\Facades;

use Illuminate\Encryption\Encrypter;

/**
 * Facade para la clase Crypt de laravel
 * @package contSoft\Finanzas\Facades
 */
class Crypt
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
            static::$instance = new Encrypter((new self)->keyGenerate(), 'AES-256-CBC');
        }
        return static::$instance->{$name}(...$args);
    }

	/**
	 * @return string
	 */
	protected function keyGenerate()
    {
        $llave = Encrypter::generateKey('AES-256-CBC');
        return $llave;
    }
}
