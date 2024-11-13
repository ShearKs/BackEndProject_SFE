<?php

include_once '../config/cors.php';

include_once '../Daos/ReservasDao.php';


//Definimos una constante de la tabla
define('TABLA_PISTAS', 'pistas');
define('TABLA_RESERVAS', 'reservas');

$data = json_decode(file_get_contents('php://input'), true);

$modo = $data['modo'];
$idDeporte = $data['idDeporte'] ?? null;

$daoReservas = new ReservasDao();
$result = [];

switch ($modo) {

    case 'getReservas':
        //Obetenemos las reservas para ese deporte y esa determinada fecha

        $fechaReserva = $data['fecha'];
        $result = $daoReservas->getReservaDeporte($idDeporte, $fechaReserva);
        break;
    case 'getPistas':
        $result = $daoReservas->getByExternalId(TABLA_PISTAS, 'idDeporte', $idDeporte);
        break;

    case 'getHorario':
        $result = $daoReservas->getHorarioDeporte($idDeporte);
        break;

    case 'hacerReserva':
        $usuario = $data['data']['usuario'];
        $correo = $usuario['email'];
        $reserva = $data['data']['reserva'];

        $nuevaReserva = [
            'fecha' => $reserva['fecha'],
            'idHorario' => $reserva['idHorario'],
            'idPista' => $reserva['idPista'],
            'idCliente' => $reserva['idCliente']
        ];


        $result = $daoReservas->insertEntity(TABLA_RESERVAS, $nuevaReserva);

        if ($result['status'] === 'exito') {
            $daoReservas->utils->enviarCorreo($correo, "Reserva realizada!", "Enhorabuena has realizado una reserva!");
            //$daoReservas->generarPDFReserva($reserva);
        }
        break;

    default:
        $result = ["error" => "Modo no soportado"];
}

echo json_encode($result);
