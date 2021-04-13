<?php

namespace contSoft\Finanzas\Clases\Igac;

use Exception;
use contSoft\Finanzas\Clases\Igac\Parsers\FormatIgac;
use contSoft\Finanzas\Clases\Igac\Entities\IgacResult;

/**
 * Analizador de archivos Igac
 *
 * @copyright  2017 - Diego Soba.
 * @author    Diego Soba <didom900@gmail.com>
 * @version    v1.0.0
 * @package    Asobancaria
 * @subpackage Asobancaria
 */

class IgacParser
{

    /**
     * Constantes
     */
    const F_1 = '1';
    //const F_1998 = '1998';

    /**
     * Array de formatos Igac.
     * @var array
     */
    public static $FORMATS = [
        self::F_1 => 'IGAC1',
       // self::F_1998 => 'Asobancaria 1998',
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
    public function __construct($filePath, $format = '1')
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
        if($this->format == self::F_1){
            return (new FormatIgac($this->filePath))->parse();
        }else {
            throw new Exception('No hay ningún analizador IGAC definido para el formato provisto');
        }
    }

}