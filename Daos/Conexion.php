<?php

class Conexion
{

    //La propia conexión de la base de datos...
    private static $conexion;

    public function __construct()
    {

        $host = "localhost";
        $usuario = "root";
        $nombreBdd = "bdd_project_sfe";
        $contrasenaBdd = "";

        self::$conexion = new mysqli($host, $usuario, $contrasenaBdd, $nombreBdd);
        self::$conexion->set_charset("utf8");

        $this->conexion->set_charset("utf8");

        if ($this->conexion->connect_errno) {
            echo 'Error conectando con la base de datos ,error: '
                . $this->conexion->connect_error;
            exit();
        }
    }
    public static function getConexion()
    {

        return self::$conexion;
    }

    public function cerrarConexion()
    {

        self::$conexion->close();
    }
}
