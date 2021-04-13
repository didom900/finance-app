<?php
/**
 * Rutas de la aplicación
 *
 * Rutas que utiliza la aplicación para poder llegar a los diferentes
 * recursos que se vayan a utilizar (PHP, CSS, JS, Imagenes, Otros)
 *
 * @copyright  2017 - Diego Soba.
 * @author     Diego Soba <didom900@gmail.com>
 * @version    3.0
 */
namespace contSoft\Finanzas\Clases;

class RutaAplicacion
{
    private $app = [];
    private $rutaDocumento = '';
    private $rutaAbsoluta = '';
    private $rutaRelativa = '';

    public function __construct($app)
    {
        $this->app = $app;
        $this->rutaDocumentoAbsoluta = $this->app['PROTOCOLO'].'://'.$_SERVER['SERVER_NAME'].':'.$this->app['PUERTO'].'/documents/';
        $this->rutaDocumentoRelativa =  __DIR__ . $this->app['DOCUMENTO'] . '/';
        $this->rutaRelativa = $_SERVER['DOCUMENT_ROOT'] . $this->app['RAIZ'];
        $this->rutaAbsoluta = $this->app['PROTOCOLO'] . '://' . $_SERVER['SERVER_NAME'] . ':' . $this->app['PUERTO'] . $this->app['RAIZ'];
    }

    public function __get($variable)
    {
        return $this->$variable;
    }

    public function __set($name, $value)
    {
        $this->$name = $value;
    }
}
