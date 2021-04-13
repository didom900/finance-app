<?php
    /**
     * Formularios de la aplicación
     *
     * Crea el HTML de formualarios que requiera la aplicación
     *
     * @copyright  2017 - Diego Soba.
     * @author     Diego Soba <didom900@gmail.com>
     * @version    2.0
     */

    namespace contSoft\Finanzas\Clases;

    class FormularioAplicacion {
        private $centralConsulta;
        private $rutaAplicacion;
        private $formatoAplicacion;
        private $idFormulario = 0;
        private $nombreFormulario = '';
        private $actionFormulario = '';
        private $tablaRelacion = '';
        private $datoPregunta = '';
        private $datos = Array();
        private $contextoFormulario = '';

        public function __construct($centralConsulta, $rutaAplicacion, $formatoAplicacion) {
            $this->centralConsulta = $centralConsulta;
            $this->rutaAplicacion = $rutaAplicacion;
            $this->formatoAplicacion = $formatoAplicacion;
        }

        public function __get($nombre){//SE UTILIZA PARA CONSULTAR DATOS A PARTIR DE PROPIEDADES INACCESIBLES
            return $this->$nombre;
        }
        public function __isset($nombre){//SE UTILIZA PARA CONSULTAR SI UNA VARIABLE ESTÁ DEFINIDA Y NO ES NULL  SOBRE PROPIEDADES INACCESIBLES
            return isset($this->$nombre);
        }

        public function formulario($idFormulario, $nombreFormulario, $actionFormulario, $tablaRelacion, $datoPregunta) {
            $this->idFormulario = $idFormulario;
            $this->nombreFormulario = $nombreFormulario;
            $this->actionFormulario = $actionFormulario;
            $this->tablaRelacion = $tablaRelacion;
            $this->datoPregunta = $datoPregunta;
            $this->buscaResultado();
            $this->contexto();
        }

        public function buscaResultado() {

            // Armado de Formularios
            if(!is_string($this->tablaRelacion)){
                foreach ($this->tablaRelacion as $tabla => $valor) {
                    $auxConsulta = $this->centralConsulta->$tabla($valor);
                    // Empezamos recorrido de ese campo.
                    foreach ($auxConsulta as $campo) {
                        // Los tipo select y checkbox pueden contener más de un (1) dato.
                        if ($campo['selector'] == 'select' || $campo['selector'] == 'checkbox') {
                            $this->datos[$campo['nombre']][] = $campo['valor'];
                        // Datos normales.
                        } else {
                            $this->datos[$campo['nombre']] = $campo['valor'];
                        }
                    }
                }
            }

            // Tabla tercero
            if ($this->tablaRelacion == 'rl_tercero_campos' && $this->datoPregunta != '') {
                $rlCampo = $this->centralConsulta->rlCampo($this->datoPregunta);
                foreach ($rlCampo as $campo) {

                    // Los tipo select y checkbox pueden contener más de un (1) dato.
                    if ($campo['selector'] == 'select' || $campo['selector'] == 'checkbox') {
                        $this->datos[$campo['nombre']][] = $campo['valor'];

                    // Datos normales.
                    } else {
                        $this->datos[$campo['nombre']] = $campo['valor'];
                    }
                }
                $tercero = $this->centralConsulta->tercero($this->datoPregunta);
                $this->datos['nit'] = $tercero[0]['nit'];
                $this->datos['digitoVerificacion'] = $tercero[0]['digito_verificacion'];
                $this->datos['cedulaTercero'] = $tercero[0]['identificacion'];
                $this->datos['nombres'] = $tercero[0]['nombre'];
                $this->datos['apellidos'] = $tercero[0]['apellido'];
                $this->datos['razonSocial'] = $tercero[0]['razon_social'];
                $this->datos['cedulaRepresentante'] = $tercero[0]['identificacion'];
                $this->datos['representanteLegal'] = $tercero[0]['nombre'];
                $this->datos['apellidoRepresentate'] = $tercero[0]['apellido'];
                $this->datos['municipio'][] = $tercero[0]['municipio'];
                $this->datos['ciudadPago'][] = $tercero[0]['ciudad_pago'];
                $terceroTipoTercero = $this->centralConsulta->terceroTipoTercero($this->datoPregunta);
                foreach ($terceroTipoTercero as $campo) {
                    $this->datos['tipoTercero'][] = $campo['valor'];
                }
                //print "<pre>"; print_r($this->datos); print "</pre>\n";
            }

            // Tabla estado-producto.
            if ($this->tablaRelacion == 'rl_estado_producto' && $this->datoPregunta != '') {
                $rlCampo = $this->centralConsulta->rlEstadoProducto($this->datoPregunta);
                //var_dump($rlCampo[0]['seriales']);
                //die();
                $this->datos['fecha_estado'] = $rlCampo[0]['fecha_estado'];
                $this->datos['tipoestado'] = $rlCampo[0]['tipoestado'];
                $this->datos['porcentaje_estado'] = $rlCampo[0]['porcentaje_deterioro'];
                //$this->datos['activo'][] = $rlCampo[0]['activo'];
                $this->datos['producto_estado'][] = $rlCampo[0]['producto'];
                $this->datos['bodega_estado'][] = $rlCampo[0]['bodega'];
                //$this->datos['estado_producto'] = $rlCampo[0]['estado_producto'];
                $this->datos['remitente_estado'][] = $rlCampo[0]['remitente'];
                //$this->datos['serial_estado'][][] = $rlCampo[0]['seriales'];
                $this->datos['estado_producto'][] = $rlCampo[0]['tipoestado'];
                $this->datos['observacion_estados']= $rlCampo[0]['observacion'];
                //var_dump($rlCampo[0]['estado_producto']);
                //die();
                $idEstado=$rlCampo[0]['estado_producto'];
                $terceroTipoTercero = $this->centralConsulta->serialesEstado($idEstado);
                foreach ($terceroTipoTercero as $campo) {
                    $this->datos['serial_estado'][] = $campo['seriales'];
                }
            }

            // Tabla campo
            if ($this->idFormulario == '3' && $this->datoPregunta != '') {

                $datosCampo = $this->centralConsulta->datosCampo($this->datoPregunta);
                $this->datos['etiqueta'] = $datosCampo[0]['etiqueta'];
                $this->datos['nombre'] = $datosCampo[0]['nombre'];
                $this->datos['descripcion'] = $datosCampo[0]['descripcion'];
                $this->datos['tablaDestino'] = $datosCampo[0]['tabla_destino'];
                $this->datos['campoDestino'] = $datosCampo[0]['campo_destino'];
                $this->datos['operacion'] = $datosCampo[0]['operacion'];
                //print "<pre>"; print_r($this->datos); print "</pre>\n";
            }

            // Tabla campo_atributo
            if ($this->idFormulario == '4' && $this->datoPregunta != '') {

                $datosCampo = $this->centralConsulta->datosCampoAtributo($this->datoPregunta);
                $this->datos['campoFormulario'][] = $datosCampo[0]['campo_formulario'];
                $this->datos['tipoAtributo'][] = $datosCampo[0]['tipo_atributo'];
                $this->datos['valor'] = $datosCampo[0]['valor'];
                $this->datos['formulario'][] = $datosCampo[0]['formulario'];
                //print "<pre>"; print_r($this->datos); print "</pre>\n";
            }

            //tabla campo_atributo
            if ($this->idFormulario == '5' && $this->datoPregunta != '') {

                $datosCampo = $this->centralConsulta->datosCampoFormulario($this->datoPregunta);
                $this->datos['campo'][] = $datosCampo[0]['campo'];
                $this->datos['formulario'][] = $datosCampo[0]['formulario'];
                $this->datos['seccion'] = $datosCampo[0]['seccion'];
                $this->datos['ordenSeccion'] = $datosCampo[0]['orden_seccion'];
                $this->datos['tipoCampo'][] = $datosCampo[0]['tipo_campo'];
                $this->datos['activo'] = $datosCampo[0]['activo'];
                $this->datos['valor'] = $datosCampo[0]['valor'];
                $this->datos['etiquetaSecundaria'] = $datosCampo[0]['etiqueta_secundaria'];
                $this->datos['funcion'] = $datosCampo[0]['funcion'];
                $this->datos['tabla'] = $datosCampo[0]['tabla'];
                $this->datos['mostrar'] = $datosCampo[0]['mostrar'];


                //print "<pre>"; print_r($datosCampo); print "</pre>\n";
            }

            // FORMULARIO PROCESOS
            if ($this->idFormulario == '405' && $this->datoPregunta != '') {
                $datosCampo = $this->centralConsulta->getProceso($this->datoPregunta);
                $this->datos['Fecha_orden_produccio'] = $datosCampo[0]['fecha'];
                $this->datos['producto_fabricar'][] = $datosCampo[0]['producto'];
                $this->datos['cantidad_producir'] = $datosCampo[0]['cantidad_producir'];
                $this->datos['Responsable_proceso'][] = $datosCampo[0]['responsable'];
                $this->datos['concepto_proceso'] = $datosCampo[0]['concepto'];
                $this->datos['Estado_proceso'] = $datosCampo[0]['estado'];
                $this->datos['Bodega_proceso'][] = $datosCampo[0]['bodega'];
                $this->datos['idProceso'] = $datosCampo[0]['id_proceso'];
                $this->datos['Unidad_proceso'] = $datosCampo[0]['unidad'];
            }

            // Tabla categoria_producto
            if ($this->idFormulario == '6' && $this->datoPregunta != '') {

                $datosCampo = $this->centralConsulta->datosCategoriaProducto($this->datoPregunta);
                $this->datos['codigo'] = $datosCampo[0]['codigo'];
                $this->datos['nombre'] = $datosCampo[0]['nombre'];
                $this->datos['prefijo_categoria'] = $datosCampo[0]['prefijo'];
                //print "<pre>"; print_r($this->datos); print "</pre>\n";
            }

             // Tabla tipo_producto
            if ($this->idFormulario == '450' && $this->datoPregunta != '') {

                $datosCampo = $this->centralConsulta->datosTipoProducto($this->datoPregunta);
                $this->datos['nombre_tipo'] = $datosCampo[0]['nombre'];
                $this->datos['categoria_tipo'][] = $datosCampo[0]['categoria'];
                //print "<pre>"; print_r($this->datos); print "</pre>\n";
            }


             // Tabla clase_producto
            if ($this->idFormulario == '451' && $this->datoPregunta != '') {

                $datosCampo = $this->centralConsulta->datosClaseaProducto($this->datoPregunta);
                $this->datos['nombre_clase'] = $datosCampo[0]['nombre'];
                $this->datos['clase_tipo'][] = $datosCampo[0]['tipo_producto'];
                $this->datos['contabilizacion'][] = $datosCampo[0]['contabilizacion'];

            }

            // Tabla Dependencias
            if ($this->idFormulario == '452' && $this->datoPregunta != '') {

                $datosCampo = $this->centralConsulta->datosDependencia($this->datoPregunta);
                $this->datos['nombre_dependencia'] = $datosCampo[0]['nombre'];
                $this->datos['abreviatura_dependencia'] = $datosCampo[0]['abreviatura'];
                $this->datos['direccion_dependencia'] = $datosCampo[0]['direccion'];
                $this->datos['telefono_dependencia'] = $datosCampo[0]['telefono'];
                $this->datos['email_dependencia'] = $datosCampo[0]['email'];
                $this->datos['responsable_dependencia'][] = $datosCampo[0]['responsable'];

            }

            if ($this->tablaRelacion == 'rl_producto_campos' && $this->datoPregunta != '') {
                $rlCampoProducto = $this->centralConsulta->rlCampoProducto($this->datoPregunta);

                foreach ($rlCampoProducto as $campo) {

                    // Los tipo select y checkbox pueden contener más de un (1) dato.
                    if ($campo['selector'] == 'select' || $campo['selector'] == 'checkbox') {
                        $this->datos[$campo['nombre']][] = $campo['valor'];

                    // Datos normales.
                    } else {
                        $this->datos[$campo['nombre']] = $campo['valor'];
                    }
                }
                $producto = $this->centralConsulta->producto($this->datoPregunta);
                $this->datos['categoria'][] = $producto[0]['categoria_producto'];
                $this->datos['unidadManejo'][] = $producto[0]['unidad_manejo'];
                $this->datos['ubicacionInventario'][] = $producto[0]['ubicacion_inventario'];
                //$this->datos['tipo_producto'][] = $producto[0]['tipo_producto'];
                //$this->datos['clase_producto'][] = $producto[0]['clase_producto'];

                //print "<pre>"; print_r($this->datos); print "</pre>\n";
            }

            if ($this->idFormulario == '8' && $this->datoPregunta != '') {

                $datosCampo = $this->centralConsulta->datosUbicacionInventario($this->datoPregunta);
                $this->datos['codigo'] = $datosCampo[0]['codigo'];
                $this->datos['nombre'] = $datosCampo[0]['nombre'];
                $this->datos['direccion'] = $datosCampo[0]['direccion'];

                //print "<pre>"; print_r($this->datos); print "</pre>\n";
            }

            if ($this->idFormulario == '9' && $this->datoPregunta != '') {

                $datosCampo = $this->centralConsulta->datosUnidadManejo($this->datoPregunta);
                $this->datos['codigo'] = $datosCampo[0]['codigo'];
                $this->datos['abreviatura'] = $datosCampo[0]['abreviatura'];
                $this->datos['descripcion'] = $datosCampo[0]['descripcion'];

                //print "<pre>"; print_r($this->datos); print "</pre>\n";
            }

            if ($this->idFormulario == '10' && $this->datoPregunta != '') {

                $datosCampo = $this->centralConsulta->datosDocumento($this->datoPregunta);
                $this->datos['abreviacion'] = $datosCampo[0]['abreviacion'];
                $this->datos['descripcion'] = $datosCampo[0]['descripcion'];
                $this->datos['cambioExistencia'] = $datosCampo[0]['cambio_existencia'];

                //print "<pre>"; print_r($this->datos); print "</pre>\n";
            }

            if ($this->idFormulario == '11' && $this->datoPregunta != '') {

                $datosCampo = $this->centralConsulta->datosAfectacionInventario($this->datoPregunta);
                $this->datos['documento'][] = $datosCampo[0]['documento'];
                $this->datos['tercero'][] = $datosCampo[0]['tercero'];
                $this->datos['fecha'] = $datosCampo[0]['fecha'];
                $this->datos['noDocumento'] = $datosCampo[0]['no_documento'];
                $this->datos['facturaCompra'] = $datosCampo[0]['factura_compra'];
                $this->datos['observaciones'] = $datosCampo[0]['observaciones'];


                //print "<pre>"; print_r($this->datos); print "</pre>\n";
            }

            if ($this->tablaRelacion == 'rl_empresa_campos' && $this->datoPregunta != '') {
                $rlCampoEmpresa = $this->centralConsulta->rlCampoEmpresa($this->datoPregunta);

                foreach ($rlCampoEmpresa as $campo) {

                    // Los tipo select y checkbox pueden contener más de un (1) dato.
                    if ($campo['selector'] == 'select' || $campo['selector'] == 'checkbox') {
                        $this->datos[$campo['nombre']][] = $campo['valor'];

                    // Datos normales.
                    } else {
                        $this->datos[$campo['nombre']] = $campo['valor'];
                    }
                }
                $empresa = $this->centralConsulta->empresa($this->datoPregunta);
                $this->datos['nit'] = $empresa[0]['nit'];
                $this->datos['municipio'][] = $empresa[0]['municipio'];
                $this->datos['activo'][] = $empresa[0]['activo'];
            }

            if ($this->tablaRelacion == 'rl_servicios_campos' && $this->datoPregunta != '') {
                $rlCampoServicios = $this->centralConsulta->rlCampoServicios($this->datoPregunta);
                foreach ($rlCampoServicios as $campo) {
                    // Los tipo select y checkbox pueden contener más de un (1) dato.
                    if ($campo['selector'] == 'select' || $campo['selector'] == 'checkbox') {
                        $this->datos[$campo['nombre']][] = $campo['valor'];
                    // Datos normales.
                    } else {
                        $this->datos[$campo['nombre']] = $campo['valor'];
                    }
                }
                $serviciosPlan = $this->centralConsulta->serviciosPlan($this->datoPregunta);
                foreach ($serviciosPlan as $campo) {
                    $this->datos['planes'][] = $campo['valor'];
                }
            }

            if ($this->tablaRelacion == 'rl_plan_campos' && $this->datoPregunta != '') {
                $datosCampo = $this->centralConsulta->datosPlan($this->datoPregunta);
                $this->datos['novedad'][] = $datosCampo[0]['novedad'];
                $rlCampoPlan = $this->centralConsulta->rlCampoPlan($this->datoPregunta);
                foreach ($rlCampoPlan as $campo) {
                    // Los tipo select y checkbox pueden contener más de un (1) dato.
                    if ($campo['selector'] == 'select' || $campo['selector'] == 'checkbox') {
                        $this->datos[$campo['nombre']][] = $campo['valor'];
                    // Datos normales.
                    } else {
                        $this->datos[$campo['nombre']] = $campo['valor'];
                    }
                }
            }

            if ($this->idFormulario == '22' && $this->datoPregunta != '') {
                $datosCampo = $this->centralConsulta->datosZona($this->datoPregunta);
                $this->datos['codigo'] = $datosCampo[0]['codigo'];
                $this->datos['nombre'] = $datosCampo[0]['nombre'];
                $zonaSector = $this->centralConsulta->zonaSector($this->datoPregunta);
                foreach ($zonaSector as $campo) {
                    $this->datos['sectores'][] = $campo['valor'];
                }
            }

            if ($this->idFormulario == '23' && $this->datoPregunta != '') {
                $datosCampo = $this->centralConsulta->datosSector($this->datoPregunta);
                $this->datos['codigo'] = $datosCampo[0]['codigo'];
                $this->datos['nombre'] = $datosCampo[0]['nombre'];
            }

            if ($this->idFormulario == '24' && $this->datoPregunta != '') {

                $datosCampo = $this->centralConsulta->datosNovedad($this->datoPregunta);
                $this->datos['codigo'] = $datosCampo[0]['codigo'];
                $this->datos['nombre'] = $datosCampo[0]['nombre'];
                $this->datos['valor'] = $datosCampo[0]['valor'];
                $this->datos['tipoNovedad'][] = $datosCampo[0]['tipo_novedad'];
                $this->datos['tipoNaturaleza'][] = $datosCampo[0]['tipo_naturaleza'];
                //print "<pre>"; print_r($datosCampo); print "</pre>\n";
            }

            if ($this->idFormulario == '25' && $this->datoPregunta != '') {

                $datosCampo = $this->centralConsulta->datosPeriodo($this->datoPregunta);
                $this->datos['desde'] = $datosCampo[0]['desde'];
                $this->datos['hasta'] = $datosCampo[0]['hasta'];
                $this->datos['corte'] = $datosCampo[0]['corte'];
                $this->datos['prorrateo'] = $datosCampo[0]['prorrateo'];
                $this->datos['nombre'] = $datosCampo[0]['nombre'];
                $this->datos['activo'][] = $datosCampo[0]['activo'];
                //print "<pre>"; print_r($datosCampo); print "</pre>\n";
            }

            if ($this->idFormulario == '27' && $this->datoPregunta != '') {
                $this->datos['zonaSector'][] = $this->datoPregunta;
                //print "<pre>"; print_r($datosCampo); print "</pre>\n";
            }

            if ($this->idFormulario == '28' && $this->datoPregunta != '') {
                $datosCampo = $this->centralConsulta->datosPucEmpresa($this->datoPregunta);
                $this->datos['codigo'] = $datosCampo[0]['codigo'];
                $this->datos['cuenta'] = $datosCampo[0]['cuenta'];
                $this->datos['centroCosto'] = $datosCampo[0]['centro_costo'];
                $this->datos['tercero'] = $datosCampo[0]['tercero'];
                $this->datos['clasificacionCuenta'][] = $datosCampo[0]['clasificacion_cuenta'];
                $this->datos['niif'][] = $datosCampo[0]['niif'];
                $this->datos['idTercero'][] = $datosCampo[0]['id_tercero'];
                $this->datos['activo'][] = $datosCampo[0]['activo'];
                $this->datos['grupoCuenta'][] = $datosCampo[0]['grupo_cuenta'];
                $this->datos['tipoCuenta'][] = $datosCampo[0]['tipo_cuenta'];
                $this->datos['baseGravable'] = $datosCampo[0]['base_gravable'];
                $this->datos['tarifaImpuesto'] = $datosCampo[0]['tarifa_impuesto'];
            }

            if ($this->idFormulario == '29' && $this->datoPregunta != '') {
                $datosCampo = $this->centralConsulta->datosPucCuenta($this->datoPregunta);
                $this->datos['codigo'] = $datosCampo[0]['codigo'];
                $this->datos['cuenta'] = $datosCampo[0]['cuenta'];
            }

            if ($this->idFormulario == '30' && $this->datoPregunta != '') {
                $datosCampo = $this->centralConsulta->datosCentroCosto($this->datoPregunta);
                $this->datos['codigo'] = $datosCampo[0]['codigo'];
                $this->datos['nombre'] = $datosCampo[0]['nombre'];
            }

            if ($this->idFormulario == '31' && $this->datoPregunta != '') {
                $datosCampo = $this->centralConsulta->datosTipoDocumentoContable($this->datoPregunta);
                $this->datos['abreviatura'] = $datosCampo[0]['abreviatura'];
                $this->datos['descripcion'] = $datosCampo[0]['descripcion'];
                $this->datos['secuencia'] = ($this->centralConsulta->valorSecuencia("tipo_documento_contable_$_SESSION[empresa]_".$this->datoPregunta."_seq") - 1);

            }
            if ($this->idFormulario == '75' && $this->datoPregunta != '') {
                $datosCampo = $this->centralConsulta->datosPrefijo($this->datoPregunta);
                $this->datos['descripcionPrefijo'] = $datosCampo[0]['descripcion'];
                $this->datos['secuencia'] = ($this->centralConsulta->valorSecuencia("prefijo_$_SESSION[empresa]_".$this->datoPregunta."_seq") - 1);

            }

            if ($this->idFormulario == '32' && $this->datoPregunta != '') {
                $datosCampo = $this->centralConsulta->datosSoporteContable($this->datoPregunta);
                $this->datos['fechaDocumento'] = $datosCampo[0]['fecha_documento'];
                $this->datos['consecutivo'] = $datosCampo[0]['consecutivo'];
                $this->datos['tipoDocumentoContable'][] = $datosCampo[0]['tipo_documento_contable'];
            }

            if ($this->idFormulario == '33' && $this->datoPregunta != '') {
                $datosCampo = $this->centralConsulta->datosRecaudo($this->datoPregunta);
                $this->datos['ordenTrabajo'][] = $datosCampo[0]['orden_trabajo'];
                //$_REQUEST["ordenTrabajo"] = $datosCampo[0]['orden_trabajo'];
                $this->datos['mesPagado'] = $datosCampo[0]['mes_pagado'];
                $this->datos['fechaPago'] = $datosCampo[0]['fecha_pago'];
                $this->datos['valorPago'] = $datosCampo[0]['valor_pago'];
                $this->datos['registroOperacion'] = $datosCampo[0]['registro_operacion'];
                $this->datos['horaPago'] = $datosCampo[0]['hora_pago'];
                $this->datos['secuenciaPago'] = $datosCampo[0]['secuencia_pago'];
            }

            if ($this->idFormulario == '67' && $this->datoPregunta != '') {
                $datosCampo = $this->centralConsulta->datosServicio($this->datoPregunta);
                $this->datos['codigoServicio'] = $datosCampo[0]['codigo'];
                $this->datos['nombre'] = $datosCampo[0]['nombre'];
                $this->datos['tipo_servicio'][] = $datosCampo[0]['tipo_servicio'];
            }

            if ($this->tablaRelacion == 'rl_predio_campo' && $this->datoPregunta != '') {
                $rlCampoPredioServicioPublico = $this->centralConsulta->rlCampoPredioServicioPublico($this->datoPregunta);

                foreach ($rlCampoPredioServicioPublico as $campo) {

                    // Los tipo select y checkbox pueden contener más de un (1) dato.
                    if ($campo['selector'] == 'select' || $campo['selector'] == 'checkbox') {
                        $this->datos[$campo['nombre']][] = $campo['valor'];

                    // Datos normales.
                    } else {
                        $this->datos[$campo['nombre']] = $campo['valor'];
                    }
                }
                $predioServicioPublico = $this->centralConsulta->predioServicioPublico($this->datoPregunta);
                $this->datos['numeroMatricula'] = $predioServicioPublico[0]['numero_matricula'];
            }
            if ($this->idFormulario == '69' && $this->datoPregunta != '') {
                $datosCampo = $this->centralConsulta->datosEstrato($this->datoPregunta);
                $this->datos['descripcionEstrato'] = $datosCampo[0]['descripcion'];
                $this->datos['estrato'][] = $datosCampo[0]['estrato'];
            }
            if ($this->idFormulario == '70' && $this->datoPregunta != '') {
                $datosCampo = $this->centralConsulta->datosDescripcionPredio($this->datoPregunta);
                $this->datos['nombreDescripcionPredio'] = $datosCampo[0]['nombre'];
                $this->datos['descripcion_predio'][] = $datosCampo[0]['descripcion_predio'];
            }
            if ($this->idFormulario == '71' && $this->datoPregunta != '') {
                $datosCampo = $this->centralConsulta->datosCostoServicioPublico($this->datoPregunta);
                $this->datos['costoGm'] = $datosCampo[0]['costo_gm'];
                $this->datos['costoTm'] = $datosCampo[0]['costo_tm'];
                $this->datos['cargoFijo'] = $datosCampo[0]['cargo_fijo'];
                $this->datos['cargoVariable'] = $datosCampo[0]['cargo_variable'];
                $this->datos['cargoDistribucion'] = $datosCampo[0]['cargo_distribucion'];
                $this->datos['factorPoder'] = $datosCampo[0]['factor_poder'];
                $this->datos['cargoComercializacion'] = $datosCampo[0]['cargo_comercializacion'];
                $this->datos['factorCorreccion'] = $datosCampo[0]['factor_correccion'];
                $this->datos['costoPrestacioE1'] = $datosCampo[0]['costo_prestacion_e1'];
                $this->datos['costoPrestacioE2'] = $datosCampo[0]['costo_prestacion_e2'];
                $this->datos['costoPrestacion'] = $datosCampo[0]['costo_prestacion'];
                $this->datos['subsidioE1'] = $datosCampo[0]['subsidio_e1'];
                $this->datos['subsidioE2'] = $datosCampo[0]['subsidio_e2'];
                $this->datos['tarifaE1'] = $datosCampo[0]['tarifa_e1'];
                $this->datos['tarifaE2'] = $datosCampo[0]['tarifa_e2'];
                $this->datos['tarifaE3'] = $datosCampo[0]['tarifa_e3'];
                $this->datos['tarifaE4'] = $datosCampo[0]['tarifa_e4'];
                $this->datos['tarifaE5'] = $datosCampo[0]['tarifa_e5'];
                $this->datos['tarifaE6'] = $datosCampo[0]['tarifa_e6'];
                $this->datos['poderCalorifico'] = $datosCampo[0]['poder_calorifico'];
                $this->datos['fechaInicio'] = $datosCampo[0]['fecha_inicio'];
                $this->datos['fechaFin'] = $datosCampo[0]['fecha_fin'];
                $this->datos['resolucion'] = $datosCampo[0]['resolucion'];
                $this->datos['servicioCosto'][] = $datosCampo[0]['tipo_servicio'];
                $this->datos['costoTfr'] = $datosCampo[0]['costo_tfr'];
                $this->datos['costoTbl'] = $datosCampo[0]['costo_tbl'];
                $this->datos['costoTrt'] = $datosCampo[0]['costo_trt'];
                $this->datos['costoTte'] = $datosCampo[0]['costo_tte'];
                $this->datos['costoTdf'] = $datosCampo[0]['costo_tdf'];
                $this->datos['estratoAseo1'] = $datosCampo[0]['estrato_aseo1'];
                $this->datos['estratoAseo2'] = $datosCampo[0]['estrato_aseo2'];
                $this->datos['estratoAseo3'] = $datosCampo[0]['estrato_aseo3'];
                $this->datos['estratoAseo4'] = $datosCampo[0]['estrato_aseo4'];
                $this->datos['estratoAseo5'] = $datosCampo[0]['estrato_aseo5'];
                $this->datos['estratoAseo6'] = $datosCampo[0]['estrato_aseo6'];
                $this->datos['ppiAseo'] = $datosCampo[0]['ppi_aseo'];
                $this->datos['ppcAseo1'] = $datosCampo[0]['ppc_aseo1'];
                $this->datos['ppcAseo2'] = $datosCampo[0]['ppc_aseo2'];
                $this->datos['ppcAseo3'] = $datosCampo[0]['ppc_aseo3'];
                $this->datos['ppcAseo4'] = $datosCampo[0]['ppc_aseo4'];
                $this->datos['ppoeAseo'] = $datosCampo[0]['ppoe_aseo'];
                $this->datos['gpiAseo'] = $datosCampo[0]['gpi_aseo'];
                $this->datos['gpcAseo'] = $datosCampo[0]['gpc_aseo'];
                $this->datos['gpoAseo'] = $datosCampo[0]['gpo_aseo'];
                $this->datos['inmueblesDesocupados'] = $datosCampo[0]['inmuebles_desocupados_aseo'];
                $this->datos['cargoFijoAseo'] = $datosCampo[0]['cargo_fijo_aseo'];
                $this->datos['cargoFijoAlcantarillado'] = $datosCampo[0]['cargo_fijo_alcantarillado'];
                $this->datos['cargoFijoAcueducto'] = $datosCampo[0]['cargo_fijo_acueducto'];
                $this->datos['subsidioAaa1'] = $datosCampo[0]['subsidio_aaa1'];
                $this->datos['subsidioAaa2'] = $datosCampo[0]['subsidio_aaa2'];
                $this->datos['contribucionAaa'] = $datosCampo[0]['contribucion_aaa'];
                $this->datos['subsidioAaa3'] = $datosCampo[0]['subsidio_aaa3'];
                $this->datos['costoKwh'] = $datosCampo[0]['costo_kwh'];
                $this->datos['subsidioE1Energia'] = $datosCampo[0]['subsidio_e1_energia'];
                $this->datos['subsidioE2Energia'] = $datosCampo[0]['subsidio_e2_energia'];
                $this->datos['subsidioE3Energia'] = $datosCampo[0]['subsidio_e3_energia'];
                $this->datos['contribucionEnergia'] = $datosCampo[0]['contribucion_energia'];
                $this->datos['cargoFijoEnergia'] = $datosCampo[0]['cargo_fijo_energia'];
                $this->datos['contribucionGas'] = $datosCampo[0]['contribucion_gas'];
                $this->datos['cmtCosto'] = $datosCampo[0]['cmt'];
                $this->datos['cmtAcueducto'] = $datosCampo[0]['cmt_acueducto'];
                $this->datos['cmoAcueducto'] = $datosCampo[0]['cmo_acueducto'];
                $this->datos['cmiAcueducto'] = $datosCampo[0]['cmi_acueducto'];
                $this->datos['cargoFijoAcue1'] = $datosCampo[0]['cargo_fijo_acue1'];
                $this->datos['basicoAcue1'] = $datosCampo[0]['basico_acue1'];
                $this->datos['compAcue1'] = $datosCampo[0]['comp_acue1'];
                $this->datos['cargoFijoAcue2'] = $datosCampo[0]['cargo_fijo_acue2'];
                $this->datos['basicoAcue2'] = $datosCampo[0]['basico_acue2'];
                $this->datos['compAcue2'] = $datosCampo[0]['comp_acue2'];
                $this->datos['cargoFijoAcue3'] = $datosCampo[0]['cargo_fijo_acue3'];
                $this->datos['basicoAcue3'] = $datosCampo[0]['basico_acue3'];
                $this->datos['compAcue3'] = $datosCampo[0]['comp_acue3'];
                $this->datos['cargoFijoAcue4'] = $datosCampo[0]['cargo_fijo_acue4'];
                $this->datos['basicoAcue4'] = $datosCampo[0]['basico_acue4'];
                $this->datos['compAcue4'] = $datosCampo[0]['comp_acue4'];
                $this->datos['cargoFijoAcue5'] = $datosCampo[0]['cargo_fijo_acue5'];
                $this->datos['basicoAcue5'] = $datosCampo[0]['basico_acue5'];
                $this->datos['compAcue5'] = $datosCampo[0]['comp_acue5'];
                $this->datos['cargoFijoAcue6'] = $datosCampo[0]['cargo_fijo_acue6'];
                $this->datos['basicoAcue6'] = $datosCampo[0]['basico_acue6'];
                $this->datos['compAcue6'] = $datosCampo[0]['comp_acue6'];
                $this->datos['cargoFijoComercialAcue'] = $datosCampo[0]['cargo_fijo_comercial_acue'];
                $this->datos['basicoComercialAcue'] = $datosCampo[0]['basico_comercial_acue'];
                $this->datos['compComercialAcue'] = $datosCampo[0]['comp_comercial_acue'];
                $this->datos['cmtAlcantarillado'] = $datosCampo[0]['cmt_alcantarillado'];
                $this->datos['cmoAlcantarillado'] = $datosCampo[0]['cmo_alcantarillado'];
                $this->datos['cmiAlcantarillado'] = $datosCampo[0]['cmi_alcantarillado'];
                $this->datos['cargoFijoAlcan1'] = $datosCampo[0]['cargo_fijo_alcan1'];
                $this->datos['basicoAlcan1'] = $datosCampo[0]['basico_alcan1'];
                $this->datos['compAlcan1'] = $datosCampo[0]['comp_alcan1'];
                $this->datos['cargoFijoAlcan2'] = $datosCampo[0]['cargo_fijo_alcan2'];
                $this->datos['basicoAlcan2'] = $datosCampo[0]['basico_alcan2'];
                $this->datos['compAlcan2'] = $datosCampo[0]['comp_alcan2'];
                $this->datos['cargoFijoAlcan3'] = $datosCampo[0]['cargo_fijo_alcan3'];
                $this->datos['basicoAlcan3'] = $datosCampo[0]['basico_alcan3'];
                $this->datos['compAlcan3'] = $datosCampo[0]['comp_alcan3'];
                $this->datos['cargoFijoAlcan4'] = $datosCampo[0]['cargo_fijo_alcan4'];
                $this->datos['basicoAlcan4'] = $datosCampo[0]['basico_alcan4'];
                $this->datos['compAlcan4'] = $datosCampo[0]['comp_alcan4'];
                $this->datos['cargoFijoAlcan5'] = $datosCampo[0]['cargo_fijo_alcan5'];
                $this->datos['basicoAlcan5'] = $datosCampo[0]['basico_alcan5'];
                $this->datos['compAlcan5'] = $datosCampo[0]['comp_alcan5'];
                $this->datos['cargoFijoAlcan6'] = $datosCampo[0]['cargo_fijo_alcan6'];
                $this->datos['basicoAlcan6'] = $datosCampo[0]['basico_alcan6'];
                $this->datos['compAlcan6'] = $datosCampo[0]['comp_alcan6'];
                $this->datos['cargoFijoComercialAlcan'] = $datosCampo[0]['cargo_fijo_comercial_alcan'];
                $this->datos['basicoComercialAlcan'] = $datosCampo[0]['basico_comercial_alcan'];
                $this->datos['compComercialAlcan'] = $datosCampo[0]['comp_comercial_alcan'];
                $this->datos['cargoFijoOficialAcue'] = $datosCampo[0]['cargo_fijo_oficial_acue'];
                $this->datos['basicoOficialAcue'] = $datosCampo[0]['basico_oficial_acue'];
                $this->datos['cargoFijoOficialAlcan'] = $datosCampo[0]['cargo_fijo_oficial_alcan'];
                $this->datos['basicoOficialAlcan'] = $datosCampo[0]['basico_oficial_alcan'];
            }
            if ($this->idFormulario == '72' && $this->datoPregunta != '') {
                $datosCampo = $this->centralConsulta->datosLecturaServicio($this->datoPregunta);
                $this->datos['servicioLectura'][] = $datosCampo[0]['tipo_servicio'];
                $this->datos['predioLectura'][] = $datosCampo[0]['predio_servicio_publico'];
                $this->datos['lecturaAnterior'] = $datosCampo[0]['lectura_anterior'];
                $this->datos['lecturaActual'] = $datosCampo[0]['lectura_actual'];
                $this->datos['usuarioLectura'][] = $datosCampo[0]['usuario_lectura'];
                $this->datos['periodoLectura'] = $datosCampo[0]['periodo'];
                $this->datos['observacionLectura'] = $datosCampo[0]['observacion'];
                $this->datos['fechaLectura'] = $datosCampo[0]['fecha_lectura'];
                $this->datos['estadoMedidor'] = $datosCampo[0]['estado_medidor'];
                $this->datos['determinarConsumo'] = $datosCampo[0]['determinar_consumo'];
            }

            if ($this->idFormulario == '74' && $this->datoPregunta != '') {
                $datosCampo = $this->centralConsulta->datosConvenioBanco($this->datoPregunta);
                $this->datos['numConvenio'] = $datosCampo[0]['no_convenio'];
                $this->datos['bancoConvenio'][] = $datosCampo[0]['banco'];
                $this->datos['tipoCuentaConvenio'][] = $datosCampo[0]['tipo_cuenta_bancaria'];
                $this->datos['numCuentaConvenio'] = $datosCampo[0]['no_cuenta'];
            }
            if ($this->idFormulario == '78' && $this->datoPregunta != '') {
                $datosCampo = $this->centralConsulta->datosTextoFactura($this->datoPregunta);
                $this->datos['servicioTexto'][] = $datosCampo[0]['tipo_servicio'];
                $this->datos['textoInformativo'] = $datosCampo[0]['texto_informativo'];
                $this->datos['textoCabecera'] = $datosCampo[0]['texto_cabecera'];
                $this->datos['textoLey'] = $datosCampo[0]['texto_ley'];
            }

            if ($this->idFormulario == '79' && $this->datoPregunta != '') {
                $datosCampo = $this->centralConsulta->datosInteresReconexion($this->datoPregunta);
                $this->datos['porcentajeResidencial'] = $datosCampo[0]['interes_residencial'];
                $this->datos['porcentajeOtros'] = $datosCampo[0]['interes_otros'];
                $this->datos['reconexionAseo'] = $datosCampo[0]['reconexion_aseo'];
                $this->datos['reconexionAlcantarillado'] = $datosCampo[0]['reconexion_alcantarillado'];
                $this->datos['reconexionAcueducto'] = $datosCampo[0]['reconexion_acueducto'];
                $this->datos['reconexionEnergia'] = $datosCampo[0]['reconexion_energia'];
                $this->datos['reconexionGas'] = $datosCampo[0]['reconexion_gas'];
                $this->datos['numeroMesesCorte'] = $datosCampo[0]['corte'];
            }
            if ($this->idFormulario == '80' && $this->datoPregunta != '') {
                $datosCampo = $this->centralConsulta->datosAbonoFactura($this->datoPregunta);
                $this->datos['abonoPredio'][] = $datosCampo[0]['predio_servicio_publico'];
                $this->datos['servicioAbono'][] = $datosCampo[0]['tipo_servicio'];
                $this->datos['numFacturaAbono'][] = $datosCampo[0]['numero'];
                $this->datos['valorAbono'] = $datosCampo[0]['valor'];
            }
            if ($this->idFormulario == '81' && $this->datoPregunta != '') {
                $datosCampo = $this->centralConsulta->datosPagoAbono($this->datoPregunta);
                $this->datos['servicioPagoAbono'][] = $datosCampo[0]['tipo_servicio'];
                $this->datos['numeroAbono'][] = $datosCampo[0]['numero_abono'];
                $this->datos['fechaPago'] = $datosCampo[0]['fecha_pago'];
            }

            if ($this->idFormulario == '83' && $this->datoPregunta != '') {
                $datosCampo = $this->centralConsulta->datosRangoConsumoId($this->datoPregunta);
                $this->datos['servicioRango'][] = $datosCampo[0]['tipo_servicio'];
                $this->datos['consumoBasico'] = $datosCampo[0]['basico'];
                $this->datos['consumoComplementario'] = $datosCampo[0]['complementario'];
            }

            if ($this->idFormulario == '84' && $this->datoPregunta != '') {
                $datosCampo = $this->centralConsulta->datosRefacturacion($this->datoPregunta);
                $this->datos['periodoRefac'] = $datosCampo[0]['mes'];
                $this->datos['tipoRefactura'][] = $datosCampo[0]['aumento_descuento'];
                $this->datos['servicioRefac'][] = $datosCampo[0]['tipo_servicio'];
                $this->datos['facturaRefac'][] = $datosCampo[0]['numero_factura'];
                $this->datos['valorFacturaRefac'] = $datosCampo[0]['valor_factura'];
                $this->datos['valorRefac'] = $datosCampo[0]['valor_refactura'];
                $this->datos['observacionRefac'] = $datosCampo[0]['observacion'];
                $this->datos['causalRefacturacion'] = $datosCampo[0]['causal'];

            }

            if ($this->idFormulario == '85' && $this->datoPregunta != '') {
                $datosCampo = $this->centralConsulta->datosUsuarioPqr($this->datoPregunta);
                $this->datos['fechaPqr'] = $datosCampo[0]['fecha_pqr'];
                $this->datos['servicioPqr'][] = $datosCampo[0]['tipo_servicio'];
                $this->datos['predioPqr'][] = $datosCampo[0]['predio_servicio_publico'];
                $this->datos['tramitePqr'][] = $datosCampo[0]['tramite'];
                $this->datos['causalPqr'] = $datosCampo[0]['causal'];
                $this->datos['detallePqr'] = $datosCampo[0]['detalle'];
                $this->datos['mesFacturacionPqr'] = $datosCampo[0]['mes_facturacion'];
            }

            if ($this->idFormulario == '86' && $this->datoPregunta != '') {
                $datosCampo = $this->centralConsulta->datosRespuestaPqr($this->datoPregunta);
                $this->datos['fechaTraslado'] = $datosCampo[0]['fecha_traslado'];
                $this->datos['fechaRespPqr'] = $datosCampo[0]['fecha_respuesta'];
                $this->datos['respuestaPqr'][] = $datosCampo[0]['tipo_respuesta'];
                $this->datos['radicadoRespuesta'] = $datosCampo[0]['radicacion_respuesta'];
                $this->datos['fechaNotificacion'] = $datosCampo[0]['fecha_notificacion'];
                $this->datos['notificacionPqr'][] = $datosCampo[0]['tipo_notificacion'];
                $this->datos['observacionRespuesta'] = $datosCampo[0]['observacion'];
            }


            if (($this->idFormulario == '90' || $this->idFormulario == '91') && $this->datoPregunta != '') {
                $datosCampo = $this->centralConsulta->datosNotaCaracter($this->datoPregunta);
                $this->datos['codigo'] = $datosCampo[0]['codigo'];
                $this->datos['nombre'] = $datosCampo[0]['nombre'];
                $this->datos['detalle'] = $datosCampo[0]['detalle'];
                $this->datos['referencia'] = $datosCampo[0]['referencia'];
                $this->datos['clasificacion'] = $datosCampo[0]['clasificacion'];
                $this->datos['valor'] = $datosCampo[0]['valor'];
                $this->datos['fechaNota'] = $datosCampo[0]['fecha_nota'];
            }

            if ($this->tablaRelacion == 'rl_soporte_contable_campos' && $this->datoPregunta != '') {
                $rlCampoSoporteContable = $this->centralConsulta->rlCampoSoporteContable($this->datoPregunta);

                foreach ($rlCampoSoporteContable as $campo) {

                    // Los tipo select y checkbox pueden contener más de un (1) dato.
                    if ($campo['selector'] == 'select' || $campo['selector'] == 'checkbox') {
                        $this->datos[$campo['nombre']][] = $campo['valor'];

                    // Datos normales.
                    } else {
                        $this->datos[$campo['nombre']] = $campo['valor'];
                    }
                }

                $datosCampo = $this->centralConsulta->datosSoporteContable($this->datoPregunta);

                if (!isset($_REQUEST["enlazar"])) {
                    $this->datos['consecutivo'] = $datosCampo[0]['consecutivo'];
                    $this->datos['prefijo'][] = $datosCampo[0]['prefijo'];
                } else {
                    $this->datos['numeroFactura'] = $this->centralConsulta->completarConsecutivo($datosCampo[0]['consecutivo']);
                    $this->datos['documentoExterno'] = isset($this->datos['documentoExterno']) ? $this->datos['documentoExterno'] : $this->centralConsulta->completarConsecutivo($datosCampo[0]['consecutivo']);
                    if ($datosCampo[0]['prefijo'] != "" && $datosCampo[0]['prefijo'] != "0") {
                        $datosPrefijo = $this->centralConsulta->datosPrefijo($datosCampo[0]['prefijo']);
                        $this->datos['prefijoFactura'] = $datosPrefijo[0]['descripcion'];
                        $this->datos['prefijoDocumentoExterno'] = $datosPrefijo[0]['descripcion'];
                    }
                }
                $_REQUEST['registroPresupuestal'] = $datosCampo[0]['registro_presupuestal'];
                $_REQUEST['obligacionPresupuestal'] = $datosCampo[0]['obligacion_presupuestal'];

                $this->datos['terceroElabora'][] = $datosCampo[0]['tercero_elabora'];
                $this->datos['registroPresupuestal'][] = $datosCampo[0]['registro_presupuestal'];
                $this->datos['obligacionPresupuestal'][] = $datosCampo[0]['obligacion_presupuestal'];

                if (isset($_REQUEST["recurso"]) && !isset($_REQUEST["bitacoraSoporte"])) {
                    if ($_REQUEST["recurso"] != "475") {
                        unset($this->datos['terceroElabora']);
                        unset($this->datos['concepto']);
                        unset($this->datos['fechaDocumento']);
                    }
                }

                $this->datos['terceroRecibe'][] = $datosCampo[0]['tercero_recibe'];
                $this->datos['empresaSeleccionada'][] = $_SESSION["empresa"];

            }

            if ($this->tablaRelacion == 'rl_orden_trabajo_campos' && $this->datoPregunta != '') {
                if (isset($_REQUEST["vista"])) {
                    $rlCampoOrdenTrabajo = $this->centralConsulta->rlCampoOrdenTrabajoHistorial($this->datoPregunta);
                } else {
                    $rlCampoOrdenTrabajo = $this->centralConsulta->rlCampoOrdenTrabajo($this->datoPregunta);
                }
                foreach ($rlCampoOrdenTrabajo as $campo) {
                    // Los tipo select y checkbox pueden contener más de un (1) dato.
                    if ($campo['selector'] == 'select' || $campo['selector'] == 'checkbox') {
                        $this->datos[$campo['nombre']][] = $campo['valor'];
                    // Datos normales.
                    } else {
                        $this->datos[$campo['nombre']] = $campo['valor'];
                    }
                }
                //print "<pre>"; print_r($rlCampoOrdenTrabajo); print "</pre>\n";
                if (isset($_REQUEST["vista"])) {
                    $ordenTrabajo = $this->centralConsulta->bOrdenTrabajo($this->datoPregunta);
                    $this->datoPregunta = $ordenTrabajo[0]['orden_trabajo'];
                } else {
                    $ordenTrabajo = $this->centralConsulta->ordenTrabajo($this->datoPregunta);
                }
                $this->datos['ticket'] = $ordenTrabajo[0]['ticket'];
                $this->datos['usuario'][] = $ordenTrabajo[0]['usuario'];
                $this->datos['estadoTicket'][] = $ordenTrabajo[0]['estado_ticket'];
                $this->datos['sector'][] = $ordenTrabajo[0]['sector'];
                $this->datos['documentoContable'][] = $ordenTrabajo[0]['documento_contable'];
                $this->datos['prorrateo'] = $ordenTrabajo[0]['prorrateo'];
                $this->datos['asignado'][] = $ordenTrabajo[0]['asignado'];

                switch ($this->idFormulario) {
                    case '13':
                    case '14':
                        $this->datos['tercero'] = $ordenTrabajo[0]['tercero'];
                        break;
                    case '17':
                        $this->datos['tercero'][] = $ordenTrabajo[0]['tercero'];
                        $this->datos['ordenTrabajo'][] = $ordenTrabajo[0]['orden_trabajo'];
                        if ($ordenTrabajo[0]['tipo_orden_trabajo'] == "1" || $ordenTrabajo[0]['tipo_orden_trabajo'] == "2") {
                            unset($this->datos['estadoTicket']);
                            unset($this->datos['ticket']);
                            unset($this->datos['usuario']);
                            $this->datos['usuario'][] = $_SESSION['usuario'];
                        }
                        break;
                    case '18':
                        $this->datos['tercero'][] = $ordenTrabajo[0]['tercero'];
                        $this->datos['ordenTrabajo'][] = $ordenTrabajo[0]['orden_trabajo'];
                        if ($ordenTrabajo[0]['tipo_orden_trabajo'] == "3") {
                            unset($this->datos['estadoTicket']);
                            unset($this->datos['ticket']);
                            unset($this->datos['usuario']);
                            $this->datos['usuario'][] = $_SESSION['usuario'];
                        }
                        break;
                    case '19':
                        $this->datos['tercero'][] = $ordenTrabajo[0]['tercero'];
                        $this->datos['ordenTrabajo'][] = $ordenTrabajo[0]['orden_trabajo'];
                        if ($ordenTrabajo[0]['tipo_orden_trabajo'] == "4" || isset($_REQUEST["nuevo"])) {
                            unset($this->datos['estadoTicket']);
                            unset($this->datos['ticket']);
                            unset($this->datos['usuario']);
                            $this->datos['usuario'][] = $_SESSION['usuario'];
                        }
                        break;
                    case '20':
                        $this->datos['tercero'][] = $ordenTrabajo[0]['tercero'];
                        $this->datos['ordenTrabajo'][] = $ordenTrabajo[0]['orden_trabajo'];
                        if ($ordenTrabajo[0]['tipo_orden_trabajo'] == "4" || isset($_REQUEST["nuevo"])) {
                            unset($this->datos['estadoTicket']);
                            unset($this->datos['ticket']);
                            unset($this->datos['usuario']);
                            $this->datos['usuario'][] = $_SESSION['usuario'];
                        }
                        break;
                    case '21':
                        $this->datos['tercero'][] = $ordenTrabajo[0]['tercero'];
                        $this->datos['ordenTrabajo'][] = $ordenTrabajo[0]['orden_trabajo'];
                        if ($ordenTrabajo[0]['tipo_orden_trabajo'] == "4" || isset($_REQUEST["nuevo"])) {
                            unset($this->datos['estadoTicket']);
                            unset($this->datos['ticket']);
                            unset($this->datos['usuario']);
                            $this->datos['usuario'][] = $_SESSION['usuario'];
                        }
                        break;
                    case '26':
                        $this->datos['tercero'][] = $ordenTrabajo[0]['tercero'];
                        $this->datos['ordenTrabajo'][] = $ordenTrabajo[0]['orden_trabajo'];
                        break;

                }
                $rlCampo = $this->centralConsulta->rlCampo($ordenTrabajo[0]['tercero']);
                foreach ($rlCampo as $campo) {
                    //if ($campo['nombre'] == "direccion" || $campo['nombre'] == "telefono") {
                        // Los tipo select y checkbox pueden contener más de un (1) dato.
                        if ($campo['selector'] == 'select' || $campo['selector'] == 'checkbox') {
                            $this->datos[$campo['nombre']][] = $campo['valor'];
                        // Datos normales.
                        } else {
                            $this->datos[$campo['nombre']] = $campo['valor'];
                        }
                    //}
                }
                $tercero = $this->centralConsulta->tercero($ordenTrabajo[0]['tercero']);
                $this->datos['nit'] = $tercero[0]['nit'];
                $this->datos['municipio'][] = $tercero[0]['municipio'];
                $this->datos['digitoVerificacion'] = $tercero[0]['digito_verificacion'];
                $this->datos['cedulaTercero'] = $tercero[0]['identificacion'];
                $this->datos['nombres'] = $tercero[0]['nombre'];
                $this->datos['apellidos'] = $tercero[0]['apellido'];
                $this->datos['razonSocial'] = $tercero[0]['razon_social'];
                $this->datos['cedulaRepresentante'] = $tercero[0]['identificacion'];
                $this->datos['representanteLegal'] = $tercero[0]['nombre'] . " " . $tercero[0]['apellido'];
                $this->datos['municipio'][] = $tercero[0]['municipio'];
                //print "<pre>"; print_r($this->datos); print "</pre>\n";
            }

            /*
             * Nuevo Código OpenLab
             */

            // fomulario tabla nomina_datos
            if ($this->idFormulario == '102' && $this->datoPregunta == 'anio') {
                $datosCampo = $this->centralConsulta->CargarNominaDatosAnioAnterior();
                foreach ($datosCampo[0] as $key => $value) {
                    if($this->centralConsulta->getSelector('nomina_datos',$key) != 'select')
                        $this->datos[$key] = $datosCampo[0][$key];
                    else {
                        $this->datos[$key] = array("0" => $datosCampo[0][$key]);
                    }
                }
                $this->datos['nomina_datos'] = 0;
                $this->datos['anio'] = array("0" => 0);
            }

            // formulario tabla comisión
            if ($this->idFormulario == '105' && $this->datoPregunta != '') {
                $datosCampo = $this->centralConsulta->buscarNominaComision($this->datoPregunta);
                foreach ($datosCampo[0] as $key => $value) {
                    $selector = $this->centralConsulta->getSelector('nomina_comision',$key);
                    if($selector == 'select' ||  $selector == 'checkbox' )
                        $this->datos[$key][] = $datosCampo[0][$key];
                    else {
                        $this->datos[$key] = $datosCampo[0][$key];
                    }
                }
            }

            // formulario tabla horas extras
            if ($this->idFormulario == '104' && $this->datoPregunta != '') {
                $datosCampo = $this->centralConsulta->buscarHorasExtras($this->datoPregunta);
                foreach ($datosCampo[0] as $key => $value) {
                    if($this->centralConsulta->getSelector('nomina_horas_extras',$key) != 'select')
                        $this->datos[$key] = $datosCampo[0][$key];
                    else {
                        $this->datos[$key] = array("0" => $datosCampo[0][$key]);
                    }
                }
            }

            // formulario tabla nómina datos
            if ($this->idFormulario == '102' && $this->datoPregunta != '' && $this->datoPregunta != 'anio') {
                $datosCampo = $this->centralConsulta->buscarNominaDatos($this->datoPregunta);
                foreach ($datosCampo[0] as $key => $value) {
                    if($this->centralConsulta->getSelector('nomina_datos',$key) != 'select')
                        $this->datos[$key] = $datosCampo[0][$key];
                    else {
                        $this->datos[$key] = array("0" => $datosCampo[0][$key]);
                    }
                }
            }

            // formulario tabla nómina cesantías
            if ($this->idFormulario == '107' && $this->datoPregunta != '') {
                $datosCampo = $this->centralConsulta->buscarNominaCesantias($this->datoPregunta);
                foreach ($datosCampo[0] as $key => $value) {
                    if($this->centralConsulta->getSelector('nomina_cesantias',$key) != 'select')
                        $this->datos[$key] = $value;
                    else {
                        $this->datos[$key] = array("0" => $value);
                    }
                }
            }

            // formulario tabla nómina vacaciones
            if ($this->idFormulario == '108' && $this->datoPregunta != '') {
                $datosCampo = $this->centralConsulta->buscarNominaVacaciones($this->datoPregunta);
                foreach ($datosCampo[0] as $key => $value) {
                    if($this->centralConsulta->getSelector('nomina_vacacion',$key) != 'select')
                        $this->datos[$key] = $value;
                    else {
                        $this->datos[$key] = array("0" => $value);
                    }
                }
            }

            if ($this->idFormulario == '111' && $this->datoPregunta != '') {
                $datosCampo = $this->centralConsulta->buscarCargo($this->datoPregunta);
                foreach ($datosCampo[0] as $key => $value) {
                    if($this->centralConsulta->getSelector('cargo',$key) != 'select')
                        $this->datos[$key] = $value;
                    else {
                        $this->datos[$key] = array("0" => $value);
                    }
                }
            }

            if ($this->idFormulario == '112' && $this->datoPregunta != '') {
                $datosCampo = $this->centralConsulta->buscarCentroTrabajo($this->datoPregunta);
                foreach ($datosCampo[0] as $key => $value) {
                    if($this->centralConsulta->getSelector('centro_trabajo',$key) != 'select')
                        $this->datos[$key] = $value;
                    else {
                        $this->datos[$key] = array("0" => $value);
                    }
                }
            }

            // formulario ausentismos : 113
            if ($this->idFormulario == '113' && $this->datoPregunta != '') {
                $datosCampo = $this->centralConsulta->buscarAusentismos($this->datoPregunta);
                foreach ($datosCampo[0] as $key => $value) {
                    $selector = $this->centralConsulta->getSelector('nomina_ausentismo',$key);
                    if($selector == 'select' ||  $selector == 'checkbox' )
                        $this->datos[$key][] = $datosCampo[0][$key];

                    else {
                        $this->datos[$key] = $datosCampo[0][$key];
                    }
                }
            }

            // formulario tabla descuentos
            if ($this->idFormulario == '114' && $this->datoPregunta != '') {
                $datosCampo = $this->centralConsulta->buscarDescuentos($this->datoPregunta);
                foreach ($datosCampo[0] as $key => $value) {
                    if($this->centralConsulta->getSelector('nomina_descuentos',$key) != 'select')
                        $this->datos[$key] = $datosCampo[0][$key];
                    else {
                        $this->datos[$key] = array("0" => $datosCampo[0][$key]);
                    }
                }
            }

            // formulario grado salarial : 120
            if ($this->idFormulario == '120' && $this->datoPregunta != '') {
                $datosCampo = $this->centralConsulta->buscarMesRetroactividad($this->datoPregunta);
                $this->datos['anio'] = array("0" => $this->datoPregunta);
                $this->datos['mes'] = array('0' => $datosCampo );
            }

            // Formulario Tipo de Vinculación
            if ($this->idFormulario == '121' && $this->datoPregunta != '') {
                $datosCampo = $this->centralConsulta->getRecursoById($this->datoPregunta);
                $this->datos['descripcionVinculacion'] = $datosCampo;
            }

            // Formulario Causales de Retiro
            if ($this->idFormulario == '122' && $this->datoPregunta != '') {
                $datosCampo = $this->centralConsulta->getRecursoById($this->datoPregunta);
                $this->datos['descripcionCausalRetiro'] = $datosCampo;
            }

            // Tabla Nomina_contrato
            if ($this->idFormulario == '101' && $this->datoPregunta != '') {
                $datosCampo = $this->centralConsulta->getContrato ($this->datoPregunta);
                foreach ($datosCampo[0] as $key => $value){

                    if($this->centralConsulta->getSelector('nomina_contrato',$key) != 'select')
                        $this->datos[$key] = $datosCampo[0][$key];
                    else {
                        $this->datos[$key] = array("0" => $datosCampo[0][$key]);
                    }
                 }
            }

            // Tabla contratos_empleado
            if ($this->idFormulario == '103' && $this->datoPregunta != '') {
                $datosCampo = $this->centralConsulta->getContratosEmpleado($this->datoPregunta);
                if($datosCampo !=null){
                    foreach ($datosCampo[0] as $key => $value) {
                        $selector = $this->centralConsulta->getSelector('rl_nomina_contratos_empleado',$key);
                        if($selector == 'select' || $selector == 'checkbox')
                            $this->datos[$key] = array("0" => $datosCampo[0][$key]);
                        else {
                            $this->datos[$key] = $datosCampo[0][$key];
                        }
                    }
                }
                else {
                    $this->datos['empleado'] = $this->datoPregunta;
                    $this->datos['rl_nomina_contratos_empleado'] = null;
                }
            }

            if ($this->idFormulario == '146' && $this->datoPregunta != '') {
                $datosCampo = $this->centralConsulta->radicadoConfiguracionSistema($this->datoPregunta);
                $this->datos['tipoDocumentoContable'][] = $datosCampo[0]['tipo_documento_contable'];
                $this->datos['tipoComprobante'][] = $datosCampo[0]['tipo_comprobante'];
                $this->datos['ceroCompletar'][] = $datosCampo[0]['cero_completar'];
                $this->datos['variable1'][] = $datosCampo[0]['variable1'];
                $this->datos['variable2'][] = $datosCampo[0]['variable2'];
                $this->datos['variable3'][] = $datosCampo[0]['variable3'];
                $this->datos['variable4'][] = $datosCampo[0]['variable4'];
                $this->datos['variable5'][] = $datosCampo[0]['variable5'];
                $this->datos['variable6'][] = $datosCampo[0]['variable6'];
                $this->datos['variable7'][] = $datosCampo[0]['variable7'];
                $this->datos['variable8'][] = $datosCampo[0]['variable8'];
                $this->datos['variable9'][] = $datosCampo[0]['variable9'];
                $this->datos['variable10'][] = $datosCampo[0]['variable10'];
            }

            if ($this->idFormulario == '147' && $this->datoPregunta != '') {
                $datosCampo = $this->centralConsulta->chequeraSistema($this->datoPregunta);
                $datosSecuencia = $this->centralConsulta->datosSecuencia("chequera_{$this->datoPregunta}_seq");
                if ($datosSecuencia[0]["last_value"] == "1" && $datosSecuencia[0]["is_called"] == "f") {
                    $datosSecuencia[0]["last_value"] = 0;
                }
                $this->datos['codigo'] = $datosCampo[0]['codigo'];
                $this->datos['nombre'] = $datosCampo[0]['nombre'];
                $this->datos['prefijo'] = $datosCampo[0]['prefijo'];
                $this->datos['desde'] = $datosSecuencia[0]['min_value'];
                $this->datos['hasta'] = $datosSecuencia[0]['max_value'];
                $this->datos['ultimo'] = $datosSecuencia[0]['last_value'];
                $this->datos['pucEmpresa'][] = $datosCampo[0]['puc_empresa'];
            }

            if ($this->idFormulario == '190' && $this->datoPregunta != '') {
                $datosCampo = $this->centralConsulta->datosImpuesto($this->datoPregunta);
                $this->datos['nombreImpuesto'] = $datosCampo[0]['nombre'];
                $impuestoCuenta = $this->centralConsulta->impuestoCuenta($this->datoPregunta);
                foreach ($impuestoCuenta as $campo) {
                    $this->datos['impuestoPuc'][] = $campo['valor'];
                }
            }

            if ($this->idFormulario == '192' && $this->datoPregunta != '') {
                $datosCampo = $this->centralConsulta->datosRevelacion($this->datoPregunta);
                $this->datos['anioRevelacion'] = $datosCampo[0]['anio'];
            }

        }

        public function contexto() {

        }

        public function crearFormulario() {
            $seccionFormulario = $this->centralConsulta->seccionFormulario($this->idFormulario);
            $campoFormulario = $this->centralConsulta->campoFormulario($this->idFormulario);
            $valida = '';
            $atributos = '';
            $funcion = '';
            $opciones = '';
            $valorCampo = '';
            $dataSelect = '';
            $contador = 0;
            $codigos = Array();
            $html = '';
            if (!isset($_REQUEST["bitacoraSoporte"])) {
                $html .= '<form action="' . $this->actionFormulario . '" method="post" name="' . $this->nombreFormulario . '" id="' . $this->nombreFormulario . '" class="form-aplication" enctype="multipart/form-data" accept-charset="UTF-8" data-idformulario="' . $this->idFormulario . '"  data-contexto="' . $this->contextoFormulario . '">';
            }
//
            // Construimos las secciones.

            foreach ($seccionFormulario as $seccion) {
                $html .= '<fieldset id="seccion_'.$seccion['seccion'].'">';
                $html .= '<legend>' . $seccion['seccion'] . '</legend>';

                // Construimos los campos.
                foreach ($campoFormulario as &$campo) {

                    $valida = '';
                    $atributos = '';
                    $funcion = '';
                    $opciones = '';
                    $valorCampo = '';
                    $dataSelect = 'data-placeholder="Seleccione Una Opción"';

                    // El campo pertenece a esa sección.
                    if ($seccion['seccion'] == $campo['seccion']) {

                        // Tipo de campo.
                        switch ($campo['selector']) {
                            case 'text':
                            case 'password':
                            case 'url':
                            case 'tel':
                            case 'email':
                            case 'number':
                            case 'file':
                            case 'range':
                            case 'color':
                            case 'date':
                            case 'datetime':
                            case 'datetime-local':
                            case 'time':
                            case 'week':
                            case 'month':

                                if ($campo['nombre'] == "horaPago" ) {
                                    $html .= '<div class="field checkbox_label"></div>';
                                }
                                if ($campo['nombre'] == "fechaVisita" ) {
                                    $html .= '<div class="field checkbox_label"></div>';
                                }

                                $campoValidacion = $this->centralConsulta->campoValidacion($campo['campo_formulario'], $this->idFormulario);
                                $campoAtributo = $this->centralConsulta->campoAtributo($campo['campo_formulario'], $this->idFormulario);

                                // Construimos la validación.
                                foreach ($campoValidacion as $validacion) {
                                    $valida .= $validacion['validacion'] . ' ';
                                }
                                trim($valida);

                                // Construimos los atributos.
                                foreach ($campoAtributo as $atributo) {
                                    $atributos .= $atributo['atributo'] . '="' . $atributo['valor'] . '" ';
                                }
                                trim($atributos);

                                // Tiene valor ya guardado o no.
                                if (array_key_exists($campo['nombre'], $this->datos) === true) {
                                    $valorCampo = $this->datos[$campo['nombre']];

                                } else {
                                    switch ($campo['nombre']) {
                                        case 'ticket':
                                            $valorCampo = substr(md5(time()), 0, 6);
                                            break;
                                        case 'fechaDocumento':
                                            $valorCampo = DATE('Y-m-d');
                                            if (isset($_REQUEST["recurso"]) && !isset($_REQUEST["bitacoraSoporte"])) {
                                                if ($_REQUEST["recurso"] != 469) {
                                                    $valorCampo = '';
                                                }
                                            }
                                            break;
                                        case 'mesFacturacion':
                                            if (isset($_REQUEST["mes"])) {
                                                $valorCampo = $_REQUEST["mes"];
                                            } else {
                                                $valorCampo = DATE('Y-m');
                                            }
                                            break;
                                        default:
                                            $valorCampo = $campo['valor'];
                                    }
                                }
                                if ($campo['nombre'] == "bancoPago") {
                                    $html .= '<div class="field checkbox_label">';
                                    $html .= '<label>Cuenta Registrada para Efectuar Pagos:</label>';
                                    $html .= '</div>';
                                }
                                if ($campo['nombre'] == "potencia") {
                                    $html .= '<div class="field checkbox_label">';
                                    $html .= '<label>Parametros de Señal:</label>';
                                    $html .= '</div>';
                                }
                                if ($campo['nombre'] == "placaNumero") {
                                    $html .= '<div class="field checkbox_label">';
                                    $html .= '<label><h2>Activos Fijos</h2></label>';
                                    $html .= '</div>';
                                }

                                switch ($campo['campo_formulario']) {
                                    case '353':
                                    case '354':
                                    case '355':
                                    case '357':
                                    case '358':
                                    case '359':
                                    case '364':
                                    case '365':
                                    case '366':
                                    case '367':
                                        $html .= '<div class="field button">';
                                        break;
                                    case '10':
                                    case '25':
                                    case '34':
                                    case '68':
                                    case '82':
                                    case '83':
                                    case '126':
                                    case '127':
                                    case '128':
                                    case '129':
                                    case '130':
                                    case '131':
                                    case '495':
                                    case '496':
                                    case '501':
                                    case '508':
                                    case '509':
                                    case '510':
                                    case '526':
                                    case '530':
                                    case '531':
                                    case '532':
                                    case '533':
                                    case '534':
                                    case '535':
                                    case '539':
                                    case '546':
                                    case '547':
                                    case '548':
                                    case '549':
                                    case '550':
                                    case '551':
                                    case '553':
                                    case '554':
                                    case '555':
                                    case '556':
                                    case '557':
                                    case '558':
                                        $html .= '<div class="field text" style="width: 20% !important;">';
                                        break;
                                    case '2':
                                    case '24':
                                    case '53':
                                    case '633':
                                    case '634':
                                    case '641':
                                    case '642':
                                    case '1520':
                                    case '1521':
                                    case '1522':
                                    case '1523':
                                    case '1524':
                                    case '1525':
                                    case '1526':
                                    case '1527':
                                    case '1528':
                                    case '1529':
                                    case '1530':
                                    case '1531':
                                    case '1532':
                                    case '1533':
                                    case '1534':
                                    case '1535':
                                    case '1536':
                                    case '1537':
                                    case '1538':
                                    case '1539':
                                    case '1540':
                                    case '1541':
                                    case '1567':
                                    case '1568':
                                    case '1569':
                                    case '1570':
                                    case '1571':
                                    case '1588':
                                    case '1589':
                                    case '1590':
                                    case '1591':
                                    case '1592':
                                    case '1593':
                                    case '1594':
                                    case '1648':
                                    case '1649':
                                    case '1650':
                                    case '1651':
                                    case '1652':
                                    case '1653':
                                    case '1685':
                                    case '1687':
                                    case '1688':
                                    case '1694':
                                    case '1696':
                                    case '1697':
                                    case '1698':
                                    case '1699':
                                    case '1700':
                                    case '1701':
                                    case '1702':
                                    case '1703':
                                    case '1704':
                                    case '1705':
                                    case '1706':
                                    case '1707':
                                    case '1708':
                                    case '1709':
                                    case '1710':
                                    case '1711':
                                    case '1712':
                                    case '1713':
                                    case '1714':
                                    case '1715':
                                    case '1716':
                                    case '1717':
                                    case '1718':
                                    case '1719':
                                    case '1722':
                                    case '1723':
                                    case '1738':
                                    case '1739':
                                    case '1740':
                                    case '1741':
                                    case '1742':
                                    case '1743':
                                    case '1744':
                                    case '1745':
                                    case '1746':
                                    case '1747':
                                    case '1748':
                                    case '1749':
                                    case '1750':
                                    case '1751':
                                    case '1752':
                                    case '1753':
                                    case '1754':
                                    case '1755':
                                    case '1756':
                                    case '1757':
                                    case '1758':
                                    case '1759':
                                    case '1760':
                                    case '1761':
                                    case '1762':
                                    case '1763':
                                    case '1764':
                                    case '1765':
                                    case '1776':
                                    case '1778':
                                    case '1779':
                                    case '1781':
                                    case '1364':
                                    case '1365':
                                        $html .= '<div class="field text" style="width: 21.5% !important;">';
                                        break;
                                    case '439':
                                    case '464':
                                    case '373':
                                    case '608':
                                    case '609':
                                        $html .= '<div class="field text" style="width: 10% !important;">';
                                    break;
                                    case '1572':
                                    case '1573':
                                    case '1574':
                                    case '1575':
                                    case '1576':
                                    case '1577':
                                    case '1578':
                                    case '1579':
                                    case '1580':
                                    case '1581':
                                    case '1582':
                                    case '1583':
                                    case '1584':
                                    case '1585':
                                    case '1586':
                                    case '1587':
                                        $html .= '<div class="field text" style="width: 10.3% !important;">';
                                    break;
                                    case 'x':
                                        $html .= '<div class="field text" style="width: 68% !important;">';
                                        break;
                                    case '3078':
                                    case '3070':
                                          $html .= '<div class="field text" style="width: 28% !important;">';
                                        break;
                                    case '423':
                                    case '424':
                                    case '425':
                                    case '427':
                                    case '428':
                                    case '429':
                                        if ($campo['campo_formulario'] == "427") {
                                            $atributos .= ' data-registro="' . $valorCampo . '"';
                                        }
                                        if ($campo['campo_formulario'] == "429") {
                                            $atributos .= ' data-secuencia="' . $valorCampo . '"';
                                        }
                                         $html .= '<div class="field text" style="width: 25% !important;">';
                                        break;
                                    case '1050':
                                        $html .= '<div class="field select" style="width: 10% !important;">';
                                        break;
                                    case '1051':
                                        $html .= '<div class="field text" style="width: 16% !important;">';
                                        break;
                                    case '1052':
                                        $html .= '<div class="field text" style="width: 20% !important;">';
                                        break;
                                    case '1105':
                                        $html .= '<div class="field text" style="width: 10% !important;">';
                                        break;
                                    case '1106':
                                    case '3074':
                                    case '3081':
                                        $html .= '<div class="field text" style="width: 15% !important;">';
                                        break;
                                    case '1107':
                                        $html .= '<div class="field text" style="width: 20% !important;">';
                                        break;
                                    case '374':
                                        $html .= '<div class="field text" style="width: 33% !important;">';
                                        break;
                                    case '2004':
                                        $html .= '<div class="field text" style="width: 32% !important;">';
                                        break;
                                    case '2005':
                                        $html .= '<div class="field text" style="width: 11% !important;">';
                                        break;
                                    default:
                                        $html .= '<div class="field text">';
                                }

                                if ($campo['selector'] == "time") {
                                    $step = "step='1'";
                                } else {
                                    $step = "step='any'";
                                    if ($campo['selector'] == "email") {
                                        $step .= " style= 'width: 100%;'";
                                    }
                                }

                                $html .= '<label for="' . $campo['nombre'] . '"><span>' . $campo['etiqueta'] . '</span></label>';
                                $html .= '<input ' . $step . ' type="' . $campo['selector'] . '" id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '" value="' . $valorCampo . '" ' . $atributos . ' class="' . $valida . '" />';
                                $html .= '</div>';

                                if ($campo['nombre'] == "descuento") {
                                    $html .= '<div class="field checkbox_label"></div>';
                                }
                                if ($campo['campo_formulario'] == "367") {
                                    $html .= '<div class="field textarea" style="visibility: hidden;">';
                                    $html .= '<label ><span>A</span></label>';
                                    $html .= '<textarea style="height: 0px !important;"></textarea>';
                                    $html .= '</div>';
                                }
                                if ($campo['campo_formulario'] == "382") {
                                    $html .= '<input type="hidden" id="puc" name="puc" value="' . $_REQUEST["puc"] . '">';
                                    $html .= '<input type="hidden" id="nombre" name="nombre" value="' . $_REQUEST["nombre"] . '">';
                                    $html .= '<input type="hidden" id="descripcion" name="descripcion" value="' . $_REQUEST["descripcion"] . '">';

                                }

                            break;

                            case 'textarea':
                                $campoValidacion = $this->centralConsulta->campoValidacion($campo['campo_formulario'], $this->idFormulario);
                                $campoAtributo = $this->centralConsulta->campoAtributo($campo['campo_formulario'], $this->idFormulario);

                                // Construimos la validación.
                                foreach ($campoValidacion as $validacion) {
                                    $valida .= $validacion['validacion'] . ' ';
                                }
                                trim($valida);

                                // Construimos los atributos.
                                foreach ($campoAtributo as $atributo) {
                                    $atributos .= $atributo['atributo'] . '="' . $atributo['valor'] . '" ';
                                }
                                trim($atributos);

                                // Tiene valor ya guardado o no.
                                if (array_key_exists($campo['nombre'], $this->datos) === true) {
                                    $valorCampo = $this->datos[$campo['nombre']];

                                } else {
                                    $valorCampo = $campo['valor'];
                                }

                                $html .= '<div class="field textarea" style="width:91.5% !important;">';
                                $html .= '<label for="' . $campo['nombre'] . '"><span>' . $campo['etiqueta'] . '</span></label>';
                                $html .= '<textarea id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '" ' . $atributos . ' class="' . $valida . '">' . $valorCampo . '</textarea>';
                                $html .= '</div>';

                            break;
                            case 'hidden':
                                // Tomar el valor por defecto o el identificador de formulario.
                            //$this->tablaRelacion == 'rl_tercero_campos' &&
                                if ($this->datoPregunta != '' && $campo['nombre'] != 'tipoOrdenTrabajo') {
                                    if (isset($this->datos[$campo['nombre']])) {
                                        $valorCampo = $this->datos[$campo['nombre']];
                                    } else {
                                        if (!isset($_REQUEST["enlazar"])) {
                                            $valorCampo = $this->datoPregunta;
                                        } else {
                                            $valorCampo = "";
                                        }
                                    }
                                } else {
                                    $valorCampo = $campo['valor'];
                                }

                                $html .= '<input type="' . $campo['selector'] . '" id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '" value="' . $valorCampo . '" />';
                                if ($campo['campo'] == "363") {
                                    $html .= '<input type="hidden" id="tipoDocumentoContable" name="tipoDocumentoContable" value="' . $_REQUEST["tipoDocumentoContable"] . '">';
                                    $nombreTipoDocumentoContable = $this->centralConsulta->datosTipoDocumentoContable($_REQUEST["tipoDocumentoContable"]);
                                    $html .= '<input type="hidden" id="nombreTipoDocumentoContable" name="nombreTipoDocumentoContable" value="' . $nombreTipoDocumentoContable[0]["nombre"] . '">';
                                    if (!isset($_REQUEST["enlazar"])) {
                                        $enlazar = 0;
                                    } else {
                                        $enlazar = $this->datoPregunta;
                                    }
                                    if (isset($_REQUEST["forzarConsecutivo"])) {
                                        $html .= '<input type="hidden" id="forzarConsecutivo" name="forzarConsecutivo" value="' . $_REQUEST["forzarConsecutivo"] . '">';
                                    }
                                    $html .= '<input type="hidden" id="utilizado" name="utilizado" value="' . $enlazar . '">';
                                    if ($this->tablaRelacion == 'rl_soporte_contable_campos' && ($this->datoPregunta == '' || $_REQUEST["tipoDocumentoContable"] == '25')) {
                                        $html .= '<input type="hidden" id="elaboraTercero" name="elaboraTercero" value="' . $_SESSION["tercero"] . '">';
                                    }
                                }
                            break;
                            case 'submit':
                            case 'reset':
                            case 'button':
                                $campoAtributo = $this->centralConsulta->campoAtributo($campo['campo_formulario'], $this->idFormulario);

                                // Construimos los atributos.
                                foreach ($campoAtributo as $atributo) {
                                    $atributos .= $atributo['atributo'] . '="' . $atributo['valor'] . '" ';
                                }
                                trim($atributos);

                                $html .= '</fieldset><fieldset><div class="field button">';
                                $html .= '<input type="' . $campo['selector'] . '" id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '" value="' . $campo['valor'] . '" ' . $atributos . ' />';
                                $html .= '</div></fieldset>';

                            break;

                            case 'radio':
                                if ($campo['etiqueta_secundaria'] == "Sociedad Anonima (S.A.)") {
                                    $html .= '<div class="field checkbox_label">';
                                    $html .= '<label><h2>Información Tributaria</h2></label>';
                                    $html .= '</div>';
                                }
                                if ($campo['etiqueta_secundaria'] == "Contado") {
                                    $html .= '<div class="field checkbox_label">';
                                    $html .= '<label><h2>Información Comercial</h2></label>';
                                    $html .= '</div>';
                                }

                                $campoValidacion = $this->centralConsulta->campoValidacion($campo['campo_formulario'], $this->idFormulario);
                                $campoAtributo = $this->centralConsulta->campoAtributo($campo['campo_formulario'], $this->idFormulario);

                                // Construimos la validación.
                                foreach ($campoValidacion as $validacion) {
                                    $valida .= $validacion['validacion'] . ' ';
                                }
                                trim($valida);

                                // Un solo label para los radios.
                                if (array_key_exists($campo['campo'], $codigos) === false) {
                                    $codigos[$campo['campo']] = $campo['campo'];
                                    $contador = 1;

                                    switch ($campo['campo']) {
                                        case '334':

                                            break;
                                        default:
                                        $html .= '<div class="field radio_label">';
                                        $html .= '<label for="' . $campo['nombre'] . $contador . '"><span>' . $campo['etiqueta'] . '</span></label>';
                                        $html .= '</div>';

                                    }

                                } else {
                                    $contador++;
                                }

                                // Tiene valor ya guardado o no.
                                if (isset($this->datos[$campo['nombre']])) {
                                    if ($this->datos[$campo['nombre']] == $campo['valor']) {
                                        $valorCampo = 'checked="checked"';

                                    } else {
                                        $valorCampo = '';
                                    }
                                } else {
                                    $valorCampo = '';
                                }

                                if ($campo['funcion'] == '') {
                                    // Construimos los atributos.
                                    foreach ($campoAtributo as $atributo) {
                                        switch ($atributo['atributo']) {
                                            case 'checked':
                                                // Colocar checked si no esta guardado por defecto y posee el atributo.
                                                if ($atributo['atributo'] == 'checked' && array_key_exists($campo['nombre'], $this->datos) === false) {
                                                    $atributos .= $atributo['atributo'] . '="' . $atributo['valor'] . '" ';
                                                }
                                                break;
                                            default:
                                                $atributos .= $atributo['atributo'] . '="' . $atributo['valor'] . '" ';
                                                break;
                                        }
                                    }
                                    trim($atributos);

                                    switch ($campo['campo_formulario']) {
                                        case '375':
                                        case '376':
                                        case '377':
                                        case '378':

                                            $html .= '<div class="field radio" style="width: 4% !important;">';
                                            break;
                                        default:
                                            $html .= '<div class="field radio">';
                                            break;
                                    }

                                    $html .= '<label for="' . $campo['nombre'] . $contador . '"><span>' . $campo['etiqueta_secundaria'] . '</span></label>';
                                    $html .= '<input ' . $valorCampo . ' type="' . $campo['selector'] . '" id="' . $campo['nombre'] . $contador . '" name="' . $campo['nombre'] . '" value="' . $campo['valor'] . '" ' . $atributos . ' class="' . $valida . '" />';
                                    $html .= '</div>';
                                } else {
                                    // Construimos los atributos.
                                    $valor = '';
                                    foreach ($campoAtributo as $atributo) {
                                        $atributos .= $atributo['atributo'] . ' ';
                                        $valor = $atributo['valor'];
                                    }
                                    trim($atributos);

                                    $x = $campo['funcion'];
                                    $funcion = $this->centralConsulta->$x();
                                    // Construimos las opciones del datalist.
                                    foreach ($funcion as $opcion) {
                                        $html .= '<div class="field radio">';
                                        $html .= '<label for="' . $campo['nombre'] . $contador . '"><span>' . $opcion[1] . '</span></label>';
                                        if ($valor == $opcion[0]) {
                                            $html .= '<input ' . $valorCampo . ' type="' . $campo['selector'] . '" id="' . $campo['nombre'] . $contador . '" name="' . $campo['nombre'] . '[]" value="' . $opcion[0] . '" ' . $atributos . ' class="' . $valida . '" />';
                                        } else {
                                            $html .= '<input ' . $valorCampo . ' type="' . $campo['selector'] . '" id="' . $campo['nombre'] . $contador . '" name="' . $campo['nombre'] . '[]" value="' . $opcion[0] . '"  class="' . $valida . '" />';
                                        }
                                        $html .= '</div>';
                                        $contador++;
                                    }
                                }

                            break;

                            case 'checkbox':
                                $campoValidacion = $this->centralConsulta->campoValidacion($campo['campo_formulario'], $this->idFormulario);
                                $campoAtributo = $this->centralConsulta->campoAtributo($campo['campo_formulario'], $this->idFormulario);

                                // Construimos la validación.
                                foreach ($campoValidacion as $validacion) {
                                    $valida .= $validacion['validacion'] . ' ';
                                }
                                trim($valida);

                                // Un solo label para los checkbox.
                                if (array_key_exists($campo['campo'], $codigos) === false) {
                                    $codigos[$campo['campo']] = $campo['campo'];
                                    $contador = 1;
                                    switch ($campo['campo_formulario']) {
                                        case '435':
                                        case '438':
                                        case '440':
                                        case '453':
                                        case '454':
                                        case '455':
                                        case '456':
                                        case '460':
                                        case '463':
                                        case '465':
                                        case '478':
                                        case '479':
                                        case '480':
                                        case '481':
                                        case '491':
                                        case '492':
                                        case '493':
                                        case '494':
                                        case '497':
                                        case '502':
                                        case '503':
                                        case '511':
                                        case '512':
                                        case '513':
                                        case '527':
                                        case '529':
                                        case '537':
                                        case '538':
                                        case '578':
                                        case '579':
                                        case '599':
                                        case '600':
                                        case '601':
                                        case '627':
                                        case '628':
                                        case '629':
                                        case '651':
                                        case '652':
                                        case '653':
                                        case '669':
                                        case '670':
                                        case '671':
                                        case '689':
                                        case '690':
                                        case '706':
                                        case '707':
                                        case '708':
                                        case '724':
                                        case '743':
                                        case '744':
                                        case '764':
                                        case '765':
                                        case '785':
                                        case '786':
                                        case '804':
                                        case '805':
                                        case '825':
                                        case '826':
                                        case '846':
                                        case '847':
                                        case '866':
                                        case '867':
                                        case '887':
                                        case '888':
                                        case '908':
                                        case '909':
                                        case '928':
                                        case '929':
                                        case '947':
                                        case '948':
                                        case '959':
                                        case '960':
                                        case '976':
                                        case '977':
                                        case '996':
                                        case '997':
                                        case '998':
                                        case '2019':
                                        case '2020':
                                        case '2021':
                                        case '2038':
                                        case '2039':
                                        case '2050':
                                        case '2051':
                                        case '2062':
                                        case '2063':
                                        case '2082':
                                        case '2083':
                                        case '2094':
                                        case '2095':
                                        case '2112':
                                        case '2113':
                                        case '2125':
                                        case '2148':
                                        case '2149':
                                        case '2221':
                                        case '2278':
                                        case '2279':
                                        case '2280':
                                        case '2301':
                                        case '2302':
                                        case '2303':
                                        case '3017':
                                        case '455':

                                            break;
                                        // checkbox ausentismos
                                        case '1140':
                                             $html .= '<div class="field checkbox" style="width: 30% !important;">';
                                             $html .= '<label for="' . $campo['nombre'] . $contador . '"><span>' . $campo['etiqueta'] . '</span></label>';
                                            $html .= '</div>';
                                            break;
                                        case '1158':
                                            $html .= '<div class="field checkbox" style="width: 30% !important;">';
                                             $html .= '<label for="' . $campo['nombre'] . $contador . '"><span>' . $campo['etiqueta'] . '</span></label>';
                                            $html .= '</div>';
                                            break;
                                        case '1139':
                                            $html .= '<div class="field checkbox" style="width: 30% !important;">';
                                             $html .= '<label for="' . $campo['nombre'] . $contador . '"><span>' . $campo['etiqueta'] . '</span></label>';
                                            $html .= '</div>';
                                            break;

                                        // fin checkbox ausentismos
                                        default:
                                        $html .= '<div class="field checkbox_label">';
                                        $html .= '<label for="' . $campo['nombre'] . $contador . '"><span>' . $campo['etiqueta'] . '</span></label>';
                                        $html .= '</div>';
                                    }

                                } else {
                                    $contador++;
                                }

                                if ($campo['funcion'] == '') {
                                    // Tiene valor ya guardado o no.
                                    if (isset($this->datos[$campo['nombre']])) {
                                        for ($i = 0, $tamano = sizeof($this->datos[$campo['nombre']]); $i < $tamano; $i++) {
                                            if ($this->datos[$campo['nombre']][$i] == $campo['valor']) {
                                                $valorCampo = 'checked="checked"';
                                                break;
                                            } else {
                                                $valorCampo = '';
                                            }
                                        }
                                    }
                                    // Construimos los atributos.
                                    foreach ($campoAtributo as $atributo) {
                                        $atributos .= $atributo['atributo'] . '="' . $atributo['valor'] . '" ';
                                    }
                                    trim($atributos);
                                    switch ($campo['campo_formulario']) {
                                        case '435':
                                        case '460':
                                            $html .= '<div class="field text" style="width: 21% !important; text-align: center !important;">';
                                            break;
                                        default:
                                            $html .= '<div class="field checkbox">';
                                    }
                                    $html .= '<label for="' . $campo['nombre'] . $contador . '"><span>' . $campo['etiqueta_secundaria'] . '</span></label>';
                                    $html .= '<input ' . $valorCampo . ' type="' . $campo['selector'] . '" id="' . $campo['nombre'] . $contador . '" name="' . $campo['nombre'] . '[]" value="' . $campo['valor'] . '" ' . $atributos . ' class="' . $valida . '" />';
                                    $html .= '</div>';
                                } else {
                                    $x = $campo['funcion'];
                                    $funcion = $this->centralConsulta->$x();
                                    // Construimos los atributos.
                                    $valor = '';
                                    foreach ($campoAtributo as $atributo) {
                                        $atributos .= $atributo['atributo'] . ' ';
                                        $valor = $atributo['valor'];
                                    }
                                    trim($atributos);

                                    // Construimos las opciones del datalist.
                                    foreach ($funcion as $opcion) {
                                        // Tiene valor ya guardado o no.
                                        if (isset($this->datos[$campo['nombre']])) {
                                            for ($i = 0, $tamano = sizeof($this->datos[$campo['nombre']]); $i < $tamano; $i++) {
                                                if ($this->datos[$campo['nombre']][$i] == $opcion[0]) {
                                                    $valorCampo = 'checked="checked"';
                                                    break;
                                                } else {
                                                    $valorCampo = '';
                                                }
                                            }
                                        }

                                        $html .= '<div class="field checkbox">';
                                        $html .= '<label for="' . $campo['nombre'] . $contador . '"><span>' . $opcion[1] . '</span></label>';
                                        if ($valor == $opcion[0]) {
                                            $html .= '<input ' . $valorCampo . ' type="' . $campo['selector'] . '" id="' . $campo['nombre'] . $contador . '" name="' . $campo['nombre'] . '[]" value="' . $opcion[0] . '" ' . $atributos . ' class="' . $valida . '" />';
                                        } else {
                                            $html .= '<input ' . $valorCampo . ' type="' . $campo['selector'] . '" id="' . $campo['nombre'] . $contador . '" name="' . $campo['nombre'] . '[]" value="' . $opcion[0] . '" class="' . $valida . '" />';
                                        }
                                        $html .= '</div>';
                                        $contador++;
                                    }
                                }
                            break;

                            case 'search':
                                $campoValidacion = $this->centralConsulta->campoValidacion($campo['campo_formulario'], $this->idFormulario);
                                $campoAtributo = $this->centralConsulta->campoAtributo($campo['campo_formulario'], $this->idFormulario);

                                // Necesita llenar opciones.
                                if ($campo['funcion'] != '') {
                                    $x = $campo['funcion'];
                                    $funcion = $this->centralConsulta->$x();
                                }

                                // Construimos la validación.
                                foreach ($campoValidacion as $validacion) {
                                    $valida .= $validacion['validacion'] . ' ';
                                }
                                trim($valida);

                                // Construimos los atributos.
                                foreach ($campoAtributo as $atributo) {
                                    if ($atributo['atributo'] == 'list'){$id = $atributo['valor'];}

                                    $atributos .= $atributo['atributo'] . '="' . $atributo['valor'] . '" ';
                                }
                                trim($atributos);

                                // Construimos las opciones del datalist.
                                foreach ($funcion as $opcion) {
                                    $opciones .= '<option value="' . $opcion[0] . '">' . $opcion[1] . '</option>';
                                }

                                $html .= '<div class="field search">';
                                $html .= '<label for="' . $campo['nombre'] . '"><span>' . $campo['etiqueta'] . '</span></label>';
                                $html .=' <input type="' . $campo['selector'] . '" id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '" value="' . $campo['valor'] . '" ' . $atributos . ' class="' . $valida . '" />';
                                $html .= '<datalist id="' . $id . '">';
                                $html .= $opciones;
                                $html .= '</datalist>';
                                $html .= '</div>';

                            break;

                            case 'select':

                                switch ($campo['campo_formulario']) {
                                    case '447':
                                    case '472':
                                        $html .= '<div class="field checkbox_label">';
                                        $html .= '<label><h2>Nómina</h2></label>';
                                        $html .= '</div>';
                                        break;
                                    case '545':
                                        $html .= '<div class="field checkbox_label">';
                                        $html .= '<label><h2>Contabilidad Local</h2></label>';
                                        $html .= '</div>';
                                        break;
                                    case '552':
                                        $html .= '<div class="field checkbox_label">';
                                        $html .= '<label><h2>Contabilidad NIIF</h2></label>';
                                        $html .= '</div>';
                                        break;
                                }

                                $campoAtributo = $this->centralConsulta->campoAtributo($campo['campo_formulario'], $this->idFormulario);

                                // Necesita llenar opciones.
                                $funcion = array();
                                if ($campo['funcion'] != '') {
                                    $_REQUEST["campoFormularioFuncion"] = $campo['campo_formulario'];
                                    if ($campo['mostrar'] != "0") {
                                        $_REQUEST["start"] = 0;
                                        $_REQUEST["length"] = $campo['mostrar'];
                                        $_REQUEST["idSelect"] =  isset($this->datos[$campo['nombre']]) ? $this->datos[$campo['nombre']][0] : 0;
                                    }
                                    switch ($campo['campo_formulario']) {
                                        case '542':
                                            $x = $campo['funcion'];
                                            $funcion = $this->centralConsulta->$x(date("Y-m-d"));
                                            break;
                                        default:
                                            $x = $campo['funcion'];
                                            $funcion = $this->centralConsulta->$x();
                                    }
                                    unset($_REQUEST["campoFormularioFuncion"]);
                                    unset($_REQUEST["start"]);
                                    unset($_REQUEST["length"]);
                                }
                                // Construimos los atributos.
                                foreach ($campoAtributo as $atributo) {

                                    // Caso especial para placeholder con chosen.
                                    if ($atributo['atributo'] == 'placeholder') {
                                        $dataSelect = 'data-placeholder="' . $atributo['valor'] . '"';
                                        //$opciones .= '<option value="0"></option>';

                                    } else {
                                        $atributos .= $atributo['atributo'] . '="' . $atributo['valor'] . '" ';

                                    }
                                }
                                trim($atributos);

                                switch ($campo['campo_formulario']) {
                                    case '395':
                                    case '422':
                                    case '570':
                                    case '571':
                                    case '577':
                                    case '591':
                                    case '592':
                                    case '598':
                                    case '619':
                                    case '620':
                                    case '626':
                                    case '643':
                                    case '644':
                                    case '662':
                                    case '663':
                                    case '2284':
                                    case '2285':
                                    case '2289':
                                    case '2290':
                                    case '680':
                                    case '681':
                                    case '699':
                                    case '700':
                                    case '717':
                                    case '718':
                                    case '735':
                                    case '736':
                                    case '742':
                                    case '755':
                                    case '756':
                                    case '762':
                                    case '776':
                                    case '777':
                                    case '783':

                                    case '795':
                                    case '796':
                                    case '802':

                                    case '816':
                                    case '817':
                                    case '823':

                                    case '837':
                                    case '838':
                                    case '844':

                                    case '858':
                                    case '859':
                                    case '865':

                                    case '878':
                                    case '879':
                                    case '885':

                                    case '899':
                                    case '900':
                                    case '906':

                                    case '920':
                                    case '921':
                                    case '927':

                                    case '938':
                                    case '945':
                                    case '939':

                                    case '957':

                                    case '969':
                                    case '970':

                                    case '988':
                                    case '989':
                                    case '995':
                                    case '1801':
                                    case '1802':


                                    case '2009':
                                    case '2266':
                                    case '2010':
                                    case '2016':

                                    case '2030':
                                    case '2031':
                                    case '2037':

                                    case '2048':

                                    case '2060':

                                    case '2074':
                                    case '2075':
                                    case '2081':

                                    case '2092':


                                    case '2104':
                                    case '2105':
                                    case '2111':

                                    case '2122':
                                    case '2124':
                                    case '2203':
                                    case '2140':
                                    case '2141':
                                    case '2147':
                                    case '2152':
                                    case '1011':
                                    case '1050':
                                    case '1062':
                                    case '1054':
                                    case '1058':
                                    case '1083':
                                //formulario vacaciones
                                    case '1092':
                                    case '1093':
                                    case '1095':
                                //fin vacaciones
                                    case '1034':
                                    case '1074':
                                    case '1075':
                                   // case '1002':
                                    //case '1004':
                                    ///case '1006':
                                    case '1111':
                                // formulario ausentismo
                                    case '1130':
                                    case '1131':
                                    case '1137':
                                //fin formulario ausentismo
                                 // formulario descuento
                                    case '1144':
                                    case '1145':
                                    case '1146':
                                    case '1147':
                                    case '1150':
                                // fin formulario descuento
                                    case '1046':
                                    case '1042':
                                    case '1031':
                                    case '1721':
                                    case '2211':
                                    case '2212':

                                    //Select formulario de asobancaria manual.
                                    case '6202':
                                    case '6203':
                                    case '6209':
                                    case '6210':
                                    case '7045':
                                    case '2239':
                                    case '2003':
                                    case '7180':


                                        $opciones .= '<option value=""></option>';
                                        break;
                                    case '418':
                                        $opciones .= '<option value="0">No Aplica</option>';
                                        break;
                                    case '1800':
                                        if ($_REQUEST["identificador"] != "") {
                                            $opciones .= '<option value="0">NO APLICA</option>';
                                        } else {
                                            $opciones .= '<option value=""></option><option value="0">NO APLICA</option>';
                                        }
                                        break;
                                    case '568':
                                    case '590':
                                    case '618':
                                    case '640':
                                    case '661':
                                    case '679':
                                    case '698':
                                    case '716':
                                    case '650':
                                    case '732':
                                    case '752':
                                    case '773':
                                    case '794':
                                    case '813':
                                    case '834':
                                    case '855':
                                    case '875':
                                    case '896':
                                    case '917':
                                    case '937':
                                    case '956':
                                    case '968':
                                    case '985':
                                    case '1599':
                                    case '1621':
                                    case '2006':
                                    case '2018':
                                    case '2029':
                                    case '2047';
                                    case '2059';
                                    case '2071';
                                    case '2091';
                                    case '2103';
                                    case '2121';
                                    case '2139':
                                    case '2210':
                                    case '2227':
                                    case '2272':
                                    case '2296':
                                    case '1082':
                                    case '16':
                                    case '59':
                                    case '2018':
                                    case '2233':

                                        break;
                                    case '3032':
                                    $opciones .= '<option value=""></option>';
                                        break;
                                    case '2237':
                                    $opciones .= '<option value="0">Seleccione Una Opción</option>';
                                        break;
                                    case '3049':
                                    $opciones .= '<option value=""></option>';
                                        break;
                                    case '3056':
                                    case '3064':

                                    $opciones .= '<option value=""></option>';
                                        break;

                                    default:
                                        if ($campo['campo'] != 736 && $campo['campo'] != 737 && $campo['campo'] != 715) {
                                            $opciones .= '<option value="0"></option>';
                                        }
                                }

                                // Construimos las opciones del select.
                                foreach ($funcion as $opcion) {
                                    // Tiene valor ya guardado o no.
                                    if (isset($this->datos[$campo['nombre']])) {
                                        for ($i = 0, $tamano = sizeof($this->datos[$campo['nombre']]); $i < $tamano; $i++) {
                                            if ($this->datos[$campo['nombre']][$i] == $opcion[0]) {
                                                $valorCampo = 'selected="selected"';
                                                break;

                                            } else {
                                                $valorCampo = '';
                                            }
                                        }
                                    } else {
                                        //$valorCampo = '';

                                            if ($campo['valor'] == $opcion[0]) {
                                                $valorCampo = 'selected="selected"';
                                            } else {
                                                $valorCampo = '';
                                            }
                                    }
                                    if ($valorCampo == 'selected="selected"') {
                                        $opciones .= '<option value="' . $opcion[0] . '" ' . $valorCampo . '>' . $opcion[1] . '</option>';
                                    } else {
                                        if (isset($_REQUEST["bitacora"])) {
                                            $opciones .= '<option value="' . $opcion[0] . '" ' . $valorCampo . '>' . $opcion[1] . '</option>';
                                        } else {
                                            if (!isset($_REQUEST["vista"])) {
                                                $opciones .= '<option value="' . $opcion[0] . '" ' . $valorCampo . '>' . $opcion[1] . '</option>';
                                            }
                                        }
                                    }
                                }
                                /*
                                if ($campo['nombre'] == "servicios1") {
                                    $html .= '<div class="field checkbox_label">';
                                    $html .= '<label>Servicios Requeridos:</label>';
                                    $html .= '</div>';
                                }
                                */// || $campo['campo_formulario'] == "48"   || $campo['campo_formulario'] == "18"
                                if ($campo['nombre'] == "planes" || $campo['campo_formulario'] == "263" || $campo['campo_formulario'] == "226" || $campo['campo_formulario'] == "280" || $campo['campo_formulario'] == "295" || $campo['campo_formulario'] == "307" || $campo['campo_formulario'] == "329" || $campo['campo_formulario'] == "335" || $campo['campo_formulario'] == "336" || $campo['campo_formulario'] == "337" || $campo['campo_formulario'] == "356" || $campo['campo_formulario'] == "363" || $campo['campo_formulario'] == "422" || $campo['campo_formulario'] == "519" || $campo['campo_formulario'] == "2306" || $campo['campo_formulario'] == "2203" || $campo['campo_formulario'] == "3026" || $campo['campo_formulario'] == "1367") {
                                    $html .= '<div class="field textarea">';
                                } else {

                                    switch ($campo['campo']) {
                                        case '371':
                                            $html .= '<div class="field select" style="display: none;">';
                                            break;
                                        case '17':
                                            $html .= '<div class="field text" style="width: 60% !important;">';
                                            break;
                                        case '376':
                                        case '377':
                                        case '378':
                                        case '2606':
                                            $html .= '<div class="field text" style="width: 33% !important;">';
                                            break;
                                        case '2617':
                                            $html .= '<div class="field select" style="width: 33% !important;">';
                                            break;
                                        case '18':
                                            $html .= '<div class="field text" style="width: 30% !important;">';
                                            break;
                                        case '2614':
                                            $html .= '<div class="field text" style="width: 10% !important;">';
                                            break;
                                        case '13':
                                            $html .= '<div class="field select" style="width: 21.5% !important;">';
                                            break;
                                        case '2642':
                                            $html .= '<div class="field select" style="width: 30.5% !important;">';
                                            break;
                                        case '372':
                                            if ($campo['campo_formulario'] == '2011') {
                                                $html .= '<div class="field select" style="width: 22% !important;">';
                                            } else {
                                                $html .= '<div class="field select" >';
                                            }
                                            break;
                                        case '365':
                                            if ($campo['campo_formulario'] == '2014') {
                                                $html .= '<div class="field select" style="width: 21% !important;">';
                                            } else {
                                                $html .= '<div class="field select" >';
                                            }
                                            break;
                                        case '-':
                                            $html .= '<div class="field text" style="width: 25% !important;">';
                                            break;
                                        default:
                                            $html .= '<div class="field select" >';
                                    }
                                }

                                switch ($campo['campo']) {
                                    case '356':
                                        if (isset($_REQUEST["vista"])) {
                                            $html .= '<label for="' . $campo['nombre'] . '"><span>' . $campo['etiqueta'] . '</span></label>';
                                        } else {
                                            $html .= '  <label for="' . $campo['nombre'] . '">
                                                            <span>' . $campo['etiqueta'] . '
                                                                <a id = "agregarPrefijo" href="javascript:void(0);">&nbsp;&nbsp;<img src="'.$this->rutaAplicacion->rutaAbsoluta.'app/public/imagenes/iconos/adicionar.png"></a>
                                                                <a id = "actualizarPrefijo" href="javascript:void(0);">&nbsp;&nbsp;<img src="'.$this->rutaAplicacion->rutaAbsoluta.'app/public/imagenes/iconos/actualizar.png"></a>
                                                            </span>
                                                        </label>';
                                        }
                                        break;
                                    case '605':
                                        if (isset($_REQUEST["vista"])) {
                                            $html .= '<label for="' . $campo['nombre'] . '"><span>' . $campo['etiqueta'] . '</span></label>';
                                        } else {
                                            $html .= '  <label for="' . $campo['nombre'] . '">
                                                            <span>' . $campo['etiqueta'] . '
                                                                <a id = "reporteRp" href="javascript:void(0);">&nbsp;&nbsp;<img src="'.$this->rutaAplicacion->rutaAbsoluta.'app/public/imagenes/iconos/excel.png" width="24" height="24"></a>
                                                                <span id = "valor_saldo_rp" style="color: red; font-size: xx-large; font-weight: bold;"></span>
                                                            </span>
                                                        </label>';
                                        }
                                        break;
                                    case '360':
                                    case '387':
                                    case '391':
                                    case '393':
                                    case '405':
                                    case '425':
                                    case '427':
                                        if (isset($_REQUEST["vista"])) {
                                            $html .= '<label for="' . $campo['nombre'] . '"><span>' . $campo['etiqueta'] . '</span></label>';
                                        } else {
                                            $html .= '  <label for="' . $campo['nombre'] . '">
                                                            <span>' . $campo['etiqueta'] . '
                                                                <a id = "agregarVendedor" href="javascript:void(0);">&nbsp;&nbsp;<img src="'.$this->rutaAplicacion->rutaAbsoluta.'app/public/imagenes/iconos/adicionar.png"></a>
                                                                <a id = "actualizarVendedor" href="javascript:void(0);">&nbsp;&nbsp;<img src="'.$this->rutaAplicacion->rutaAbsoluta.'app/public/imagenes/iconos/actualizar.png"></a>
                                                            </span>
                                                        </label>';
                                        }
                                        break;
                                    case '361':
                                    case '388':
                                    case '397':
                                    case '428':
                                    case '394':
                                        if (isset($_REQUEST["vista"])) {
                                            $html .= '<label for="' . $campo['nombre'] . '"><span>' . $campo['etiqueta'] . '</span></label>';
                                        } else {
                                            $html .= '  <label for="' . $campo['nombre'] . '">
                                                            <span>' . $campo['etiqueta'] . '
                                                                <a id = "agregarCliente" href="javascript:void(0);">&nbsp;&nbsp;<img src="'.$this->rutaAplicacion->rutaAbsoluta.'app/public/imagenes/iconos/adicionar.png"></a>
                                                                <a id = "actualizarCliente" href="javascript:void(0);">&nbsp;&nbsp;<img src="'.$this->rutaAplicacion->rutaAbsoluta.'app/public/imagenes/iconos/actualizar.png"></a>
                                                            </span>
                                                        </label>';
                                        }
                                        break;
                                    default:
                                        $html .= '<label for="' . $campo['nombre'] . '"><span>' . $campo['etiqueta'] . '</span></label>';
                                }

                                switch ($campo['campo']) {
                                    case '13':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select-divipola" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;
                                    case '371':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;
                                    case '736':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select-marca-vehiculo" ' . $dataSelect . ' ' . $atributos . ' >';
                                        if (isset($this->datos[$campo['nombre']][0])) {
                                            $tablaSelect = $this->centralConsulta->tablaSelect('marca_vehiculo', $this->datos[$campo['nombre']][0]);
                                            $html .= '<option value="' . $this->datos[$campo['nombre']][0] .'">' . $tablaSelect['nombre'] . '</option>';
                                        }
                                        break;
                                    case '737':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select-linea-vehiculo" ' . $dataSelect . ' ' . $atributos . ' >';
                                        if (isset($this->datos[$campo['nombre']][0])) {
                                            $tablaSelect = $this->centralConsulta->tablaSelect('linea_vehiculo', $this->datos[$campo['nombre']][0]);
                                            $html .= '<option value="' . $this->datos[$campo['nombre']][0] .'">' . $tablaSelect['nombre'] . '</option>';
                                        }
                                        break;
                                    case '7050':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;
                                    case '850':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select-tercero" ' . $dataSelect . ' ' . $atributos . ' >';
                                        if (isset($this->datos[$campo['nombre']][0])) {
                                            $tablaSelect = $this->centralConsulta->ajaxTercero($this->datos[$campo['nombre']][0],$_SESSION['empresa'],12);
                                            $html .= '<option value="' . $this->datos[$campo['nombre']][0] .'">' . $tablaSelect['nombre'] . '</option>';
                                        }
                                        break;
                                    case '106':
                                    case '144':
                                    case '316':
                                    case '317':
                                    case '327':
                                    case '360':
                                    case '361':
                                    case '387':
                                    case '388':
                                    case '391':
                                    case '393':
                                    case '394':
                                    case '397':
                                    case '398':
                                    case '405':
                                    case '425':
                                    case '427':
                                    case '428':
                                    case '433':
                                    case '2044':
                                    case '2617':
                                    case '2648':
                                    case '2653':
                                    case '2662':
                                    case '1144':
                                    case '7105':
                                    case '3007':
                                    case '3021':
                                    case '3015':
                                    case '7118':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select-tercero" style="width: 100% !important;" ' . $dataSelect . ' ' . $atributos . ' >';
                                        /*
                                        if (isset($this->datos[$campo['nombre']][0])) {
                                            $tablaSelect = $this->centralConsulta->ajaxTercero($this->datos[$campo['nombre']][0],$_SESSION['empresa'],12);
                                            $html .= '<option value="' . $this->datos[$campo['nombre']][0] .'">' . $tablaSelect['nombre'] . '</option>';
                                        }
                                        */
                                        break;
                                    case '891':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select-tercero" ' . $dataSelect . ' ' . $atributos . ' >';
                                        if (isset($this->datos[$campo['nombre']][0])) {
                                            $tablaSelect = $this->centralConsulta->ajaxTercero($this->datos[$campo['nombre']][0],$_SESSION['empresa'],12);
                                            $html .= '<option value="' . $this->datos[$campo['nombre']][0] .'">' . $tablaSelect['nombre'] . '</option>';
                                        }
                                        break;
                                    case '852':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                        if (isset($this->datos[$campo['nombre']][0])) {
                                            $tablaSelect = $this->centralConsulta->ajaxPlacaVehiculo($this->datos[$campo['nombre']][0]);
                                            $html .= '<option value="' . $this->datos[$campo['nombre']][0] .'">' . $tablaSelect['nombre'] . '</option>';
                                        }
                                        break;
                                    case '2046':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                        if (isset($this->datos[$campo['nombre']][0])) {
                                            $tablaSelect = $this->centralConsulta->ajaxPlacaVehiculo($this->datos[$campo['nombre']][0]);
                                            $html .= '<option value="' . $this->datos[$campo['nombre']][0] .'">' . $tablaSelect['nombre'] . '</option>';
                                        }
                                        break;
                                    case '7047':
                                        $html .= '<select style="width: 100%"" id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="referenceDeclaracion" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;
                                    // Municipio y departamento select to Ajax
                                    case '827':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;
                                    case '828':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;
                                    //End Municipio y departamento select to Ajax
                                    //Información Traslados
                                    case '3000':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                    break;

                                    case '3058':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                    break;

                                    case '3057':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                    break;

                                    case '3056':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                    break;

                                    case '3059':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                    break;
                                    //
                                    //End Información Traslados
                                    //Información Estados
                                    case '3056':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                    break;

                                    case '3012':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                    break;
                                    case '3011':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                    break;

                                    case '3016':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                    break;
                                    //End Información Estados
                                    case '714':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select-contribuyente" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;
                                    case '715':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select-placa" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;
                                    case '1213':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;
                                    case '1214':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2"  style="width: 100% !important;" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;
                                    case '1215':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;
                                    case '1236':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;
                                    case '1242':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;
                                    case '1240':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select-tercero" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;
                                    case '1242':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;
                                    case '1243':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;
                                    case '1247':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;
                                    case '1248':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;
                                    case '1250':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;
                                    case '1251':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;
                                    case '1260':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;
                                    case '1263':
                                    case '1328':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select-tercero" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;
                                    case '1279':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;
                                    case '1296':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;
                                    case '1322':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;
                                    //case '1328':
                                      //  $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                       // break;
                                    case '1329':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;
                                    case '1330':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;
                                    case '1332':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;
                                    case '1333':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;
                                    //case '1334':
                                        //$html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                      //  break;
                                    case '1337':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;
                                        // HEREDIA
                                    // Select de creacion de servicio
                                    case '437':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;
                                    // Select de creacion de estrato
                                    case '449':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;
                                    // Select de creacion de Descripcion de predio
                                    case '452':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;
                                    // Selects de la creacion de Predios
                                    case '443':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;
                                    case '444':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;
                                    case '445':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;
                                    case '603':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;
                                    case '604':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;
                                    case '606':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;
                                    case '446':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;
                                    case '650':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;
                                    //Creacion de costo
                                    case '481':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;
                                    // Creacion de Lectura Servicio
                                    case '483':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;
                                    case '484':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;
                                    case '488':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;
                                    case '607':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;
                                    case '609':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;
                                    //Creacion de texto para el servicio
                                    case '550':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;
                                    // Select abono predio
                                    case '582':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;
                                    case '583':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;
                                    case '584':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;
                                    // Selects de pago Abono
                                    case '588':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;
                                    case '589':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;
                                    // Selects de pago facturas
                                    case '596':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;
                                    case '595':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;
                                    // Select Rango
                                    case '634':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;
                                    // Select Refacturacion
                                    case '642':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;
                                    case '640':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;
                                    case '641':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;
                                    case '651':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;
                                    // Select Pqr Usuario
                                    case '683':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;
                                    case '698':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;
                                    case '686':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;
                                    case '684':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;
                                    case '685':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;
                                    // Select Pqr Respuesta
                                    case '690':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;
                                    case '693':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;
                                    case '851':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;
                                    case '2045':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;
                                    case '853':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;
                                    case '854':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;
                                    case '860':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;
                                    case '877':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;
                                    case '883':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;
                                    case '885':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;
                                    //Campo para categoria Almacen
                                    case '73':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select-categoria" style="width: 100% !important;" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;
                                    case '7080':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select-tipo_producto" style="width: 100% !important;" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;
                                    case '7081':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select-clase_producto" style="width: 100% !important;" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;
                                    case '7097':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select-tercero" ' . $dataSelect . ' ' . $atributos . ' >';
                                        if (isset($this->datos[$campo['nombre']][0])) {
                                            $tablaSelect = $this->centralConsulta->ajaxTercero($this->datos[$campo['nombre']][0],$_SESSION['empresa'],12);
                                            $html .= '<option value="' . $this->datos[$campo['nombre']][0] .'">' . $tablaSelect['nombre'] . '</option>';
                                        }
                                        break;
                                    //Tercero Responsable Ubicación inventario
                                    case '7185':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select-tercero" ' . $dataSelect . ' ' . $atributos . ' >';
                                        if (isset($this->datos[$campo['nombre']][0])) {
                                            $tablaSelect = $this->centralConsulta->ajaxTercero($this->datos[$campo['nombre']][0],$_SESSION['empresa'],12);
                                            $html .= '<option value="' . $this->datos[$campo['nombre']][0] .'">' . $tablaSelect['nombre'] . '</option>';
                                        }
                                        break;
                                   /* case '7055':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2-vigencia" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;*/
                                    case '9112':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2-vigencia" ' . $dataSelect . ' ' . $atributos . ' >';
                                    break;
                                    /*case '7074':
                                    $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select-predio" ' . $dataSelect . ' ' . $atributos . ' >';

                                    /*
                                    |--------------------------------------------------------------------------
                                    | Select Pago Manual Predial
                                    |-----
                                    */
                                    case '9159':
                                    $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select-predio" ' . $dataSelect . ' ' . $atributos . ' >';
                                    break;

                                    /*case '9166':
                                    $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select-vigencia" ' . $dataSelect . ' ' . $atributos . ' >';
                                    break;*/

                                          case '9166':
                                    $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                    break;

                                    case '9165':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                    break;
                                     /*
                                    |--------------------------------------------------------------------------
                                    |End  Select Pago Manual Predial
                                    |-----
                                    */
                                    //Banco Cuenta Configuracion Predial
                                    case '9173':
                                    $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                    break;

                                    case '9175':
                                    $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                    break;

                                       case '9177':
                                    $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                    break;


                                     /*
                                    |--------------------------------------------------------------------------
                                    |End  Banco Cuenta Configuracion Predial
                                    |-----
                                    */



                                    case '9131':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select-predio" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;

                                    //Grupo Tarifa predial
                                    case '9145':
                                    $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                    break;

                                    //Tipo de Acuerdo Tarifa predial
                                    case '9146':
                                    $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                    break;

                                    //Tipo de predio (Rural-urbano) predial
                                    case '9148':
                                    $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                    break;

                                    // Municipio y departamento Predial Ajax
                                    case '9153':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;

                                    /*
                                    case '7054':
                                       $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select-tercero" ' . $dataSelect . ' ' . $atributos . ' >';

                                        break;*/
                                    case '9111':
                                       $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select-tercero" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;
                                    // Select 2 para vigencia de tipo impuesto
                                    case '7049':
                                        $html .= '<select style="width: 100%"" id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select-year" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;
                                    case '7069':
                                        $html .= '<select style="width: 100%"" id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="referenceDeclaracionpre" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;
                                    case '7066':
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;
                                    case '9003':
                                    case '9004':
                                    case '9005':
                                    case '9021':
                                    case '9022':
                                    case '9025':
                                    case '9028':
                                    case '9153':
                                    case '9600':
                                    case '9601':
                                    case '9602':
                                    case '9603':
                                    case '9154': //tIPO SATRIBUTO padre predial predio
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="select2" ' . $dataSelect . ' ' . $atributos . ' >';
                                        break;
                                    default:
                                        $html .= '<select id="' . $campo['nombre'] . '" name="' . $campo['nombre'] . '[]" class="chosen-select" ' . $dataSelect . ' ' . $atributos . ' >';
                                }

                                $html .= $opciones;
                                $html .= '</select>';
                                $html .= '</div>';

                            break;

                            case 'table':

                                switch ($campo['campo_formulario']) {
                                    case '361':
                                        $html .= '<div class="field textarea"><label><span style="color: white !important;">.....</span></label></div>';
                                        break;
                                    case '459':
                                    case '484':
                                        $html .= '<div class="field checkbox_label">';
                                        $html .= '<label><h2>Mercadeo</h2></label>';
                                        $html .= '</div>';
                                        break;
                                    case '561':
                                        $html .= '<div class="field checkbox_label">';
                                        $html .= '<label><h2>Conversión</h2></label>';
                                        $html .= '</div>';
                                        break;
                                    case '563':
                                    case '564':
                                        $datosEmpresa = $this->centralConsulta->datosEmpresa($_SESSION["empresa"]);
                                        $campo['etiqueta'] .= " - <b>".$datosEmpresa[0]['razon_social']."</b>";
                                        break;
                                    case '2128':
                                    case '2129':
                                        $html .= '<div class="field checkbox_label"><label><h2><br><br></h2></label></div>';
                                        break;
                                }

                                $html .= '<div class="field table">';
                                if (!isset($_REQUEST["bitacoraSoporte"])) {
                                    $html .= '<label for="' . $campo['nombre'] . '"><span>' . $campo['etiqueta'] . '</span></label>';
                                }
                                $aux = explode("/", $campo['tabla']);$aux2 = explode("$aux[0]/", $campo['tabla']);
                                $campo['tabla'] = "app/componente/$aux[0]/vistas/$aux2[1]";


                                include $this->rutaAplicacion->rutaRelativa . $campo['tabla'] . '.php';
                                $html .= '</div>';

                            break;

                        }
                    }

                }
                $html .= '</fieldset>';
            }
            if (!isset($_REQUEST["bitacoraSoporte"])) {
                $html .= '<output id="mensaje"></output>';
                if ($this->idFormulario < 100 || $this->idFormulario > 122) {
                    $html .= '<input type="hidden" id="_token" name="_token" value="' .TokenAplicacion::generateToken($this->nombreFormulario). '"/>';
                    $html .= '<input type="hidden" name="fechaActual" id="fechaActual" value="'.date('Y-m-d').'"></form>';
                } else {
                    $html .= '</form>';
                }
            }
            return $html;
        }
    }
