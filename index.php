<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CoatzaSeguro | Denuncia Ciudadana</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { 
            font-family: 'Inter', sans-serif;
            /* Fondo con escudo de la policía traslúcido */
            background-image: url('http://imgfz.com/i/Nc9t0sX.png');
            background-repeat: no-repeat;
            background-position: center center;
            background-attachment: fixed;
            background-size: 300px; /* Tamaño del escudo */
        }
        /* Capa blanca semitransparente sobre el fondo para asegurar legibilidad */
        .bg-overlay {
            background-color: rgba(241, 245, 249, 0.92); /* bg-slate-100 con opacidad */
            min-height: 100vh;
        }
    </style>
</head>
<body class="text-slate-700">
    <div class="bg-overlay">
        <!-- Navbar -->
        <nav class="bg-blue-900 text-white p-4 shadow-lg">
            <div class="container mx-auto flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <!-- LOGO DE CABECERA (Reemplaza al texto) -->
                    <img src="https://www.coatzacoalcos.gob.mx/wp-content/uploads/2024/08/logo-header-768x168.png" alt="Logo Coatzacoalcos" class="h-12 md:h-16">
                </div>
                <div class="flex gap-3">
                    <a href="seguimiento.php" class="text-sm bg-blue-800 hover:bg-blue-700 px-3 py-1 rounded transition border border-blue-700 flex items-center">
                        <i class="fa-solid fa-magnifying-glass mr-2"></i> Consultar Folio
                    </a>
                    <a href="login.php" class="text-sm hover:text-blue-200 transition flex items-center">
                        <i class="fa-solid fa-user-shield mr-1"></i> Oficial
                    </a>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="container mx-auto px-4 py-8 max-w-lg">
            
            <!-- Botón Móvil de Consulta Rápida (Visible solo si se necesita enfatizar) -->
            <div class="mb-6 text-center">
                <a href="seguimiento.php" class="inline-block text-blue-900 font-bold text-sm hover:underline">
                    ¿Ya hizo un reporte? Consulte el estatus aquí.
                </a>
            </div>

            <div class="bg-white rounded-xl shadow-md p-6 border-t-4 border-blue-900 relative overflow-hidden">
                <h2 class="text-2xl font-bold text-slate-800 mb-2">Nuevo Reporte</h2>
                <p class="text-sm text-slate-500 mb-6">Complete el formulario para generar un ticket de atención inmediata.</p>

                <form id="denunciaForm" class="space-y-4 relative z-10">
                    <!-- Dato Oculto: Patrulla ID -->
                    <input type="hidden" name="patrulla_id" value="A-042-C">

                    <!-- Título -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Título del Incidente</label>
                        <input type="text" name="titulo" required class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-900 bg-white bg-opacity-90" placeholder="Ej: Robo de autopartes">
                    </div>

                    <!-- Categoría -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Categoría</label>
                        <select name="categoria" required class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-900 bg-white bg-opacity-90">
                            <option value="" disabled selected>Seleccione una opción</option>
                            <option value="Emergencia">🚨 Emergencia</option>
                            <option value="Robo">🚙 Robo</option>
                            <option value="Violencia">👊 Violencia</option>
                            <option value="Queja">📝 Queja Ciudadana</option>
                            <option value="Otros">📦 Otros</option>
                        </select>
                    </div>

                    <!-- Descripción -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Descripción Detallada</label>
                        <textarea name="descripcion" rows="4" required class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-900 bg-white bg-opacity-90" placeholder="Describa el lugar y los hechos..."></textarea>
                    </div>

                    <!-- Evidencia -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Evidencia (Foto)</label>
                        <div class="flex items-center justify-center w-full">
                            <label for="dropzone-file" class="flex flex-col items-center justify-center w-full h-32 border-2 border-slate-300 border-dashed rounded-lg cursor-pointer bg-slate-50 hover:bg-slate-100 bg-opacity-90">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <i class="fa-solid fa-camera text-slate-400 text-2xl mb-2"></i>
                                    <p class="text-sm text-slate-500"><span class="font-semibold">Toque para subir</span> o arrastre imagen</p>
                                    <p class="text-xs text-slate-400">JPG, PNG (Max 5MB)</p>
                                </div>
                                <input id="dropzone-file" name="imagen" type="file" class="hidden" accept="image/png, image/jpeg" />
                            </label>
                        </div>
                        <p id="fileName" class="text-xs text-blue-900 mt-1 font-semibold"></p>
                    </div>

                    <!-- SECCIÓN DE PRIVACIDAD -->
                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-100 mt-4 bg-opacity-90">
                        <h3 class="text-sm font-bold text-blue-900 mb-2"><i class="fa-solid fa-user-secret"></i> Datos de Contacto (Opcional)</h3>
                        <p class="text-xs text-slate-500 mb-3">Si desea recibir notificaciones por correo, llene estos campos. Si prefiere el anonimato total, déjelos en blanco.</p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <input type="text" name="nombre" class="w-full px-3 py-2 border rounded text-sm bg-white" placeholder="Su Nombre (Opcional)">
                            <input type="text" name="telefono" class="w-full px-3 py-2 border rounded text-sm bg-white" placeholder="Teléfono (Opcional)">
                        </div>
                        <input type="email" name="email" class="w-full px-3 py-2 border rounded text-sm mt-3 bg-white" placeholder="Correo Electrónico (Recomendado para alertas)">
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" id="btnSubmit" class="w-full bg-blue-900 hover:bg-blue-800 text-white font-bold py-3 rounded-lg shadow transition duration-200 flex justify-center items-center gap-2 mt-6">
                        <span>Enviar Reporte</span>
                        <i class="fa-solid fa-paper-plane"></i>
                    </button>
                </form>
            </div>
        </div>

        <!-- Modal de Éxito -->
        <div id="successModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50 px-4">
            <div class="bg-white rounded-lg p-6 max-w-sm w-full text-center shadow-2xl relative z-50">
                <div class="w-16 h-16 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-check text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-800 mb-2">¡Reporte Enviado!</h3>
                <p class="text-slate-600 mb-4">Su reporte ha sido registrado correctamente.</p>
                <div class="bg-slate-100 p-3 rounded-lg mb-4 border border-slate-200">
                    <p class="text-xs text-slate-500 uppercase tracking-wide">Folio de Seguimiento</p>
                    <p id="folioResult" class="text-2xl font-mono font-bold text-blue-900">CZT-000</p>
                    <p class="text-xs text-red-500 mt-2 font-bold">¡Guarde este folio para consultar respuestas!</p>
                </div>
                <div class="flex flex-col gap-2">
                    <button onclick="window.location.reload()" class="w-full bg-slate-800 text-white py-2 rounded-lg hover:bg-slate-700">Crear otro reporte</button>
                    <a href="seguimiento.php" class="w-full bg-white border border-slate-300 text-slate-700 py-2 rounded-lg hover:bg-slate-50 block">Ir a Consultar Folio</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Mostrar nombre de archivo seleccionado
        document.getElementById('dropzone-file').addEventListener('change', function(e) {
            if (e.target.files[0]) {
                document.getElementById('fileName').textContent = 'Archivo: ' + e.target.files[0].name;
            }
        });

        // Manejo del formulario
        document.getElementById('denunciaForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const btn = document.getElementById('btnSubmit');
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Procesando...';

            const formData = new FormData(this);

            try {
                const response = await fetch('api/save_ticket.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();

                if (result.success) {
                    document.getElementById('folioResult').textContent = result.folio;
                    document.getElementById('successModal').classList.remove('hidden');
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Ocurrió un error al conectar con el servidor.');
            } finally {
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        });
    </script>
</body>
</html>