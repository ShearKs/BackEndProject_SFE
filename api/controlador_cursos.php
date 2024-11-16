<?php

include_once '../config/cors.php';

include_once '../Daos/CursosDao.php';

$cursos = new CursosDao();
$result = '';
$modo = '';


// Para otros métodos, se espera recibir datos en el cuerpo
$data = json_decode(file_get_contents('php://input'), true);
$modo = $data['modo'] ?? '';

// Datos de la inscripción
$idCliente = $data['inscripcion']['idCliente'] ?? $data['idCliente'] ?? null;
$idCurso = $data['data']['nuevaInscripcion']['idCurso'] ?? null;
//$inscripcion = $data['inscripcion'] ?? null;



// Switch para manejar las diferentes operaciones según el modo
switch ($modo) {
    case 'getCurso':
        $result = $cursos->getCursos($idCliente);
        break;
    case 'addInscripcion':
        // Verificar que ambos IDs estén disponibles antes de proceder
        //$result = $cursos->inscripcionCurso($idCliente, $idCurso);

        $idCliente = $data['data']['nuevaInscripcion']['idCliente'];
        $usuario = $data['data']['usuario'];

        $result = $cursos->insertEntity('inscripciones_cursos', ['idCliente' => $idCliente, 'idCurso' => $idCurso]);

        if ($result['status'] === 'exito') {

            $cursos->utils->enviarCorreo(
                $usuario['email'],
                "¡Te has inscrito a un curso de ARD!",
                "Felicidades " . $usuario['nombre'] . " te has incrito a un curso",
            );
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
        $result = [
            'status' => "error",
            'mensaje' => "Modo no valido"
        ];
        break;
}

echo json_encode($result);
