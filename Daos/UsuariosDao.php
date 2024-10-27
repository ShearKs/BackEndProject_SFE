<?php

include_once 'EntityDao.php';

class UsuariosDao extends EntityDao
{

    public function __construct()
    {
        parent::__construct();
    }


    //Para autenticación y registro-----
    public function login($usuario, $contrasena)
    {

        //Hacemos una consulta a la base de datos al usuario 
        $sql = "SELECT u.id,u.nombre_usuario, u.contrasena, u.nombre, u.apellidos, u.telefono, u.email, u.fecha_nac,
                    
                    CASE 
                        WHEN c.id IS NOT NULL THEN 'cliente'
                        WHEN t.id IS NOT NULL THEN 'trabajador'
                        ELSE 'desconocido'
                    END AS tipo_usuario
                FROM usuarios u
                LEFT JOIN clientes c ON u.id = c.usuario_id
                LEFT JOIN trabajadores t ON u.id = t.usuario_id
                WHERE u.nombre_usuario = ? AND u.estado = 'activo' ";
        $setencia = $this->conexion->prepare($sql);
        $setencia->bind_param("s", $usuario);

        $estado = $setencia->execute();
        $resultado = $setencia->get_result();

        if ($estado != null && $resultado->num_rows == 1) {

            //Hay un usuario con ese nombre
            $usuario = $resultado->fetch_assoc();

            //Contraseña del usuario
            $contrasenaBdd = $usuario['contrasena'];

            if ($contrasenaBdd === $contrasena) {

                //Eliminamos la contraseña ya que se la vamos a pasar al cliente y estará visible
                unset($usuario['contrasena']);

                return ["success" => true, "user" => json_encode($usuario), "token" => base64_encode(random_bytes(16))];
            }

            return ["success" => false, "message" => "Has introducido mal la contraseña"];
        } else {
            return ["success" => false, "message" => "No existe ningún usuario con ese nombre.."];
        }
    }


    public function registro() {}

    public function getUsuarios()
    {
        $usuarios = [];

        $sql = "SELECT u.id, u.nombre_usuario, u.nombre, u.apellidos, u.email,u.telefono,u.fecha_add,u.fecha_nac,
               c.usuario_id,t.usuario_id,
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

    //registro para el crud de usuarios....
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
            $this->rollback();
            return ['error' => "Ha habido algún error al insertar el usuario en la tabla."];
        }
    }

    //Igual que hemos con el delete vamos a tener que hacer algo parecido ya que no nos va servir el método básico para actualizar "editEntity"
    public function actualizarUsuario($datosUserUp)
    {
        //$this->beginTransaction();

        //Obtenemos el id del usuario que vamos a actualizar
        $id = $datosUserUp['id'];

        $tablaAdicional = $datosUserUp['tipo_usuario'] === 'cliente' ? 'clientes' : 'trabajadores';

        //Campos que vamos a querer que se actualicen en 'usuario'
        $camposUpdate = ['nombre_usuario', 'nombre', 'apellidos', 'email', 'telefono', 'fecha_nac'];
        $infoUser = [];
        $infoAdicional = [];

        foreach ($datosUserUp as $indice => $valor) {

            if (in_array($indice, $camposUpdate)) {
                $infoUser[$indice] = $valor;
            } else {
                $infoAdicional[$indice] = $valor;
            }
        }

        //Actualizamos en la tabla de Usuarios todos los cambios que vayamos a hacer..
        $usuariosUpdate = $this->editEntity($id, 'usuarios', $infoUser);

        if ($usuariosUpdate["status"] === 'exito') {

            //Si no existe registro significará que tenemos que actualizar el tipo de usuario y significa que cambiara de cliente a trabjador o viceversa 
            //si no, no tenemos que cambiar nada.

            if (!$this->existeRegistro($id, $tablaAdicional, 'usuario_id')) {

                //si entramos aquí significa que hay que actualizar
                $tablaUpdate = ($tablaAdicional === 'clientes') ? 'trabjadores' : 'clientes';

                //Primero eliminamos en la tabla donde estaba
                $mensajeElim = $this->deleteById($id, $tablaUpdate);

                if ($mensajeElim['status'] !== 'exito') {
                    return ['status' => 'error', 'mensaje' => 'Ha habido algún error a eliminar el tipo de usuario antiguo...'];
                }

                //Ahora insertamos el usuario con su nuevo tipo
                $mensajeInsert = $this->insertEntity($tablaAdicional, ["usuario_id" => $id]);

                if ($mensajeInsert['status'] !== 'exito') {

                    return ['status' => 'error', 'mensaje' => 'Ha habido algún error al insertar el tipo de usuario...'];
                }
            }

            //Si está todo bien hacemos todos los cambios...
            //$this->conexion->commit();
            return ['status' => 'exito', 'mensaje' => 'Se ha conseguido editar el usuario de forma satisfactoria!'];
        } else {
            return ['status' => 'error', 'mensaje' => 'Ha habido algún error a actualizar el usuario...'];
        }
    }
}
