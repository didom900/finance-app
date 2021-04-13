<?php

/**
 * Creación de documentos en la aplicación
 *
 * Creación de documentos que genera la aplicación.
 *
 * @copyright  2017 - Diego Soba.
 * @author     Diego Soba <didom900@gmail.com>
 * @version    1.0
 */

ini_set('max_execution_time', 500000);
ini_set('memory_limit', '1024M');

require_once './bootstrap/autoload.php';

use Ayeo\Barcode;
use Ayeo\Barcode\Printer;

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
error_reporting(E_ERROR);
/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/
//echo $rutaAplicacion->rutaRelativa."++++++";
// include str_replace('//', '/finanzas/',  $rutaAplicacion->rutaRelativa) . 'clases/Documento.php';
if ($rutaAplicacion->rutaRelativa == "/var/www/") {
    $rutaAplicacion->rutaRelativa = "/var/www/finanzas/";
}
include str_replace('/finanzas/finanzas/', '/finanzas/', str_replace('//', '/finanzas/', str_replace('///', '/', $rutaAplicacion->rutaRelativa))) . 'app/Core/Clases/Documento.php';

// echo $rutaAplicacion->rutaRelativa . 'clases/Documento.php';
// include $rutaAplicacion->rutaRelativa . 'clases/Documento.php';

if (!isset($_REQUEST['xml'])) {
    if (!(isset($_REQUEST['bimestre']) && isset($_REQUEST['retIndustriaComercio14']))) {
        if (isset($_REQUEST['remision'])) {
            $documento->setIdDocumento($_REQUEST['remision']);
        } else {
            if (isset($_REQUEST['soporteContable'])) {
                $documento->setIdDocumento($_REQUEST['soporteContable'] ? $_REQUEST['soporteContable'] : 1);
            } else {
                $documento->setIdDocumento(1);
            }
        }

        $documento->consultarInfomacion($_REQUEST);
        // $documento->obtenerInformacion();
        $documento->crearDocumento();

        // Convertir a pdf.
        exec("libreoffice --invisible --convert-to pdf '" . $rutaAplicacion->rutaDocumentoRelativa . 'documento/' . $documento->getNombreDocumento() . ".docx' --outdir " . $rutaAplicacion->rutaDocumentoRelativa . 'documento/');
        exec('"C:\Program Files\LibreOffice 5\program\python.exe" C:\Users\Adres.Heredia\Dropbox\finanzas\app\public\lib\\unoconv-master\unoconv -f pdf -o C:\Users\Adres.Heredia\Dropbox\docsAPP\factcontSoftDocumento\documento\\' . $documento->getNombreDocumento() . '.pdf C:\Users\Adres.Heredia\Dropbox\docsAPP\factcontSoftDocumento\documento\\' . $documento->getNombreDocumento() . '.docx');
        exec('"C:\Program Files\LibreOffice 5\program\python.exe" C:\Users\usuario\Dropbox\finanzas\app\public\lib\unoconv-master\unoconv -f pdf -o C:\Users\usuario\Dropbox\docsAPP\factcontSoftDocumento\documento\\' . $documento->getNombreDocumento() . '.pdf C:\Users\usuario\Dropbox\docsAPP\factcontSoftDocumento\documento\\' . $documento->getNombreDocumento() . '.docx');
        exec('"C:\Program Files\LibreOffice 5\program\python.exe" C:\Users\contSoft\Dropbox\finanzas\app\public\lib\unoconv-master\unoconv -f pdf -o C:\Users\contSoft\Dropbox\docsAPP\factcontSoftDocumento\documento\\' . $documento->getNombreDocumento() . '.pdf C:\Users\contSoft\Dropbox\docsAPP\factcontSoftDocumento\documento\\' . $documento->getNombreDocumento() . '.docx');
    }
    //sleep(5);
    // switch (substr($_SERVER['SERVER_NAME'], 0, 3)) {
    //     case "127":
    //     case "192":
    //         if ($_SERVER['SERVER_PORT'] == "2031") {
    //             exec("libreoffice --invisible --convert-to pdf '" . $rutaAplicacion->rutaDocumentoRelativa . 'documento/' . $documento->getNombreDocumento() . ".docx' --outdir " . $rutaAplicacion->rutaDocumentoRelativa . 'documento/');
    //         } else {
    //             exec('"C:\Program Files\LibreOffice 5\program\python.exe" C:\Users\Adres.Heredia\Dropbox\finanzas\app\public\lib\\unoconv-master\unoconv -f pdf -o C:\Users\Adres.Heredia\Dropbox\docsAPP\factcontSoftDocumento\documento\\' . $documento->getNombreDocumento() . '.pdf C:\Users\Adres.Heredia\Dropbox\docsAPP\factcontSoftDocumento\documento\\' . $documento->getNombreDocumento() . '.docx');
    //         }
    //         //C:\Program Files\LibreOffice 5\
    //         break;
    //     default:
    //         exec("libreoffice --invisible --convert-to pdf '" . $rutaAplicacion->rutaDocumentoRelativa . 'documento/' . $documento->getNombreDocumento() . ".docx' --outdir " . $rutaAplicacion->rutaDocumentoRelativa . 'documento/');
    // }

    error_reporting(0);

    if (isset($_REQUEST['acuerdoPago'])) {
        require_once('./app/public/lib/jpgraph/src/jpgraph.php');
        require_once('./app/public/lib/jpgraph/src/jpgraph_bar.php');
        require_once('./app/public/lib/PDFMerger/fpdf/fpdf.php');
        require_once('./app/public/lib/PDFMerger/fpdi/fpdi.php');

        error_reporting(0);

        // $pdf = new FPDI();
        // $pdf->AddPage();
        // $pdf->setSourceFile($rutaAplicacion->rutaDocumentoRelativa . 'documento/' . $documento->getNombreDocumento() . '.pdf');
        // // import page 1
        // $tplIdx = $pdf->importPage(1);
        // // use the imported page as the template
        // $pdf->useTemplate($tplIdx,0,0);
        // $pdf->SetFont('Arial','',10);
        // //A,C,B sets
        // $code= $codigoBarrasAbono;
        // $code2 = str_replace('(', '', $codigoBarrasAbono);
        // $code3 = str_replace(')', '', $code2);
        // $pdf->Code128(80,167,$code3,115,15);
        // $pdf->SetXY(80,182);
        // $pdf->Write(5, $code);
        // $pdf->Output($rutaAplicacion->rutaDocumentoRelativa . 'documento/' . $documento->getNombreDocumento() . '.pdf');

        $pagoTotal = str_replace(".", "", $_REQUEST["cvalorTotal"]);
        // Completamos.
        while (strlen($pagoTotal) < 14) {
            $pagoTotal = '0' . $pagoTotal;
        }
        $pagoTotal = $pagoTotal;
        if (isset($_REQUEST["cuota"])) {
            $auxCuota = ($_REQUEST["cuota"] == "") ? 0 : $_REQUEST["cuota"];
        } else {
            $auxCuota = 0;
        }

        while (strlen($_REQUEST["formulario"]) < 16) {
            if (strlen($_REQUEST["formulario"]) == 15) {
                $_REQUEST["formulario"] = '0' . $_REQUEST["formulario"];//"$auxCuota" .
            } else {
                $_REQUEST["formulario"] = '0' . $_REQUEST["formulario"];
            }
        }

        if ($_REQUEST["cfechaPago"] == "") {
            $_REQUEST["cfechaPago"] = "0000-00-00";
        }
        $fechaPagoBanco = str_replace("-", "", $_REQUEST["cfechaPago"]);

        $codigoBarras = '(415)7709998022003(8020)' . $_REQUEST["formulario"] . '(3900)' . $pagoTotal  . '(96)' . str_replace('-', '', $fechaPagoBanco);

        $imageCodeBar = sha1(date('Y-m-d H:i:s')).'.png';
        //print_r($codigoBarras);

        $builder = new Barcode\Builder();
        $builder->setFilename($rutaAplicacion->rutaDocumentoRelativa . 'documento/' . $imageCodeBar);
        $builder->setBarcodeType('gs1-128');
        $builder->setWidth(450);
        $builder->setHeight(80);
        $builder->setFontSize(8);
        $builder->saveImage($codigoBarras);

        $pdf = new FPDI();
        // set the source file
        $pageCount = $pdf->setSourceFile($rutaAplicacion->rutaDocumentoRelativa . 'documento/' . $documento->getNombreDocumento() . '.pdf');

        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $tplIdx = $pdf->importPage($pageNo);
            // add a page
            $pdf->AddPage();
            $pdf->useTemplate($tplIdx, null, null, 0, 0, true);
            $pdf->SetFont('Arial', '', 8);

            if ($pageNo ==1) {
                $pdf->Image($rutaAplicacion->rutaDocumentoRelativa . 'documento/' . $imageCodeBar, 80, 145, 120, 20);
            }
        }

        $pdf->Output($rutaAplicacion->rutaDocumentoRelativa . 'documento/' . $documento->getNombreDocumento() . '.pdf');
        if (isset($_REQUEST["idLiquidacion"])) {
            //Registramos el documento.
            $sql = "UPDATE ica_acuerdo_pago_liquidacion SET archivo = ? WHERE ica_acuerdo_pago_liquidacion = ?";
            $resultado = $conexion->getDBCon()->Execute($sql, [
                'archivo' => $documento->getNombreDocumento() . '.pdf',
                'ica_acuerdo_pago_liquidacion' => $_REQUEST["idLiquidacion"]
            ]);
        }
    }

    if (isset($_REQUEST['abonoFactura'])) {
        require_once('./app/public/lib/jpgraph/src/jpgraph.php');
        require_once('./app/public/lib/jpgraph/src/jpgraph_bar.php');
        require_once('./app/public/lib/PDFMerger/fpdf/fpdf.php');
        require_once('./app/public/lib/PDFMerger/fpdi/fpdi.php');
        error_reporting(0);
        $valorReciboBarras = str_replace(".", "", $_REQUEST["valorAbonado"]);
        $valorReciboBarras = str_replace("$", "", $valorReciboBarras);

        switch (strlen($valorReciboBarras)) {
            case 1:
                $valorReciboBarras = "000000000$valorReciboBarras";
                break;
            case 2:
                $valorReciboBarras = "00000000$valorReciboBarras";
                break;
            case 3:
                $valorReciboBarras = "0000000$valorReciboBarras";
                break;
            case 4:
                $valorReciboBarras = "000000$valorReciboBarras";
                break;
            case 5:
                $valorReciboBarras = "00000$valorReciboBarras";
                break;
            case 6:
                $valorReciboBarras = "0000$valorReciboBarras";
                break;
            case 7:
                $valorReciboBarras = "000$valorReciboBarras";
                break;
            case 8:
                $valorReciboBarras = "00$valorReciboBarras";
                break;
            case 9:
                $valorReciboBarras = "0$valorReciboBarras";
                break;
        }
        $fechaLimite = str_replace("-", "", $_REQUEST["fechaLimit"]);
        $codigoBarrasAbono = '(415)7709998014466' . '(8020)' . $_REQUEST["noFactura"] . '(3900)' . $valorReciboBarras . '(96)' . $fechaLimite;

        $pdf = new FPDI();
        $pdf->AddPage();
        $pdf->setSourceFile($rutaAplicacion->rutaDocumentoRelativa . 'documento/' . $documento->getNombreDocumento() . '.pdf');
        // import page 1
        $tplIdx = $pdf->importPage(1);
        // use the imported page as the template
        $pdf->useTemplate($tplIdx, 0, 0);
        $pdf->SetFont('Arial', '', 10);
        //A,C,B sets
        $code= $codigoBarrasAbono;
        $code2 = str_replace('(', '', $codigoBarrasAbono);
        $code3 = str_replace(')', '', $code2);
        $pdf->Code128(80, 167, $code3, 115, 15);
        $pdf->SetXY(80, 182);
        $pdf->Write(5, $code);
        $pdf->Output($rutaAplicacion->rutaDocumentoRelativa . 'documento/' . $documento->getNombreDocumento() . '.pdf');
    }

    if (isset($_REQUEST['facturaServicioPublico']) && $_REQUEST['tipoServicio'] == 5) {
        require_once('./app/public/lib/jpgraph/src/jpgraph.php');
        require_once('./app/public/lib/jpgraph/src/jpgraph_bar.php');
        require_once('./app/public/lib/PDFMerger/fpdf/fpdf.php');
        require_once('./app/public/lib/PDFMerger/fpdi/fpdi.php');

        error_reporting(0);

        $datay1 = array(
            $centralConsulta->consumoMes('2017-01', 7, $_REQUEST['predioServicioPublico']),
            $centralConsulta->consumoMes('2017-02', 7, $_REQUEST['predioServicioPublico']),
            $centralConsulta->consumoMes('2017-03', 7, $_REQUEST['predioServicioPublico']),
            $centralConsulta->consumoMes('2017-01', 7, $_REQUEST['predioServicioPublico']),
            $centralConsulta->consumoMes('2017-05', 7, $_REQUEST['predioServicioPublico']),
            $centralConsulta->consumoMes('2017-06', 7, $_REQUEST['predioServicioPublico']),
            $centralConsulta->consumoMes('2017-07', 7, $_REQUEST['predioServicioPublico']),
            $centralConsulta->consumoMes('2017-08', 7, $_REQUEST['predioServicioPublico']),
            $centralConsulta->consumoMes('2017-09', 7, $_REQUEST['predioServicioPublico']),
            $centralConsulta->consumoMes('2017-10', 7, $_REQUEST['predioServicioPublico']),
            $centralConsulta->consumoMes('2017-11', 7, $_REQUEST['predioServicioPublico']),
            $centralConsulta->consumoMes('2017-12', 7, $_REQUEST['predioServicioPublico'])
        );

        $graph = new Graph(450, 200, 'auto');
        $graph->SetScale("textlin");
        $graph->SetShadow();
        $graph->img->SetMargin(40, 30, 40, 40);
        $graph->xaxis->SetTickLabels(array("Ene","Feb","Mar","Abr","May","Jun","Jul","Agos","Sept","Oct","Nov","Dic"));
        $graph->xaxis->title->Set('2017');
        $graph->xaxis->title->SetFont(FF_FONT1, FS_BOLD);
        $graph->title->Set('Consumos');
        $graph->title->SetFont(FF_FONT1, FS_BOLD);
        $bplot1 = new BarPlot($datay1);
        $bplot1->SetFillColor("black");
        $bplot1->SetShadow();
        $bplot1->SetShadow();
        $gbarplot = new GroupBarPlot(array($bplot1));
        $gbarplot->SetWidth(0.6);
        $graph->Add($gbarplot);

        // Finally send the graph to the browser
        $graph->Stroke($rutaAplicacion->rutaDocumentoRelativa . 'documento/' . $documento->getNombreDocumento() . '4.png');

        // initiate FPDI
        $pdf = new FPDI();
        // add a page
        $pdf->AddPage();
        // set the source file
        $pdf->setSourceFile($rutaAplicacion->rutaDocumentoRelativa . 'documento/' . $documento->getNombreDocumento() . '.pdf');
        // import page 1
        $tplIdx = $pdf->importPage(1);
        // use the imported page and place it at position 10,10 with a width of 100 mm
        $pdf->useTemplate($tplIdx);
        $pdf->Image($rutaAplicacion->rutaDocumentoRelativa . 'documento/' . $documento->getNombreDocumento() . '4.png', 112, 45, 80, 60);
        $pdf->Output($rutaAplicacion->rutaDocumentoRelativa . 'documento/' . $documento->getNombreDocumento() . '.pdf');
        $pdf = new FPDI();
        $pdf->AddPage();
        $pdf->setSourceFile($rutaAplicacion->rutaDocumentoRelativa . 'documento/' . $documento->getNombreDocumento() . '.pdf');
        // import page 1
        $tplIdx = $pdf->importPage(1);
        // use the imported page as the template
        $pdf->useTemplate($tplIdx, 0, 0);
        $pdf->SetFont('Arial', '', 10);
        //A,C,B sets
        $code= $_REQUEST["codigoBarras"];
        $pdf->Code128(80, 250, $code, 115, 15);
        $pdf->SetXY(80, 265);
        $pdf->Write(5, $code);
        $pdf->Output($rutaAplicacion->rutaDocumentoRelativa . 'documento/' . $documento->getNombreDocumento() . '.pdf');
    }

    if (isset($_REQUEST['facturaServicioPublico']) && $_REQUEST['tipoServicio'] == 6) {
        require_once('./app/public/lib/jpgraph/src/jpgraph.php');
        require_once('./app/public/lib/jpgraph/src/jpgraph_bar.php');
        require_once('./app/public/lib/PDFMerger/fpdf/fpdf.php');
        require_once('./app/public/lib/PDFMerger/fpdi/fpdi.php');
        error_reporting(0);

        $datay1=array($centralConsulta->consumoMes('2018-01', 1, $_REQUEST['predioServicioPublico']),$centralConsulta->consumoMes('2018-02', 1, $_REQUEST['predioServicioPublico']),$centralConsulta->consumoMes('2018-03', 1, $_REQUEST['predioServicioPublico']),$centralConsulta->consumoMes('2018-01', 1, $_REQUEST['predioServicioPublico']),$centralConsulta->consumoMes('2018-05', 1, $_REQUEST['predioServicioPublico']),$centralConsulta->consumoMes('2018-06', 1, $_REQUEST['predioServicioPublico']),$centralConsulta->consumoMes('2018-07', 1, $_REQUEST['predioServicioPublico']),$centralConsulta->consumoMes('2018-08', 1, $_REQUEST['predioServicioPublico']),$centralConsulta->consumoMes('2018-09', 1, $_REQUEST['predioServicioPublico']),$centralConsulta->consumoMes('2018-10', 1, $_REQUEST['predioServicioPublico']),$centralConsulta->consumoMes('2018-11', 1, $_REQUEST['predioServicioPublico']),$centralConsulta->consumoMes('2018-12', 1, $_REQUEST['predioServicioPublico']));
        $graph = new Graph(450, 200, 'auto');
        $graph->SetScale("textlin");
        $graph->SetShadow();
        $graph->img->SetMargin(40, 30, 40, 40);
        $graph->xaxis->SetTickLabels(array("Ene","Feb","Mar","Abr","May","Jun","Jul","Agos","Sept","Oct","Nov","Dic"));
        $graph->xaxis->title->Set('2018');
        $graph->xaxis->title->SetFont(FF_FONT1, FS_BOLD);
        $graph->title->Set('Consumos');
        $graph->title->SetFont(FF_FONT1, FS_BOLD);
        $bplot1 = new BarPlot($datay1);
        $bplot1->SetFillColor("black");
        $bplot1->SetShadow();
        $bplot1->SetShadow();
        $gbarplot = new GroupBarPlot(array($bplot1));
        $gbarplot->SetWidth(0.6);
        $graph->Add($gbarplot);

        // Finally send the graph to the browser
        $graph->Stroke($rutaAplicacion->rutaDocumentoRelativa . 'documento/' . $documento->getNombreDocumento() . '3.png');
        // initiate FPDI
        $pdf = new FPDI();
        // add a page
        $pdf->AddPage();
        // set the source file
        $pdf->setSourceFile($rutaAplicacion->rutaDocumentoRelativa . 'documento/' . $documento->getNombreDocumento() . '.pdf');
        // import page 1
        $tplIdx = $pdf->importPage(1);
        // use the imported page and place it at position 10,10 with a width of 100 mm
        $pdf->useTemplate($tplIdx);
        $pdf->Image($rutaAplicacion->rutaDocumentoRelativa . 'documento/' . $documento->getNombreDocumento() . '3.png', 110, 162, 85, 45);
        $pdf->Output($rutaAplicacion->rutaDocumentoRelativa . 'documento/' . $documento->getNombreDocumento() . '.pdf');



        $datay1=array($centralConsulta->consumoMes('2017-01', 4, $_REQUEST['predioServicioPublico']),$centralConsulta->consumoMes('2017-02', 4, $_REQUEST['predioServicioPublico']),$centralConsulta->consumoMes('2017-03', 4, $_REQUEST['predioServicioPublico']),$centralConsulta->consumoMes('2017-04', 4, $_REQUEST['predioServicioPublico']),$centralConsulta->consumoMes('2017-05', 4, $_REQUEST['predioServicioPublico']),$centralConsulta->consumoMes('2017-06', 4, $_REQUEST['predioServicioPublico']),$centralConsulta->consumoMes('2017-07', 4, $_REQUEST['predioServicioPublico']),$centralConsulta->consumoMes('2017-08', 4, $_REQUEST['predioServicioPublico']),$centralConsulta->consumoMes('2017-09', 4, $_REQUEST['predioServicioPublico']),$centralConsulta->consumoMes('2017-10', 4, $_REQUEST['predioServicioPublico']),$centralConsulta->consumoMes('2017-11', 4, $_REQUEST['predioServicioPublico']),$centralConsulta->consumoMes('2017-12', 4, $_REQUEST['predioServicioPublico']));
        $graph = new Graph(450, 200, 'auto');
        $graph->SetScale("textlin");
        $graph->SetShadow();
        $graph->img->SetMargin(40, 30, 40, 40);
        $graph->xaxis->SetTickLabels(array("Ene","Feb","Mar","Abr","May","Jun","Jul","Agos","Sept","Oct","Nov","Dic"));
        $graph->xaxis->title->Set('2017');
        $graph->xaxis->title->SetFont(FF_FONT1, FS_BOLD);
        $graph->title->Set('Consumos');
        $graph->title->SetFont(FF_FONT1, FS_BOLD);
        $bplot1 = new BarPlot($datay1);
        $bplot1->SetFillColor("black");
        $bplot1->SetShadow();
        $bplot1->SetShadow();
        $gbarplot = new GroupBarPlot(array($bplot1));
        $gbarplot->SetWidth(0.6);
        $graph->Add($gbarplot);

        // Finally send the graph to the browser
        $graph->Stroke($rutaAplicacion->rutaDocumentoRelativa . 'documento/' . $documento->getNombreDocumento() . '2.png');

        // initiate FPDI
        $pdf = new FPDI();
        // add a page
        $pdf->AddPage();
        // set the source file
        $pdf->setSourceFile($rutaAplicacion->rutaDocumentoRelativa . 'documento/' . $documento->getNombreDocumento() . '.pdf');
        // import page 1
        $tplIdx = $pdf->importPage(1);
        // use the imported page and place it at position 10,10 with a width of 100 mm
        $pdf->useTemplate($tplIdx);
        $pdf->Image($rutaAplicacion->rutaDocumentoRelativa . 'documento/' . $documento->getNombreDocumento() . '2.png', 110, 45, 85, 45);
        $pdf->Output($rutaAplicacion->rutaDocumentoRelativa . 'documento/' . $documento->getNombreDocumento() . '.pdf');


        $datay1=array($centralConsulta->consumoMes('2017-01', 7, $_REQUEST['predioServicioPublico']),$centralConsulta->consumoMes('2017-02', 7, $_REQUEST['predioServicioPublico']),$centralConsulta->consumoMes('2017-03', 7, $_REQUEST['predioServicioPublico']),$centralConsulta->consumoMes('2017-01', 7, $_REQUEST['predioServicioPublico']),$centralConsulta->consumoMes('2017-05', 7, $_REQUEST['predioServicioPublico']),$centralConsulta->consumoMes('2017-06', 7, $_REQUEST['predioServicioPublico']),$centralConsulta->consumoMes('2017-07', 7, $_REQUEST['predioServicioPublico']),$centralConsulta->consumoMes('2017-08', 7, $_REQUEST['predioServicioPublico']),$centralConsulta->consumoMes('2017-09', 7, $_REQUEST['predioServicioPublico']),$centralConsulta->consumoMes('2017-10', 7, $_REQUEST['predioServicioPublico']),$centralConsulta->consumoMes('2017-11', 7, $_REQUEST['predioServicioPublico']),$centralConsulta->consumoMes('2017-12', 7, $_REQUEST['predioServicioPublico']));


        $graph = new Graph(450, 200, 'auto');
        $graph->SetScale("textlin");
        $graph->SetShadow();
        $graph->img->SetMargin(40, 30, 40, 40);
        $graph->xaxis->SetTickLabels(array("Ene","Feb","Mar","Abr","May","Jun","Jul","Agos","Sept","Oct","Nov","Dic"));
        $graph->xaxis->title->Set('2017');
        $graph->xaxis->title->SetFont(FF_FONT1, FS_BOLD);
        $graph->title->Set('Consumos');
        $graph->title->SetFont(FF_FONT1, FS_BOLD);
        $bplot1 = new BarPlot($datay1);
        $bplot1->SetFillColor("black");
        $bplot1->SetShadow();
        $bplot1->SetShadow();
        $gbarplot = new GroupBarPlot(array($bplot1));
        $gbarplot->SetWidth(0.6);
        $graph->Add($gbarplot);

        // Finally send the graph to the browser
        $graph->Stroke($rutaAplicacion->rutaDocumentoRelativa . 'documento/' . $documento->getNombreDocumento() . '4.png');

        // initiate FPDI
        $pdf = new FPDI();
        // add a page
        $pdf->AddPage();
        // set the source file
        $pdf->setSourceFile($rutaAplicacion->rutaDocumentoRelativa . 'documento/' . $documento->getNombreDocumento() . '.pdf');
        // import page 1
        $tplIdx = $pdf->importPage(1);
        // use the imported page and place it at position 10,10 with a width of 100 mm
        $pdf->useTemplate($tplIdx);
        $pdf->Image($rutaAplicacion->rutaDocumentoRelativa . 'documento/' . $documento->getNombreDocumento() . '4.png', 110, 105, 85, 45);
        $pdf->Output($rutaAplicacion->rutaDocumentoRelativa . 'documento/' . $documento->getNombreDocumento() . '.pdf');
        $pdf = new FPDI();
        $pdf->AddPage();
        $pdf->setSourceFile($rutaAplicacion->rutaDocumentoRelativa . 'documento/' . $documento->getNombreDocumento() . '.pdf');
        // import page 1
        $tplIdx = $pdf->importPage(1);
        // use the imported page as the template
        $pdf->useTemplate($tplIdx, 0, 0);
        $pdf->SetFont('Arial', '', 10);
        //A,C,B sets
        $code= $_REQUEST["codigoBarras"];
        $pdf->Code128(80, 250, $code, 115, 15);
        $pdf->SetXY(80, 265);
        $pdf->Write(5, $code);
        $pdf->Output($rutaAplicacion->rutaDocumentoRelativa . 'documento/' . $documento->getNombreDocumento() . '.pdf');
    }

    if (isset($_REQUEST['facturaServicioPublico'])) {
        require_once('./app/public/lib/jpgraph/src/jpgraph.php');
        require_once('./app/public/lib/jpgraph/src/jpgraph_bar.php');
        require_once('./app/public/lib/PDFMerger/fpdf/fpdf.php');
        require_once('./app/public/lib/PDFMerger/fpdi/fpdi.php');
        error_reporting(0);
        /*$data1y=array($centralConsulta->consumoMes('2017-01', $_REQUEST['tipoServicio'], $_REQUEST['predioServicioPublico']),$centralConsulta->consumoMes('2017-02', $_REQUEST['tipoServicio'], $_REQUEST['predioServicioPublico']),$centralConsulta->consumoMes('2017-03', $_REQUEST['tipoServicio'], $_REQUEST['predioServicioPublico']),$centralConsulta->consumoMes('2017-04', $_REQUEST['tipoServicio'], $_REQUEST['predioServicioPublico']),$centralConsulta->consumoMes('2017-05', $_REQUEST['tipoServicio'], $_REQUEST['predioServicioPublico']),$centralConsulta->consumoMes('2017-06', $_REQUEST['tipoServicio'], $_REQUEST['predioServicioPublico']),$centralConsulta->consumoMes('2017-07', $_REQUEST['tipoServicio'], $_REQUEST['predioServicioPublico']),$centralConsulta->consumoMes('2017-08', $_REQUEST['tipoServicio'], $_REQUEST['predioServicioPublico']),$centralConsulta->consumoMes('2017-09', $_REQUEST['tipoServicio'], $_REQUEST['predioServicioPublico']),$centralConsulta->consumoMes('2017-10', $_REQUEST['tipoServicio'], $_REQUEST['predioServicioPublico']),$centralConsulta->consumoMes('2017-11', $_REQUEST['tipoServicio'], $_REQUEST['predioServicioPublico']),$centralConsulta->consumoMes('2017-12', $_REQUEST['tipoServicio'], $_REQUEST['predioServicioPublico']));
        $graph = new Graph(400, 300, 'auto');
        $graph->SetScale('textlin');

        $theme_class = new AquaTheme;
        $graph->SetTheme($theme_class);

        // after setting theme, you can change details as you want
        $graph->SetFrame(true, 'lightgray');                        // set frame visible

        $graph->xaxis->SetTickLabels(array("Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic")); // change xaxis lagels
        $graph->title->Set("Consumos");                    // add title

        // add barplot
        $bplot = new BarPlot($data1y);
        $graph->Add($bplot);

        // you can change properties of the plot only after calling Add()
        $bplot->SetWeight(0);
        $bplot->SetFillGradient('#FFAAAA:0.7', '#FFAAAA:1.2', GRAD_VER);*/


        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


        $datay1=array($centralConsulta->consumoMes('2018-01', $_REQUEST['tipoServicio'], $_REQUEST['predioServicioPublico']),$centralConsulta->consumoMes('2018-02', $_REQUEST['tipoServicio'], $_REQUEST['predioServicioPublico']),$centralConsulta->consumoMes('2018-03', $_REQUEST['tipoServicio'], $_REQUEST['predioServicioPublico']),$centralConsulta->consumoMes('2018-04', $_REQUEST['tipoServicio'], $_REQUEST['predioServicioPublico']),$centralConsulta->consumoMes('2018-05', $_REQUEST['tipoServicio'], $_REQUEST['predioServicioPublico']),$centralConsulta->consumoMes('2018-06', $_REQUEST['tipoServicio'], $_REQUEST['predioServicioPublico']),$centralConsulta->consumoMes('2018-07', $_REQUEST['tipoServicio'], $_REQUEST['predioServicioPublico']),$centralConsulta->consumoMes('2018-08', $_REQUEST['tipoServicio'], $_REQUEST['predioServicioPublico']),$centralConsulta->consumoMes('2018-09', $_REQUEST['tipoServicio'], $_REQUEST['predioServicioPublico']),$centralConsulta->consumoMes('2018-10', $_REQUEST['tipoServicio'], $_REQUEST['predioServicioPublico']),$centralConsulta->consumoMes('2018-11', $_REQUEST['tipoServicio'], $_REQUEST['predioServicioPublico']),$centralConsulta->consumoMes('2018-12', $_REQUEST['tipoServicio'], $_REQUEST['predioServicioPublico']));


        $graph = new Graph(450, 200, 'auto');
        $graph->SetScale("textlin");
        $graph->SetShadow();
        $graph->img->SetMargin(40, 30, 40, 40);
        $graph->xaxis->SetTickLabels(array("Ene","Feb","Mar","Abr","May","Jun","Jul","Agos","Sept","Oct","Nov","Dic"));
        $graph->xaxis->title->Set('2018');
        $graph->xaxis->title->SetFont(FF_FONT1, FS_BOLD);

        $graph->title->Set('Consumos');
        $graph->title->SetFont(FF_FONT1, FS_BOLD);

        $bplot1 = new BarPlot($datay1);

        $bplot1->SetFillColor("black");

        $bplot1->SetShadow();

        $bplot1->SetShadow();

        $gbarplot = new GroupBarPlot(array($bplot1));
        $gbarplot->SetWidth(0.6);
        $graph->Add($gbarplot);


        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        /*// We need some data
        $datay=array($centralConsulta->consumoMes('2017-01', $_REQUEST['tipoServicio'], $_REQUEST['predioServicioPublico']),$centralConsulta->consumoMes('2017-02', $_REQUEST['tipoServicio'], $_REQUEST['predioServicioPublico']),$centralConsulta->consumoMes('2017-03', $_REQUEST['tipoServicio'], $_REQUEST['predioServicioPublico']),$centralConsulta->consumoMes('2017-04', $_REQUEST['tipoServicio'], $_REQUEST['predioServicioPublico']),$centralConsulta->consumoMes('2017-05', $_REQUEST['tipoServicio'], $_REQUEST['predioServicioPublico']),$centralConsulta->consumoMes('2017-06', $_REQUEST['tipoServicio'], $_REQUEST['predioServicioPublico']),$centralConsulta->consumoMes('2017-07', $_REQUEST['tipoServicio'], $_REQUEST['predioServicioPublico']),$centralConsulta->consumoMes('2017-08', $_REQUEST['tipoServicio'], $_REQUEST['predioServicioPublico']),$centralConsulta->consumoMes('2017-09', $_REQUEST['tipoServicio'], $_REQUEST['predioServicioPublico']),$centralConsulta->consumoMes('2017-10', $_REQUEST['tipoServicio'], $_REQUEST['predioServicioPublico']),$centralConsulta->consumoMes('2017-11', $_REQUEST['tipoServicio'], $_REQUEST['predioServicioPublico']),$centralConsulta->consumoMes('2017-12', $_REQUEST['tipoServicio'], $_REQUEST['predioServicioPublico']));


        $datax=array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");

        // Setup the graph.
        $graph = new Graph(400,240);
        $graph->img->SetMargin(60,20,35,75);
        $graph->SetScale("textlin");
        $graph->SetMarginColor("lightblue:1.1");
        $graph->SetShadow();

        // Set up the title for the graph
        $graph->title->Set("Consumos del Año");
        $graph->title->SetMargin(8);
        $graph->title->SetFont(FF_VERDANA,FS_BOLD,12);
        $graph->title->SetColor("darkred");

        // Setup font for axis
        $graph->xaxis->SetFont(FF_VERDANA,FS_NORMAL,10);
        $graph->yaxis->SetFont(FF_VERDANA,FS_NORMAL,10);

        // Show 0 label on Y-axis (default is not to show)
        $graph->yscale->ticks->SupressZeroLabel(false);

        // Setup X-axis labels
        $graph->xaxis->SetTickLabels($datax);
        $graph->xaxis->SetLabelAngle(50);

        // Create the bar pot
        $bplot = new BarPlot($datay);
        $bplot->SetWidth(0.6);

        // Setup color for gradient fill style
        $bplot->SetFillGradient("navy:0.9","navy:1.85",GRAD_LEFT_REFLECTION);

        // Set color for the frame of each bar
        $bplot->SetColor("white");
        $graph->Add($bplot);
*/
        // Finally send the graph to the browser
        $graph->Stroke($rutaAplicacion->rutaDocumentoRelativa . 'documento/' . $documento->getNombreDocumento() . '.png');

        // initiate FPDI
        $pdf = new FPDI();
        // add a page
        $pdf->AddPage();
        // set the source file
        $pdf->setSourceFile($rutaAplicacion->rutaDocumentoRelativa . 'documento/' . $documento->getNombreDocumento() . '.pdf');
        // import page 1
        $tplIdx = $pdf->importPage(1);
        // use the imported page and place it at position 10,10 with a width of 100 mm
        $pdf->useTemplate($tplIdx);
        if ($_REQUEST["tipoServicio"] == 4) {
            $pdf->Image($rutaAplicacion->rutaDocumentoRelativa . 'documento/' . $documento->getNombreDocumento() . '.png', 120, 60, 75, 38);
        }
        /*if ($_REQUEST["tipoServicio"] == 5){
        $pdf->Image($rutaAplicacion->rutaDocumentoRelativa . 'documento/' . $documento->getNombreDocumento() . '.png', 100, 45, 65, 60);
        }*/
        if ($_REQUEST["tipoServicio"] == 1) {
            $pdf->Image($rutaAplicacion->rutaDocumentoRelativa . 'documento/' . $documento->getNombreDocumento() . '.png', 100, 60, 95, 40);
        }
        if ($_REQUEST["tipoServicio"] == 2) {
            $pdf->Image($rutaAplicacion->rutaDocumentoRelativa . 'documento/' . $documento->getNombreDocumento() . '.png', 100, 65, 95, 50);
        }
        if ($_REQUEST["tipoServicio"] == 3) {
            $pdf->Image($rutaAplicacion->rutaDocumentoRelativa . 'documento/' . $documento->getNombreDocumento() . '.png', 100, 60, 95, 50);
        }
        if ($_REQUEST["tipoServicio"] == 7) {
            $pdf->Image($rutaAplicacion->rutaDocumentoRelativa . 'documento/' . $documento->getNombreDocumento() . '.png', 110, 60, 83, 40);
        }

        $pdf->Output($rutaAplicacion->rutaDocumentoRelativa . 'documento/' . $documento->getNombreDocumento() . '.pdf');

        if ($_REQUEST["tipoServicio"] == 4) {
            $pdf = new FPDI();
            $pdf->AddPage();
            $pdf->setSourceFile($rutaAplicacion->rutaDocumentoRelativa . 'documento/' . $documento->getNombreDocumento() . '.pdf');
            // import page 1
            $tplIdx = $pdf->importPage(1);
            // use the imported page as the template
            $pdf->useTemplate($tplIdx, 0, 0);
            $pdf->SetFont('Arial', '', 10);
            //A,C,B sets
            $code= $_REQUEST["codigoBarras"];
            $pdf->Code128(80, 250, $code, 115, 15);
            $pdf->SetXY(80, 265);
            $pdf->Write(5, $code);
            $pdf->Output($rutaAplicacion->rutaDocumentoRelativa . 'documento/' . $documento->getNombreDocumento() . '.pdf');
        }
        if ($_REQUEST["tipoServicio"] == 1) {
            $pdf = new FPDI();
            $pdf->AddPage();
            $pdf->setSourceFile($rutaAplicacion->rutaDocumentoRelativa . 'documento/' . $documento->getNombreDocumento() . '.pdf');
            // import page 1
            $tplIdx = $pdf->importPage(1);
            // use the imported page as the template
            $pdf->useTemplate($tplIdx, 0, 0);
            $pdf->SetFont('Arial', '', 10);
            //A,C,B sets
            $code= $_REQUEST["codigoBarras"];
            $pdf->Code128(80, 230, $code, 115, 15);
            $pdf->SetXY(80, 245);
            $pdf->Write(5, $code);
            $pdf->Output($rutaAplicacion->rutaDocumentoRelativa . 'documento/' . $documento->getNombreDocumento() . '.pdf');
        }
        if ($_REQUEST["tipoServicio"] == 2) {
            $pdf = new FPDI();
            $pdf->AddPage();
            $pdf->setSourceFile($rutaAplicacion->rutaDocumentoRelativa . 'documento/' . $documento->getNombreDocumento() . '.pdf');
            // import page 1
            $tplIdx = $pdf->importPage(1);
            // use the imported page as the template
            $pdf->useTemplate($tplIdx, 0, 0);
            $pdf->SetFont('Arial', '', 10);
            //A,C,B sets
            $code= $_REQUEST["codigoBarras"];
            $pdf->Code128(80, 240, $code, 115, 15);
            $pdf->SetXY(80, 255);
            $pdf->Write(5, $code);
            $pdf->Output($rutaAplicacion->rutaDocumentoRelativa . 'documento/' . $documento->getNombreDocumento() . '.pdf');
        }
        if ($_REQUEST["tipoServicio"] == 3) {
            $pdf = new FPDI();
            $pdf->AddPage();
            $pdf->setSourceFile($rutaAplicacion->rutaDocumentoRelativa . 'documento/' . $documento->getNombreDocumento() . '.pdf');
            // import page 1
            $tplIdx = $pdf->importPage(1);
            // use the imported page as the template
            $pdf->useTemplate($tplIdx, 0, 0);
            $pdf->SetFont('Arial', '', 10);
            //A,C,B sets
            $code= $_REQUEST["codigoBarras"];
            $pdf->Code128(80, 235, $code, 115, 15);
            $pdf->SetXY(80, 250);
            $pdf->Write(5, $code);
            $pdf->Output($rutaAplicacion->rutaDocumentoRelativa . 'documento/' . $documento->getNombreDocumento() . '.pdf');
        }
        /*if ($_REQUEST["tipoServicio"] == 5){
            $pdf = new FPDI();
            $pdf->AddPage();
            $pdf->setSourceFile($rutaAplicacion->rutaDocumentoRelativa . 'documento/' . $documento->getNombreDocumento() . '.pdf');
            // import page 1
            $tplIdx = $pdf->importPage(1);
            // use the imported page as the template
            $pdf->useTemplate($tplIdx,0,0);
            $pdf->SetFont('Arial','',10);
            //A,C,B sets
            $code= $_REQUEST["codigoBarras"];
            $pdf->Code128(80,250,$code,115,15);
            $pdf->SetXY(80,265);
            $pdf->Write(5, $code);
            $pdf->Output($rutaAplicacion->rutaDocumentoRelativa . 'documento/' . $documento->getNombreDocumento() . '.pdf');
        }*/
        if ($_REQUEST["tipoServicio"] == 7) {
            $pdf = new FPDI();
            $pdf->AddPage();
            $pdf->setSourceFile($rutaAplicacion->rutaDocumentoRelativa . 'documento/' . $documento->getNombreDocumento() . '.pdf');
            // import page 1
            $tplIdx = $pdf->importPage(1);
            // use the imported page as the template
            $pdf->useTemplate($tplIdx, 0, 0);
            $pdf->SetFont('Arial', '', 10);
            //A,C,B sets
            $code= $_REQUEST["codigoBarras"];
            $pdf->Code128(80, 230, $code, 115, 15);
            $pdf->SetXY(80, 245);
            $pdf->Write(5, $code);
            $pdf->Output($rutaAplicacion->rutaDocumentoRelativa . 'documento/' . $documento->getNombreDocumento() . '.pdf');
        }
    }

    if (!isset($_REQUEST['pdf'])) {
        if (isset($_REQUEST['individual'])) {
            echo str_replace("http://127.0.0.1:2032", "http://finanzas-test.contSoft.com.co:2032", str_replace("https://127.0.0.1", "https://finanzas.contSoft.com.co", $rutaAplicacion->rutaDocumentoAbsoluta)) . 'documento/' . $documento->getNombreDocumento() . '.pdf';
        } else {
            //str_replace('https://finanzas.contSoft.com.co/', 'http://finanzas.contSoft.com.co:2031/', $rutaAplicacion->rutaDocumentoAbsoluta)
            if (isset($_REQUEST['declaracionCiudadano']) || isset($_REQUEST['declaracionCiudadanoManual'])) {
                if (isset($_REQUEST['guardar'])) {
                    if ($_REQUEST["formulario_manual"] == "") {
                        $declaracion[0] = 0;
                    } else {
                        $declaracion = explode("*_*", $_REQUEST["formulario_manual"]);
                    }

                    if (isset($_REQUEST["cfechaPago"]) && isset($_REQUEST["auxFormulario"])) {
                        $declaracion[0] = -1;
                    }

                    //print "<pre>"; print_r($declaracion); print "</pre>\n";
                    if (isset($_REQUEST['bimestre'])) {
                        $data = "soporteContable=". $_REQUEST['soporteContable'] .
                            "&documentoExterno=".
                            "&prefijoDocumentoExterno=".
                            "&tipoDocumentoContable=8".
                            "&prefijo[]=0".
                            "&consecutivo=1".
                            "&noAlmacen=0".
                            "&utilizado=0".
                            "&declaracionCiudadano=0".
                            "&fechaDocumento=".date('Y-m-d').
                            "&empresaSeleccionada[]=".$_REQUEST['empresa'].
                            "&terceroElabora[]=".$_REQUEST['tercero'].
                            "&terceroRecibe[]=".$_REQUEST['tercero'].
                            "&prefijoFactura=".$_REQUEST['vigencia'].
                            "&fechaEntrega=".$_REQUEST['fecha_pago'].
                            "&numeroFactura=".$_REQUEST['formulario'].
                            "&fechaEmisión=".$_REQUEST['fechafor'].
                            "&documentoExterno=".
                            "&fechaRec=".
                            "&exportacion[]=".$_REQUEST['tipo'].
                            "&liquidada[]=".$_REQUEST['contribuyente'].
                            "&importacion[]=".$_REQUEST['bimestre'].
                            "&contribuyente[]=1".
                            "&formaPago[]=2".
                            "&concepto=Recibo Oficial Caja ReteIca Manual".
                            "&tablaSoporteContable[]=-7".
                            "&tablaSoporteContable[]=0".
                            "&tablaSoporteContable[]=0".
                            "&tablaSoporteContable[]=1".
                            "&tablaSoporteContable[]=0".
                            "&tablaSoporteContable[]=".$_REQUEST['retIndustriaComercio14'].",00".
                            "&tablaSoporteContable[]=0,00".
                            "&tablaSoporteContable[]=".$_REQUEST['retIndustriaComercio14'].",00".
                            "&tablaSoporteContable[]=0,00".
                            "&tablaSoporteContable[]=0".
                            "&tablaSoporteContable[]=".$_REQUEST['tercero'].
                            "&tablaSoporteContable[]=".date('Y-m-d').
                            "&tablaSoporteContable[]=0".
                            "&tablaSoporteContable[]=0".
                            "&tablaSoporteContable[]=".
                            "&tablaSoporteContable[]=".
                            "&tablaSoporteContable[]=-8".
                            "&tablaSoporteContable[]=0".
                            "&tablaSoporteContable[]=0".
                            "&tablaSoporteContable[]=1".
                            "&tablaSoporteContable[]=0".
                            "&tablaSoporteContable[]=".$_REQUEST['autorretenciones'].",00".
                            "&tablaSoporteContable[]=0,00".
                            "&tablaSoporteContable[]=".$_REQUEST['autorretenciones'].",00".
                            "&tablaSoporteContable[]=0,00".
                            "&tablaSoporteContable[]=0".
                            "&tablaSoporteContable[]=".$_REQUEST['tercero'].
                            "&tablaSoporteContable[]=".date('Y-m-d').
                            "&tablaSoporteContable[]=0".
                            "&tablaSoporteContable[]=0".
                            "&tablaSoporteContable[]=".
                            "&tablaSoporteContable[]=".
                            "&tablaSoporteContable[]=-9".
                            "&tablaSoporteContable[]=0".
                            "&tablaSoporteContable[]=0".
                            "&tablaSoporteContable[]=1".
                            "&tablaSoporteContable[]=0".
                            "&tablaSoporteContable[]=".$_REQUEST['sanciones'].",00".
                            "&tablaSoporteContable[]=0,00".
                            "&tablaSoporteContable[]=".$_REQUEST['sanciones'].",00".
                            "&tablaSoporteContable[]=0,00".
                            "&tablaSoporteContable[]=0".
                            "&tablaSoporteContable[]=".$_REQUEST['tercero'].
                            "&tablaSoporteContable[]=".date('Y-m-d').
                            "&tablaSoporteContable[]=0".
                            "&tablaSoporteContable[]=0".
                            "&tablaSoporteContable[]=".
                            "&tablaSoporteContable[]=".
                            "&tablaSoporteContable[]=-10".
                            "&tablaSoporteContable[]=0".
                            "&tablaSoporteContable[]=0".
                            "&tablaSoporteContable[]=1".
                            "&tablaSoporteContable[]=0".
                            "&tablaSoporteContable[]=".$_REQUEST['interesesMoratorios'].",00".
                            "&tablaSoporteContable[]=0,00".
                            "&tablaSoporteContable[]=".$_REQUEST['interesesMoratorios'].",00".
                            "&tablaSoporteContable[]=0,00".
                            "&tablaSoporteContable[]=0".
                            "&tablaSoporteContable[]=".$_REQUEST['tercero'].
                            "&tablaSoporteContable[]=".date('Y-m-d').
                            "&tablaSoporteContable[]=0".
                            "&tablaSoporteContable[]=0".
                            "&tablaSoporteContable[]=".
                            "&tablaSoporteContable[]=".
                            "&tablaSoporteContable[]=-11".
                            "&tablaSoporteContable[]=0".
                            "&tablaSoporteContable[]=0".
                            "&tablaSoporteContable[]=1".
                            "&tablaSoporteContable[]=0".
                            "&tablaSoporteContable[]=".$_REQUEST['totalPagar'].",00".
                            "&tablaSoporteContable[]=0,00".
                            "&tablaSoporteContable[]=".$_REQUEST['totalPagar'].",00".
                            "&tablaSoporteContable[]=0,00".
                            "&tablaSoporteContable[]=0".
                            "&tablaSoporteContable[]=".$_REQUEST['tercero'].
                            "&tablaSoporteContable[]=".date('Y-m-d').
                            "&tablaSoporteContable[]=0".
                            "&tablaSoporteContable[]=0".
                            "&tablaSoporteContable[]=".
                            "&tablaSoporteContable[]=".
                            "&soporteCantidad=5,00".
                            "&soporteSubtotal=".$_REQUEST['totalPagar'].",00".
                            "&soporteDescuento=0,00".
                            "&soporteIva=0,00".
                            "&soporteTotal=".$_REQUEST['totalPagar'].",00";
                    } else {
                        $data = "soporteContable=". $_REQUEST['soporteContable'] .
                            "&documentoExterno=".
                            "&prefijoDocumentoExterno=".
                            "&tipoDocumentoContable=8".
                            "&prefijo[]=0".
                            "&consecutivo=1".
                            "&noAlmacen=0".
                            "&utilizado=$declaracion[0]".
                            "&declaracionCiudadano=0".
                            "&fechaDocumento=".date('Y-m-d').
                            "&empresaSeleccionada[]=".$_REQUEST['empresa'].
                            "&terceroElabora[]=".$_REQUEST['tercero'].
                            "&terceroRecibe[]=".$_REQUEST['tercero'].
                            "&prefijoFactura=".$_REQUEST['vigencia'].
                            "&fechaEntrega=".$_REQUEST['fecha_pago'].
                            "&numeroFactura=".$_REQUEST['formulario'].
                            "&fechaEmisión=".$_REQUEST['fechafor'].
                            "&documentoExterno=".$_REQUEST['numeroAcuerdo'].
                            "&fechaRec=".$_REQUEST['cfechaPago'].
                            "&exportacion[]=".$_REQUEST['cuota'].
                            "&liquidada[]=".$_REQUEST['cuotas'].
                            "&importacion[]=".$_REQUEST['valor'].
                            "&contribuyente[]=1".
                            "&formaPago[]=2".
                            "&concepto=Recibo Oficial Caja Manual".
                            "&tablaSoporteContable[]=-1".
                            "&tablaSoporteContable[]=0".
                            "&tablaSoporteContable[]=0".
                            "&tablaSoporteContable[]=1".
                            "&tablaSoporteContable[]=0".
                            "&tablaSoporteContable[]=".$_REQUEST['cimpuestoc'].",00".
                            "&tablaSoporteContable[]=0,00".
                            "&tablaSoporteContable[]=".$_REQUEST['cimpuestoc'].",00".
                            "&tablaSoporteContable[]=0,00".
                            "&tablaSoporteContable[]=0".
                            "&tablaSoporteContable[]=".$_REQUEST['tercero'].
                            "&tablaSoporteContable[]=".date('Y-m-d').
                            "&tablaSoporteContable[]=0".
                            "&tablaSoporteContable[]=0".
                            "&tablaSoporteContable[]=".
                            "&tablaSoporteContable[]=".
                            "&tablaSoporteContable[]=-2".
                            "&tablaSoporteContable[]=0".
                            "&tablaSoporteContable[]=0".
                            "&tablaSoporteContable[]=1".
                            "&tablaSoporteContable[]=0".
                            "&tablaSoporteContable[]=".$_REQUEST['cavisoc'].",00".
                            "&tablaSoporteContable[]=0,00".
                            "&tablaSoporteContable[]=".$_REQUEST['cavisoc'].",00".
                            "&tablaSoporteContable[]=0,00".
                            "&tablaSoporteContable[]=0".
                            "&tablaSoporteContable[]=".$_REQUEST['tercero'].
                            "&tablaSoporteContable[]=".date('Y-m-d').
                            "&tablaSoporteContable[]=0".
                            "&tablaSoporteContable[]=0".
                            "&tablaSoporteContable[]=".
                            "&tablaSoporteContable[]=".
                            "&tablaSoporteContable[]=-3".
                            "&tablaSoporteContable[]=0".
                            "&tablaSoporteContable[]=0".
                            "&tablaSoporteContable[]=1".
                            "&tablaSoporteContable[]=0".
                            "&tablaSoporteContable[]=".$_REQUEST['csobretasac'].",00".
                            "&tablaSoporteContable[]=0,00".
                            "&tablaSoporteContable[]=".$_REQUEST['csobretasac'].",00".
                            "&tablaSoporteContable[]=0,00".
                            "&tablaSoporteContable[]=0".
                            "&tablaSoporteContable[]=".$_REQUEST['tercero'].
                            "&tablaSoporteContable[]=".date('Y-m-d').
                            "&tablaSoporteContable[]=0".
                            "&tablaSoporteContable[]=0".
                            "&tablaSoporteContable[]=".
                            "&tablaSoporteContable[]=".
                            "&tablaSoporteContable[]=-4".
                            "&tablaSoporteContable[]=0".
                            "&tablaSoporteContable[]=0".
                            "&tablaSoporteContable[]=1".
                            "&tablaSoporteContable[]=0".
                            "&tablaSoporteContable[]=".$_REQUEST['csancionc'].",00".
                            "&tablaSoporteContable[]=0,00".
                            "&tablaSoporteContable[]=".$_REQUEST['csancionc'].",00".
                            "&tablaSoporteContable[]=0,00".
                            "&tablaSoporteContable[]=0".
                            "&tablaSoporteContable[]=".$_REQUEST['tercero'].
                            "&tablaSoporteContable[]=".date('Y-m-d').
                            "&tablaSoporteContable[]=0".
                            "&tablaSoporteContable[]=0".
                            "&tablaSoporteContable[]=".
                            "&tablaSoporteContable[]=".
                            "&tablaSoporteContable[]=-5".
                            "&tablaSoporteContable[]=0".
                            "&tablaSoporteContable[]=0".
                            "&tablaSoporteContable[]=1".
                            "&tablaSoporteContable[]=0".
                            "&tablaSoporteContable[]=".$_REQUEST['cinteresc'].",00".
                            "&tablaSoporteContable[]=0,00".
                            "&tablaSoporteContable[]=".$_REQUEST['cinteresc'].",00".
                            "&tablaSoporteContable[]=0,00".
                            "&tablaSoporteContable[]=0".
                            "&tablaSoporteContable[]=".$_REQUEST['tercero'].
                            "&tablaSoporteContable[]=".date('Y-m-d').
                            "&tablaSoporteContable[]=0".
                            "&tablaSoporteContable[]=0".
                            "&tablaSoporteContable[]=".
                            "&tablaSoporteContable[]=".
                            "&tablaSoporteContable[]=-6".
                            "&tablaSoporteContable[]=0".
                            "&tablaSoporteContable[]=0".
                            "&tablaSoporteContable[]=1".
                            "&tablaSoporteContable[]=0".
                            "&tablaSoporteContable[]=".$_REQUEST['cvalorTotal'].",00".
                            "&tablaSoporteContable[]=0,00".
                            "&tablaSoporteContable[]=".$_REQUEST['cvalorTotal'].",00".
                            "&tablaSoporteContable[]=0,00".
                            "&tablaSoporteContable[]=0".
                            "&tablaSoporteContable[]=".$_REQUEST['tercero'].
                            "&tablaSoporteContable[]=".date('Y-m-d').
                            "&tablaSoporteContable[]=0".
                            "&tablaSoporteContable[]=0".
                            "&tablaSoporteContable[]=".
                            "&tablaSoporteContable[]=".
                            "&soporteCantidad=5,00".
                            "&soporteSubtotal=".$_REQUEST['cvalorTotal'].",00".
                            "&soporteDescuento=0,00".
                            "&soporteIva=0,00".
                            "&soporteTotal=".$_REQUEST['cvalorTotal'].",00";
                    }


                    //echo $data;

                    // abrimos la sesión cURL
                    $ch = curl_init();
                    // definimos la URL a la que hacemos la petición
                    curl_setopt($ch, CURLOPT_URL, env('CURL_URL') . "app/componente/facturacion/modulos/insercionActualizacion.php");
                    // indicamos el tipo de petición: POST
                    curl_setopt($ch, CURLOPT_POST, true);
                    // definimos cada uno de los parámetros
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                    // recibimos la respuesta y la guardamos en una variable
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                    $remote_server_output = curl_exec($ch);
                    //echo "aaa";
                    // cerramos la sesión cURL
                    curl_close($ch);
                    echo $remote_server_output;
                    //echo json_encode($obligacion);
                }
                if (!(isset($_REQUEST['bimestre']) && isset($_REQUEST['retIndustriaComercio14']))) {
                    header("Location: " . str_replace("http://127.0.0.1:2032", "http://finanzas-test.contSoft.com.co:2032", str_replace("https://127.0.0.1", "https://finanzas.contSoft.com.co", $rutaAplicacion->rutaDocumentoAbsoluta)) . 'documento/' . $documento->getNombreDocumento() . '.pdf');                    
                } else {
                    echo "<script type='text/javascript'>window.close();</script>";
                    //echo "<script type='text/javascript'>setTimeout(function() {window.close();}, 1000);</script>";
                }
            } else {
                echo str_replace("http://127.0.0.1:2032", "http://finanzas-test.contSoft.com.co:2032", str_replace("https://127.0.0.1", "https://finanzas.contSoft.com.co", $rutaAplicacion->rutaDocumentoAbsoluta)) . 'documento/' . $documento->getNombreDocumento() . '.pdf';
            }
            //echo $rutaAplicacion->rutaDocumentoRelativa . 'documento/' . $documento->getNombreDocumento() . '.pdf';
        }
    } else {
       echo $rutaAplicacion->rutaDocumentoRelativa . 'documento/' . $documento->getNombreDocumento() . '.pdf';
    }
} else {
    /*$datos = '<pagos cpt="5010" tdoc="13" nid="1121853181" apl1="aaaaaaa" apl2="bbbbbbbbbb" nom1="ccccccccc" nom2="ddddddddd" dir="eeeeeeeeeee" dpto="91" mun="001" pais="169" pago="0" pnded="1" ided="2" inded="3" retp="4" reta="5" comun="6" simp="7" ndom="8" rcree="9" rasumc="10"/>
    <pagos cpt="5010" tdoc="31" nid="900451045" dv="0" raz="fgdfg" pais="059" pago="11" pnded="12" ided="13" inded="14" retp="15" reta="16" comun="17" simp="18" ndom="19" rcree="20" rasumc="1"/>
    <pagos cpt="5010" tdoc="31" nid="900451045" dv="0" raz="fgdfg" pais="059" pago="11" pnded="12" ided="13" inded="14" retp="15" reta="16" comun="17" simp="18" ndom="19" rcree="20" rasumc="21"/>';
    */
    $rlFormatoDian = $centralConsulta->rlFormatoDian($_REQUEST['formatoDian']);
    $datos = "";
    foreach ($rlFormatoDian as &$dato) {
        $datos .= "<pagos";
        foreach ($dato as $posicion=>$valor) {
            if (!is_int($posicion) && $posicion != "rl_formato_dian" && $posicion != "formato_dian") {
                $datos .= ' ' . $posicion . '="' . $valor . '"';
            }
        }
        $datos .= "/>\n";
    }
    $cantReg = COUNT($rlFormatoDian);
    //2017-05-04T18:47:19
    $dt = new DateTime();
    $dt->setTimeZone(new DateTimeZone('America/Bogota'));
    $fecha = $dt->format('Y-m-d\TH:i:s');
    $string = <<<XML
		<mas xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="../xsd/1001.xsd">
		<Cab>
		<Ano>2017</Ano>
		<CodCpt>1</CodCpt>
		<Formato>1001</Formato>
		<Version>9</Version>
		<NumEnvio>1</NumEnvio>
		<FecEnvio>$fecha</FecEnvio>
		<FecInicial>2017-01-01</FecInicial>
		<FecFinal>2017-12-31</FecFinal>
		<ValorTotal>10014</ValorTotal>
		<CantReg>$cantReg</CantReg>
		</Cab>
		$datos
		</mas>
		XML;
    $xml = new SimpleXMLElement($string);
    //$xml->addAttribute('encoding', 'UTF-8');
    //Header('Content-type: text/xml');
    //print($xml->asXML());
    $xml->asXML($rutaAplicacion->rutaDocumentoRelativa . 'xml/1001.xml');
    echo $rutaAplicacion->rutaDocumentoAbsoluta . 'xml/1001.xml';
}
/*
echo $rutaAplicacion->rutaDocumentoRelativa . 'documento/---'. $aux;
print "<pre>"; print_r($aux); print "</pre>\n";
*/
