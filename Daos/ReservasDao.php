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
        $sql = "SELECT r.*,p.idDeporte,d.nombre,h.horario_inicio,h.horario_fin
            FROM reservas r,deportes d,pistas p,horarios h
            WHERE r.idPista = p.id AND  p.idDeporte = d.id AND r.idHorario = h.id
            AND p.idDeporte = ?" ;

        $sentencia = $this->conexion->prepare($sql);
        $sentencia->bind_param("i", $idDeporte);
        $estado = $sentencia->execute();
        $resultado = $sentencia->get_result();

        if ($estado && $resultado->num_rows > 0) {
            while ($fila = $resultado->fetch_assoc()) {
                $reservas[] = $fila;
            }
        }

        return $reservas;
    }
}
