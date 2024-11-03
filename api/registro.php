<?php

include_once '../config/cors.php';

include_once '../Daos/UsuariosDao.php';

$daoUsuario = new UsuariosDao();

//Recogemos lo que ha introducido el usuario..

$data = json_decode(file_get_contents('php://input'), true);
