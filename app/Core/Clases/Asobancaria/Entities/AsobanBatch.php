<?php

namespace contSoft\Finanzas\Clases\Asobancaria\Entities;

/**
 * Lectura de lote.
 *
 * @copyright  2017 - Diego Soba.
 * @author     Diego Soba <didom900@gmail.com>
 * @version    v1.0.0
 * @package    Asobancaria
 * @subpackage Asobancaria\Entities
 */

class AsobanBatch
{
    /**
     * [$data description]
     * @var [type]
     */
    private $data;

    /**
     * [__construct description]
     * @param [type] $data [description]
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * [get description]
     * @param  [type] $key [description]
     * @return [type]      [description]
     */
    private function get($key)
    {
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }

    /**
     * [batchCode description]
     * @return [type] [description]
     */
    public function batchCode()
    {
        return $this->get('batchCode');
    }

    /**
     * [serviceCode description]
     * @return [type] [description]
     */
    public function serviceCode()
    {
        return $this->get('serviceCode');
    }
}