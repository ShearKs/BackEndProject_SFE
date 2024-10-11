<?php

class Usuario{

    private $nombre;
    private $usuario;
    private $correo;
    private $edad;


    public function __construct($nombre,$usuario,$correo,$edad)
    {

        $this->nombre = $nombre;
        $this->usuario = $usuario;
        $this->correo = $correo;
        $this->edad = $edad;
    }

    


}