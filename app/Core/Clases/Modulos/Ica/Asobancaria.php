<?php

namespace contSoft\Finanzas\Clases\Modulos\Ica;

use contSoft\Finanzas\Clases\Helpers\Helper;

/**
 * Class IcaAcuerdoPago
 * @copyright  2017 - Diego Soba.
 * @author     Diego Soba <didom900@gmail.com>
 * @version    v1.0.0
 * @package contSoft\Finanzas\Clases\Modulos\Ica
 * @subpackage contSoft\Finanzas\Clases\Modulos\Ica\IcaAcuerdoPago
 */
class Asobancaria
{

    /**
     * constructor
     * @param object $conexion contSoft\Finanzas\Clases\Conexion
     * @param object $centralConsulta contSoft\Finanzas\Clases\CentralConsulta
     */
    public function __construct()
    {
        //ToDo
    }

    /**
     * Completa con ceros el valor de la referencia
     * @return string retorna el consecutivo de la referencia
     * segun el tipo de impuesto.
     */
    public function completarReferencia($referencia)
    {
        $nuevaReferencia = $referencia;

        while (strlen($nuevaReferencia) < 16) {
            $nuevaReferencia = '0' . $nuevaReferencia;
        }

        return $nuevaReferencia;
    }

    /**
     * Devuelve el tipo de impuesto 2 (ICA) paraa ingresar en asobancaria.
     * @return [int] [tipo de impuesto]
     */
    public function tipoImpuesto($empresa, $referencia)
    {
        switch ($empresa) {
            case 3:
                $aux = substr($referencia, 0, 4);

                if ($aux == '0501' || $aux == '0601' || $aux == '0602') {
                    return 2; //Devuelve 2 para industria y comercio
                }
                if ($aux == '0502') {
                    return 4; //devuelve 4 para impuesto de reteica
                }
                if ($aux == '0100') {
                    return 3; //devuelve 3 para impuesto de predial
                }
                return 2;
                break;

            default:
                return 2;
                break;
        }
    }
}
