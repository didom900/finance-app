<?php

namespace contSoft\Finanzas\Clases\Asobancaria\Entities;

use DateTime;

/**
 * Contiene la información del encabezado del reporte.
 *
 * @copyright  2017 - Diego Soba.
 * @author     Diego Soba <didom900@gmail.com>
 * @version    v1.0.0
 * @package    Asobancaria
 * @subpackage Asobancaria\Entities
 */

class AsobanHeader
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
     * @return string
     */
    public function nit()
    {
        return $this->get('nit');
    }

    /**
     * Returns a Y-m-d
     * @return string
     */
    public function date()
    {
        return $this->get('date');
    }

    /**
     * Codigo Entidad Recaudadora
     * @return string
     */
    public function bankCode()
    {
        return $this->get('bankCode');
    }

    /**
     * @return string
     */
    public function accountNumber()
    {
        return $this->get('accountNumber');
    }

    /**
     * Returns a Y-m-d with the date when the file was created
     * @return string
     */
    public function fileDate()
    {
        return $this->get('fileDate');
    }

    /**
     * Returns the time with a format HH:MM representing the time when
     * the file was created
     * @return string
     */
    public function fileTime()
    {
        return substr_replace($this->get('fileTime'), ':', 2, 0);
    }

    /**
     * @return DateTime
     */
    public function fileDateTime()
    {
        return new DateTime($this->fileDate() . ' ' . $this->fileTime());
    }

    /**
     * [fileModifier description]
     * @return [type] [description]
     */
    public function fileModifier()
    {
        return $this->get('fileModifier');
    }

    /**
     * Returns
     *  1: Savings Account
     *  2: Checking Account
     *  3: Credit Card
     * @return int
     */
    public function accountType()
    {
        return $this->get('accountType');
    }

}