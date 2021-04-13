<?php

namespace contSoft\Finanzas\Clases\Modulos\ImpuestoVehiculo;

use Exception as Exception;
use contSoft\Finanzas\Clases\Helpers\Helper;
use InvalidArgumentException as InvalidArgumentException;

class CorreccionVehiculo
{

    /**
     * [$conexion description]
     * @var [type]
     */
    private $conexion;

    /**
     * [$conexion description]
     * @var [type]
     */
    private $centralConsulta;

    /**
     * constructor
     * @param object contSoft\Finanzas\Clases\Conexion
     */
    public function __construct($conexion, $centralConsulta)
    {
        $this->conexion = $conexion;
        $this->centralConsulta = $centralConsulta;
    }

    public function numeroDeclaracion()
    {
        $nuevaDeclaracion = $this->centralConsulta->consecutivoDeclaracion();

        while (strlen($nuevaDeclaracion) < 6) {
            $nuevaDeclaracion = '0' . $nuevaDeclaracion;
        }
        $nuevaDeclaracion = Helper::vigencia() . $nuevaDeclaracion;

        return $nuevaDeclaracion;
    }

    /**
     * Devuelve el proximo consecutivo de la tabla liquidacion_correcion.
     * @return [type] [description]
     */
    public function consecutivoCorreccion()
    {
        return $this->centralConsulta->consecutivoLiquidacionCorreccion();
    }

    /**
     * Devuelve el proximo consecutivo de la tabla liquidacion_vehiculo.
     * @return [type] [description]
     */
    public function consecutivoLiquidacionVehiculo()
    {
        return $this->centralConsulta->consecutivoLiquidacionVehiculo();
    }

    /**
     * Calcula la diferencia entre lo que debia
     * pagar y lo que pago.
     * @param  array  $datos datos a operar
     * @return [type]        [description]
     */
    public function calcularDiferencia($data = [])
    {
        return Helper::monedaCop($data['debe'], 0)
            - Helper::monedaCop($data['pago'], 0);
    }

    /**
     * Suma las diferencias del impuesto, sancion e intereses.
     * @param  array  $data [description]
     * @return [type]       [description]
     */
    public function calcularTotalPago(array $data = [])
    {
        return Helper::monedaCop($data['impuesto'], 0)
            + Helper::monedaCop($data['sancion'], 0)
            + Helper::monedaCop($data['interes'], 0)
            - Helper::monedaCop($data['descuento'], 0);
    }

    /**
     * Devuelve el valor de configuracion para agregar dias
     * a la fecha actual
     * @param  string $value [description]
     * @return [type]        [description]
     */
    public function fechaPago($empresa, $vigencia)
    {
        $dias = $this->centralConsulta->configuracionVehiculoTiempoPago($empresa, $vigencia);
        return Helper::fechaPago($dias[0]['dias_pago']);
    }

    /**
     * Funcion para usar ADO.
     * @param string $sql Consulta sql
     * @param array $data Array de datos para bindear en la consulta.
     * @return array
     */
    public function query($sql, array $data = [])
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
     * Devuelve el municipio del tercero asociado al vehiculo
     *
     * @param string $vehiculo Id del vehiculo
     * @param int $empresa empresa del tercero.
     * @return array
     */
    public function buscarPlacaMunicipio($vehiculo, $empresa)
    {
        $resultado = $this->centralConsulta->buscarPlacaMunicipio($vehiculo, $empresa);
        return $resultado['municipio'];
    }
}
