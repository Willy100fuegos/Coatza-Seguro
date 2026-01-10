<?php
session_start();
require_once '../db.php';
if (!isset($_SESSION['user_id'])) header('Location: ../login.php');

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM tickets WHERE id = ?");
$stmt->execute([$id]);
$ticket = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$ticket) die("Ticket no encontrado");

// Obtener Mensajes
$mStmt = $pdo->prepare("SELECT *, DATE_FORMAT(fecha, '%d/%m %H:%i') as fecha_corta FROM mensajes WHERE ticket_id = ? ORDER BY fecha ASC");
$mStmt->execute([$id]);
$mensajes = $mStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expediente <?php echo $ticket['folio']; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-slate-100">

<div class="container mx-auto p-4 max-w-4xl">
    <a href="dashboard.php" class="text-slate-500 hover:text-blue-900 mb-4 inline-block"><i class="fa-solid fa-arrow-left"></i> Volver al Dashboard</a>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <!-- Info del Ticket -->
        <div class="col-span-1 bg-white p-6 rounded-xl shadow h-fit">
            <h1 class="text-xl font-bold text-blue-900 mb-1"><?php echo $ticket['folio']; ?></h1>
            <span class="text-xs bg-slate-200 px-2 py-1 rounded"><?php echo $ticket['estatus']; ?></span>
            
            <div class="mt-6 space-y-4 text-sm text-slate-700">
                <div>
                    <label class="font-bold block">Ciudadano:</label>
                    <?php echo $ticket['nombre_ciudadano'] ?: 'Anónimo'; ?>
                </div>
                <div>
                    <label class="font-bold block">Contacto:</label>
                    <?php echo $ticket['telefono'] ?: '-'; ?> <br>
                    <?php echo $ticket['email_contacto'] ?: '-'; ?>
                </div>
                <div>
                    <label class="font-bold block">Descripción:</label>
                    <p class="bg-slate-50 p-2 rounded mt-1"><?php echo $ticket['descripcion']; ?></p>
                </div>
                <?php if($ticket['imagen_path']): ?>
                    <div>
                        <label class="font-bold block mb-1">Evidencia:</label>
                        <a href="../<?php echo $ticket['imagen_path']; ?>" target="_blank">
                            <img src="../<?php echo $ticket['imagen_path']; ?>" class="rounded border hover:opacity-75 transition">
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Chat Oficial -->
        <div class="col-span-1 md:col-span-2 bg-white rounded-xl shadow flex flex-col h-[600px]">
            <div class="p-4 border-b bg-slate-50 font-bold text-slate-700">
                Canal de Comunicación Oficial
            </div>
            
            <div class="flex-1 overflow-y-auto p-4 space-y-4 bg-slate-100" id="chatContainer">
                <?php foreach($mensajes as $m): $esOficial = $m['remitente'] == 'oficial'; ?>
                    <div class="flex <?php echo $esOficial ? 'justify-end' : 'justify-start'; ?>">
                        <div class="max-w-[80%] px-4 py-2 rounded-lg shadow-sm text-sm <?php echo $esOficial ? 'bg-blue-900 text-white rounded-br-none' : 'bg-white text-slate-800 rounded-bl-none'; ?>">
                            <p><?php echo $m['mensaje']; ?></p>
                            <span class="text-[10px] opacity-70 block text-right mt-1"><?php echo $m['fecha_corta']; ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="p-4 bg-white border-t">
                <form id="replyForm" class="flex gap-2">
                    <input type="hidden" name="ticket_id" value="<?php echo $id; ?>">
                    <input type="hidden" name="remitente" value="oficial">
                    <input type="text" name="mensaje" required class="flex-1 border rounded-lg px-4 focus:outline-none focus:ring-2 focus:ring-blue-900" placeholder="Escriba una respuesta oficial...">
                    <button type="submit" class="bg-blue-900 text-white px-6 py-2 rounded-lg hover:bg-blue-800"><i class="fa-solid fa-paper-plane"></i></button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Scroll al final del chat
    const box = document.getElementById('chatContainer');
    box.scrollTop = box.scrollHeight;

    document.getElementById('replyForm').addEventListener('submit', async function(e){
        e.preventDefault();
        const formData = new FormData(this);
        
        await fetch('../api/chat.php', { method: 'POST', body: formData });
        window.location.reload();
    });
</script>

</body>
</html>