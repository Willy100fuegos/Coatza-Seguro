<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seguimiento | CoatzaSeguro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-100 min-h-screen flex flex-col">

    <nav class="bg-blue-900 text-white p-4 shadow-lg">
        <div class="container mx-auto flex items-center justify-between">
            <a href="index.php" class="flex items-center space-x-2 font-bold">
                <i class="fa-solid fa-arrow-left"></i> <span>Volver</span>
            </a>
            <h1 class="text-lg">Consulta de Folio</h1>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-8 max-w-2xl flex-grow">
        
        <!-- Buscador -->
        <div class="bg-white p-6 rounded-xl shadow-md mb-6">
            <label class="block text-slate-700 font-bold mb-2">Ingrese su Número de Folio</label>
            <div class="flex gap-2">
                <input type="text" id="inputFolio" class="flex-1 border rounded-lg px-4 py-2 uppercase" placeholder="Ej: CZT-2025-XXXX">
                <button onclick="buscarFolio()" class="bg-blue-900 text-white px-6 py-2 rounded-lg font-bold">Buscar</button>
            </div>
        </div>

        <!-- Área de Resultados (Oculta al inicio) -->
        <div id="resultadoArea" class="hidden">
            <!-- Detalles -->
            <div class="bg-white p-6 rounded-xl shadow-md mb-6 border-l-4 border-blue-500">
                <div class="flex justify-between items-start">
                    <div>
                        <h2 id="resTitulo" class="text-xl font-bold text-slate-800"></h2>
                        <p id="resCategoria" class="text-sm text-slate-500"></p>
                    </div>
                    <span id="resEstatus" class="px-3 py-1 rounded-full text-xs font-bold text-white"></span>
                </div>
                <p id="resDescripcion" class="mt-4 text-slate-700 bg-slate-50 p-3 rounded"></p>
                
                <div id="resEvidencia" class="mt-4 hidden">
                    <p class="text-xs text-slate-400 mb-1">Evidencia Adjunta:</p>
                    <img id="imgEvidencia" src="" class="h-32 rounded border shadow-sm cursor-pointer" onclick="window.open(this.src)">
                </div>
            </div>

            <!-- Chat -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden flex flex-col h-96">
                <div class="bg-slate-200 p-3 font-bold text-slate-700 text-sm flex justify-between">
                    <span><i class="fa-solid fa-comments"></i> Historial de Respuesta</span>
                </div>
                
                <div id="chatBox" class="flex-1 overflow-y-auto p-4 space-y-3 bg-slate-50">
                    <!-- Mensajes cargados aquí -->
                </div>

                <div class="p-3 bg-white border-t flex gap-2">
                    <input type="text" id="msjInput" class="flex-1 border rounded-full px-4 py-2 text-sm focus:outline-none focus:border-blue-500" placeholder="Escriba una respuesta...">
                    <button onclick="enviarMensaje()" class="bg-blue-600 text-white w-10 h-10 rounded-full hover:bg-blue-700 flex items-center justify-center">
                        <i class="fa-solid fa-paper-plane text-sm"></i>
                    </button>
                </div>
            </div>
        </div>

    </div>

    <script>
        let currentTicketId = null;

        async function buscarFolio() {
            const folio = document.getElementById('inputFolio').value.trim();
            if(!folio) return alert("Escriba un folio");

            const res = await fetch(`api/chat.php?action=get&folio=${folio}`);
            const data = await res.json();

            if(data.success) {
                mostrarTicket(data.ticket, data.mensajes);
            } else {
                alert("Folio no encontrado.");
            }
        }

        function mostrarTicket(ticket, mensajes) {
            currentTicketId = ticket.id;
            document.getElementById('resultadoArea').classList.remove('hidden');
            
            document.getElementById('resTitulo').textContent = ticket.titulo;
            document.getElementById('resCategoria').textContent = ticket.categoria;
            document.getElementById('resDescripcion').textContent = ticket.descripcion;
            
            const badge = document.getElementById('resEstatus');
            badge.textContent = ticket.estatus;
            badge.className = `px-3 py-1 rounded-full text-xs font-bold text-white ${
                ticket.estatus === 'Abierto' ? 'bg-red-500' : 
                ticket.estatus === 'En Proceso' ? 'bg-yellow-500' : 'bg-green-500'
            }`;

            if(ticket.imagen_path) {
                document.getElementById('resEvidencia').classList.remove('hidden');
                document.getElementById('imgEvidencia').src = ticket.imagen_path;
            } else {
                document.getElementById('resEvidencia').classList.add('hidden');
            }

            renderChat(mensajes);
        }

        function renderChat(mensajes) {
            const box = document.getElementById('chatBox');
            box.innerHTML = '';
            
            if(mensajes.length === 0) {
                box.innerHTML = '<p class="text-center text-xs text-slate-400 mt-4">Sin mensajes aún.</p>';
                return;
            }

            mensajes.forEach(m => {
                const esMio = m.remitente === 'ciudadano';
                const div = document.createElement('div');
                div.className = `flex ${esMio ? 'justify-end' : 'justify-start'}`;
                div.innerHTML = `
                    <div class="max-w-[80%] rounded-lg px-3 py-2 text-sm shadow-sm ${
                        esMio ? 'bg-blue-100 text-blue-900 rounded-br-none' : 'bg-white text-slate-700 rounded-bl-none border'
                    }">
                        <p>${m.mensaje}</p>
                        <span class="text-[10px] opacity-50 block text-right mt-1">${m.fecha_corta}</span>
                    </div>
                `;
                box.appendChild(div);
            });
            box.scrollTop = box.scrollHeight;
        }

        async function enviarMensaje() {
            const txt = document.getElementById('msjInput').value.trim();
            if(!txt || !currentTicketId) return;

            const formData = new FormData();
            formData.append('ticket_id', currentTicketId);
            formData.append('mensaje', txt);
            formData.append('remitente', 'ciudadano');

            await fetch('api/chat.php', { method: 'POST', body: formData });
            
            document.getElementById('msjInput').value = '';
            buscarFolio(); // Recargar chat
        }
    </script>
</body>
</html>