<?php

/**
 * Archivo de configuración de la aplicación.
 *
 * Archivo de configuración de la aplicación.
 *
 * @copyright  2017 - Diego Soba.
 * @author     Diego Soba <didom900@gmail.com>
 * @version    2.0
 */

use Dotenv\Dotenv;
use Monolog\Logger;
use Ramsey\Uuid\Uuid;
use Illuminate\Log\Writer;
use contSoft\Finanzas\Clases\Acl;
use contSoft\Finanzas\Facades\Log;
use Illuminate\Config\Repository;
use Illuminate\Events\Dispatcher;
use contSoft\Finanzas\Facades\File;
use Illuminate\Validation\Factory;
use Monolog\Handler\StreamHandler;
use contSoft\Finanzas\Facades\Crypt;
use Illuminate\Container\Container;
use contSoft\Finanzas\Facades\Config;
use contSoft\Finanzas\Facades\LogApp;
use contSoft\Finanzas\Clases\Conexion;
use Illuminate\Filesystem\Filesystem;
use contSoft\Finanzas\Events\UserLogin;
use Illuminate\Translation\FileLoader;
use Illuminate\Translation\Translator;
use contSoft\Finanzas\Clases\CuentaUsuario;
use contSoft\Finanzas\Clases\RutaAplicacion;
use contSoft\Finanzas\Clases\CentralConsulta;
use contSoft\Finanzas\Clases\TokenAplicacion;
use contSoft\Finanzas\Clases\SesionAplicacion;
use contSoft\Finanzas\Clases\Conexion\Conector;
use contSoft\Finanzas\Clases\FormatoAplicacion;
use contSoft\Finanzas\Listeners\SendMessageToLog;
use contSoft\Finanzas\Clases\FormularioAplicacion;
use contSoft\Finanzas\Clases\ValidacionAplicacion;
use contSoft\Finanzas\Events\UserHasRecordDatabase;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Validation\DatabasePresenceVerifier;
use Illuminate\Support\Collection as Collection;
use Illuminate\Cache\CacheManager;
use Illuminate\Redis\RedisManager;

header("X-Frame-Options: SAMEORIGIN");
//
if (!isset($_SESSION)) {
    $lifetime=6000000;
    session_start();
    setcookie(session_name(), session_id(), time()+$lifetime);
}

ini_set('max_execution_time', 50000000);
ini_set('memory_limit', '2000000M');

/*
|--------------------------------------------------------------------------
| Cargado De Variables de Entorno
|--------------------------------------------------------------------------
*/

// Creamos una nueva instancia del Contenedor IoC
$container = new Illuminate\Container\Container;


$container->bind('dotenv', function ($container) {
    $dotenv = new Dotenv(__DIR__);
    return $dotenv->load();
});
$container->bind('dispatcher', Dispatcher::class);



$container->make('dotenv');
$dispatcher = $container->make('dispatcher');

/*$container['config'] = [
	'cache.default' => 'redis',
	'cache.stores.redis' => [
		'driver' => 'redis',
		'connection' => 'default'
	],
	'cache.prefix' => 'factcontSoft',
	'database.redis' => [
		'cluster' => false,
		'default' => [
			'host' => '127.0.0.1',
			'port' => 6379,
			'database' => 0,
		],
	]
];

$container['redis'] = new RedisManager('predis',$container['config']['database.redis']);
$cacheManager = new CacheManager($container);
$cache = $cacheManager->store();*/
/*
|--------------------------------------------------------------------------
| Sirve para ver errores en consola chrome (Solo descomentar en Local)
|--------------------------------------------------------------------------
*/
// $whoops = new \Whoops\Run;
// $whoops->pushHandler(new \Whoops\Handler\JsonResponseHandler);
// $whoops->register();

/*
|--------------------------------------------------------------------------
| Prueba Eloquent
|--------------------------------------------------------------------------
*/
// $capsule = new Capsule;
// $capsule->addConnection([
//     'driver'    => 'pgsql',
//     'host'      => 'localhost',
//     'database'  => 'factcontSoft',
//     'username'  => 'homestead',
//     'password'  => 'secret',
//     'charset'   => 'utf8',
//     'schema' => 'public',
//     'prefix'    => '',
// ]);
// $capsule->setEventDispatcher(new Dispatcher(new Container));
// // Make this Capsule instance available globally via static methods... (optional)
// $capsule->setAsGlobal();
// // Setup the Eloquent ORM... (optional; unless you've used setEventDispatcher())
// $capsule->bootEloquent();

/*
|--------------------------------------------------------------------------
| Eventos y Oyentes (Events & Listeners)
|--------------------------------------------------------------------------
*/

$dispatcher->listen([
    UserLogin::class,
    UserHasRecordDatabase::class,
], SendMessageToLog::class);



/*
|--------------------------------------------------------------------------
| Instacias de las Clases Comunes en toda la Aplicación.
|--------------------------------------------------------------------------
*/

$rutaAplicacion       = new RutaAplicacion(Config::get('app'));
$conexion             = new Conexion(Config::get('database'));
$conexion->conectar();
$conexionWs           = new Conexion(Config::get('webService'));
$conexionWs->conectar();
$conexionIca          = new Conexion(Config::get('ica'));
$conexionIca->conectar();
$sesionAplicacion     = new SesionAplicacion($conexion);
$centralConsulta      = new CentralConsulta($conexion, $rutaAplicacion);
$centralConsultaWS    = new CentralConsulta($conexionWs, $rutaAplicacion);
$centralConsultaIca   = new CentralConsulta($conexionIca, $rutaAplicacion);
$acl                  = new Acl($conexion);
$tokenAplicacion      = new TokenAplicacion();
$formatoAplicacion    = new FormatoAplicacion();
$formularioAplicacion = new FormularioAplicacion($centralConsulta, $rutaAplicacion, $formatoAplicacion);
$validacionAplicacion = new ValidacionAplicacion();
$cuentaUsuario        = new CuentaUsuario($conexion);

/*
|--------------------------------------------------------------------------
| Filesystem
|--------------------------------------------------------------------------
*/
$loader = new FileLoader(new Filesystem, __DIR__.'/lang');
$translator = new Translator($loader, 'es');
$validation = new Factory($translator, new Container);

/*
|--------------------------------------------------------------------------
| Loggers del Sistema
|--------------------------------------------------------------------------
*/

if (isset($_SESSION['cedula'])) {
    LogApp::useDailyFiles(__DIR__.'/storage/log/app/app.log');
    Log::useDailyFiles(__DIR__.'/storage/log/user/'.$_SESSION['cedula'].'.log');
}
