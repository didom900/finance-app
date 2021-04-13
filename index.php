<?php

/**
 * Raiz de la aplicación
 *
 * Estructura de las cabeceras del documento HTML, parte del cuerpo HTML donde se muestra el contenido, cargue del CSS y JS necesarios, y clases comunes de la aplicación
 *
 * @copyright  2017 - Diego Soba.
 * @author     Diego Soba <didom900@gmail.com>
 * @version    1.0
 */
use contSoft\Finanzas\Facades\Config;
use contSoft\Finanzas\Clases\Helpers\Helper;

require_once __DIR__.'/bootstrap/autoload.php';

$auth_empresa = $_SESSION['empresa'] ?? 0;
$type_empresa = $_SESSION['tipoEmpresa'] ?? 0;


?>
<!doctype html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <meta content="Autor" name="contSoft Soluciones Informaticas S.A.S."/>
        <link href="<?php echo $rutaAplicacion->rutaAbsoluta."app/public/imagenes/favicon.png"; ?>" rel="shortcut icon" type="image/x-icon" />

        <!-- external Libreries -->
        <link rel="stylesheet" type="text/css" href="<?php echo $rutaAplicacion->rutaAbsoluta; ?>app/public/lib/libreriasExternas/DataTables-1.10.3/media/css/jquery.dataTables.min.css">
        <link rel="stylesheet" type="text/css" href="<?php echo $rutaAplicacion->rutaAbsoluta; ?>app/public/lib/libreriasExternas/DataTables-1.10.3/extensions/KeyTable/css/dataTables.keyTable.min.css">
        <link rel="stylesheet" type="text/css" href="<?php echo $rutaAplicacion->rutaAbsoluta; ?>app/public/lib/libreriasExternas/DataTables-1.10.3/extensions/Responsive/css/dataTables.responsive.css">
        <link rel="stylesheet" type="text/css" href="<?php echo $rutaAplicacion->rutaAbsoluta; ?>app/public/lib/libreriasExternas/DataTables-1.10.3/extensions/TableTools/css/dataTables.tableTools.min.css">
        <link rel="stylesheet" type="text/css" href="<?php echo $rutaAplicacion->rutaAbsoluta; ?>app/public/lib/libreriasExternas/fullcalendar-2.1.1/fullcalendar.min.css">
        <link rel="stylesheet" type="text/css" href="<?php echo $rutaAplicacion->rutaAbsoluta; ?>app/public/lib/libreriasExternas/fullcalendar-2.1.1/fullcalendar.print.css" media='print'>
        <link rel="stylesheet" type="text/css" href="<?php echo $rutaAplicacion->rutaAbsoluta; ?>app/public/lib/libreriasExternas/notify/dist/styles/metro/notify-metro.css">
        <link rel="stylesheet" type="text/css" href="<?php echo $rutaAplicacion->rutaAbsoluta; ?>app/public/lib/libreriasExternas/chosen_v1.1.0/chosen.min.css">
        <link rel="stylesheet" type="text/css" href="<?php echo $rutaAplicacion->rutaAbsoluta; ?>app/public/lib/libreriasExternas/uploadify/uploadify.css"/>
        <link rel="stylesheet" type="text/css" href="<?php echo $rutaAplicacion->rutaAbsoluta; ?>app/public/lib/libreriasExternas/dreamerslab-jquery.msg-1512fa1/jquery.msg.css" media="screen" >
        <link rel="stylesheet" type="text/css" href="<?php echo $rutaAplicacion->rutaAbsoluta; ?>node_modules/select2/dist/css/select2.min.css"/>
        <link rel="stylesheet" type="text/css" href="<?php echo $rutaAplicacion->rutaAbsoluta; ?>app/public/lib/libreriasExternas/framewarp/assets/framewarp/framewarp.css" />
        <link rel="stylesheet" type="text/css" href="<?php echo $rutaAplicacion->rutaAbsoluta; ?>app/public/css/font-awesome.min.css">
        <link rel="stylesheet" type="text/css" href="<?php echo $rutaAplicacion->rutaAbsoluta; ?>app/public/css/normalize.css">
        <link rel="stylesheet" type="text/css" href="<?php echo $rutaAplicacion->rutaAbsoluta; ?>app/public/css/estilo.css">
        <link rel="stylesheet" type="text/css" href="<?php echo $rutaAplicacion->rutaAbsoluta; ?>app/public/css/stock/animacion.css">

        <title>Finanzas-JEMP</title>
    </head>
    <body>
        <div class="loading" style="display:none;"></div>
        
        <div class="contentGeneral">
            <?php
                if (!isset($_REQUEST["id"])) {
                    $_REQUEST["id"] = "";
                }
                /*
                |--------------------------------------------------------------------------
                | Caso Ya Inicio Sesión
                |--------------------------------------------------------------------------
                */
                if ($sesionAplicacion->existeSesion()) {
                    include $rutaAplicacion->rutaRelativa . "app/componente/estructura/estructura.php";
                }
                /*
                |--------------------------------------------------------------------------
                | Caso Cambiar Clave
                |--------------------------------------------------------------------------
                */
                elseif (isset($_GET['nuevo'])) {
                    include $rutaAplicacion->rutaRelativa . "app/componente/usuario/vistas/nuevaClave.php";
                }
                /*
                |--------------------------------------------------------------------------
                | Caso Inicio de Sesión
                |--------------------------------------------------------------------------
                */
                else {
                    include $rutaAplicacion->rutaRelativa . "app/componente/estructura/inicioSesion.php";
                    $sesionAplicacion->cerrarSesion();
                }
                
                $mostrarFooter = 0;
                if (isset($_SESSION['empresa'])) {
                    if ($_SESSION['empresa'] != "3") {
                        $mostrarFooter = 1;
                    }
                }
            ?>
            <div class="push"></div>
        </div>
        
        <footer id="piePagina">
            <?php if ($mostrarFooter == 1): ?>
                <div>
                    <small>Copyright &copy; 2017 Diego - Soba ALL RIGHTS RESERVED.</small>
                </div>
                <div>
                    <span>Un producto de:</span>
                    <figure>
                        <img src="<?php echo $rutaAplicacion->rutaAbsoluta; ?>app/public/imagenes/logocontSoft.png" alt="Logo de contSoft" width="200" height="60" />
                    </figure>
                </div>
            <?php else: ?>
                <div>
                    <small></small>
                </div>
                <div>
                    <span></span>
                    <figure>
                        <img width="200" height="60" />
                    </figure>
                </div>
            <?php endif; ?>
        </footer>
        
        <input type="hidden" id="ventanaPrincipal">
        <input type="hidden" id="idSubVentana" name="idSubVentana" value="<?php echo $_REQUEST["id"]; ?>">
        <input type="hidden" id="raiz" name="raiz" value="<?php echo Config::get('app.RAIZ'); ?>" />
        <input type="hidden" id="api" name="api" value="<?php echo Config::get('app.API_URL'); ?>" />
        <input type="hidden" id="actual" name="actual" value="***vistas/index.php+++">
        <input type="hidden" id="anterior" name="anterior" value="anterior">
        <input type="hidden" id="rolUsuarioSistema" name="rolUsuarioSistema" value="<?php echo $_SESSION['rol']; ?>">
        <input type="hidden" id="auth_empresa" name="auth_empresa" value="<?php echo $auth_empresa; ?>" />
        <input type="hidden" id="type_empresa" name="type_empresa" value="<?php echo $type_empresa; ?>" />

        <script src="<?php echo $rutaAplicacion->rutaAbsoluta; ?>app/public/lib/libreriasExternas/jquery-2.1.1.min.js" charset="utf-8"></script>
        <script src="<?php echo $rutaAplicacion->rutaAbsoluta; ?>app/public/lib/libreriasExternas/dreamerslab-jquery.msg-1512fa1/jquery.center.min.js"></script>
        <script src="<?php echo $rutaAplicacion->rutaAbsoluta; ?>app/public/lib/libreriasExternas/dreamerslab-jquery.msg-1512fa1/jquery.msg.js" charset="utf-8"></script>
        <script src="<?php echo $rutaAplicacion->rutaAbsoluta; ?>app/public/lib/libreriasExternas/chosen_v1.1.0/chosen.jquery.min.js" charset="utf-8" defer></script>
        <script src="<?php echo $rutaAplicacion->rutaAbsoluta; ?>app/public/lib/libreriasExternas/DataTables-1.10.3/media/js/jquery.dataTables.min.js" type="text/javascript" charset="utf-8" defer></script>
        <script src="<?php echo $rutaAplicacion->rutaAbsoluta; ?>app/public/lib/libreriasExternas/DataTables-1.10.3/extensions/KeyTable/js/dataTables.keyTable.js" type="text/javascript" charset="utf-8" defer></script>
        <script src="<?php echo $rutaAplicacion->rutaAbsoluta; ?>app/public/lib/libreriasExternas/DataTables-1.10.3/extensions/Responsive/js/dataTables.responsive.js" type="text/javascript" charset="utf-8" defer></script>
        <script src="<?php echo $rutaAplicacion->rutaAbsoluta; ?>app/public/lib/libreriasExternas/DataTables-1.10.3/extensions/TableTools/js/dataTables.tableTools.min.js" type="text/javascript" charset="utf-8" defer></script>
        <script src="<?php echo $rutaAplicacion->rutaAbsoluta; ?>app/public/lib/libreriasExternas/fullcalendar-2.1.1/lib/moment.min.js" type="text/javascript" charset="utf-8" defer></script>
        <script src="<?php echo $rutaAplicacion->rutaAbsoluta; ?>app/public/lib/libreriasExternas/fullcalendar-2.1.1/fullcalendar.min.js" type="text/javascript" charset="utf-8" defer></script>
        <script src="<?php echo $rutaAplicacion->rutaAbsoluta; ?>app/public/lib/libreriasExternas/fullcalendar-2.1.1/lang-all.js" type="text/javascript" charset="utf-8" defer></script>
        <script src="<?php echo $rutaAplicacion->rutaAbsoluta; ?>app/public/lib/libreriasExternas/fullcalendar-2.1.1/gcal.js" type="text/javascript" charset="utf-8" defer></script>
        <script src="<?php echo $rutaAplicacion->rutaAbsoluta; ?>app/public/lib/libreriasExternas/notify/dist/notify.js" type="text/javascript" charset="utf-8" defer></script>
        <script src="<?php echo $rutaAplicacion->rutaAbsoluta; ?>app/public/lib/libreriasExternas/notify/dist/styles/metro/notify-metro.js" type="text/javascript" charset="utf-8" defer></script>
        <script src="<?php echo $rutaAplicacion->rutaAbsoluta; ?>app/public/lib/libreriasExternas/shortcuts/shortcuts.js" type="text/javascript" charset="utf-8" defer></script>
        <script src="<?php echo $rutaAplicacion->rutaAbsoluta; ?>app/public/lib/libreriasExternas/uploadify/jquery.uploadify.min.js"></script>
        <script src="<?php echo $rutaAplicacion->rutaAbsoluta; ?>node_modules/jquery-knob/dist/jquery.knob.min.js" charset="utf-8"></script>
        <script src="<?php echo $rutaAplicacion->rutaAbsoluta; ?>node_modules/select2/dist/js/select2.min.js"></script>
        <script src="<?php echo $rutaAplicacion->rutaAbsoluta; ?>node_modules/select2/dist/js/i18n/es.js"></script>
        <script src="<?php echo $rutaAplicacion->rutaAbsoluta; ?>node_modules/chart.js/dist/Chart.min.js"></script>
        <script src="<?php echo $rutaAplicacion->rutaAbsoluta; ?>app/public/lib/libreriasExternas/framewarp/assets/js/jquerypp.custom.js"></script>
        <script src="<?php echo $rutaAplicacion->rutaAbsoluta; ?>app/public/lib/libreriasExternas/framewarp/assets/framewarp/framewarp.js"></script>
        
        <!-- <script src="https://www.gstatic.com/firebasejs/5.0.4/firebase.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js" charset="utf-8" defer></script> -->
        
        <script src="<?php echo $rutaAplicacion->rutaAbsoluta; ?>app/public/js/controladorValidacion.js" defer></script>
        <script src="<?php echo $rutaAplicacion->rutaAbsoluta; ?>app/public/js/controladorPrincipal.js" charset="utf-8" defer></script>

        <script type="text/javascript">
            if (window.history && window.history.pushState) {
                $(window).on('popstate', function(event) {
                    var hashLocation = location.hash;
                    var hashSplit = hashLocation.split("#!/");
                    var hashName = hashSplit[1];
                    if (hashName !== '') {
                        var hash = window.location.hash;
                        if (hash === '') {
                            if ($("#anterior").val() != 'anterior') {
                                var datos = $("#anterior").val();
                                var ext = datos.split('***');
                                
                                if (!event.state) {
                                    // alert("dd");
                                } else {
                                    $("#actual").val('***' + ext[ext.length -1]);
                                    $("#anterior").val("vistas/index.php+++");
                                    for (i = 1; i < ext.length -1; i++) {
                                        $("#anterior").val($("#anterior").val() + '***' + ext[i]);
                                    }
                                    //$("#anterior").val($("#anterior").val() + $("#actual").val());
                                    var datos2 = ext[ext.length -1];
                                    var ext2 = datos2.split('+++');
                                    respuestaAjax("workspace", ext2[1], 'html', '0', true, ext2[0], true, false);

                                    var datos3 = ext2[1];
                                    var ext3 = datos3.split('&search=');

                                    if (ext3[1] != "") {
                                        setTimeout(function() {  //Beginning of code that should run AFTER the timeout
                                            $('input[type="search"]').val(ext3[1]);
                                            table.api().search(ext3[1]).draw();
                                        }, 1000);
                                    }
                                    window.history.pushState('forward', null, './');
                                }
                            }
                        }
                    }
                })
                
                //window.location.hash = "";
                window.history.pushState('forward', null, './');
            }

            //alert(window.name);
            //console.log(screen);
            //document.getElementById('screen').innerHTML = window.innerWidth + "-" + window.innerHeight;
        </script>

    </body>
</html>
