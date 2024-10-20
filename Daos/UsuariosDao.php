<?php

include_once 'EntityDao.php';

class UsuariosDao extends EntityDao
{

    public function __construct()
    {
        parent::__construct();
    }


    public function getUsuarios()
    {
        $usuarios = [];

        $sql = "SELECT u.id, u.nombre_usuario, u.nombre, u.apellidos, u.email,u.telefono,u.fecha_add,u.fecha_nac,
            CASE 
                WHEN c.usuario_id IS NOT NULL THEN 'Cliente' 
                WHEN t.usuario_id IS NOT NULL THEN 'Trabajador' 
            END AS tipo_usuario
        FROM usuarios u
        LEFT JOIN clientes c ON u.id = c.usuario_id
        LEFT JOIN trabajadores t ON u.id = t.usuario_id
        WHERE c.usuario_id IS NOT NULL OR t.usuario_id IS NOT NULL;";

        $resultado = $this->conexion->query($sql);


        //Si hemos obtenido algún usuario...
        if ($resultado->num_rows > 0) {
            while ($fila = $resultado->fetch_assoc()) {
                $usuarios[] = $fila;
            }
        }

        return $usuarios;
    }

    public function insertarUsuario($tabla, $datosUsuario)
    {
        $camposDeseados = ['nombre_usuario', 'nombre', 'apellidos', 'email', 'telefono', 'fecha_nac'];
        $infoUser = [];
        $infoAdicional = [];
    
        // Dividir los datos en dos arrays: información del usuario y datos adicionales
        foreach ($datosUsuario as $indice => $valor) {
            if (in_array($indice, $camposDeseados)) {
                $infoUser[$indice] = $valor;
            } else {
                $infoAdicional[$indice] = $valor;
            }
        }
    
        // Obtener el tipo de usuario (cliente o trabajador) y formar el nombre de la tabla
        $tipoUsuario = $datosUsuario['tipo_usuario'];
        $tablaAdicional = $tipoUsuario === 'cliente' ? 'clientes' : 'trabajadores';
    
        // Insertar en la tabla de usuarios
        $insertUser = $this->insertEntity($tabla, $infoUser);
    
        // Si la inserción del usuario fue exitosa, proceder a insertar en la tabla adicional
        if ($insertUser['status'] === 'exito') {
            // Insertar en la tabla correspondiente (clientes o trabajadores) con el ID de usuario recién insertado
            $insertOther = $this->insertEntity($tablaAdicional, ['usuario_id' => $insertUser['id_insert']]);
            return $insertOther;
        } else {
            return ['error' => "Ha habido algún error al insertar el usuario en la tabla."];
        }
    }
    
}
