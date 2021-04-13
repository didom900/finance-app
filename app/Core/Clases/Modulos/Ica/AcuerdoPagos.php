<?php

namespace contSoft\Finanzas\Clases\Modulos\Ica;

use contSoft\Finanzas\Clases\Helpers\Helper;
use Carbon\Carbon;

/**
 * Class IcaAcuerdoPago
 * @copyright  2017 - Diego Soba.
 * @author     Diego Soba <didom900@gmail.com>
 * @version    v1.0.0
 * @package contSoft\Finanzas\Clases\Modulos\Ica
 * @subpackage contSoft\Finanzas\Clases\Modulos\Ica\IcaAcuerdoPago
 */
class AcuerdoPagos
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
     * Numero de consecutivo para ica para acuerdo de pago. propio de la aplicacion.
     * @return string retorna el consecutivo de la referencia
     * segun el tipo de impuesto.
     */
    public function consecutivoReferenciaIcaAcuerdoPago()
    {
        $nuevaDeclaracion = $this->centralConsulta->consecutivoReferenciaIcaAcuerdoPago($this->tipoImpuesto());

        while (strlen($nuevaDeclaracion) < 6) {
            $nuevaDeclaracion = '0' . $nuevaDeclaracion;
        }
        $nuevaDeclaracion = Helper::vigencia() .
            $this->tipoImpuesto() .
            $nuevaDeclaracion;

        return $nuevaDeclaracion;
    }

    /**
     * Numero de referencia para el codigo de barras (Codigo 8020)
     * @return string retorna la referencia 8020 a utilizar en barcode.
     */
    public function referenciaLiquidacionIcaAcuerdoi()
    {
        $now = Carbon::now('America/Bogota');
        $nuevaDeclaracion = $this->centralConsulta->referenciaLiquidacionIcaAcuerdoi($this->tipoImpuesto());

        while (strlen($nuevaDeclaracion) < 8) {
            $nuevaDeclaracion = '0' . $nuevaDeclaracion;
        }
        $nuevaDeclaracion = '0602' .
            $nuevaDeclaracion .
            Carbon::parse($now)->format('m') .   //Concatenamos el mes
            Carbon::parse($now)->format('y');    //Concatenamos el Año;

        return $nuevaDeclaracion;
    }

    /**
     * Devuelve el número de acuerdo creado en acuerdos_pago
     * @return string
     */
    public function selectNumeroIcaAcuerdo($nuevoIcaAcuerdo)
    {
        return $this->centralConsulta->selectNumeroIcaAcuerdo($nuevoIcaAcuerdo);
    }

    /**
     * Devuelve array de consecutivos
     * @return string
     */
    public function consecutivoIcaAcuerdo($acuerdo)
    {
        return $this->centralConsulta->consecutivoIcaAcuerdo($acuerdo);
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
    public function consecutivoIcaAcuerdoPago()
    {
        return $this->centralConsulta->consecutivoIcaAcuerdoPago();
    }


    /**
     * Devuelve el proximo consecutivo de la tabla acuerdo_pago_liquidacion.
     * @return string
     */
    public function consecutivoIcaAcuerdoPagoLiquidacion()
    {
        return $this->centralConsulta->consecutivoIcaAcuerdoPagoLiquidacion();
    }

    /**
     * Devuelve el identificador de tipo de impuesto para vehiculos.
     * @return int
     */
    public function tipoImpuesto()
    {
        return 2; //Identificador para impuesto ica.
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
    public function saldoInicialIcaAcuerdoPago($acuerdo)
    {
        $total = 0;
        $inicial = 0;
        $cuotas = $this->centralConsulta->icaAcuerdoPagoCuota($acuerdo);
        for ($i=1; $i < sizeof($cuotas); $i++) {
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
