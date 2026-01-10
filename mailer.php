<?php
/**
 * Clase ligera para envío SMTP (Sin Composer/PHPMailer)
 * Configurada para cPanel
 */

function enviarNotificacion($destinatario, $asunto, $mensajeHTML) {
    // Credenciales SMTP
    $smtpHost = 'AQUI VA TU SERVIDOR'; // Usualmente mail.dominio.com en cPanel
    $smtpUser = 'MAIL REMITENTE';
    $smtpPass = 'AQUI VA TU PASSWORD DE CORREO';
    $smtpPort = 465; // Puerto SSL estándar

    // Cabeceras para mail() nativo (Plan A - Más compatible en cPanel local)
    // Intentamos usar la función mail() de PHP configurando los headers correctamente
    // ya que estamos dentro del mismo servidor.
    
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: Alerta C4 Coatza <$smtpUser>" . "\r\n";
    $headers .= "Reply-To: $smtpUser" . "\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    // Si hay destinatario, enviamos
    if ($destinatario && filter_var($destinatario, FILTER_VALIDATE_EMAIL)) {
        return mail($destinatario, $asunto, $mensajeHTML, $headers);
    }
    return false;
}
?>