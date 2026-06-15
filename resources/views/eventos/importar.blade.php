@extends('layouts.app')

@section('content')
<!-- Encabezado de la Sección -->
<div class="mb-8 flex items-center justify-between gap-4 flex-col sm:flex-row">
    <div>
        <a href="{{ route('eventos.index') }}" class="group inline-flex items-center gap-1 text-sm font-semibold text-indigo-600 hover:text-indigo-800 transition">
            <svg class="h-4 w-4 transition group-hover:-translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Volver a Eventos
        </a>
        <h1 class="text-3xl font-bold tracking-tight text-slate-900 mt-2">Importación Masiva de Participantes</h1>
        <p class="mt-1.5 text-sm text-slate-500">Carga circulistas a partir de archivos Excel, CSV o PDF, con detección automática de duplicados.</p>
    </div>
    
    <div>
        <button onclick="descargarPlantilla()" 
                class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 focus:outline-none cursor-pointer">
            <svg class="h-4.5 w-4.5 mr-1.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4-4m0 0L8 8m4-4v12" />
            </svg>
            Descargar Plantilla Excel
        </button>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    <!-- Lado Izquierdo: Configuración del Evento e Importación de Archivo -->
    <div class="space-y-6 lg:col-span-1">
        
        <!-- Tarjeta 1: Cargar Archivo -->
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="text-lg font-bold text-slate-900 mb-4">1. Seleccionar Archivo</h3>
            
            <div id="dropzone" 
                 class="relative flex flex-col items-center justify-center rounded-xl border-2 border-dashed border-slate-300 bg-slate-50/50 p-6 text-center transition hover:bg-slate-50 cursor-pointer">
                <input type="file" id="fileInput" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" accept=".xlsx,.xls,.csv,.pdf">
                
                <svg class="h-10 w-10 text-slate-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                
                <p class="text-sm font-semibold text-slate-700">Arrastra tu archivo aquí</p>
                <p class="text-xs text-slate-400 mt-1">Soporta Excel (.xlsx, .xls), CSV o PDF</p>
                
                <div id="fileInfo" class="hidden mt-4 w-full bg-indigo-50 border border-indigo-100 rounded-lg p-2.5 text-xs text-indigo-700 text-left">
                    <span class="font-bold">Archivo cargado:</span> <span id="fileName" class="break-all font-mono"></span>
                </div>
            </div>
        </div>
        
        <!-- Tarjeta 2: Configuración del Evento destino -->
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="text-lg font-bold text-slate-900 mb-4">2. Evento Destino</h3>
            
            <!-- Selector de Modo de Evento -->
            <div class="grid grid-cols-2 gap-2 p-1 bg-slate-100 rounded-lg mb-4">
                <button type="button" id="btnModoExistente" onclick="cambiarModoEvento('existente')" 
                        class="w-full py-1.5 text-xs font-semibold rounded-md shadow-sm bg-white text-indigo-700 focus:outline-none transition">
                    Asociar a Existente
                </button>
                <button type="button" id="btnModoNuevo" onclick="cambiarModoEvento('nuevo')" 
                        class="w-full py-1.5 text-xs font-semibold rounded-md text-slate-600 hover:text-slate-900 focus:outline-none transition">
                    Crear Nuevo Evento
                </button>
            </div>
            
            <!-- Formulario: Evento Existente -->
            <div id="formEventoExistente" class="space-y-4">
                <div>
                    <label for="evento_id" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Seleccionar Evento</label>
                    <select id="evento_id" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:outline-none shadow-sm">
                        <option value="">-- Seleccionar un evento --</option>
                        @foreach($eventos as $e)
                            <option value="{{ $e->id }}">
                                {{ $e->tipoEvento->nombre ?? 'Retiro' }} Nº {{ $e->numero_evento }} ({{ $e->lugar }} - {{ $e->fecha_inicio->format('d/m/Y') }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <!-- Formulario: Evento Nuevo -->
            <div id="formEventoNuevo" class="hidden space-y-4">
                <div>
                    <label for="tipo_evento_id" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Tipo de Evento</label>
                    <select id="tipo_evento_id" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:outline-none shadow-sm">
                        <option value="">-- Seleccionar --</option>
                        @foreach($tiposEvento as $te)
                            <option value="{{ $te->id }}">{{ $te->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label for="numero_evento" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Número de Evento</label>
                    <input type="number" id="numero_evento" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:outline-none shadow-sm" placeholder="Ej. 102">
                </div>
                
                <div>
                    <label for="lugar" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Lugar de Realización</label>
                    <input type="text" id="lugar" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:outline-none shadow-sm" placeholder="Ej. Casa del Circulista">
                </div>
                
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="fecha_inicio" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Fecha de Inicio</label>
                        <input type="date" id="fecha_inicio" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:outline-none shadow-sm">
                    </div>
                    <div>
                        <label for="fecha_fin" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Fecha de Fin</label>
                        <input type="date" id="fecha_fin" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:outline-none shadow-sm">
                    </div>
                </div>
                
                <div>
                    <label for="observaciones" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Observaciones</label>
                    <textarea id="observaciones" rows="3" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:outline-none shadow-sm" placeholder="Opcional..."></textarea>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Lado Derecho: Vista Previa y Edición Interactiva -->
    <div class="lg:col-span-2 space-y-6">
        
        <!-- Tarjeta Grilla Editable -->
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden flex flex-col min-h-[500px]">
            
            <div class="border-b border-slate-200/80 bg-slate-50/50 p-4 flex items-center justify-between gap-4 flex-col sm:flex-row">
                <div>
                    <h3 class="text-lg font-bold text-slate-900">3. Grilla Editable / Vista Previa</h3>
                    <p class="text-xs text-slate-400">Edita celdas directamente o elimina filas erróneas antes de importar.</p>
                </div>
                <div class="text-xs font-semibold px-2.5 py-1 rounded-full bg-slate-100 text-slate-700" id="rowCountDisplay">
                    0 registros a procesar
                </div>
            </div>
            
            <!-- Tabla de Datos -->
            <div class="flex-1 overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200" id="previewTable">
                    <thead class="bg-slate-50/70">
                        <tr>
                            <th scope="col" class="py-3 pl-4 pr-2 text-center text-xs font-bold uppercase tracking-wider text-slate-500">Acciones</th>
                            <th scope="col" class="px-2 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Estado</th>
                            <th scope="col" class="px-2 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Apellido</th>
                            <th scope="col" class="px-2 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Nombre</th>
                            <th scope="col" class="px-2 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Fecha Nac.</th>
                            <th scope="col" class="px-2 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Celular</th>
                            <th scope="col" class="px-2 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Email</th>
                            <th scope="col" class="px-2 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Domicilio</th>
                            <th scope="col" class="px-2 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500" style="width: 176px; min-width: 176px;">Rol</th>
                            <th scope="col" class="px-2 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Grupo/Patrulla</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white" id="previewTableBody">
                        <tr>
                            <td colspan="10" class="py-16 text-center text-slate-400 italic text-sm">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <svg class="h-8 w-8 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-3-3v6m-9-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Sube un archivo para comenzar</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- Barra de Acciones Finales -->
            <div class="border-t border-slate-100 bg-slate-50/50 p-4 flex items-center justify-end gap-3">
                <button type="button" onclick="limpiarImportador()"
                        class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 cursor-pointer">
                    Limpiar Todo
                </button>
                <button type="button" id="btnSubmitImport" onclick="enviarImportacion()" disabled
                        class="rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-md shadow-indigo-100 transition hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer">
                    Confirmar e Importar
                </button>
            </div>
            
        </div>
    </div>
</div>

<!-- Modal de Carga / Animación -->
<div id="loadingModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm hidden">
    <div class="w-full max-w-md bg-white rounded-2xl p-6 shadow-xl text-center space-y-4">
        <div class="flex items-center justify-center">
            <svg class="animate-spin h-12 w-12 text-indigo-600" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>
        <h4 id="loadingTitle" class="text-base font-bold text-slate-900">Procesando archivo...</h4>
        <p id="loadingMsg" class="text-xs text-slate-500">Extrayendo información y buscando duplicados en el padrón, por favor espera.</p>
    </div>
</div>

<!-- Scripts de Mapeo y Librerías CDN -->
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

    // Lista de roles pre-cargada para el frontend
    const ROLES = @json($roles);
    let IMPORT_DATA = [];
    let MODO_EVENTO = 'existente'; // o 'nuevo'

    // Cambiar entre Asociar a existente o Crear nuevo
    function cambiarModoEvento(modo) {
        MODO_EVENTO = modo;
        
        const btnExistente = document.getElementById('btnModoExistente');
        const btnNuevo = document.getElementById('btnModoNuevo');
        
        const formExistente = document.getElementById('formEventoExistente');
        const formNuevo = document.getElementById('formEventoNuevo');
        
        if (modo === 'existente') {
            btnExistente.className = 'w-full py-1.5 text-xs font-semibold rounded-md shadow-sm bg-white text-indigo-700 focus:outline-none transition';
            btnNuevo.className = 'w-full py-1.5 text-xs font-semibold rounded-md text-slate-600 hover:text-slate-900 focus:outline-none transition';
            formExistente.style.display = 'block';
            formNuevo.style.display = 'none';
        } else {
            btnExistente.className = 'w-full py-1.5 text-xs font-semibold rounded-md text-slate-600 hover:text-slate-900 focus:outline-none transition';
            btnNuevo.className = 'w-full py-1.5 text-xs font-semibold rounded-md shadow-sm bg-white text-indigo-700 focus:outline-none transition';
            formExistente.style.display = 'none';
            formNuevo.style.display = 'block';
        }
    }

    // Drag and Drop Zone
    const dropzone = document.getElementById('dropzone');
    const fileInput = document.getElementById('fileInput');
    
    fileInput.addEventListener('change', handleFileSelect);
    
    dropzone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropzone.classList.add('border-indigo-500', 'bg-indigo-50/30');
    });
    
    dropzone.addEventListener('dragleave', () => {
        dropzone.classList.remove('border-indigo-500', 'bg-indigo-50/30');
    });
    
    dropzone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropzone.classList.remove('border-indigo-500', 'bg-indigo-50/30');
        if (e.dataTransfer.files.length) {
            fileInput.files = e.dataTransfer.files;
            handleFileSelect();
        }
    });

    function handleFileSelect() {
        const file = fileInput.files[0];
        if (!file) return;
        
        document.getElementById('fileName').innerText = file.name;
        document.getElementById('fileInfo').classList.remove('hidden');
        
        mostrarCarga('Cargando archivo...', 'Extrayendo contenido del documento. Esto puede tomar unos segundos.');
        
        const ext = file.name.split('.').pop().toLowerCase();
        if (ext === 'pdf') {
            parsearPDF(file);
        } else if (['xlsx', 'xls', 'csv'].includes(ext)) {
            parsearExcel(file);
        } else {
            alert('Formato de archivo no soportado.');
            ocultarCarga();
        }
    }

    // Procesar Planilla Excel / CSV
    function parsearExcel(file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const data = new Uint8Array(e.target.result);
            const workbook = XLSX.read(data, {type: 'array'});
            
            // Tomamos la primera hoja del libro
            const sheetName = workbook.SheetNames[0];
            const sheet = workbook.Sheets[sheetName];
            
            // Convertimos la hoja a un formato de array bidimensional (filas y columnas crudas)
            const rows = XLSX.utils.sheet_to_json(sheet, {header: 1, defval: ''});
            
            const registros = parsearRegistros2D(rows);
            procesarRegistrosImportados(registros);
        };
        reader.readAsArrayBuffer(file);
    }

    // Analiza las filas bidimensionales de Excel buscando dinámicamente las cabeceras
    function parsearRegistros2D(rows) {
        // 1. Encontrar la fila que contiene las cabeceras reales
        let headerRowIndex = -1;
        const keywords = [
            'apellido', 'nombre', 'celular', 'email', 'correo', 
            'f. nac', 'f.nac', 'fecha nac', 'fecha de nacimiento', 'nacimiento', 'fec. nac', 'fec.nac', 'fecha', 'nac',
            'domicilio', 'direccion', 'dirección', 'calle', 'address',
            'localidad', 'grupo', 'patrulla', 'rol', 'función', 'funcion', 
            'telefono', 'teléfono', 'tel', 'cel', 'phone', 'mail', 'contacto'
        ];
        
        for (let i = 0; i < rows.length; i++) {
            const row = rows[i];
            if (!Array.isArray(row)) continue;
            
            let matchCount = 0;
            for (let j = 0; j < row.length; j++) {
                const cellVal = String(row[j] || '').toLowerCase().trim();
                if (keywords.some(kw => cellVal === kw || cellVal.includes(kw))) {
                    matchCount++;
                }
            }
            
            const rowText = row.join(' ').toLowerCase();
            const hasNameHeader = rowText.includes('nombre') || rowText.includes('apellido') || rowText.includes('circulista') || rowText.includes('participante');
            const hasMultipleCells = row.filter(c => String(c || '').trim().length > 0).length >= 3;
            
            if (matchCount >= 1 && hasNameHeader && hasMultipleCells) {
                headerRowIndex = i;
                break;
            }
        }
        
        // Si no se detectó cabecera, asumimos que empieza en la fila 0
        if (headerRowIndex === -1) {
            headerRowIndex = 0;
        }
        
        const headers = rows[headerRowIndex] || [];
        
        // 2. Mapear los índices de las columnas correspondientes de forma unívoca
        const indices = {
            apellido_y_nombre: -1,
            apellido: -1,
            nombre: -1,
            fecha_nacimiento: -1,
            celular: -1,
            email: -1,
            domicilio: -1,
            localidad: -1,
            provincia: -1,
            rol: -1,
            grupo: -1
        };
        
        headers.forEach((h, idx) => {
            const hStr = String(h || '').toLowerCase().trim();
            if (!hStr) return;
            
            if (hStr.includes('apellido') && hStr.includes('nombre')) {
                indices.apellido_y_nombre = idx;
            } else if (hStr.includes('apellido') || hStr.includes('apellidos') || hStr === 'last name' || hStr === 'surname') {
                indices.apellido = idx;
            } else if (hStr.includes('nombre') || hStr.includes('nombres') || hStr === 'first name' || hStr === 'name') {
                indices.nombre = idx;
            } else if (hStr.includes('nac') || hStr.includes('birth') || (hStr.includes('fecha') && !hStr.includes('inicio') && !hStr.includes('fin') && !hStr.includes('creacion') && !hStr.includes('registro'))) {
                indices.fecha_nacimiento = idx;
            } else if (hStr.includes('cel') || hStr.includes('tel') || hStr.includes('phone') || hStr.includes('contacto')) {
                indices.celular = idx;
            } else if (hStr.includes('mail') || hStr.includes('email') || hStr.includes('correo')) {
                indices.email = idx;
            } else if (hStr.includes('domicilio') || hStr.includes('direccion') || hStr.includes('dirección') || hStr === 'calle' || hStr === 'address') {
                indices.domicilio = idx;
            } else if (hStr.includes('localidad') || hStr.includes('ciudad') || hStr.includes('barrio')) {
                indices.localidad = idx;
            } else if (hStr.includes('provincia') || hStr.includes('estado')) {
                indices.provincia = idx;
            } else if (hStr === 'rol' || hStr === 'role' || hStr.includes('funcion') || hStr.includes('función') || hStr.includes('puesto')) {
                indices.rol = idx;
            } else if (hStr.includes('grupo') || hStr.includes('patrulla') || hStr.includes('equipo') || hStr === 'gr') {
                indices.grupo = idx;
            }
        });
        
        // 3. Procesar las filas de datos a partir de la fila siguiente a las cabeceras
        const parsedRows = [];
        for (let i = headerRowIndex + 1; i < rows.length; i++) {
            const row = rows[i];
            if (!row || row.length === 0) continue;
            
            // Ignorar filas vacías o con muy pocos datos (totales, observaciones sueltas, etc.)
            const cellCount = row.filter(c => String(c || '').trim().length > 0).length;
            if (cellCount <= 1) continue; 
            
            // Ignorar textos recurrentes de firmas o pie de página
            const rowText = row.join(' ').toLowerCase();
            if (rowText.includes('movimiento círculos') || rowText.includes('casa del circulista') || rowText.includes('jesús te espera') || rowText.includes('nosotros hemos conocido')) {
                continue;
            }
            
            let apellido = indices.apellido !== -1 ? String(row[indices.apellido] || '').trim() : '';
            let nombre = indices.nombre !== -1 ? String(row[indices.nombre] || '').trim() : '';
            
            // Si apellido y nombre vienen juntos (ej. en circular "APELLIDO Y NOMBRE")
            if (!apellido && !nombre && indices.apellido_y_nombre !== -1) {
                const fullname = String(row[indices.apellido_y_nombre] || '').trim();
                if (fullname) {
                    const commaIndex = fullname.indexOf(',');
                    if (commaIndex !== -1) {
                        // Formato: "Alvarez, María Alejandra"
                        apellido = fullname.substring(0, commaIndex).trim();
                        nombre = fullname.substring(commaIndex + 1).trim();
                    } else {
                        // Formato: "Alvarez María Alejandra" (separado por espacio)
                        const words = fullname.split(/\s+/);
                        if (words.length >= 2) {
                            apellido = words[0];
                            nombre = words.slice(1).join(' ');
                        } else {
                            apellido = fullname;
                        }
                    }
                }
            }
            
            // Si no tiene nombre ni apellido mínimo, ignoramos la fila
            if (!nombre && !apellido) continue;
            
            // Procesamiento de fechas
            let fecha_nacimiento = '';
            let sin_anio_nacimiento = false;
            if (indices.fecha_nacimiento !== -1 && row[indices.fecha_nacimiento]) {
                const rawDate = row[indices.fecha_nacimiento];
                if (rawDate instanceof Date || (rawDate && typeof rawDate.getMonth === 'function')) {
                    // Si ya es un objeto Date de JS (evitamos desfases de zona horaria usando UTC)
                    let yyyy = rawDate.getUTCFullYear();
                    let mm = String(rawDate.getUTCMonth() + 1).padStart(2, '0');
                    let dd = String(rawDate.getUTCDate()).padStart(2, '0');
                    fecha_nacimiento = `${yyyy}-${mm}-${dd}`;
                    sin_anio_nacimiento = false;
                } else if (typeof rawDate === 'number') {
                    const res = formatExcelDate(rawDate);
                    fecha_nacimiento = res.date;
                    sin_anio_nacimiento = res.sinAnio;
                } else {
                    const res = parseDateString(String(rawDate));
                    fecha_nacimiento = res.date;
                    sin_anio_nacimiento = res.sinAnio;
                }
            }
            
            const celular = indices.celular !== -1 ? String(row[indices.celular] || '').trim() : '';
            const email = indices.email !== -1 ? String(row[indices.email] || '').trim() : '';
            const domicilio = indices.domicilio !== -1 ? String(row[indices.domicilio] || '').trim() : '';
            const localidad = indices.localidad !== -1 ? String(row[indices.localidad] || '').trim() : '';
            const provincia = indices.provincia !== -1 ? String(row[indices.provincia] || '').trim() : '';
            const rol = indices.rol !== -1 ? String(row[indices.rol] || '').trim() : '';
            const grupo = indices.grupo !== -1 ? String(row[indices.grupo] || '').trim() : '';
            
            parsedRows.push({
                apellido,
                nombre,
                fecha_nacimiento,
                sin_anio_nacimiento,
                celular,
                email,
                domicilio,
                localidad,
                provincia,
                rol,
                grupo
            });
        }
        
        return parsedRows;
    }

    // Convierte número serial de Excel a formato YYYY-MM-DD
    function formatExcelDate(serial) {
        // Redondeamos al entero más cercano para evitar problemas de precisión decimal (ej: 46322.9994 -> 46323)
        const dateInfo = XLSX.SSF.parse_date_code(Math.round(serial));
        let yyyy = dateInfo.y;
        let mm = String(dateInfo.m).padStart(2, '0');
        let dd = String(dateInfo.d).padStart(2, '0');
        
        return {
            date: `${yyyy}-${mm}-${dd}`,
            sinAnio: false
        };
    }

    // Normaliza strings de fechas a YYYY-MM-DD
    function parseDateString(str) {
        if (!str) return { date: '', sinAnio: false };
        
        // Limpiar hora si existe (ej. "28/10/2026 00:00:00" -> "28/10/2026")
        str = str.split(/\s+/)[0];
        
        str = str.replace(/[.\/-]/g, '-').trim();
        
        const meses = {
            'ene': '01', 'enero': '01', 'jan': '01',
            'feb': '02', 'febrero': '02',
            'mar': '03', 'marzo': '03',
            'abr': '04', 'abril': '04', 'apr': '04',
            'may': '05', 'mayo': '05',
            'jun': '06', 'junio': '06',
            'jul': '07', 'julio': '07',
            'ago': '08', 'agosto': '08', 'aug': '08',
            'sep': '09', 'septiembre': '09', 'set': '09',
            'oct': '10', 'octubre': '10',
            'nov': '11', 'noviembre': '11',
            'dic': '12', 'diciembre': '12', 'dec': '12'
        };

        const parts = str.split('-').map(p => p.trim());
        let day = null;
        let month = null;
        let year = null;
        
        if (parts.length === 2) {
            const p0_lower = parts[0].toLowerCase();
            const p1_lower = parts[1].toLowerCase();
            
            if (meses[p0_lower]) {
                month = meses[p0_lower];
                day = parseInt(parts[1], 10);
            } else if (meses[p1_lower]) {
                month = meses[p1_lower];
                day = parseInt(parts[0], 10);
            } else {
                day = parseInt(parts[0], 10);
                month = parts[1].padStart(2, '0');
            }
            year = 1904;
            
            if (day && month && !isNaN(day) && parseInt(month, 10) >= 1 && parseInt(month, 10) <= 12 && day >= 1 && day <= 31) {
                return {
                    date: `1904-${String(month).padStart(2, '0')}-${String(day).padStart(2, '0')}`,
                    sinAnio: true
                };
            }
        } else if (parts.length === 3) {
            let p0 = parts[0];
            let p1 = parts[1];
            let p2 = parts[2];
            
            const p1_lower = p1.toLowerCase();
            if (meses[p1_lower]) {
                p1 = meses[p1_lower];
            }
            
            if (p0.length === 4) {
                year = parseInt(p0, 10);
                month = p1;
                day = parseInt(p2, 10);
            } else if (p2.length === 4 || p2.length === 2) {
                year = parseInt(p2, 10);
                if (p2.length === 2) {
                    year = year < 50 ? 2000 + year : 1900 + year;
                }
                
                const p0_num = parseInt(p0, 10);
                const p1_num = parseInt(p1, 10);
                
                // Si el segundo término (p1) es mayor a 12, es el día (formato MM-DD-YYYY o MM/DD/YYYY)
                if (p1_num > 12 && p0_num <= 12) {
                    month = p0_num;
                    day = p1_num;
                } else {
                    // Formato por defecto: DD-MM-YYYY
                    month = p1_num;
                    day = p0_num;
                }
            }
            
            if (day && month && year && !isNaN(day) && !isNaN(year)) {
                return {
                    date: `${year}-${String(month).padStart(2, '0')}-${String(day).padStart(2, '0')}`,
                    sinAnio: false
                };
            }
        }
        
        const parsed = Date.parse(str);
        if (!isNaN(parsed)) {
            const d = new Date(parsed);
            let y = d.getFullYear();
            let mm = d.getMonth() + 1;
            let dd = d.getDate();
            
            // Si la cadena parece UTC/ISO, usamos métodos UTC para evitar desfase de zona horaria
            if (str.includes('T') || str.includes('Z')) {
                y = d.getUTCFullYear();
                mm = d.getUTCMonth() + 1;
                dd = d.getUTCDate();
            }
            
            const mmStr = String(mm).padStart(2, '0');
            const ddStr = String(dd).padStart(2, '0');
            return {
                date: `${y}-${mmStr}-${ddStr}`,
                sinAnio: false
            };
        }
        
        return { date: '', sinAnio: false };
    }

    // Procesar PDF usando PDF.js
    function parsearPDF(file) {
        const reader = new FileReader();
        reader.onload = async function() {
            try {
                const typedarray = new Uint8Array(this.result);
                const pdf = await pdfjsLib.getDocument(typedarray).promise;
                let fullText = '';
                
                // Extraemos el texto de todas las páginas del PDF
                for (let i = 1; i <= pdf.numPages; i++) {
                    const page = await pdf.getPage(i);
                    const textContent = await page.getTextContent();
                    const textItems = textContent.items.map(item => item.str);
                    
                    // Tratamos de reconstruir líneas de texto
                    fullText += textItems.join('\n') + '\n';
                }
                
                const registros = parsearTextoPDF(fullText);
                procesarRegistrosImportados(registros);
            } catch (err) {
                console.error(err);
                alert('Error al leer el archivo PDF.');
                ocultarCarga();
            }
        };
        reader.readAsArrayBuffer(file);
    }

    // Parser heurístico de texto crudo de PDF
    function parsearTextoPDF(text) {
        const lines = text.split('\n').map(l => l.trim()).filter(l => l.length > 0);
        const registros = [];
        
        // Expresión regular simplificada para buscar emails
        const emailRegex = /[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/;
        // Expresión regular simplificada para buscar teléfonos / celulares
        const phoneRegex = /(?:\+?54\s?)?(?:9?\d{2,4})?\s?\d{6,8}/;
        
        lines.forEach(line => {
            // Saltamos encabezados o firmas de circular conocidas
            if (line.includes('MOVIMIENTO') || line.includes('CIRCULISTAS') || line.includes('Casa del Circulista') || line.includes('Misa del Circulista') || line.includes('Asesor') || line.includes('Rector') || line.includes('Cocinero')) {
                return;
            }
            
            // Limpieza y parseo de columnas separadas por tabulaciones, comas o múltiples espacios
            const parts = line.split(/\t| {2,}/).map(p => p.trim()).filter(p => p.length > 0);
            
            if (parts.length >= 2) {
                // Buscamos si es una línea de participante: Apellido, Nombre
                let apellido = '';
                let nombre = '';
                let email = '';
                let celular = '';
                let rol = '';
                let grupo = '';
                
                // Primer elemento suele tener Apellido y Nombre (a veces juntos "Alvarez Maria")
                const namePart = parts[0];
                const commaIndex = namePart.indexOf(',');
                if (commaIndex !== -1) {
                    apellido = namePart.substring(0, commaIndex).trim();
                    nombre = namePart.substring(commaIndex + 1).trim();
                } else {
                    const words = namePart.split(' ');
                    if (words.length >= 2) {
                        apellido = words[0];
                        nombre = words.slice(1).join(' ');
                    } else {
                        apellido = namePart;
                    }
                }
                
                // Mapear elementos adicionales del array
                parts.slice(1).forEach(part => {
                    if (emailRegex.test(part)) {
                        email = part.match(emailRegex)[0];
                    } else if (phoneRegex.test(part)) {
                        celular = part;
                    } else if (ROLES.some(r => r.nombre.toLowerCase() === part.toLowerCase())) {
                        rol = part;
                    } else if (part.length < 15 && isNaN(part)) {
                        grupo = part;
                    }
                });
                
                if (nombre || apellido) {
                    registros.push({
                        apellido,
                        nombre,
                        fecha_nacimiento: '',
                        sin_anio_nacimiento: false,
                        celular,
                        email,
                        domicilio: '',
                        localidad: '',
                        provincia: '',
                        rol,
                        grupo
                    });
                }
            }
        });
        
        return registros;
    }

    // Enviar lista a la BD para verificar duplicados masivamente
    async function procesarRegistrosImportados(registros) {
        if (!registros || registros.length === 0) {
            alert('No se encontraron registros legibles en el archivo.');
            ocultarCarga();
            return;
        }

        // Estructurar registros y validar campos básicos
        IMPORT_DATA = registros.map(r => {
            // Intentar formatear la fecha
            let fecha = '';
            let sinAnio = r.sin_anio_nacimiento || false;
            if (r.fecha_nacimiento) {
                if (/^\d{4}-\d{2}-\d{2}$/.test(r.fecha_nacimiento)) {
                    fecha = r.fecha_nacimiento;
                } else {
                    const dateObj = new Date(r.fecha_nacimiento);
                    if (!isNaN(dateObj.getTime())) {
                        fecha = dateObj.toISOString().split('T')[0];
                    }
                }
            }

            // Asignar Rol ID
            let rolId = 1; // Default: Circulista
            if (r.rol) {
                const matchedRol = ROLES.find(rol => rol.nombre.toLowerCase().trim() === r.rol.toLowerCase().trim());
                if (matchedRol) rolId = matchedRol.id;
            }

            return {
                db_id: null,
                apellido: r.apellido || '',
                nombre: r.nombre || '',
                fecha_nacimiento: fecha,
                sin_anio_nacimiento: sinAnio,
                celular: r.celular || '',
                email: r.email || '',
                domicilio: r.domicilio || '',
                localidad: r.localidad || '',
                provincia: r.provincia || '',
                rol_id: rolId,
                grupo: r.grupo || '',
                estado: 'validando'
            };
        });

        // Enviar consulta en segundo plano a /circulistas/verificar-importables
        try {
            const response = await fetch("{{ route('circulistas.verificarImportables') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': "{{ csrf_token() }}",
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    personas: IMPORT_DATA.map(r => ({ 
                        nombre: r.nombre, 
                        apellido: r.apellido,
                        celular: r.celular
                    }))
                })
            });

            const result = await response.json();
            if (result.success) {
                // Marcar coincidencias
                for (const idx in result.coincidencias) {
                    const dbMatch = result.coincidencias[idx];
                    IMPORT_DATA[idx].db_id = dbMatch.id;
                    IMPORT_DATA[idx].estado = 'existente';
                    // Prefilar los campos si vienen vacíos en la planilla
                    if (!IMPORT_DATA[idx].celular && dbMatch.celular) IMPORT_DATA[idx].celular = dbMatch.celular;
                    if (!IMPORT_DATA[idx].email && dbMatch.email) IMPORT_DATA[idx].email = dbMatch.email;
                    if (!IMPORT_DATA[idx].fecha_nacimiento && dbMatch.fecha_nacimiento) {
                        IMPORT_DATA[idx].fecha_nacimiento = dbMatch.fecha_nacimiento;
                        IMPORT_DATA[idx].sin_anio_nacimiento = dbMatch.sin_anio_nacimiento;
                    }
                    if (!IMPORT_DATA[idx].domicilio && dbMatch.domicilio) IMPORT_DATA[idx].domicilio = dbMatch.domicilio;
                    if (!IMPORT_DATA[idx].localidad && dbMatch.localidad) IMPORT_DATA[idx].localidad = dbMatch.localidad;
                    if (!IMPORT_DATA[idx].provincia && dbMatch.provincia) IMPORT_DATA[idx].provincia = dbMatch.provincia;
                }
                
                // Completar los que no coincidieron como "nuevos"
                IMPORT_DATA.forEach(r => {
                    if (r.estado === 'validando') r.estado = 'nuevo';
                });
            }
        } catch (err) {
            console.error('Error al validar coincidencias:', err);
            IMPORT_DATA.forEach(r => r.estado = 'nuevo');
        }

        renderGrilla();
        ocultarCarga();
    }

    // Renderizar grilla interactiva en el DOM
    function renderGrilla() {
        const body = document.getElementById('previewTableBody');
        body.innerHTML = '';
        
        document.getElementById('rowCountDisplay').innerText = `${IMPORT_DATA.length} registros a procesar`;
        document.getElementById('btnSubmitImport').disabled = IMPORT_DATA.length === 0;

        if (IMPORT_DATA.length === 0) {
            body.innerHTML = `
                <tr>
                    <td colspan="10" class="py-16 text-center text-slate-400 italic text-sm">
                        <div class="flex flex-col items-center justify-center gap-2">
                            <svg class="h-8 w-8 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-3-3v6m-9-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Sube un archivo para comenzar</span>
                        </div>
                    </td>
                </tr>
            `;
            return;
        }

        IMPORT_DATA.forEach((row, index) => {
            const tr = document.createElement('tr');
            tr.className = "hover:bg-slate-50/50 transition text-sm";
            
            // Badge Estado
            let badgeClass = "bg-emerald-50 text-emerald-800 ring-1 ring-emerald-600/10";
            let badgeText = "Nuevo";
            if (row.estado === 'existente') {
                badgeClass = "bg-sky-50 text-sky-800 ring-1 ring-sky-600/10";
                badgeText = "En Padrón";
            }
            
            // Opciones de Roles
            let roleSelectOptions = '';
            ROLES.forEach(r => {
                const selected = row.rol_id === r.id ? 'selected' : '';
                roleSelectOptions += `<option value="${r.id}" ${selected}>${r.nombre}</option>`;
            });

            tr.innerHTML = `
                <!-- Acciones -->
                <td class="whitespace-nowrap py-3 pl-4 pr-2 text-center">
                    <button type="button" onclick="eliminarFila(${index})" 
                            class="inline-flex h-7 w-7 items-center justify-center rounded-lg border border-red-100 text-red-500 hover:bg-red-50 transition">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </td>

                <!-- Estado -->
                <td class="whitespace-nowrap px-2 py-3">
                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold ${badgeClass}" title="${row.db_id ? 'ID #' + row.db_id : 'Se creará en base de datos'}">
                        ${badgeText}
                    </span>
                </td>
                
                <!-- Apellido -->
                <td class="px-2 py-2">
                    <input type="text" value="${escapeHtml(row.apellido)}" 
                           onchange="updateRowField(${index}, 'apellido', this.value)" 
                           class="w-full text-slate-800 bg-transparent border-b border-transparent focus:border-indigo-500 focus:outline-none py-0.5">
                </td>
                
                <!-- Nombre -->
                <td class="px-2 py-2">
                    <input type="text" value="${escapeHtml(row.nombre)}" 
                           onchange="updateRowField(${index}, 'nombre', this.value)" 
                           class="w-full text-slate-800 bg-transparent border-b border-transparent focus:border-indigo-500 focus:outline-none py-0.5">
                </td>
                
                <!-- Fecha Nacimiento -->
                <td class="px-2 py-2">
                    <input type="date" value="${row.fecha_nacimiento}" 
                           onchange="updateRowField(${index}, 'fecha_nacimiento', this.value)" 
                           class="w-full text-slate-800 bg-transparent border-b border-transparent focus:border-indigo-500 focus:outline-none py-0.5">
                </td>
                
                <!-- Celular -->
                <td class="px-2 py-2">
                    <input type="text" value="${escapeHtml(row.celular)}" 
                           onchange="updateRowField(${index}, 'celular', this.value)" 
                           class="w-full text-slate-800 bg-transparent border-b border-transparent focus:border-indigo-500 focus:outline-none py-0.5">
                </td>
                
                <!-- Email -->
                <td class="px-2 py-2">
                    <input type="email" value="${escapeHtml(row.email)}" 
                           onchange="updateRowField(${index}, 'email', this.value)" 
                           class="w-full text-slate-800 bg-transparent border-b border-transparent focus:border-indigo-500 focus:outline-none py-0.5">
                </td>
                
                <!-- Domicilio -->
                <td class="px-2 py-2">
                    <input type="text" value="${escapeHtml(row.domicilio)}" 
                           onchange="updateRowField(${index}, 'domicilio', this.value)" 
                           class="w-full text-slate-800 bg-transparent border-b border-transparent focus:border-indigo-500 focus:outline-none py-0.5">
                </td>
                
                <!-- Rol -->
                <td class="px-2 py-2">
                    <select onchange="updateRowField(${index}, 'rol_id', parseInt(this.value))" 
                            class="w-full rounded-lg border border-slate-200 bg-white px-2 py-1 text-xs text-slate-800 focus:border-indigo-500 focus:outline-none shadow-sm"
                            style="min-width: 140px;">
                        ${roleSelectOptions}
                    </select>
                </td>
                
                <!-- Grupo / Patrulla -->
                <td class="px-2 py-2">
                    <input type="text" value="${escapeHtml(row.grupo)}" 
                           onchange="updateRowField(${index}, 'grupo', this.value)" 
                           class="w-full text-slate-800 bg-transparent border-b border-transparent focus:border-indigo-500 focus:outline-none py-0.5">
                </td>
            `;
            
            body.appendChild(tr);
        });
    }

    // Actualizar campo de fila localmente
    function updateRowField(index, field, value) {
        IMPORT_DATA[index][field] = value;
    }

    // Eliminar fila de grilla localmente
    function eliminarFila(index) {
        IMPORT_DATA.splice(index, 1);
        renderGrilla();
    }

    function escapeHtml(text) {
        if (!text) return '';
        return text
            .toString()
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    // Enviar importación masiva final
    async function enviarImportacion() {
        // Validar datos de evento antes de enviar
        const payload = {
            evento_modo: MODO_EVENTO,
            participantes: IMPORT_DATA
        };

        if (MODO_EVENTO === 'existente') {
            const select = document.getElementById('evento_id');
            if (!select.value) {
                alert('Debes seleccionar un evento destino.');
                select.focus();
                return;
            }
            payload.evento_id = select.value;
        } else {
            const tipoSelect = document.getElementById('tipo_evento_id');
            const numInput = document.getElementById('numero_evento');
            const lugInput = document.getElementById('lugar');
            const inicioInput = document.getElementById('fecha_inicio');
            const finInput = document.getElementById('fecha_fin');
            const obsTextarea = document.getElementById('observaciones');

            if (!tipoSelect.value || !numInput.value || !lugInput.value || !inicioInput.value || !finInput.value) {
                alert('Debes completar todos los campos del nuevo evento.');
                return;
            }

            payload.tipo_evento_id = tipoSelect.value;
            payload.numero_evento = numInput.value;
            payload.lugar = lugInput.value;
            payload.fecha_inicio = inicioInput.value;
            payload.fecha_fin = finInput.value;
            payload.observaciones = obsTextarea.value;
        }

        mostrarCarga('Guardando datos...', 'Creando evento y vinculando participantes, por favor espera.');

        try {
            const response = await fetch("{{ route('eventos.import.submit') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': "{{ csrf_token() }}",
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            });

            const result = await response.json();
            ocultarCarga();

            if (result.success) {
                alert(`¡Importación Completa!\n\n- Evento: ${result.summary.evento}\n- Circulistas Nuevos: ${result.summary.circulistas_nuevos}\n- Circulistas Existentes: ${result.summary.circulistas_existentes}\n- Participaciones: ${result.summary.participaciones}`);
                window.location.href = "{{ route('eventos.index') }}";
            } else {
                alert('Error al realizar la importación:\n' + result.message);
            }
        } catch (err) {
            console.error('Error al enviar la importación:', err);
            ocultarCarga();
            alert('Ocurrió un error inesperado al comunicarse con el servidor.');
        }
    }

    // Descargar plantilla Excel modelo
    function descargarPlantilla() {
        const headers = [['Apellido', 'Nombre', 'Fecha Nacimiento', 'Celular', 'Email', 'Domicilio', 'Localidad', 'Provincia', 'Rol', 'Grupo']];
        const data = [
            ['Pérez', 'Juan Carlos', '1995-05-15', '2641234567', 'juanperez@example.com', 'Av. Libertador 1234', 'Capital', 'San Juan', 'Circulista', 'San Pedro'],
            ['Gómez', 'María Alejandra', '1988-12-08', '2647654321', 'mariagomez@example.com', 'Calle Mitre 456', 'Rivadavia', 'San Juan', 'Rector', 'Cocinera'],
            ['Sánchez', 'Carlos Raúl', '1990-07-22', '', '', '', '', '', 'Asesor', '']
        ];
        
        const wb = XLSX.utils.book_new();
        const ws = XLSX.utils.aoa_to_sheet(headers.concat(data));
        
        // Darle un poco de diseño a las columnas (ancho recomendado)
        ws['!cols'] = [
            { wch: 15 }, // Apellido
            { wch: 20 }, // Nombre
            { wch: 18 }, // Fecha Nacimiento
            { wch: 15 }, // Celular
            { wch: 25 }, // Email
            { wch: 25 }, // Domicilio
            { wch: 15 }, // Localidad
            { wch: 15 }, // Provincia
            { wch: 12 }, // Rol
            { wch: 15 }  // Grupo
        ];
        
        XLSX.utils.book_append_sheet(wb, ws, 'Plantilla Importación');
        XLSX.writeFile(wb, 'plantilla_importacion_mcj.xlsx');
    }

    function limpiarImportador() {
        if (confirm('¿Estás seguro de que deseas limpiar el archivo y la grilla?')) {
            IMPORT_DATA = [];
            fileInput.value = '';
            document.getElementById('fileInfo').classList.add('hidden');
            renderGrilla();
        }
    }

    function mostrarCarga(title, msg) {
        document.getElementById('loadingTitle').innerText = title;
        document.getElementById('loadingMsg').innerText = msg;
        document.getElementById('loadingModal').classList.remove('hidden');
    }

    function ocultarCarga() {
        document.getElementById('loadingModal').classList.add('hidden');
    }
</script>
@endsection
