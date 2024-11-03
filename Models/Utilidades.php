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


    public function __construct()
    {
    }

    public function enviarCorreo($correoElectronico, $mensaje)
    {
        
        //Create an instance; passing `true` enables exceptions
        $mail = new PHPMailer(true);

        try {
            //Por si queremos depurar el envio del email descomentar
            //$mail->SMTPDebug = SMTP::DEBUG_SERVER;

            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->Username = "sergiopruebasfernandez@gmail.com";
            $mail->Password = "xygf ebgj gway hjtd";


            $mail->setFrom('sergiopruebasfernandez@gmail.com', 'Bolsa de Empleo IES Leonardo Da Vinci');
            $mail->addAddress($correoElectronico, "Sergio");
            //Content
            $mail->isHTML(true);
            $mail->Subject = 'Correo Enviado';
            $mail->Body    = 'Tienes un nuevo mensaje\n' . $mensaje;

            $mail->send();
            //echo 'El correo ha sido enviado correctamente';
        } catch (Exception $e) {
            echo "El mensanje no pudo ser enviado. Tipo de Error: {$mail->ErrorInfo}";
        }
    }
}
