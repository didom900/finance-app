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

class IgacRecord
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
     * [departamento description]
     * @return [type] [description]
     */
    public function departamento()
    {
        return $this->get('departamento');
    }

    /**
     * [municipio description]
     * @return [type] [description]
     */
    public function municipio()
    {
        return $this->get('municipio');
    }

    /**
     * [numero_predio description]
     * @return [type] [description]
     */
    public function numero_predio()
    {
        return $this->get('numero_predio');
    }

    /**
     * [tipo_registro description]
     * @return [type] [description]
     */
    public function tipo_registro()
    {
        return $this->get('tipo_registro');
    }

    /**
     * [numero_orden description]
     * @return [type] [description]
     */
    public function numero_orden()
    {
        return $this->get('numero_orden');
    }

    /**
     * [total_propietarios description]
     * @return [type] [description]
     */
    public function total_propietarios()
    {
        return $this->get('total_propietarios');
    }

    /**
     * [nombre description]
     * @return [type] [description]
     */
    public function nombre()
    {
        return $this->get('nombre');
    }

    /**
     * [estado_civil description]
     * @return [type] [description]
     */
    public function estado_civil()
    {
        return $this->get('estado_civil');
    }

    /**
     * tipo_documento
     * @return [type] [description]
     */
    public function tipo_documento()
    {
        return $this->get('tipo_documento');
    }

    /**
     * numero_documento
     * @return [type] [description]
     */
    public function numero_documento()
    {
        return $this->get('numero_documento');
    }

    /**
     * direccion
     * @return [type] [description]
     */
    public function direccion()
    {
        return $this->get('direccion');
    }


    /**
     * comuna
     * @return [type] [description]
     */
    public function comuna()
    {
        return $this->get('comuna');
    }


    /**
     * destino_economico
     * @return [type] [description]
     */
    public function destino_economico()
    {
        return $this->get('destino_economico');
    }
      /**
     * area_terreno
     * @return [type] [description]
     */
    public function area_terreno()
    {
        return $this->get('area_terreno');
    }
      /**
     * area_construida
     * @return [type] [description]
     */
    public function area_construida()
    {
        return $this->get('area_construida');
    }
      /**
     * avaluo
     * @return [type] [description]
     */
    public function avaluo()
    {
        return $this->get('avaluo');
    }
      /**
     * vigencia
     * @return [type] [description]
     */
    public function vigencia()
    {
        return $this->get('vigencia');
    }
}