<?php
ob_start(); // Inicia el almacenamiento de salida

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configuración de CORS
header("Access-Control-Allow-Origin: http://localhost:4200"); // Especifica el origen de tu aplicación
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

include_once '../Daos/ReservasDao.php';

// Obtener el parámetro idDeporte desde la URL
if (isset($_REQUEST['idDeporte'])) {
    $idDeporte = $_REQUEST['idDeporte'];
} else {
    $idDeporte = null; // Si no se envía, puedes manejarlo como quieras
}

$daoReservas = new ReservasDao();

//Obetenemos las reservas para ese deporte
$reservas = $daoReservas->getReservaDeporte($idDeporte,null);

echo json_encode($reservas);
