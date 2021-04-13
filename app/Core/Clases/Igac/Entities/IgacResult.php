<?php

namespace contSoft\Finanzas\Clases\Igac\Entities;

/**
 *
 * @copyright  2017 - Diego Soba.
 * @author     Diego Soba <didom900@gmail.com>
 * @version    v1.0.0
 * @package    Igac
 * @subpackage Igac\Entities
 */

class IgacResult
{

    protected $header;
    protected $records = [];
    protected $batchs = [];
    protected $control;

    /**
     * [addHeader description]
     * @param AsobanHeader $asobanHeader [description]
     */
    /*public function addHeader(AsobanHeader $asobanHeader)
    {
        $this->header = $asobanHeader;
        return $this;
    }*/

    /**
     * [addRecord description]
     * @param AsobanRecord $record [description]
     */
    public function addRecord(IgacRecord $record)
    {
        $this->records[] = $record;
        return $this;
    }

    /**
     * [addBatch description]
     * @param AsobanBatch $batch [description]
     */
    /*public function addBatch(AsobanBatch $batch)
    {
        $this->batchs[] = $batch;
        return $this;
    }*/

    /**
     * [addControl description]
     * @param [type] $control [description]
     */
    /*public function addControl($control)
    {
        $this->control = $control;
        return $this;
    }*/

    /**
     * Returns all the records parsed
     * @return AsobanRecord[]
     */
    public function getRecords()
    {
        return $this->records;
    }

    /**
     * @return AsobanHeader
     */
    public function header()
    {
        return $this->header;
    }

    /**
     * @return AsobanControl
     */
    public function control()
    {
        return $this->control;
    }

    /**
     * Returns the number of records for this result
     * @return int
     */
    public function recordCount()
    {
        return sizeof($this->getRecords());
    }
}