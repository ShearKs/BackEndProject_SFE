<?php

class EntityDao
{
    private $conexion;

    public function __construct()
    {
        // Usamos la clase que maneja la conexión a la BDD
        $this->conexion = Conexion::getConexion();
    }



    public function getEntity($tabla, $camposAQuitar = [], $completo)
    {
        $entidad = [];

        // Hacemos la consulta a la base de datos para obtener los datos de la tabla principal
        $sql = "SELECT * FROM `$tabla`";
        $resultado = $this->conexion->query($sql);

        // Si hemos obtenido algo de la consulta
        if ($resultado->num_rows > 0) {
            while ($fila = $resultado->fetch_assoc()) {
                $entidad[] = $fila;
            }
        }

        // Si queremos obtener también los datos de las relaciones
        if ($completo) {
            // Obtener las relaciones de la tabla usando getRelations
            $relaciones = $this->getRelations($tabla);

            // Para cada relación, hacer una consulta para obtener los datos relacionados
            foreach ($relaciones as $relacionItem) {
                if (isset($relacionItem['columna_relacion'], $relacionItem['tabla_relacionada'], $relacionItem['columna_referenciada'])) {
                    $columna = $relacionItem['columna_relacion'];
                    $tablaRelacionada = $relacionItem['tabla_relacionada'];
                    $columnaReferenciada = $relacionItem['columna_referenciada'];

                    // Obtener los datos relacionados para cada registro en la tabla principal
                    foreach ($entidad as &$registro) {
                        if (isset($registro[$columna])) {
                            $valorReferencia = $registro[$columna];

                            // Consulta para obtener los datos de la tabla relacionada
                            $sqlRelacion = "SELECT * FROM `$tablaRelacionada` WHERE `$columnaReferenciada` = '$valorReferencia'";
                            $resultadoRelacion = $this->conexion->query($sqlRelacion);

                            if ($resultadoRelacion && $resultadoRelacion->num_rows > 0) {
                                // Combinar los datos de la tabla relacionada con los datos de la tabla principal
                                while ($filaRelacion = $resultadoRelacion->fetch_assoc()) {
                                    foreach ($filaRelacion as $key => $value) {
                                        // Prefijar el nombre de la tabla relacionada al nombre del campo para evitar conflictos
                                        $registro["{$tablaRelacionada}_{$key}"] = $value;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return $entidad;
    }



    public function getById($id, $tabla, $camposAQuitar)
    {

        $entidad = $this->getEntity($tabla, $camposAQuitar, false);

        $valorRegistro = [];

        foreach ($tabla as $registro) {

            if (isset($registro['id'])  && $registro['id'] == $id) {
                $valorRegistro = $registro;
            }
        }

        return $valorRegistro;
    }

    //Función que se encarga de obtener todas las relaciones de una tabla...
    private function getRelations($tabla)
    {
        // Array de relaciones
        $relaciones = [];

        // Consulta para obtener tanto las relaciones salientes como las entrantes
        $sql = "
        SELECT 
            COLUMN_NAME, 
            REFERENCED_TABLE_NAME, 
            REFERENCED_COLUMN_NAME, 
            'saliente' AS tipo_relacion 
        FROM 
            information_schema.KEY_COLUMN_USAGE 
        WHERE 
            TABLE_NAME = '$tabla' 
            AND TABLE_SCHEMA = 'BDD_PROJECT_SFE' 
            AND REFERENCED_TABLE_NAME IS NOT NULL
        UNION
        SELECT 
            COLUMN_NAME, 
            TABLE_NAME AS REFERENCED_TABLE_NAME, 
            REFERENCED_COLUMN_NAME, 
            'entrante' AS tipo_relacion 
        FROM 
            information_schema.KEY_COLUMN_USAGE 
        WHERE 
            REFERENCED_TABLE_NAME = '$tabla' 
            AND TABLE_SCHEMA = 'BDD_PROJECT_SFE' 
            AND REFERENCED_TABLE_NAME IS NOT NULL";

        // Ejecutamos la consulta
        $resultado = $this->conexion->query($sql);

        // Si en esa tabla se encuentran relaciones
        if ($resultado && $resultado->num_rows > 0) {
            while ($fila = $resultado->fetch_assoc()) {
                // Agregamos la relación al array
                $relaciones[] = [
                    'tabla_relacionada' => $fila['REFERENCED_TABLE_NAME'],
                    'columna_relacion' => $fila['COLUMN_NAME'],
                    'columna_referenciada' => $fila['REFERENCED_COLUMN_NAME'],
                    'tipo_relacion' => $fila['tipo_relacion'] // 'saliente' o 'entrante'
                ];
            }
        }

        return $relaciones;
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
