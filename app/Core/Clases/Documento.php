<?php

/**
 * Documento de la aplicación
 *
 * Documentos que genera la aplicación.
 *
 * @copyright  2017 - Diego Soba.
 * @author     Diego Soba <didom900@gmail.com>
 * @version    1.0
 */

//namespace contSoft\Finanzas\Traits\Nomina\Orm;


///include str_replace('///', '/finanzas/',  $rutaAplicacion->rutaRelativa) . 'clases/libreriasExternas/PHPWord-master/src/PhpWord/Autoloader.php';  // Librería PHPWord.
//include str_replace('///', '/',  $rutaAplicacion->rutaRelativa) . 'clases/libreriasExternas/PHPWord-master/src/PhpWord/Autoloader.php';  // Librería PHPWord.

include str_replace('/finanzas/finanzas/', '/finanzas/', str_replace('//', '/finanzas/', str_replace('///', '/', $rutaAplicacion->rutaRelativa))) . 'app/public/lib/PHPWord-master/src/PhpWord/Autoloader.php';


//echo str_replace('///', '/',  $rutaAplicacion->rutaRelativa) . 'clases/libreriasExternas/PHPWord-master/src/PhpWord/Autoloader.php';

\PhpOffice\PhpWord\Autoloader::register();
use PhpOffice\PhpWord\TemplateProcessor;
use contSoft\Finanzas\Clases\Modulos\Ica\AcuerdoPagos;
use contSoft\Finanzas\Clases\Helpers\Helper;

class Documento
{
    private $centralConsulta;
    private $centralConsultaIca;
    private $rutaAplicacion;
    private $formatoAplicacion;
    private $templateWord;
    private $idDocumento;
    private $nombrePlantilla;
    private $nombreDocumento;
    private $informacion;
    private $conexion;

    public function __construct($centralConsulta, $centralConsultaIca, $rutaAplicacion, $formatoAplicacion, $conexion)
    {
        $this->centralConsulta = $centralConsulta;
        $this->centralConsultaIca = $centralConsultaIca;
        $this->rutaAplicacion = $rutaAplicacion;
        $this->formatoAplicacion = $formatoAplicacion;
        $this->conexion = $conexion;
    }

    public function setIdDocumento($idDocumento)
    {
        $this->idDocumento = $idDocumento;
        //$documento1 = $this->centralConsulta->documento1($this->idDocumento);
        //$this->nombrePlantilla = $documento1[0]['plantilla'];
        //$this->nombreDocumento = sha1(date('Y-m-d H:i:s'));

        $documento1 = "1";
/*
        if (isset($_REQUEST['reciboPagoReteica'])) {
            $this->nombrePlantilla = "recibo_pago_reteica.docx";
        }
*/
        if (isset($_REQUEST['liquidacion'])) {
            $this->nombrePlantilla=$_REQUEST['formato']==1 ?"formato_liquidar.docx":"formato_liquidar2.docx";
        } else {
            if (isset($_REQUEST['remision'])) {
                $this->nombrePlantilla = "formato_remision.docx";
            } else {
                if (isset($_REQUEST['abonoFactura'])) {
                    $this->nombrePlantilla = "abono_$_REQUEST[tipoServicio]_$_REQUEST[empresa].docx";
                } else {
                    if (isset($_REQUEST['facturaServicioPublico'])) {
                        $this->nombrePlantilla = "factura_$_REQUEST[tipoServicio]_$_REQUEST[empresa].docx";
                    } else {
                        if (isset($_REQUEST['acuerdoPago'])) {
                            $this->nombrePlantilla = "recibo_pago.docx";
                        } else {
                            if (isset($_REQUEST['grupoCuenta'])) {
                                $this->nombrePlantilla = "certificado_retencion_$_REQUEST[grupoCuenta].docx";
                            } else {
                                if (isset($_REQUEST['estadoCuentaIca'])) {
                                    $this->nombrePlantilla = "estado_cuenta_ica.docx";
                                } else {
                                    $this->nombrePlantilla = "$_REQUEST[tipoDocumentoContable]_$_REQUEST[empresa]$_REQUEST[completar].docx";
                                    if (!file_exists($this->rutaAplicacion->rutaDocumentoRelativa . 'plantilla/' . $this->nombrePlantilla)) {
                                        $this->nombrePlantilla = "$_REQUEST[tipoDocumentoContable]_5.docx";
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $t = microtime(true);
        $micro = sprintf("%06d", ($t - floor($t)) * 1000000);
        $d = new DateTime(date('Y-m-d H:i:s.'.$micro, $t));

        $this->nombreDocumento = sha1($d->format("Y-m-d H:i:s.u"));
        //print($this->nombreDocumento);
        //die();
        $this->templateWord = new TemplateProcessor($this->rutaAplicacion->rutaDocumentoRelativa . 'plantilla/' . $this->nombrePlantilla);
        //print "<pre>"; print_r($this->templateWord->getVariables()); print "</pre>\n";
    }

    public function consultarInfomacion($datos) {
        /*
        print("<pre>");
        print_r($vigencia);
        print("</pre>");
        die();
        */
        // if (isset($_REQUEST['estadoCuentaIca'])) {
        //     $datos = $_REQUEST['estadoCuentaIca'];
        // }
        //datos para el template de liquidacion

        if (isset($_REQUEST['estadoCuentaIca'])) {
            if (isset($datos['estadoCuentaIca'][0])) {
                $buscar = array(',', '.', '$');
                $cambiar = array('', '', '');
                $buscar2 = array(',', '$');
                $cambiar2 = array('', '');
                $nombre = explode(" RL. ", $datos['estadoCuentaIca'][0][2]);
                $this->templateWord->setValue('nombre', $nombre[0]);
                $this->templateWord->setValue('identificacion', $datos['estadoCuentaIca'][0][0]);
                $this->templateWord->setValue('direccion', $datos['estadoCuentaIca'][0][17]);
                $this->templateWord->setValue('telefono', $datos['estadoCuentaIca'][0][20]);
                $this->templateWord->setValue('ciudad', $datos['estadoCuentaIca'][0][18]);
                $this->templateWord->setValue('regimen', $datos['estadoCuentaIca'][0][25]);
                $this->templateWord->setValue('impresion', date("Y-m-d"));
                if (isset($nombre[1])) {
                    $this->templateWord->setValue('representante', $nombre[1]);
                } else {
                    $this->templateWord->setValue('representante', "N/A");
                }
                $vigencias = 0;
                for ($i = 0, $tamano = sizeof($datos['estadoCuentaIca']); $i < $tamano; $i+=1) {
                    $vigencia = explode("</a>", $datos['estadoCuentaIca'][$i][6]);
                    $vigencia[0] = substr($vigencia[0], -4);
                    //$vigencia = explode("<", $vigencia[0]);
                    if ($vigencia[0] == $vigencias) {
                        $datos['estadoCuentaIca'][$i][10] = "XXXXX";
                        $datos['estadoCuentaIca'][$i][11] = "XXXXX";
                        $datos['estadoCuentaIca'][$i][14] = "XXXXX";
                    } else {
                        $vigencias =  $vigencia[0];
                    }
                }
                $contador = 0;
                for ($i = 0, $tamano = sizeof($datos['estadoCuentaIca']); $i < $tamano; $i+=1) {
                    if ($datos['estadoCuentaIca'][$i][10] == "Presentada" && strrpos($datos['estadoCuentaIca'][$i][11], "Pagada") !== false && strrpos($datos['estadoCuentaIca'][$i][14], "Al día") !== false) {//BUSQUEDA EN LA CADENA DE TEXTO Y ENCUENTRA
                        $contador++;
                    }
                }
                $this->templateWord->cloneRow('t0', $contador);
                $contador = 1;
                $auxTotal = 0;
                $auxInteres = 0;
                $auxSancion = 0;
                $auxImpuesto = 0;
                $auxDescuento = 0;
                for ($i = 0, $tamano = sizeof($datos['estadoCuentaIca']); $i < $tamano; $i+=1) {
                    if ($datos['estadoCuentaIca'][$i][10] == "Presentada" && strrpos($datos['estadoCuentaIca'][$i][11], "Pagada") !== false && strrpos($datos['estadoCuentaIca'][$i][14], "Al día") !== false) {
                        //BUSQUEDA EN LA CADENA DE TEXTO Y ENCUENTRA
                        $vigencia = explode("</a>", $datos['estadoCuentaIca'][$i][6]);
                        $vigencia[0] = substr($vigencia[0], -4);
                        //$vigencia = explode("<", $vigencia[0]);
                        $this->templateWord->setValue('t0#' . $contador, $vigencia[0]);
                        $this->templateWord->setValue('t1#' . $contador, $datos['estadoCuentaIca'][$i][5]);
                        $this->templateWord->setValue('t2#' . $contador, $datos['estadoCuentaIca'][$i][8]);
                        $this->templateWord->setValue('t3#' . $contador, str_replace("Declaración ", "", $datos['estadoCuentaIca'][$i][9]));
                        if (substr($datos['estadoCuentaIca'][$i][11], 7, 10) == "") {
                            $this->templateWord->setValue('t4#' . $contador, "N/A");
                            $this->templateWord->setValue('t5#' . $contador, "N/A");
                        } else {
                            $this->templateWord->setValue('t4#' . $contador, "Normal87");
                            $this->templateWord->setValue('t5#' . $contador, substr($datos['estadoCuentaIca'][$i][11], 7, 10));
                        }
                        $interes = intval(str_replace($buscar, $cambiar2, $datos['estadoCuentaIca'][$i][56]));
                        $descuento = -(intval(str_replace($buscar, $cambiar2, $datos['estadoCuentaIca'][$i][49])) - intval(str_replace($buscar, $cambiar2, $datos['estadoCuentaIca'][$i][45])) - intval(str_replace($buscar, $cambiar2, $datos['estadoCuentaIca'][$i][46])) - intval(str_replace($buscar, $cambiar2, $datos['estadoCuentaIca'][$i][47])) - intval(str_replace($buscar, $cambiar2, $datos['estadoCuentaIca'][$i][48])) - intval(str_replace($buscar, $cambiar2, $datos['estadoCuentaIca'][$i][51])));
                        $sancion = intval(str_replace($buscar, $cambiar2, $datos['estadoCuentaIca'][$i][50]));
                        $impuesto = intval(str_replace($buscar, $cambiar2, $datos['estadoCuentaIca'][$i][39])) + intval(str_replace($buscar, $cambiar2, $datos['estadoCuentaIca'][$i][40])) + intval(str_replace($buscar, $cambiar2, $datos['estadoCuentaIca'][$i][42]));
                        $total = $impuesto - $descuento + $sancion + $interes;
                        $auxTotal += $total;
                        $auxInteres += $interes;
                        $auxSancion += $sancion;
                        $auxDescuento += $descuento;
                        $auxImpuesto += $impuesto;
                        $this->templateWord->setValue('t6#' . $contador, $this->formatoAplicacion->monedaColombia4($impuesto));
                        $this->templateWord->setValue('t7#' . $contador, $this->formatoAplicacion->monedaColombia4($descuento));
                        $this->templateWord->setValue('t8#' . $contador, $this->formatoAplicacion->monedaColombia4($sancion));
                        $this->templateWord->setValue('t9#' . $contador, $this->formatoAplicacion->monedaColombia4($interes));
                        $this->templateWord->setValue('t10#' . $contador, $this->formatoAplicacion->monedaColombia4($total));
                        $contador++;
                    }
                }
                $this->templateWord->setValue('t12', $this->formatoAplicacion->monedaColombia4($auxDescuento));
                $this->templateWord->setValue('t11', $this->formatoAplicacion->monedaColombia4($auxImpuesto));
                $this->templateWord->setValue('t13', $this->formatoAplicacion->monedaColombia4($auxSancion));
                $this->templateWord->setValue('t14', $this->formatoAplicacion->monedaColombia4($auxInteres));
                $this->templateWord->setValue('t15', $this->formatoAplicacion->monedaColombia4($auxTotal));
                $contador = 0;
                for ($i = 0, $tamano = sizeof($datos['estadoCuentaIca']); $i < $tamano; $i+=1) {
                    if (strrpos($datos['estadoCuentaIca'][$i][14], "Saldo a Favor") !== false) {//BUSQUEDA EN LA CADENA DE TEXTO Y ENCUENTRA
                        $contador++;
                    }
                }
                $this->templateWord->cloneRow('t16', $contador);
                $contador = 1;
                $auxTotal = 0;
                $auxInteres = 0;
                $auxSancion = 0;
                $auxImpuesto = 0;
                $auxDescuento = 0;
                for ($i = 0, $tamano = sizeof($datos['estadoCuentaIca']); $i < $tamano; $i+=1) {
                    if (strrpos($datos['estadoCuentaIca'][$i][14], "Saldo a Favor") !== false) {
                        //BUSQUEDA EN LA CADENA DE TEXTO Y ENCUENTRA
                        $vigencia = explode("</a>", $datos['estadoCuentaIca'][$i][6]);
                        $vigencia[0] = substr($vigencia[0], -4);
                        //$vigencia = explode("<", $vigencia[0]);
                        $this->templateWord->setValue('t16#' . $contador, $vigencia[0]);
                        $this->templateWord->setValue('t17#' . $contador, $datos['estadoCuentaIca'][$i][5]);
                        $this->templateWord->setValue('t18#' . $contador, $datos['estadoCuentaIca'][$i][8]);
                        $this->templateWord->setValue('t19#' . $contador, str_replace("Declaración ", "", $datos['estadoCuentaIca'][$i][9]));
                        $interes = intval(str_replace($buscar, $cambiar2, $datos['estadoCuentaIca'][$i][56]));
                        $descuento = -(intval(str_replace($buscar, $cambiar2, $datos['estadoCuentaIca'][$i][49])) - intval(str_replace($buscar, $cambiar2, $datos['estadoCuentaIca'][$i][45])) - intval(str_replace($buscar, $cambiar2, $datos['estadoCuentaIca'][$i][46])) - intval(str_replace($buscar, $cambiar2, $datos['estadoCuentaIca'][$i][47])) - intval(str_replace($buscar, $cambiar2, $datos['estadoCuentaIca'][$i][48])) - intval(str_replace($buscar, $cambiar2, $datos['estadoCuentaIca'][$i][51])));
                        $sancion = intval(str_replace($buscar, $cambiar2, $datos['estadoCuentaIca'][$i][50]));
                        $impuesto = intval(str_replace($buscar, $cambiar2, $datos['estadoCuentaIca'][$i][39])) + intval(str_replace($buscar, $cambiar2, $datos['estadoCuentaIca'][$i][40])) + intval(str_replace($buscar, $cambiar2, $datos['estadoCuentaIca'][$i][42]));
                        $total = -($impuesto - $descuento + $sancion + $interes);
                        $auxTotal += $total;
                        $auxInteres += $interes;
                        $auxSancion += $sancion;
                        $auxDescuento += $descuento;
                        $auxImpuesto += $impuesto;
                        $this->templateWord->setValue('t20#' . $contador, $this->formatoAplicacion->monedaColombia4($impuesto));
                        $this->templateWord->setValue('t21#' . $contador, $this->formatoAplicacion->monedaColombia4($descuento));
                        $this->templateWord->setValue('t22#' . $contador, $this->formatoAplicacion->monedaColombia4($sancion));
                        $this->templateWord->setValue('t23#' . $contador, $this->formatoAplicacion->monedaColombia4($interes));
                        $this->templateWord->setValue('t24#' . $contador, $this->formatoAplicacion->monedaColombia4($total));
                        $contador++;
                    }
                }
                $this->templateWord->setValue('t26', $this->formatoAplicacion->monedaColombia4($auxDescuento));
                $this->templateWord->setValue('t25', $this->formatoAplicacion->monedaColombia4($auxImpuesto));
                $this->templateWord->setValue('t27', $this->formatoAplicacion->monedaColombia4($auxSancion));
                $this->templateWord->setValue('t28', $this->formatoAplicacion->monedaColombia4($auxInteres));
                $this->templateWord->setValue('t29', $this->formatoAplicacion->monedaColombia4($auxTotal));
                $contador = 0;
                for ($i = 0, $tamano = sizeof($datos['estadoCuentaIca']); $i < $tamano; $i+=1) {
                    if ($datos['estadoCuentaIca'][$i][10] == "Presentada" && $datos['estadoCuentaIca'][$i][11] == "Pendiente de Pago") {
                        $contador++;
                    }
                }
                $this->templateWord->cloneRow('t30', $contador);
                $contador = 1;
                $auxTotal = 0;
                $auxInteres = 0;
                $auxSancion = 0;
                $auxImpuesto = 0;
                $auxDescuento = 0;
                for ($i = 0, $tamano = sizeof($datos['estadoCuentaIca']); $i < $tamano; $i+=1) {
                    if ($datos['estadoCuentaIca'][$i][10] == "Presentada" && ($datos['estadoCuentaIca'][$i][11] == "Pendiente de Pago" || $datos['estadoCuentaIca'][$i][11] == "Acuerdo de Pago")) {
                        $vigencia = explode("</a>", $datos['estadoCuentaIca'][$i][6]);
                        $vigencia[0] = substr($vigencia[0], -4);
                        //$vigencia = explode("<", $vigencia[0]);
                        $this->templateWord->setValue('t30#' . $contador, $vigencia[0]);
                        $this->templateWord->setValue('t31#' . $contador, $datos['estadoCuentaIca'][$i][5]);
                        $this->templateWord->setValue('t32#' . $contador, $datos['estadoCuentaIca'][$i][8]);
                        $this->templateWord->setValue('t33#' . $contador, str_replace("Declaración ", "", $datos['estadoCuentaIca'][$i][9]));
                        $interes = intval(str_replace($buscar, $cambiar2, $datos['estadoCuentaIca'][$i][56]));
                        $descuento = -(intval(str_replace($buscar, $cambiar2, $datos['estadoCuentaIca'][$i][49])) - intval(str_replace($buscar, $cambiar2, $datos['estadoCuentaIca'][$i][45])) - intval(str_replace($buscar, $cambiar2, $datos['estadoCuentaIca'][$i][46])) - intval(str_replace($buscar, $cambiar2, $datos['estadoCuentaIca'][$i][47])) - intval(str_replace($buscar, $cambiar2, $datos['estadoCuentaIca'][$i][48])) - intval(str_replace($buscar, $cambiar2, $datos['estadoCuentaIca'][$i][51])));
                        $sancion = intval(str_replace($buscar, $cambiar2, $datos['estadoCuentaIca'][$i][50]));
                        $impuesto = intval(str_replace($buscar, $cambiar2, $datos['estadoCuentaIca'][$i][39])) + intval(str_replace($buscar, $cambiar2, $datos['estadoCuentaIca'][$i][40])) + intval(str_replace($buscar, $cambiar2, $datos['estadoCuentaIca'][$i][42]));
                        $total = $impuesto - $descuento + $sancion + $interes;
                        $auxTotal += $total;
                        $auxInteres += $interes;
                        $auxSancion += $sancion;
                        $auxDescuento += $descuento;
                        $auxImpuesto += $impuesto;
                        $this->templateWord->setValue('t34#' . $contador, $this->formatoAplicacion->monedaColombia4($impuesto));
                        $this->templateWord->setValue('t35#' . $contador, $this->formatoAplicacion->monedaColombia4($descuento));
                        $this->templateWord->setValue('t36#' . $contador, $this->formatoAplicacion->monedaColombia4($sancion));
                        $this->templateWord->setValue('t37#' . $contador, $this->formatoAplicacion->monedaColombia4($interes));
                        $this->templateWord->setValue('t38#' . $contador, $this->formatoAplicacion->monedaColombia4($total));
                        $contador++;
                    }
                }
                $this->templateWord->setValue('t40', $this->formatoAplicacion->monedaColombia4($auxDescuento));
                $this->templateWord->setValue('t39', $this->formatoAplicacion->monedaColombia4($auxImpuesto));
                $this->templateWord->setValue('t41', $this->formatoAplicacion->monedaColombia4($auxSancion));
                $this->templateWord->setValue('t42', $this->formatoAplicacion->monedaColombia4($auxInteres));
                $this->templateWord->setValue('t43', $this->formatoAplicacion->monedaColombia4($auxTotal));
            }
            // [0] => 900364032
            // [1] => 2
            // [2] => contSoft SOLUCIONES INFORMATICAS S.A.S RL. GABRIEL HOYOS
            // [3] => YOPAL
            // [4] => CASANARE
            // [5] => 0001001
            // [6] => <a target='_blank' href='http://127.0.0.1/ica/public/storage/documentos/16db1234-3503-4a6a-ba76-53ce56bd866d.pdf'>2011</a>
            // [7] => 7
            // [8] => 2012-03-30
            // [9] => Declaración Inicial
            // [10] => Presentada sin pago
            // [11] => Pagada
            // [12] => $0
            // [13] => $0
            // [14] => <a data-financiero='declaracion=56897***vigencia=9***tercero=13538***estado=3***fecha=2012-03-30***formulario=0001001***total=0***mora=0***descuento=0***recibo2=0***vigenciaNumero=2011' href='javascript:void(0);'>Al día</a>
            // [15] =>
            // [16] =>
            // [17] => Calle 30 # 28 - 69
            // [18] => YOPAL
            // [19] => CASANARE
            // [20] => 3118474705
            // [21] => contabilidad@contSoft.com.co
            // [22] => n
            // [23] => n
            // [24] => 0
            // [25] => Persona Juridica
            // [26] => 01
            // [27] => $467.455.000
            // [28] => $467.455.000
            // [29] => $0
            // [30] => $0
            // [31] => $0
            // [32] => $0
            // [33] => $0
            // [34] => $0
            // [35] => $0
            // [36] => $0
            // [37] => $0
            // [38] => $0
            // [39] => $020
            // [40] => $021
            // [41] => $022
            // [42] => $023
            // [43] => $024
            // [44] => $025
            // [45] => $026
            // [46] => $027
            // [47] => $028
            // [48] => $029
            // [49] => $030
            // [50] => $031
            // [51] => $032
            // [52] => $033
            // [53] => $034
            // [54] => $035
            // [55] => $036
            // [56] => $037
            // [57] => $038
        } else {
            ////////////////////////////////////////
            if (isset($datos['liquidacion']) || isset($datos['facturaServicioPublico']) || isset($datos['abonoFactura']) || isset($datos['grupoCuenta'])) {
                $j = 1;
                foreach ($datos as $key => $value) {
                    switch ($key) {
                        case 'crbases':
                        case 'crtotales':
                        case 'crcuentas':
                        case 'crps':
                            if ($j == 1) {
                                //print "<pre>"; print_r(sizeof($_REQUEST["crtotales"])); print "</pre>\n";
                                $this->templateWord->cloneRow('crtotales', sizeof($_REQUEST['crtotales']));  // Clonar.
                                for ($i = 0, $tamano = sizeof($_REQUEST["crtotales"]), $j = 1; $i < $tamano; $i++, $j++) {
                                    $this->templateWord->setValue('crbases#' . $j, $_REQUEST["crbases"][$i]);
                                    $this->templateWord->setValue('crtotales#' . $j, $_REQUEST["crtotales"][$i]);
                                    $this->templateWord->setValue('crcuentas#' . $j, $_REQUEST["crcuentas"][$i]);
                                    $this->templateWord->setValue('crps#' . $j, $_REQUEST["crps"][$i]);
                                }
                            }
                            break;
                        default:
                            $this->templateWord->setValue($key, $value);
                            break;
                    }
                }
            } else {
                if (isset($_REQUEST['remision']) || isset($_REQUEST['acuerdoPago'])) {
                    if (isset($_REQUEST['remision'])) {
                        $this->templateWord->setValue('fecha', $datos['fecha']);
                        $this->templateWord->setValue('periodos', $datos['periodos']);
                        $this->templateWord->setValue('hasta', $datos['hasta']);
                        $this->templateWord->setValue('letra', $datos['letra']);
                        $this->templateWord->setValue('otros', $datos['otros']);
                        $this->templateWord->setValue('reconexion', $datos['reconexion']);
                        $this->templateWord->setValue('descuentos', $datos['descuentos']);
                        $this->templateWord->setValue('instalacion', $datos['instalacion']);
                        $this->templateWord->setValue('nombre', $datos['nombre']);
                        $this->templateWord->setValue('documento', $datos['documento']);
                        $this->templateWord->setValue('direccion', $datos['direccion']." ".$datos['ciudad']);
                        $this->templateWord->setValue('movil', $datos['telefono']);
                        $this->templateWord->setValue('total', $datos['total']);
                        $this->templateWord->setValue('textootros', $datos['textootros']);
                        $this->templateWord->setValue('remision', $datos['remision']);
                    } else {
                        if (!isset($_REQUEST["declaracionCiudadanoManual"])) {
                            $datosDeclaracion = $this->centralConsultaIca->datosDeclaracion();
                            $this->templateWord->setValue('formulario', $datosDeclaracion[0]['formulario']);
                            $this->templateWord->setValue('anio', substr($datosDeclaracion[0]['created_at'], 0, 4));
                            $this->templateWord->setValue('mes', substr($datosDeclaracion[0]['created_at'], 5, 2));
                            $this->templateWord->setValue('dia', substr($datosDeclaracion[0]['created_at'], 8, 2));
                        }
                        if (isset($_REQUEST["declaracionCiudadano"])) {
                            $this->templateWord->setValue('numeroAcuerdo', "");
                            $_REQUEST["formulario"] = $datosDeclaracion[0]['formulario'];
                            $this->templateWord->setValue('aio', '');
                            $this->templateWord->setValue('ms', '');
                            $this->templateWord->setValue('da', '');
                            $this->templateWord->setValue('cuota', '');
                            $this->templateWord->setValue('cuotas', '');
                            $this->templateWord->setValue('cvalorTotal', $this->formatoAplicacion->monedaColombia4($datosDeclaracion[0]['total']));
                            $_REQUEST["cvalorTotal"] = $datosDeclaracion[0]['total'];
                            $_REQUEST["cfechaPago"] = date('Y-m-d');
                            $cimpuestoc = $datosDeclaracion[0]['impuesto'];
                            $cavisoc = $datosDeclaracion[0]['avisos_tableros'];
                            $csobretasac = $datosDeclaracion[0]['sobretasa_bomberil'];
                            $csancionc = $datosDeclaracion[0]['sanciones'];
                            $cinteresc = $datosDeclaracion[0]['pago_mora'];
                            if ($cimpuestoc < 0) {
                                $cavisoc += $cimpuestoc;
                                $cimpuestoc = 0;
                            }
                            if ($cavisoc  < 0) {
                                $csobretasac += $cavisoc ;
                                $cavisoc = 0;
                            }
                            if ($csobretasac < 0) {
                                $csancionc += $csobretasac;
                                $csobretasac = 0;
                            }
                            if ($csancionc < 0) {
                                $cinteresc += $csancionc;
                                $csancionc = 0;
                            }
                            $this->templateWord->setValue('cimpuestoc', $this->formatoAplicacion->monedaColombia4($cimpuestoc));
                            $this->templateWord->setValue('cavisoc', $this->formatoAplicacion->monedaColombia4($cavisoc));
                            $this->templateWord->setValue('csobretasac', $this->formatoAplicacion->monedaColombia4($csobretasac));
                            $this->templateWord->setValue('csancionc', $this->formatoAplicacion->monedaColombia4($csancionc));
                            $this->templateWord->setValue('cinteresc', $this->formatoAplicacion->monedaColombia4($cinteresc));
                            $_SESSION["empresa"] = $datosDeclaracion[0]['empresa'];
                            $vigenciasCiudadanoICA = $this->centralConsulta->vigenciasCiudadanoICA($_SESSION['empresa']);
                            $key = array_search($datosDeclaracion[0]['vigencia'], array_column($vigenciasCiudadanoICA, 'vigencia'));
                            if ($key == null && $key !=0) {
                                $key = -1;
                            }
                            $vigencia = (isset($vigenciasCiudadanoICA[$key]['descripcion'])) ? $vigenciasCiudadanoICA[$key]['descripcion'] : "";
                            $this->templateWord->setValue('periodo', $vigencia);
                            $datosTercero = $this->centralConsulta->datosTercero($datosDeclaracion[0]['tercero']);
                            $this->templateWord->setValue('nit', $datosTercero[0]["nit"]);
                            if ($datosTercero[0]["formato"] == "1") {
                                $this->templateWord->setValue('contribuyente', $datosTercero[0]["razon_social"]);
                            } else {
                                $this->templateWord->setValue('contribuyente', $datosTercero[0]["nombres"] . " " . $datosTercero[0]["apellidos"]);
                            }
                        } else {
                            if (isset($_REQUEST["cfechaPago"])) {
                                $_REQUEST["cfechaPago2"] = $_REQUEST['cfechaPago'];
                            }

                            if (!isset($_REQUEST["declaracionCiudadanoManual"])) {
                                $acuerdoPago = new AcuerdoPagos($this->conexion, $this->centralConsulta);

                                $info = $this->centralConsulta->icaAcuerdoPagoCuotaID($_REQUEST['acuerdo'], $_REQUEST['acuerdopagocuota']);
                                $nuevoAcuerdoLiquidacion =$acuerdoPago->consecutivoIcaAcuerdoPagoLiquidacion();
                                $cuotaIca = $this->centralConsulta->busquedaCuotaLiquidacionIca($_REQUEST['acuerdopagocuota']);
                                if (sizeof($cuotaIca) != 0 && $cuotaIca[0]['estado_pago'] == 'd') {
                                    $insert = $acuerdoPago->query($this->centralConsulta->updateIcaAcuerdoPagoLiquidacion(), [
                                        'vigencia'           => Helper::vigencia(),
                                        'fecha_generacion'   => Helper::fechaActual(),
                                        'fecha_limite_pago'  => $info[0]['fecha_pago'],
                                        'tercero'            => $_REQUEST['tercero'],
                                        'archivo'            => '',
                                        'fecha_sistema'      => Helper::fechaSistema(),
                                        'acuerdo_pago_liquidacion' => $cuotaIca[0]['ica_acuerdo_pago_liquidacion']
                                    ]);
                                    $auxnuevoAcuerdoLiquidacion = $cuotaIca[0]['ica_acuerdo_pago_liquidacion'];
                                }
                                if (sizeof($cuotaIca) != 0 && $cuotaIca[0]['estado_pago'] == 'p') {
                                    $error = true;
                                    $mensaje = 'Esta cuota ya se encuentra';
                                }

                                //Si no existe la liquidacion para esa cuota la inserta.
                                if (sizeof($cuotaIca) == 0) {
                                    $insert = $acuerdoPago->query($this->centralConsulta->insertIcaAcuerdoPagoLiquidacion(), [
                                        'acuerdo_pago_liquidacion'   => $nuevoAcuerdoLiquidacion,
                                        'acuerdo_pago_cuota' => $_REQUEST['acuerdopagocuota'],
                                        'vigencia'           => Helper::vigencia(),
                                        'numero_liquidacion_acuerdo' => $acuerdoPago->referenciaLiquidacionIcaAcuerdoi(),
                                        'fecha_generacion'   => Helper::fechaActual(),
                                        'fecha_limite_pago'  => $info[0]['fecha_pago'],
                                        'estado_pago'        => 'd',
                                        'tercero'            => $_REQUEST['tercero'],
                                        'tipo_impuesto'      => $acuerdoPago->tipoImpuesto(),
                                        'archivo'            => ''
                                    ]);
                                    $auxnuevoAcuerdoLiquidacion = $nuevoAcuerdoLiquidacion;
                                }
                                //aca modificar
                                $datosAcuerdoIca = $this->centralConsulta->datosAcuerdoIca();
                                $datosLiquidacion = $this->centralConsulta->busquedaCuotaLiquidacionIca($_REQUEST['acuerdopagocuota']);

                                $this->templateWord->setValue('numeroAcuerdo', $datosLiquidacion[0]['numero_liquidacion_ica_acuerdo']);
                                $_REQUEST["formulario"] = $datosLiquidacion[0]['numero_liquidacion_ica_acuerdo'];
                                $_REQUEST["idLiquidacion"] =$datosLiquidacion[0]['ica_acuerdo_pago_liquidacion'];
                                $this->templateWord->setValue('aio', substr($_REQUEST["cfechaPago"], 0, 4));
                                $this->templateWord->setValue('ms', substr($_REQUEST["cfechaPago"], 5, 2));
                                $this->templateWord->setValue('da', substr($_REQUEST["cfechaPago"], 8, 2));
                            } else {
                                $_REQUEST["nit"] .= "-$_REQUEST[dv]";
                                $_SESSION["empresa"] = $_REQUEST["empresa"];
                                $vigenciasCiudadanoICA = $this->centralConsulta->vigenciasCiudadanoICA($_SESSION['empresa']);
                                $key = array_search($_REQUEST["vigencia"], array_column($vigenciasCiudadanoICA, 'vigencia'));
                                if ($key == null && $key !=0) {
                                    $key = -1;
                                }
                                $vigencia = (isset($vigenciasCiudadanoICA[$key]['descripcion'])) ? $vigenciasCiudadanoICA[$key]['descripcion'] : "";
                                $this->templateWord->setValue('periodo', $vigencia);
                                if (isset($_REQUEST['guardar'])) {
                                    if ($_REQUEST["cfechaPago"] != "") {
                                        //$_REQUEST["cfechaPago"] = date('Y-m-d');
                                    }
                                    if ($_REQUEST["fecha_pago"] == "") {
                                        $_REQUEST["fecha_pago"] = date('Y-m-d');
                                    }
                                    //$_REQUEST["fecha_pago"] = date('Y-m-d');
                                }
                                if ($_REQUEST["cfechaPago"] == "") {
                                    $this->templateWord->setValue('aio', substr($_REQUEST["fecha_pago"], 0, 4));
                                    $this->templateWord->setValue('ms', substr($_REQUEST["fecha_pago"], 5, 2));
                                    $this->templateWord->setValue('da', substr($_REQUEST["fecha_pago"], 8, 2));
                                    $_REQUEST["cfechaPago"] = $_REQUEST["fecha_pago"];
                                } else {
                                    $this->templateWord->setValue('aio', substr($_REQUEST["cfechaPago"], 0, 4));
                                    $this->templateWord->setValue('ms', substr($_REQUEST["cfechaPago"], 5, 2));
                                    $this->templateWord->setValue('da', substr($_REQUEST["cfechaPago"], 8, 2));
                                }
                                $this->templateWord->setValue('anio', substr($_REQUEST["fechafor"], 0, 4));
                                $this->templateWord->setValue('mes', substr($_REQUEST["fechafor"], 5, 2));
                                $this->templateWord->setValue('dia', substr($_REQUEST["fechafor"], 8, 2));
                                //print "<pre>"; print_r($_REQUEST); print "</pre>\n";
                            }
                        }

                        foreach ($_REQUEST as $key => $val) {
                            switch ($key) {
                                case '':
                                    break;
                                default:
                                    $this->templateWord->setValue($key, str_replace("&", "&amp;", $val));
                                    break;
                            }
                        }

                        if (isset($_REQUEST["valor"])) {
                            if ($_REQUEST["valor"] != "" && $_REQUEST["valor"] != "0") {
                                $_REQUEST['auxFormulario'] = $_REQUEST['formulario'];
                                $_REQUEST['formulario'] = $_REQUEST['numeroAcuerdo'];
                                $_REQUEST['numeroAcuerdo'] = $_REQUEST['auxFormulario'];
                            }
                        }
                        //print "<pre>"; print_r($_REQUEST); print "</pre>\n";
                    }
                } else {
                    error_reporting(0);

                    $datosSoporteContable = $this->centralConsulta->datosSoporteContable($_REQUEST['soporteContable']);
                    if ($datosSoporteContable[0]["registro_presupuestal"] != "0" && $datosSoporteContable[0]["registro_presupuestal"] != "") {
                        $contador = 1;
                        if ($datosSoporteContable[0]["obligacion_presupuestal"] != "0" && $datosSoporteContable[0]["obligacion_presupuestal"] != "") {
                            $rubroRP = $this->centralConsulta->rubroObligacion($datosSoporteContable[0]["obligacion_presupuestal"]);
                            foreach ($rubroRP as $rp) {
                                $modalidad_seleccion = explode(' - ', $rp["modalidad_seleccion"]);
                                $modalidad_soporte = explode(' - ', $rp["modalidad_soporte"]);
                                $clase_presupuesto = explode(' - ', $rp["clase_presupuesto"]);
                                $this->templateWord->setValue("vg$contador", $rp["vigencia"]);
                                $this->templateWord->setValue("codigoRubro$contador", $rp["codigorubro"]);
                                $this->templateWord->setValue("fe$contador", $rp["fuente"]);
                                $this->templateWord->setValue("codigoNombre$contador", $rp["nombrerubro"]);
                                $this->templateWord->setValue("nfe$contador", $rp["nombrefuente"]);
                                $this->templateWord->setValue("modo_seleccion", $modalidad_seleccion[1]);
                                $this->templateWord->setValue("reg_numeros", $modalidad_seleccion[0]);
                                $this->templateWord->setValue("soporte", $modalidad_soporte[1]);
                                $this->templateWord->setValue("numeros", $modalidad_soporte[0]);
                                $this->templateWord->setValue("tipo", $clase_presupuesto[1]);
                                $this->templateWord->setValue("textoPresupuesto", "Objeto $rp[objeto] - Contrato $rp[contrato] - Estudio Previo $rp[estudio_previo]");
                                $this->templateWord->setValue("valorRp$contador", $this->formatoAplicacion->monedaColombia4($rp["valor_obligacion"]));
                                $this->templateWord->setValue("fecha_rp", "$rp[fecha_rp]");
                            }
                        } else {
                            $rubroRP = $this->centralConsulta->rubroRP($datosSoporteContable[0]["registro_presupuestal"]);
                            foreach ($rubroRP as $rp) {
                                $modalidad_seleccion = explode(' - ', $rp["modalidad_seleccion"]);
                                $modalidad_soporte = explode(' - ', $rp["modalidad_soporte"]);
                                $clase_presupuesto = explode(' - ', $rp["clase_presupuesto"]);
                                $this->templateWord->setValue("modo_seleccion", $modalidad_seleccion[1]);
                                $this->templateWord->setValue("reg_numeros", $modalidad_seleccion[0]);
                                $this->templateWord->setValue("soporte", $modalidad_soporte[1]);
                                $this->templateWord->setValue("numeros", $modalidad_soporte[0]);
                                $this->templateWord->setValue("tipo", $clase_presupuesto[1]);
                                $this->templateWord->setValue("textoPresupuesto", "Objeto $rp[objeto] - Contrato $rp[contrato] - Estudio Previo $rp[estudio_previo]");
                                $this->templateWord->setValue("fecha_rp", "$rp[fecha_rp]");
                            }                            
                        }
                    }

                    if ($datosSoporteContable[0]["prefijo"] != "0") {
                        $datosPrefijo = $this->centralConsulta->datosPrefijo($datosSoporteContable[0]["prefijo"]);
                        $this->templateWord->setValue('prefijo', $datosPrefijo[0]["descripcion"]. ' -');
                    } else {
                        $this->templateWord->setValue('prefijo', '');
                    }
                    $this->templateWord->setValue('noSoporteContable', $datosSoporteContable[0]["consecutivo"]);
                    $datosTercero = $this->centralConsulta->datosTercero($datosSoporteContable[0]["tercero_elabora"]);
                    $this->templateWord->setValue('elaboroSoporteContable', $datosTercero[0]["abreviado"]);
                    $datosTercero = $this->centralConsulta->datosTercero($datosSoporteContable[0]["tercero_recibe"]);
                    $this->templateWord->setValue('beneficiario', $datosTercero[0]["abreviado"]);
                    if ($datosTercero[0]["razon_social"] == "") {
                        $this->templateWord->setValue('5', strtoupper($datosTercero[0]["nombres"] . " " . $datosTercero[0]["apellidos"]));
                    } else {
                        $this->templateWord->setValue('5', strtoupper($datosTercero[0]["razon_social"]));
                    }
                    //$this->templateWord->setValue('5', $datosTercero[0]["nombre_tercero"]);
                    $this->templateWord->setValue('nitBeneficiario', $datosTercero[0]["nit"]);
                    $this->templateWord->setValue('telefonoBeneficiario', $_REQUEST['telefono']);
                    $this->templateWord->setValue('formaPago', $_REQUEST['formaPago']);
                    $this->templateWord->setValue('cuentaBanco', $_REQUEST['cuentaBanco']);



                    /////////////////////Traer tercero responsable almacen (Entrada y salida)//////////////////////////////////////////////
                   if($_REQUEST['tipoDocumentoContable'] == 22 || $_REQUEST['tipoDocumentoContable'] == 31){
                        $responsableBodega = $this->centralConsulta->terceroSoporteAlmacen($_REQUEST['soporteContable']);
                        $this->templateWord->setValue('almacenista', $responsableBodega[0]['nombre_tercero']);
                    //Cargar datos objeto contrato, codigo, mod_seleccion y doc soporte
                    //$informacionRegistroPresupuestal = $this->centralConsulta->rubroRP($_REQUEST['soporteContable']);
                    //var_dump($informacionRegistroPresupuestal);

                        if ($datosSoporteContable[0]["registro_presupuestal"] != "0" && $datosSoporteContable[0]["registro_presupuestal"] != "") {
                            $rubroRP = $this->centralConsulta->rubroRP($datosSoporteContable[0]["registro_presupuestal"]);
                            $this->templateWord->setValue('modalidad_seleccion', $rubroRP[0]['modalidad_seleccion']);
                            $this->templateWord->setValue('estudio_previo', $rubroRP[0]['estudio_previo']);
                            $this->templateWord->setValue('numero_rp', $rubroRP[0]['numero_rp']);
                            $this->templateWord->setValue('objeto', $rubroRP[0]['objeto']);
                        }
                    }
                    /////////////////////END Traer tercero responsable almacen//////////////////////////////////////////////


                    $_REQUEST["soporteDescuento"] = str_replace(',', '.', str_replace('.', '', $_REQUEST["soporteDescuento"]));
                    $_REQUEST["soporteSubtotal"] = str_replace(',', '.', str_replace('.', '', $_REQUEST["soporteSubtotal"]));
                    $_REQUEST["totalBruto"] = $_REQUEST["soporteSubtotal"] - $_REQUEST["soporteDescuento"];
                    $this->templateWord->setValue('totalBruto', $this->formatoAplicacion->monedaColombia4($_REQUEST['totalBruto']));

                    $_REQUEST["soporteIva"] = str_replace(',', '.', str_replace('.', '', $_REQUEST["soporteIva"]));
                    $_REQUEST["soporteTotal"] = str_replace(',', '.', str_replace('.', '', $_REQUEST["soporteTotal"]));
                    $_REQUEST["soporteTotalDocumento"] = str_replace(',', '.', str_replace('.', '', $_REQUEST["soporteTotalDocumento"]));

                    $this->templateWord->setValue('descuento', $this->formatoAplicacion->monedaColombia4($_REQUEST['soporteDescuento']));
                    $this->templateWord->setValue('soporteSubtotal', $this->formatoAplicacion->monedaColombia4($_REQUEST['soporteSubtotal']));

                    $_REQUEST["soporteReteFuente"] = $this->centralConsulta->rlAsientoContableSoporteGrupoCuenta($_REQUEST['soporteContable'], '4');
                    $_REQUEST["soporteReteIva"] = $this->centralConsulta->rlAsientoContableSoporteGrupoCuenta($_REQUEST['soporteContable'], '5');
                    $_REQUEST["soporteReteIca"] = $this->centralConsulta->rlAsientoContableSoporteGrupoCuenta($_REQUEST['soporteContable'], '6');
                    $this->templateWord->setValue('soporteReteFuente', $this->formatoAplicacion->monedaColombia4($_REQUEST["soporteReteFuente"]));
                    $this->templateWord->setValue('soporteReteIva', $this->formatoAplicacion->monedaColombia4($_REQUEST["soporteReteIva"]));
                    $this->templateWord->setValue('soporteReteIca', $this->formatoAplicacion->monedaColombia4($_REQUEST["soporteReteIca"]));
                    // <option value="1">NORMAL</option>
                    // <option value="2">TESORERIA</option>
                    // <option value="5">RETENCIÓN DE IVA</option>
                    // <option value="6">RETENCIÓN DE ICA</option>
                    // <option value="8">RETEIVA RÉGIMEN SIMPLIFICADO C</option>
                    // <option value="10">CUENTAS DE DESTINO</option>
                    // <option value="3">IVA</option>
                    // <option value="4">RETENCIÓN EN LA FUENTE</option>
                    // <option value="9">RETEIVA RÉGIMEN SIMPLIFICADO D</option>
                    // <option value="7" selected="selected">RETENCIÓN CREE</option>
                    // <option value="11">IMPUESTO AL CONSUMO</option>
                    // -  $_REQUEST["soporteReteFuente"] - $_REQUEST["soporteReteIca"]
                    $this->templateWord->setValue('soporteValorIva', $this->formatoAplicacion->monedaColombia4($_REQUEST['soporteIva']));
                    $this->templateWord->setValue('soporteTotal', $this->formatoAplicacion->monedaColombia4($_REQUEST['soporteTotal']));
                    $this->templateWord->setValue('soporteTotalDocumento', $this->formatoAplicacion->monedaColombia4($_REQUEST['soporteTotalDocumento']));
                    list($direccion, $ciudad) = explode('/', $_REQUEST['direccion']);
                    $this->templateWord->setValue('direccionBeneficiario', $direccion);
                    $ciudad = explode(',', $ciudad);
                    $this->templateWord->setValue('ciudadBeneficiario', $ciudad[0]);
                    $rlCampoSoporteContable = $this->centralConsulta->rlCampoSoporteContable($_REQUEST['soporteContable']);
                    foreach ($rlCampoSoporteContable as $campo) {
                        switch ($campo['nombre']) {
                            case 'formaPago':
                                break;
                            case 'fechaDocumento':
                                $this->templateWord->setValue($campo['nombre'], $campo['valor']);
                                $this->templateWord->setValue('1', substr($campo['valor'], 0, 4));
                                $this->templateWord->setValue('2', substr($campo['valor'], 5, 2));
                                $this->templateWord->setValue('3', substr($campo['valor'], 8, 2));
                                break;
                            default:
                                $this->templateWord->setValue($campo['nombre'], $campo['valor']);
                                break;
                        }
                    }
                    //PROCESO CAMBIO ID
                    $verificarUtilizadoEntrada = $this->centralConsulta->verificarUtilizadoEntrada($_REQUEST['soporteContable']);
                    if ($verificarUtilizadoEntrada != 0) {
                        //$_REQUEST['soporteContable'] = $verificarUtilizadoEntrada;

                    }
                    if ($_REQUEST["completar"] == "_1" || ($_REQUEST["tipoDocumentoContable"] == "24" && $_REQUEST["empresa"] == "3")) {
                        $rlSoporteContableProducto2 = $this->centralConsulta->rlAsientoContableSoporte($_REQUEST['soporteContable']);
                        foreach ($rlSoporteContableProducto2 as $producto) {
                            $movimiento = ($producto["debito"] == "0") ? "_c" : "_d";
                            if (isset($rlSoporteContableProducto["$producto[cuenta]_$movimiento"])) {
                                $rlSoporteContableProducto["$producto[cuenta]_$movimiento"]["base_ret"] += $producto["base_ret"];
                                $rlSoporteContableProducto["$producto[cuenta]_$movimiento"]["debito"] += $producto["debito"];
                                $rlSoporteContableProducto["$producto[cuenta]_$movimiento"]["credito"] += $producto["credito"];
                            } else {
                                $rlSoporteContableProducto["$producto[cuenta]_$movimiento"] =  Array(   "cuenta"                => $producto["cuenta"],
                                                                                                        "base_ret"              => $producto["base_ret"],
                                                                                                        "concepto"              => $producto["concepto"],
                                                                                                        "cantidad"              => $producto["cantidad"],
                                                                                                        "vence"                 => $producto["vence"],
                                                                                                        "cantFisica"            => $producto["cantFisica"],
                                                                                                        "cantSistema"           => $producto["cantSistema"],
                                                                                                        "cantDiferencia"        => $producto["cantDiferencia"],
                                                                                                        "centro_costo"          => $producto["centro_costo"],
                                                                                                        "ubicacion_inventario"  => $producto["ubicacion_inventario"],
                                                                                                        "unidad_manejo"         => $producto["unidad_manejo"],
                                                                                                        "valor_unitario"        => $producto["valor_unitario"],
                                                                                                        "porcentaje_iva"        => $producto["porcentaje_iva"],
                                                                                                        "total"                 => $producto["total"],
                                                                                                        "tercero"               => $producto["tercero"],
                                                                                                        "debito"                => $producto["debito"],
                                                                                                        "credito"               => $producto["credito"]
                                                                                                    );                                
                            }
                        }
                    } else {
                        $rlSoporteContableProducto = $this->centralConsulta->rlSoporteContableProducto($_REQUEST['soporteContable']);
                    }
                    $contador = 1;
                    $usoClone = 0;
                    if (($_REQUEST["tipoDocumentoContable"] == "2" || $_REQUEST["tipoDocumentoContable"] == "3" || $_REQUEST["tipoDocumentoContable"] == "5" || ($_REQUEST["tipoDocumentoContable"] == "24" && $_REQUEST["empresa"] == "3")) && $_REQUEST["completar"] != "_2") {
                        if (count($rlSoporteContableProducto) > 10) {
                            $usoClone = 7;
                            $this->templateWord->cloneRow('soporteCuenta9', (count($rlSoporteContableProducto) - 9));
                        }
                    }

                    if ($_REQUEST["tipoDocumentoContable"] == "8" || $_REQUEST["tipoDocumentoContable"] == "32" || $_REQUEST["tipoDocumentoContable"] == "22") {
                        if (count($rlSoporteContableProducto) > 10) {
                            $usoClone = 7;
                            $this->templateWord->cloneRow('soporteProducto9', (count($rlSoporteContableProducto) - 9));
                        }
                    }
                    $cheque = 0;
                    $_REQUEST["soporteDebito"] = isset($_REQUEST["soporteDebito"]) ? $_REQUEST["soporteDebito"] : 0;
                    $_REQUEST["soporteCredito"] = isset($_REQUEST["soporteCredito"]) ? $_REQUEST["soporteCredito"] : 0;
                    $_REQUEST["soporteDeduccion"] = 0;
                    $_REQUEST["soporteBase"] = 0;
                    $idContadorConcepto = 1;
                    foreach ($rlSoporteContableProducto as $key=>&$dato) {
                        $idContador = $contador;
                        if ($usoClone != 0) {
                            if ($key > $usoClone && (count($rlSoporteContableProducto) - 1) != $key) {
                                $idContador = "9#" . ($key - $usoClone);
                            } else {
                                if ((count($rlSoporteContableProducto) - 1) == $key) {
                                    $idContador = $usoClone + 3;
                                }
                            }
                        }

                        if ($dato["cuenta"] != "") {
                            $datosPucEmpresa = $this->centralConsulta->datosPucEmpresa($dato["cuenta"]);
                            /*$this->templateWord->setValue("soporteCuenta$idContador", $datosPucEmpresa[0]["codigo"]." ".$datosPucEmpresa[0]["cuenta"]);*/
                            if (($_REQUEST["tipoDocumentoContable"] == "24" && $_REQUEST["empresa"] == "3")) {
                                $this->templateWord->setValue("soporteCuenta$idContador", $datosPucEmpresa[0]["codigo"]. " " . $datosPucEmpresa[0]["cuenta"]);
                                $this->templateWord->setValue("soporteBase$idContador", $this->formatoAplicacion->monedaColombia4($dato["base_ret"]));

                            } else {
                                $this->templateWord->setValue("soporteCuenta$idContador", $datosPucEmpresa[0]["codigo"]);
                            }
                            $this->templateWord->setValue("soporteNombreCuenta$idContador", $datosPucEmpresa[0]["cuenta"]);
                            $this->templateWord->setValue("p$idContador", $datosPucEmpresa[0]["codigo"]);
                            if (strrpos($datosPucEmpresa[0]["codigo"], "11") !== false) {
                                $nombreCuenta = explode(" ", $datosPucEmpresa[0]["cuenta"]);
                                $this->templateWord->setValue('ba', $nombreCuenta[0]);
                                $this->templateWord->setValue('de', $nombreCuenta[1]);
                            //if ($datosPucEmpresa[0]["codigo"] >= '1110' &&  $datosPucEmpresa[0]["codigo"] <= '1130') {
                                $cheque = ($cheque == 0) ? 1 : 2;
                            }
                        }

                        if ($dato["producto"] != "") {
                            $datosProducto = $this->centralConsulta->datosProducto($dato["producto"]);
                            $this->templateWord->setValue("soporteProducto$idContador", $datosProducto[0]["nombre"]." ".$datosProducto[0]["referencia"]);
                            $this->templateWord->setValue("CodigoProducto$idContador", $datosProducto[0]["sku"]);
                        }

                        if ($_REQUEST["tipoDocumentoContable"] == "3") {
                            if (strrpos($dato["concepto"], "PAGO CONCEPTO") !== false) {//BUSQUEDA EN LA CADENA DE TEXTO Y ENCUENTRA
                                $this->templateWord->setValue("soporteConcepto$idContadorConcepto", substr($dato["concepto"], 14));
                                if ($dato["debito"] == "0") {
                                    $this->templateWord->setValue("soporteValor$idContadorConcepto", $this->formatoAplicacion->monedaColombia4($dato["credito"]));
                                } else {
                                    $this->templateWord->setValue("soporteValor$idContadorConcepto", $this->formatoAplicacion->monedaColombia4($dato["debito"]));
                                }
                                $idContadorConcepto++;
                                if (strrpos($idContador, "#") !== false) {//BUSQUEDA EN LA CADENA DE TEXTO Y ENCUENTRA
                                    $this->templateWord->setValue("soporteConcepto$idContador", "");
                                    $this->templateWord->setValue("soporteValor$idContador", "");
                                }
                            }
                        } else {
                            $this->templateWord->setValue("soporteConcepto$idContador", $dato["concepto"]);
                        }
                        
                        $this->templateWord->setValue("c$idContador", $dato["concepto"]);
                        $this->templateWord->setValue("soporteCantidad$idContador", $dato["cantidad"]);
                        $this->templateWord->setValue("soporteVencimiento$idContador", $dato["vence"]);
                        $this->templateWord->setValue("soporteCantFisica$idContador", $dato["cantFisica"]);
                        $this->templateWord->setValue("soporteCantSistema$idContador", $dato["cantSistema"]);
                        $this->templateWord->setValue("soporteCantDiferencia$idContador", $dato["cantDiferencia"]);
                        /*$this->templateWord->setValue("soporteCostos$idContador", $dato["centro_costo"]);*/

                        if ($dato["centro_costo"] != "" && $dato["centro_costo"] != "0") {
                            $datosCentroCosto = $this->centralConsulta->datosCentroCosto($dato["centro_costo"]);
                            $this->templateWord->setValue("soporteCostos$idContador", $datosCentroCosto[0]["nombre"]);
                        }

                        if ($dato["ubicacion_inventario"] != "" && $dato["ubicacion_inventario"] != "0") {
                            $datosUbicacionInventario = $this->centralConsulta->datosUbicacionInventario($dato["ubicacion_inventario"]);
                            $this->templateWord->setValue("soporteBodega$idContador", $datosUbicacionInventario[0]["nombre"]);
                        }

                        if ($dato["unidad_manejo"] != "" && $dato["unidad_manejo"] != "0") {
                            $datosUnidadManejo = $this->centralConsulta->datosUnidadManejo($dato["unidad_manejo"]);
                            $this->templateWord->setValue("soporteUMedida$idContador", $datosUnidadManejo[0]["abreviatura"]);
                        }

                        $this->templateWord->setValue("soporteValorUnitario$idContador", $this->formatoAplicacion->monedaColombia4($dato["valor_unitario"]));
                        $this->templateWord->setValue("soportePorcentajeIva$idContador", $dato["porcentaje_iva"]."%");
                        $this->templateWord->setValue("soporteTotalUnidad$idContador", $this->formatoAplicacion->monedaColombia4($dato["total"]));

                        if ($dato["cuenta"] != "") {
                            $datosTercero = $this->centralConsulta->datosTercero($dato["tercero"]);
                        }

                        $this->templateWord->setValue("soporteTercero$idContador", $datosTercero[0]["abreviado"]);

                        $this->templateWord->setValue("soporteDebito$idContador", $this->formatoAplicacion->monedaColombia4($dato["debito"]));
                        if ($cheque == 1) {
                            $this->templateWord->setValue('4', $this->formatoAplicacion->monedaColombia4($dato["debito"]));
                            $this->templateWord->setValue('6', strtoupper($this->formatoAplicacion->numeroLetra(floor($dato["debito"]), 'Pesos Mcte.')));
                        }
                        if ($_REQUEST["completar"] == "_1") {
                            $_REQUEST["soporteDebito"] += $dato["debito"];
                            $_REQUEST["soporteCredito"] += $dato["credito"];
                        } else {
                            if ($_REQUEST["tipoDocumentoContable"] == "24" && $_REQUEST["empresa"] == "3") {
                                if ($dato["base_ret"] == "0") {
                                    $_REQUEST["soporteCredito"] += $dato["credito"];
                                } else {
                                    $_REQUEST["soporteDeduccion"] += $dato["credito"];
                                    $_REQUEST["soporteBase"] += $dato["base_ret"];
                                }
                                $_REQUEST["soporteDebito"] += $dato["debito"];
                            }
                        }
                        if ($dato["debito"] == "0") {
                            $this->templateWord->setValue("v$idContador", $this->formatoAplicacion->monedaColombia4($dato["credito"]));
                        } else {
                            $this->templateWord->setValue("v$idContador", $this->formatoAplicacion->monedaColombia4($dato["debito"]));
                        }
                        $this->templateWord->setValue("soporteCredito$idContador", $this->formatoAplicacion->monedaColombia4($dato["credito"]));

                        $this->templateWord->setValue("fechaVencim", $dato["vence"]);
                        $contador++;
                    }
                    if ($_REQUEST["completar"] == "_1" || ($_REQUEST["tipoDocumentoContable"] == "24" && $_REQUEST["empresa"] == "3")) {
                        $_REQUEST["soporteCredito"] = $this->formatoAplicacion->monedaColombia4($_REQUEST["soporteCredito"]);
                        $_REQUEST["soporteDeduccion"] = $this->formatoAplicacion->monedaColombia4($_REQUEST["soporteDeduccion"]);
                        $_REQUEST["soporteBase"] = $this->formatoAplicacion->monedaColombia4($_REQUEST["soporteBase"]);
                        $_REQUEST["soporteDebito"] = $this->formatoAplicacion->monedaColombia4($_REQUEST["soporteDebito"]);
                    }
                    $this->templateWord->setValue('letra', $this->formatoAplicacion->numeroLetra(floor($_REQUEST["soporteTotal"]), 'Pesos'));
                    $_REQUEST["soporteTotal"] = str_replace(',', '.', str_replace('.', '', $_REQUEST["soporteTotal"]));
                    $_REQUEST["soporteTotalDocumento"] = str_replace(',', '.', str_replace('.', '', $_REQUEST["soporteTotalDocumento"]));
                    $_REQUEST["soporteDebito"] = str_replace(',', '.', str_replace('.', '', $_REQUEST["soporteDebito"]));
                    $_REQUEST["soporteCredito"] = str_replace(',', '.', str_replace('.', '', $_REQUEST["soporteCredito"]));
                    $_REQUEST["soporteDeduccion"] = str_replace(',', '.', str_replace('.', '', $_REQUEST["soporteDeduccion"]));
                    $_REQUEST["soporteBase"] = str_replace(',', '.', str_replace('.', '', $_REQUEST["soporteBase"]));
                    $this->templateWord->setValue('soporteDebito', $this->formatoAplicacion->monedaColombia4($_REQUEST["soporteDebito"]));
                    $this->templateWord->setValue('v', $this->formatoAplicacion->monedaColombia4($_REQUEST["soporteDebito"]));
                    $this->templateWord->setValue('soporteCredito', $this->formatoAplicacion->monedaColombia4($_REQUEST["soporteCredito"]));
                    $this->templateWord->setValue('soporteDeduccion', $this->formatoAplicacion->monedaColombia4($_REQUEST["soporteDeduccion"]));
                    $this->templateWord->setValue('soporteBase', $this->formatoAplicacion->monedaColombia4($_REQUEST["soporteBase"]));
                    $empresa = explode(' ', $_REQUEST['empresaSeleccionada']);
                    $this->templateWord->setValue('nitEmpresa', $empresa[0]);
                    $nombreEmpresa = $empresa[1];
                    for ($i=2; $i < count($empresa); $i++) {
                        $nombreEmpresa .= " ".$empresa[$i];
                    }
                    $this->templateWord->setValue('nombreEmpresa', $nombreEmpresa);
                    //Recibo de caja y comprobante de egreso
                    $this->templateWord->setValue('fecha', '');
                    $this->templateWord->setValue('letra', '');
                    $this->templateWord->setValue('vg1', '');
                    $this->templateWord->setValue('vg2', '');
                    $this->templateWord->setValue('vg3', '');
                    $this->templateWord->setValue('vg4', '');
                    $this->templateWord->setValue('vg5', '');
                    $this->templateWord->setValue('vg6', '');
                    $this->templateWord->setValue('vg7', '');
                    $this->templateWord->setValue('vg8', '');
                    $this->templateWord->setValue('vg9', '');
                    $this->templateWord->setValue('vg10', '');


                    $this->templateWord->setValue('codigoRubro1', '');
                    $this->templateWord->setValue('codigoRubro2', '');
                    $this->templateWord->setValue('codigoRubro3', '');
                    $this->templateWord->setValue('codigoRubro4', '');
                    $this->templateWord->setValue('codigoRubro5', '');
                    $this->templateWord->setValue('codigoRubro6', '');
                    $this->templateWord->setValue('codigoRubro7', '');
                    $this->templateWord->setValue('codigoRubro8', '');
                    $this->templateWord->setValue('codigoRubro9', '');
                    $this->templateWord->setValue('codigoRubro10', '');


                    $this->templateWord->setValue('fe1', '');
                    $this->templateWord->setValue('fe2', '');
                    $this->templateWord->setValue('fe3', '');
                    $this->templateWord->setValue('fe4', '');
                    $this->templateWord->setValue('fe5', '');
                    $this->templateWord->setValue('fe6', '');
                    $this->templateWord->setValue('fe7', '');
                    $this->templateWord->setValue('fe8', '');
                    $this->templateWord->setValue('fe9', '');
                    $this->templateWord->setValue('fe10', '');

                    $this->templateWord->setValue('codigoNombre1', '');
                    $this->templateWord->setValue('codigoNombre2', '');
                    $this->templateWord->setValue('codigoNombre3', '');
                    $this->templateWord->setValue('codigoNombre4', '');
                    $this->templateWord->setValue('codigoNombre5', '');
                    $this->templateWord->setValue('codigoNombre6', '');
                    $this->templateWord->setValue('codigoNombre7', '');
                    $this->templateWord->setValue('codigoNombre8', '');
                    $this->templateWord->setValue('codigoNombre9', '');
                    $this->templateWord->setValue('codigoNombre10', '');

                    $this->templateWord->setValue('nfe1', '');
                    $this->templateWord->setValue('nfe2', '');
                    $this->templateWord->setValue('nfe3', '');
                    $this->templateWord->setValue('nfe4', '');
                    $this->templateWord->setValue('nfe5', '');
                    $this->templateWord->setValue('nfe6', '');
                    $this->templateWord->setValue('nfe7', '');
                    $this->templateWord->setValue('nfe8', '');
                    $this->templateWord->setValue('nfe9', '');
                    $this->templateWord->setValue('nfe10', '');

                    $this->templateWord->setValue('valorRp1', '');
                    $this->templateWord->setValue('valorRp2', '');
                    $this->templateWord->setValue('valorRp3', '');
                    $this->templateWord->setValue('valorRp4', '');
                    $this->templateWord->setValue('valorRp5', '');
                    $this->templateWord->setValue('valorRp6', '');
                    $this->templateWord->setValue('valorRp7', '');
                    $this->templateWord->setValue('valorRp8', '');
                    $this->templateWord->setValue('valorRp9', '');
                    $this->templateWord->setValue('valorRp10', '');

                    $this->templateWord->setValue('soporteCuenta1', '');
                    $this->templateWord->setValue('soporteCuenta2', '');
                    $this->templateWord->setValue('soporteCuenta3', '');
                    $this->templateWord->setValue('soporteCuenta4', '');
                    $this->templateWord->setValue('soporteCuenta5', '');
                    $this->templateWord->setValue('soporteCuenta6', '');
                    $this->templateWord->setValue('soporteCuenta7', '');
                    $this->templateWord->setValue('soporteCuenta8', '');
                    $this->templateWord->setValue('soporteCuenta9', '');
                    $this->templateWord->setValue('soporteCuenta10', '');
                    $this->templateWord->setValue('soporteCuenta11', '');
                    $this->templateWord->setValue('soporteCuenta12', '');
                    $this->templateWord->setValue('soporteCuenta13', '');
                    $this->templateWord->setValue('soporteCuenta14', '');
                    $this->templateWord->setValue('soporteCuenta15', '');
                    $this->templateWord->setValue('soporteCuenta16', '');
                    $this->templateWord->setValue('soporteCuenta17', '');
                    $this->templateWord->setValue('soporteCuenta18', '');
                    $this->templateWord->setValue('soporteCuenta19', '');
                    $this->templateWord->setValue('soporteCuenta20', '');
                    $this->templateWord->setValue('soporteCuenta21', '');
                    $this->templateWord->setValue('soporteCuenta22', '');
                    $this->templateWord->setValue('soporteCuenta23', '');
                    $this->templateWord->setValue('soporteCuenta24', '');
                    $this->templateWord->setValue('soporteCuenta25', '');
                    $this->templateWord->setValue('soporteCuenta26', '');
                    $this->templateWord->setValue('soporteCuenta27', '');
                    $this->templateWord->setValue('soporteCuenta28', '');
                    $this->templateWord->setValue('soporteCuenta29', '');
                    $this->templateWord->setValue('soporteCuenta30', '');
                    $this->templateWord->setValue('soporteCuenta31', '');
                    $this->templateWord->setValue('soporteCuenta32', '');
                    $this->templateWord->setValue('soporteCuenta33', '');
                    $this->templateWord->setValue('soporteCuenta34', '');
                    $this->templateWord->setValue('soporteCuenta35', '');
                    $this->templateWord->setValue('soporteCuenta36', '');
                    $this->templateWord->setValue('soporteCuenta37', '');
                    $this->templateWord->setValue('soporteCuenta38', '');
                    $this->templateWord->setValue('soporteCuenta39', '');
                    $this->templateWord->setValue('soporteCuenta40', '');
                    $this->templateWord->setValue('soporteCuenta41', '');
                    $this->templateWord->setValue('soporteCuenta42', '');
                    $this->templateWord->setValue('soporteCuenta43', '');
                    $this->templateWord->setValue('soporteCuenta44', '');
                    $this->templateWord->setValue('soporteCuenta45', '');
                    $this->templateWord->setValue('soporteCuenta46', '');
                    $this->templateWord->setValue('soporteCuenta47', '');
                    $this->templateWord->setValue('soporteCuenta48', '');
                    $this->templateWord->setValue('soporteCuenta49', '');
                    $this->templateWord->setValue('soporteCuenta50', '');
                    $this->templateWord->setValue('soporteCuenta51', '');
                    $this->templateWord->setValue('soporteCuenta52', '');
                    $this->templateWord->setValue('soporteCuenta53', '');
                    $this->templateWord->setValue('soporteCuenta54', '');
                    $this->templateWord->setValue('soporteCuenta55', '');
                    $this->templateWord->setValue('soporteCuenta56', '');
                    $this->templateWord->setValue('soporteCuenta57', '');
                    $this->templateWord->setValue('soporteCuenta58', '');
                    $this->templateWord->setValue('soporteCuenta59', '');
                    $this->templateWord->setValue('soporteCuenta60', '');
                    $this->templateWord->setValue('soporteCuenta61', '');
                    $this->templateWord->setValue('soporteCuenta62', '');
                    $this->templateWord->setValue('soporteCuenta63', '');
                    $this->templateWord->setValue('soporteCuenta64', '');
                    $this->templateWord->setValue('soporteCuenta65', '');
                    $this->templateWord->setValue('soporteCuenta66', '');
                    $this->templateWord->setValue('soporteCuenta67', '');
                    $this->templateWord->setValue('soporteCuenta68', '');
                    $this->templateWord->setValue('soporteCuenta69', '');
                    $this->templateWord->setValue('soporteCuenta70', '');
                    $this->templateWord->setValue('soporteCuenta71', '');
                    $this->templateWord->setValue('soporteCuenta72', '');
                    $this->templateWord->setValue('soporteCuenta73', '');
                    $this->templateWord->setValue('soporteCuenta74', '');
                    $this->templateWord->setValue('soporteCuenta75', '');
                    $this->templateWord->setValue('soporteCuenta76', '');
                    $this->templateWord->setValue('soporteCuenta77', '');
                    $this->templateWord->setValue('soporteCuenta78', '');
                    $this->templateWord->setValue('soporteCuenta79', '');
                    $this->templateWord->setValue('soporteCuenta80', '');
                    $this->templateWord->setValue('soporteCuenta81', '');
                    $this->templateWord->setValue('soporteCuenta82', '');
                    $this->templateWord->setValue('soporteCuenta83', '');
                    $this->templateWord->setValue('soporteCuenta84', '');
                    $this->templateWord->setValue('soporteCuenta85', '');
                    $this->templateWord->setValue('soporteCuenta86', '');
                    $this->templateWord->setValue('soporteCuenta87', '');
                    $this->templateWord->setValue('soporteCuenta88', '');
                    $this->templateWord->setValue('soporteCuenta89', '');
                    $this->templateWord->setValue('soporteCuenta90', '');
                    $this->templateWord->setValue('soporteCuenta91', '');
                    $this->templateWord->setValue('soporteCuenta92', '');
                    $this->templateWord->setValue('soporteCuenta93', '');
                    $this->templateWord->setValue('soporteCuenta94', '');
                    $this->templateWord->setValue('soporteCuenta95', '');
                    $this->templateWord->setValue('soporteCuenta96', '');
                    $this->templateWord->setValue('soporteCuenta97', '');
                    $this->templateWord->setValue('soporteCuenta98', '');
                    $this->templateWord->setValue('soporteCuenta99', '');
                    $this->templateWord->setValue('soporteCuenta100', '');
                    $this->templateWord->setValue('soporteBase1', '');
                    $this->templateWord->setValue('soporteBase2', '');
                    $this->templateWord->setValue('soporteBase3', '');
                    $this->templateWord->setValue('soporteBase4', '');
                    $this->templateWord->setValue('soporteBase5', '');
                    $this->templateWord->setValue('soporteBase6', '');
                    $this->templateWord->setValue('soporteBase7', '');
                    $this->templateWord->setValue('soporteBase8', '');
                    $this->templateWord->setValue('soporteBase9', '');
                    $this->templateWord->setValue('soporteBase10', '');
                    $this->templateWord->setValue('soporteBase11', '');
                    $this->templateWord->setValue('soporteBase12', '');
                    $this->templateWord->setValue('soporteBase13', '');
                    $this->templateWord->setValue('soporteBase14', '');
                    $this->templateWord->setValue('soporteBase15', '');
                    $this->templateWord->setValue('soporteBase16', '');
                    $this->templateWord->setValue('soporteBase17', '');
                    $this->templateWord->setValue('soporteBase18', '');
                    $this->templateWord->setValue('soporteBase19', '');
                    $this->templateWord->setValue('soporteBase20', '');
                    $this->templateWord->setValue('soporteBase21', '');
                    $this->templateWord->setValue('soporteBase22', '');
                    $this->templateWord->setValue('soporteBase23', '');
                    $this->templateWord->setValue('soporteBase24', '');
                    $this->templateWord->setValue('soporteBase25', '');
                    $this->templateWord->setValue('soporteBase26', '');
                    $this->templateWord->setValue('soporteBase27', '');
                    $this->templateWord->setValue('soporteBase28', '');
                    $this->templateWord->setValue('soporteBase29', '');
                    $this->templateWord->setValue('soporteBase30', '');
                    $this->templateWord->setValue('soporteBase31', '');
                    $this->templateWord->setValue('soporteBase32', '');
                    $this->templateWord->setValue('soporteBase33', '');
                    $this->templateWord->setValue('soporteBase34', '');
                    $this->templateWord->setValue('soporteBase35', '');
                    $this->templateWord->setValue('soporteBase36', '');
                    $this->templateWord->setValue('soporteBase37', '');
                    $this->templateWord->setValue('soporteBase38', '');
                    $this->templateWord->setValue('soporteBase39', '');
                    $this->templateWord->setValue('soporteBase40', '');
                    $this->templateWord->setValue('soporteBase41', '');
                    $this->templateWord->setValue('soporteBase42', '');
                    $this->templateWord->setValue('soporteBase43', '');
                    $this->templateWord->setValue('soporteBase44', '');
                    $this->templateWord->setValue('soporteBase45', '');
                    $this->templateWord->setValue('soporteBase46', '');
                    $this->templateWord->setValue('soporteBase47', '');
                    $this->templateWord->setValue('soporteBase48', '');
                    $this->templateWord->setValue('soporteBase49', '');
                    $this->templateWord->setValue('soporteBase50', '');
                    $this->templateWord->setValue('soporteBase51', '');
                    $this->templateWord->setValue('soporteBase52', '');
                    $this->templateWord->setValue('soporteBase53', '');
                    $this->templateWord->setValue('soporteBase54', '');
                    $this->templateWord->setValue('soporteBase55', '');
                    $this->templateWord->setValue('soporteBase56', '');
                    $this->templateWord->setValue('soporteBase57', '');
                    $this->templateWord->setValue('soporteBase58', '');
                    $this->templateWord->setValue('soporteBase59', '');
                    $this->templateWord->setValue('soporteBase60', '');
                    $this->templateWord->setValue('soporteBase61', '');
                    $this->templateWord->setValue('soporteBase62', '');
                    $this->templateWord->setValue('soporteBase63', '');
                    $this->templateWord->setValue('soporteBase64', '');
                    $this->templateWord->setValue('soporteBase65', '');
                    $this->templateWord->setValue('soporteBase66', '');
                    $this->templateWord->setValue('soporteBase67', '');
                    $this->templateWord->setValue('soporteBase68', '');
                    $this->templateWord->setValue('soporteBase69', '');
                    $this->templateWord->setValue('soporteBase70', '');
                    $this->templateWord->setValue('soporteBase71', '');
                    $this->templateWord->setValue('soporteBase72', '');
                    $this->templateWord->setValue('soporteBase73', '');
                    $this->templateWord->setValue('soporteBase74', '');
                    $this->templateWord->setValue('soporteBase75', '');
                    $this->templateWord->setValue('soporteBase76', '');
                    $this->templateWord->setValue('soporteBase77', '');
                    $this->templateWord->setValue('soporteBase78', '');
                    $this->templateWord->setValue('soporteBase79', '');
                    $this->templateWord->setValue('soporteBase80', '');
                    $this->templateWord->setValue('soporteBase81', '');
                    $this->templateWord->setValue('soporteBase82', '');
                    $this->templateWord->setValue('soporteBase83', '');
                    $this->templateWord->setValue('soporteBase84', '');
                    $this->templateWord->setValue('soporteBase85', '');
                    $this->templateWord->setValue('soporteBase86', '');
                    $this->templateWord->setValue('soporteBase87', '');
                    $this->templateWord->setValue('soporteBase88', '');
                    $this->templateWord->setValue('soporteBase89', '');
                    $this->templateWord->setValue('soporteBase90', '');
                    $this->templateWord->setValue('soporteBase91', '');
                    $this->templateWord->setValue('soporteBase92', '');
                    $this->templateWord->setValue('soporteBase93', '');
                    $this->templateWord->setValue('soporteBase94', '');
                    $this->templateWord->setValue('soporteBase95', '');
                    $this->templateWord->setValue('soporteBase96', '');
                    $this->templateWord->setValue('soporteBase97', '');
                    $this->templateWord->setValue('soporteBase98', '');
                    $this->templateWord->setValue('soporteBase99', '');
                    $this->templateWord->setValue('soporteBase100', '');
                    $this->templateWord->setValue('soporteNombreCuenta1', '');
                    $this->templateWord->setValue('soporteNombreCuenta2', '');
                    $this->templateWord->setValue('soporteNombreCuenta3', '');
                    $this->templateWord->setValue('soporteNombreCuenta4', '');
                    $this->templateWord->setValue('soporteNombreCuenta5', '');
                    $this->templateWord->setValue('soporteNombreCuenta6', '');
                    $this->templateWord->setValue('soporteNombreCuenta7', '');
                    $this->templateWord->setValue('soporteNombreCuenta8', '');
                    $this->templateWord->setValue('soporteNombreCuenta9', '');
                    $this->templateWord->setValue('soporteNombreCuenta10', '');
                    $this->templateWord->setValue('soporteNombreCuenta11', '');
                    $this->templateWord->setValue('soporteNombreCuenta12', '');
                    $this->templateWord->setValue('soporteNombreCuenta13', '');
                    $this->templateWord->setValue('soporteNombreCuenta14', '');
                    $this->templateWord->setValue('soporteNombreCuenta15', '');
                    $this->templateWord->setValue('soporteNombreCuenta16', '');
                    $this->templateWord->setValue('soporteNombreCuenta17', '');
                    $this->templateWord->setValue('soporteNombreCuenta18', '');
                    $this->templateWord->setValue('soporteNombreCuenta19', '');
                    $this->templateWord->setValue('soporteNombreCuenta20', '');
                    $this->templateWord->setValue('soporteNombreCuenta21', '');
                    $this->templateWord->setValue('soporteNombreCuenta22', '');
                    $this->templateWord->setValue('soporteNombreCuenta23', '');
                    $this->templateWord->setValue('soporteNombreCuenta24', '');
                    $this->templateWord->setValue('soporteNombreCuenta25', '');
                    $this->templateWord->setValue('soporteNombreCuenta26', '');
                    $this->templateWord->setValue('soporteNombreCuenta27', '');
                    $this->templateWord->setValue('soporteNombreCuenta28', '');
                    $this->templateWord->setValue('soporteNombreCuenta29', '');
                    $this->templateWord->setValue('soporteNombreCuenta30', '');
                    $this->templateWord->setValue('soporteNombreCuenta31', '');
                    $this->templateWord->setValue('soporteNombreCuenta32', '');
                    $this->templateWord->setValue('soporteNombreCuenta33', '');
                    $this->templateWord->setValue('soporteNombreCuenta34', '');
                    $this->templateWord->setValue('soporteNombreCuenta35', '');
                    $this->templateWord->setValue('soporteNombreCuenta36', '');
                    $this->templateWord->setValue('soporteNombreCuenta37', '');
                    $this->templateWord->setValue('soporteNombreCuenta38', '');
                    $this->templateWord->setValue('soporteNombreCuenta39', '');
                    $this->templateWord->setValue('soporteNombreCuenta40', '');
                    $this->templateWord->setValue('soporteNombreCuenta41', '');
                    $this->templateWord->setValue('soporteNombreCuenta42', '');
                    $this->templateWord->setValue('soporteNombreCuenta43', '');
                    $this->templateWord->setValue('soporteNombreCuenta44', '');
                    $this->templateWord->setValue('soporteNombreCuenta45', '');
                    $this->templateWord->setValue('soporteNombreCuenta46', '');
                    $this->templateWord->setValue('soporteNombreCuenta47', '');
                    $this->templateWord->setValue('soporteNombreCuenta48', '');
                    $this->templateWord->setValue('soporteNombreCuenta49', '');
                    $this->templateWord->setValue('soporteNombreCuenta50', '');
                    $this->templateWord->setValue('soporteNombreCuenta51', '');
                    $this->templateWord->setValue('soporteNombreCuenta52', '');
                    $this->templateWord->setValue('soporteNombreCuenta53', '');
                    $this->templateWord->setValue('soporteNombreCuenta54', '');
                    $this->templateWord->setValue('soporteNombreCuenta55', '');
                    $this->templateWord->setValue('soporteNombreCuenta56', '');
                    $this->templateWord->setValue('soporteNombreCuenta57', '');
                    $this->templateWord->setValue('soporteNombreCuenta58', '');
                    $this->templateWord->setValue('soporteNombreCuenta59', '');
                    $this->templateWord->setValue('soporteNombreCuenta60', '');
                    $this->templateWord->setValue('soporteNombreCuenta61', '');
                    $this->templateWord->setValue('soporteNombreCuenta62', '');
                    $this->templateWord->setValue('soporteNombreCuenta63', '');
                    $this->templateWord->setValue('soporteNombreCuenta64', '');
                    $this->templateWord->setValue('soporteNombreCuenta65', '');
                    $this->templateWord->setValue('soporteNombreCuenta66', '');
                    $this->templateWord->setValue('soporteNombreCuenta67', '');
                    $this->templateWord->setValue('soporteNombreCuenta68', '');
                    $this->templateWord->setValue('soporteNombreCuenta69', '');
                    $this->templateWord->setValue('soporteNombreCuenta70', '');
                    $this->templateWord->setValue('soporteNombreCuenta71', '');
                    $this->templateWord->setValue('soporteNombreCuenta72', '');
                    $this->templateWord->setValue('soporteNombreCuenta73', '');
                    $this->templateWord->setValue('soporteNombreCuenta74', '');
                    $this->templateWord->setValue('soporteNombreCuenta75', '');
                    $this->templateWord->setValue('soporteNombreCuenta76', '');
                    $this->templateWord->setValue('soporteNombreCuenta77', '');
                    $this->templateWord->setValue('soporteNombreCuenta78', '');
                    $this->templateWord->setValue('soporteNombreCuenta79', '');
                    $this->templateWord->setValue('soporteNombreCuenta80', '');
                    $this->templateWord->setValue('soporteNombreCuenta81', '');
                    $this->templateWord->setValue('soporteNombreCuenta82', '');
                    $this->templateWord->setValue('soporteNombreCuenta83', '');
                    $this->templateWord->setValue('soporteNombreCuenta84', '');
                    $this->templateWord->setValue('soporteNombreCuenta85', '');
                    $this->templateWord->setValue('soporteNombreCuenta86', '');
                    $this->templateWord->setValue('soporteNombreCuenta87', '');
                    $this->templateWord->setValue('soporteNombreCuenta88', '');
                    $this->templateWord->setValue('soporteNombreCuenta89', '');
                    $this->templateWord->setValue('soporteNombreCuenta90', '');
                    $this->templateWord->setValue('soporteNombreCuenta91', '');
                    $this->templateWord->setValue('soporteNombreCuenta92', '');
                    $this->templateWord->setValue('soporteNombreCuenta93', '');
                    $this->templateWord->setValue('soporteNombreCuenta94', '');
                    $this->templateWord->setValue('soporteNombreCuenta95', '');
                    $this->templateWord->setValue('soporteNombreCuenta96', '');
                    $this->templateWord->setValue('soporteNombreCuenta97', '');
                    $this->templateWord->setValue('soporteNombreCuenta98', '');
                    $this->templateWord->setValue('soporteNombreCuenta99', '');
                    $this->templateWord->setValue('soporteNombreCuenta100', '');

                    $this->templateWord->setValue('soporteConcepto1', '');
                    $this->templateWord->setValue('soporteConcepto2', '');
                    $this->templateWord->setValue('soporteConcepto3', '');
                    $this->templateWord->setValue('soporteConcepto4', '');
                    $this->templateWord->setValue('soporteConcepto5', '');
                    $this->templateWord->setValue('soporteConcepto6', '');
                    $this->templateWord->setValue('soporteConcepto7', '');
                    $this->templateWord->setValue('soporteConcepto8', '');
                    $this->templateWord->setValue('soporteConcepto9', '');
                    $this->templateWord->setValue('soporteConcepto10', '');
                    $this->templateWord->setValue('soporteConcepto11', '');
                    $this->templateWord->setValue('soporteConcepto12', '');
                    $this->templateWord->setValue('soporteConcepto13', '');
                    $this->templateWord->setValue('soporteConcepto14', '');
                    $this->templateWord->setValue('soporteConcepto15', '');
                    $this->templateWord->setValue('soporteConcepto16', '');
                    $this->templateWord->setValue('soporteConcepto17', '');
                    $this->templateWord->setValue('soporteConcepto18', '');
                    $this->templateWord->setValue('soporteConcepto19', '');
                    $this->templateWord->setValue('soporteConcepto20', '');
                    $this->templateWord->setValue('soporteConcepto21', '');
                    $this->templateWord->setValue('soporteConcepto22', '');
                    $this->templateWord->setValue('soporteConcepto23', '');
                    $this->templateWord->setValue('soporteConcepto24', '');
                    $this->templateWord->setValue('soporteConcepto25', '');
                    $this->templateWord->setValue('soporteConcepto26', '');
                    $this->templateWord->setValue('soporteConcepto27', '');
                    $this->templateWord->setValue('soporteConcepto28', '');
                    $this->templateWord->setValue('soporteConcepto29', '');
                    $this->templateWord->setValue('soporteConcepto30', '');
                    $this->templateWord->setValue('soporteConcepto31', '');
                    $this->templateWord->setValue('soporteConcepto32', '');
                    $this->templateWord->setValue('soporteConcepto33', '');
                    $this->templateWord->setValue('soporteConcepto34', '');
                    $this->templateWord->setValue('soporteConcepto35', '');
                    $this->templateWord->setValue('soporteConcepto36', '');
                    $this->templateWord->setValue('soporteConcepto37', '');
                    $this->templateWord->setValue('soporteConcepto38', '');
                    $this->templateWord->setValue('soporteConcepto39', '');
                    $this->templateWord->setValue('soporteConcepto40', '');
                    $this->templateWord->setValue('soporteConcepto41', '');
                    $this->templateWord->setValue('soporteConcepto42', '');
                    $this->templateWord->setValue('soporteConcepto43', '');
                    $this->templateWord->setValue('soporteConcepto44', '');
                    $this->templateWord->setValue('soporteConcepto45', '');
                    $this->templateWord->setValue('soporteConcepto46', '');
                    $this->templateWord->setValue('soporteConcepto47', '');
                    $this->templateWord->setValue('soporteConcepto48', '');
                    $this->templateWord->setValue('soporteConcepto49', '');
                    $this->templateWord->setValue('soporteConcepto50', '');
                    $this->templateWord->setValue('soporteConcepto51', '');
                    $this->templateWord->setValue('soporteConcepto52', '');
                    $this->templateWord->setValue('soporteConcepto53', '');
                    $this->templateWord->setValue('soporteConcepto54', '');
                    $this->templateWord->setValue('soporteConcepto55', '');
                    $this->templateWord->setValue('soporteConcepto56', '');
                    $this->templateWord->setValue('soporteConcepto57', '');
                    $this->templateWord->setValue('soporteConcepto58', '');
                    $this->templateWord->setValue('soporteConcepto59', '');
                    $this->templateWord->setValue('soporteConcepto60', '');
                    $this->templateWord->setValue('soporteConcepto61', '');
                    $this->templateWord->setValue('soporteConcepto62', '');
                    $this->templateWord->setValue('soporteConcepto63', '');
                    $this->templateWord->setValue('soporteConcepto64', '');
                    $this->templateWord->setValue('soporteConcepto65', '');
                    $this->templateWord->setValue('soporteConcepto66', '');
                    $this->templateWord->setValue('soporteConcepto67', '');
                    $this->templateWord->setValue('soporteConcepto68', '');
                    $this->templateWord->setValue('soporteConcepto69', '');
                    $this->templateWord->setValue('soporteConcepto70', '');
                    $this->templateWord->setValue('soporteConcepto71', '');
                    $this->templateWord->setValue('soporteConcepto72', '');
                    $this->templateWord->setValue('soporteConcepto73', '');
                    $this->templateWord->setValue('soporteConcepto74', '');
                    $this->templateWord->setValue('soporteConcepto75', '');
                    $this->templateWord->setValue('soporteConcepto76', '');
                    $this->templateWord->setValue('soporteConcepto77', '');
                    $this->templateWord->setValue('soporteConcepto78', '');
                    $this->templateWord->setValue('soporteConcepto79', '');
                    $this->templateWord->setValue('soporteConcepto80', '');
                    $this->templateWord->setValue('soporteConcepto81', '');
                    $this->templateWord->setValue('soporteConcepto82', '');
                    $this->templateWord->setValue('soporteConcepto83', '');
                    $this->templateWord->setValue('soporteConcepto84', '');
                    $this->templateWord->setValue('soporteConcepto85', '');
                    $this->templateWord->setValue('soporteConcepto86', '');
                    $this->templateWord->setValue('soporteConcepto87', '');
                    $this->templateWord->setValue('soporteConcepto88', '');
                    $this->templateWord->setValue('soporteConcepto89', '');
                    $this->templateWord->setValue('soporteConcepto90', '');
                    $this->templateWord->setValue('soporteConcepto91', '');
                    $this->templateWord->setValue('soporteConcepto92', '');
                    $this->templateWord->setValue('soporteConcepto93', '');
                    $this->templateWord->setValue('soporteConcepto94', '');
                    $this->templateWord->setValue('soporteConcepto95', '');
                    $this->templateWord->setValue('soporteConcepto96', '');
                    $this->templateWord->setValue('soporteConcepto97', '');
                    $this->templateWord->setValue('soporteConcepto98', '');
                    $this->templateWord->setValue('soporteConcepto99', '');
                    $this->templateWord->setValue('soporteConcepto100', '');

                    $this->templateWord->setValue('soporteValor1', '');
                    $this->templateWord->setValue('soporteValor2', '');
                    $this->templateWord->setValue('soporteValor3', '');
                    $this->templateWord->setValue('soporteValor4', '');
                    $this->templateWord->setValue('soporteValor5', '');
                    $this->templateWord->setValue('soporteValor6', '');
                    $this->templateWord->setValue('soporteValor7', '');
                    $this->templateWord->setValue('soporteValor8', '');
                    $this->templateWord->setValue('soporteValor9', '');
                    $this->templateWord->setValue('soporteValor10', '');
                    $this->templateWord->setValue('soporteValor11', '');
                    $this->templateWord->setValue('soporteValor12', '');
                    $this->templateWord->setValue('soporteValor13', '');
                    $this->templateWord->setValue('soporteValor14', '');
                    $this->templateWord->setValue('soporteValor15', '');
                    $this->templateWord->setValue('soporteValor16', '');
                    $this->templateWord->setValue('soporteValor17', '');
                    $this->templateWord->setValue('soporteValor18', '');
                    $this->templateWord->setValue('soporteValor19', '');
                    $this->templateWord->setValue('soporteValor20', '');
                    $this->templateWord->setValue('soporteValor21', '');
                    $this->templateWord->setValue('soporteValor22', '');
                    $this->templateWord->setValue('soporteValor23', '');
                    $this->templateWord->setValue('soporteValor24', '');
                    $this->templateWord->setValue('soporteValor25', '');
                    $this->templateWord->setValue('soporteValor26', '');
                    $this->templateWord->setValue('soporteValor27', '');
                    $this->templateWord->setValue('soporteValor28', '');
                    $this->templateWord->setValue('soporteValor29', '');
                    $this->templateWord->setValue('soporteValor30', '');
                    $this->templateWord->setValue('soporteValor31', '');
                    $this->templateWord->setValue('soporteValor32', '');
                    $this->templateWord->setValue('soporteValor33', '');
                    $this->templateWord->setValue('soporteValor34', '');
                    $this->templateWord->setValue('soporteValor35', '');
                    $this->templateWord->setValue('soporteValor36', '');
                    $this->templateWord->setValue('soporteValor37', '');
                    $this->templateWord->setValue('soporteValor38', '');
                    $this->templateWord->setValue('soporteValor39', '');
                    $this->templateWord->setValue('soporteValor40', '');
                    $this->templateWord->setValue('soporteValor41', '');
                    $this->templateWord->setValue('soporteValor42', '');
                    $this->templateWord->setValue('soporteValor43', '');
                    $this->templateWord->setValue('soporteValor44', '');
                    $this->templateWord->setValue('soporteValor45', '');
                    $this->templateWord->setValue('soporteValor46', '');
                    $this->templateWord->setValue('soporteValor47', '');
                    $this->templateWord->setValue('soporteValor48', '');
                    $this->templateWord->setValue('soporteValor49', '');
                    $this->templateWord->setValue('soporteValor50', '');
                    $this->templateWord->setValue('soporteValor51', '');
                    $this->templateWord->setValue('soporteValor52', '');
                    $this->templateWord->setValue('soporteValor53', '');
                    $this->templateWord->setValue('soporteValor54', '');
                    $this->templateWord->setValue('soporteValor55', '');
                    $this->templateWord->setValue('soporteValor56', '');
                    $this->templateWord->setValue('soporteValor57', '');
                    $this->templateWord->setValue('soporteValor58', '');
                    $this->templateWord->setValue('soporteValor59', '');
                    $this->templateWord->setValue('soporteValor60', '');
                    $this->templateWord->setValue('soporteValor61', '');
                    $this->templateWord->setValue('soporteValor62', '');
                    $this->templateWord->setValue('soporteValor63', '');
                    $this->templateWord->setValue('soporteValor64', '');
                    $this->templateWord->setValue('soporteValor65', '');
                    $this->templateWord->setValue('soporteValor66', '');
                    $this->templateWord->setValue('soporteValor67', '');
                    $this->templateWord->setValue('soporteValor68', '');
                    $this->templateWord->setValue('soporteValor69', '');
                    $this->templateWord->setValue('soporteValor70', '');
                    $this->templateWord->setValue('soporteValor71', '');
                    $this->templateWord->setValue('soporteValor72', '');
                    $this->templateWord->setValue('soporteValor73', '');
                    $this->templateWord->setValue('soporteValor74', '');
                    $this->templateWord->setValue('soporteValor75', '');
                    $this->templateWord->setValue('soporteValor76', '');
                    $this->templateWord->setValue('soporteValor77', '');
                    $this->templateWord->setValue('soporteValor78', '');
                    $this->templateWord->setValue('soporteValor79', '');
                    $this->templateWord->setValue('soporteValor80', '');
                    $this->templateWord->setValue('soporteValor81', '');
                    $this->templateWord->setValue('soporteValor82', '');
                    $this->templateWord->setValue('soporteValor83', '');
                    $this->templateWord->setValue('soporteValor84', '');
                    $this->templateWord->setValue('soporteValor85', '');
                    $this->templateWord->setValue('soporteValor86', '');
                    $this->templateWord->setValue('soporteValor87', '');
                    $this->templateWord->setValue('soporteValor88', '');
                    $this->templateWord->setValue('soporteValor89', '');
                    $this->templateWord->setValue('soporteValor90', '');
                    $this->templateWord->setValue('soporteValor91', '');
                    $this->templateWord->setValue('soporteValor92', '');
                    $this->templateWord->setValue('soporteValor93', '');
                    $this->templateWord->setValue('soporteValor94', '');
                    $this->templateWord->setValue('soporteValor95', '');
                    $this->templateWord->setValue('soporteValor96', '');
                    $this->templateWord->setValue('soporteValor97', '');
                    $this->templateWord->setValue('soporteValor98', '');
                    $this->templateWord->setValue('soporteValor99', '');
                    $this->templateWord->setValue('soporteValor100', '');

                    $this->templateWord->setValue('soporteTercero1', '');
                    $this->templateWord->setValue('soporteTercero2', '');
                    $this->templateWord->setValue('soporteTercero3', '');
                    $this->templateWord->setValue('soporteTercero4', '');
                    $this->templateWord->setValue('soporteTercero5', '');
                    $this->templateWord->setValue('soporteTercero6', '');
                    $this->templateWord->setValue('soporteTercero7', '');
                    $this->templateWord->setValue('soporteTercero8', '');
                    $this->templateWord->setValue('soporteTercero9', '');
                    $this->templateWord->setValue('soporteTercero10', '');
                    $this->templateWord->setValue('soporteTercero11', '');
                    $this->templateWord->setValue('soporteTercero12', '');
                    $this->templateWord->setValue('soporteTercero13', '');
                    $this->templateWord->setValue('soporteTercero14', '');
                    $this->templateWord->setValue('soporteTercero15', '');
                    $this->templateWord->setValue('soporteTercero16', '');
                    $this->templateWord->setValue('soporteTercero17', '');
                    $this->templateWord->setValue('soporteTercero18', '');
                    $this->templateWord->setValue('soporteTercero19', '');
                    $this->templateWord->setValue('soporteTercero20', '');
                    $this->templateWord->setValue('soporteTercero21', '');
                    $this->templateWord->setValue('soporteTercero22', '');
                    $this->templateWord->setValue('soporteTercero23', '');
                    $this->templateWord->setValue('soporteTercero24', '');
                    $this->templateWord->setValue('soporteTercero25', '');
                    $this->templateWord->setValue('soporteTercero26', '');
                    $this->templateWord->setValue('soporteTercero27', '');
                    $this->templateWord->setValue('soporteTercero28', '');
                    $this->templateWord->setValue('soporteTercero29', '');
                    $this->templateWord->setValue('soporteTercero30', '');
                    $this->templateWord->setValue('soporteTercero31', '');
                    $this->templateWord->setValue('soporteTercero32', '');
                    $this->templateWord->setValue('soporteTercero33', '');
                    $this->templateWord->setValue('soporteTercero34', '');
                    $this->templateWord->setValue('soporteTercero35', '');
                    $this->templateWord->setValue('soporteTercero36', '');
                    $this->templateWord->setValue('soporteTercero37', '');
                    $this->templateWord->setValue('soporteTercero38', '');
                    $this->templateWord->setValue('soporteTercero39', '');
                    $this->templateWord->setValue('soporteTercero40', '');
                    $this->templateWord->setValue('soporteTercero41', '');
                    $this->templateWord->setValue('soporteTercero42', '');
                    $this->templateWord->setValue('soporteTercero43', '');
                    $this->templateWord->setValue('soporteTercero44', '');
                    $this->templateWord->setValue('soporteTercero45', '');
                    $this->templateWord->setValue('soporteTercero46', '');
                    $this->templateWord->setValue('soporteTercero47', '');
                    $this->templateWord->setValue('soporteTercero48', '');
                    $this->templateWord->setValue('soporteTercero49', '');
                    $this->templateWord->setValue('soporteTercero50', '');
                    $this->templateWord->setValue('soporteTercero51', '');
                    $this->templateWord->setValue('soporteTercero52', '');
                    $this->templateWord->setValue('soporteTercero53', '');
                    $this->templateWord->setValue('soporteTercero54', '');
                    $this->templateWord->setValue('soporteTercero55', '');
                    $this->templateWord->setValue('soporteTercero56', '');
                    $this->templateWord->setValue('soporteTercero57', '');
                    $this->templateWord->setValue('soporteTercero58', '');
                    $this->templateWord->setValue('soporteTercero59', '');
                    $this->templateWord->setValue('soporteTercero60', '');
                    $this->templateWord->setValue('soporteTercero61', '');
                    $this->templateWord->setValue('soporteTercero62', '');
                    $this->templateWord->setValue('soporteTercero63', '');
                    $this->templateWord->setValue('soporteTercero64', '');
                    $this->templateWord->setValue('soporteTercero65', '');
                    $this->templateWord->setValue('soporteTercero66', '');
                    $this->templateWord->setValue('soporteTercero67', '');
                    $this->templateWord->setValue('soporteTercero68', '');
                    $this->templateWord->setValue('soporteTercero69', '');
                    $this->templateWord->setValue('soporteTercero70', '');
                    $this->templateWord->setValue('soporteTercero71', '');
                    $this->templateWord->setValue('soporteTercero72', '');
                    $this->templateWord->setValue('soporteTercero73', '');
                    $this->templateWord->setValue('soporteTercero74', '');
                    $this->templateWord->setValue('soporteTercero75', '');
                    $this->templateWord->setValue('soporteTercero76', '');
                    $this->templateWord->setValue('soporteTercero77', '');
                    $this->templateWord->setValue('soporteTercero78', '');
                    $this->templateWord->setValue('soporteTercero79', '');
                    $this->templateWord->setValue('soporteTercero80', '');
                    $this->templateWord->setValue('soporteTercero81', '');
                    $this->templateWord->setValue('soporteTercero82', '');
                    $this->templateWord->setValue('soporteTercero83', '');
                    $this->templateWord->setValue('soporteTercero84', '');
                    $this->templateWord->setValue('soporteTercero85', '');
                    $this->templateWord->setValue('soporteTercero86', '');
                    $this->templateWord->setValue('soporteTercero87', '');
                    $this->templateWord->setValue('soporteTercero88', '');
                    $this->templateWord->setValue('soporteTercero89', '');
                    $this->templateWord->setValue('soporteTercero90', '');
                    $this->templateWord->setValue('soporteTercero91', '');
                    $this->templateWord->setValue('soporteTercero92', '');
                    $this->templateWord->setValue('soporteTercero93', '');
                    $this->templateWord->setValue('soporteTercero94', '');
                    $this->templateWord->setValue('soporteTercero95', '');
                    $this->templateWord->setValue('soporteTercero96', '');
                    $this->templateWord->setValue('soporteTercero97', '');
                    $this->templateWord->setValue('soporteTercero98', '');
                    $this->templateWord->setValue('soporteTercero99', '');
                    $this->templateWord->setValue('soporteTercero100', '');
                    $this->templateWord->setValue('soporteTercero1', '');
                    $this->templateWord->setValue('soporteTercero2', '');
                    $this->templateWord->setValue('soporteTercero3', '');
                    $this->templateWord->setValue('soporteTercero4', '');
                    $this->templateWord->setValue('soporteTercero5', '');
                    $this->templateWord->setValue('soporteTercero6', '');
                    $this->templateWord->setValue('soporteTercero7', '');
                    $this->templateWord->setValue('soporteTercero8', '');
                    $this->templateWord->setValue('soporteTercero9', '');
                    $this->templateWord->setValue('soporteTercero10', '');
                    $this->templateWord->setValue('soporteTercero1', '');
                    $this->templateWord->setValue('soporteTercero2', '');
                    $this->templateWord->setValue('soporteTercero3', '');
                    $this->templateWord->setValue('soporteTercero4', '');
                    $this->templateWord->setValue('soporteTercero5', '');
                    $this->templateWord->setValue('soporteTercero6', '');
                    $this->templateWord->setValue('soporteTercero7', '');
                    $this->templateWord->setValue('soporteTercero8', '');
                    $this->templateWord->setValue('soporteTercero9', '');
                    $this->templateWord->setValue('soporteTercero10', '');
                    $this->templateWord->setValue('soporteTercero1', '');
                    $this->templateWord->setValue('soporteTercero2', '');
                    $this->templateWord->setValue('soporteTercero3', '');
                    $this->templateWord->setValue('soporteTercero4', '');
                    $this->templateWord->setValue('soporteTercero5', '');
                    $this->templateWord->setValue('soporteTercero6', '');
                    $this->templateWord->setValue('soporteTercero7', '');
                    $this->templateWord->setValue('soporteTercero8', '');
                    $this->templateWord->setValue('soporteTercero9', '');
                    $this->templateWord->setValue('soporteTercero10', '');

                    $this->templateWord->setValue('soporteDebito1', '');
                    $this->templateWord->setValue('soporteDebito2', '');
                    $this->templateWord->setValue('soporteDebito3', '');
                    $this->templateWord->setValue('soporteDebito4', '');
                    $this->templateWord->setValue('soporteDebito5', '');
                    $this->templateWord->setValue('soporteDebito6', '');
                    $this->templateWord->setValue('soporteDebito7', '');
                    $this->templateWord->setValue('soporteDebito8', '');
                    $this->templateWord->setValue('soporteDebito9', '');
                    $this->templateWord->setValue('soporteDebito10', '');
                    $this->templateWord->setValue('soporteDebito11', '');
                    $this->templateWord->setValue('soporteDebito12', '');
                    $this->templateWord->setValue('soporteDebito13', '');
                    $this->templateWord->setValue('soporteDebito14', '');
                    $this->templateWord->setValue('soporteDebito15', '');
                    $this->templateWord->setValue('soporteDebito16', '');
                    $this->templateWord->setValue('soporteDebito17', '');
                    $this->templateWord->setValue('soporteDebito18', '');
                    $this->templateWord->setValue('soporteDebito19', '');
                    $this->templateWord->setValue('soporteDebito20', '');
                    $this->templateWord->setValue('soporteDebito21', '');
                    $this->templateWord->setValue('soporteDebito22', '');
                    $this->templateWord->setValue('soporteDebito23', '');
                    $this->templateWord->setValue('soporteDebito24', '');
                    $this->templateWord->setValue('soporteDebito25', '');
                    $this->templateWord->setValue('soporteDebito26', '');
                    $this->templateWord->setValue('soporteDebito27', '');
                    $this->templateWord->setValue('soporteDebito28', '');
                    $this->templateWord->setValue('soporteDebito29', '');
                    $this->templateWord->setValue('soporteDebito30', '');
                    $this->templateWord->setValue('soporteDebito31', '');
                    $this->templateWord->setValue('soporteDebito32', '');
                    $this->templateWord->setValue('soporteDebito33', '');
                    $this->templateWord->setValue('soporteDebito34', '');
                    $this->templateWord->setValue('soporteDebito35', '');
                    $this->templateWord->setValue('soporteDebito36', '');
                    $this->templateWord->setValue('soporteDebito37', '');
                    $this->templateWord->setValue('soporteDebito38', '');
                    $this->templateWord->setValue('soporteDebito39', '');
                    $this->templateWord->setValue('soporteDebito40', '');
                    $this->templateWord->setValue('soporteDebito41', '');
                    $this->templateWord->setValue('soporteDebito42', '');
                    $this->templateWord->setValue('soporteDebito43', '');
                    $this->templateWord->setValue('soporteDebito44', '');
                    $this->templateWord->setValue('soporteDebito45', '');
                    $this->templateWord->setValue('soporteDebito46', '');
                    $this->templateWord->setValue('soporteDebito47', '');
                    $this->templateWord->setValue('soporteDebito48', '');
                    $this->templateWord->setValue('soporteDebito49', '');
                    $this->templateWord->setValue('soporteDebito50', '');
                    $this->templateWord->setValue('soporteDebito51', '');
                    $this->templateWord->setValue('soporteDebito52', '');
                    $this->templateWord->setValue('soporteDebito53', '');
                    $this->templateWord->setValue('soporteDebito54', '');
                    $this->templateWord->setValue('soporteDebito55', '');
                    $this->templateWord->setValue('soporteDebito56', '');
                    $this->templateWord->setValue('soporteDebito57', '');
                    $this->templateWord->setValue('soporteDebito58', '');
                    $this->templateWord->setValue('soporteDebito59', '');
                    $this->templateWord->setValue('soporteDebito60', '');
                    $this->templateWord->setValue('soporteDebito61', '');
                    $this->templateWord->setValue('soporteDebito62', '');
                    $this->templateWord->setValue('soporteDebito63', '');
                    $this->templateWord->setValue('soporteDebito64', '');
                    $this->templateWord->setValue('soporteDebito65', '');
                    $this->templateWord->setValue('soporteDebito66', '');
                    $this->templateWord->setValue('soporteDebito67', '');
                    $this->templateWord->setValue('soporteDebito68', '');
                    $this->templateWord->setValue('soporteDebito69', '');
                    $this->templateWord->setValue('soporteDebito70', '');
                    $this->templateWord->setValue('soporteDebito71', '');
                    $this->templateWord->setValue('soporteDebito72', '');
                    $this->templateWord->setValue('soporteDebito73', '');
                    $this->templateWord->setValue('soporteDebito74', '');
                    $this->templateWord->setValue('soporteDebito75', '');
                    $this->templateWord->setValue('soporteDebito76', '');
                    $this->templateWord->setValue('soporteDebito77', '');
                    $this->templateWord->setValue('soporteDebito78', '');
                    $this->templateWord->setValue('soporteDebito79', '');
                    $this->templateWord->setValue('soporteDebito80', '');
                    $this->templateWord->setValue('soporteDebito81', '');
                    $this->templateWord->setValue('soporteDebito82', '');
                    $this->templateWord->setValue('soporteDebito83', '');
                    $this->templateWord->setValue('soporteDebito84', '');
                    $this->templateWord->setValue('soporteDebito85', '');
                    $this->templateWord->setValue('soporteDebito86', '');
                    $this->templateWord->setValue('soporteDebito87', '');
                    $this->templateWord->setValue('soporteDebito88', '');
                    $this->templateWord->setValue('soporteDebito89', '');
                    $this->templateWord->setValue('soporteDebito90', '');
                    $this->templateWord->setValue('soporteDebito91', '');
                    $this->templateWord->setValue('soporteDebito92', '');
                    $this->templateWord->setValue('soporteDebito93', '');
                    $this->templateWord->setValue('soporteDebito94', '');
                    $this->templateWord->setValue('soporteDebito95', '');
                    $this->templateWord->setValue('soporteDebito96', '');
                    $this->templateWord->setValue('soporteDebito97', '');
                    $this->templateWord->setValue('soporteDebito98', '');
                    $this->templateWord->setValue('soporteDebito99', '');
                    $this->templateWord->setValue('soporteDebito100', '');

                    $this->templateWord->setValue('soporteCredito1', '');
                    $this->templateWord->setValue('soporteCredito2', '');
                    $this->templateWord->setValue('soporteCredito3', '');
                    $this->templateWord->setValue('soporteCredito4', '');
                    $this->templateWord->setValue('soporteCredito5', '');
                    $this->templateWord->setValue('soporteCredito6', '');
                    $this->templateWord->setValue('soporteCredito7', '');
                    $this->templateWord->setValue('soporteCredito8', '');
                    $this->templateWord->setValue('soporteCredito9', '');
                    $this->templateWord->setValue('soporteCredito10', '');
                    $this->templateWord->setValue('soporteCredito11', '');
                    $this->templateWord->setValue('soporteCredito12', '');
                    $this->templateWord->setValue('soporteCredito13', '');
                    $this->templateWord->setValue('soporteCredito14', '');
                    $this->templateWord->setValue('soporteCredito15', '');
                    $this->templateWord->setValue('soporteCredito16', '');
                    $this->templateWord->setValue('soporteCredito17', '');
                    $this->templateWord->setValue('soporteCredito18', '');
                    $this->templateWord->setValue('soporteCredito19', '');
                    $this->templateWord->setValue('soporteCredito20', '');
                    $this->templateWord->setValue('soporteCredito21', '');
                    $this->templateWord->setValue('soporteCredito22', '');
                    $this->templateWord->setValue('soporteCredito23', '');
                    $this->templateWord->setValue('soporteCredito24', '');
                    $this->templateWord->setValue('soporteCredito25', '');
                    $this->templateWord->setValue('soporteCredito26', '');
                    $this->templateWord->setValue('soporteCredito27', '');
                    $this->templateWord->setValue('soporteCredito28', '');
                    $this->templateWord->setValue('soporteCredito29', '');
                    $this->templateWord->setValue('soporteCredito30', '');
                    $this->templateWord->setValue('soporteCredito31', '');
                    $this->templateWord->setValue('soporteCredito32', '');
                    $this->templateWord->setValue('soporteCredito33', '');
                    $this->templateWord->setValue('soporteCredito34', '');
                    $this->templateWord->setValue('soporteCredito35', '');
                    $this->templateWord->setValue('soporteCredito36', '');
                    $this->templateWord->setValue('soporteCredito37', '');
                    $this->templateWord->setValue('soporteCredito38', '');
                    $this->templateWord->setValue('soporteCredito39', '');
                    $this->templateWord->setValue('soporteCredito40', '');
                    $this->templateWord->setValue('soporteCredito41', '');
                    $this->templateWord->setValue('soporteCredito42', '');
                    $this->templateWord->setValue('soporteCredito43', '');
                    $this->templateWord->setValue('soporteCredito44', '');
                    $this->templateWord->setValue('soporteCredito45', '');
                    $this->templateWord->setValue('soporteCredito46', '');
                    $this->templateWord->setValue('soporteCredito47', '');
                    $this->templateWord->setValue('soporteCredito48', '');
                    $this->templateWord->setValue('soporteCredito49', '');
                    $this->templateWord->setValue('soporteCredito50', '');
                    $this->templateWord->setValue('soporteCredito51', '');
                    $this->templateWord->setValue('soporteCredito52', '');
                    $this->templateWord->setValue('soporteCredito53', '');
                    $this->templateWord->setValue('soporteCredito54', '');
                    $this->templateWord->setValue('soporteCredito55', '');
                    $this->templateWord->setValue('soporteCredito56', '');
                    $this->templateWord->setValue('soporteCredito57', '');
                    $this->templateWord->setValue('soporteCredito58', '');
                    $this->templateWord->setValue('soporteCredito59', '');
                    $this->templateWord->setValue('soporteCredito60', '');
                    $this->templateWord->setValue('soporteCredito61', '');
                    $this->templateWord->setValue('soporteCredito62', '');
                    $this->templateWord->setValue('soporteCredito63', '');
                    $this->templateWord->setValue('soporteCredito64', '');
                    $this->templateWord->setValue('soporteCredito65', '');
                    $this->templateWord->setValue('soporteCredito66', '');
                    $this->templateWord->setValue('soporteCredito67', '');
                    $this->templateWord->setValue('soporteCredito68', '');
                    $this->templateWord->setValue('soporteCredito69', '');
                    $this->templateWord->setValue('soporteCredito70', '');
                    $this->templateWord->setValue('soporteCredito71', '');
                    $this->templateWord->setValue('soporteCredito72', '');
                    $this->templateWord->setValue('soporteCredito73', '');
                    $this->templateWord->setValue('soporteCredito74', '');
                    $this->templateWord->setValue('soporteCredito75', '');
                    $this->templateWord->setValue('soporteCredito76', '');
                    $this->templateWord->setValue('soporteCredito77', '');
                    $this->templateWord->setValue('soporteCredito78', '');
                    $this->templateWord->setValue('soporteCredito79', '');
                    $this->templateWord->setValue('soporteCredito80', '');
                    $this->templateWord->setValue('soporteCredito81', '');
                    $this->templateWord->setValue('soporteCredito82', '');
                    $this->templateWord->setValue('soporteCredito83', '');
                    $this->templateWord->setValue('soporteCredito84', '');
                    $this->templateWord->setValue('soporteCredito85', '');
                    $this->templateWord->setValue('soporteCredito86', '');
                    $this->templateWord->setValue('soporteCredito87', '');
                    $this->templateWord->setValue('soporteCredito88', '');
                    $this->templateWord->setValue('soporteCredito89', '');
                    $this->templateWord->setValue('soporteCredito90', '');
                    $this->templateWord->setValue('soporteCredito91', '');
                    $this->templateWord->setValue('soporteCredito92', '');
                    $this->templateWord->setValue('soporteCredito93', '');
                    $this->templateWord->setValue('soporteCredito94', '');
                    $this->templateWord->setValue('soporteCredito95', '');
                    $this->templateWord->setValue('soporteCredito96', '');
                    $this->templateWord->setValue('soporteCredito97', '');
                    $this->templateWord->setValue('soporteCredito98', '');
                    $this->templateWord->setValue('soporteCredito99', '');
                    $this->templateWord->setValue('soporteCredito100', '');

                    $this->templateWord->setValue('soporteDebito', '');
                    $this->templateWord->setValue('soporteCredito', '');
                    $this->templateWord->setValue('fechaVencim', '');
                    $this->templateWord->setValue('beneficiario', '');
                    $this->templateWord->setValue('nitBeneficiario', '');
                    $this->templateWord->setValue('telefonoBeneficiario', '');
                    $this->templateWord->setValue('ciudadBeneficiario', '');
                    $this->templateWord->setValue('direccionBeneficiario', '');
                    $this->templateWord->setValue('elaboroSoporteContable', '');
                    if (isset($_REQUEST["noChe"])) {
                        $this->templateWord->setValue('noChe', $_REQUEST['noChe']);
                    } else {
                        $this->templateWord->setValue('noChe', '');
                    }

                    $this->templateWord->setValue('nombreEmpresa', '');
                    $this->templateWord->setValue('nitEmpresa', '');
                    $this->templateWord->setValue('noSoporteContable', '');
                    $this->templateWord->setValue('concepto', '');

                    //Factura de Venta
                    $this->templateWord->setValue('soporteProducto1', '');
                    $this->templateWord->setValue('soporteProducto2', '');
                    $this->templateWord->setValue('soporteProducto3', '');
                    $this->templateWord->setValue('soporteProducto4', '');
                    $this->templateWord->setValue('soporteProducto5', '');
                    $this->templateWord->setValue('soporteProducto6', '');
                    $this->templateWord->setValue('soporteProducto7', '');
                    $this->templateWord->setValue('soporteProducto8', '');
                    $this->templateWord->setValue('soporteProducto9', '');
                    $this->templateWord->setValue('soporteProducto10', '');
                    $this->templateWord->setValue('soporteProducto11', '');
                    $this->templateWord->setValue('soporteProducto12', '');
                    $this->templateWord->setValue('soporteProducto13', '');
                    $this->templateWord->setValue('soporteProducto14', '');
                    $this->templateWord->setValue('soporteProducto15', '');
                    $this->templateWord->setValue('soporteProducto16', '');
                    $this->templateWord->setValue('soporteProducto17', '');
                    $this->templateWord->setValue('soporteProducto18', '');
                    $this->templateWord->setValue('soporteProducto19', '');
                    $this->templateWord->setValue('soporteProducto20', '');
                    $this->templateWord->setValue('soporteProducto21', '');
                    $this->templateWord->setValue('soporteProducto22', '');
                    $this->templateWord->setValue('soporteProducto23', '');
                    $this->templateWord->setValue('soporteProducto24', '');
                    $this->templateWord->setValue('soporteProducto25', '');
                    $this->templateWord->setValue('soporteProducto26', '');
                    $this->templateWord->setValue('soporteProducto27', '');
                    $this->templateWord->setValue('soporteProducto28', '');
                    $this->templateWord->setValue('soporteProducto29', '');
                    $this->templateWord->setValue('soporteProducto30', '');
                    $this->templateWord->setValue('soporteProducto31', '');
                    $this->templateWord->setValue('soporteProducto32', '');
                    $this->templateWord->setValue('soporteProducto33', '');
                    $this->templateWord->setValue('soporteProducto34', '');
                    $this->templateWord->setValue('soporteProducto35', '');
                    $this->templateWord->setValue('soporteProducto36', '');
                    $this->templateWord->setValue('soporteProducto37', '');
                    $this->templateWord->setValue('soporteProducto38', '');
                    $this->templateWord->setValue('soporteProducto39', '');
                    $this->templateWord->setValue('soporteProducto40', '');
                    $this->templateWord->setValue('soporteProducto41', '');
                    $this->templateWord->setValue('soporteProducto42', '');
                    $this->templateWord->setValue('soporteProducto43', '');
                    $this->templateWord->setValue('soporteProducto44', '');
                    $this->templateWord->setValue('soporteProducto45', '');
                    $this->templateWord->setValue('soporteProducto46', '');
                    $this->templateWord->setValue('soporteProducto47', '');
                    $this->templateWord->setValue('soporteProducto48', '');
                    $this->templateWord->setValue('soporteProducto49', '');
                    $this->templateWord->setValue('soporteProducto50', '');
                    $this->templateWord->setValue('soporteProducto51', '');
                    $this->templateWord->setValue('soporteProducto52', '');
                    $this->templateWord->setValue('soporteProducto53', '');
                    $this->templateWord->setValue('soporteProducto54', '');
                    $this->templateWord->setValue('soporteProducto55', '');
                    $this->templateWord->setValue('soporteProducto56', '');
                    $this->templateWord->setValue('soporteProducto57', '');
                    $this->templateWord->setValue('soporteProducto58', '');
                    $this->templateWord->setValue('soporteProducto59', '');
                    $this->templateWord->setValue('soporteProducto60', '');
                    $this->templateWord->setValue('soporteProducto61', '');
                    $this->templateWord->setValue('soporteProducto62', '');
                    $this->templateWord->setValue('soporteProducto63', '');
                    $this->templateWord->setValue('soporteProducto64', '');
                    $this->templateWord->setValue('soporteProducto65', '');
                    $this->templateWord->setValue('soporteProducto66', '');
                    $this->templateWord->setValue('soporteProducto67', '');
                    $this->templateWord->setValue('soporteProducto68', '');
                    $this->templateWord->setValue('soporteProducto69', '');
                    $this->templateWord->setValue('soporteProducto70', '');
                    $this->templateWord->setValue('soporteProducto71', '');
                    $this->templateWord->setValue('soporteProducto72', '');
                    $this->templateWord->setValue('soporteProducto73', '');
                    $this->templateWord->setValue('soporteProducto74', '');
                    $this->templateWord->setValue('soporteProducto75', '');
                    $this->templateWord->setValue('soporteProducto76', '');
                    $this->templateWord->setValue('soporteProducto77', '');
                    $this->templateWord->setValue('soporteProducto78', '');
                    $this->templateWord->setValue('soporteProducto79', '');
                    $this->templateWord->setValue('soporteProducto80', '');
                    $this->templateWord->setValue('soporteProducto81', '');
                    $this->templateWord->setValue('soporteProducto82', '');
                    $this->templateWord->setValue('soporteProducto83', '');
                    $this->templateWord->setValue('soporteProducto84', '');
                    $this->templateWord->setValue('soporteProducto85', '');
                    $this->templateWord->setValue('soporteProducto86', '');
                    $this->templateWord->setValue('soporteProducto87', '');
                    $this->templateWord->setValue('soporteProducto88', '');
                    $this->templateWord->setValue('soporteProducto89', '');
                    $this->templateWord->setValue('soporteProducto90', '');
                    $this->templateWord->setValue('soporteProducto91', '');
                    $this->templateWord->setValue('soporteProducto92', '');
                    $this->templateWord->setValue('soporteProducto93', '');
                    $this->templateWord->setValue('soporteProducto94', '');
                    $this->templateWord->setValue('soporteProducto95', '');
                    $this->templateWord->setValue('soporteProducto96', '');
                    $this->templateWord->setValue('soporteProducto97', '');
                    $this->templateWord->setValue('soporteProducto98', '');
                    $this->templateWord->setValue('soporteProducto99', '');
                    $this->templateWord->setValue('soporteProducto100', '');


                                      //Codigo del producto
                    $this->templateWord->setValue('CodigoProducto1', '');
                    $this->templateWord->setValue('CodigoProducto2', '');
                    $this->templateWord->setValue('CodigoProducto3', '');
                    $this->templateWord->setValue('CodigoProducto4', '');
                    $this->templateWord->setValue('CodigoProducto5', '');
                    $this->templateWord->setValue('CodigoProducto6', '');
                    $this->templateWord->setValue('CodigoProducto7', '');
                    $this->templateWord->setValue('CodigoProducto8', '');
                    $this->templateWord->setValue('CodigoProducto9', '');
                    $this->templateWord->setValue('CodigoProducto10', '');


                    $this->templateWord->setValue('soporteCantidad1', '');
                    $this->templateWord->setValue('soporteCantidad2', '');
                    $this->templateWord->setValue('soporteCantidad3', '');
                    $this->templateWord->setValue('soporteCantidad4', '');
                    $this->templateWord->setValue('soporteCantidad5', '');
                    $this->templateWord->setValue('soporteCantidad6', '');
                    $this->templateWord->setValue('soporteCantidad7', '');
                    $this->templateWord->setValue('soporteCantidad8', '');
                    $this->templateWord->setValue('soporteCantidad9', '');
                    $this->templateWord->setValue('soporteCantidad10', '');

                    $this->templateWord->setValue('soporteCantidad11', '');
                    $this->templateWord->setValue('soporteCantidad12', '');
                    $this->templateWord->setValue('soporteCantidad13', '');
                    $this->templateWord->setValue('soporteCantidad14', '');
                    $this->templateWord->setValue('soporteCantidad15', '');
                    $this->templateWord->setValue('soporteCantidad16', '');
                    $this->templateWord->setValue('soporteCantidad17', '');
                    $this->templateWord->setValue('soporteCantidad18', '');
                    $this->templateWord->setValue('soporteCantidad19', '');
                    $this->templateWord->setValue('soporteCantidad20', '');
                    $this->templateWord->setValue('soporteCantidad21', '');
                    $this->templateWord->setValue('soporteCantidad22', '');
                    $this->templateWord->setValue('soporteCantidad23', '');
                    $this->templateWord->setValue('soporteCantidad24', '');
                    $this->templateWord->setValue('soporteCantidad25', '');
                    $this->templateWord->setValue('soporteCantidad26', '');
                    $this->templateWord->setValue('soporteCantidad27', '');
                    $this->templateWord->setValue('soporteCantidad28', '');
                    $this->templateWord->setValue('soporteCantidad29', '');
                    $this->templateWord->setValue('soporteCantidad30', '');
                    $this->templateWord->setValue('soporteCantidad31', '');
                    $this->templateWord->setValue('soporteCantidad32', '');
                    $this->templateWord->setValue('soporteCantidad33', '');
                    $this->templateWord->setValue('soporteCantidad34', '');
                    $this->templateWord->setValue('soporteCantidad35', '');
                    $this->templateWord->setValue('soporteCantidad36', '');
                    $this->templateWord->setValue('soporteCantidad37', '');
                    $this->templateWord->setValue('soporteCantidad38', '');
                    $this->templateWord->setValue('soporteCantidad39', '');
                    $this->templateWord->setValue('soporteCantidad40', '');
                    $this->templateWord->setValue('soporteCantidad41', '');
                    $this->templateWord->setValue('soporteCantidad42', '');
                    $this->templateWord->setValue('soporteCantidad43', '');
                    $this->templateWord->setValue('soporteCantidad44', '');
                    $this->templateWord->setValue('soporteCantidad45', '');
                    $this->templateWord->setValue('soporteCantidad46', '');
                    $this->templateWord->setValue('soporteCantidad47', '');
                    $this->templateWord->setValue('soporteCantidad48', '');
                    $this->templateWord->setValue('soporteCantidad49', '');
                    $this->templateWord->setValue('soporteCantidad50', '');
                    $this->templateWord->setValue('soporteCantidad51', '');
                    $this->templateWord->setValue('soporteCantidad52', '');
                    $this->templateWord->setValue('soporteCantidad53', '');
                    $this->templateWord->setValue('soporteCantidad54', '');
                    $this->templateWord->setValue('soporteCantidad55', '');
                    $this->templateWord->setValue('soporteCantidad56', '');
                    $this->templateWord->setValue('soporteCantidad57', '');
                    $this->templateWord->setValue('soporteCantidad58', '');
                    $this->templateWord->setValue('soporteCantidad59', '');
                    $this->templateWord->setValue('soporteCantidad60', '');
                    $this->templateWord->setValue('soporteCantidad61', '');
                    $this->templateWord->setValue('soporteCantidad62', '');
                    $this->templateWord->setValue('soporteCantidad63', '');
                    $this->templateWord->setValue('soporteCantidad64', '');
                    $this->templateWord->setValue('soporteCantidad65', '');
                    $this->templateWord->setValue('soporteCantidad66', '');
                    $this->templateWord->setValue('soporteCantidad67', '');
                    $this->templateWord->setValue('soporteCantidad68', '');
                    $this->templateWord->setValue('soporteCantidad69', '');
                    $this->templateWord->setValue('soporteCantidad70', '');
                    $this->templateWord->setValue('soporteCantidad71', '');
                    $this->templateWord->setValue('soporteCantidad72', '');
                    $this->templateWord->setValue('soporteCantidad73', '');
                    $this->templateWord->setValue('soporteCantidad74', '');
                    $this->templateWord->setValue('soporteCantidad75', '');
                    $this->templateWord->setValue('soporteCantidad76', '');
                    $this->templateWord->setValue('soporteCantidad77', '');
                    $this->templateWord->setValue('soporteCantidad78', '');
                    $this->templateWord->setValue('soporteCantidad79', '');
                    $this->templateWord->setValue('soporteCantidad80', '');
                    $this->templateWord->setValue('soporteCantidad81', '');
                    $this->templateWord->setValue('soporteCantidad82', '');
                    $this->templateWord->setValue('soporteCantidad83', '');
                    $this->templateWord->setValue('soporteCantidad84', '');
                    $this->templateWord->setValue('soporteCantidad85', '');
                    $this->templateWord->setValue('soporteCantidad86', '');
                    $this->templateWord->setValue('soporteCantidad87', '');
                    $this->templateWord->setValue('soporteCantidad88', '');
                    $this->templateWord->setValue('soporteCantidad89', '');
                    $this->templateWord->setValue('soporteCantidad90', '');
                    $this->templateWord->setValue('soporteCantidad91', '');
                    $this->templateWord->setValue('soporteCantidad92', '');
                    $this->templateWord->setValue('soporteCantidad93', '');
                    $this->templateWord->setValue('soporteCantidad94', '');
                    $this->templateWord->setValue('soporteCantidad95', '');
                    $this->templateWord->setValue('soporteCantidad96', '');
                    $this->templateWord->setValue('soporteCantidad97', '');
                    $this->templateWord->setValue('soporteCantidad98', '');
                    $this->templateWord->setValue('soporteCantidad99', '');
                    $this->templateWord->setValue('soporteCantidad100', '');

                    $this->templateWord->setValue('soporteCantFisica1', '');
                    $this->templateWord->setValue('soporteCantFisica2', '');
                    $this->templateWord->setValue('soporteCantFisica3', '');
                    $this->templateWord->setValue('soporteCantFisica4', '');
                    $this->templateWord->setValue('soporteCantFisica5', '');
                    $this->templateWord->setValue('soporteCantFisica6', '');
                    $this->templateWord->setValue('soporteCantFisica7', '');
                    $this->templateWord->setValue('soporteCantFisica8', '');
                    $this->templateWord->setValue('soporteCantFisica9', '');
                    $this->templateWord->setValue('soporteCantFisica10', '');
                    $this->templateWord->setValue('soporteCantSistema1', '');
                    $this->templateWord->setValue('soporteCantSistema2', '');
                    $this->templateWord->setValue('soporteCantSistema3', '');
                    $this->templateWord->setValue('soporteCantSistema4', '');
                    $this->templateWord->setValue('soporteCantSistema5', '');
                    $this->templateWord->setValue('soporteCantSistema6', '');
                    $this->templateWord->setValue('soporteCantSistema7', '');
                    $this->templateWord->setValue('soporteCantSistema8', '');
                    $this->templateWord->setValue('soporteCantSistema9', '');
                    $this->templateWord->setValue('soporteCantSistema10', '');
                    $this->templateWord->setValue('soporteCantDiferencia1', '');
                    $this->templateWord->setValue('soporteCantDiferencia2', '');
                    $this->templateWord->setValue('soporteCantDiferencia3', '');
                    $this->templateWord->setValue('soporteCantDiferencia4', '');
                    $this->templateWord->setValue('soporteCantDiferencia5', '');
                    $this->templateWord->setValue('soporteCantDiferencia6', '');
                    $this->templateWord->setValue('soporteCantDiferencia7', '');
                    $this->templateWord->setValue('soporteCantDiferencia8', '');
                    $this->templateWord->setValue('soporteCantDiferencia9', '');
                    $this->templateWord->setValue('soporteCantDiferencia10', '');
                    $this->templateWord->setValue('soporteUMedida1', '');
                    $this->templateWord->setValue('soporteUMedida2', '');
                    $this->templateWord->setValue('soporteUMedida3', '');
                    $this->templateWord->setValue('soporteUMedida4', '');
                    $this->templateWord->setValue('soporteUMedida5', '');
                    $this->templateWord->setValue('soporteUMedida6', '');
                    $this->templateWord->setValue('soporteUMedida7', '');
                    $this->templateWord->setValue('soporteUMedida8', '');
                    $this->templateWord->setValue('soporteUMedida9', '');
                    $this->templateWord->setValue('soporteUMedida10', '');

                    $this->templateWord->setValue('soporteUMedida11', '');
                    $this->templateWord->setValue('soporteUMedida12', '');
                    $this->templateWord->setValue('soporteUMedida13', '');
                    $this->templateWord->setValue('soporteUMedida14', '');
                    $this->templateWord->setValue('soporteUMedida15', '');
                    $this->templateWord->setValue('soporteUMedida16', '');
                    $this->templateWord->setValue('soporteUMedida17', '');
                    $this->templateWord->setValue('soporteUMedida18', '');
                    $this->templateWord->setValue('soporteUMedida19', '');
                    $this->templateWord->setValue('soporteUMedida20', '');
                    $this->templateWord->setValue('soporteUMedida21', '');
                    $this->templateWord->setValue('soporteUMedida22', '');
                    $this->templateWord->setValue('soporteUMedida23', '');
                    $this->templateWord->setValue('soporteUMedida24', '');
                    $this->templateWord->setValue('soporteUMedida25', '');
                    $this->templateWord->setValue('soporteUMedida26', '');
                    $this->templateWord->setValue('soporteUMedida27', '');
                    $this->templateWord->setValue('soporteUMedida28', '');
                    $this->templateWord->setValue('soporteUMedida29', '');
                    $this->templateWord->setValue('soporteUMedida30', '');
                    $this->templateWord->setValue('soporteUMedida31', '');
                    $this->templateWord->setValue('soporteUMedida32', '');
                    $this->templateWord->setValue('soporteUMedida33', '');
                    $this->templateWord->setValue('soporteUMedida34', '');
                    $this->templateWord->setValue('soporteUMedida35', '');
                    $this->templateWord->setValue('soporteUMedida36', '');
                    $this->templateWord->setValue('soporteUMedida37', '');
                    $this->templateWord->setValue('soporteUMedida38', '');
                    $this->templateWord->setValue('soporteUMedida39', '');
                    $this->templateWord->setValue('soporteUMedida40', '');
                    $this->templateWord->setValue('soporteUMedida41', '');
                    $this->templateWord->setValue('soporteUMedida42', '');
                    $this->templateWord->setValue('soporteUMedida43', '');
                    $this->templateWord->setValue('soporteUMedida44', '');
                    $this->templateWord->setValue('soporteUMedida45', '');
                    $this->templateWord->setValue('soporteUMedida46', '');
                    $this->templateWord->setValue('soporteUMedida47', '');
                    $this->templateWord->setValue('soporteUMedida48', '');
                    $this->templateWord->setValue('soporteUMedida49', '');
                    $this->templateWord->setValue('soporteUMedida50', '');
                    $this->templateWord->setValue('soporteUMedida51', '');
                    $this->templateWord->setValue('soporteUMedida52', '');
                    $this->templateWord->setValue('soporteUMedida53', '');
                    $this->templateWord->setValue('soporteUMedida54', '');
                    $this->templateWord->setValue('soporteUMedida55', '');
                    $this->templateWord->setValue('soporteUMedida56', '');
                    $this->templateWord->setValue('soporteUMedida57', '');
                    $this->templateWord->setValue('soporteUMedida58', '');
                    $this->templateWord->setValue('soporteUMedida59', '');
                    $this->templateWord->setValue('soporteUMedida60', '');
                    $this->templateWord->setValue('soporteUMedida61', '');
                    $this->templateWord->setValue('soporteUMedida62', '');
                    $this->templateWord->setValue('soporteUMedida63', '');
                    $this->templateWord->setValue('soporteUMedida64', '');
                    $this->templateWord->setValue('soporteUMedida65', '');
                    $this->templateWord->setValue('soporteUMedida66', '');
                    $this->templateWord->setValue('soporteUMedida67', '');
                    $this->templateWord->setValue('soporteUMedida68', '');
                    $this->templateWord->setValue('soporteUMedida69', '');
                    $this->templateWord->setValue('soporteUMedida70', '');
                    $this->templateWord->setValue('soporteUMedida71', '');
                    $this->templateWord->setValue('soporteUMedida72', '');
                    $this->templateWord->setValue('soporteUMedida73', '');
                    $this->templateWord->setValue('soporteUMedida74', '');
                    $this->templateWord->setValue('soporteUMedida75', '');
                    $this->templateWord->setValue('soporteUMedida76', '');
                    $this->templateWord->setValue('soporteUMedida77', '');
                    $this->templateWord->setValue('soporteUMedida78', '');
                    $this->templateWord->setValue('soporteUMedida79', '');
                    $this->templateWord->setValue('soporteUMedida80', '');
                    $this->templateWord->setValue('soporteUMedida81', '');
                    $this->templateWord->setValue('soporteUMedida82', '');
                    $this->templateWord->setValue('soporteUMedida83', '');
                    $this->templateWord->setValue('soporteUMedida84', '');
                    $this->templateWord->setValue('soporteUMedida85', '');
                    $this->templateWord->setValue('soporteUMedida86', '');
                    $this->templateWord->setValue('soporteUMedida87', '');
                    $this->templateWord->setValue('soporteUMedida88', '');
                    $this->templateWord->setValue('soporteUMedida89', '');
                    $this->templateWord->setValue('soporteUMedida90', '');
                    $this->templateWord->setValue('soporteUMedida91', '');
                    $this->templateWord->setValue('soporteUMedida92', '');
                    $this->templateWord->setValue('soporteUMedida93', '');
                    $this->templateWord->setValue('soporteUMedida94', '');
                    $this->templateWord->setValue('soporteUMedida95', '');
                    $this->templateWord->setValue('soporteUMedida96', '');
                    $this->templateWord->setValue('soporteUMedida97', '');
                    $this->templateWord->setValue('soporteUMedida98', '');
                    $this->templateWord->setValue('soporteUMedida99', '');
                    $this->templateWord->setValue('soporteUMedida100', '');

                    $this->templateWord->setValue('soporteValorUnitario1', '');
                    $this->templateWord->setValue('soporteValorUnitario2', '');
                    $this->templateWord->setValue('soporteValorUnitario3', '');
                    $this->templateWord->setValue('soporteValorUnitario4', '');
                    $this->templateWord->setValue('soporteValorUnitario5', '');
                    $this->templateWord->setValue('soporteValorUnitario6', '');
                    $this->templateWord->setValue('soporteValorUnitario7', '');
                    $this->templateWord->setValue('soporteValorUnitario8', '');
                    $this->templateWord->setValue('soporteValorUnitario9', '');
                    $this->templateWord->setValue('soporteValorUnitario10', '');
                    $this->templateWord->setValue('soporteValorUnitario11', '');
                    $this->templateWord->setValue('soporteValorUnitario12', '');
                    $this->templateWord->setValue('soporteValorUnitario13', '');
                    $this->templateWord->setValue('soporteValorUnitario14', '');
                    $this->templateWord->setValue('soporteValorUnitario15', '');
                    $this->templateWord->setValue('soporteValorUnitario16', '');
                    $this->templateWord->setValue('soporteValorUnitario17', '');
                    $this->templateWord->setValue('soporteValorUnitario18', '');
                    $this->templateWord->setValue('soporteValorUnitario19', '');
                    $this->templateWord->setValue('soporteValorUnitario20', '');
                    $this->templateWord->setValue('soporteValorUnitario21', '');
                    $this->templateWord->setValue('soporteValorUnitario22', '');
                    $this->templateWord->setValue('soporteValorUnitario23', '');
                    $this->templateWord->setValue('soporteValorUnitario24', '');
                    $this->templateWord->setValue('soporteValorUnitario25', '');
                    $this->templateWord->setValue('soporteValorUnitario26', '');
                    $this->templateWord->setValue('soporteValorUnitario27', '');
                    $this->templateWord->setValue('soporteValorUnitario28', '');
                    $this->templateWord->setValue('soporteValorUnitario29', '');
                    $this->templateWord->setValue('soporteValorUnitario30', '');
                    $this->templateWord->setValue('soporteValorUnitario31', '');
                    $this->templateWord->setValue('soporteValorUnitario32', '');
                    $this->templateWord->setValue('soporteValorUnitario33', '');
                    $this->templateWord->setValue('soporteValorUnitario34', '');
                    $this->templateWord->setValue('soporteValorUnitario35', '');
                    $this->templateWord->setValue('soporteValorUnitario36', '');
                    $this->templateWord->setValue('soporteValorUnitario37', '');
                    $this->templateWord->setValue('soporteValorUnitario38', '');
                    $this->templateWord->setValue('soporteValorUnitario39', '');
                    $this->templateWord->setValue('soporteValorUnitario40', '');
                    $this->templateWord->setValue('soporteValorUnitario41', '');
                    $this->templateWord->setValue('soporteValorUnitario42', '');
                    $this->templateWord->setValue('soporteValorUnitario43', '');
                    $this->templateWord->setValue('soporteValorUnitario44', '');
                    $this->templateWord->setValue('soporteValorUnitario45', '');
                    $this->templateWord->setValue('soporteValorUnitario46', '');
                    $this->templateWord->setValue('soporteValorUnitario47', '');
                    $this->templateWord->setValue('soporteValorUnitario48', '');
                    $this->templateWord->setValue('soporteValorUnitario49', '');
                    $this->templateWord->setValue('soporteValorUnitario50', '');
                    $this->templateWord->setValue('soporteValorUnitario51', '');
                    $this->templateWord->setValue('soporteValorUnitario52', '');
                    $this->templateWord->setValue('soporteValorUnitario53', '');
                    $this->templateWord->setValue('soporteValorUnitario54', '');
                    $this->templateWord->setValue('soporteValorUnitario55', '');
                    $this->templateWord->setValue('soporteValorUnitario56', '');
                    $this->templateWord->setValue('soporteValorUnitario57', '');
                    $this->templateWord->setValue('soporteValorUnitario58', '');
                    $this->templateWord->setValue('soporteValorUnitario59', '');
                    $this->templateWord->setValue('soporteValorUnitario60', '');
                    $this->templateWord->setValue('soporteValorUnitario61', '');
                    $this->templateWord->setValue('soporteValorUnitario62', '');
                    $this->templateWord->setValue('soporteValorUnitario63', '');
                    $this->templateWord->setValue('soporteValorUnitario64', '');
                    $this->templateWord->setValue('soporteValorUnitario65', '');
                    $this->templateWord->setValue('soporteValorUnitario66', '');
                    $this->templateWord->setValue('soporteValorUnitario67', '');
                    $this->templateWord->setValue('soporteValorUnitario68', '');
                    $this->templateWord->setValue('soporteValorUnitario69', '');
                    $this->templateWord->setValue('soporteValorUnitario70', '');
                    $this->templateWord->setValue('soporteValorUnitario71', '');
                    $this->templateWord->setValue('soporteValorUnitario72', '');
                    $this->templateWord->setValue('soporteValorUnitario73', '');
                    $this->templateWord->setValue('soporteValorUnitario74', '');
                    $this->templateWord->setValue('soporteValorUnitario75', '');
                    $this->templateWord->setValue('soporteValorUnitario76', '');
                    $this->templateWord->setValue('soporteValorUnitario77', '');
                    $this->templateWord->setValue('soporteValorUnitario78', '');
                    $this->templateWord->setValue('soporteValorUnitario79', '');
                    $this->templateWord->setValue('soporteValorUnitario80', '');
                    $this->templateWord->setValue('soporteValorUnitario81', '');
                    $this->templateWord->setValue('soporteValorUnitario82', '');
                    $this->templateWord->setValue('soporteValorUnitario83', '');
                    $this->templateWord->setValue('soporteValorUnitario84', '');
                    $this->templateWord->setValue('soporteValorUnitario85', '');
                    $this->templateWord->setValue('soporteValorUnitario86', '');
                    $this->templateWord->setValue('soporteValorUnitario87', '');
                    $this->templateWord->setValue('soporteValorUnitario88', '');
                    $this->templateWord->setValue('soporteValorUnitario89', '');
                    $this->templateWord->setValue('soporteValorUnitario90', '');
                    $this->templateWord->setValue('soporteValorUnitario91', '');
                    $this->templateWord->setValue('soporteValorUnitario92', '');
                    $this->templateWord->setValue('soporteValorUnitario93', '');
                    $this->templateWord->setValue('soporteValorUnitario94', '');
                    $this->templateWord->setValue('soporteValorUnitario95', '');
                    $this->templateWord->setValue('soporteValorUnitario96', '');
                    $this->templateWord->setValue('soporteValorUnitario97', '');
                    $this->templateWord->setValue('soporteValorUnitario98', '');
                    $this->templateWord->setValue('soporteValorUnitario99', '');
                    $this->templateWord->setValue('soporteValorUnitario100', '');

                    $this->templateWord->setValue('soportePorcentajeIva1', '');
                    $this->templateWord->setValue('soportePorcentajeIva2', '');
                    $this->templateWord->setValue('soportePorcentajeIva3', '');
                    $this->templateWord->setValue('soportePorcentajeIva4', '');
                    $this->templateWord->setValue('soportePorcentajeIva5', '');
                    $this->templateWord->setValue('soportePorcentajeIva6', '');
                    $this->templateWord->setValue('soportePorcentajeIva7', '');
                    $this->templateWord->setValue('soportePorcentajeIva8', '');
                    $this->templateWord->setValue('soportePorcentajeIva9', '');
                    $this->templateWord->setValue('soportePorcentajeIva10', '');
                    $this->templateWord->setValue('soportePorcentajeIva11', '');
                    $this->templateWord->setValue('soportePorcentajeIva12', '');
                    $this->templateWord->setValue('soportePorcentajeIva13', '');
                    $this->templateWord->setValue('soportePorcentajeIva14', '');
                    $this->templateWord->setValue('soportePorcentajeIva15', '');
                    $this->templateWord->setValue('soportePorcentajeIva16', '');
                    $this->templateWord->setValue('soportePorcentajeIva17', '');
                    $this->templateWord->setValue('soportePorcentajeIva18', '');
                    $this->templateWord->setValue('soportePorcentajeIva19', '');
                    $this->templateWord->setValue('soportePorcentajeIva20', '');
                    $this->templateWord->setValue('soportePorcentajeIva21', '');
                    $this->templateWord->setValue('soportePorcentajeIva22', '');
                    $this->templateWord->setValue('soportePorcentajeIva23', '');
                    $this->templateWord->setValue('soportePorcentajeIva24', '');
                    $this->templateWord->setValue('soportePorcentajeIva25', '');
                    $this->templateWord->setValue('soportePorcentajeIva26', '');
                    $this->templateWord->setValue('soportePorcentajeIva27', '');
                    $this->templateWord->setValue('soportePorcentajeIva28', '');
                    $this->templateWord->setValue('soportePorcentajeIva29', '');
                    $this->templateWord->setValue('soportePorcentajeIva30', '');
                    $this->templateWord->setValue('soportePorcentajeIva31', '');
                    $this->templateWord->setValue('soportePorcentajeIva32', '');
                    $this->templateWord->setValue('soportePorcentajeIva33', '');
                    $this->templateWord->setValue('soportePorcentajeIva34', '');
                    $this->templateWord->setValue('soportePorcentajeIva35', '');
                    $this->templateWord->setValue('soportePorcentajeIva36', '');
                    $this->templateWord->setValue('soportePorcentajeIva37', '');
                    $this->templateWord->setValue('soportePorcentajeIva38', '');
                    $this->templateWord->setValue('soportePorcentajeIva39', '');
                    $this->templateWord->setValue('soportePorcentajeIva40', '');
                    $this->templateWord->setValue('soportePorcentajeIva41', '');
                    $this->templateWord->setValue('soportePorcentajeIva42', '');
                    $this->templateWord->setValue('soportePorcentajeIva43', '');
                    $this->templateWord->setValue('soportePorcentajeIva44', '');
                    $this->templateWord->setValue('soportePorcentajeIva45', '');
                    $this->templateWord->setValue('soportePorcentajeIva46', '');
                    $this->templateWord->setValue('soportePorcentajeIva47', '');
                    $this->templateWord->setValue('soportePorcentajeIva48', '');
                    $this->templateWord->setValue('soportePorcentajeIva49', '');
                    $this->templateWord->setValue('soportePorcentajeIva50', '');
                    $this->templateWord->setValue('soportePorcentajeIva51', '');
                    $this->templateWord->setValue('soportePorcentajeIva52', '');
                    $this->templateWord->setValue('soportePorcentajeIva53', '');
                    $this->templateWord->setValue('soportePorcentajeIva54', '');
                    $this->templateWord->setValue('soportePorcentajeIva55', '');
                    $this->templateWord->setValue('soportePorcentajeIva56', '');
                    $this->templateWord->setValue('soportePorcentajeIva57', '');
                    $this->templateWord->setValue('soportePorcentajeIva58', '');
                    $this->templateWord->setValue('soportePorcentajeIva59', '');
                    $this->templateWord->setValue('soportePorcentajeIva60', '');
                    $this->templateWord->setValue('soportePorcentajeIva61', '');
                    $this->templateWord->setValue('soportePorcentajeIva62', '');
                    $this->templateWord->setValue('soportePorcentajeIva63', '');
                    $this->templateWord->setValue('soportePorcentajeIva64', '');
                    $this->templateWord->setValue('soportePorcentajeIva65', '');
                    $this->templateWord->setValue('soportePorcentajeIva66', '');
                    $this->templateWord->setValue('soportePorcentajeIva67', '');
                    $this->templateWord->setValue('soportePorcentajeIva68', '');
                    $this->templateWord->setValue('soportePorcentajeIva69', '');
                    $this->templateWord->setValue('soportePorcentajeIva70', '');
                    $this->templateWord->setValue('soportePorcentajeIva71', '');
                    $this->templateWord->setValue('soportePorcentajeIva72', '');
                    $this->templateWord->setValue('soportePorcentajeIva73', '');
                    $this->templateWord->setValue('soportePorcentajeIva74', '');
                    $this->templateWord->setValue('soportePorcentajeIva75', '');
                    $this->templateWord->setValue('soportePorcentajeIva76', '');
                    $this->templateWord->setValue('soportePorcentajeIva77', '');
                    $this->templateWord->setValue('soportePorcentajeIva78', '');
                    $this->templateWord->setValue('soportePorcentajeIva79', '');
                    $this->templateWord->setValue('soportePorcentajeIva80', '');
                    $this->templateWord->setValue('soportePorcentajeIva81', '');
                    $this->templateWord->setValue('soportePorcentajeIva82', '');
                    $this->templateWord->setValue('soportePorcentajeIva83', '');
                    $this->templateWord->setValue('soportePorcentajeIva84', '');
                    $this->templateWord->setValue('soportePorcentajeIva85', '');
                    $this->templateWord->setValue('soportePorcentajeIva86', '');
                    $this->templateWord->setValue('soportePorcentajeIva87', '');
                    $this->templateWord->setValue('soportePorcentajeIva88', '');
                    $this->templateWord->setValue('soportePorcentajeIva89', '');
                    $this->templateWord->setValue('soportePorcentajeIva90', '');
                    $this->templateWord->setValue('soportePorcentajeIva91', '');
                    $this->templateWord->setValue('soportePorcentajeIva92', '');
                    $this->templateWord->setValue('soportePorcentajeIva93', '');
                    $this->templateWord->setValue('soportePorcentajeIva94', '');
                    $this->templateWord->setValue('soportePorcentajeIva95', '');
                    $this->templateWord->setValue('soportePorcentajeIva96', '');
                    $this->templateWord->setValue('soportePorcentajeIva97', '');
                    $this->templateWord->setValue('soportePorcentajeIva98', '');
                    $this->templateWord->setValue('soportePorcentajeIva99', '');
                    $this->templateWord->setValue('soportePorcentajeIva100', '');

                    $this->templateWord->setValue('soporteTotalUnidad1', '');
                    $this->templateWord->setValue('soporteTotalUnidad2', '');
                    $this->templateWord->setValue('soporteTotalUnidad3', '');
                    $this->templateWord->setValue('soporteTotalUnidad4', '');
                    $this->templateWord->setValue('soporteTotalUnidad5', '');
                    $this->templateWord->setValue('soporteTotalUnidad6', '');
                    $this->templateWord->setValue('soporteTotalUnidad7', '');
                    $this->templateWord->setValue('soporteTotalUnidad8', '');
                    $this->templateWord->setValue('soporteTotalUnidad9', '');
                    $this->templateWord->setValue('soporteTotalUnidad10', '');

                    $this->templateWord->setValue('soporteTotalUnidad11', '');
                    $this->templateWord->setValue('soporteTotalUnidad12', '');
                    $this->templateWord->setValue('soporteTotalUnidad13', '');
                    $this->templateWord->setValue('soporteTotalUnidad14', '');
                    $this->templateWord->setValue('soporteTotalUnidad15', '');
                    $this->templateWord->setValue('soporteTotalUnidad16', '');
                    $this->templateWord->setValue('soporteTotalUnidad17', '');
                    $this->templateWord->setValue('soporteTotalUnidad18', '');
                    $this->templateWord->setValue('soporteTotalUnidad19', '');
                    $this->templateWord->setValue('soporteTotalUnidad20', '');
                    $this->templateWord->setValue('soporteTotalUnidad21', '');
                    $this->templateWord->setValue('soporteTotalUnidad22', '');
                    $this->templateWord->setValue('soporteTotalUnidad23', '');
                    $this->templateWord->setValue('soporteTotalUnidad24', '');
                    $this->templateWord->setValue('soporteTotalUnidad25', '');
                    $this->templateWord->setValue('soporteTotalUnidad26', '');
                    $this->templateWord->setValue('soporteTotalUnidad27', '');
                    $this->templateWord->setValue('soporteTotalUnidad28', '');
                    $this->templateWord->setValue('soporteTotalUnidad29', '');
                    $this->templateWord->setValue('soporteTotalUnidad30', '');
                    $this->templateWord->setValue('soporteTotalUnidad31', '');
                    $this->templateWord->setValue('soporteTotalUnidad32', '');
                    $this->templateWord->setValue('soporteTotalUnidad33', '');
                    $this->templateWord->setValue('soporteTotalUnidad34', '');
                    $this->templateWord->setValue('soporteTotalUnidad35', '');
                    $this->templateWord->setValue('soporteTotalUnidad36', '');
                    $this->templateWord->setValue('soporteTotalUnidad37', '');
                    $this->templateWord->setValue('soporteTotalUnidad38', '');
                    $this->templateWord->setValue('soporteTotalUnidad39', '');
                    $this->templateWord->setValue('soporteTotalUnidad40', '');
                    $this->templateWord->setValue('soporteTotalUnidad41', '');
                    $this->templateWord->setValue('soporteTotalUnidad42', '');
                    $this->templateWord->setValue('soporteTotalUnidad43', '');
                    $this->templateWord->setValue('soporteTotalUnidad44', '');
                    $this->templateWord->setValue('soporteTotalUnidad45', '');
                    $this->templateWord->setValue('soporteTotalUnidad46', '');
                    $this->templateWord->setValue('soporteTotalUnidad47', '');
                    $this->templateWord->setValue('soporteTotalUnidad48', '');
                    $this->templateWord->setValue('soporteTotalUnidad49', '');
                    $this->templateWord->setValue('soporteTotalUnidad50', '');
                    $this->templateWord->setValue('soporteTotalUnidad51', '');
                    $this->templateWord->setValue('soporteTotalUnidad52', '');
                    $this->templateWord->setValue('soporteTotalUnidad53', '');
                    $this->templateWord->setValue('soporteTotalUnidad54', '');
                    $this->templateWord->setValue('soporteTotalUnidad55', '');
                    $this->templateWord->setValue('soporteTotalUnidad56', '');
                    $this->templateWord->setValue('soporteTotalUnidad57', '');
                    $this->templateWord->setValue('soporteTotalUnidad58', '');
                    $this->templateWord->setValue('soporteTotalUnidad59', '');
                    $this->templateWord->setValue('soporteTotalUnidad60', '');
                    $this->templateWord->setValue('soporteTotalUnidad61', '');
                    $this->templateWord->setValue('soporteTotalUnidad62', '');
                    $this->templateWord->setValue('soporteTotalUnidad63', '');
                    $this->templateWord->setValue('soporteTotalUnidad64', '');
                    $this->templateWord->setValue('soporteTotalUnidad65', '');
                    $this->templateWord->setValue('soporteTotalUnidad66', '');
                    $this->templateWord->setValue('soporteTotalUnidad67', '');
                    $this->templateWord->setValue('soporteTotalUnidad68', '');
                    $this->templateWord->setValue('soporteTotalUnidad69', '');
                    $this->templateWord->setValue('soporteTotalUnidad70', '');
                    $this->templateWord->setValue('soporteTotalUnidad71', '');
                    $this->templateWord->setValue('soporteTotalUnidad72', '');
                    $this->templateWord->setValue('soporteTotalUnidad73', '');
                    $this->templateWord->setValue('soporteTotalUnidad74', '');
                    $this->templateWord->setValue('soporteTotalUnidad75', '');
                    $this->templateWord->setValue('soporteTotalUnidad76', '');
                    $this->templateWord->setValue('soporteTotalUnidad77', '');
                    $this->templateWord->setValue('soporteTotalUnidad78', '');
                    $this->templateWord->setValue('soporteTotalUnidad79', '');
                    $this->templateWord->setValue('soporteTotalUnidad80', '');
                    $this->templateWord->setValue('soporteTotalUnidad81', '');
                    $this->templateWord->setValue('soporteTotalUnidad82', '');
                    $this->templateWord->setValue('soporteTotalUnidad83', '');
                    $this->templateWord->setValue('soporteTotalUnidad84', '');
                    $this->templateWord->setValue('soporteTotalUnidad85', '');
                    $this->templateWord->setValue('soporteTotalUnidad86', '');
                    $this->templateWord->setValue('soporteTotalUnidad87', '');
                    $this->templateWord->setValue('soporteTotalUnidad88', '');
                    $this->templateWord->setValue('soporteTotalUnidad89', '');
                    $this->templateWord->setValue('soporteTotalUnidad90', '');
                    $this->templateWord->setValue('soporteTotalUnidad91', '');
                    $this->templateWord->setValue('soporteTotalUnidad92', '');
                    $this->templateWord->setValue('soporteTotalUnidad93', '');
                    $this->templateWord->setValue('soporteTotalUnidad94', '');
                    $this->templateWord->setValue('soporteTotalUnidad95', '');
                    $this->templateWord->setValue('soporteTotalUnidad96', '');
                    $this->templateWord->setValue('soporteTotalUnidad97', '');
                    $this->templateWord->setValue('soporteTotalUnidad98', '');
                    $this->templateWord->setValue('soporteTotalUnidad99', '');
                    $this->templateWord->setValue('soporteTotalUnidad100', '');

                    $this->templateWord->setValue('soporteBodega1', '');
                    $this->templateWord->setValue('soporteBodega2', '');
                    $this->templateWord->setValue('soporteBodega3', '');
                    $this->templateWord->setValue('soporteBodega4', '');
                    $this->templateWord->setValue('soporteBodega5', '');
                    $this->templateWord->setValue('soporteBodega6', '');
                    $this->templateWord->setValue('soporteBodega7', '');
                    $this->templateWord->setValue('soporteBodega8', '');
                    $this->templateWord->setValue('soporteBodega9', '');
                    $this->templateWord->setValue('soporteBodega10', '');

                    $this->templateWord->setValue('soporteCostos1', '');
                    $this->templateWord->setValue('soporteCostos2', '');
                    $this->templateWord->setValue('soporteCostos3', '');
                    $this->templateWord->setValue('soporteCostos4', '');
                    $this->templateWord->setValue('soporteCostos5', '');
                    $this->templateWord->setValue('soporteCostos6', '');
                    $this->templateWord->setValue('soporteCostos7', '');
                    $this->templateWord->setValue('soporteCostos8', '');
                    $this->templateWord->setValue('soporteCostos9', '');
                    $this->templateWord->setValue('soporteCostos10', '');
                    $this->templateWord->setValue('soporteCostos11', '');
                    $this->templateWord->setValue('soporteCostos12', '');
                    $this->templateWord->setValue('soporteCostos13', '');
                    $this->templateWord->setValue('soporteCostos14', '');
                    $this->templateWord->setValue('soporteCostos15', '');
                    $this->templateWord->setValue('soporteCostos16', '');
                    $this->templateWord->setValue('soporteCostos17', '');
                    $this->templateWord->setValue('soporteCostos18', '');
                    $this->templateWord->setValue('soporteCostos19', '');
                    $this->templateWord->setValue('soporteCostos20', '');
                    $this->templateWord->setValue('soporteCostos21', '');
                    $this->templateWord->setValue('soporteCostos22', '');
                    $this->templateWord->setValue('soporteCostos23', '');
                    $this->templateWord->setValue('soporteCostos24', '');
                    $this->templateWord->setValue('soporteCostos25', '');
                    $this->templateWord->setValue('soporteCostos26', '');
                    $this->templateWord->setValue('soporteCostos27', '');
                    $this->templateWord->setValue('soporteCostos28', '');
                    $this->templateWord->setValue('soporteCostos29', '');
                    $this->templateWord->setValue('soporteCostos30', '');
                    $this->templateWord->setValue('soporteCostos31', '');
                    $this->templateWord->setValue('soporteCostos32', '');
                    $this->templateWord->setValue('soporteCostos33', '');
                    $this->templateWord->setValue('soporteCostos34', '');
                    $this->templateWord->setValue('soporteCostos35', '');
                    $this->templateWord->setValue('soporteCostos36', '');
                    $this->templateWord->setValue('soporteCostos37', '');
                    $this->templateWord->setValue('soporteCostos38', '');
                    $this->templateWord->setValue('soporteCostos39', '');
                    $this->templateWord->setValue('soporteCostos40', '');
                    $this->templateWord->setValue('soporteCostos41', '');
                    $this->templateWord->setValue('soporteCostos42', '');
                    $this->templateWord->setValue('soporteCostos43', '');
                    $this->templateWord->setValue('soporteCostos44', '');
                    $this->templateWord->setValue('soporteCostos45', '');
                    $this->templateWord->setValue('soporteCostos46', '');
                    $this->templateWord->setValue('soporteCostos47', '');
                    $this->templateWord->setValue('soporteCostos48', '');
                    $this->templateWord->setValue('soporteCostos49', '');
                    $this->templateWord->setValue('soporteCostos50', '');
                    $this->templateWord->setValue('soporteCostos51', '');
                    $this->templateWord->setValue('soporteCostos52', '');
                    $this->templateWord->setValue('soporteCostos53', '');
                    $this->templateWord->setValue('soporteCostos54', '');
                    $this->templateWord->setValue('soporteCostos55', '');
                    $this->templateWord->setValue('soporteCostos56', '');
                    $this->templateWord->setValue('soporteCostos57', '');
                    $this->templateWord->setValue('soporteCostos58', '');
                    $this->templateWord->setValue('soporteCostos59', '');
                    $this->templateWord->setValue('soporteCostos60', '');
                    $this->templateWord->setValue('soporteCostos61', '');
                    $this->templateWord->setValue('soporteCostos62', '');
                    $this->templateWord->setValue('soporteCostos63', '');
                    $this->templateWord->setValue('soporteCostos64', '');
                    $this->templateWord->setValue('soporteCostos65', '');
                    $this->templateWord->setValue('soporteCostos66', '');
                    $this->templateWord->setValue('soporteCostos67', '');
                    $this->templateWord->setValue('soporteCostos68', '');
                    $this->templateWord->setValue('soporteCostos69', '');
                    $this->templateWord->setValue('soporteCostos70', '');
                    $this->templateWord->setValue('soporteCostos71', '');
                    $this->templateWord->setValue('soporteCostos72', '');
                    $this->templateWord->setValue('soporteCostos73', '');
                    $this->templateWord->setValue('soporteCostos74', '');
                    $this->templateWord->setValue('soporteCostos75', '');
                    $this->templateWord->setValue('soporteCostos76', '');
                    $this->templateWord->setValue('soporteCostos77', '');
                    $this->templateWord->setValue('soporteCostos78', '');
                    $this->templateWord->setValue('soporteCostos79', '');
                    $this->templateWord->setValue('soporteCostos80', '');
                    $this->templateWord->setValue('soporteCostos81', '');
                    $this->templateWord->setValue('soporteCostos82', '');
                    $this->templateWord->setValue('soporteCostos83', '');
                    $this->templateWord->setValue('soporteCostos84', '');
                    $this->templateWord->setValue('soporteCostos85', '');
                    $this->templateWord->setValue('soporteCostos86', '');
                    $this->templateWord->setValue('soporteCostos87', '');
                    $this->templateWord->setValue('soporteCostos88', '');
                    $this->templateWord->setValue('soporteCostos89', '');
                    $this->templateWord->setValue('soporteCostos90', '');
                    $this->templateWord->setValue('soporteCostos91', '');
                    $this->templateWord->setValue('soporteCostos92', '');
                    $this->templateWord->setValue('soporteCostos93', '');
                    $this->templateWord->setValue('soporteCostos94', '');
                    $this->templateWord->setValue('soporteCostos95', '');
                    $this->templateWord->setValue('soporteCostos96', '');
                    $this->templateWord->setValue('soporteCostos97', '');
                    $this->templateWord->setValue('soporteCostos98', '');
                    $this->templateWord->setValue('soporteCostos99', '');
                    $this->templateWord->setValue('soporteCostos100', '');

                    $this->templateWord->setValue('soporteVencimiento1', '');
                    $this->templateWord->setValue('soporteVencimiento2', '');
                    $this->templateWord->setValue('soporteVencimiento3', '');
                    $this->templateWord->setValue('soporteVencimiento4', '');
                    $this->templateWord->setValue('soporteVencimiento5', '');
                    $this->templateWord->setValue('soporteVencimiento6', '');
                    $this->templateWord->setValue('soporteVencimiento7', '');
                    $this->templateWord->setValue('soporteVencimiento8', '');
                    $this->templateWord->setValue('soporteVencimiento9', '');
                    $this->templateWord->setValue('soporteVencimiento10', '');

                    $this->templateWord->setValue('descuento', '');

                    $this->templateWord->setValue('noResolucion', '440000039107');
                    $this->templateWord->setValue('fechaResolucion', '2016 - 06 - 29');
                    $this->templateWord->setValue('desdeResolucion', '501');
                    $this->templateWord->setValue('hastaResolucion', '1000');
                    $this->templateWord->setValue('direccionEmpresa', 'Calle 13 No 17-31 Oficina: 202');
                    $this->templateWord->setValue('direccionCiudadEmpresa', 'Yopal-Casanare');
                    $this->templateWord->setValue('celularEmpresa', '3124703693');
                    $this->templateWord->setValue('telefaxEmpresa', '098-6357759');
                    $this->templateWord->setValue('webEmpresa', 'www.contSoft.com.co');
                    $this->templateWord->setValue('emailEmpresa', 'Informacion@contSoft.com.co');
                    $this->templateWord->setValue('emailCartera', 'contabilidad@contSoft.com.co');
                }
            }
        }
    }

    public function obtenerInformacion()
    {
        // Variables de la plantilla.
        foreach ($this->templateWord->getVariables() as $variable) {
            switch ($variable) {
                case '':
                    break;
                default:
                    break;
            }
        }
    }

    public function crearDocumento()
    {
        $this->templateWord->saveAs($this->rutaAplicacion->rutaDocumentoRelativa . 'documento/' . $this->nombreDocumento . '.docx');
    }

    public function getNombreDocumento()
    {
        return $this->nombreDocumento;
    }
}

$documento = new Documento($centralConsulta, $centralConsultaIca, $rutaAplicacion, $formatoAplicacion, $conexion);  // Para no llamarlo dentro de cada archivo.
