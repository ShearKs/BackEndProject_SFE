<?php

include_once 'EntityDao.php';

class PanelControlUsuariosDao extends EntityDao
{

    public function __construct()
    {
        parent::__construct();
    }

    public function obtenerReservasUsuario($idUsuario)
    {

        $reservasUsuario = [];

        $sql = "SELECT p.nombre AS 'pista_nombre', d.nombre AS 'deporte_nombre',h.horario_inicio AS 'inicio', h.horario_fin AS 'fin',r.fecha AS 'fecha_reserva',
                    CASE 
                        WHEN r.fecha < CURRENT_DATE THEN 'Vencido' 
                        ELSE 'Activo' 
                    END AS 'estado_reserva'
                FROM reservas r
                INNER JOIN pistas p ON r.idPista = p.id
                INNER JOIN deportes d ON p.idDeporte = d.id
                INNER JOIN horarios h ON r.idHorario = h.id
                INNER JOIN clientes c ON r.idCliente = c.id
                WHERE r.idCliente = ? ORDER BY fecha_reserva desc;";

        $sentencia = $this->conexion->prepare($sql);
        $sentencia->bind_param('i', $idUsuario);
        $estado = $sentencia->execute();
        $resutado = $sentencia->get_result();

        if ($estado && $resutado->num_rows > 0) {

            while ($fila = $resutado->fetch_assoc()) {
                $reservasUsuario[] = $fila;
            }
        }

        return $reservasUsuario;
    }
}
