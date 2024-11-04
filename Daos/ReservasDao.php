<?php

include_once 'EntityDao.php';


class ReservasDao extends EntityDao
{

    public function __construct()
    {
        parent::__construct();
    }

    //Método que devuelve las reservas para ese día de un deporte determinado...
    public function getReservaDeporte($idDeporte, $fechaReserva)
    {

        //Reservas que tiene el deporte para ese día
        $reservas = [];
        $sql = "SELECT r.*,p.idDeporte,d.nombre,h.horario_inicio as 'inicio',h.horario_fin as 'fin'
            FROM reservas r,deportes d,pistas p,horarios h
            WHERE r.idPista = p.id AND  p.idDeporte = d.id AND r.idHorario = h.id
            AND p.idDeporte = ? and r.fecha = ?";

        $sentencia = $this->conexion->prepare($sql);
        $sentencia->bind_param("is", $idDeporte,$fechaReserva);
        $estado = $sentencia->execute();
        $resultado = $sentencia->get_result();

        if ($estado && $resultado->num_rows > 0) {
            while ($fila = $resultado->fetch_assoc()) {
                $reservas[] = $fila;
            }
        }

        return $reservas;
    }

    public function getHorarioDeporte($idDeporte)
    {
        $horarios = [];
        $sql = "SELECT h.id,h.horario_inicio, h.horario_fin
                FROM horarios h
                INNER JOIN deportes_horarios dh ON h.id = dh.idHorario
                WHERE dh.idDeporte = ? AND h.disponible = 1;";
        
        $sentencia = $this->conexion->prepare($sql);
        $sentencia->bind_param("i", $idDeporte);
        $estado = $sentencia->execute();
        $resultado = $sentencia->get_result();
    
        if ($estado && $resultado->num_rows > 0) {
            while ($fila = $resultado->fetch_assoc()) {
                $horarios[] = [
                    'id' => $fila['id'],
                    'inicio' => $fila['horario_inicio'],
                    'fin' => $fila['horario_fin'],
                ];
            }
        }
        return $horarios;
    }
    
}
