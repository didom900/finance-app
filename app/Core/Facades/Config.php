<?php

namespace contSoft\Finanzas\Facades;

use Illuminate\Config\Repository;

/**
*
* Fachada para el acceso a la Clase Repository Config de laravel.
*
* @package  Config
* @author   Diego Soba <didom900@gmail.com>
* @version  v.1.0.0
*/
class Config
{
    /**
     * Instancia de la clase Illuminate\Config\Repository
     * @var object
     */
    protected static $instance;

    /**
     * [__callStatic description]
     * @param  [type] $method [description]
     * @param  [type] $args   [description]
     * @return Illuminate\Config\Repository Retorna la instancia de la clase Illuminate\Config\Repository de laravel
     */
    public static function __callStatic($method, $args)
    {
        if (! static::$instance) {
            static::$instance = new Repository(
                (new self)->loadConfig(__DIR__.'/../../../config')
            );
        }

        return static::$instance->{$method}(...$args);
    }

    /**
     * [loadConfig description]
     * @param  [type] $configPath [description]
     * @return [type]             [description]
     */
    protected function loadConfig($configPath)
    {
        $items = [];

        foreach (scandir($configPath) as $file) {
            if (substr($file, -4) === '.php') {
                $filename = str_replace('.php', '', $file);
                $items[$filename] = require $configPath . '/' . $file;
            }
        }

        return $items;
    }
}
