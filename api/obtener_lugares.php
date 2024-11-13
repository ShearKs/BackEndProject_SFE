<?php

include_once '../Daos/EntityDao.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$daoEntity = new EntityDao();

$resultado = $daoEntity->getEntity("lugares", false);

echo json_encode($resultado);
