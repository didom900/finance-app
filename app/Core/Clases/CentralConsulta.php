<?php

    namespace contSoft\Finanzas\Clases;

    /**
     * Central de consultas de la aplicación
     *ID
     * Central de consultas que utiliza la aplicación para traer información de la base de datos
     * Utilizamos la librería de abstracción de datos ADODB para mayor portabilidad de la aplicación
     *
     * @copyright  2017 - Diego Soba.
     * @author     Diego Soba <didom900@gmail.com>
     * @version    1.0
     */

    use contSoft\Finanzas\Traits\Campos\CentralConsultaCampo AS Campo;
    use contSoft\Finanzas\Traits\Configuracion\CentralConsultaConfiguracion AS Configuracion;
    use contSoft\Finanzas\Traits\Empresa\CentralConsultaEmpresa AS Empresa;
    /*use contSoft\Finanzas\Traits\Exogena\CentralConsultaExogena AS Exogena;
    use contSoft\Finanzas\Traits\Facturacion\CentralConsultaFacturacion AS Facturacion;
    use contSoft\Finanzas\Traits\Ica\CentralConsultaIca AS Ica;
    use contSoft\Finanzas\Traits\Inventario\CentralConsultaInventario AS Inventario;
    use contSoft\Finanzas\Traits\Nomina\CentralConsultaNomina AS Nomina;*/
    use contSoft\Finanzas\Traits\Presupuesto\CentralConsultaPresupuesto AS Presupuesto;
    use contSoft\Finanzas\Traits\Servicio\CentralConsultaServicio AS Servicio;
    /*use contSoft\Finanzas\Traits\ServicioPublico\CentralConsultaServicioPublico AS ServicioPublico;*/
    use contSoft\Finanzas\Traits\Tercero\CentralConsultaTercero AS Tercero;
    /*use contSoft\Finanzas\Traits\ImpuestoVehiculo\CentralConsultaImpuestoVehiculo AS ImpuestoVehiculo;
    use contSoft\Finanzas\Traits\Predial\CentralConsultaPredial AS Predial;
    use contSoft\Finanzas\Traits\GestorPlantillas\CentralConsultaGestorPlantillas AS GestorPlantillas;*/

    class CentralConsulta {

        use Campo;
        use Configuracion;
        use Empresa;
        /*use Exogena;
        use Facturacion;
        use Ica;
        use Inventario;
        use Nomina;*/
        use Presupuesto;
        use Servicio;
        /*use ServicioPublico;*/
        use Tercero;
        /*use ImpuestoVehiculo;
        use Predial;
        use GestorPlantillas;*/

        public $conexion;
        public $rutaAplicacion;

        public function __construct($conexion, $rutaAplicacion) {
            $this->conexion = $conexion;
            $this->rutaAplicacion = $rutaAplicacion;
        }
    }
?>
