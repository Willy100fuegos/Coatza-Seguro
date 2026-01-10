<?php
session_start();
require_once '../db.php';

// Verificar sesión
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

// 1. OBTENER DATOS PARA LA TABLA (Listado completo)
$stmt = $pdo->query("SELECT * FROM tickets ORDER BY fecha_creacion DESC");
$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 2. OBTENER DATOS PARA GRÁFICAS (Métricas)

// A. Conteo por Estatus
$sqlStatus = "SELECT estatus, COUNT(*) as total FROM tickets GROUP BY estatus";
$stmtStatus = $pdo->query($sqlStatus);
$statusData = ['Abierto' => 0, 'En Proceso' => 0, 'Cerrado' => 0];
while($row = $stmtStatus->fetch(PDO::FETCH_ASSOC)) {
    $statusData[$row['estatus']] = $row['total'];
}

// B. Conteo por Categoría
$sqlCat = "SELECT categoria, COUNT(*) as total FROM tickets GROUP BY categoria";
$stmtCat = $pdo->query($sqlCat);
$catLabels = [];
$catValues = [];
while($row = $stmtCat->fetch(PDO::FETCH_ASSOC)) {
    $catLabels[] = $row['categoria'];
    $catValues[] = $row['total'];
}

// C. Tendencia Últimos 7 Días
$sqlTrend = "SELECT DATE_FORMAT(fecha_creacion, '%Y-%m-%d') as fecha, COUNT(*) as total 
             FROM tickets 
             WHERE fecha_creacion >= DATE_SUB(NOW(), INTERVAL 7 DAY) 
             GROUP BY fecha 
             ORDER BY fecha ASC";
$stmtTrend = $pdo->query($sqlTrend);
$trendLabels = [];
$trendValues = [];
while($row = $stmtTrend->fetch(PDO::FETCH_ASSOC)) {
    $trendLabels[] = date('d/m', strtotime($row['fecha'])); // Formato día/mes
    $trendValues[] = $row['total'];
}

// Función helper para colores de estatus (UI Tabla)
function getStatusColor($status) {
    switch ($status) {
        case 'Abierto': return 'bg-red-100 text-red-800';
        case 'En Proceso': return 'bg-yellow-100 text-yellow-800';
        case 'Cerrado': return 'bg-green-100 text-green-800';
        default: return 'bg-gray-100 text-gray-800';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard C5 | CoatzaSeguro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { 
            font-family: 'Inter', sans-serif;
            /* Fondo con escudo de la policía traslúcido */
            background-image: url('http://imgfz.com/i/Nc9t0sX.png');
            background-repeat: no-repeat;
            background-position: center center;
            background-attachment: fixed;
            background-size: 400px; /* Tamaño del escudo en dashboard */
        }
        /* Capa blanca semitransparente sobre el fondo */
        .bg-overlay {
            background-color: rgba(248, 250, 252, 0.94); /* bg-slate-50 con opacidad */
            min-height: 100vh;
        }
    </style>
</head>
<body class="text-slate-700">
    <div class="bg-overlay">
        <!-- Header Admin -->
        <header class="bg-white shadow border-b border-slate-200 sticky top-0 z-50 bg-opacity-95">
            <div class="container mx-auto px-4 py-3 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-tower-broadcast text-blue-900 text-xl"></i>
                    <h1 class="text-xl font-bold text-slate-800 hidden md:block">C5 Virtual <span class="text-slate-400">| Coatzacoalcos</span></h1>
                </div>
                <div class="flex items-center gap-4">
                    <div class="text-right hidden md:block">
                        <p class="text-xs text-slate-400">Oficial de Guardia</p>
                        <p class="text-sm font-bold text-slate-700"><?php echo htmlspecialchars($_SESSION['user_nombre']); ?></p>
                    </div>
                    <a href="../login.php?logout=1" class="text-sm bg-red-50 hover:bg-red-100 text-red-600 px-3 py-2 rounded transition">
                        <i class="fa-solid fa-right-from-bracket"></i> Salir
                    </a>
                </div>
            </div>
        </header>

        <main class="container mx-auto px-4 py-8 relative z-10">
            
            <!-- SECCIÓN 1: KPIs Rápidos -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                <!-- Total -->
                <div class="bg-white p-4 rounded-xl shadow-sm border-l-4 border-blue-900 bg-opacity-90">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-xs text-slate-500 uppercase font-bold">Total Tickets</p>
                            <p class="text-2xl font-bold text-slate-800"><?php echo count($tickets); ?></p>
                        </div>
                        <i class="fa-solid fa-folder-open text-blue-100 text-3xl"></i>
                    </div>
                </div>
                <!-- Abiertos -->
                <div class="bg-white p-4 rounded-xl shadow-sm border-l-4 border-red-500 bg-opacity-90">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-xs text-slate-500 uppercase font-bold">Pendientes</p>
                            <p class="text-2xl font-bold text-red-600"><?php echo $statusData['Abierto']; ?></p>
                        </div>
                        <i class="fa-solid fa-triangle-exclamation text-red-100 text-3xl"></i>
                    </div>
                </div>
                <!-- En Proceso -->
                <div class="bg-white p-4 rounded-xl shadow-sm border-l-4 border-yellow-500 bg-opacity-90">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-xs text-slate-500 uppercase font-bold">En Atención</p>
                            <p class="text-2xl font-bold text-yellow-600"><?php echo $statusData['En Proceso']; ?></p>
                        </div>
                        <i class="fa-solid fa-helmet-safety text-yellow-100 text-3xl"></i>
                    </div>
                </div>
                <!-- Cerrados -->
                <div class="bg-white p-4 rounded-xl shadow-sm border-l-4 border-green-500 bg-opacity-90">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-xs text-slate-500 uppercase font-bold">Resueltos</p>
                            <p class="text-2xl font-bold text-green-600"><?php echo $statusData['Cerrado']; ?></p>
                        </div>
                        <i class="fa-solid fa-check-circle text-green-100 text-3xl"></i>
                    </div>
                </div>
            </div>

            <!-- SECCIÓN 2: Gráficas de Análisis -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                
                <!-- Gráfica 1: Estatus (Dona) -->
                <div class="bg-white p-5 rounded-xl shadow-sm bg-opacity-90">
                    <h3 class="text-sm font-bold text-slate-700 mb-4 border-b pb-2">Estado de Tickets</h3>
                    <div class="h-48">
                        <canvas id="chartStatus"></canvas>
                    </div>
                </div>

                <!-- Gráfica 2: Categorías (Dona) -->
                <div class="bg-white p-5 rounded-xl shadow-sm bg-opacity-90">
                    <h3 class="text-sm font-bold text-slate-700 mb-4 border-b pb-2">Tipología de Incidentes</h3>
                    <div class="h-48">
                        <canvas id="chartCat"></canvas>
                    </div>
                </div>

                <!-- Gráfica 3: Tendencia (Barras) -->
                <div class="bg-white p-5 rounded-xl shadow-sm bg-opacity-90">
                    <h3 class="text-sm font-bold text-slate-700 mb-4 border-b pb-2">Actividad (Últimos 7 días)</h3>
                    <div class="h-48">
                        <canvas id="chartTrend"></canvas>
                    </div>
                </div>
            </div>

            <!-- SECCIÓN 3: Tabla Operativa -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-slate-100 bg-opacity-90">
                <div class="p-4 bg-slate-50 border-b flex justify-between items-center bg-opacity-90">
                    <h3 class="font-bold text-slate-700">Bitácora de Reportes</h3>
                    <span class="text-xs text-slate-400">Ordenado por más reciente</span>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full whitespace-nowrap text-sm">
                        <thead class="bg-slate-100 text-slate-500 text-left uppercase tracking-wider font-semibold bg-opacity-90">
                            <tr>
                                <th class="px-6 py-3">Folio / Hora</th>
                                <th class="px-6 py-3">Incidente</th>
                                <th class="px-6 py-3">Categoría</th>
                                <th class="px-6 py-3 text-center">Evidencia</th>
                                <th class="px-6 py-3 text-center">Estatus</th>
                                <th class="px-6 py-3 text-right">Gestión</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white bg-opacity-90">
                            <?php foreach ($tickets as $ticket): ?>
                            <tr class="hover:bg-blue-50 transition group" id="row-<?php echo $ticket['id']; ?>">
                                <td class="px-6 py-3 align-top">
                                    <div class="font-bold text-blue-900"><?php echo htmlspecialchars($ticket['folio']); ?></div>
                                    <div class="text-xs text-slate-400"><?php echo date('d/m/Y H:i', strtotime($ticket['fecha_creacion'])); ?></div>
                                </td>
                                <td class="px-6 py-3 align-top">
                                    <div class="font-bold text-slate-700 mb-1"><?php echo htmlspecialchars($ticket['titulo']); ?></div>
                                    <p class="text-xs text-slate-500 whitespace-normal max-w-xs leading-relaxed">
                                        <?php echo substr(htmlspecialchars($ticket['descripcion']), 0, 80) . '...'; ?>
                                    </p>
                                </td>
                                <td class="px-6 py-3 align-top">
                                    <span class="bg-slate-100 text-slate-600 px-2 py-1 rounded text-xs border border-slate-200">
                                        <?php echo htmlspecialchars($ticket['categoria']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-center align-top">
                                    <?php if($ticket['imagen_path']): ?>
                                        <a href="../<?php echo $ticket['imagen_path']; ?>" target="_blank" class="inline-block relative">
                                            <img src="../<?php echo $ticket['imagen_path']; ?>" class="w-10 h-10 rounded object-cover border border-slate-200 hover:scale-150 transition shadow-sm bg-white">
                                        </a>
                                    <?php else: ?>
                                        <span class="text-slate-300 text-xl"><i class="fa-regular fa-image"></i></span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-3 text-center align-top">
                                    <span id="badge-<?php echo $ticket['id']; ?>" class="px-3 py-1 rounded-full text-xs font-bold <?php echo getStatusColor($ticket['estatus']); ?>">
                                        <?php echo $ticket['estatus']; ?>
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-right align-top">
                                    <div class="flex justify-end gap-2 items-start">
                                        <a href="ticket_details.php?id=<?php echo $ticket['id']; ?>" class="bg-white border border-slate-200 text-slate-600 w-8 h-8 flex items-center justify-center rounded hover:bg-blue-50 hover:text-blue-900 transition" title="Ver Expediente">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                        <select onchange="updateStatus(<?php echo $ticket['id']; ?>, this.value)" class="bg-white border border-slate-200 text-slate-700 text-xs rounded h-8 px-2 focus:outline-none focus:border-blue-500 shadow-sm">
                                            <option value="Abierto" <?php echo $ticket['estatus'] == 'Abierto' ? 'selected' : ''; ?>>Abierto</option>
                                            <option value="En Proceso" <?php echo $ticket['estatus'] == 'En Proceso' ? 'selected' : ''; ?>>En Proceso</option>
                                            <option value="Cerrado" <?php echo $ticket['estatus'] == 'Cerrado' ? 'selected' : ''; ?>>Cerrado</option>
                                        </select>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Script de Gráficas -->
    <script>
        // Configuración Común
        Chart.defaults.font.family = "'Inter', sans-serif";
        Chart.defaults.color = '#64748b';

        // 1. ESTATUS (Dona)
        const ctxStatus = document.getElementById('chartStatus').getContext('2d');
        new Chart(ctxStatus, {
            type: 'doughnut',
            data: {
                labels: ['Abierto', 'En Proceso', 'Cerrado'],
                datasets: [{
                    data: [<?php echo $statusData['Abierto']; ?>, <?php echo $statusData['En Proceso']; ?>, <?php echo $statusData['Cerrado']; ?>],
                    backgroundColor: ['#ef4444', '#eab308', '#22c55e'],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'right', labels: { boxWidth: 12, usePointStyle: true } } }
            }
        });

        // 2. CATEGORÍAS (Dona)
        const ctxCat = document.getElementById('chartCat').getContext('2d');
        new Chart(ctxCat, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode($catLabels); ?>,
                datasets: [{
                    data: <?php echo json_encode($catValues); ?>,
                    backgroundColor: ['#3b82f6', '#8b5cf6', '#f97316', '#ec4899', '#64748b'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } } // Ocultar leyenda si son muchas cats
            }
        });

        // 3. TENDENCIA (Barras)
        const ctxTrend = document.getElementById('chartTrend').getContext('2d');
        new Chart(ctxTrend, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($trendLabels); ?>,
                datasets: [{
                    label: 'Reportes',
                    data: <?php echo json_encode($trendValues); ?>,
                    backgroundColor: '#1e3a8a',
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { borderDash: [2, 2] } },
                    x: { grid: { display: false } }
                }
            }
        });

        // Lógica de Actualización AJAX
        async function updateStatus(id, newStatus) {
            const badge = document.getElementById(`badge-${id}`);
            badge.className = 'px-3 py-1 rounded-full text-xs font-bold bg-slate-200 text-slate-500';
            badge.textContent = '...';

            try {
                const response = await fetch('../api/update_status.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: id, estatus: newStatus })
                });
                const result = await response.json();

                if (result.success) {
                    badge.textContent = newStatus;
                    badge.className = `px-3 py-1 rounded-full text-xs font-bold ${result.colorClass}`;
                    // Opcional: Recargar para actualizar gráficas
                    // location.reload(); 
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                console.error(error);
                alert('Error de conexión');
            }
        }
    </script>
</body>
</html>