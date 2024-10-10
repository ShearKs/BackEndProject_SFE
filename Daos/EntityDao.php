<?php

class EntityDao
{
    private $conexion;

    public function __construct()
    {
        $this->conexion = Conexion::getConexion();  // Usamos la clase que maneja la conexión a la BDD
    }

    // Método que obtiene el valor de una tabla
    public function getEntity($tableName, $camposAQuitar)
    {
        $array = [];
        
        // Hacemos la consulta a la base de datos
        $sql = "SELECT * FROM usuario ";
        $resultado = $this->conexion->query($sql);

        if ($resultado->num_rows > 0) {
            while ($fila = $resultado->fetch_assoc()) {
                $array[] = $fila;
            }
        }

        return $array;
    }
}
