<?php

/**
 * ValidarCampos short summary.
 *
 * ValidarCampos description.
 *
 * @version 1.0
 * @author byte
 */
namespace contSoft\Finanzas\Clases;

class ValidarCampos
{
    public static function getSelector($tabla,$campo) {
        include_once '../configuracion.php';

        $sql = "SELECT tp.selector AS selector
                        FROM campo c, campo_formulario cp, tipo_campo tp
	                    WHERE cp.tipo_campo = tp.tipo_campo AND cp.campo = c.campo AND c.tabla_destino = '$tabla' AND c.campo_destino = '$campo';";
        $resultado = $conexion->getDBCon()->Execute($sql);

        return $resultado->fields['selector'];
    }
    public static function LimpiarDatos($array,$tabla){

        $res=array();
        foreach ($array as $key => $value){

            switch (ValidarCampos::getSelector($tabla,$key))
            {
                case 'number':
                    $res[$key] = (int)$array[$key];
                    break;
                case 'date':
                    $res[$key] = $array[$key]==""?null:$array[$key];
                    break;

            	default:
                    $res=$array[$key];
                    break;
            }
        }
        return $res;
    }

}

echo ( ValidarCampos::getSelector('nomina_contrato','responsable'));