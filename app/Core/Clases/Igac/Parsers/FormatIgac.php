<?php

namespace contSoft\Finanzas\Clases\Igac\Parsers;

//use contSoft\Finanzas\Clases\Asobancaria\Entities\AsobanBatch;
//use contSoft\Finanzas\Clases\Asobancaria\Entities\AsobanControl;
//use contSoft\Finanzas\Clases\Asobancaria\Entities\AsobanEndBatch;
//use contSoft\Finanzas\Clases\Asobancaria\Entities\AsobanHeader;
use contSoft\Finanzas\Clases\Igac\Entities\IgacRecord;

/**
 * Analizador para formato 2001
 *
 * @copyright  2017 - Diego Soba.
 * @author     Diego Soba <didom900@gmail.com>
 * @version    v1.0.0
 * @package    Igac
 * @subpackage Igac\Parsers
 */

class FormatIgac extends GenericParser
{
    /**
     * Maximo de caracteres por linea.
     * @return int
     */
    public function lineLength()
    {
        return 162;
    }

    /**
     * Tipo de Registro.
     * @param  int $line linea a revisar.
     * @return string Devuelve el codigo de tipo de registro.
     */
    public function recordType($line)
    {
        return substr($line, 0, 2);
    }

     /**
     * @param $row
     * @return AsobanRecord
     */
    public function parseDetail($row)
    {
        return new IgacRecord([
            'departamento' =>  ltrim(substr($row, 0, 2)),
            'municipio' => ltrim(substr($row, 2, 3)),
            'numero_predio' =>ltrim(substr($row, 5, 15)),
            'tipo_registro' =>ltrim(substr($row, 20, 1)),
            'numero_orden' =>ltrim(substr($row, 21, 3)),
            'total_propietarios' =>ltrim(substr($row, 24, 3)),
            'nombre' =>ltrim(substr($row, 27, 33)),
            'estado_civil' =>ltrim(substr($row,  60, 1)),
            'tipo_documento' =>ltrim(substr($row,  61, 1)),
            'numero_documento' =>ltrim(substr($row,  62, 12)),
            'direccion' =>ltrim(substr($row,  74, 34)),
            'comuna' =>ltrim(substr($row,  108, 1)),
            'destino_economico' =>ltrim(substr($row,  109, 1)),
            'area_terreno' =>ltrim(substr($row,  110, 12)),
            'area_construida' =>ltrim(substr($row,  122, 6)),
            'avaluo' =>ltrim(substr($row,  128, 12)),
            'vigencia' =>ltrim(substr($row,  140, 9)),
        ]);
    }
}