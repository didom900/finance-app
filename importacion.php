<?php
    /**
     * Importación Alcaldia en la aplicación
     *
     * Importación Alcaldia en la aplicación
     *
     * @copyright  2017 - Diego Soba.
     * @author     Diego Soba <didom900@gmail.com>
     * @version    1.0
     */
    use contSoft\Finanzas\Clases\Helpers\Helper;
    if (!isset($validacionAplicacion)) {
        require_once './bootstrap/autoload.php';
    }
    include $rutaAplicacion->rutaRelativa . 'vendor/phpoffice/phpexcel/Classes/PHPExcel.php';
    include $rutaAplicacion->rutaRelativa . 'vendor/phpoffice/phpexcel/Classes/PHPExcel/Writer/Excel2007.php';
    include $rutaAplicacion->rutaRelativa . 'vendor/phpoffice/phpexcel/Classes/PHPExcel/Cell/AdvancedValueBinder.php';
    ini_set('max_execution_time', 50000000);
    ini_set('memory_limit', '9000000000M');
   // ini_set('memory_limit', '-1');
    //ini_set('memory_limit', '4096M');
    error_reporting(E_ERROR);
    date_default_timezone_set('Europe/Berlin');
    //    ini_set('display_errors', 1);
    //    ini_set('display_startup_errors', 1);
    //    error_reporting(E_ALL);
    /**
     * Calcula el Digito de Verificacion
     * @param  [type] $nit [description]
     * @return [type]      [description]
     */
    function dv($nit) {
        if (! is_numeric($nit)) {
            return false;
        }
        $arr = [
            1  => 3,
            4  => 17,
            7  => 29,
            10 => 43,
            13 => 59,
            2  => 7,
            5  => 19,
            8  => 37,
            11 => 47,
            14 => 67,
            3  => 13,
            6  => 23,
            9  => 41,
            12 => 53,
            15 => 71
        ];
        $x = 0;
        $y = 0;
        $z = strlen($nit);
        $dv = '';
        for ($i=0; $i<$z; $i++) {
            $y = substr($nit, $i, 1);
            $x += ($y*$arr[$z-$i]);
        }
        $y = $x%11;
        if ($y > 1) {
            $dv = 11-$y;
            return $dv;
        } else {
            $dv = $y;
            return $dv;
        }
    }
    $ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
    $empresa = $_SESSION['empresa'];
    $usuario = $_SESSION['usuario'];
    if (isset($_REQUEST["importacion"]) && isset($_REQUEST["proceso"])) {
        $datosImportacion = $centralConsulta->datosImportacion($_REQUEST['importacion']);
        $fila = 0;
        if ($_REQUEST["proceso"] == "1") {
            $letra = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ', 'BA', 'BB', 'BC', 'BD', 'BE', 'BF', 'BG', 'BH', 'BI', 'BJ', 'BK', 'BL', 'BM', 'BN', 'BO', 'BP', 'BQ', 'BR', 'BS', 'BT', 'BU', 'BV', 'BW', 'BX', 'BY', 'BZ', 'CA', 'CB', 'CC', 'CD', 'CE', 'CF', 'CG', 'CH', 'CI', 'CJ', 'CK', 'CL', 'CM', 'CN', 'CO', 'CP', 'CQ', 'CR', 'CS', 'CT', 'CU', 'CV', 'CW', 'CX', 'CY', 'CZ');
            //print "<pre>"; print_r($letra); print "</pre>\n";
            PHPExcel_Cell::setValueBinder( new PHPExcel_Cell_AdvancedValueBinder() );
            $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
            $cacheSettings = array( ' memoryCacheSize ' => '100MB');
            PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
            $objReader = new PHPExcel_Reader_Excel2007();  // Creamos el objeto para leer del excel.
            $objReader->setReadDataOnly(true);
            $objPHPExcel = $objReader->load($rutaAplicacion->rutaDocumentoRelativa . $datosImportacion[0]['archivo']);  // Cargamos el archivo excel subido (extensión *.xlsx).
            $objPHPExcel->setActiveSheetIndex(0);  // Asignamos el excel activo.
            $intentos = 1;
            $cantidadLetra = 0;
            $columnas = "importacion";
            while ($intentos < 16) {
                for ($i = $cantidadLetra, $tamano = sizeof($letra); $i < $tamano; $i++) {
                    if (htmlspecialchars(trim($objPHPExcel->getActiveSheet()->getCell($letra[$i] . $intentos)->getFormattedValue()), ENT_QUOTES, 'UTF-8') == "" && htmlspecialchars(trim($objPHPExcel->getActiveSheet()->getCell($letra[$i + 1] . $intentos)->getFormattedValue()), ENT_QUOTES, 'UTF-8') == "") {
                        $fila = ($cantidadLetra < ($i - 1)) ? $intentos : $fila;
                        $cantidadLetra = ($cantidadLetra < ($i - 1)) ? ($i - 1) : $cantidadLetra;
                        $i = $tamano;
                    } else {
                        $columnas .= (strrpos($columnas, ", {$letra[$i]}") !== false) ? "" : ", {$letra[$i]}";
                    }
                }
                //print "<pre>"; print_r("INTENTOS " . $intentos); print "</pre>\n";//print "<pre>"; print_r("INTENTOS " . $intentos); print "</pre>\n";
                //print "<pre>"; print_r($cantidadLetra . " " . $columnas); print "</pre>\n";
                $intentos++;
            }
            //print "<pre>"; print_r("FILAS " . $fila); print "</pre>\n";
            $columnas = strtolower(str_replace(", as,", ", ass,", $columnas));
            $sql = "UPDATE importacion
                    SET columna = '$columnas'
                    WHERE importacion = $_REQUEST[importacion]";
            $resultado = $conexion->getDBCon()->Execute($sql);
            //echo $objPHPExcel->getActiveSheet()->getCell($letra[0] . $fila)->getFormattedValue();
            $intentos = $fila + 2;
            //print "<pre>"; print_r("INTENTOS " . $intentos); print "</pre>\n";
            $fila = $datosImportacion[0]['fila'];
            $auxFila = $fila;
            while (($objPHPExcel->getActiveSheet()->getCell('A' . $fila)->getValue() != '') || ($objPHPExcel->getActiveSheet()->getCell('B' . $fila)->getValue() != '') || ($objPHPExcel->getActiveSheet()->getCell('C' . $fila)->getValue() != '') || ($objPHPExcel->getActiveSheet()->getCell('D' . $fila)->getValue() != '') || ($objPHPExcel->getActiveSheet()->getCell('E' . $fila)->getValue() != '') || ($fila <= $intentos)) {
                $datos = "'$_REQUEST[importacion]'";
                for ($i = 0; $i <= $cantidadLetra; $i++) {
                    $datos .= ", '" . htmlspecialchars(trim($objPHPExcel->getActiveSheet()->getCell($letra[$i] . $fila)->getFormattedValue()), ENT_QUOTES, 'UTF-8') . "'";    
                    if ($i == 1) {
                        //$datos .= ", '" . htmlspecialchars(trim($objPHPExcel->getActiveSheet()->getCell($letra[$i] . $fila)->getValue()), ENT_QUOTES, 'UTF-8') . "'"; 


                        //$datos .= ", '" . $objPHPExcel->getActiveSheet()->getCell($letra[$i] . $fila)->getValue() . "'"; 
                        //$datos .= ", '" . $objPHPExcel->setCellValueExplicit($letra[$i] . $fila,'',PHPExcel_Cell_DataType::TYPE_STRING) . "'"; 
                        ///$worksheet->setCellValueExplicit($letra[$j - 2] . ($i+ $inicioFila),rtrim($fila[$aux]),PHPExcel_Cell_DataType::TYPE_STRING);    
                    } else {
                    }
                }
                $sql = "INSERT INTO importacion_dato
                        ($columnas)
                        VALUES($datos)";
                $resultado = $conexion->getDBCon()->Execute($sql);
                if ($resultado === false) {
                    //print "<pre>"; print_r($sql); print "</pre>\n";
                    //print "<pre>"; print_r($sql . " " . $conexion->getDBCon()->ErrorMsg()); print "</pre>\n";
                    $fila = 99999998;
                }
                //print "<pre>"; print_r($columnas); print "</pre>\n";
                //print "<pre>"; print_r($datos); print "</pre>\n";
                //break;
                $fila++;
            }
            $datosImportacion[0]['inserciones'] = $fila - $auxFila;
            $datosImportacion[0]['columnas'] = $columnas;
            $objPHPExcel->disconnectWorksheets();
            $objPHPExcel->garbageCollect();
            unset($objPHPExcel);
            unset($objReader);
        }
        if (isset($_REQUEST["externo"])) {
            //print "<pre>"; print_r($datosImportacion); print "</pre>\n";
            //print "<pre>"; print_r("INSERCIONES $fila"); print "</pre>\n";
            $datosImportacion = $centralConsulta->datosImportacion($_REQUEST['importacion']);
            $_REQUEST["proceso"] = "2";
        }
        if ($_REQUEST["proceso"] == "2") {
            switch ($datosImportacion[0]['tipo']) {
                case '14':
                    $sql = "UPDATE importacion_dato SET importado = 0 WHERE importacion = 14";
                    $resultado = $conexion->getDBCon()->Execute($sql);
                    break;
            }
            $datosImportacionTabla = $centralConsulta->datosImportacionTabla($_REQUEST['importacion'], $datosImportacion[0]['columna']);
            //print "<pre>"; print_r($datosImportacionTabla); print "</pre>\n";
            if (count($datosImportacionTabla) > 0) {
                switch ($datosImportacion[0]['tipo']) {
                    case '1':
                        $terceroNitEmpresaSistema = $centralConsulta->terceroNitEmpresaSistema();
                        break;
                    case '2':
                        $pucEmpresa = $centralConsulta->pucEmpresa();
                        if (isset($_REQUEST["externo"])) {
                            $terceroSistemaId = $centralConsulta->terceroSistemaId();
                        } else {
                            $terceroSistemaId = $centralConsulta->terceroSistemaIdImportacion();
                        }
                        $idTerceroEmpresa = $centralConsulta->idTerceroEmpresa();
                        $soporteSistemaId = $centralConsulta->soporteSistemaIdImportacion();
                        $fechas = array();
                        $fechaNumeroImportacion = $centralConsulta->fechaNumeroImportacion($_REQUEST['importacion'], "b");
                        foreach ($fechaNumeroImportacion as $fecha) {
                            $fechas[$fecha["b"]] = date($format = "Y-m-d", PHPExcel_Shared_Date::ExcelToPHP($fecha["b"]));
                        }
                        //print "<pre>"; print_r($fechas); print "</pre>\n";
                        /*
                        $sql = "DELETE FROM rl_soporte_contable_asiento";
                        $resultado = $conexion->getDBCon()->Execute($sql);
                        $sql = "DELETE FROM rl_soporte_contable_producto";
                        $resultado = $conexion->getDBCon()->Execute($sql);
                        $sql = "DELETE FROM rl_soporte_contable_campos";
                        $resultado = $conexion->getDBCon()->Execute($sql);
                        $sql = "DELETE FROM soporte_contable";
                        $resultado = $conexion->getDBCon()->Execute($sql);
                        $sql = "SELECT pg_catalog.setval('rl_soporte_contable_asiento_rl_soporte_contable_asiento_seq', 1, false)";
                        $resultado = $conexion->getDBCon()->Execute($sql);
                        $sql = "SELECT pg_catalog.setval('rl_soporte_contable_producto_rl_soporte_contable_producto_seq', 1, false)";
                        $resultado = $conexion->getDBCon()->Execute($sql);
                        $sql = "SELECT pg_catalog.setval('rl_soporte_contable_campos_rl_soporte_contable_campos_seq', 1, false)";
                        $resultado = $conexion->getDBCon()->Execute($sql);
                        $sql = "SELECT pg_catalog.setval('soporte_contable_soporte_contable_seq', 1, false)";
                        $resultado = $conexion->getDBCon()->Execute($sql);
                        */
                        break;
                    case '3':
                        $pucEmpresa = $centralConsulta->pucEmpresa();
                        //print "<pre>"; print_r($pucEmpresa); print "</pre>\n";
                        $terceroSistemaId = $centralConsulta->terceroSistemaIdImportacion();
                        $_REQUEST["soporteContable"]="";
                        $_REQUEST["tipoDocumentoContable"]="7";
                        $_REQUEST["prefijo"][]="0";
                        $_REQUEST["consecutivo"]=$centralConsulta->valorSecuencia("tipo_documento_contable_$_SESSION[empresa]_$_REQUEST[tipoDocumentoContable]_seq");
                        $_REQUEST["utilizado"]="0";
                        $_REQUEST["fechaDocumento"]="2017-12-31";
                        $_REQUEST["empresaSeleccionada"][]=$_SESSION["empresa"];
                        $_REQUEST["terceroRecibe"][]=$centralConsulta->idTerceroEmpresa();
                        $_REQUEST["concepto"]="SALDOS INICIALES";
                        $_REQUEST["formaPago"][]="3";
                        $auxImportado = $centralConsulta->soporteContableId() + 1;
                        $cuenta = 0;
                        break;
                    case '14':
                        $sql = "DELETE FROM pago_presupuestal_rubro";
                        $resultado = $conexion->getDBCon()->Execute($sql);
                        $sql = "DELETE FROM pago_presupuestal_campos";
                        $resultado = $conexion->getDBCon()->Execute($sql);
                        $sql = "DELETE FROM pago_presupuestal";
                        $resultado = $conexion->getDBCon()->Execute($sql);
                        $sql = "DELETE FROM obligacion_presupuestal_rubrorp";
                        $resultado = $conexion->getDBCon()->Execute($sql);
                        $sql = "DELETE FROM obligacion_presupuestal_campos";
                        $resultado = $conexion->getDBCon()->Execute($sql);
                        $sql = "DELETE FROM obligacion_presupuestal";
                        $resultado = $conexion->getDBCon()->Execute($sql);
                        $sql = "DELETE FROM registro_presupuestal_rubrocdp";
                        $resultado = $conexion->getDBCon()->Execute($sql);
                        $sql = "DELETE FROM registro_presupuestal_campos";
                        $resultado = $conexion->getDBCon()->Execute($sql);
                        $sql = "DELETE FROM registro_presupuestal";
                        $resultado = $conexion->getDBCon()->Execute($sql);
                        $sql = "UPDATE soporte_contable SET registro_presupuestal = 0, obligacion_presupuestal = 0, pago_presupuestal = 0 WHERE pago_presupuestal <> 0 OR obligacion_presupuestal <> 0";
                        $resultado = $conexion->getDBCon()->Execute($sql);
                        $omitidos = "0";
                        $registroPresupuestalesImportacion = $centralConsulta->registroPresupuestalesImportacion($empresa, 1);
                        $certificadosDisponibilidadPresupuestal = $centralConsulta->certificadosDisponibilidadPresupuestal($empresa, 1);
                        $datosRubroInfoImportacion = $centralConsulta->datosRubroInfoImportacion();
                        $datosCdpRubroInfoImportacion = $centralConsulta->datosCdpRubroInfoImportacion();
                        $terceroSistemaId = $centralConsulta->terceroSistemaIdImportacion();
                        $soporteSistemaId = $centralConsulta->soporteSistemaIdImportacion();
                        $obligacionPresupuestalImportacion = array();
                        //print "<pre>"; print_r($datosRubroInfoImportacion); print "</pre>\n";
                        //die();
                        break;
                    case '9013':
                    case '9014':
                    case '9015':
                    case '9016':
                    case '9017':
                    case '9018':
                    case '9019':
                    case '9020':
                    case '9021':
                    case '9022':

                    case '9040':
                    case '9041':
                    case '9042':
                    case '9043':
                    case '9044':
                    case '9045':
                    case '9048': //Prueba datos
                    case '9050': //Prueba datos

                        //$terceroNitEmpresaSistema = $centralConsulta->terceroNitEmpresaSistema();
                        //print_r($terceroNitEmpresaSistema);
                        //die();
                        $empresa = $_SESSION["empresa"];
                        $vigencia_formulario = $_REQUEST['vigencia'];

                        //var_dump($_REQUEST);
                        //die();

                        $fecha_sistema = date('Y-m-d');
                        $usuario = $_SESSION['usuario'];
                        //$informacionPredioGeneral = $centralConsulta->informacionPredioGeneral($vigencia_formulario);
                        $informacionPredioInicial = $centralConsulta->informacionPredioInicial(3);
                        $traerTerceros = $centralConsulta->traerTerceros();
                        //$predialTercero = $centralConsulta->predialTercero($vigencia_formulario);
                        $predialTercero = $centralConsulta->predialTercero();
                        $vigenciaPredial = $centralConsulta->vigenciaPredial();
                        //$informacionPredioLiquidacion = $centralConsulta->informacionPredioLiquidacion();
                        //$informacionImpuestoPreliquidacion = $centralConsulta->informacionImpuestoPreliquidacion();
                        //$PredialCampos = $centralConsulta->PredialCampos($vigencia_formulario,$empresa);
                        /*



                        */


                        //
                        //

                        // $informacionPredioLiquidacionOtroCampo = $centralConsulta->informacionPredioLiquidacionOtroCampo();
                        // $informacionPredioAtributo = $centralConsulta->informacionPredioAtributo();


                        //

                        //$predialTerceroVigencia = $centralConsulta->predialTerceroVigencia($vigencia_formulario);
                        //
                       // print "<pre>"; print_r($vigenciaPredial); print "</pre>\n";
                         //print "<pre>"; print_r($informacionPredioAtributo); print "</pre>\n";

                        break;
                }
            }
            //print "<pre>"; print_r($datosImportacion[0]['tipo']); print "</pre>\n";
            foreach ($datosImportacionTabla as &$dato) {
                $importado = 0;
                switch ($datosImportacion[0]['tipo']) {
                    case '1':
                        if (ctype_digit($dato["a"])) {//codigo
                            $auxiliar = explode("-", $dato["i"]);
                            $dato["i"] = ltrim(ltrim(preg_replace("/[^0-9]/", "", $auxiliar[0])), "0");//nit
                            $dato["i"] = ($dato["i"] == "") ? $dato["a"] : $dato["i"];
                            $key = array_search($dato["i"], array_column($terceroNitEmpresaSistema, 'nit'));
                            if ($key == null && $key !=0) {
                                $key = -1;
                            }
                            if ($terceroNitEmpresaSistema[$key]['nit'] == $dato["i"] && $terceroNitEmpresaSistema[$key]['empresa'] == $_SESSION["empresa"]) {
                                $importado = $terceroNitEmpresaSistema[$key]['tercero'];//existe para la empresa
                            } else {
                                $fila++;
                                if ($terceroNitEmpresaSistema[$key]['nit'] != $dato["i"]) { //nuevo tercero
                                    $dato["b"] = ($dato["b"] == "Natural") ? 1 : 2;
                                    $dato["h"] = ($dato["c"] == "") ? $dato["h"] : $dato["c"];
                                    $dato["c"] = ($dato["b"] == 1) ? "" : $dato["c"];
                                    if ($dato["d"] == "" && $dato["e"] == "" && $dato["f"] == "" && $dato["g"] == "" && $dato["b"] == 1) {
                                        $auxiliar2 = explode(" ", $dato["h"]);
                                        $dato["d"] = $auxiliar2[0];
                                        $dato["f"] = trim(str_replace("$auxiliar2[0] ", "", $dato["h"]));
                                    }
                                    if ($dato["c"] == "" && $dato["b"] == 2) {
                                        $dato["c"] = ($dato["h"] == "") ? "$dato[d] $dato[e] $dato[f] $dato[g]" : $dato["h"];
                                        $dato["d"] = "";
                                        $dato["e"] = "";
                                        $dato["f"] = "";
                                        $dato["g"] = "";
                                    }
                                    $dv = dv($dato["i"]);
                                    $sql = "INSERT INTO tercero
                                            (nit, municipio, ciudad_pago, identificacion, digito_verificacion, nombre, apellido, razon_social, usuario)
                                            VALUES( '$dato[i]',
                                                    '0',
                                                    '0',
                                                    '$dato[i]',
                                                    '$dv',
                                                    '$dato[d] $dato[e]',
                                                    '$dato[f] $dato[g]',
                                                    '$dato[c]',
                                                    '$_SESSION[usuario]')";
                                    $resultado = $conexion->getDBCon()->Execute($sql);
                                    $importado = $conexion->getDBCon()->insert_Id();
                                    $terceroNitEmpresaSistema[] = Array("tercero"=> $importado,
                                                                        "nit"=> $dato["i"],
                                                                        "empresa"=> $_SESSION["empresa"]);
                                    //print "<pre>"; print_r($dato["i"]); print "</pre>\n";
                                    //print "<pre>"; print_r($conexion->getDBCon()->insert_Id()); print "</pre>\n";
                                    if ($resultado === false) {
                                        print "<pre>"; print_r($sql); print "</pre>\n";
                                        print "<pre>"; print_r($sql . " " . $conexion->getDBCon()->ErrorMsg()); print "</pre>\n";
                                    }
                                    $sql = "INSERT INTO rl_tercero_tipo_persona
                                            (empresa, tercero , tipo_persona)
                                            VALUES($_SESSION[empresa], '$importado',
                                                   '$dato[b]')";
                                    $resultado = $conexion->getDBCon()->Execute($sql);
                                } else {
                                    $importado = $terceroNitEmpresaSistema[$key]['tercero'];//existe tercero pero no para la empresa
                                }
                                $sql = "INSERT INTO rl_tercero_campos
                                        (empresa, tercero , campo, valor)
                                        VALUES($_SESSION[empresa], '$importado',
                                               '12',
                                               '$dato[j]')";//direccion manual
                                $resultado = $conexion->getDBCon()->Execute($sql);
                                if ($dato["l"] != "" || $dato["m"] != "") {
                                    for ($i = 0, $tamano = 5; $i < $tamano; $i++) {
                                        $valor = ($i == 0) ? $dato["l"] : "";
                                        $valor = ($i == 4) ? $dato["m"] : $valor;
                                        $sql = "INSERT INTO rl_tercero_campos
                                                    (empresa, tercero, campo, valor)
                                                    VALUES($_SESSION[empresa], $importado, 11, '$valor')";//telefono y fax
                                        $resultado = $conexion->getDBCon()->Execute($sql);
                                    }
                                }
                                if ($dato["k"] != "") {
                                    for ($i = 0, $tamano = 3; $i < $tamano; $i++) {
                                        $valor = ($i == 0) ? $dato["k"] : "";
                                        $sql = "INSERT INTO rl_tercero_campos
                                                    (empresa, tercero, campo, valor)
                                                    VALUES($_SESSION[empresa], $importado, 14, '$valor')";//email
                                        $resultado = $conexion->getDBCon()->Execute($sql);
                                    }
                                }
                                if ($dato["p"] != "" && $dato["r"] != "") {
                                    $dato["p"] = trim(ltrim(preg_replace("/[^0-9]/", "", "$dato[p]$dato[r]")), "0");
                                    if ($dato["p"] != "") {
                                        for ($i = 0, $tamano = 23; $i < $tamano; $i++) {
                                            $valor = ($i == 22) ? "$dato[p]000" : "";
                                            $sql = "INSERT INTO rl_tercero_campos
                                                        (empresa, tercero, campo, valor)
                                                        VALUES($_SESSION[empresa], $importado, 165, '$valor')";//municipio
                                            $resultado = $conexion->getDBCon()->Execute($sql);
                                        }
                                    }
                                }
                                if ($dato["b"] == 1) {
                                    $sql = "INSERT INTO rl_tercero_campos
                                            (empresa, tercero , campo, valor)
                                            VALUES($_SESSION[empresa], '$importado',
                                                   '435',
                                                   '1')";//tipo_documento
                                    $resultado = $conexion->getDBCon()->Execute($sql);
                                }
                            }
                        } else {
                            $importado = -100;
                        }
                        break;
                    case '2':
                        if (ctype_digit($dato["a"]) || (isset($_REQUEST["externo"]) && $dato["b"] != "" && $dato["b"] != "FECHA")) {//codigo asiento
                            $tercero = (trim($dato["j"]) == "") ? "-1" : trim($dato["j"]);
                            $key = array_search($tercero, array_column($terceroSistemaId, 'nit'));
                            if ($key == null && $key != 0) {
                                $key = -1;
                            }
                            $tercero = ($terceroSistemaId[$key]['nit'] == $tercero) ? $terceroSistemaId[$key]['tercero'] : 0;
                            $tipoDocumentoContable = 2;
                            $key = array_search("$tipoDocumentoContable-$dato[e]-$dato[b]", array_column($soporteSistemaId, 'consecutivo'));
                            if ($key == null && $key != 0) {
                                $key = -1;
                            }
                            $importado = ($soporteSistemaId[$key]['consecutivo'] == "$tipoDocumentoContable-$dato[e]-$dato[b]") ? $soporteSistemaId[$key]['soporte_contable'] : 0;
                            $fechaNumero = $dato["b"];
                            $dato["b"] = $fechas[$dato["b"]];
                            if ($importado == 0) {
                                $sql = "INSERT INTO soporte_contable
                                        (consecutivo, prefijo, tipo_documento_contable, tercero_elabora, tercero_recibe, empresa, usuario)
                                        VALUES( '$dato[e]',
                                                '0',
                                                '$tipoDocumentoContable',
                                                '$idTerceroEmpresa',
                                                '$tercero',
                                                '$_SESSION[empresa]',
                                                '$_SESSION[usuario]')";
                                $resultado = $conexion->getDBCon()->Execute($sql);
                                $importado = $conexion->getDBCon()->insert_Id();
                                if ($resultado === false) {
                                    print "<pre>"; print_r($sql); print "</pre>\n";
                                    print "<pre>"; print_r($sql . " " . $conexion->getDBCon()->ErrorMsg()); print "</pre>\n";
                                }
                                $soporteSistemaId[] = Array("soporte_contable"=> $importado,
                                                            "consecutivo"=> "$tipoDocumentoContable-$dato[e]-$fechaNumero",
                                                            "comprobante"=> "NA");
                                $sql = "INSERT INTO rl_soporte_contable_campos
                                                    (soporte_contable, campo, valor)
                                                    VALUES('$importado',
                                                           '359',
                                                           '$dato[b]')";
                                $resultado = $conexion->getDBCon()->Execute($sql);
                                $sql = "INSERT INTO rl_soporte_contable_campos
                                                    (soporte_contable, campo, valor)
                                                    VALUES('$importado',
                                                           '364',
                                                           '$dato[f]')";
                                $resultado = $conexion->getDBCon()->Execute($sql);
                            }
                            $cuenta = trim($dato["g"]);
                            $key = array_search($cuenta, array_column($pucEmpresa, 'codigo'));
                            if ($key == null && $key != 0) {
                                $key = -1;
                            }
                            $cuenta = ($pucEmpresa[$key]['codigo'] == $cuenta) ? $pucEmpresa[$key]['puc_empresa'] : 0;
                            $dato["r"] = trim($dato["r"]);
                            $dato["s"] = trim($dato["s"]);
                            $dato["y"] = (trim($dato["y"]) == "") ? "0" : trim($dato["y"]);
                            $sql = "INSERT INTO rl_soporte_contable_producto
                                    (soporte_contable, cuenta, concepto, centro_costo, tercero, cheque, debito, credito, por_ret, base_ret, vence, gmf, imp, liq, clasificacion)
                                    VALUES($importado, $cuenta, '$dato[f]', 0, $tercero, '', $dato[r], $dato[s], 0, $dato[y], '$dato[b]', 0, 0, 0, 0)";
                            $resultado = $conexion->getDBCon()->Execute($sql);
                            $auxImportado = $conexion->getDBCon()->insert_Id();
                            if ($resultado === false) {
                                print "<pre>"; print_r($sql); print "</pre>\n";
                                print "<pre>"; print_r($sql . " " . $conexion->getDBCon()->ErrorMsg()); print "</pre>\n";
                            }
                            $sql = "INSERT INTO rl_soporte_contable_asiento
                                    (rl_soporte_contable_producto, puc_empresa, valor_debito, valor_credito, tercero, centro_costo, soporte_contable_origen, por_ret, base_ret)
                                    VALUES($auxImportado,
                                           $cuenta,
                                           $dato[r],
                                           $dato[s],
                                           $tercero,
                                           0,
                                           0,
                                           0,
                                           $dato[y])";
                            $resultado = $conexion->getDBCon()->Execute($sql);
                            if ($resultado === false) {
                                print "<pre>"; print_r($sql); print "</pre>\n";
                                print "<pre>"; print_r($sql . " " . $conexion->getDBCon()->ErrorMsg()); print "</pre>\n";
                            }
                            $fila++;
                        } else {
                            $importado = -100;
                        }
/*
ASIENTO CONVERGENCIA
ORDENES DE PAGO
/////////////////////////////////////////////////////INGRESOS A CAJA Y BANCOS
EGRESOS DE BANCOS
INGRESOS DE PREDIAL
/////////////////////////////////////////////////////NOMINA
CAUSACION ICA
INGRESOS DE INDUSTRIA Y COMERCIO
/////////////////////////////////////////////////////ENTRADAS DE ALMACEN
/////////////////////////////////////////////////////SALIDAS DE ALMACEN
/////////////////////////////////////////////////////DEPRECIACIONES Y AMORTIZACIONES
CRUCES DE PREDIAL Y OTRAS CAUSA PRE
/////////////////////////////////////////////////////NOTAS CONTABLES
TRASLADO DE SALDOS ICA Y PREDIAL
CRUCE DE ICA Y OTRAS CAUSA ICA
CAUSACION PREDIAL
OTRAS CAUSACIONES

tipo_documento_contable descripcion
/////////////////////////////////////////////////////1   COMPROBANTE DE CAJA
/////////////////////////////////////////////////////2   COMPROBANTE DE EGRESO
3   COMPROBANTE DE INGRESO
4   NOTA DEBITO PARA CLIENTES
/////////////////////////////////////////////////////5   NOTA DE CONTABILIDAD
/////////////////////////////////////////////////////6   RECIBO DE CAJA
/////////////////////////////////////////////////////7   SALDOS INICIALES
8   FACTURA DE VENTA
9   AJUSTES DE INVENTARIO
/////////////////////////////////////////////////////10  COMPROBANTE DE NOMINA
11  COTIZACIÃ“N
/////////////////////////////////////////////////////12  AJUSTES POR INFLACION
13  AJUSTES POR INFLACION COSTO DE VENTAS
14  AJUSTES POR INFLACION DEPRECIACION
15  CONSIGNACION BANCARIA
16  COMPRA REMISIONADA
/////////////////////////////////////////////////////17  COSTO DE VENTAS
18  DEVOLUCION DE MERCANCIAS CLIENTES
19  DEVOLUCION DE MERCANCIAS PROVEEDORES
/////////////////////////////////////////////////////20  DEPRECIACION
21  DEVOLUCION DE REMISION
/////////////////////////////////////////////////////22  ENTRADA A ALMACEN
23  ENTRADA DE PRODUCTO TERMINADO
24  FACTURA DE COMPRA
25  COMPRA EXTRANJERA
26  IMPORTACION
27  NOTA DE CREDITO PARA CLIENTES
28  NOTA DE CREDITO DE PROVEEDORES
29  NOTA DEBITO DE PROVEEDORES
30  REMISION
/////////////////////////////////////////////////////31  SALIDA DE ALMACEN
32  ORDEN DE COMPRA
33  AJUSTES MINIMOS Y MAXIMOS
34  REQUISICIÃ“N
35  DOCUMENTO EQUIVALENTE
36  PEDIDO
37  CUENTA DE COBRO
/////////////////////////////////////////////////////38  CANCELACION CUENTAS
/////////////////////////////////////////////////////39  COMPROBANTE DE EGRESO DE NOMINA
/////////////////////////////////////////////////////40  RECAUDO IMPUESTO
41  RECIBO OFICIAL PAGO

//formularios con cuenta
2
5
6
7
10
10
12
17
20
38
39
39
40
*/
                        break;
                    case '3':
                        //ctype_digit($dato["a"])
                        if ($dato["a"] != "" && trim($dato["d"]) != "0") {//codigo cuenta y saldo inicial
                            $cuenta = ((trim($dato["b"]) == "") || (strlen(trim($dato["a"])) == strlen(trim(ltrim(preg_replace("/[^0-9]/", "", $dato["a"])), "0")))) ? trim($dato["a"]) : $cuenta;
                            $cuenta = (trim($dato["b"]) != "" && $cuenta == 0) ? trim($dato["a"]) : $cuenta;
                            $auxcuenta = $cuenta;
                            $tercero = (trim($dato["b"]) == "") ? "008918550177" : trim($dato["b"]);
                            $dato["d"] = (trim($dato["d"]) == "") ? 0 : $dato["d"];
                            $valor = round(trim($dato["d"]), 2);
                            if ($valor == 0) {
                                $importado = -100;
                            } else {
                                if (strrpos($valor, "-") !== false) {//BUSQUEDA EN LA CADENA DE TEXTO Y ENCUENTRA
                                    $debito = 0;
                                    $credito = $valor * -1;
                                } else {
                                    $debito = $valor;
                                    $credito = 0;
                                }
                                $debito = str_replace('.', ',', $debito);
                                $credito = str_replace('.', ',', $credito);
                                $key = array_search($cuenta, array_column($pucEmpresa, 'codigo'));
                                if ($key == null && $key != 0) {
                                    $key = -1;
                                }
                                $nivel = ($pucEmpresa[$key]['codigo'] == $cuenta) ? $pucEmpresa[$key]['nivel_uno'] : 0;
                                $cuenta = ($pucEmpresa[$key]['codigo'] == $cuenta) ? $pucEmpresa[$key]['puc_empresa'] : 0;
                                $key = array_search($tercero, array_column($terceroSistemaId, 'nit'));
                                if ($key == null && $key != 0) {
                                    $key = -1;
                                }
                                $tercero = ($terceroSistemaId[$key]['nit'] == $tercero) ? $terceroSistemaId[$key]['tercero'] : 0;
                                //print "<pre>"; print_r("$dato[a] - $cuenta - $tercero - $valor - $nivel"); print "</pre>\n";
                                $_REQUEST["tablaSoporteContable"][]="3";
                                $_REQUEST["tablaSoporteContable"][]=$cuenta;
                                $_REQUEST["tablaSoporteContable"][]="SALDOS INICIALES";
                                $_REQUEST["tablaSoporteContable"][]="";
                                $_REQUEST["tablaSoporteContable"][]="";
                                $_REQUEST["tablaSoporteContable"][]="0";
                                $_REQUEST["tablaSoporteContable"][]="0";
                                $_REQUEST["tablaSoporteContable"][]="0,00";
                                $_REQUEST["tablaSoporteContable"][]="0,00";
                                $_REQUEST["tablaSoporteContable"][]="0";
                                $_REQUEST["tablaSoporteContable"][]=$tercero;
                                $_REQUEST["tablaSoporteContable"][]="";
                                $_REQUEST["tablaSoporteContable"][]="0";
                                $_REQUEST["tablaSoporteContable"][]=$debito;
                                $_REQUEST["tablaSoporteContable"][]=$credito;
                                /*
                                switch ($nivel) {
                                    case '0':
                                    case '1':
                                    case '5':
                                    case '6':
                                    case '7':
                                        $_REQUEST["tablaSoporteContable"][]=$debito;
                                        $_REQUEST["tablaSoporteContable"][]=$credito;
                                        break;
                                    default:
                                        $_REQUEST["tablaSoporteContable"][]=$credito;
                                        $_REQUEST["tablaSoporteContable"][]=$debito;
                                        break;
                                }
                                */
                                $_REQUEST["tablaSoporteContable"][]="0";
                                $_REQUEST["tablaSoporteContable"][]="2017-12-31";//date('Y-m-d');
                                $_REQUEST["tablaSoporteContable"][]="0,00";
                                $_REQUEST["tablaSoporteContable"][]="0,00";
                                $_REQUEST["tablaSoporteContable"][]="0,00";
                                $_REQUEST["tablaSoporteContable"][]="0,00";
                                $_REQUEST["tablaSoporteContable"][]="0";
                                $_REQUEST["tablaSoporteContable"][]="0";
                                $_REQUEST["tablaSoporteContable"][]="";
                                $_REQUEST["tablaSoporteContable"][]="";
                                $fila++;
                                $importado = $auxImportado;
                            }
                            $cuenta = $auxcuenta;
                        } else {
                            $importado = -100;
                        }
                        break;
                    case '4':
                        $dato["a"] = trim(str_replace("  ", " ", $dato["a"]));
                        $dato["a"] = trim(str_replace("  ", " ", $dato["a"]));
                        $dato["a"] = trim(str_replace("  ", " ", $dato["a"]));
                        $dato["a"] = trim(str_replace("  ", " ", $dato["a"]));
                        $dato["a"] = trim(str_replace(" ", "", $dato["a"]));
                        $dato["a"] = trim($dato["a"]);
                        $ceros = explode("0", $dato["a"]);
                        if (strrpos($dato["a"], "00") !== false || strrpos($dato["a"], "A") || strrpos($dato["a"], "B") || strrpos($dato["a"], "C") !== false || strrpos($dato["a"], "D") !== false || strrpos($dato["a"], "E") !== false || strrpos($dato["a"], "I") !== false || strrpos($dato["a"], "O") !== false || strrpos($dato["a"], "U") !== false) {//BUSQUEDA EN LA CADENA DE TEXTO Y ENCUENTRA
                            $dato["a"] = trim(ltrim(preg_replace("/[^0-9]/", "0", "$dato[a]")), "0");
                            $dato["a"] = trim(rtrim(preg_replace("/[^0-9]/", "0", "$dato[a]")), "0");
                        }
                        $dato["b"] = trim($dato["b"]);
                        if (ctype_digit($dato["a"])) {//codigo cuenta
                            $sql = "INSERT INTO puc_empresa
                                    (codigo, cuenta, empresa)
                                    VALUES('$dato[a]', '$dato[b]', $_SESSION[empresa])";
                            $resultado = $conexion->getDBCon()->Execute($sql);
                            $importado = $conexion->getDBCon()->insert_Id();
                            if ($resultado === false) {
                                print "<pre>"; print_r($sql); print "</pre>\n";
                                print "<pre>"; print_r($sql . " " . $conexion->getDBCon()->ErrorMsg()); print "</pre>\n";
                            }
                            $fila++;
                        } else {
                            $importado = -100;
                        }
                        break;
                    case '14':
                        if (ctype_digit($dato["a"])) {//codigo cl
                            $dato["d"] = ($dato["d"] == "") ? "" : (substr($dato["d"], 6, 4) . "-" . substr($dato["d"], 3, 2) . "-" . substr($dato["d"], 0, 2));
                            $dato["n"] = ($dato["n"] == "") ? "" : (substr($dato["n"], 6, 4) . "-" . substr($dato["n"], 3, 2) . "-" . substr($dato["n"], 0, 2));
                            switch ($dato["m"]) {
                                case ''://RP
                                    $key = array_search($dato["e"], array_column($certificadosDisponibilidadPresupuestal, 'numero_cdp'));
                                    if ($key == null && $key != 0) {
                                        $key = -1;
                                    }
                                    $cdp = ($certificadosDisponibilidadPresupuestal[$key]['numero_cdp'] == $dato["e"]) ? $certificadosDisponibilidadPresupuestal[$key]['cdp'] : 0;
                                    $plan = ($certificadosDisponibilidadPresupuestal[$key]['numero_cdp'] == $dato["e"]) ? $certificadosDisponibilidadPresupuestal[$key]['plan_presupuestal'] : 0;
                                    if ($cdp != 0) {
                                        $key = array_search("$cdp*$dato[b]", array_column($registroPresupuestalesImportacion, 'cdp_rp'));
                                        if ($key == null && $key != 0) {
                                            $key = -1;
                                        }
                                        $rp = ($registroPresupuestalesImportacion[$key]['cdp_rp'] == "$cdp*$dato[b]") ? $registroPresupuestalesImportacion[$key]['registro_presupuestal'] : 0;
                                        if ($rp == 0) {
                                            // Conocemos el próximo consecutivo del Registro Presupuestal.
                                            $sql = "SELECT MAX(rp.registro_presupuestal) + 1 AS proximo_rp
                                                    FROM registro_presupuestal rp";
                                            $resultado = $conexion->getDBCon()->Execute($sql);
                                            $respuesta = $resultado->FetchRow();
                                            $rp = ($respuesta['proximo_rp'] == '') ? 1 : $respuesta['proximo_rp'];
                                            //Insertar Registro Presupuestal.
                                            $sql = "INSERT INTO registro_presupuestal(registro_presupuestal,cdp,numero_rp,plan_presupuestal,empresa,usuario)
                                                    VALUES($rp, $cdp,'$dato[b]', $plan, $empresa,'$usuario')";
                                            $resultado = $conexion->getDBCon()->Execute($sql);
                                            if ($resultado === false) {
                                                $sqls[] = $sql;
                                                $errors[] = $conexion->getDBCon()->ErrorMsg();
                                            }
                                            $registroPresupuestalesImportacion[] = Array(   "registro_presupuestal"=> $rp,
                                                                                            "cdp_rp"=> "$cdp*$dato[b]",
                                                                                            "obligacion"=> 0,
                                                                                            "pago"=> 0);
                                            $sql = "INSERT INTO registro_presupuestal_campos(valor, registro_presupuestal, campo)
                                                    VALUES('$dato[d]', $rp, 1237)";
                                            $resultado = $conexion->getDBCon()->Execute($sql);
                                            if ($resultado === false) {
                                                $sqls[] = $sql;
                                                $errors[] = $conexion->getDBCon()->ErrorMsg();
                                            }
                                            $key = array_search($dato['f'], array_column($terceroSistemaId, 'nit'));
                                            if ($key == null && $key != 0) {
                                                $key = -1;
                                            }
                                            $tercero = ($terceroSistemaId[$key]['nit'] == $dato['f']) ? $terceroSistemaId[$key]['tercero'] : 0;
                                            $sql = "INSERT INTO registro_presupuestal_campos(valor, registro_presupuestal, campo)
                                                    VALUES('$tercero', $rp, 1240)";
                                            $resultado = $conexion->getDBCon()->Execute($sql);
                                            if ($resultado === false) {
                                                $sqls[] = $sql;
                                                $errors[] = $conexion->getDBCon()->ErrorMsg();
                                            }
                                        }
                                        $importado = $rp;
                                        $key = array_search("$dato[h]*$dato[i]$dato[j]", array_column($datosRubroInfoImportacion, 'rubrofuente'));
                                        if ($key == null && $key != 0) {
                                            $key = -1;
                                        }
                                        $planPresupuestalRubros = ($datosRubroInfoImportacion[$key]['rubrofuente'] == "$dato[h]*$dato[i]$dato[j]") ? $datosRubroInfoImportacion[$key]['plan_presupuestal_rubros'] : 0;
                                        if ($planPresupuestalRubros != 0) {
                                            $key = array_search("$planPresupuestalRubros*$cdp", array_column($datosCdpRubroInfoImportacion, 'rubro_cdp'));
                                            if ($key == null && $key != 0) {
                                                $key = -1;
                                            }
                                            $cdpRubro = ($datosCdpRubroInfoImportacion[$key]['rubro_cdp'] == "$planPresupuestalRubros*$cdp") ? $datosCdpRubroInfoImportacion[$key]['cdp_rubro'] : 0;
                                            if ($cdpRubro != 0) {
                                                $sql = "INSERT INTO registro_presupuestal_rubrocdp(registro_presupuestal, campo, cdp_rubro, valor_rubro_rp, empresa, usuario)
                                                        VALUES($rp,1245, $cdpRubro,'$dato[o]', $empresa,'$usuario')";
                                                $resultado = $conexion->getDBCon()->Execute($sql);
                                                if ($resultado === false) {
                                                    $sqls[] = $sql;
                                                    $errors[] = $conexion->getDBCon()->ErrorMsg();
                                                }
                                                $fila++;
                                            } else {
                                                $importado = -100;
                                            }
                                        } else {
                                            $importado = -100;
                                        }
                                    } else {
                                        $importado = -100;
                                    }
                                    break;
                                case 'ORD/ PAGO':
                                    $key = array_search($dato["e"], array_column($certificadosDisponibilidadPresupuestal, 'numero_cdp'));
                                    if ($key == null && $key != 0) {
                                        $key = -1;
                                    }
                                    $cdp = ($certificadosDisponibilidadPresupuestal[$key]['numero_cdp'] == $dato["e"]) ? $certificadosDisponibilidadPresupuestal[$key]['cdp'] : 0;
                                    $plan = ($certificadosDisponibilidadPresupuestal[$key]['numero_cdp'] == $dato["e"]) ? $certificadosDisponibilidadPresupuestal[$key]['plan_presupuestal'] : 0;
                                    if ($cdp != 0) {
                                        $key = array_search("$cdp*$dato[b]", array_column($registroPresupuestalesImportacion, 'cdp_rp'));
                                        if ($key == null && $key != 0) {
                                            $key = -1;
                                        }
                                        $rp = ($registroPresupuestalesImportacion[$key]['cdp_rp'] == "$cdp*$dato[b]") ? $registroPresupuestalesImportacion[$key]['registro_presupuestal'] : 0;
                                        if ($rp != 0 && $registroPresupuestalesImportacion[$key]['obligacion'] == 0) {
                                            $sql = "SELECT MAX(m.obligacion_presupuestal) + 1 AS proximo_obligacion
                                                    FROM obligacion_presupuestal m
                                                    WHERE m.empresa = $_SESSION[empresa]";
                                            $resultado = $conexion->getDBCon()->Execute($sql);
                                            $respuesta = $resultado->FetchRow();
                                            $obligacion = ($respuesta['proximo_obligacion'] == '') ? 1 : $respuesta['proximo_obligacion'];
                                            $sql = "INSERT INTO obligacion_presupuestal(obligacion_presupuestal,numero_obligacion,registro_presupuestal,plan_presupuestal,empresa,usuario)
                                                    VALUES($obligacion,'$dato[l]', $rp, $plan, $_SESSION[empresa], '$_SESSION[usuario]')";
                                            $resultado = $conexion->getDBCon()->Execute($sql);
                                            if ($resultado === false) {
                                                $sqls[] = $sql;
                                                $errors[] = $conexion->getDBCon()->ErrorMsg();
                                            }
                                            $obligacionPresupuestalImportacion[] = Array(   "obligacion_presupuestal"=> $obligacion,
                                                                                            "rp_plan"=> "$rp*$plan");
                                            $registroPresupuestalesImportacion[$key]['obligacion'] = $obligacion;
                                            $importado = $obligacion;
                                            $fila++;
                                            // Guardamos Consecutivo en Tabla obligacion_presupuestal_campos.
                                            $sql = "INSERT INTO obligacion_presupuestal_campos(valor, obligacion_presupuestal, campo)
                                                    VALUES('$dato[l]', $obligacion, 1272)";
                                            $resultado = $conexion->getDBCon()->Execute($sql);
                                            if ($resultado === false) {
                                                $sqls[] = $sql;
                                                $errors[] = $conexion->getDBCon()->ErrorMsg();
                                            }
                                            $sql = "INSERT INTO obligacion_presupuestal_campos(valor, obligacion_presupuestal, campo)
                                                    VALUES('$dato[n]', $obligacion, 1268)";
                                            $resultado = $conexion->getDBCon()->Execute($sql);
                                            if ($resultado === false) {
                                                $sqls[] = $sql;
                                                $errors[] = $conexion->getDBCon()->ErrorMsg();
                                            }
                                            $consultarRegistroPendiente = $centralConsulta->consultarRegistroPendiente($rp);
                                            //foreach ($consultarRegistroPendiente as $registro) {
                                            if (isset($consultarRegistroPendiente[0]["registro_presupuestal_rubrocdp"])) {
                                                $sql = "INSERT INTO obligacion_presupuestal_rubrorp(obligacion_presupuestal, campo, registro_presupuestal_rubrocdp, valor_obligacion, empresa, usuario)
                                                        VALUES($obligacion,1245, {$consultarRegistroPendiente[0][registro_presupuestal_rubrocdp]}, $dato[p], $_SESSION[empresa], '$_SESSION[usuario]')";
                                                $resultado = $conexion->getDBCon()->Execute($sql);
                                                if ($resultado === false) {
                                                    $sqls[] = $sql;
                                                    $errors[] = $conexion->getDBCon()->ErrorMsg();
                                                }
                                            }
                                        } else {
                                            if ($rp != 0 && $registroPresupuestalesImportacion[$key]['obligacion'] != 0) {
                                                $obligacion = $registroPresupuestalesImportacion[$key]['obligacion'];
                                                $importado = $obligacion;
                                                $consultarRegistroPendiente = $centralConsulta->consultarRegistroPendiente($rp);
                                                if (isset($consultarRegistroPendiente[0]["registro_presupuestal_rubrocdp"])) {
                                                    $sql = "INSERT INTO obligacion_presupuestal_rubrorp(obligacion_presupuestal, campo, registro_presupuestal_rubrocdp, valor_obligacion, empresa, usuario)
                                                            VALUES($obligacion,1245, {$consultarRegistroPendiente[0][registro_presupuestal_rubrocdp]}, $dato[p], $_SESSION[empresa], '$_SESSION[usuario]')";
                                                    $resultado = $conexion->getDBCon()->Execute($sql);
                                                    if ($resultado === false) {
                                                        $sqls[] = $sql;
                                                        $errors[] = $conexion->getDBCon()->ErrorMsg();
                                                    }
                                                }
                                            } else {
                                                $importado = -100;
                                            }
                                        }
                                    } else {
                                        $importado = -100;
                                    }
                                    break;
                                case 'COMP/PAGO':
                                    $key = array_search($dato["e"], array_column($certificadosDisponibilidadPresupuestal, 'numero_cdp'));
                                    if ($key == null && $key != 0) {
                                        $key = -1;
                                    }
                                    $cdp = ($certificadosDisponibilidadPresupuestal[$key]['numero_cdp'] == $dato["e"]) ? $certificadosDisponibilidadPresupuestal[$key]['cdp'] : 0;
                                    $plan = ($certificadosDisponibilidadPresupuestal[$key]['numero_cdp'] == $dato["e"]) ? $certificadosDisponibilidadPresupuestal[$key]['plan_presupuestal'] : 0;
                                    if ($cdp != 0) {
                                        $key = array_search("$cdp*$dato[b]", array_column($registroPresupuestalesImportacion, 'cdp_rp'));
                                        if ($key == null && $key != 0) {
                                            $key = -1;
                                        }
                                        $rp = ($registroPresupuestalesImportacion[$key]['cdp_rp'] == "$cdp*$dato[b]") ? $registroPresupuestalesImportacion[$key]['registro_presupuestal'] : 0;
                                        if ($rp != 0) {
                                            $auxPago = $registroPresupuestalesImportacion[$key]['pago'];
                                            $key = array_search("$rp*$plan", array_column($obligacionPresupuestalImportacion, 'rp_plan'));
                                            if ($key == null && $key != 0) {
                                                $key = -1;
                                            }
                                            $obligacion = ($obligacionPresupuestalImportacion[$key]['rp_plan'] == "$rp*$plan") ? $obligacionPresupuestalImportacion[$key]['obligacion_presupuestal'] : 0;
                                            if ($auxPago == 0) {
                                                // Conocemos el próximo consecutivo del Pago Presupuestal.
                                                $sql = "SELECT MAX(m.pago_presupuestal) + 1 AS proximo_pago
                                                        FROM pago_presupuestal m
                                                        WHERE m.empresa = $_SESSION[empresa]";
                                                $resultado = $conexion->getDBCon()->Execute($sql);
                                                $respuesta = $resultado->FetchRow();
                                                $pago = ($respuesta['proximo_pago'] == '') ? 1 : $respuesta['proximo_pago'];
                                                $sql = "INSERT INTO pago_presupuestal(pago_presupuestal,obligacion_presupuestal,numero_pago,plan_presupuestal,empresa,usuario)
                                                        VALUES($pago, $obligacion, '$dato[l]', '$plan', $_SESSION[empresa], '$_SESSION[usuario]')";
                                                $resultado = $conexion->getDBCon()->Execute($sql);
                                                if ($resultado === false) {
                                                    $sqls[] = $sql;
                                                    $errors[] = $conexion->getDBCon()->ErrorMsg();
                                                }
                                                $importado = $pago;
                                                $registroPresupuestalesImportacion[$key]['pago'] = $pago;
                                                // Guardamos Consecutivo en Tabla CDP_CAMPOS.
                                                $sql = "INSERT INTO pago_presupuestal_campos(valor, pago_presupuestal, campo)
                                                        VALUES('$dato[l]', $pago, 1286)";
                                                $resultado = $conexion->getDBCon()->Execute($sql);
                                                if ($resultado === false) {
                                                    $sqls[] = $sql;
                                                    $errors[] = $conexion->getDBCon()->ErrorMsg();
                                                }
                                                $sql = "INSERT INTO pago_presupuestal_campos(valor, pago_presupuestal, campo)
                                                        VALUES('$dato[n]', $pago, 1285)";
                                                $resultado = $conexion->getDBCon()->Execute($sql);
                                                if ($resultado === false) {
                                                    $sqls[] = $sql;
                                                    $errors[] = $conexion->getDBCon()->ErrorMsg();
                                                }
                                                $sql = "INSERT INTO pago_presupuestal_campos(valor, pago_presupuestal, campo)
                                                        VALUES('$dato[u]', $pago, 364)";
                                                $resultado = $conexion->getDBCon()->Execute($sql);
                                                if ($resultado === false) {
                                                    $sqls[] = $sql;
                                                    $errors[] = $conexion->getDBCon()->ErrorMsg();
                                                }
                                                $fila++;
                                            } else {
                                                $pago = $registroPresupuestalesImportacion[$key]['pago'];
                                                $importado = $pago;
                                            }
                                            $key = array_search($dato['f'], array_column($terceroSistemaId, 'nit'));
                                            if ($key == null && $key != 0) {
                                                $key = -1;
                                            }
                                            $tercero = ($terceroSistemaId[$key]['nit'] == $dato['f']) ? $terceroSistemaId[$key]['tercero'] : 0;
                                            $conceptoHistoricoFactura = ($dato['u'] == "") ? 0 : $centralConsulta->conceptoHistoricoFactura($dato['u'], $tercero, $omitidos);
                                            $conceptoHistoricoFactura = explode("*", $conceptoHistoricoFactura);
                                            //echo $conceptoHistoricoFactura;die($conceptoHistoricoFactura);
                                            //print "<pre>"; print_r($conceptoHistoricoFactura); print "</pre>\n";
                                            //if ($fila > 120) {
                                                //die();
                                            //}
                                            if ($conceptoHistoricoFactura[0] != "0") {
                                                $omitidos .= ", $conceptoHistoricoFactura[0], $conceptoHistoricoFactura[1]";
                                                $sql = "UPDATE soporte_contable
                                                        SET registro_presupuestal = $rp,
                                                        obligacion_presupuestal = $obligacion
                                                        WHERE soporte_contable = $conceptoHistoricoFactura[0]";
                                                $resultado = $conexion->getDBCon()->Execute($sql);
                                                if ($resultado === false) {
                                                    $sqls[] = $sql;
                                                    $errors[] = $conexion->getDBCon()->ErrorMsg();
                                                }
                                                if ($conceptoHistoricoFactura[1] != "0") {
                                                    $key = array_search("$conceptoHistoricoFactura[1]", array_column($soporteSistemaId, 'soporte_contable'));
                                                    if ($key == null && $key != 0) {
                                                        $key = -1;
                                                    }
                                                    $conceptoHistoricoFactura[1] = ($soporteSistemaId[$key]['soporte_contable'] == "$conceptoHistoricoFactura[1]" && $soporteSistemaId[$key]['comprobante'] == "EGRESOS DE BANCOS") ? $conceptoHistoricoFactura[1] : "0";
                                                    if ($conceptoHistoricoFactura[1] != "0") {
                                                        $sql = "UPDATE soporte_contable
                                                                SET pago_presupuestal = $pago
                                                                WHERE soporte_contable = $conceptoHistoricoFactura[1]";
                                                        $resultado = $conexion->getDBCon()->Execute($sql);
                                                        if ($resultado === false) {
                                                            $sqls[] = $sql;
                                                            $errors[] = $conexion->getDBCon()->ErrorMsg();
                                                        }
                                                    }
                                                }
                                            }
                                            $key = array_search("$dato[h]*$dato[i]$dato[j]", array_column($datosRubroInfoImportacion, 'rubrofuente'));
                                            if ($key == null && $key != 0) {
                                                $key = -1;
                                            }
                                            $planPresupuestalRubros = ($datosRubroInfoImportacion[$key]['rubrofuente'] == "$dato[h]*$dato[i]$dato[j]") ? $datosRubroInfoImportacion[$key]['plan_presupuestal_rubros'] : 0;
                                            $consultarObligacionPendiente = $centralConsulta->consultarObligacionPendiente($obligacion);
                                            foreach ($consultarObligacionPendiente as &$obligaciones) {
                                                if ($obligaciones["rubro"] == $planPresupuestalRubros) {
                                                    $sql = "INSERT INTO pago_presupuestal_rubro(pago_presupuestal, campo, obligacion_presupuestal_rubrorp, valor_pago, empresa, usuario)
                                                            VALUES($pago, 1245, $obligaciones[obligacion_presupuestal_rubrorp], $dato[q], $_SESSION[empresa], '$_SESSION[usuario]')";
                                                    $resultado = $conexion->getDBCon()->Execute($sql);
                                                    if ($resultado === false) {
                                                        $sqls[] = $sql;
                                                        $errors[] = $conexion->getDBCon()->ErrorMsg();
                                                    }
                                                }
                                            }
                                        } else {
                                            $importado = -100;
                                        }
                                    } else {
                                        $importado = -100;
                                    }
                                    break;
                                case 'MODIFICA.':
                                    break;
                            }
                        } else {
                            $importado = -100;
                        }
                        break;

                    case '9013':
                    case '9014':
                    case '9015':
                    case '9016':
                    case '9017':
                    case '9018':
                    case '9019':
                    case '9020':
                    case '9021':
                    case '9022':

                    case '9040':
                    case '9041':
                    case '9042':
                    case '9043':
                    case '9044':
                    case '9045':
                    case '9048'://para prueba
                    case '9050'://para prueba
                        if (ctype_digit($dato["a"])) {//codigo



                            //Asignamos los valores
                            $referencia_catastral  = trim($dato["a"]);
                            $dato["d"] = trim(str_replace("*", " ", $dato["d"]));
                            $dato["d"] = trim(str_replace("-", " ", $dato["d"]));
                            $dato["d"] = trim(str_replace(".", " ", $dato["d"]));
                            $dato["d"] = trim(str_replace("  ", " ", $dato["d"]));
                            $dato["d"] = trim(str_replace("  ", " ", $dato["d"]));
                            $dato["d"] = trim(str_replace("  ", " ", $dato["d"]));
                            $dato["d"] = trim(str_replace("  ", " ", $dato["d"]));
                            $nombre_porpietario    = trim($dato["d"]);
                            $tipo_documento        = 1;
                            $auxiliar = explode("-", $dato["c"]);
                            $dato["c"] = ltrim(ltrim(preg_replace("/[^0-9]/", "", $auxiliar[0])), "0");//nit
                            $numero_documento      = ltrim($dato["c"],'0');
                            $direccion             = trim($dato["f"]);
                            $area_terreno          = trim($dato["m"]);
                            $area_construida       = trim($dato["o"]);
                            $area_exenta           = trim($dato["n"]);
                            $avaluo                = (trim($dato["l"])== "") ? 0: trim($dato["l"]);
                            $vigencia              = trim($dato["g"]);
                            $tipo_predio           = (trim($dato["j"]) == "URBANO") ? 2 : 1;
                            $matricula_inmobiliaria= trim($dato["b"]);
                            $municipio             = 0;

                            $fecha_pago            = date($format = "Y-m-d", PHPExcel_Shared_Date::ExcelToPHP($fecha["ak"]));
                            $descuento_tributario                = (trim($dato["ab"])== "") ? 0: trim($dato["ab"]);
                            $descuento_interes_sobretasa         = (trim($dato["af"])== "") ? 0: trim($dato["af"]);

                            $numero_documento_zero      = trim($dato["c"]);


                            $atributo = (trim($dato["p"])== "") ? 100: trim($dato["p"]);
                            $subatributo = (trim($dato["q"])== "") ? 0: trim($dato["q"]);
                            $tarifa = (trim($dato["t"])== "") ? 0: trim($dato["t"]);
                            $numero_factura = trim($dato["v"]);
                            $valor_total = (trim($dato["y"])== "") ? 0: trim($dato["y"]);
                            $impuesto = (trim($dato["z"])== "") ? 0: trim($dato["z"]);
                            $interes = (trim($dato["aa"])== "") ? 0: trim($dato["aa"]);
                            $descuento = (trim($dato["ac"])== "") ? 0: trim($dato["ac"]);
                            $sobretasa = (trim($dato["ad"])== "") ? 0: trim($dato["ad"]);
                            $interes_sobretasa = (trim($dato["ae"])== "") ? 0: trim($dato["ae"]);


                            $estado = trim($dato["am"]);
                            //$numero_documento = ($numero_documento == "") ? $dato["a"] : $numero_documento;

                             $key = array_search($vigencia, array_column($vigenciaPredial, 'descripcion'));
                             $key = ($key == null && $key != 0) ? -1 : $key;
                             $vigenciaId = ($vigenciaPredial[$key]['descripcion'] == $vigencia) ? $vigenciaPredial[$key]['predial_vigencia'] : 0;


                            // //Para ver si existe el predio
                             $key = array_search($referencia_catastral, array_column($informacionPredioInicial, 'referencia_catastral'));
                             $key = ($key == null && $key != 0) ? -1 : $key;
                             $predio = ($informacionPredioInicial[$key]['referencia_catastral'] == $referencia_catastral) ? $informacionPredioInicial[$key]['predial_predio'] : 0;


                            // $key = array_search($predio.'*'.$vigenciaId.'*9004', array_column($informacionPredioAtributo, 'predial_predio_campo'));
                            // $key = ($key == null && $key != 0) ? -1 : $key;
                            // $predial_atributo = ($informacionPredioAtributo[$key]['predial_predio_campo'] == $predio.'*'.$vigenciaId.'*9004') ? $informacionPredioAtributo[$key]['predial_predio_campos'] : 0;
                            // print "<pre>"; print_r($predial_atributo); print "</pre>\n";
//___________________________________________________________________________________________________________________________________________________________________________________________________       //COMIENZA SUB ATRIBUTO
                            // if($predio != 0)
                            // {


                            //     if($vigencia > '2012') {

                            //         switch ($atributo) {
                            //                         case '01':  // PREDIOS URBANOS Y RURALES PARA VIVIENDA

                            //                             if ($subatributo == '96' || $subatributo == '98' || $subatributo == '99') {
                            //                                 if ($subatributo == '96'){ //PREDIO ARENAUTICA
                            //                                      $atributoFinal= 63;

                            //                                 }if ($subatributo == '98'){ // PREDIOS DEL MUNICIPIO POR VERIFICAR
                            //                                      $atributoFinal= 65;

                            //                                 }if ($subatributo == '99'){ //PREDIOS DEL MUNICIPIO
                            //                                      $atributoFinal= 66;
                            //                                 }
                            //                             } else { //PREDIOS DEL MUNICIPIO
                            //                                 $atributoFinal= 1;
                            //                             }
                            //                             break;
                            //                         case '02':  //    PREDIOS URBANOS Y RURALES ESPECIALES

                            //                           if ($subatributo == '01') { //COMERCIALES EN AREA URBUNA Y RURAL
                            //                                 $atributoFinal= 2;

                            //                             } if ($subatributo == '02') { //   INDUSTRIALES EN SUELO RURAL Y URBANO
                            //                                 $atributoFinal= 3;
                            //                             }
                            //                             if ($subatributo == '03') { // FINANCIEROS
                            //                                 $atributoFinal= 4;
                            //                             }
                            //                             if ($subatributo == '04') { //     PREDIOS DE USO INSTITUCIONAL PÚBLICO Y PRIVADO
                            //                                 $atributoFinal= 5;
                            //                             }
                            //                             if ($subatributo == '05') { // DÉPOSITOS, PARQUEADEROS, SERVICIOS Y OTRAS ACTIV
                            //                                 $atributoFinal= 6;
                            //                             }
                            //                             if ($subatributo == '06') { // PREDIOS URBANIZABLES NO URBANIZADOS
                            //                                 $atributoFinal= 7;
                            //                             }
                            //                             if ($subatributo == '07') { // PREDIOS URBANIZADOS NO EDIFICADOS  250m2
                            //                                 $atributoFinal= 8;
                            //                             }
                            //                             if ($subatributo == '08') { // PREDIOS URBANIZADOS NO EDIFICADOS  250m2 y 500m2
                            //                                 $atributoFinal= 9;
                            //                             }
                            //                             if ($subatributo == '09') { // PREDIOS URBANIZADOS NO EDIFICADOS  500m2
                            //                                 $atributoFinal= 10;
                            //                             }
                            //                             if ($subatributo == '10') { //     PREDIOS NO URBANIZABLES
                            //                                 $atributoFinal= 11;
                            //                             }
                            //                             if ($subatributo == '0'){ //PREDIOS DEL MUNICIPIO
                            //                                  $atributoFinal= x;
                            //                             }
                            //                             break;


                            //                         case '03':  // VIVIENDA URBANA ESTRATOS 1, 2 Y 3

                            //                             if ($subatributo == '01' || $subatributo == '02' || $subatributo == '03') { //VIVIENDA URBANA Y RURAL
                            //                                 $atributoFinal= 12;
                            //                             }
                            //                             if ($subatributo == '04') { // PEQUEÑA PROPIEDAD RURAL CON DESTINO ECONÓMICO
                            //                                 $atributoFinal= 13;
                            //                             }

                            //                             if ($subatributo == '05') { // SUELOS DE PROTECCIÓN, CONSERVACIÓN Y RECUPERACIÓN
                            //                                 $atributoFinal= 14;
                            //                             }

                            //                             if ($subatributo == '06') { // PREDIOS RURALES DESTINADOS AL TURISMO
                            //                                 $atributoFinal= 15;
                            //                             }

                            //                             if ($subatributo == '07') { // DEMÁS PROPIEDADES RURALES SUPERIORES A 61
                            //                                 $atributoFinal= 16;
                            //                             }
                            //                             if ($subatributo == '08') { // PREDIOS DONDE SE EXTRAE ARCILLA, BALASTRO, ARENA
                            //                                 $atributoFinal= 17;
                            //                             }
                            //                             if ($subatributo == '09') { // EMPRESAS PRESTADORAS DE SERVICIOS PÚBLICOS DOMICILIARIOS
                            //                                 $atributoFinal= 18;
                            //                             }
                            //                             if ($subatributo == '10') { // PREDIOS DESTINADOS A INSTALACIONES Y MONTAJE DE EQUIPOS PARA EXPLORACIÓN
                            //                                 $atributoFinal= 19;
                            //                             }
                            //                             if ($subatributo == '0'){ //PREDIOS DEL MUNICIPIO
                            //                                  $atributoFinal= x;
                            //                             }
                            //                          break;

                            //                          case '04':  //   Exentos

                            //                           if ($subatributo == '01') { // MADRES COMUNITARIAS RESOLUCION 1670-2012
                            //                                 $atributoFinal= 51;
                            //                             }
                            //                            if ($subatributo == '02') { // ACUERDO 013 ART. 42 LIT D. HOGARES COMUN
                            //                                 $atributoFinal= 52;
                            //                             }
                            //                              if ($subatributo == '03') { // ACUERDO 03/2005 EXEN.10 AÑOS 2006-2015
                            //                                 $atributoFinal= 53;
                            //                             }

                            //                              if ($subatributo == '04') { // PREDIOS A NOMBRE DEL IDURY
                            //                                 $atributoFinal= 54;
                            //                             }
                            //                              if ($subatributo == '05') { // AACUERDO 013 ART 41 LIT H-IGLESIAS
                            //                                 $atributoFinal= 55;
                            //                             }
                            //                              if ($subatributo == '06') { // ACUERDO 013 ART 41 LT I BOMB-CRUZ
                            //                                 $atributoFinal= 56;
                            //                             }
                            //                              if ($subatributo == '07') { // ACUERDO 013 PARQUEADEROS
                            //                                 $atributoFinal= 57;
                            //                             }
                            //                              if ($subatributo == '08') { //PREDIOS A NOMBRE DEL ACUEDUCTO
                            //                                 $atributoFinal= 58;
                            //                             }
                            //                              if ($subatributo == '09') { //PREDIOS A NOMBRE DE LA ESE
                            //                                 $atributoFinal= 59;
                            //                             }

                            //                              if ($subatributo == '10') { //PREDIOS DE LA NACION ATR 41 L E
                            //                                 $atributoFinal= 60;
                            //                             }

                            //                              if ($subatributo == '11') { //PREDIOS A NOMBRE DE CORPORINOQUIA
                            //                                 $atributoFinal= 61;
                            //                             }
                            //                             if ($subatributo == '12') { //PREDIOS A NOMBRE DEL IDRY
                            //                                 $atributoFinal= 62;
                            //                             }
                            //                              if ($subatributo == '96') { //PREDIO ARENAUTICA
                            //                                 $atributoFinal= 63;
                            //                             }
                            //                              if ($subatributo = '97') { //PRED EN SNR A NOMBRE DE OTROS Y NO MUNICIPIO
                            //                                 $atributoFinal= 64;
                            //                             }
                            //                             if ($subatributo = '98') { //PREDIOS DEL MUNICIPIO POR VERIFICAR
                            //                                 $atributoFinal= 65;
                            //                             }
                            //                             if ($subatributo = '99') { //PREDIOS DEL MUNICIPIO
                            //                                 $atributoFinal= 66;
                            //                             }
                            //                             if ($subatributo == '0'){ //PREDIOS DEL MUNICIPIO
                            //                                  $atributoFinal= x;
                            //                             }
                            //                             default:
                            //                             $atributoFinal=0;
                            //                             break;
                            //         }

                            //             $sql="INSERT INTO predial_predio_campos(predial_predio, predial_vigencia, campo, valor)
                            //                     VALUES($predio, $vigenciaId, 9004, '$atributoFinal')";

                            //                     $resultado = $conexion->getDBCon()->Execute($sql);
                            //                     $predial_atributo = $conexion->getDBCon()->insert_Id();
                            //                     if ($resultado === false)
                            //                     {
                            //                          print "<pre>"; print_r($sql . " " . $conexion->getDBCon()->ErrorMsg()); print "</pre>\n";
                            //                     }
                            //                     $importado = $predial_atributo;
                            //     } else if($vigencia >'1997' && $vigencia < '2013') {
                            //         switch ($atributo) {
                            //                     case '01':  // RURAL
                            //                             $atributoFinal= 39;
                            //                          break;

                            //                     case '02': //ESTRATO 1 Y 2
                            //                              $atributoFinal= 23;
                            //                         break;

                            //                     case '03': //ESTRATO 3 Y 4
                            //                              $atributoFinal= 25;
                            //                             break;

                            //                     case '04': // ESTRATO 5 Y 6
                            //                              $atributoFinal= 26;
                            //                         break;

                            //                     case '05'://INTERES SOCIAL
                            //                              $atributoFinal= 67;
                            //                          break;


                            //                     case '06': //LOTES URBANIZADOS NO URBANIZABLES
                            //                         if($vigencia >'1997' && $vigencia < '2007') {
                            //                              $atributoFinal= 34;
                            //                         }else{
                            //                              $atributoFinal= 50;
                            //                         }
                            //                      break;

                            //                     case '07': //MENOR DE 250 MTRS

                            //                         if($vigencia >'1997' && $vigencia < '2007') {
                            //                              $atributoFinal= 35;
                            //                         }else{
                            //                             $atributoFinal= 47;
                            //                         }
                            //                      break;

                            //                     case '08': //ENTRE 251 - 500 MTRS
                            //                         if($vigencia >'1997' && $vigencia < '2007') {
                            //                              $atributoFinal= 36;
                            //                         }else{
                            //                             $atributoFinal= 48;
                            //                         }
                            //                     break;

                            //                     case '09': //MAYORES DE 500 MTRS
                            //                         if($vigencia >'1997' && $vigencia < '2007') {
                            //                              $atributoFinal= 37;
                            //                         }else{
                            //                              $atributoFinal= 49;
                            //                         }
                            //                     break;

                            //                     case '10': //EXENTOS
                            //                              $atributoFinal= 75;
                            //                      break;

                            //                     case '11'://URBANOS INDUSTRIALES Y COMERCIALES
                            //                              $atributoFinal= 28;
                            //                      break;

                            //                     case '12': //PETROLERAS
                            //                              $atributoFinal= 41;
                            //                     break;

                            //                     case '13': //EMPRESAS SERVICIOS PUBLICOS
                            //                              $atributoFinal= 46;
                            //                     break;
                            //                    case '14': //CORREGIMIENTOS
                            //                              $atributoFinal= 80;
                            //                         break;

                            //                     default:
                            //                     $atributoFinal=x;

                            //                     break;
                            //         }
                            //             $sql="INSERT INTO predial_predio_campos(predial_predio, predial_vigencia, campo, valor)
                            //                     VALUES($predio, $vigenciaId, 9004, '$atributoFinal')";

                            //                     $resultado = $conexion->getDBCon()->Execute($sql);
                            //                     $predial_atributo = $conexion->getDBCon()->insert_Id();
                            //                     if ($resultado === false)
                            //                     {
                            //                          print "<pre>"; print_r($sql . " " . $conexion->getDBCon()->ErrorMsg()); print "</pre>\n";
                            //                     }
                            //                     $importado = $predial_atributo;
                            //     } else if($vigencia >'1992' && $vigencia <= '1997') // End vigencia mayor a 1997 y menor a 2007
                            //     {
                            //         switch ($atributo) {
                            //                         case '01':  // RURAL
                            //                                 $atributoFinal= 39;
                            //                              break;

                            //                         case '02': //ESTRATO 1 Y 2
                            //                                  $atributoFinal= 23;
                            //                             break;

                            //                         case '03': //ESTRATO 3 Y 4
                            //                          if($vigencia == '1997') {
                            //                                  $atributoFinal= 82;
                            //                              }else{
                            //                                  $atributoFinal= 69;
                            //                              }
                            //                                 break;

                            //                         case '04': // ESTRATO 5 Y 6
                            //                                  $atributoFinal= 70;
                            //                             break;

                            //                         case '05'://INTERES SOCIAL
                            //                                  $atributoFinal= 71;
                            //                              break;

                            //                         case '06': //LOTES URBANIZADOS NO URBANIZABLES
                            //                                  $atributoFinal= 72;
                            //                          break;

                            //                         case '07': //MENOR DE 250 MTRS
                            //                                  $atributoFinal= 47;
                            //                          break;

                            //                         case '08': //ENTRE 251 - 500 MTRS
                            //                                  $atributoFinal= 73;
                            //                         break;

                            //                         case '09': //MAYORES DE 500 MTRS
                            //                               if($vigencia == '1997') {
                            //                                 $atributoFinal= 37;
                            //                               }else{
                            //                                  $atributoFinal= 74;
                            //                               }
                            //                         break;

                            //                         case '10': //EXENTOS
                            //                                  $atributoFinal= 75;
                            //                          break;

                            //                         case '11'://URBANOS INDUSTRIALES Y COMERCIALES
                            //                                  $atributoFinal= 77;
                            //                          break;

                            //                         case '12': //PETROLERAS
                            //                                  $atributoFinal= 78;
                            //                         break;

                            //                         case '13': //EMPRESAS SERVICIOS PUBLICOS
                            //                                  $atributoFinal= 79;
                            //                         break;
                            //                        case '14': //CORREGIMIENTOS
                            //                                  $atributoFinal= 81;
                            //                             break;

                            //                         default:
                            //                         $atributoFinal=x;

                            //                         break;
                            //         }
                            //                 $sql="INSERT INTO predial_predio_campos(predial_predio, predial_vigencia, campo, valor)
                            //                     VALUES($predio, $vigenciaId, 9004, '$atributoFinal')";

                            //                     $resultado = $conexion->getDBCon()->Execute($sql);
                            //                     $predial_atributo = $conexion->getDBCon()->insert_Id();
                            //                     if ($resultado === false)
                            //                     {
                            //                          print "<pre>"; print_r($sql . " " . $conexion->getDBCon()->ErrorMsg()); print "</pre>\n";
                            //                     }
                            //                     $importado = $predial_atributo;
                            //     } // End vigencia mayor a 1992 y menor a 1998

                            // } else
                            // {
                            //         $sql="INSERT INTO predial_predio_campos(predial_predio, predial_vigencia, campo, valor)
                            //                     VALUES($predio, $vigenciaId, 9004, '1')";

                            //                     $resultado = $conexion->getDBCon()->Execute($sql);
                            //                     $predial_atributo = $conexion->getDBCon()->insert_Id();
                            //                     if ($resultado === false)
                            //                     {
                            //                          print "<pre>"; print_r($sql . " " . $conexion->getDBCon()->ErrorMsg()); print "</pre>\n";
                            //                     }
                            //                     $importado = $predial_atributo;
                            // }


//___________________________________________________________________________________________________________________________________________________________________________________________________
//TERMINA SUB ATRIBUTO




                            /*$key = array_search($vigencia, array_column($vigenciaPredial, 'descripcion'));
                            $key = ($key == null && $key != 0) ? -1 : $key;
                            $vigenciaId = ($vigenciaPredial[$key]['descripcion'] == $vigencia) ? $vigenciaPredial[$key]['predial_vigencia'] : 0;


                            //Para ver si existe el predio
                            $key = array_search($referencia_catastral, array_column($informacionPredioInicial, 'referencia_catastral'));
                            $key = ($key == null && $key != 0) ? -1 : $key;
                            $predio = ($informacionPredioInicial[$key]['referencia_catastral'] == $referencia_catastral) ? $informacionPredioInicial[$key]['predial_predio'] : 0;




                            //print "<pre>"; print_r($predio); print "</pre>\n";

                            if($predio != 0)
                            {

                                switch ($estado) {
                                    case 'PAGO':
                                        $estado = "p";
                                        break;
                                    case 'SIN PAGO':
                                        $estado = "d";
                                        break;
                                    case 'EN ACUERDO':
                                        $estado = "a";
                                        break;
                                }

                                //print "<pre>"; print_r($estado); print "</pre>\n";
                                if($estado == 'p')
                                {
                                        $key = array_search($predio.'*'.$numero_factura, array_column($informacionPredioLiquidacion, 'predio_factura'));
                                        $key = ($key == null && $key != 0) ? -1 : $key;
                                        $predial_liquidacion = ($informacionPredioLiquidacion[$key]['predio_factura'] == $predio.'*'.$numero_factura) ? $informacionPredioLiquidacion[$key]['predial_liquidacion'] : 0;
                                        //print "<pre>"; print_r($predial_liquidacion); print "</pre>\n";

                                        if($predial_liquidacion == 0){

                                            // $key = array_search($predio.'*'.$vigenciaId, array_column($predialTerceroVigencia, 'predio_vigencia'));
                                            // $key = ($key == null && $key != 0) ? -1 : $key;
                                            // $tercero_liquidacion = ($predialTerceroVigencia[$key]['predio_vigencia'] == $predio.'*'.$vigenciaId) ? $predialTerceroVigencia[$key]['tercero'] : 0;

                                            //$tercero_liquidacion = $_SESSION["tercero"];



                                            $sql="INSERT INTO predial_liquidacion(predial_predio, predial_vigencia, empresa, tercero, numero_factura, archivo, tipo_liquidacion, estado_pago)
                                                    VALUES($predio,$vigenciaId, 3, 353, '$numero_factura', '', 'm', '$estado')";


                                            $resultado = $conexion->getDBCon()->Execute($sql);
                                            if ($resultado === false) {
                                                print "<pre>"; print_r($sql . " " . $conexion->getDBCon()->ErrorMsg()); print "</pre>\n";
                                            }
                                            $predial_liquidacion = $conexion->getDBCon()->insert_Id();
                                            $informacionPredioLiquidacion[] = Array("predial_predio"=> $predio,
                                                                            "predial_vigencia"=> $vigenciaId,
                                                                            "predio_factura"=> $predio.'*'.$numero_factura,
                                                                            "predial_liquidacion"=> $predial_liquidacion
                                                                        );
                                        }
                                        //print "<pre>"; print_r($predial_liquidacion); print "</pre>\n";

                                //}

                                    $sql= "INSERT INTO predial_liquidacion_vigencia(predial_liquidacion, predial_vigencia, numero_factura, generado, tarifa, impuesto, interes, sobretasa, interes_sobretasa, descuento, valor_total, avaluo, descuento_tributario, descuento_interes_sobretasa)
                                            VALUES($predial_liquidacion, $vigenciaId, '$numero_factura', 's', '$tarifa', '$impuesto', '$interes', '$sobretasa', '$interes_sobretasa', '$descuento', '$valor_total', '$avaluo', '$descuento_tributario', '$descuento_interes_sobretasa')";
                                    $resultado = $conexion->getDBCon()->Execute($sql);

                                    if ($resultado === false) {
                                        print "<pre>"; print_r($sql . " " . $conexion->getDBCon()->ErrorMsg()); print "</pre>\n";
                                    }

                                    //if ($estado == "p") {
                                        // Conocemos el próximo consecutivo de Asobancaria
                                        $sql = "SELECT MAX(a.asobancaria) + 1 AS proxima_asobancaria
                                                FROM asobancaria a";
                                        $resultado = $conexion->getDBCon()->Execute($sql,[]);
                                        $respuesta = $resultado->FetchRow();
                                        $nuevoAsobank = ($respuesta['proxima_asobancaria'] == '') ? 1 : $respuesta['proxima_asobancaria'];

                                        //Ingresamos el Registro.
                                        $sql = "INSERT INTO asobancaria(asobancaria, empresa, nit, bank_code, account_number, account_type, reference, amount, origin, channel, operationid, authcode, thirdentity, branch, sequence, refundreason, pay_date, file_date, file_time, file,file_modifier, usuario, created_at, updated_at, year, tipo_impuesto, descripcion, ejecucion_manual)
                                                VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
                                        $resultado = $conexion->getDBCon()->Execute($sql,[
                                            'asobancaria' => $nuevoAsobank,
                                            'empresa' => $empresa,
                                            'nit' => '891855017',
                                            'bank_code' => '000',
                                            'account_number' => '000',
                                            'account_type' => '000',
                                            'reference' => $numero_factura,
                                            'amount' => Helper::formatearMoneda($valor_total),
                                            'origin' => '01',
                                            'channel' => '01',
                                            'operationid' => '000000',
                                            'authcode' => '000000',
                                            'thirdentity' => '000',
                                            'branch' => '000',
                                            'sequence' => '10',
                                            'refundreason' => '000',
                                            'pay_date' => $fecha_pago,
                                            'file_date' => '',
                                            'file_time' => '00:00',
                                            'file' => '',
                                            'file_modifier' => 'A',
                                            'usuario' => 'didom900@gmail.com',
                                            'created_at' => Helper::fechaActual(),
                                            'updated_at' => Helper::fechaActual(),
                                            'year' => $vigencia,
                                            'tipo_impuesto' => 3,
                                            'descripcion' => '',
                                            'ejecucion_manual' => 'p',
                                        ]);
                                        if ($resultado === false) {
                                            print "<pre>"; print_r($sql . " " . $conexion->getDBCon()->ErrorMsg()); print "</pre>\n";
                                        }
                                    //}

                                     $importado = $predial_liquidacion;


        // DB::insert("INSERT INTO predial_liquidacion_otro_campo(predial_liquidacion , campo, valor, calculo,fecha_valido)
        //     VALUES($nuevoLiquidacion,  9100 ,NULL, $avaluo,'$fecha_limite')");

        // // Impuesto
        // DB::insert("INSERT INTO predial_liquidacion_otro_campo(predial_liquidacion , campo, valor, calculo,fecha_valido)
        //     VALUES($nuevoLiquidacion,  9102 ,NULL, $impuesto,'$fecha_limite')");

        // // Interes
        // DB::insert("INSERT INTO predial_liquidacion_otro_campo(predial_liquidacion , campo, valor, calculo,fecha_valido)
        //     VALUES($nuevoLiquidacion,  9103 ,NULL, $interes,'$fecha_limite')");

        // // >sobretasa
        // DB::insert("INSERT INTO predial_liquidacion_otro_campo(predial_liquidacion , campo, valor, calculo,fecha_valido)
        //     VALUES($nuevoLiquidacion,  9104 ,NULL, $sobretasa,'$fecha_limite')");

        // // >interesSobretasa
        // DB::insert("INSERT INTO predial_liquidacion_otro_campo(predial_liquidacion , campo, valor, calculo,fecha_valido)
        //     VALUES($nuevoLiquidacion,  9105 ,NULL, $interesSobretasa,'$fecha_limite')");

        // // >Descuento
        // DB::insert("INSERT INTO predial_liquidacion_otro_campo(predial_liquidacion , campo, valor, calculo,fecha_valido)
        //     VALUES($nuevoLiquidacion,  9106 ,NULL, $descuento,'$fecha_limite')");

        // // >pago total
        // DB::insert("INSERT INTO predial_liquidacion_otro_campo(predial_liquidacion , campo, valor, calculo,fecha_valido)
        //     VALUES($nuevoLiquidacion,  9107 ,NULL, $totalPago,'$fecha_limite')");



                                } else if($estado = 'd')
                                {

                                    $key = array_search($predio.'*'.$vigenciaId, array_column($informacionImpuestoPreliquidacion, 'predio_preliquidacion'));
                                        $key = ($key == null && $key != 0) ? -1 : $key;
                                        $predio_preliquidacion = ($informacionImpuestoPreliquidacion[$key]['predio_preliquidacion'] == $predio.'*'.$numero_factura) ? $informacionImpuestoPreliquidacion[$key]['predio_preliquidacion'] : 0;
                                        //print "<pre>"; print_r($predial_liquidacion); print "</pre>\n";

                                        if($predio_preliquidacion == 0){

                                            $sql = "  SELECT nd.uvt
                                                      FROM nomina_datos nd
                                                      WHERE nd.empresa = 3
                                                      AND nd.anio = '$vigencia'";
                                             $resultado = $conexion->getDBCon()->Execute($sql);
                                             $respuesta = $resultado->FetchRow();
                                             //$base_uvt = $respuesta['uvt'];
                                             $base_uvt = ($respuesta['uvt'] != null || $respuesta['uvt']  != '')  ? $respuesta['uvt'] : 0;


                                             $sql1 = "  SELECT ppc.valor AS sub_atributo
                                                      FROM predial_predio pp
                                                      INNER JOIN predial_predio_campos ppc ON ppc.predial_predio = ppc.predial_predio AND ppc.campo = 9004 AND ppc.predial_vigencia = '$vigenciaId'
                                                      WHERE pp.predial_predio = $predio";
                                             $resultado1 = $conexion->getDBCon()->Execute($sql1);
                                             $respuesta1 = $resultado1->FetchRow();
                                             //$sub_atributo_bien = $respuesta['sub_atributo'];
                                             $sub_atributo_bien = ($respuesta1['sub_atributo'] != null || $respuesta1['sub_atributo']  != '')  ? $respuesta1['sub_atributo'] : 1;

                                            $key = array_search($predio.'*'.$vigenciaId, array_column($predialTerceroVigencia, 'predio_vigencia'));
                                            $key = ($key == null && $key != 0) ? -1 : $key;
                                            $tercero_liquidacion = ($predialTerceroVigencia[$key]['predio_vigencia'] == $predio.'*'.$vigenciaId) ? $predialTerceroVigencia[$key]['tercero'] : 0;

                                            $tercero_liquidacion = $_SESSION["tercero"];



                                            $sql="INSERT INTO impuesto_preliquidaciones(
                                                                                        predial_predio,
                                                                                        referencia_catastral,
                                                                                        vigencia,
                                                                                        tipo_predio,
                                                                                        sub_atributo,
                                                                                        avaluo,
                                                                                        liquidaciones_anteriores,
                                                                                        tarifa_anterior,
                                                                                        porcentaje_aumento,
                                                                                        compensacion,
                                                                                        saldo_favor,
                                                                                        base_uvt,
                                                                                        tarifa,
                                                                                        impuesto,
                                                                                        sobretasa,
                                                                                        empresa,
                                                                                        vigencia_id,
                                                                                        tipo_descuento,
                                                                                        interes,
                                                                                        interes_sobretasa,
                                                                                        tipo_interes,
                                                                                        valor_total,
                                                                                        deleted_at,
                                                                                        descuento,
                                                                                        descuento_tributario,
                                                                                        descuento_interes_sobretasa)


                                                    VALUES ($predio,
                                                            '$referencia_catastral',
                                                            '$vigencia',
                                                            '$tipo_predio',
                                                            '1',
                                                            $avaluo,
                                                            0,
                                                            0,
                                                            '0',
                                                            '0',
                                                            0,
                                                            111,
                                                            '$tarifa',
                                                            $impuesto,
                                                            $sobretasa,
                                                            3,
                                                            $vigenciaId,
                                                            'null',
                                                            $interes,
                                                            $interes_sobretasa,
                                                            'null',
                                                            $valor_total,
                                                            'null',
                                                            $descuento,
                                                            $descuento_tributario,
                                                            $descuento_interes_sobretasa)";


                                            $resultado = $conexion->getDBCon()->Execute($sql);
                                            if ($resultado === false) {
                                                print "<pre>"; print_r($sql . " " . $conexion->getDBCon()->ErrorMsg()); print "</pre>\n";
                                            }
                                            $predio_preliquidacion = $conexion->getDBCon()->insert_Id();
                                            $informacionImpuestoPreliquidacion[] = Array("predial_predio"=> $predio,
                                                                            "predio_preliquidacion"=> $predio.'*'.$vigenciaId
                                                                        );
                                        }


                                    $importado = $predio_preliquidacion;

                                } else
                                {

                                }
                            } else {
                                 $importado = -200;
                            }

//___________________________________________________________________________________________________________________________________________________________________________________________________
//COMIENZA PREDIAL PREDIO CON TERCERO



                            // $numero_documento = ($numero_documento == "") ? $dato["a"] : $numero_documento;


                            // //Para ver si existe el predio
                            // $key = array_search($referencia_catastral, array_column($informacionPredioInicial, 'referencia_catastral'));
                            // $key = ($key == null && $key != 0) ? -1 : $key;
                            // $predio = ($informacionPredioInicial[$key]['referencia_catastral'] == $referencia_catastral) ? $informacionPredioInicial[$key]['predial_predio'] : 0;



                            // //Existe el predio
                            // if($predio != 0) {

                            //     $key = array_search($predio.'*'.$vigenciaId, array_column($informacionPredioGeneral, 'predio_vigencia'));
                            //     $key = ($key == null && $key != 0) ? -1 : $key;
                            //     $predio_vigencia = ($informacionPredioGeneral[$key]['predio_vigencia'] == $predio.'*'.$vigenciaId) ? $informacionPredioGeneral[$key]['predial_predio'] : 0;

                            //     // if($predio_vigencia != 0) {  //Existe el predio con la vigencia

                            //     //     $sql="DELETE FROM predial_predio_campos ppc
                            //     //                WHERE ppc.predial_predio=$predio AND ppc.predial_vigencia=$vigenciaId AND (ppc.campo=9002 OR ppc.campo=9006 OR ppc.campo=9007) ;";
                            //     //          $resultado = $conexion->getDBCon()->Execute($sql);

                            //     //     $sql="DELETE FROM predial_avaluo_valor ppv
                            //     //               WHERE  ppv.predial_predio=$predio AND ppv.predial_vigencia=$vigenciaId;";
                            //     //          $resultado = $conexion->getDBCon()->Execute($sql);

                            //     //     $sql="INSERT INTO predial_predio_campos (predial_predio, predial_vigencia, campo,valor)
                            //     //                     VALUES($predio,$vigenciaId, 9002,'$direccion')";
                            //     //          $resultado = $conexion->getDBCon()->Execute($sql);

                            //     //        //Insertamos area_terreno
                            //     //         $sql="INSERT INTO predial_predio_campos (predial_predio, predial_vigencia, campo,valor)
                            //     //                 VALUES($predio,$vigenciaId, 9006,'$area_terreno')";
                            //     //         $resultado = $conexion->getDBCon()->Execute($sql);
                            //     //          //Insertamos area_construida
                            //     //         $sql="INSERT INTO predial_predio_campos (predial_predio, predial_vigencia, campo,valor)
                            //     //                 VALUES($predio,$vigenciaId, 9007,'$area_construida')";
                            //     //         $resultado = $conexion->getDBCon()->Execute($sql);
                            //     //          //Insertamos avaluo predial_avaluo_valor
                            //     //         $sql="INSERT INTO predial_avaluo_valor (predial_predio, predial_vigencia, valor_avaluo)
                            //     //                 VALUES($predio,$vigenciaId,$avaluo)";
                            //     //         $resultado = $conexion->getDBCon()->Execute($sql);

                            //     // }
                            //     if ($predio_vigencia == 0) { //Existe predio pero no vigencia

                            //             $informacionPredioGeneral[] = Array("predial_predio"=> $predio,
                            //                                                 "referencia_catastral"=> $referencia_catastral,
                            //                                                 "predial_vigencia"=> $vigenciaId,
                            //                                                 "predio_vigencia"=> $predio.'*'.$vigenciaId
                            //                                             );
                            //             // //Para verificar campos predial_campos anterior
                            //             // $key = array_search($predio, array_column($PredialCampos, 'referencia_catastral'));
                            //             // $key = ($key == null && $key != 0) ? -1 : $key;
                            //             // //$tipo_predio = ($PredialCampos[$key]['referencia_catastral'] == $referencia_catastral) ? $PredialCampos[$key]['tipo_predio_id'] : 0;
                            //             // $tipo_atributo = ($PredialCampos[$key]['referencia_catastral'] == $referencia_catastral) ? $PredialCampos[$key]['tipo_atributo'] : 0;
                            //             // $estrato = ($PredialCampos[$key]['referencia_catastral'] == $referencia_catastral) ? $PredialCampos[$key]['estrato'] : 0;
                            //             // //$area_exenta = ($PredialCampos[$key]['referencia_catastral'] == $referencia_catastral) ? $PredialCampos[$key]['area_exenta'] : 0;
                            //             // //$matricula_inmobiliaria = ($PredialCampos[$key]['referencia_catastral'] == $referencia_catastral) ? $PredialCampos[$key]['matricula'] : 0;

                            //             //Insertamos Referencia Catastral
                            //             //$matricula_inmobiliaria = $centralConsulta->getMatriculaInmobiliaria($predio);
                            //             $sql="INSERT INTO predial_predio_campos (predial_predio, predial_vigencia, campo,valor)
                            //                     VALUES($predio,$vigenciaId, 9000,'$referencia_catastral')";
                            //             $resultado = $conexion->getDBCon()->Execute($sql);

                            //             //Insertamos matricula inmobiliaria
                            //             //$matricula_inmobiliaria = $centralConsulta->getMatriculaInmobiliaria($predio);
                            //             $sql="INSERT INTO predial_predio_campos (predial_predio, predial_vigencia, campo,valor)
                            //                     VALUES($predio,$vigenciaId, 9001,'$matricula_inmobiliaria')";
                            //             $resultado = $conexion->getDBCon()->Execute($sql);

                            //             //Insertamos Direccion
                            //             $sql="INSERT INTO predial_predio_campos (predial_predio, predial_vigencia, campo,valor)
                            //                     VALUES($predio,$vigenciaId, 9002,'$direccion')";
                            //             $resultado = $conexion->getDBCon()->Execute($sql);

                            //             //Insertamos Tipo Predio
                            //             //$tipo_predio =  $centralConsulta->getTipoPredio($predio);
                            //             $sql="INSERT INTO predial_predio_campos (predial_predio, predial_vigencia, campo,valor)
                            //                     VALUES($predio,$vigenciaId, 9003,'$tipo_predio')";
                            //             $resultado = $conexion->getDBCon()->Execute($sql);

                            //             // //Insertamos Tipo Atributo
                            //             // //$tipo_atributo =  $centralConsulta->getTipoAtributo($predio);
                            //             // $sql="INSERT INTO predial_predio_campos (predial_predio, predial_vigencia, campo,valor)
                            //             //         VALUES($predio,$vigenciaId, 9004,'$tipo_atributo')";
                            //             // $resultado = $conexion->getDBCon()->Execute($sql);

                            //             //Insertamos Estrato
                            //             //$estrato =  $centralConsulta->getEstrato($predio);
                            //             $sql="INSERT INTO predial_predio_campos (predial_predio, predial_vigencia, campo,valor)
                            //                     VALUES($predio,$vigenciaId, 9005,'$estrato')";
                            //             $resultado = $conexion->getDBCon()->Execute($sql);

                            //              //Insertamos area_terreno
                            //             $sql="INSERT INTO predial_predio_campos (predial_predio, predial_vigencia, campo,valor)
                            //                     VALUES($predio,$vigenciaId, 9006,'$area_terreno')";
                            //             $resultado = $conexion->getDBCon()->Execute($sql);
                            //              //Insertamos area_construida
                            //             $sql="INSERT INTO predial_predio_campos (predial_predio, predial_vigencia, campo,valor)
                            //                     VALUES($predio,$vigenciaId, 9007,'$area_construida')";
                            //             $resultado = $conexion->getDBCon()->Execute($sql);

                            //              //Insertamos avaluo predial_avaluo_valor
                            //             $sql="INSERT INTO predial_avaluo_valor (predial_predio, predial_vigencia, valor_avaluo)
                            //                     VALUES($predio,$vigenciaId,$avaluo)";
                            //             $resultado = $conexion->getDBCon()->Execute($sql);

                            //             //Insertamos Area extensa
                            //             //$area_exenta =  $centralConsulta->getAreaExenta($predio);
                            //             $sql="INSERT INTO predial_predio_campos (predial_predio, predial_vigencia, campo,valor)
                            //                     VALUES($predio,$vigenciaId, 9008,'$area_exenta')";
                            //             $resultado = $conexion->getDBCon()->Execute($sql);

                            //             //Insertamos Area excluida 0
                            //             $sql="INSERT INTO predial_predio_campos (predial_predio, predial_vigencia, campo,valor)
                            //                     VALUES($predio,$vigenciaId, 9144,'0')";
                            //             $resultado = $conexion->getDBCon()->Execute($sql);

                            //             //Insertamos Divipola por defecto 85001000
                            //             //$area_exenta =  $centralConsulta->getAreaExenta($predio);
                            //             $sql="INSERT INTO predial_predio_campos (predial_predio, predial_vigencia, campo,valor)
                            //                     VALUES($predio,$vigenciaId, 9153,'85001000')";
                            //             $resultado = $conexion->getDBCon()->Execute($sql);

                            //             $importado = $predio;

                            //     }

                            // }
                            // //  //Nuevo Predio a crear
                            // if($predio == 0) {

                            //             //Insertamos en la tabla predial_predio
                            //             $sql = "INSERT INTO predial_predio(referencia_catastral,empresa,usuario) VALUES('$referencia_catastral',$empresa,'didom900@gmail.com')";
                            //             $resultado = $conexion->getDBCon()->Execute($sql);
                            //             $predio = $conexion->getDBCon()->insert_Id();
                            //             //Insertamos en predial_predio_campos

                            //             $informacionPredioInicial[] = Array("predial_predio"=> $predio,
                            //                                                 "referencia_catastral"=> $referencia_catastral);

                            //             $informacionPredioGeneral[] = Array("predial_predio"=> $predio,
                            //                                                 "referencia_catastral"=> $referencia_catastral,
                            //                                                 "predial_vigencia"=> $vigenciaId,
                            //                                                 "predio_vigencia"=> $predio.'*'.$vigenciaId);
                            //             $importado = $predio;

                            //             //Insertamos Referencia Catastral
                            //             //$matricula_inmobiliaria = $centralConsulta->getMatriculaInmobiliaria($predio);
                            //             $sql="INSERT INTO predial_predio_campos (predial_predio, predial_vigencia, campo,valor)
                            //                     VALUES($predio,$vigenciaId, 9000,'$referencia_catastral')";
                            //             $resultado = $conexion->getDBCon()->Execute($sql);

                            //             //Insertamos matricula inmobiliaria
                            //             //$matricula_inmobiliaria = $centralConsulta->getMatriculaInmobiliaria($predio);
                            //             $sql="INSERT INTO predial_predio_campos (predial_predio, predial_vigencia, campo,valor)
                            //                     VALUES($predio,$vigenciaId, 9001,'$matricula_inmobiliaria')";
                            //             $resultado = $conexion->getDBCon()->Execute($sql);

                            //             //Insertamos Direccion
                            //             $sql="INSERT INTO predial_predio_campos (predial_predio, predial_vigencia, campo,valor)
                            //                     VALUES($predio,$vigenciaId, 9002,'$direccion')";
                            //             $resultado = $conexion->getDBCon()->Execute($sql);

                            //             //Insertamos Tipo Predio
                            //             //$tipo_predio =  $centralConsulta->getTipoPredio($predio);
                            //             $sql="INSERT INTO predial_predio_campos (predial_predio, predial_vigencia, campo,valor)
                            //                     VALUES($predio,$vigenciaId, 9003,'$tipo_predio')";
                            //             $resultado = $conexion->getDBCon()->Execute($sql);

                            //             // //Insertamos Tipo Atributo
                            //             // //$tipo_atributo =  $centralConsulta->getTipoAtributo($predio);
                            //             // $sql="INSERT INTO predial_predio_campos (predial_predio, predial_vigencia, campo,valor)
                            //             //         VALUES($predio,$vigenciaId, 9004,'$tipo_atributo')";
                            //             // $resultado = $conexion->getDBCon()->Execute($sql);

                            //             //Insertamos Estrato
                            //             //$estrato =  $centralConsulta->getEstrato($predio);
                            //             $sql="INSERT INTO predial_predio_campos (predial_predio, predial_vigencia, campo,valor)
                            //                     VALUES($predio,$vigenciaId, 9005,'$estrato')";
                            //             $resultado = $conexion->getDBCon()->Execute($sql);

                            //              //Insertamos area_terreno
                            //             $sql="INSERT INTO predial_predio_campos (predial_predio, predial_vigencia, campo,valor)
                            //                     VALUES($predio,$vigenciaId, 9006,'$area_terreno')";
                            //             $resultado = $conexion->getDBCon()->Execute($sql);
                            //              //Insertamos area_construida
                            //             $sql="INSERT INTO predial_predio_campos (predial_predio, predial_vigencia, campo,valor)
                            //                     VALUES($predio,$vigenciaId, 9007,'$area_construida')";
                            //             $resultado = $conexion->getDBCon()->Execute($sql);

                            //              //Insertamos avaluo predial_avaluo_valor
                            //             $sql="INSERT INTO predial_avaluo_valor (predial_predio, predial_vigencia, valor_avaluo)
                            //                     VALUES($predio,$vigenciaId,$avaluo)";
                            //             $resultado = $conexion->getDBCon()->Execute($sql);

                            //             //Insertamos Area extensa
                            //             //$area_exenta =  $centralConsulta->getAreaExenta($predio);
                            //             $sql="INSERT INTO predial_predio_campos (predial_predio, predial_vigencia, campo,valor)
                            //                     VALUES($predio,$vigenciaId, 9008,'$area_exenta')";
                            //             $resultado = $conexion->getDBCon()->Execute($sql);

                            //             //Insertamos Area excluida 0
                            //             $sql="INSERT INTO predial_predio_campos (predial_predio, predial_vigencia, campo,valor)
                            //                     VALUES($predio,$vigenciaId, 9144,'0')";
                            //             $resultado = $conexion->getDBCon()->Execute($sql);

                            //             //Insertamos Divipola por defecto 85001000
                            //             //$area_exenta =  $centralConsulta->getAreaExenta($predio);
                            //             $sql="INSERT INTO predial_predio_campos (predial_predio, predial_vigencia, campo,valor)
                            //                     VALUES($predio,$vigenciaId, 9153,'85001000')";
                            //             $resultado = $conexion->getDBCon()->Execute($sql);
                            // }

                            /************ Insertamos en la tabla tercero ***************************/

                        //Insertar tabla terceros
                        $empresa = 3;
                        $numero_documento = ($numero_documento == "") ? $dato["a"] : $numero_documento;
                        $usuario = 'didom900@gmail';

                        //para guardar nombres, apellidos y razon social

                        $nombrePropietario = explode(" ", $nombre_porpietario);
                            switch (count($nombrePropietario)) {
                                case 1:
                                $apellidos = "";
                                $nombres = "$nombrePropietario[0]";
                                break;
                                case 2:
                                $apellidos = "$nombrePropietario[0]";
                                $nombres = "$nombrePropietario[1]";
                                break;
                                case 3:
                                $apellidos = "$nombrePropietario[0] $nombrePropietario[1]";
                                $nombres = "$nombrePropietario[2]";
                                break;
                                default:
                                $apellidos = "$nombrePropietario[0] $nombrePropietario[1]";
                                $nombres = "$nombrePropietario[2] $nombrePropietario[3] $nombrePropietario[4] $nombrePropietario[5] $nombrePropietario[6] $nombrePropietario[7] $nombrePropietario[8] $nombrePropietario[9]";
                                break;
                            }
                        $apellidos = trim($apellidos);
                        $nombres = trim($nombres);
                        $nombre_apellido = "$nombres$apellidos";


                        var_dump("numero documentos para zero " . $numero_documento_zero);

                            //if ($numero_documento_zero == '000000000000') { //Preguntamos Nit caso especial 00000000000
                            if ($numero_documento_zero == '' || empty($numero_documento_zero)) { //Preguntamos Nit caso especial 00000000000

                                var_dump("numero documento zero++sa " . $numero_documento . ' ' .gettype($numero_documento_zero));
                                //die();

                                //buscamos el nit y la razon social
                                $key = array_search($numero_documento.'*'.$nombre_porpietario, array_column($traerTerceros, 'nit_razon'));
                                $key = ($key == null && $key != 0) ? -1 : $key;
                                $tercero = ($traerTerceros[$key]['nit_razon'] == $numero_documento.'*'.$nombre_porpietario) ? $traerTerceros[$key]['tercero'] : 0;

                                    if($tercero != 0) {
                                        //Miramos si existe el tercero en la tabla predial_tercero_predio con predio, vigencia y tercero
                                        $key = array_search($tercero.'*'.$predio, array_column($predialTercero, 'tercero_predio'));
                                        $key = ($key == null && $key != 0) ? -1 : $key;
                                        $existeTerceroPredial = ($predialTercero[$key]['tercero_predio'] == $tercero.'*'.$predio) ? $predialTercero[$key]['tercero'] : 0;
                                        //$existeTerceroPredialTercero =  $centralConsulta->existeTerceroPredialTercero($tercero,$vigenciaId,$predio);
                                            if ($existeTerceroPredial == 0) {
                                                /*$sql="INSERT INTO predial_tercero_predio (predial_predio,predial_vigencia,tercero,predial_tipo_vinculacion)
                                                    VALUES ($predio,$vigenciaId,$tercero,1)";
                                                $resultado = $conexion->getDBCon()->Execute($sql);*/

                                                $importado = $tercero;

                                                $sql = "UPDATE predial_tercero_predio
                                                SET tercero = $tercero
                                                WHERE predial_predio = $predio
                                                AND predial_vigencia=$vigenciaId";
                                                var_dump($sql);
                                                //die();
                                                $resultado = $conexion->getDBCon()->Execute($sql);

                                                //Alimentamos array de tercero predios
                                                $predialTercero[] = Array("tercero"=> $tercero,
                                                     "tercero_predio"=> $tercero.'*'.$numero_documento
                                                );
                                            }
                                    }   else{

                                                //Insertamos en terceros
                                                $sql="INSERT INTO tercero (nit, municipio, ciudad_pago,digito_verificacion,identificacion,nombre,apellido,razon_social,fecha_sistema,usuario,bandera_predial)
                                                    VALUES('$numero_documento',0,0,0,'$numero_documento','$nombres','$apellidos','','$fecha_sistema','$usuario',2)";
                                                $resultado = $conexion->getDBCon()->Execute($sql);
                                                $importado = $conexion->getDBCon()->insert_Id();

                                                //Alimentamos array de terceros
                                                $traerTerceros[] = Array(   "tercero"       => $importado,
                                                                             "nit"          => $numero_documento,
                                                                             "nit_razon"    => $numero_documento.'*'.$nombre_porpietario,
                                                                             "razon_social" => $nombre_porpietario);

                                                //Insertamos en rl_tercero_tipo_persona_nueva
                                                $sql="INSERT INTO rl_tercero_tipo_persona(tercero, tipo_persona, empresa)
                                                        VALUES($importado,  2, $empresa)";//persona natural
                                                $resultado = $conexion->getDBCon()->Execute($sql);

                                                //direccion manual
                                                $sql="INSERT INTO rl_tercero_campos(tercero, campo, valor, empresa)
                                                    VALUES($importado, 12, '', $empresa)";
                                                $resultado = $conexion->getDBCon()->Execute($sql);

                                                //Miramos si existe el tercero en la tabla predial_tercero_predio con predio, vigencia y tercero
                                                $key = array_search($importado.'*'.$predio, array_column($predialTercero, 'tercero_predio'));
                                                $key = ($key == null && $key != 0) ? -1 : $key;
                                                $existeTerceroPredial = ($predialTercero[$key]['tercero_predio'] == $importado.'*'.$predio) ? $predialTercero[$key]['tercero'] : 0;

                                                //Miramos si existe el tercero en la tabla predial_tercero_predio
                                                if ($existeTerceroPredial == 0) {
                                                //Insertamos en la tabla predial_tercero_predio
                                                    /*$sql="INSERT INTO predial_tercero_predio(predial_predio, predial_vigencia, tercero,predial_tipo_vinculacion)
                                                        VALUES($predio,  $vigenciaId, $importado,1)";
                                                    $resultado = $conexion->getDBCon()->Execute($sql);*/

                                                 $sql = "UPDATE predial_tercero_predio
                                                        SET tercero = $importado
                                                        WHERE predial_predio = $predio
                                                        AND predial_vigencia=$vigenciaId";
                                                        $resultado = $conexion->getDBCon()->Execute($sql);

                                                    //Alimentamos array de tercero predios
                                                        $predialTercero[] = Array("tercero"=> $importado,
                                                                        "tercero_predio"=> $importado.'*'.$numero_documento
                                                        );
                                           }
                                        }
                            } else { //Insertamos normal en la tabla tercero (No caso especial)

                                    //buscamos el nit
                                    $key = array_search($numero_documento, array_column($traerTerceros, 'nit'));
                                    $key = ($key == null && $key != 0) ? -1 : $key;
                                    $existeTercero = ($traerTerceros[$key]['nit'] == $numero_documento) ? $traerTerceros[$key]['tercero'] : 0;

                                    $existeTercero =  $centralConsulta->existeTercero($numero_documento);

                                    var_dump("existe " . $existeTercero . ' ' .$vigenciaId);

                                    if ($existeTercero == 0) {

                                        //Insertamos en la tabla tercero
                                        $sql="INSERT INTO tercero (nit, municipio, ciudad_pago,digito_verificacion,identificacion,nombre,apellido,razon_social,fecha_sistema,usuario,bandera_predial)
                                        VALUES($numero_documento,0,0,0,$numero_documento,'$nombres','$apellidos','','$fecha_sistema','$usuario',1)";
                                        $resultado = $conexion->getDBCon()->Execute($sql);
                                        $importado = $conexion->getDBCon()->insert_Id();

                                        var_dump("segundo " . $importado);


                                        //Alimentamos array de terceros
                                        $traerTerceros[] = Array(   "tercero"       => $importado,
                                                                    "nit"           => $numero_documento,
                                                                    "nit_razon"     => $numero_documento.'*'.$nombre_porpietario,
                                                                    "razon_social"  => $nombre_porpietario
                                                                );

                                        //Insertamos en rl_tercero_tipo_persona_nueva
                                        $sql="INSERT INTO rl_tercero_tipo_persona(tercero, tipo_persona, empresa)
                                                VALUES($importado,  2, $empresa)";
                                        $resultado = $conexion->getDBCon()->Execute($sql);

                                        //Direccion manual
                                        $sql="INSERT INTO rl_tercero_campos(tercero, campo, valor, empresa)
                                            VALUES($importado, 12, '', $empresa)";
                                        $resultado = $conexion->getDBCon()->Execute($sql);

                                        //Insertamos en la tabla predial_tercero_predio
                                        /*$sql="INSERT INTO predial_tercero_predio(predial_predio, predial_vigencia, tercero,predial_tipo_vinculacion)
                                            VALUES($predio,  $vigenciaId, $importado,1)";
                                        $resultado = $conexion->getDBCon()->Execute($sql);*/

                                        $sql = "UPDATE predial_tercero_predio
                                        SET tercero = $importado
                                        WHERE predial_predio = $predio
                                        AND predial_vigencia=$vigenciaId";

                                        var_dump($sql);

                                        $resultado = $conexion->getDBCon()->Execute($sql);


                                        //Alimentamos array de tercero predios
                                        $predialTercero[] = Array(  "tercero"       => $tercero,
                                                                    "tercero_predio"=> $tercero.'*'.$numero_documento
                                        );


                                    } else {
                                         //Miramos si existe el tercero en la tabla predial_tercero_predio con predio, vigencia y tercero
                                            $key = array_search($existeTercero.'*'.$predio, array_column($predialTercero, 'tercero_predio'));
                                            $key = ($key == null && $key != 0) ? -1 : $key;
                                            $existeTerceroPredial = ($predialTercero[$key]['tercero_predio'] == $existeTercero.'*'.$predio) ? $predialTercero[$key]['tercero'] : 0;

                                            var_dump("primero " . $existeTerceroPredial);
                                            $importado = $existeTercero;


                                            if ($existeTerceroPredial == 0) {
                                                 //Insertamos en la tabla predial_tercero_predio
                                                /*$sql="INSERT INTO predial_tercero_predio(predial_predio, predial_vigencia, tercero,predial_tipo_vinculacion)
                                                        VALUES($predio,  $vigenciaId, $existeTercero,1)";
                                                $resultado = $conexion->getDBCon()->Execute($sql);*/
                                             $sql = "UPDATE predial_tercero_predio
                                                    SET tercero = $existeTercero
                                                    WHERE predial_predio = $predio
                                                    AND predial_vigencia=$vigenciaId";

                                                    var_dump($sql);

                                                    $resultado = $conexion->getDBCon()->Execute($sql);

                                                //Alimentamos array de tercero predios
                                                $predialTercero[] = Array(  "tercero"       => $existeTercero,
                                                                            "tercero_predio"=> $existeTercero.'*'.$numero_documento
                                                );
                                            }

                                    }
                            }


//___________________________________________________________________________________________________________________________________________________________________________________________________
//TERMINA PREDIAL PREDIO CON TERCERO




//___________________________________________________________________________________________________________________________________________________________________________________________________
//COMIENZA TERCEROS

                            //$dato["az"] id tercero
                            //PROCEDIMIENTO TERCEROS

                            // $pruebas = 0;
                            // $auxiliar = explode("-", $dato["c"]);
                            // $dato["c"] = ltrim(ltrim(preg_replace("/[^0-9]/", "", $auxiliar[0])), "0");//nit
                            // $banderaVacio = ($dato["c"] == "") ? 's' : 'n';
                            // $dato["c"] = ($dato["c"] == "") ? $dato["a"] : $dato["c"];//TENER EN CUENTA NACION Y OTROS QUE SE REPLICAN 0000000 VARIAS VECES CON <> REFERENCIA
                            // $key = array_search($dato["c"], array_column($terceroNitEmpresaSistema, 'nit'));
                            // $key = ($key == null && $key != 0) ? -1 : $key;
                            // if ($terceroNitEmpresaSistema[$key]['nit'] == $dato["c"] && $terceroNitEmpresaSistema[$key]['empresa'] == $_SESSION["empresa"]) {

                            //     $importado = $terceroNitEmpresaSistema[$key]['tercero'];//existe para la empresa

                            //     $pruebas = "1 {$terceroNitEmpresaSistema[$key][nit]} == $dato[c] && {$terceroNitEmpresaSistema[$key][empresa]} == $_SESSION[empresa] * {$terceroNitEmpresaSistema[$key][tercero]}";
                            // } else {
                            //     $fila++;
                            //     if ($terceroNitEmpresaSistema[$key]['nit'] != $dato["c"]) { //nuevo tercero
                            //         $pruebas = 2;
                            //         $dato["d"] = trim(str_replace("*", " ", $dato["d"]));
                            //         $dato["d"] = trim(str_replace("-", " ", $dato["d"]));
                            //         $dato["d"] = trim(str_replace(".", " ", $dato["d"]));
                            //         $dato["d"] = trim(str_replace("  ", " ", $dato["d"]));
                            //         $dato["d"] = trim(str_replace("  ", " ", $dato["d"]));
                            //         $dato["d"] = trim(str_replace("  ", " ", $dato["d"]));
                            //         $dato["d"] = trim(str_replace("  ", " ", $dato["d"]));
                            //         $nombrePropietario = explode(" ", $dato["d"]);
                            //         switch (count($nombrePropietario)) {
                            //             case 1:
                            //                 $apellidos = "";
                            //                 $nombres = "$nombrePropietario[0]";
                            //                 break;
                            //             case 2:
                            //                 $apellidos = "$nombrePropietario[0]";
                            //                 $nombres = "$nombrePropietario[1]";
                            //                 break;
                            //             case 3:
                            //                 $apellidos = "$nombrePropietario[0] $nombrePropietario[1]";
                            //                 $nombres = "$nombrePropietario[2]";
                            //                 break;
                            //             default:
                            //                 $apellidos = "$nombrePropietario[0] $nombrePropietario[1]";
                            //                 $nombres = "$nombrePropietario[2] $nombrePropietario[3] $nombrePropietario[4] $nombrePropietario[5] $nombrePropietario[6] $nombrePropietario[7] $nombrePropietario[8] $nombrePropietario[9]";
                            //                 break;
                            //         }
                            //         $apellidos = trim($apellidos);
                            //         $nombres = trim($nombres);
                            //         $dv = dv($dato["c"]);
                            //         $sql = "INSERT INTO tercero
                            //                 (nit, municipio, ciudad_pago, identificacion, digito_verificacion, nombre, apellido, razon_social, usuario, bandera_predial_vacio)
                            //                 VALUES( '$dato[c]',
                            //                         '0',
                            //                         '0',
                            //                         '$dato[c]',
                            //                         '$dv',
                            //                         '$nombres',
                            //                         '$apellidos',
                            //                         '',
                            //                         '$_SESSION[usuario]',
                            //                         '$banderaVacio')";
                            //         $resultado = $conexion->getDBCon()->Execute($sql);
                            //         $importado = $conexion->getDBCon()->insert_Id();
                            //         $terceroNitEmpresaSistema[] = Array("tercero"=> $importado,
                            //                                             "nit"=> $dato["c"],
                            //                                             "empresa"=> $_SESSION["empresa"]);
                            //         //print "<pre>"; print_r($dato["c"]); print "</pre>\n";
                            //         //print "<pre>"; print_r($conexion->getDBCon()->insert_Id()); print "</pre>\n";
                            //         if ($resultado === false) {
                            //             print "<pre>"; print_r($sql); print "</pre>\n";
                            //             print "<pre>"; print_r($sql . " " . $conexion->getDBCon()->ErrorMsg()); print "</pre>\n";
                            //         }
                            //         $sql = "INSERT INTO rl_tercero_tipo_persona
                            //                 (empresa, tercero , tipo_persona)
                            //                 VALUES($_SESSION[empresa], '$importado',
                            //                        '1')";
                            //         $resultado = $conexion->getDBCon()->Execute($sql);
                            //     } else {
                            //         $pruebas = 3;
                            //         $importado = $terceroNitEmpresaSistema[$key]['tercero'];//existe tercero pero no para la empresa
                            //     }
                            //     $sql = "INSERT INTO rl_tercero_campos
                            //             (empresa, tercero , campo, valor)
                            //             VALUES($_SESSION[empresa], '$importado',
                            //                    '12',
                            //                    '$dato[f]')";//direccion manual
                            //     $resultado = $conexion->getDBCon()->Execute($sql);
                            //     if ($dato["b"] == 1) {
                            //         $sql = "INSERT INTO rl_tercero_campos
                            //                 (empresa, tercero , campo, valor)
                            //                 VALUES($_SESSION[empresa], '$importado',
                            //                        '435',
                            //                        '1')";//tipo_documento
                            //         $resultado = $conexion->getDBCon()->Execute($sql);
                            //     }
                            // }

//___________________________________________________________________________________________________________________________________________________________________________________________________
//TERMINA TERCEROS





                        } else {
                            $importado = -100;
                        }



                        break;


                }
                $sql = "UPDATE importacion_dato
                        SET importado = $importado
                        WHERE importacion_dato = $dato[importacion_dato] ---$pruebas";
                $resultado = $conexion->getDBCon()->Execute($sql);
            }
            $datosImportacion[0]['inserciones'] = $fila;
            switch ($datosImportacion[0]['tipo']) {
                case '3':
                    //print "<pre>"; print_r($_REQUEST["tablaSoporteContable"]); print "</pre>\n";
                    include './app/componente/facturacion/modulos/insercionActualizacion.php';
                    include './app/componente/facturacion/modulos/soporteContable.php';
                    break;

            }
        }
        ///print "<pre>"; print_r($soporteSistemaId); print "</pre>\n";
        if (!isset($_REQUEST["externo"])) {
            print "<pre>"; print_r($datosImportacion); print "</pre>\n";
            print "<pre>"; print_r("INSERCIONES $fila"); print "</pre>\n";
        }
    } else {
        print "<pre>"; print_r("FALTAN DATOS PARA EL PROCESO DE IMPORTACIÓN"); print "</pre>\n";
    }
?>