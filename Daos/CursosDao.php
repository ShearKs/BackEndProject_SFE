<?php

include_once 'EntityDao.php';


class CursosDao extends EntityDao
{

    public function __construct()
    {

        parent::__construct();
    }

    //Función que devuelve todos los cursos disponibles que hay.
    public function getCursos($idCliente)
    {

        $cursos = [];
        $sql = "SELECT c.id,c.nombre,c.icono_curso,c.plazas,c.idDeporte,c.descripcion as 'informacion',
                    d.nombre AS deporte,
                    EXISTS (
                        SELECT id
                        FROM inscripciones_cursos ic 
                        WHERE ic.idCurso = c.id AND ic.idCliente = ? ) AS esta_inscrito
                FROM 
                    cursos c
                INNER JOIN 
                    deportes d ON d.id = c.idDeporte; ";


        $sentencia = $this->conexion->prepare($sql);
        $sentencia->bind_param("i", $idCliente);

        $estado = $sentencia->execute();
        $resultado = $sentencia->get_result();

        if ($estado && $resultado->num_rows > 0) {

            while ($fila = $resultado->fetch_assoc()) {

                $cursos[] = $fila;
            }
        }

        return $cursos;
    }
}
