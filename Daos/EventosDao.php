<?php

include_once 'EntityDao.php';


class EventosDao extends EntityDao
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getEventos($idCliente)
    {

        $eventos = [];
        $sql = "SELECT e.id, e.nombre,DATE_FORMAT(e.hora_salida, '%H:%i') AS 'hora_salida', d.nombre AS deporte_nombre,l.nombre AS 'lugar' ,l.id as 'idLugar',l.latitud,l.longitud,e.plazas_disponibles, e.descripcion, e.fecha_evento, e.distancia,e.idDeporte,
                    
                    EXISTS (
                    		SELECT id
                    		FROM inscripciones_eventos ie
                    		WHERE ie.idEvento = e.id AND ie.idCliente = ? ) AS esta_inscrito
                    
                    FROM eventos e
                    INNER JOIN deportes d ON e.idDeporte = d.id
                    INNER JOIN lugares l ON e.idLugar = l.id 
                    ORDER BY e.fecha_evento";

        $sentencia = $this->conexion->prepare($sql);
        $sentencia->bind_param('i',$idCliente);
        $estado = $sentencia->execute();
        $result = $sentencia->get_result();
     

        if ($estado && $result->num_rows > 0) {

            while ($fila = $result->fetch_assoc()) {

                $eventos[] = $fila;
            }
        }

        return $eventos;
    }
}
