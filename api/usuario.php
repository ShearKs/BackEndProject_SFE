<?php

include_once '../Daos/EntityDao.php';
include_once '../Daos/Conexion.php';

// Crear una instancia de Conexion para establecer la conexión

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Creamos una nueva instancia del DAO para hacer la consulta
$daoEntity = new EntityDao();

// Obtenemos los datos de la base de datos
$resultado = $daoEntity->getEntity("trabajadores", [], true);

// Devolvemos los datos como JSON
echo json_encode($resultado);
