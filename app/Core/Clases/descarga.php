<?php
	/************************************************************
	 * Rutina: descargar		*
	 *															*
	 * Autor: contSoft 											*
	 * Programador-Creador: Diego Soba							*
	 * Programador-Última Actualización: Diego Soba					*
	 *															*
	 * Descargar 		*
	 *															*
	 ***********************************************************/
	include_once '../configuracion.php';

	$directorio = $rutaAplicacion->rutaDocumentoRelativa."files/";
	$archivo = basename($_REQUEST['archivo']);
	$ruta = $directorio . $archivo;

	$type = '';

	if (is_file($ruta)) {
		$tamano = filesize($ruta);

		if (function_exists('mime_content_type')) {
			$type = mime_content_type($ruta);

		} else if (function_exists('finfo_file')) {
			$info = finfo_open(FILEINFO_MIME);
			$type = finfo_file($info, $ruta);
			finfo_close($info);

		}

		if ($type == '') {
			$type = 'application/force-download';
		}

		 header("Content-Type: $type");
		 header("Content-Disposition: attachment; filename=$archivo");
		 header("Content-Transfer-Encoding: binary");
		 header("Content-Length: $tamano");
		 readfile($ruta);
	}
?>