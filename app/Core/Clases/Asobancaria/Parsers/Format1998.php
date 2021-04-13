<?php

namespace contSoft\Finanzas\Clases\Asobancaria\Parsers;

use contSoft\Finanzas\Clases\Entities\AsobanBatch;
use contSoft\Finanzas\Clases\AsobanControl;
use contSoft\Finanzas\Clases\AsobanEndBatch;
use contSoft\Finanzas\Clases\AsobanHeader;
use contSoft\Finanzas\Clases\AsobanRecord;

/**
 * Analizador para formato 1998
 *
 * @copyright  2017 - Diego Soba.
 * @author     Diego Soba <didom900@gmail.com>
 * @version    v1.0.0
 * @package    Asobancaria
 * @subpackage Asobancaria\Parsers
 */

class Format1998 extends GenericParser
{

    /**
     * [lineLength description]
     * @return [type] [description]
     */
    public function lineLength()
    {
        return 128;
    }

    /**
     * [recordType description]
     * @param  [type] $line [description]
     * @return [type]       [description]
     */
    public function recordType($line)
    {
        return substr($line, 0, 2);
    }

    /**
     * [headerCode description]
     * @return [type] [description]
     */
    public function headerCode()
    {
        return '01';
    }

    /**
     * [batchCode description]
     * @return [type] [description]
     */
    public function batchCode()
    {
        return null;
    }

    /**
     * [detailCode description]
     * @return [type] [description]
     */
    public function detailCode()
    {
        return '02';
    }

    /**
     * [endBatchCode description]
     * @return [type] [description]
     */
    public function endBatchCode()
    {
        return null;
    }

    /**
     * [controlCode description]
     * @return [type] [description]
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
            'nit' => substr($row, 2, 13),
            'date' => date('Y-m-d', strtotime(substr($row, 15, 8))),
            'bankCode' => substr($row, 23, 3),
            'accountNumber' => ltrim(substr($row, 26, 15), '0'),
            'fileDate' => null,
            'fileTime' => null,
            'fileModifier' => null,
            'accountType' => null,
        ]);
    }

    /**
     * @param $row
     * @return AsobanBatch
     */
    public function parseBatch($row)
    {
        return null;
    }

    /**
     * @param $row
     * @return AsobanRecord
     */
    public function parseDetail($row)
    {
        return new AsobanRecord([
            'reference' => ltrim(substr($row, 2, 25), '0'),
            'amount' => floatval(ltrim(substr($row, 27, 13), '0')) / 100,
            'origin' => substr($row, 40, 2),
            'sequence' => intval(ltrim(substr($row, 42, 7), '0')),
            'channel' => null,
            'operationId' => null,
            'authCode' => null,
            'thirdEntity' => null,
            'branch' => null,
            'refundReason' => null,
        ]);
    }

    /**
     * @param $row
     * @return AsobanEndBatch
     */
    public function parseEndBatch($row)
    {
        return null;
    }

    /**
     * @param $row
     * @return AsobanControl
     */
    public function parseControl($row)
    {
        return new AsobanControl([
            'records' => intval(ltrim(substr($row, 2, 9), '0')),
            'amount' => floatval(ltrim(substr($row, 11, 18), '0')) / 100,
        ]);
    }
}