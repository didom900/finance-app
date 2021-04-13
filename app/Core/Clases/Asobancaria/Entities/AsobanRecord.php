<?php

namespace contSoft\Finanzas\Clases\Asobancaria\Entities;

/**
 *
 * @copyright  2017 - Diego Soba.
 * @author     Diego Soba <didom900@gmail.com>
 * @version    v1.0.0
 * @package    Asobancaria
 * @subpackage Asobancaria\Entities
 */

class AsobanRecord
{
    /**
     * [$data description]
     * @var [type]
     */
    protected $data;

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
     * [reference description]
     * @return [type] [description]
     */
    public function reference()
    {
        return $this->get('reference');
    }

    /**
     * [amount description]
     * @return [type] [description]
     */
    public function amount()
    {
        return $this->get('amount');
    }

    /**
     * [origin description]
     * @return [type] [description]
     */
    public function origin()
    {
        return $this->get('origin');
    }

    /**
     * [channel description]
     * @return [type] [description]
     */
    public function channel()
    {
        return $this->get('channel');
    }

    /**
     * [operationId description]
     * @return [type] [description]
     */
    public function operationId()
    {
        return $this->get('operationId');
    }

    /**
     * [authCode description]
     * @return [type] [description]
     */
    public function authCode()
    {
        return $this->get('authCode');
    }

    /**
     * [thirdEntity description]
     * @return [type] [description]
     */
    public function thirdEntity()
    {
        return $this->get('thirdEntity');
    }

    /**
     * [branch description]
     * @return [type] [description]
     */
    public function branch()
    {
        return $this->get('branch');
    }

    /**
     * Secuencia
     * @return [type] [description]
     */
    public function sequence()
    {
        return $this->get('sequence');
    }

    /**
     * Motivo de Reembolso
     * @return [type] [description]
     */
    public function refundReason()
    {
        return $this->get('refundReason');
    }
}