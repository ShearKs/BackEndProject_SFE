<?php

include_once '../Daos/EntityDao.php';
include_once '../Daos/Conexion.php';

// Configuración de CORS
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Manejar solicitudes preflight (OPTIONS)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit;
}
$data = json_decode(file_get_contents('php://input'),true);

$modoCrud = isset($data['modo']) ? $data['modo'] : 'read';
$entidad = isset($data['entidad']) ? $data['entidad'] : 'entidad';
$id = isset($data['id']) ? intval($data['id']) : 0;
$entityData = isset($data['entityData']) ? $data['entityData'] : '';

// Creamos una nueva instancia del DAO para hacer la consulta
$daoEntity = new EntityDao();

switch ($modoCrud) {

    // Funcionalidad Crud

    case 'create':
        $resultado = $daoEntity->insertEntity($entidad, $entityData);
        break;
    case 'read':
        $resultado = $daoEntity->getEntity("trabajadores",[], false);
        break;
    case 'update':
        $resultado = $daoEntity->editEntity($id, $entidad, $entityData);
        break;

    case 'delete':
        $resultado = $daoEntity->deleteById($id, "trabajadores");
        break;

    default:
        $resultado = ["error" => "Modo crud no está soportado"];
}

// Devolvemos los datos como JSON
echo json_encode($resultado);
