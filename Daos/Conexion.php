<?php

class Conexion
{
    // La propia conexión de la base de datos...
    private static $conexion;

    public function __construct()
    {
        $host = "localhost";
        $usuario = "root";
        $nombreBdd = "bdd_project_sfe";
        $contrasenaBdd = "";

        self::$conexion = new mysqli($host, $usuario, $contrasenaBdd, $nombreBdd);
        self::$conexion->set_charset("utf8");

        if (self::$conexion->connect_errno) {
            echo 'Error conectando con la base de datos, error: ' . self::$conexion->connect_error;
            exit();
        }
    }

    public static function getConexion()
    {
        // Verificar si la conexión ha sido establecida
        if (self::$conexion === null) {
            // Si no hay conexión, crear una nueva instancia de Conexion
            new self(); // Esto llamará al constructor y establecerá la conexión
        }
        return self::$conexion;
    }

    public function cerrarConexion()
    {
        self::$conexion->close();
    }
}
