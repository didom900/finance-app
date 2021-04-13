<?php

namespace contSoft\Finanzas\Clases\Igac\Parsers;

use Exception;
//use contSoft\Finanzas\Clases\Asobancaria\Entities\AsobanBatch;
//use contSoft\Finanzas\Clases\Asobancaria\Entities\AsobanHeader;
use contSoft\Finanzas\Clases\Igac\Entities\IgacRecord;
use contSoft\Finanzas\Clases\Igac\Entities\IgacResult;
//use contSoft\Finanzas\Clases\Asobancaria\Entities\AsobanControl;
//use contSoft\Finanzas\Clases\Asobancaria\Entities\AsobanEndBatch;


/**
 * Analizador Generico
 *
 * @copyright  2017 - Diego Soba.
 * @author     Diego Soba <didom900@gmail.com>
 * @version    v1.0.0
 * @package    Igac
 * @subpackage Igac\Parsers
 */

abstract class GenericParser
{

    protected $fileDescriptor;
    protected $filePath;

    private $hasProcessedHeader = false;
    private $hasProcessedBatch = false;
    private $currentBatch = null;

    /**
     * Constructor de la clase.
     * @param string $filePath ruta del archivo.
     */
    public function __construct($filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * Devuelve la ruta del archivo.
     * @return string ruta del archivo.
     */
    private function filePath()
    {
        return $this->filePath;
    }

    /**
     * [getFileDescriptor description]
     * @return [type] [description]
     */
    private function getFileDescriptor()
    {
        $filePath = $this->filePath();

        if (!file_exists($this->filePath())){
            throw new Exception('El archivo no existe: [' . $filePath . ']');
        }

        $this->fileDescriptor = fopen($filePath, 'rb');

        if ($this->fileDescriptor === false){
            throw new Exception('No se puede abrir el archivo: [' . $filePath . ']');
        }

        return $this->fileDescriptor;
    }

    /**
     * Cierra el archivo
     * @return [type] [description]
     */
    private function closeFile()
    {
        fclose($this->fileDescriptor);
    }

    /**
     * Obtiene la siguiente linea.
     * @return [type] [description]
     */
    private function getNextLine()
    {
        return fgets($this->fileDescriptor, 4096);
    }

    public abstract function lineLength();
    //public abstract function recordType($line);
    //public abstract function headerCode();
    //public abstract function batchCode();
    //public abstract function detailCode();
    //public abstract function endBatchCode();
    //public abstract function controlCode();
    /**
     * @param $row
     * @return AsobanHeader
     */
    //public abstract function parseHeader($row);
    /**
     * @param $row
     * @return AsobanBatch
     */
    ///public abstract function parseBatch($row);
    /**
     * @param $row
     * @return AsobanRecord
     */
    public abstract function parseDetail($row);
    /**
     * @param $row
     * @return AsobanEndBatch
     */
    //public abstract function parseEndBatch($row);
    /**
     * @param $row
     * @return AsobanControl
     */
    //public abstract function parseControl($row);

    /**
     * @return AsobanResult
     * @throws Exception
     */
    public function parse()
    {
        $this->getFileDescriptor();
        $result = new IgacResult();
        $rows = 0;
        while(false !== ($info = $this->getNextLine())) {
            $rows++;
            //var_dump($info);
            //die();
            if (empty($info)) continue;
            if (164 < $this->lineLength())
                //var_dump(strlen($info));
            //die();
                throw new Exception('Line ' . $rows . ' has invalid length. ' . strlen($info) . ' expected: ' . $this->lineLength());
            //$recordType = $this->recordType($info);
            //$this->detailCode():
                    //if (!$this->hasProcessedBatch)
                       // throw new Exception(sprintf("El archivo en la línea %d tiene un registro de datos sin un registro previo de lote, lo cual denota una mala //estructura\n%s", $rows, $info), 1003);
                    $result->addRecord($this->parseDetail($info));
            /*switch($recordType) {
                case $this->headerCode():
                    if ($this->hasProcessedHeader)
                        throw new Exception('The file [' . $this->filePath() . '] has more than one header');
                    $result->addHeader($this->parseHeader($info));
                    $this->hasProcessedHeader = true;
                    break;
                case $this->batchCode():
                    if (!$this->hasProcessedHeader)
                        throw new Exception(sprintf("El archivo en la línea %d tiene un registro de lote sin un registro previo de control, lo cual denota una mala estructura\n%s", $rows, $info), 1003);
                    if ($this->hasProcessedBatch)
                        throw new Exception(sprintf("El archivo en la línea %d tiene más de un registro de lote anidado, lo cual denota una mala estructura\n%s", $rows, $info), 1003);
                    $batch = $this->parseBatch($info);

                    $this->hasProcessedBatch = true;
                    $this->currentBatch = $batch->batchCode();
                    break;
                case $this->detailCode():
                    if (!$this->hasProcessedBatch)
                        throw new Exception(sprintf("El archivo en la línea %d tiene un registro de datos sin un registro previo de lote, lo cual denota una mala estructura\n%s", $rows, $info), 1003);
                    $result->addRecord($this->parseDetail($info));
                    break;
                case $this->endBatchCode():
                    if (!$this->hasProcessedBatch)
                        throw new Exception(sprintf("El archivo en la línea %d tiene un registro de fin de lote sin un registro previo de lote, lo cual denota una mala estructura\n%s", $rows, $info), 1003);

                    $batch = $this->parseEndBatch($info);

                    if ($this->currentBatch != $batch->batchCode())
                        throw new Exception(sprintf("El archivo en la línea %d tiene un registro de fin de lote que no corresponde al lote abierto, lo cual denota una mala estructura\n%s", $rows, $info), 1003);
                    $this->hasProcessedBatch = false;
                    $this->currentBatch = null;
                    break;
                case $this->controlCode():
                    if (!$this->hasProcessedHeader)
                        throw new Exception(sprintf("El archivo en la línea %d tiene un registro de fin de lote sin un registro previo de control, lo cual denota una mala estructura\n%s", $rows, $info), 1003);
                    $control = $this->parseControl($info);
                    $result->addControl($control);

                    $this->hasProcessedHeader = false;
                    break;
            }*/
        }

        $this->closeFile();
        return $result;
    }
}