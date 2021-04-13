<?php

namespace contSoft\Finanzas\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Tercero extends Eloquent
{
    /**
     * Nombre de la tabla del Modelo.
     *
     * @var string
     */
    protected $table = 'tercero';

    /**
     * Los Atributos que son asignados.
     *
     * @var array
     */
    protected $fillable = [
        'nit',
        'municipio',
        'ciudad_pago',
        'empresa',
        'digito_verificacion',
        'identificacion',
        'nombre',
        'apellido',
        'razon_social',
    ];

    /**
     * Los atributos que deben estar ocultos para las matrices.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * Llave Primaria del Modelo.
     *
     * @var string
     */
    protected $primaryKey = 'tercero';

    public $timestamps = false;
}
