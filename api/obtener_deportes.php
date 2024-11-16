<?php


include_once '../config/cors.php';
include_once '../Daos/DeportesDao.php';


$daoDeportes = new DeportesDao();

$deportes = $daoDeportes->getDeportes();

echo json_encode($deportes);
