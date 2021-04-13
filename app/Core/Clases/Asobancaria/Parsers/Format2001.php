<?php

namespace contSoft\Finanzas\Clases\Asobancaria\Parsers;

use contSoft\Finanzas\Clases\Asobancaria\Entities\AsobanBatch;
use contSoft\Finanzas\Clases\Asobancaria\Entities\AsobanControl;
use contSoft\Finanzas\Clases\Asobancaria\Entities\AsobanEndBatch;
use contSoft\Finanzas\Clases\Asobancaria\Entities\AsobanHeader;
use contSoft\Finanzas\Clases\Asobancaria\Entities\AsobanRecord;

/**
 * Analizador para formato 2001
 *
 * @copyright  2017 - Diego Soba.
 * @author     Diego Soba <didom900@gmail.com>
 * @version    v1.0.0
 * @package    Asobancaria
 * @subpackage Asobancaria\Parsers
 */

class Format2001 extends GenericParser
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
     * Tipo de registro que indica el encabezado.
     * @return string
     */
    public function headerCode()
    {
        return '01';
    }

    /**
     * Tipo de registro que indica el lote.
     * @return string
     */
    public function batchCode()
    {
        return '05';
    }

    /**
     * Tipo de registro que indica un registro.
     * @return string
     */
    public function detailCode()
    {
        return '06';
    }

    /**
     * Tipo de registro que indica el final del lote.
     * @return string
     */
    public function endBatchCode()
    {
        return '08';
    }

    /**
     * Tipo de registro que indica el registro de control
     * del archivo.
     * @return string
     */
    public function controlCode()
    {
        return '09';
    }
    /**
     * @param $row
     * @return AsobanHeader
     */
    public function parseHeader($row)
    {
        return new AsobanHeader([
            'nit' => substr($row, 2, 10),
            'date' => date('Y-m-d', strtotime(substr($row, 12, 8))),
            'bankCode' => substr($row, 20, 3),
            'accountNumber' => ltrim(substr($row, 23, 17), '0'),
            'fileDate' => date('Y-m-d', strtotime(substr($row, 40, 8))),
            'fileTime' => substr($row, 48, 4),
            'fileModifier' => substr($row, 52, 1),
            'accountType' => substr($row, 53, 2),
        ]);
    }
    /**
     * @param $row
     * @return AsobanBatch
     */
    public function parseBatch($row)
    {
        return new AsobanBatch([
            'serviceCode' => substr($row, 2, 13),
            'batchCode' => substr($row, 15, 4),
        ]);
    }
    /**
     * @param $row
     * @return AsobanRecord
     */
    public function parseDetail($row)
    {
        return new AsobanRecord([
            'reference' => ltrim(substr($row, 2, 48), '0'),
            'amount' => floatval(ltrim(substr($row, 50, 14), '0')) / 100,
            'origin' => substr($row, 64, 2),
            'channel' => substr($row, 66, 2),
            'operationId' => substr($row, 68, 6),
            'authCode' => substr($row, 74, 6),
            'thirdEntity' => substr($row, 80, 3),
            'branch' => substr($row, 83, 4),
            'sequence' => intval(ltrim(substr($row, 87, 7), '0')),
            'refundReason' => substr($row, 94, 3),
        ]);
    }
    /**
     * @param $row
     * @return AsobanEndBatch
     */
    public function parseEndBatch($row)
    {
        return new AsobanEndBatch([
            'records' => intval(ltrim(substr($row, 2, 9), '0')),
            'amount' => floatval(ltrim(substr($row, 11, 18), '0')) / 100,
            'batchCode' => substr($row, 29, 4),
        ]);
    }
    /**
     * @param $row
     * @return AsobanControl
     */
    public function parseControl($row)
    {
        return new AsobanControl([
            'records' => intval(ltrim(substr($row, 2, 9), '0')),
            'amount'  => floatval(ltrim(substr($row, 11, 18), '0')) / 100,
        ]);
    }
}