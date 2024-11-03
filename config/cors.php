<?php

//Archivo php donde se encontrarán todas las cabeceras.

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

// Manejo de preflight (solicitud de verificación previa)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}
