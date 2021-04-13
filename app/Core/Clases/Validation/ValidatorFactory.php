<?php
/**
 *
 * @copyright  2017 - Diego Soba.
 * @author     Diego Soba <didom900@gmail.com>
 * @version    v1.0.0
 */
namespace contSoft\Finanzas\Clases\Validation;

use Illuminate\Validation;
use Illuminate\Translation;
use Illuminate\Filesystem\Filesystem;

class ValidatorFactory
{
    /**
     * [$factory description]
     * @var [type]
     */
    private $factory;

    /**
     * [__construct description]
     */
    public function __construct()
    {
        $this->factory = new Validation\Factory(
            $this->loadTranslator()
        );
    }

    /**
     * [loadTranslator description]
     * @return [type] [description]
     */
    protected function loadTranslator()
    {
        $filesystem = new Filesystem();
        $loader = new Translation\FileLoader($filesystem, dirname(dirname(__FILE__)) . '/Validation/lang');
            $loader->addNamespace(
                'lang',
                dirname(dirname(__FILE__)) . '/Validation/lang'
            );
        $loader->load('en', 'validation', 'lang');
        return new Translation\Translator($loader, 'en');
    }

    /**
     * [__call description]
     * @param  [type] $method [description]
     * @param  [type] $args   [description]
     * @return [type]         [description]
     */
    public function __call($method, $args)
    {
        return call_user_func_array(
            [$this->factory, $method],
            $args
        );
    }
}
