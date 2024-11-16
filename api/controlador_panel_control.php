<?php

include_once '../config/cors.php';

include_once '../Daos/PanelControlUsuariosDao.php';


$data = json_decode(file_get_contents('php://input'), true);

$modo = $data['modo'];
$idCliente = $data['idCliente'];

$daoPanelUser = new PanelControlUsuariosDao();

$result = [];

switch ($modo) {

    case 'getReservasUsuario':

        $result = $daoPanelUser->obtenerReservasUsuario($idCliente);
        break;

    case 'getCursosUsuario':

        $result = $daoPanelUser->obtenerCursosUsuario($idCliente);
        break;
    case 'getInscripEventos':

        $result = $daoPanelUser->obtenerEventosUsuario($idCliente);
        break;
    default:
        $result = ["error" => "Modo no soportado"];
}

echo json_encode($result);
