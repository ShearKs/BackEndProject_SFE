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
        $idCliente = $data['data']['idCliente'];

        //$result = $daoEventos->getEntity('eventos',false);
        $result = $daoEventos->getEventos($idCliente);
        break;

    case 'addEvento':
        //Evento nuevo que vamos a insertar..

        $evento = $data['data']['evento'];
        $result = $daoEventos->insertEntity('eventos', $evento);
        break;

    case 'deleteEvento':
        $idEvento = $data['data']['idEvento'];
        $result = $daoEventos->deleteById($idEvento, 'eventos');
        break;

    case 'editEvento':
        $evento = $data['data']['evento'];
        $idEvento = $evento['id'];
        $result = $daoEventos->editEntity($idEvento, 'eventos', $evento);
        break;

    case 'inscripcion':


        $inscripcion = $data['data']['inscripcion'];
        $nombreEvento = $inscripcion['nombreEvento'];
        $correo = $data['data']['usuario']['email'];
        unset($inscripcion['nombreEvento']);
        $result = $daoEventos->insertEntity('inscripciones_eventos', $inscripcion);

        if ($result['status'] === 'exito') {
            $daoEventos->utils->enviarCorreo(
                $correo,
                "¡Te has incrito en un evento deportivo!",
                "Felicidades " . $data['data']['usuario']['nombre'] . " por tu inscripción a " . $nombreEvento,
            );
        }

        break;
    default:
        $result = [
            'status' => "error",
            'mensaje' => "Modo no valido"
        ];
        break;
}

echo json_encode($result);
