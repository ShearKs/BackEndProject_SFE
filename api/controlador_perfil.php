<?php

// Permitir origen específico o "*"
header("Access-Control-Allow-Origin: *");

// Permitir cookies o credenciales en la solicitud
header("Access-Control-Allow-Credentials: true");

// Exponer encabezados específicos al cliente
header("Access-Control-Expose-Headers: Content-Length, X-Kuma-Revision");

// Establecer tiempo de almacenamiento en caché de la configuración CORS en el navegador
header("Access-Control-Max-Age: 600"); // Tiempo en segundos

// Métodos permitidos para las solicitudes
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

// Encabezados permitidos en la solicitud
header("Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token, Authorization");

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







