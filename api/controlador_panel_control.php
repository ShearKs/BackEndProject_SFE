<?php

include_once '../config/cors.php';

include_once '../Daos/PanelControlUsuariosDao.php';


$data = json_decode(file_get_contents('php://input'), true);

$modo = $data['modo'];


$daoPanelUser = new PanelControlUsuariosDao();

$result = [];

switch ($modo) {

    case 'getReservasUsuario':
        $idCliente = $data['idCliente'];
        $result = $daoPanelUser->obtenerReservasUsuario($idCliente);
        break;

    default:
        $result = ["error" => "Modo no soportado"];
}

echo json_encode($result);
