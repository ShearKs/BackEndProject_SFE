<?php

include_once 'Conexion.php';

class EntityDao
{
    protected $conexion;

    public function __construct()
    {
        // Usamos la clase que maneja la conexión a la BDD
        $this->conexion = Conexion::getConexion();

        //De forma predterminada no podremos hacer cambios...
        //$this->conexion->autocommit(false);
    }

    public function getEntity($tabla, $completo)
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
                    $tipoRelacion = $relacionItem['tipo_relacion'];

                    // Obtener los datos relacionados para cada registro en la tabla principal
                    foreach ($entidad as &$registro) {
                        if ($tipoRelacion == 'saliente' && isset($registro[$columna])) {
                            // Si la relación es saliente, buscar en la tabla relacionada usando el valor del registro actual
                            $valorReferencia = $registro[$columna];
                            $sqlRelacion = "SELECT * FROM `$tablaRelacionada` WHERE `$columnaReferenciada` = '$valorReferencia'";
                        } elseif ($tipoRelacion == 'entrante') {
                            // Si la relación es entrante, buscar en la tabla relacionada usando el ID del registro actual
                            $valorReferencia = $registro['id'];
                            $sqlRelacion = "SELECT * FROM `$tablaRelacionada` WHERE `$columna` = '$valorReferencia'";
                        } else {
                            continue;
                        }

                        $resultadoRelacion = $this->conexion->query($sqlRelacion);

                        if ($resultadoRelacion && $resultadoRelacion->num_rows > 0) {
                            // Combinar los datos de la tabla relacionada con los datos de la tabla principal
                            while ($filaRelacion = $resultadoRelacion->fetch_assoc()) {
                                foreach ($filaRelacion as $key => $value) {
                                    // Prefijar el nombre de la tabla relacionada al nombre del campo para evitar conflictos
                                    $registro[$key] = $value;
                                }
                            }
                        }
                    }
                }
            }
        }

        // Quitar los campos que se han especificado
        // if (!empty($camposAQuitar)) {
        //     foreach ($entidad as &$registro) {
        //         foreach ($camposAQuitar as $campo) {
        //             // Verificar si el campo existe en el registro, sin importar su valor
        //             if (array_key_exists($campo, $registro)) {
        //                 unset($registro[$campo]);
        //             }
        //         }
        //     }
        // }


        return $entidad;
    }



    //Función que ejecuta el getEntity pero coje solo un elemento el filtrado por id.
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


    //Función para obtener los campos de una entidad
    public function getFields()
    {
        return null;
    }

    //Método para insertar un registro en una tabla
    public function insertEntity($tabla, $data)

    {
        //$relaciones = $this->getRelations($tabla);

        //Lo primero que hacemos es obtener los nombres de las columnas y los valores a isnertar
        //Ejemplo de lo que nos llega "nombre, email, contraseña"
        $columnas =  implode(', ', array_keys($data));

        //cadena con el número total de interrogaciones
        $placeholders = implode(", ", array_fill(0, count($data), "?"));

        //Preparamos la consulta
        $sql = "INSERT INTO $tabla ($columnas) VALUES ($placeholders)";

        $sentencia = $this->conexion->prepare($sql);
        //Cadenad de los tipos para los bindParam
        $tipos = implode('', $this->tiposArray($data));

        //Vinculamos los parámetros
        $sentencia->bind_param($tipos, ...array_values($data));

        $estado = $sentencia->execute();

        return $estado ?
            [
                "status" => "exito",
                "mensaje" => "Se hizo correctamente el insertado de entidad con id: " . $this->conexion->insert_id,
                "id_insert" => $this->conexion->insert_id
            ] :

            ["status" => "error", "mensaje" => "Error al insertar un valor en la entidad, motivo : ", $sentencia->error];
    }


    public function deleteById($id, $nombreTabla)
    {

        $sql = "DELETE FROM $nombreTabla WHERE id = ?";
        $sentencia = $this->conexion->prepare($sql);
        $sentencia->bind_param('i', $id);

        $eliminado = $sentencia->execute();


        return $eliminado ?
            ["status" => "exito", "mensaje" => "Se eliminó correctamente"] :
            ["status" => "error", "mensaje" => "Ha habido algun error al eliminar.. "];
    }
    // public function deleteRow($nombreTabla, $campo)
    // {

    //     $sql = "DELETE FROM $nombreTabla WHERE $campo = ?";
    //     $sentencia = $this->conexion->prepare($sql);
    //     $sentencia->bind_param('i', $id);

    //     $eliminado = $sentencia->execute();


    //     return $eliminado ?
    //         ["status" => "exito", "mensaje" => "Se eliminó correctamente"] :
    //         ["status" => "error", "mensaje" => "Ha habido algun error al eliminar.. "];
    // }

    public function editEntity($id, $nombreTabla, $entidadActualizada)
    {
        if (empty($entidadActualizada)) {

            return [
                "status" => "error",
                "mensaje" => "No se ha proporcionado datos para actualizar"
            ];
        }

        //Construimos la cadena para de actualización de campos de una forma dinámica
        $campos = implode(", ", array_map(fn($campo) => "$campo = ?", array_keys($entidadActualizada)));

        $sql = "UPDATE $nombreTabla SET $campos WHERE id = ?";

        //Preparamos la sentencia
        $sentencia = $this->conexion->prepare($sql);

        //Obtenemos los tipos de datos actualizar
        $tipos = implode('', $this->tiposArray($entidadActualizada)) . 'i';

        //Vinculamos los valores y el id
        $valores = array_merge(array_values($entidadActualizada), [$id]);
        $sentencia->bind_param($tipos, ...$valores);
        $estado = $sentencia->execute();


        return $estado ?
            ($sentencia->affected_rows > 0 ?
                ["status" => "exito", "mensaje" => "Registro actualizado correctamente"] :
                ["status" => "error", "mensaje" => "No se realizó ningún cambio"])
            : ["status" => "error", "mensaje" => "Error al actualizar el registro ,motivo: " . $sentencia->error];
    }

    //Función que devuelve en un array tios de variables pasadas 
    private function tiposArray($data)
    {

        $tipos = [];

        foreach ($data as  $tipo) {

            if (is_int($tipo)) {
                $tipos[] = 'i';
            } elseif (is_double($tipo) || is_float($tipo)) {
                $tipos[] = 'd';
            } elseif (is_string($tipo)) {
                $tipos[] = 's';
            } elseif (is_null($tipo)) {
                $tipos[] = 's';
            } else {
                throw new Exception('Error, tipo no soportado...' . gettype($tipo));
            }
        }

        return $tipos;
    }

    public function existeRegistro($id, $tabla, $idAjeno = "")
    {
        $sql = "SELECT id FROM $tabla WHERE ";
        $idTabla = !empty($idAjeno) ? $idAjeno : "id";
        $sql .= " $idTabla = ? ";

        $sentencia = $this->conexion->prepare($sql);
        $sentencia->bind_param('i', $id);

        $estado = $sentencia->execute();
        $resultado = $sentencia->get_result();

        return ($resultado->num_rows === 1);
    }

    private function esFechaValida($valor)
    {
        // Comprobar si es una fecha en formato 'YYYY-MM-DD' o 'YYYY-MM-DD HH:MM:SS'
        return preg_match('/^\d{4}-\d{2}-\d{2}( \d{2}:\d{2}:\d{2})?$/', $valor) === 1;
    }



    //Función que devuelve todos los campos que tiene una tabla...
    public function getProperties($tabla, $camposNoDeseados = [])
    {
        $propiedades = [];

        //show columns no permite el uso de ?..
        $sql = "SHOW COLUMN FROM $tabla ";
        $resultado = $this->conexion->query($sql);


        if ($resultado->num_rows > 0) {
            while ($fila = $resultado->fetch_assoc()) {
                //Recogemos todas las columnas menos el id
                if ($fila['Field'] !== 'id' && !in_array($fila['Field'], $camposNoDeseados)) {
                    $propiedades[] = $fila['Field'];
                }
            }
        } else {
            echo "Error al obtener las columnas de la tabla.. : " . $this->conexion->error;
        }
        return $propiedades;
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
                    // 'saliente' o 'entrante' y dependiendo de eso elegimos como será el tipo de consulta
                    'tipo_relacion' => $fila['tipo_relacion']
                ];
            }
        }

        return $relaciones;
    }



    public function getEntityAlternative_3($tabla, $camposElegir = [], $completo = false)
    {
        $entidad = [];
        $camposPrincipal = [];

        // Inicializar campos de la tabla principal
        foreach ($camposElegir as $campo) {
            if (is_string($campo)) {
                $camposPrincipal[] = $campo;
            }
        }

        // Asegurarse de incluir 'id'
        if (!in_array('id', $camposPrincipal)) {
            $camposPrincipal[] = 'id';
        }

        // Construir la parte SELECT de la tabla principal
        $camposPrincipalSeleccionados = empty($camposPrincipal)
            ? "$tabla.*"
            : implode(', ', array_map(fn($campo) => "$tabla.$campo", $camposPrincipal));

        // Inicializar SQL
        $sql = "SELECT $camposPrincipalSeleccionados ";

        // Agregar INNER JOINs si es necesario
        if ($completo) {
            $relaciones = $this->getRelations($tabla);
            $joinClauses = [];

            foreach ($relaciones as $relacion) {
                if (isset($relacion['columna_relacion']) && isset($relacion['tabla_relacionada']) && isset($relacion['columna_referenciada'])) {
                    $tablaRelacionada = $relacion['tabla_relacionada'];
                    $columna = $relacion['columna_relacion'];
                    $columnaReferencia = $relacion['columna_referenciada'];

                    // Agregar cláusula JOIN
                    $joinClauses[] = "INNER JOIN `$tablaRelacionada` ON `$tabla`.`id` = `$tablaRelacionada`.`$columna`";
                }
            }

            // Concatenar las cláusulas JOIN
            if (!empty($joinClauses)) {
                $sql .= ' FROM `' . $tabla . '` ' . implode(' ', $joinClauses);
            }
        } else {
            $sql .= "FROM `$tabla` "; // Si no hay JOINs, se agrega la tabla principal
        }

        // Agregar los campos seleccionados de las relaciones
        if ($completo) {
            foreach ($relaciones as $relacion) {
                if (isset($relacion['tabla_relacionada']) && isset($relacion['columna_relacion'])) {
                    $tablaRelacionada = $relacion['tabla_relacionada'];
                    if ($this->checkColumnExists($tablaRelacionada, 'nombre')) {
                        $sql .= ", `$tablaRelacionada`.`nombre` AS nombre"; // Asegúrate de que esta columna exista
                    }
                    if ($this->checkColumnExists($tablaRelacionada, 'nombre_usuario')) {
                        $sql .= ", `$tablaRelacionada`.`nombre_usuario` AS nombre_usuario"; // Para la tabla usuarios
                    }
                }
            }
        }

        // Ejecutar la consulta
        $resultado = $this->conexion->query($sql);

        // Procesar resultados
        if ($resultado && $resultado->num_rows > 0) {
            while ($fila = $resultado->fetch_assoc()) {
                $entidad[] = $fila; // Asegúrate de que todos los campos sean recuperados
            }
        }

        return $entidad;
    }

    // Método para verificar si una columna existe en una tabla
    private function checkColumnExists($tabla, $columna)
    {
        $query = $this->conexion->query("SHOW COLUMNS FROM `$tabla` LIKE '$columna'");
        return $query && $query->num_rows > 0;
    }

    //Métodos para confirmar o volver átras.

    public function beginTransaction()
    {
        $this->conexion->begin_transaction();
    }


    public function commit()
    {
        $this->conexion->commit();
    }

    public function rollback()
    {
        $this->conexion->rollback();
    }
}
