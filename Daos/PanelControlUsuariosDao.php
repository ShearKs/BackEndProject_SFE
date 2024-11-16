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
                    END AS 'estado'
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

    public function obtenerCursosUsuario($idUsuario)
    {
        $inscripcionesCursosU = [];


        $sql = " SELECT 
                        i.idCliente,
                        i.id AS inscripcion_id, 
                        u.nombre AS cliente_nombre, 
                        d.nombre AS 'deporte',
                        cu.nombre AS curso_nombre,
                        i.estado AS inscripcion_estado,
                        i.fecha_inscripcion,
                        
                        CASE 
                        WHEN i.fecha_inscripcion < CURRENT_DATE THEN 'Vencido' 
                        ELSE 'Activo' 
                    END AS 'estado'
                    FROM inscripciones_cursos i
                    INNER JOIN clientes cl ON i.idCliente = cl.id  
                    INNER JOIN usuarios u ON cl.usuario_id = u.id  
                    INNER JOIN cursos cu ON i.idCurso = cu.id 
                    INNER JOIN deportes d ON cu.idDeporte = d.id
          				WHERE idCliente = ? ORDER BY fecha_inscripcion desc";

        $sentencia = $this->conexion->prepare($sql);
        $sentencia->bind_param('i', $idUsuario);
        $estado = $sentencia->execute();
        $resutado = $sentencia->get_result();

        if ($estado && $resutado->num_rows > 0) {

            while ($fila = $resutado->fetch_assoc()) {
                $inscripcionesCursosU[] = $fila;
            }
        }

        return $inscripcionesCursosU;
    }

    public function obtenerEventosUsuario($idUsuario)
    {

        $inscripcionesEventosU = [];


        $sql = "SELECT e.nombre AS 'evento_nombre',d.nombre AS 'deporte',e.distancia,i.fecha_inscripcion,
 							CASE 
                        WHEN i.fecha_inscripcion < CURRENT_DATE THEN 'Vencido' 
                        ELSE 'Activo' 
   						END AS estado
                    FROM inscripciones_eventos i
                    INNER JOIN eventos e ON i.idEvento = e.id
                    INNER JOIN deportes d ON e.idDeporte = d.id
                    INNER JOIN clientes cl ON i.idCliente = cl.id  
                    INNER JOIN usuarios u ON cl.usuario_id = u.id     

                    WHERE i.idCliente = ? ORDER BY fecha_inscripcion desc;  ";

        $sentencia = $this->conexion->prepare($sql);
        $sentencia->bind_param('i', $idUsuario);
        $estado = $sentencia->execute();
        $resutado = $sentencia->get_result();

        if ($estado && $resutado->num_rows > 0) {

            while ($fila = $resutado->fetch_assoc()) {
                $inscripcionesEventosU[] = $fila;
            }
        }

        return $inscripcionesEventosU;
    }
}
