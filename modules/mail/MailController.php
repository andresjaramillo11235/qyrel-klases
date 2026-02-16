<?php

require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailController
{
    private $mailer;

    public function __construct()
    {
        $this->mailer = new PHPMailer(true);

        try {
            // Configuración del servidor SMTP
            $this->mailer->isSMTP();
            $this->mailer->Host = 'smtp.mailersend.net'; // Servidor SMTP de MailerSend
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = 'MS_lvZibN@ceacloud.lat'; // Tu correo verificado en MailerSend
            $this->mailer->Password = '6pNqojPe3Jgecvt5'; // Contraseña generada por MailerSend
            $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Encriptación TLS
            $this->mailer->Port = 587; // Puerto SMTP

            // Configuración predeterminada del remitente
            $this->mailer->setFrom('noreply@ceacloud.lat', 'CeaCloud'); // Asegúrate de usar un correo verificado
            $this->mailer->CharSet = 'UTF-8'; // Configuración de caracteres
        } catch (Exception $e) {
            // Manejo de errores al configurar PHPMailer
            error_log('Error al configurar PHPMailer: ' . $e->getMessage());
            throw $e;
        }
    }

    public function cargarPlantilla($ruta, $variables)
    {
        if (!file_exists($ruta)) {
            throw new Exception("La plantilla no existe: {$ruta}");
        }

        $contenido = file_get_contents($ruta);

        // Reemplazar las variables en el contenido
        foreach ($variables as $clave => $valor) {
            $contenido = str_replace("{" . $clave . "}", $valor, $contenido);
        }

        return $contenido;
    }

    public function sendMail($to, $toName, $subject, $bodyHtml, $altBody = '')
    {
        try {
            // Configurar destinatario
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($to, $toName);

            // Configurar contenido del correo
            $this->mailer->isHTML(true);
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $bodyHtml;
            $this->mailer->AltBody = $altBody ?: strip_tags($bodyHtml);

            // Enviar el correo
            $this->mailer->send();
            return true;
        } catch (Exception $e) {
            error_log("Error al enviar el correo: " . $this->mailer->ErrorInfo);
            return false;
        }
    }
}
