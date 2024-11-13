<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require('../lib/PHPMailer/src/Exception.php');
require('../lib/PHPMailer/src/PHPMailer.php');
require('../lib/PHPMailer/src/SMTP.php');



//Clase que se encarga de proporcionar funciones utiles que puedan ser útiles en la aplicación.s
class Utilidades
{


    public function __construct() {}

    public function enviarCorreo($correoElectronico, $titulo, $cuerpo)
    {
        $mail = new PHPMailer(true);
    
        try {
            // Configuración básica
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
    
            $mail->Username = "sergiopruebasfernandez@gmail.com";
            $mail->Password = "xygf ebgj gway hjtd";
    
            // Remitente y destinatario
            $mail->setFrom('sergiopruebasfernandez@gmail.com', 'Aplicaciones de Reservas Deportivas - ARD');
            $mail->addAddress($correoElectronico, "Sergio");
    
            // Configuración de codificación UTF-8
            $mail->CharSet = 'UTF-8';
            $mail->Encoding = 'base64';
    
            // Contenido del correo
            $mail->isHTML(true);
            $mail->Subject = $titulo;
            $mail->Body    = $cuerpo;
    
            $mail->send();
            //echo 'El correo ha sido enviado correctamente';
        } catch (Exception $e) {
            echo "El mensaje no pudo ser enviado. Tipo de Error: {$mail->ErrorInfo}";
        }
    }


    
}
