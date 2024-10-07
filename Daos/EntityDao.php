<?php


class EntityDao
{

    private $conexion;

    public function __construct()
    {
        $this->conexion = Conexion::getConexion();
    }


    //Método que obtiene el valor de un tabla en un array
    public function getEntity($tableName, $camposAQuitar)
    {

        $array = [];


        return $array;
    }
}
