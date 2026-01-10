<?php
header('Content-Type: application/json');
require_once '../db.php';
require_once '../mailer.php';

$action = $_GET['action'] ?? '';

// OBTENER DATOS (GET)
if ($action === 'get') {
    $folio = $_GET['folio'] ?? '';
    // Buscar Ticket
    $stmt = $pdo->prepare("SELECT * FROM tickets WHERE folio = :folio");
    $stmt->execute([':folio' => $folio]);
    $ticket = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($ticket) {
        // Buscar Mensajes
        $stmt2 = $pdo->prepare("SELECT *, DATE_FORMAT(fecha, '%d/%m %H:%i') as fecha_corta FROM mensajes WHERE ticket_id = :id ORDER BY fecha ASC");
        $stmt2->execute([':id' => $ticket['id']]);
        $msgs = $stmt2->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['success' => true, 'ticket' => $ticket, 'mensajes' => $msgs]);
    } else {
        echo json_encode(['success' => false]);
    }
    exit;
}

// ENVIAR MENSAJE (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ticket_id = $_POST['ticket_id'];
    $mensaje = strip_tags($_POST['mensaje']);
    $remitente = $_POST['remitente']; // 'ciudadano' o 'oficial'

    if ($mensaje && $ticket_id) {
        $sql = "INSERT INTO mensajes (ticket_id, remitente, mensaje) VALUES (:tid, :rem, :msj)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':tid' => $ticket_id, ':rem' => $remitente, ':msj' => $mensaje]);

        // Notificaci  n por correo
        if ($remitente === 'oficial') {
            // Avisar al ciudadano si tiene email
            $tStmt = $pdo->prepare("SELECT email_contacto, folio FROM tickets WHERE id = ?");
            $tStmt->execute([$ticket_id]);
            $tData = $tStmt->fetch();
            
            if ($tData && $tData['email_contacto']) {
                enviarNotificacion($tData['email_contacto'], "Nueva Respuesta - Folio " . $tData['folio'], 
                    "<p>La autoridad ha respondido a su reporte:</p><blockquote>$mensaje</blockquote><p>Entre al portal de seguimiento para responder.</p>");
            }
        } else {
            // Avisar al admin que el ciudadano respondi  
             enviarNotificacion('AQUI VA EL MAIL REMITENTE', "Respuesta de Ciudadano", "El ciudadano coment   en el ticket ID $ticket_id: $mensaje");
        }

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
}
?>