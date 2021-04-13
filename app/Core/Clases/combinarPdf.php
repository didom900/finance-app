<?php
    ini_set('max_execution_time', 500000);
    ini_set('memory_limit', '1024M');
    ini_set('display_errors',1);
    error_reporting(1);
    require_once '../../../bootstrap/autoload.php';

    if (!isset($_REQUEST['generar'])) {
        include_once '../../public/lib/PDFMerger/PDFMerger.php';
        $array = explode("***", $_REQUEST["words"]);
        $archivos = array();

        $t = microtime(true);
        $micro = sprintf("%06d",($t - floor($t)) * 1000000);
        $d = new DateTime( date('Y-m-d H:i:s.'.$micro, $t) );
        $nombreDocumento = sha1($d->format("Y-m-d H:i:s.u"));

        $pdf = new PDFMerger;

        for ($i = 1, $tamano = sizeof($array); $i < $tamano; $i++) {
            $archivos[$i - 1] = $array[$i];
            $pdf->addPDF(trim($archivos[$i - 1]), 'all');
        }
        //echo $rutaAplicacion->rutaDocumentoRelativa."****************";
        $pdf->merge('file', $rutaAplicacion->rutaDocumentoRelativa . 'documento/' . $nombreDocumento . '.pdf');
        if (!isset($_REQUEST['mostrar'])) {
            echo $nombreDocumento;
        }
    } 
    if (isset($_REQUEST['generar'])) {
        $nombreDocumento = $_REQUEST["words"];
        $nombreDocumento = trim($nombreDocumento);
        echo $rutaAplicacion->rutaDocumentoAbsoluta . 'documento/' . $nombreDocumento . '.pdf';
    }
    if (isset($_REQUEST['mostrar'])) {
        echo $rutaAplicacion->rutaDocumentoAbsoluta . 'documento/' . $nombreDocumento . '.pdf';
    }
?>