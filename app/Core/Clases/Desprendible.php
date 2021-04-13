<?php
//namespace contSoft\Finanzas\Traits\Nomina\Orm;
/**
 * Desprendible short summary.
 *
 * Desprendible description.
 *
 * @version 1.0
 * @author byte
 */

error_reporting(E_ERROR);
include str_replace('/finanzas/finanzas/', '/finanzas/', str_replace('//', '/finanzas/', str_replace('///', '/',  $rutaAplicacion->rutaRelativa))) . 'app/public/lib/PHPWord-master/src/PhpWord/Autoloader.php';

\PhpOffice\PhpWord\Autoloader::register();
use PhpOffice\PhpWord\TemplateProcessor;
date_default_timezone_set('America/Bogota');
/**
 * Summary of Desprendible
 * llena las plantillas *.docx
 */
class Desprendible{

    public $error = "";
    //    public static $error = "";

    public  $file ="",
    $path = "",
    $url ="",
    $nombreDocumento,
    $templateWord,$rl,$plantilla;

    function Desprendible($nombrePlantilla){

        global $rutaAplicacion;
        $this->plantilla = $nombrePlantilla;
        $this->nombreDocumento = Desprendible::generarNombreDocumento();
        $this->error = "";
        $this->path = $rutaAplicacion->rutaDocumentoRelativa.'/plantilla/'.$nombrePlantilla;
        $this->url ="";
        $this->templateWord = new TemplateProcessor($this->path);
        $this->rl = $rutaAplicacion->rutaDocumentoRelativa. 'documento/';
        $this->file = $rutaAplicacion->rutaDocumentoRelativa. 'documento/' .$this->nombreDocumento . '.docx';
        $this->url = $rutaAplicacion->rutaDocumentoAbsoluta . 'documento/' . $this->nombreDocumento . '.pdf';

    }
    /**
     * Summary of getURl
     * @return string
     */
    function getURl(){return $this->url;}
    /**
     * Summary of getNombreDocumento
     * @return string
     */
    function getNombreDocumento(){return $this->nombreDocumento;}
    /**
     * Summary of llenarPlantilla
     * @param mixed $datos
     */
    function llenarPlantilla($datos){
        //print "<pre>"; print_r($_SESSION['reporte_nomina']); print "</pre>\n";
        foreach ($_SESSION['reporte_nomina'] as $key => $value)
        {
            if ($key != "apropiaciones2") {
                if (!is_array($value))
                    $this->templateWord->setValue($key, $value);

                else{
                    $j=1;
                    /**
                     * Filas dinamicas dependiendo del array
                     * */
                    $filas_dinamicas = sizeof($value);

                    if ($filas_dinamicas > 0)
                        $this->templateWord->cloneRow(key($value[0]), $filas_dinamicas);

                    foreach ($value as &$empleado)
                    {
                       
    if ($empleado === end($value)) {
      //  print "<pre>"; print_r($empleado); print "</pre>\n";
    }

                        /*$salud = 0;

                        if ($empleado["salud"] == "0") {
                            $empleado["salud"] = $empleado["appsd2"];
                        }*/
                        foreach ($empleado  as $key => $subDato)
                        {
                            /*
                            if ($key == "salud") {
                                $salud = $subDato;
                            }
                            if ($key == "appsd") {
                                $salud = $subDato;
                            } else {

                            }
                            */
                            $this->templateWord->setValue($key."#".$j ,$subDato);
                        }
                        $j++;
                    }
                }
            }
        }



    }
    function guardar(){

        $this->templateWord->saveAs($this->file);
        unset($_SESSION['reporte_nomina']);

    }
    /**
     * Summary of generarNombreDocumento
     * genera un nombre aleatorio para el documento
     * @return string
     */
    private function generarNombreDocumento(){
        switch ($this->plantilla) {
            case 'formato_reporte_nomina.docx':
                $nombreDocumento = $_SESSION['reporte_nomina']['anio']."-".$_SESSION["fecha_nomina"]."-".$_SESSION["empresa"]."-reporte";
                break;
            default:
                $t = microtime(true);
                $micro = sprintf("%06d",($t - floor($t)) * 1000000);
                $d = new DateTime( date('Y-m-d H:i:s.'.$micro, $t) );
                $nombreDocumento = sha1($d->format("Y-m-d H:i:s.u"));
        }
        return $nombreDocumento;
    }
    /**
     * Summary of respuesta
     * obtiene la ruta del pdf
     * @return string
     */
    function respuesta(){
        /**
         * Generar Pdf
         * */

        exec("libreoffice --invisible --convert-to pdf '" .  $this->file. "' --outdir " . $this->rl);
        exec('"C:\Program Files\LibreOffice 5\program\python.exe" C:\Users\usuario\Dropbox\finanzas\app\public\lib\unoconv-master\unoconv -f pdf -o C:\Users\usuario\Dropbox\docsAPP\factcontSoftDocumento\documento\\' . $this->nombreDocumento . '.pdf C:\Users\usuario\Dropbox\docsAPP\factcontSoftDocumento\documento\\' . $this->nombreDocumento . '.docx');
        exec('"C:\Program Files\LibreOffice 5\program\python.exe" C:\Users\contSoft\Dropbox\finanzas\app\public\lib\unoconv-master\unoconv -f pdf -o C:\Users\contSoft\Dropbox\docsAPP\factcontSoftDocumento\documento\\' . $this->nombreDocumento . '.pdf C:\Users\contSoft\Dropbox\docsAPP\factcontSoftDocumento\documento\\' . $this->nombreDocumento . '.docx');

/*
        if ($_SERVER['SERVER_PORT'] == "2031") {
        } else {
        }
 */
        //unlink($this->file);

        return $this->url;
    }
}