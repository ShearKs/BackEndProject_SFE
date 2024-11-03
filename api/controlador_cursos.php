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

include_once '../Daos/CursosDao.php';

$cursos = new CursosDao();
$result = '';
$modo = '';


// Para otros métodos, se espera recibir datos en el cuerpo
$data = json_decode(file_get_contents('php://input'), true);
$modo = $data['modo'] ?? '';

// Datos de la inscripción
$idCliente = $data['inscripcion']['idCliente'] ?? $data['idCliente'] ?? null;
$idCurso = $data['inscripcion']['idCurso'] ?? null;
//$inscripcion = $data['inscripcion'] ?? null;



// Switch para manejar las diferentes operaciones según el modo
switch ($modo) {
    case 'getCurso':
        $result = $cursos->getCursos($idCliente);
        break;
    case 'addInscripcion':
        // Verificar que ambos IDs estén disponibles antes de proceder
        if ($idCliente && $idCurso) {
            //$result = $cursos->inscripcionCurso($idCliente, $idCurso);
            $result = $cursos->insertEntity('inscripciones_cursos', ['idCliente' => $idCliente, 'idCurso' => $idCurso]);
        } else {
            $result = ['error' => 'Faltan datos para la inscripción'];
        }
        break;
    case 'addCurso':
        $nuevoCurso = $data['nuevoCurso'];
        $result = $cursos->insertEntity('cursos', $nuevoCurso);
        break;

    case  'editarCurso':
        $cursoEditado = $data['cursoEditado'];
        $idCurso = $cursoEditado['id'];
        $result = $cursos->editEntity($idCurso, 'cursos', $cursoEditado);
        break;

    case 'eliminarCurso':
        $idCurso = $data['idCurso'];
        $result = $cursos->deleteById($idCurso, 'cursos');
        break;

    default:
        $result = ['error' => 'Modo no válido'];
        break;
}

echo json_encode($result);
