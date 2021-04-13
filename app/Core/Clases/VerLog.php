<?php
/**
 * Leer logs
 *
 * Clase para leer y Escribir Logs los logs del sistema de manera facil.
 *
 * @copyright  2017 - Diego Soba.
 * @author     Diego Soba <didom900@gmail.com>
 * @version    v.1.0.0
 */
namespace contSoft\Finanzas\Clases;

use Psr\Log\LogLevel;

class VerLog
{
    /**
     * @var string file
     */
    private static $file;

    private static $levels_classes = [
        'debug' => 'Debug',
        'info' => 'Info',
        'notice' => 'Info',
        'warning' => 'Advertencia',
        'error' => 'Error',
        'critical' => 'Error',
        'alert' => 'Error',
        'emergency' => 'Error',
        'processed' => 'Info',
    ];
    private static $levels_imgs = [
        'debug' => 'info',
        'info' => 'info',
        'notice' => 'info',
        'warning' => 'warning',
        'error' => 'warning',
        'critical' => 'warning',
        'alert' => 'warning',
        'emergency' => 'warning',
        'processed' => 'info'
    ];

    /**
     * Niveles del Log a Usar
     * @var array
     */
    private static $log_levels = [
        'emergency',
        'alert',
        'critical',
        'error',
        'warning',
        'notice',
        'info',
        'debug',
        'processed'
    ];

    const MAX_FILE_SIZE = 52428800;

    /**
     * Compra
     * @param string $file
     */
    public static function setFile($file)
    {

        $file = self::pathToLogFile($file);
        if (file_exists ($file)) {
            self::$file = $file;
        }
    }

    /**
     *
     * @param string $file
     * @return string
     * @throws \Exception
     */
    public static function pathToLogFile($file)
    {
        $logsPath = (__DIR__.'/../../../storage/log/user');

        if (file_exists ($file)) {
            return $file;
        }

        $file = $logsPath . '/' . $file;

        if (dirname($file) !== $logsPath) {
            throw new \Exception('No existe Ningun Archivo de Logs');
        }
        return $file;
    }

    /**
     *
     * @return string
     */
    public static function getFileName($file)
    {
        return basename($file);
    }

    /**
     * @return array
     */
    public static function all($loggerFile)
    {

        $loggerFile = self::pathToLogFile($loggerFile);
        $log = array();
        $pattern = '/\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\].*/';

        // if (filesize($loggerFile) > self::MAX_FILE_SIZE) return null;
        $file = file_get_contents($loggerFile);

        preg_match_all($pattern, $file, $headings);
        if (!is_array($headings)) return $log;
        $log_data = preg_split($pattern, $file);
        if ($log_data[0] < 1) {
            array_shift($log_data);
        }
        foreach ($headings as $h) {
            for ($i=0, $j = count($h); $i < $j; $i++) {
                foreach (self::$log_levels as $level) {
                    if (strpos(strtolower($h[$i]), '.' . $level) || strpos(strtolower($h[$i]), $level . ':')) {
                        preg_match('/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\](?:.*?(\w+)\.|.*?)' . $level . ': (.*?)( in .*?:[0-9]+)?$/i', $h[$i], $current);
                        if (!isset($current[3])) continue;
                        $mensaje = explode("{", $current[3]);
                        $current[3] = str_replace($mensaje[0], "", $current[3]);

                        $log[] = array(
                            'context' => $current[2],
                            'level' => $level,
                            'level_class' => self::$levels_classes[$level],
                            'level_img' => self::$levels_imgs[$level],
                            'date' => $current[1],
                            'text' => $mensaje[0],
                            'json' => $current[3],
                            'in_file' => isset($current[4]) ? $current[4] : null,
                            'stack' => preg_replace("/^\n*/", '', $log_data[$i])
                        );
                    }
                }
            }
        }
        return array_reverse($log);
    }
    /**
     * @param bool $basename
     * @return array
     */
    public static function getFiles($basename = false)
    {

        $files = glob(__DIR__.'/../../../storage/log/user/*.log');
        $files = array_reverse($files);
        $files = array_filter($files, 'is_file');
        if ($basename && is_array($files)) {
            foreach ($files as $k => $file) {
                $files[$k] = basename($file);
            }
        }
        return array_values($files);
    }
}