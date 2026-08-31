<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta en Modo Invitado - Padrón MCJ</title>
    @if(file_exists(public_path('images/logo-mcj.png')))
        <link rel="icon" href="{{ asset('images/logo-mcj.png') }}" type="image/png">
    @endif
    
    <!-- Google Fonts: Instrument Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400..700;1,400..700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Instrument Sans', sans-serif;
            background-color: #f8fafc;
        }
        .banner-header {
            background: linear-gradient(135deg, #1e1b4b 0%, #312e81 60%, #0f172a 100%) !important;
            color: #ffffff !important;
        }
        .btn-premium {
            background: linear-gradient(135deg, var(--brand-600) 0%, var(--brand-700) 100%) !important;
            box-shadow: 0 4px 15px rgba(79, 70, 229, 0.25) !important;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
            color: #ffffff !important;
            border-radius: 0.75rem !important; /* matches rounded-xl */
        }
        .btn-premium:hover {
            background: linear-gradient(135deg, var(--brand-500) 0%, var(--brand-600) 100%) !important;
            transform: translateY(-1px);
        }
    </style>
</head>
<body class="min-h-full flex flex-col text-slate-800 antialiased">

    <!-- Header / Navbar Minimalista -->
    <header class="sticky top-0 z-40 w-full border-b border-slate-200 bg-white shadow-sm">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between">
                <!-- Logotipo -->
                <div class="flex items-center gap-3">
                    <a href="{{ route('invitado.consulta') }}" class="flex items-center gap-2.5 transition hover:opacity-90">
                        @if(file_exists(public_path('images/logo-mcj.png')))
                            <img src="{{ asset('images/logo-mcj.png') }}" class="h-10 w-auto object-contain" alt="Logo MCJ">
                        @else
                            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-700 text-white shadow-md">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                        @endif
                        <span class="text-lg font-bold tracking-tight text-slate-900">Padrón <span class="text-indigo-800 font-extrabold">MCJ</span></span>
                    </a>
                    <span class="inline-flex items-center rounded-full bg-indigo-50 px-2.5 py-0.5 text-xs font-bold text-indigo-700 border border-indigo-200">
                        Modo Invitado
                    </span>
                </div>
                
                <!-- Acceso a Login -->
                <div>
                    <a href="{{ route('login') }}" 
                       class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-slate-700 shadow-sm hover:bg-slate-50 transition">
                        <svg class="h-4 w-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                        </svg>
                        Iniciar Sesión
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Contenido Principal -->
    <main class="flex-1 mx-auto max-w-7xl w-full px-4 sm:px-6 lg:px-8 py-8 space-y-8">
        
        <!-- Banner Encabezado -->
        <div class="rounded-3xl p-6 sm:p-8 shadow-xl relative overflow-hidden banner-header">
            <div class="relative z-10 max-w-2xl">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-indigo-500/30 px-3 py-1 text-xs font-semibold text-indigo-200 border border-indigo-400/30 mb-3">
                    <svg class="h-3.5 w-3.5 text-indigo-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    Consulta Pública de Retiros
                </span>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white">Buscar Retiro o Evento</h1>
                <p class="mt-2 text-sm text-indigo-100 leading-relaxed">
                    Selecciona el tipo de retiro e ingresa el número correspondiente para consultar la información del evento, la fotografía grupal y la circular oficial.
                </p>
            </div>
        </div>

        <!-- Formulario de Búsqueda -->
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <form action="{{ route('invitado.consulta') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-4 items-end">
                
                <!-- Tipo de Retiro -->
                <div class="sm:col-span-5">
                    <label for="tipo_evento_id" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5 pl-1">
                        Tipo de Retiro / Evento
                    </label>
                    <select name="tipo_evento_id" id="tipo_evento_id" required
                            class="block w-full rounded-xl border border-slate-300 bg-white py-2.5 px-3 text-sm text-slate-800 font-medium focus:border-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-600/20 shadow-sm">
                        <option value="">-- Seleccionar Tipo --</option>
                        @foreach($tiposEvento as $tipo)
                            <option value="{{ $tipo->id }}" {{ (string)$tipoEventoId === (string)$tipo->id ? 'selected' : '' }}>
                                {{ $tipo->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Número de Retiro -->
                <div class="sm:col-span-4">
                    <label for="numero_evento" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5 pl-1">
                        Número de Retiro
                    </label>
                    <input type="number" min="1" name="numero_evento" id="numero_evento" value="{{ $numeroEvento }}" required placeholder="Ej: 15"
                           class="block w-full rounded-xl border border-slate-300 bg-white py-2.5 px-3 text-sm text-slate-800 font-medium focus:border-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-600/20 shadow-sm">
                </div>

                <!-- Botón Buscar -->
                <div class="sm:col-span-3">
                    <button type="submit" class="w-full flex items-center justify-center gap-2 rounded-xl py-2.5 px-4 text-sm font-semibold text-white cursor-pointer btn-premium">
                        <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Buscar Evento
                    </button>
                </div>
            </form>
        </div>

        <!-- Resultados de la Búsqueda -->
        @if($tipoEventoId && $numeroEvento)
            @if($evento)
                <div class="space-y-6">
                    
                    <!-- Ficha General del Evento -->
                    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-100 pb-5">
                            <div class="flex items-center gap-3.5">
                                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-700 font-extrabold text-xl border border-indigo-100 shadow-sm shrink-0">
                                    {{ substr($evento->tipoEvento->nombre ?? 'E', 0, 2) }}
                                </div>
                                <div>
                                    <h2 class="text-xl font-extrabold text-slate-900 leading-tight">
                                        {{ $evento->tipoEvento->nombre ?? 'Retiro' }} #{{ $evento->numero_evento }}
                                    </h2>
                                    <span class="text-xs font-semibold text-slate-500 flex items-center gap-1 mt-1">
                                        <svg class="h-3.5 w-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        </svg>
                                        {{ $evento->lugar }}
                                    </span>
                                </div>
                            </div>

                            <!-- Botón Circular Retiro -->
                            <div>
                                <a href="{{ route('invitado.circular-retiro', $evento->id) }}" target="_blank"
                                   class="inline-flex items-center gap-2 rounded-xl border border-indigo-200 bg-indigo-50 px-4 py-2.5 text-sm font-semibold text-indigo-700 shadow-sm transition hover:bg-indigo-100">
                                    <svg class="h-4.5 w-4.5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9v4a2 2 0 002 2zm8-9v2m-5-2v2m-5-2v2" />
                                    </svg>
                                    Ver Circular de Retiro
                                </a>
                            </div>
                        </div>

                        <!-- Detalles Adicionales (Fechas) -->
                        <div class="mt-4 grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
                            <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                                <span class="block font-semibold text-slate-400 uppercase tracking-wider">Fecha Inicio</span>
                                <span class="font-bold text-slate-700 text-sm mt-0.5 block">{{ $evento->fecha_inicio->format('d/m/Y') }}</span>
                            </div>
                            <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                                <span class="block font-semibold text-slate-400 uppercase tracking-wider">Fecha Fin</span>
                                <span class="font-bold text-slate-700 text-sm mt-0.5 block">{{ $evento->fecha_fin->format('d/m/Y') }}</span>
                            </div>
                            <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                                <span class="block font-semibold text-slate-400 uppercase tracking-wider">Participantes</span>
                                <span class="font-bold text-slate-700 text-sm mt-0.5 block">{{ $evento->participaciones->count() }} personas</span>
                            </div>
                            <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                                <span class="block font-semibold text-slate-400 uppercase tracking-wider">Estado</span>
                                <span class="mt-0.5 block">
                                    @if($evento->activo)
                                        <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-[11px] font-bold text-emerald-700 border border-emerald-200">Activo</span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 border border-slate-200">Inactivo</span>
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Galería de Fotos y Secciones -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        <!-- Fotografía del Evento -->
                        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm space-y-3">
                            <div class="flex items-center justify-between">
                                <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                                    <svg class="h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    Fotografía del Evento
                                </h3>
                            </div>
                            @if($evento->foto_evento)
                                <div class="relative rounded-xl overflow-hidden border border-slate-200 aspect-video bg-slate-50 shadow-sm transition hover:shadow-md">
                                    <a href="{{ $evento->foto_evento_url }}" target="_blank">
                                        <img src="{{ $evento->foto_evento_url }}" alt="Foto del Evento" class="object-cover w-full h-full">
                                    </a>
                                </div>
                            @else
                                <div class="flex flex-col items-center justify-center border border-dashed border-slate-200 rounded-xl aspect-video text-slate-400 bg-slate-50">
                                    <svg class="h-10 w-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span class="text-xs font-semibold mt-2">Sin fotografía oficial cargada</span>
                                </div>
                            @endif
                        </div>

                        <!-- Sección Cocina (Con Excepción Eslabón) -->
                        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm space-y-3">
                            <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                                <svg class="h-5 w-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                                Información de Cocina
                            </h3>

                            @if($isEslabon)
                                <!-- Excepción Eslabón -->
                                <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-amber-900 text-xs leading-relaxed space-y-2">
                                    <div class="flex items-center gap-2 font-bold text-amber-900">
                                        <svg class="h-4.5 w-4.5 text-amber-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                        <span>Excepción de Cocina Eslabón</span>
                                    </div>
                                    <p>
                                        La información, fotografía y nómina del equipo de cocina no están disponibles para los retiros de tipo <strong>Eslabón</strong> por razones de reserva y metodológicas del Movimiento.
                                    </p>
                                </div>
                            @else
                                <!-- Retiro No-Eslabón -->
                                @if($evento->foto_cocina)
                                    <div class="relative rounded-xl overflow-hidden border border-slate-200 aspect-video bg-slate-50 shadow-sm transition hover:shadow-md">
                                        <a href="{{ $evento->foto_cocina_url }}" target="_blank">
                                            <img src="{{ $evento->foto_cocina_url }}" alt="Foto de Cocina" class="object-cover w-full h-full">
                                        </a>
                                    </div>
                                @else
                                    <div class="flex flex-col items-center justify-center border border-dashed border-slate-200 rounded-xl aspect-video text-slate-400 bg-slate-50 p-4 text-center">
                                        <svg class="h-9 w-9 text-slate-300 mb-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span class="text-xs font-semibold text-slate-500">Sin foto de cocina disponible</span>
                                        <a href="mailto:mcjsanjuanjdd@gmail.com?subject=Aporte%20de%20Foto%20de%20Cocina%20-%20{{ urlencode($evento->nombre_completo) }}&body=Hola%20MCJ%20San%20Juan,%0A%0AAdjunto%20la%20foto%20de%20cocina%20correspondiente%20al%20{{ urlencode($evento->nombre_completo) }}.%0A%0AGracias!" 
                                           class="mt-2 inline-flex items-center gap-1 text-[11px] font-bold text-indigo-600 hover:text-indigo-800 hover:underline">
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                            ¿La tienes? ¡Envíala por correo!
                                        </a>
                                    </div>
                                @endif

                                <div class="pt-2">
                                    <a href="{{ route('invitado.circular-cocina', $evento->id) }}" target="_blank"
                                       class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-amber-200 bg-amber-50 px-4 py-2.5 text-xs font-bold text-amber-700 shadow-sm transition hover:bg-amber-100">
                                        <svg class="h-4 w-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9v4a2 2 0 002 2zm8-9v2m-5-2v2m-5-2v2" />
                                        </svg>
                                        Ver Circular de Cocina
                                    </a>
                                </div>
                            @endif
                        </div>

                    </div>

                </div>
            @else
                <!-- Evento No Encontrado -->
                <div class="rounded-2xl border border-rose-200 bg-rose-50 p-8 text-center space-y-3">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-100 text-rose-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-rose-900">No se encontró el evento buscado</h3>
                    <p class="text-xs font-semibold text-rose-700 max-w-md mx-auto">
                        No existe ningún retiro registrado con el tipo seleccionado y el número #{{ $numeroEvento }}. Por favor verifica el número e inténtalo nuevamente.
                    </p>
                    <div class="pt-2">
                        <a href="mailto:mcjsanjuanjdd@gmail.com?subject=Consulta%20o%20Aporte%20de%20Evento%20No%20Encontrado&body=Hola%20MCJ%20San%20Juan,%0A%0AEstaba%20buscando%20el%20evento%20{{ urlencode($tipoEvento) }}%20%23{{ $numeroEvento }}%20y%20no%20lo%20encontré.%0A%0APuedo%20aportar%20la%20siguiente%20información:"
                           class="inline-flex items-center gap-2 rounded-xl bg-rose-600 px-4 py-2 text-xs font-bold text-white shadow hover:bg-rose-700 transition">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            Reportar o Aportar datos de esta Jornada
                        </a>
                    </div>
                </div>
            @endif
        @endif

        <!-- Banner Banner de Colaboración / Aporte por Correo Electrónico -->
        <div class="mt-12 rounded-3xl border border-indigo-100 bg-gradient-to-br from-indigo-900 via-indigo-800 to-slate-900 p-6 sm:p-8 text-white shadow-xl relative overflow-hidden">
            <!-- SVG Decorativo de fondo -->
            <div class="absolute -right-6 -bottom-6 opacity-10 pointer-events-none">
                <svg class="h-64 w-64 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M1.5 8.67v8.58a3 3 0 003 3h15a3 3 0 003-3V8.67l-8.928 5.493a3 3 0 01-3.144 0L1.5 8.67z" />
                    <path d="M22.5 6.908V6.75a3 3 0 00-3-3h-15a3 3 0 00-3 3v.158l9.714 5.978a1.5 1.5 0 001.572 0L22.5 6.908z" />
                </svg>
            </div>

            <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="space-y-2 text-center md:text-left max-w-xl">
                    <div class="inline-flex items-center gap-2 rounded-full bg-indigo-500/20 px-3 py-1 text-xs font-bold text-indigo-200 border border-indigo-400/30">
                        <svg class="h-4 w-4 text-amber-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                        </svg>
                        <span>Colabora con la memoria de MCJ San Juan</span>
                    </div>
                    <h3 class="text-xl sm:text-2xl font-bold text-white tracking-tight">¿Tienes fotos, circulares o datos para sumar al Padrón?</h3>
                    <p class="text-xs sm:text-sm text-indigo-100 font-normal leading-relaxed">
                        Si buscas una circular, foto de retiro o información de alguna jornada que aún no esté disponible, puedes enviárnosla directamente al correo oficial de <strong>MCJ San Juan</strong>.
                    </p>
                </div>

                <div class="flex flex-col sm:flex-row items-center gap-3 shrink-0">
                    <a href="mailto:mcjsanjuanjdd@gmail.com?subject=Aporte%20de%20Informaci%C3%B3n%20/%20Foto%20-%20Padr%C3%B3n%20MCJ&body=Hola%20MCJ%20San%20Juan,%0A%0AQuiero%20aportar%20la%20siguiente%20informaci%C3%B3n/foto/circular:%0A%0A-%20Evento/Jornada:%0A-%20Detalles:%0A%0A¡Muchas%20gracias!" 
                       class="inline-flex items-center justify-center gap-2.5 rounded-2xl bg-amber-400 hover:bg-amber-300 px-5 py-3 text-xs sm:text-sm font-bold text-slate-900 shadow-lg transition transform hover:-translate-y-0.5">
                        <svg class="h-5 w-5 text-slate-900" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        Enviar Aporte por Email
                    </a>

                    <button onclick="navigator.clipboard.writeText('mcjsanjuanjdd@gmail.com'); alert('¡Correo mcjsanjuanjdd@gmail.com copiado al portapapeles!');"
                            class="inline-flex items-center justify-center gap-2 rounded-2xl bg-white/10 hover:bg-white/20 border border-white/20 px-4 py-3 text-xs sm:text-sm font-semibold text-white transition backdrop-blur-md cursor-pointer"
                            title="Copiar dirección de correo">
                        <svg class="h-4.5 w-4.5 text-indigo-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                        Copiar Correo
                    </button>
                </div>
            </div>
        </div>

    </main>

    <!-- Footer Minimalista -->
    <footer class="border-t border-slate-200 bg-white py-4 mt-auto">
        <div class="mx-auto max-w-7xl px-4 text-center text-xs text-slate-400 font-medium">
            Padrón MCJ - Movimiento Círculos de Juventud © {{ date('Y') }} • <a href="mailto:mcjsanjuanjdd@gmail.com" class="text-indigo-600 hover:underline">mcjsanjuanjdd@gmail.com</a>
        </div>
    </footer>

</body>
</html>
