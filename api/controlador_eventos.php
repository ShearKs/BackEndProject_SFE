<?php

include_once '../config/cors.php';
include_once '../Daos/EventosDao.php';


$daoEventos = new EventosDao();



// Para otros métodos, se espera recibir datos en el cuerpo
$data = json_decode(file_get_contents('php://input'), true);
$modo = $data['modo'] ?? '';
$result = '';

switch ($modo) {

    case 'getEventos':

        $result = $daoEventos->getEntity('eventos', false);
        break;

    case 'addEvento':
        //Evento nuevo que vamos a insertar..

        $evento = $data['data']['evento'];
        $result = $daoEventos->insertEntity('eventos',$evento);
        break;

    default:
        $result = [
            'status' => "error",
            'mensaje' => "Modo no valido"
        ];
        break;
}

echo json_encode($result);
