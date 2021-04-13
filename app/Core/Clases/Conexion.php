<?php
    /**
     * Conexión a la base de datos de la aplicación
     *
     * Utilizamos la librería de abstracción de datos ADODB para mayor portabilidad de la aplicación
     *
     * @copyright  2017 - Diego Soba.
     * @author     Diego Soba <didom900@gmail.com>
     * @version    1.0
     */
    namespace contSoft\Finanzas\Clases;

    use contSoft\Finanzas\Facades\Crypt;

    class Conexion {
        private $conexion = [];
        private $dbCon;

        public function __construct($conexion) {
            $this->conexion = $conexion;
            $this->dbCon = NewADOConnection(Crypt::decrypt($this->conexion['DB_CONN']));
        }

        public function conectar() {
            $this->dbCon->Connect($this->conexion['DB_HOST'].":".$this->conexion['DB_PORT'], $this->conexion['DB_USER'], $this->conexion['DB_PASS'], $this->conexion['DB_NAME']);
        }

        public function getDBCon() {
            return $this->dbCon;
        }

        public function ErrorMsg() {
            return $this->dbCon->ErrorMsg();
        }

        public function StartTrans() {
            return $this->dbCon->StartTrans();
        }

        public function query($sql, array $data =[])
        {
            $resultado = $this->getDBCon()->Execute($sql,$data);

            return $resultado;
        }

        public function CompleteTrans() {
            return $this->dbCon->CompleteTrans();
        }

        public function FailTrans() {
            return $this->dbCon->FailTrans();
        }

        public function cerrar() {
            return $this->dbCon->Close();
        }
    }

?>
