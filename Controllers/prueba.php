<?php


include_once '../Daos/EntityDao.php';
include_once '../Daos/Conexion.php'; // Asegúrate de incluir también la clase de conexión

// Crear una instancia de Conexion para establecer la conexión

header("Access-Control-Allow-Origin: *");  // Permitir solicitudes desde cualquier origen
header("Content-Type: application/json; charset=UTF-8");  // Respuesta en formato JSON


//new Conexion();

// Creamos una nueva instancia del DAO para hacer la consulta
$daoEntity = new EntityDao();

// Obtenemos los datos de la base de datos
$resultado = $daoEntity->getEntity('ususario', []);

// Devolvemos los datos como JSON
echo json_encode($resultado);

