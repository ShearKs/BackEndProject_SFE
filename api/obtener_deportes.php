<?php

include_once '../Daos/EntityDao.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$daoEntity = new EntityDao();

$resultado = $daoEntity->getEntity("deportes", false);

echo json_encode($resultado);
