<?php

include_once '../config/cors.php';

include_once '../Daos/UsuariosDao.php';

$daoUsuario = new UsuariosDao();

//Recogemos lo que ha introducido el usuario..

$data = json_decode(file_get_contents('php://input'), true);

$modo = $data['modo'];
$datos = $data['data'];

$mensajeAuth = '';

switch ($modo) {
    case 'login':
        $userName = $datos['username'];
        $contrasena = $datos['password'];
        $mensajeAuth = $daoUsuario->login($userName, $contrasena);
        break;
    case 'registro':
        $datos['tipo_usuario'] = 'cliente';
        $mensajeAuth = $daoUsuario->insertarUsuario('usuarios',$datos);
        break;
    default:        
}

echo json_encode($mensajeAuth);
