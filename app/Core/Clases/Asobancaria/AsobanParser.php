<?php

namespace contSoft\Finanzas\Clases\Asobancaria;

use Exception;
use contSoft\Finanzas\Clases\Asobancaria\Parsers\Format1998;
use contSoft\Finanzas\Clases\Asobancaria\Parsers\Format2001;
use contSoft\Finanzas\Clases\Asobancaria\Entities\AsobanResult;

/**
 * Analizador de archivos Asobancario
 *
 * @copyright  2017 - Diego Soba.
 * @author     Diego Soba <didom900@gmail.com>
 * @version    v1.0.0
 * @package    Asobancaria
 * @subpackage Asobancaria
 */

class AsobanParser
{

    /**
     * Constantes
     */
    const F_2001 = '2001';
    const F_1998 = '1998';

    /**
     * Array de formatos asobancarios.
     * @var array
     */
    public static $FORMATS = [
        self::F_2001 => 'Asobancaria 2001',
        self::F_1998 => 'Asobancaria 1998',
    ];

    /**
     * Ruta del archivo.
     * @var string
     */
    private $filePath;

    /**
     * Formato asobancario.
     * @var string
     */
    private $format;

    /**
     * Constructor.
     * @param string $filePath ruta del archivo asobancario.
     * @param string $format   formato del archivo asobancario.
     */
    public function __construct($filePath, $format = '2001')
    {
        $this->filePath = $filePath;
        $this->format = $format;
    }

    /**
     * llama al constructor de la clase.
     * @param  [type] $filePath [description]
     * @return [type]           [description]
     */
    public static function load($filePath)
    {
        return new self($filePath);
    }

    /**
     * Dependendiendo del Formato en el constructor instancia la clase.
     * @return AsobanResult
     * @throws Exception
     */
    public function parse()
    {
        if($this->format == self::F_2001){
            return (new Format2001($this->filePath))->parse();
        } else if ($this->format == self::F_1998){
            return (new Format1998($this->filePath))->parse();
        }else {
            throw new Exception('No hay ningún analizador Asobancaria definido para el formato provisto');
        }
    }

}