<?php
/**
 * Clase Crypt.
 *
 * Fachada para el acceso a la Clase Cache.
 *
 * @copyright  2017 - Diego Soba.
 * @author     Diego Soba <didom900@gmail.com>
 * @version    v.1.0.0
 */
namespace contSoft\Finanzas\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Cache
 * @package contSoft\Finanzas\Facades;
 */
class Cache extends Facade
{
	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 *
	 * @throws \RuntimeException
	 */
	protected static function getFacadeAccessor()
	{
		return 'Cache';
	}
}