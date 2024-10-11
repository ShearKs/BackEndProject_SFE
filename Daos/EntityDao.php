<?php

class EntityDao
{
    private $conexion;

    public function __construct()
    {
        // Usamos la clase que maneja la conexión a la BDD
        $this->conexion = Conexion::getConexion();
    }

    // Método que obtiene el valor de una tabla
    public function getEntity($tabla, $camposAQuitar)
    {
        $entidad = [];

        // Hacemos la consulta a la base de datos
        $sql = "SELECT * FROM `$tabla`";
        //Hacemos la consulta sql
        $resultado = $this->conexion->query($sql);

        //Si hemos obtenido algo de la consulta
        if ($resultado->num_rows > 0) {

            //Cada registro obtenido
            while ($fila = $resultado->fetch_assoc()) {

                $entidad[] = $fila;
            }
        }

        return $entidad;
    }


    public function getById($id, $tabla, $camposAQuitar)
    {

        $entidad = $this->getEntity($tabla, $camposAQuitar);

        $valorRegistro = [];

        foreach ($entidad as $registro) {

            if (isset($registro['id'])  && $registro['id'] == $id) {
                $valorRegistro = $registro;
            }
        }

        return $valorRegistro;
    }

    //Función para obtener los campos de una entidad
    public function getFields()
    {
        return null;
    }

    //Método para insertar un registro en una tabla
    public function insertEntity($tabla, $data)
    {

        //Lo primero que hacemos es obtener los nombres de las columnas y los valores a isnertar
        //Ejemplo de lo que nos llega "nombre, email, contraseña"
        $columnas =  implode(', ', array_keys($data));

        //cadena con el número total de interrogaciones
        $placeholders = implode(", ", array_fill(0, count($data), "?"));

        //Preparamos la consulta
        $sql = "INSERT INTO $tabla ($columnas) VALUES ($placeholders)";
        //Preparamos la sentencia
        $sentencia = $this->conexion->prepare($sql);

        //Cadenad de los tipos para los bindParam
        $tipos = implode(', ', $this->tiposArray($data));


        //Vinculamos los parámetros
        $sentencia->bind_param($tipos, ...$data);

        //Ejecutamos la consulta 
        if ($sentencia->execute()) {

            //Retornamos id del nuevo registro insertado
            return $this->conexion->insert_id;
        } else {

            return new Exception("Error al insertar " . $sentencia->error);
        }
    }

    //Función que devuelve en un array los tipos de un array 
    private function tiposArray($data)
    {

        $tipos = [];

        foreach ($data as $indice => $valor) {

            if (is_int($valor)) {
                $tipos[] = 'i';
            } elseif (is_double($valor) || is_float($valor)) {
                $tipos[] = 'd';
            } elseif (is_string($valor)) {
                $tipos[] = 's';
            } elseif (is_null($valor)) {
                $tipos[] = 's';
            } else {
                throw new Exception('Error, tipo no soportado...' . gettype($valor));
            }
        }

        return $tipos;
    }

    public function deleteById($id, $nombreTabla)
    {

        $sql = "DELETE FROM $nombreTabla WHERE $id = ?";
        $sentencia = $this->conexion->prepare($sql);
        $sentencia->bind_param('i', $id);

        $eliminado = $sentencia->execute();

        return $eliminado;
    }
}
