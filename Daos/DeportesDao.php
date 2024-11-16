<?php

include_once 'EntityDao.php';


class DeportesDao extends EntityDao
{

    public function __construct()
    {

        parent::__construct();
    }

    //Función que devuelve todos los cursos disponibles que hay.
    public function getDeportes()
    {
        $deportes = [];
        $sql = "SELECT d.*, COUNT(p.id) AS cantidad_pistas from deportes d
                    LEFT JOIN pistas p ON p.idDeporte = d.id
                    GROUP BY d.id;";
    
        $resultado = $this->conexion->query($sql);

    
        if ($resultado->num_rows > 0) {
            while ($fila = $resultado->fetch_assoc()) {
                $deportes[] = $fila;
            }
        }
    
        return $deportes;
    }
    
}
