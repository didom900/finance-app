<?php
/**
 * Sami API Documentor
 *
 * Creacion de la Documentacion de la Aplicacion.
 *
 * @copyright  2017 - Diego Soba.
 * @author     Diego Soba <didom900@gmail.com>
 * @version    v1.0.0
 */

use Sami\Sami;
use Sami\Version\GitVersionCollection;
use Symfony\Component\Finder\Finder;

$iterator = Finder::create()
    ->files()
    ->name('*.php')
    ->exclude('Resources')
    ->exclude('Tests')
    ->in($dir = 'app/Core')
;
// $versions = GitVersionCollection::create($dir)
//     ->addFromTags('v2.0.*')
//     ->add('2.0', '2.0 branch')
//     ->add('master', 'master branch')
// ;
return new Sami($iterator, [
    //'theme'                => 'symfony',
    'versions'             => '1.0',
    'title'                => '207 - Diego Soba',
    'build_dir'            => __DIR__.'/docs/finanzas/%version%',
    'cache_dir'            => __DIR__.'/docs/cache/finanzas/%version%',
    // use a custom theme directory
    //'template_dirs'        => array(__DIR__.'/themes/symfony'),
    //'default_opened_level' => 2,
]);
