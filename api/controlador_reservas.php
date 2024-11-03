<?php

include_once '../config/cors.php';

include_once '../Daos/ReservasDao.php';

$data = json_decode(file_get_contents('php://input'), true);

$modo = $data['modo'];
$idDeporte = $data['idDeporte'];

$daoReservas = new ReservasDao();
$result = [];

switch ($modo) {

    case 'getReservas':
        //Obetenemos las reservas para ese deporte
        $result = $daoReservas->getReservaDeporte($idDeporte, null);
        break;
    case 'getPistas':
        $result = 'hola';
        break;
    default:
        $result = ["error" => "Modo no soportado"];
}

echo json_encode($result);




