<?php

include_once '../config/cors.php';
include_once '../Daos/UsuariosDao.php';


//Obtenemos los datos que nos ha pasado el cliente
$data = json_decode(file_get_contents('php://input'),true);

//Recogemos el modo que es..
$modo = isset($data['modo']) ? $data['modo'] : 'no definido';
$usuarioEdit = $data['usuario'];

//Información al usuario de lo que ha pasado si tanto si lo hemos logrado como si no.
$mensaje = '';

$daoUsuario = new UsuariosDao();

switch($modo){
    case 'edit':
        $mensaje = $daoUsuario->actualizarUsuario($usuarioEdit);
        break;
    default:    
}


echo json_encode($mensaje);







