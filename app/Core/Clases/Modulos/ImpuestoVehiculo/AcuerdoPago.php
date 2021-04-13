<?php

namespace contSoft\Finanzas\Clases\Modulos\ImpuestoVehiculo;

use contSoft\Finanzas\Clases\Helpers\Helper;

/**
 * Class AcuerdoPago
 * @copyright  2017 - Diego Soba.
 * @author     Diego Soba <didom900@gmail.com>
 * @version    v1.0.0
 * @package contSoft\Finanzas\Clases\Modulos\ImpuestoVehiculo
 * @subpackage contSoft\Finanzas\Clases\Modulos\ImpuestoVehiculo\AcuerdoPago
 */
class AcuerdoPago
{

    /**
     * Instancia de conexion
     * @var object
     */
    private $conexion;

    /**
     * Instancia de central consulta
     * @var object
     */
    private $centralConsulta;

    /**
     * constructor
     * @param object $conexion contSoft\Finanzas\Clases\Conexion
     * @param object $centralConsulta contSoft\Finanzas\Clases\CentralConsulta
     */
    public function __construct($conexion, $centralConsulta)
    {
        $this->conexion = $conexion;
        $this->centralConsulta = $centralConsulta;
    }

    /**
     * Numero de referencia para acuerdo de pago.
     * @return string retorna el consecutivo de la referencia
     * segun el tipo de impuesto.
     */
    public function consecutivoReferenciaAcuerdoPago()
    {
        $nuevaDeclaracion = $this->centralConsulta->consecutivoReferenciaAcuerdoPago($this->tipoImpuesto());

        while (strlen($nuevaDeclaracion) < 6) {
            $nuevaDeclaracion = '0' . $nuevaDeclaracion;
        }
        $nuevaDeclaracion = Helper::vigencia() .
            $this->tipoImpuesto() .
            $nuevaDeclaracion;

        return $nuevaDeclaracion;
    }

    /**
     * Numero de referencia para generar liquidacion de una
     * cuota del acuerdo de pago
     * @return string retorna el consecutivo de la referencia
     * segun el tipo de impuesto.
     */
    public function referenciaLiquidacionAcuerdoi()
    {
        $nuevaDeclaracion = $this->centralConsulta->referenciaLiquidacionAcuerdoi($this->tipoImpuesto());

        while (strlen($nuevaDeclaracion) < 5) {
            $nuevaDeclaracion = '0' . $nuevaDeclaracion;
        }
        $nuevaDeclaracion = Helper::vigencia() .
            //$this->tipoImpuesto() .
            '9'.
            $nuevaDeclaracion;

        return $nuevaDeclaracion;
    }

    /**
     * Devuelve el número de acuerdo creado en acuerdos_pago
     * @return string
     */
    public function selectNumeroAcuerdo($nuevoAcuerdo)
    {
        return $this->centralConsulta->selectNumeroAcuerdo($nuevoAcuerdo);
    }

    /**
     * Devuelve array de consecutivos
     * @return string
     */
    public function consecutivoAcuerdo($acuerdo)
    {
        return $this->centralConsulta->consecutivoAcuerdo($acuerdo);
    }

    /**
     * Devuelve posicionTotal
     * @return string
     */
    public function consecutivoTotal($cuota)
    {
        return $this->centralConsulta->consecutivoTotal($cuota);
    }



    /**
     * Devuelve el proximo consecutivo de la tabla acuerdo_pago.
     * @return string
     */
    public function consecutivoAcuerdoPago()
    {
        return $this->centralConsulta->consecutivoAcuerdoPago();
    }


    /**
     * Devuelve el proximo consecutivo de la tabla acuerdo_pago_liquidacion.
     * @return string
     */
    public function consecutivoAcuerdoPagoLiquidacion()
    {
        return $this->centralConsulta->consecutivoAcuerdoPagoLiquidacion();
    }

    /**
     * Devuelve el identificador de tipo de impuesto para vehiculos.
     * @return int
     */
    public function tipoImpuesto()
    {
        return 1; //Identificador para impuesto vehicular.
    }

    /**
     * Devuelve el valor de configuracion para agregar dias
     * a la fecha actual
     * @param int $empresa Id de la empresa
     * @param string $vigencia Año de la vigencia
     * @return string
     */
    public function fechaPago($empresa, $vigencia)
    {
        $dias = $this->centralConsulta->configuracionVehiculoTiempoPago($empresa, $vigencia);
        return Helper::fechaPago($dias[0]['dias_pago']);
    }

    /**
     * Funcion para ejecutat inserts.
     * @param string $sql Consulta sql
     * @param array $data Array de datos para bindear en la consulta.
     * @return array
     */
    public function query($sql, array $data =[])
    {
        $errors=[];

        $resultado = $this->conexion->getDBCon()->Execute($sql, $data);
        if ($resultado === false) {
            $sqls[] = $sql;
            $errors[] = $this->conexion->getDBCon()->ErrorMsg();
        }

        $info =[
            'sql'=> $sql,
            'errors' => $errors,
        ];
        return $info;
    }

    /**
     * Devuelve el saldo del acuerdo Inicial. Es decir el valor total menos
     * la primera cuota. ya que se exige haber pagado la primera.
     * @return [type] [description]
     */
    public function saldoInicialAcuerdoPago($acuerdo)
    {
        $total = 0;
        $inicial = 0;
        $cuotas = $this->centralConsulta->acuerdoPagoCuota($acuerdo);
        for ($i=1; $i < sizeof($cuotas) ; $i++) {
            $total += $cuotas[$i]['valor_cuota'];
        };
        $inicial += $cuotas[0]['valor_cuota'];
        $data = [
            'c_inicial' => $inicial,//cuota inicial
            'c_saldo'  => $total //saldo
        ];
        return $data;
    }
}
