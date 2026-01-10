<?php
// api/update_status.php
header('Content-Type: application/json');
session_start();
require_once '../db.php';

// Verificar autenticación
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (isset($input['id']) && isset($input['estatus'])) {
    $id = $input['id'];
    $estatus = $input['estatus'];
    
    // Validar estatus permitidos
    $allowed = ['Abierto', 'En Proceso', 'Cerrado'];
    if (!in_array($estatus, $allowed)) {
        echo json_encode(['success' => false, 'message' => 'Estatus inválido']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("UPDATE tickets SET estatus = :estatus WHERE id = :id");
        $stmt->execute([':estatus' => $estatus, ':id' => $id]);

        // Devolver la clase de color para actualizar la UI sin recargar lógica JS compleja
        $colorClass = '';
        switch ($estatus) {
            case 'Abierto': $colorClass = 'bg-red-100 text-red-800'; break;
            case 'En Proceso': $colorClass = 'bg-yellow-100 text-yellow-800'; break;
            case 'Cerrado': $colorClass = 'bg-green-100 text-green-800'; break;
        }

        echo json_encode(['success' => true, 'colorClass' => $colorClass]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error DB']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
}
?>