<?php
header('Content-Type: application/json');
require_once '../db.php';
require_once '../mailer.php';

$uploadDir = '../uploads/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Datos Obligatorios
        $titulo = strip_tags($_POST['titulo'] ?? '');
        $descripcion = strip_tags($_POST['descripcion'] ?? '');
        $categoria = $_POST['categoria'] ?? 'Otros';
        $patrulla_id = $_POST['patrulla_id'] ?? 'DESCONOCIDA';
        
        // Datos Opcionales (Privacidad)
        $nombre = !empty($_POST['nombre']) ? strip_tags($_POST['nombre']) : 'An¨®nimo';
        $telefono = !empty($_POST['telefono']) ? strip_tags($_POST['telefono']) : null;
        $email = !empty($_POST['email']) ? filter_var($_POST['email'], FILTER_SANITIZE_EMAIL) : null;

        if (empty($titulo) || empty($descripcion)) {
            throw new Exception("El t¨ªtulo y la descripci¨®n son obligatorios.");
        }

        // Imagen
        $imagenPath = null;
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
            if(!in_array(strtolower($ext), ['jpg','jpeg','png'])) throw new Exception("Formato de imagen no v¨¢lido");
            
            $newFileName = 'EVID_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
            if(move_uploaded_file($_FILES['imagen']['tmp_name'], $uploadDir . $newFileName)) {
                $imagenPath = 'uploads/' . $newFileName;
            }
        }

        // Generar Folio
        $year = date('Y');
        $random = strtoupper(substr(md5(uniqid()), 0, 4));
        $folio = "CZT-$year-$random";

        // Insertar
        $sql = "INSERT INTO tickets (folio, titulo, descripcion, categoria, patrulla_id, imagen_path, nombre_ciudadano, telefono, email_contacto) 
                VALUES (:folio, :titulo, :descripcion, :categoria, :patrulla_id, :imagen_path, :nombre, :tel, :email)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':folio' => $folio, ':titulo' => $titulo, ':descripcion' => $descripcion, 
            ':categoria' => $categoria, ':patrulla_id' => $patrulla_id, ':imagen_path' => $imagenPath,
            ':nombre' => $nombre, ':tel' => $telefono, ':email' => $email
        ]);

        // Enviar Correo al Ciudadano (si lo puso)
        if ($email) {
            $msj = "<h3>Reporte Recibido: $folio</h3><p>Gracias por su reporte. Puede dar seguimiento en el portal usando su folio.</p>";
            enviarNotificacion($email, "Folio Generado: $folio - CoatzaSeguro", $msj);
        }

        // Enviar Alerta al Admin (Correo fijo del C4)
        enviarNotificacion('aqui va el mail remitente', "Nuevo Reporte: $categoria", "<h1>Nuevo Ticket: $folio</h1><p>$descripcion</p>");

        $response['success'] = true;
        $response['folio'] = $folio;

    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
    }
}
echo json_encode($response);
?>