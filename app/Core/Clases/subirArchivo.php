<?php
    /**
     * Subir archivos al servidor
     *
     * Todo el funcionamiento para la subida de archivos al servidor
     *
     * @copyright  2017 - Diego Soba.
     * @author     Diego Soba <didom900@gmail.com>
     * @version    2.0
     */
    include_once '../configuracion.php';

    $error = false;

    if ($sesionAplicacion->existeSesion()) {

        // Existe la variable de archivo.
        if (isset($_FILES['archivo'])) {

            // Subida de uno (1) archivo.
            if (!is_array($_FILES['archivo']['name'])) {
                $extension = @end(explode('.', $_FILES['archivo']['name']));
                $nombreArchivo = sha1(date('Y-m-d H:i:s')) . '.' . $extension;

                // Movemos el archivo al servidor.
                if (move_uploaded_file($_FILES['archivo']['tmp_name'], $rutaAplicacion->rutaDocumentoRelativa. $_REQUEST['carpeta'] . '/' . $nombreArchivo)) {
                    echo json_encode(Array('estado' => 0, 'nombreArchivo' => $nombreArchivo));  // Todo ok.

                } else {
                    echo json_encode(Array('estado' => 2, 'nombreArchivo' => 0));  // Algo ocurrio.
                }

            // Subida múltiple de archivos.
            } else {

                for ($i = 0, $tamano = sizeof($_FILES['archivo']['name']); $i < $tamano; $i++) {
                    $extension = @end(explode('.', $_FILES['archivo']['name'][$i]));
                    $nombreArchivo = sha1(date('Y-m-d H:i:s')) . '.' . $extension;

                    // Movemos el archivo al servidor.
                    if (move_uploaded_file($_FILES['archivo']['tmp_name'][$i], $rutaAplicacion->rutaDocumentoRelativa. $_REQUEST['carpeta'] . '/' . $nombreArchivo)) {
                       $error = false;

                    } else {
                        $error = true;
                        break;
                    }
                }

                if ($error == false) {
                    echo json_encode(Array('estado' => 0));  // Todo ok.

                } else {
                    echo json_encode(Array('estado' => 2, 'nombreArchivo' => 0));  // Algo ocurrio.
                }

            }

        } else {
            echo json_encode(Array('estado' => 1, 'nombreArchivo' => 0));  // Esto no deberia suceder.
        }

    } else {
        echo header('HTTP/1.1 403 Forbidden');
    }
 ?>