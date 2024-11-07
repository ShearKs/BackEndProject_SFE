<?php
ob_start(); // Inicia el almacenamiento de salida

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configuración de CORS
header("Access-Control-Allow-Origin: http://localhost:4200"); // Especifica el origen de tu aplicación
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Manejo de la solicitud OPTIONS (preflight)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

include_once '../Daos/UsuariosDao.php';
// include_once '../Daos/EntityDao.php';
include_once '../Daos/Conexion.php';

define('TABLA_USUARIOS', 'usuarios');


$data = json_decode(file_get_contents('php://input'), true);


$modoCrud = isset($data['modo']) ? $data['modo'] : 'create';
$entidad = isset($data['entidad']) ? $data['entidad'] : 'entidad';
$id = isset($data['id']) ? intval($data['id']) : 0;
$entityData = isset($data['entityData']) ? $data['entityData'] : '';

// Creamos una nueva instancia del DAO para hacer la consulta
$usuarioDao = new UsuariosDao();

switch ($modoCrud) {

    case 'create':

        //$resultado = $usuarioDao->insertarUsuario($entityData);
        break;
    case 'read':
        $resultado = $usuarioDao->getUsuarios();
        break;
    case 'update':
        $resultado = $usuarioDao->actualizarUsuario($entityData);
        break;
    case 'delete':
        $usuarioDao->beginTransaction();
        $resultado = $usuarioDao->deleteById($id, TABLA_USUARIOS);
        $usuarioDao->commit();
        break;

    default:
        $resultado = ["error" => "Modo crud no está soportado"];
}

// Devolvemos los datos como JSON
echo json_encode($resultado);
