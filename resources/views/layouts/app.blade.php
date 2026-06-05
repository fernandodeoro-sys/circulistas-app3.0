<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-50/50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Padrón MCJ - Sistema de Gestión</title>
    
    <!-- Google Fonts: Instrument Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400..700;1,400..700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body {
            font-family: 'Instrument Sans', sans-serif;
        }
    </style>
</head>
<body class="h-full flex flex-col text-slate-800 antialiased">

    <!-- Header / Navbar Moderno -->
    <header class="sticky top-0 z-40 w-full border-b border-slate-200/80 bg-white/80 backdrop-blur-md">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between">
                <!-- Logotipo -->
                <div class="flex items-center gap-8">
                    <a href="{{ url('/') }}" class="flex items-center gap-2.5 transition hover:opacity-90">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-600 text-white shadow-md shadow-indigo-200">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <span class="text-lg font-bold tracking-tight text-slate-900">Padrón <span class="text-indigo-600">MCJ</span></span>
                    </a>
                    
                    <!-- Navegación principal -->
                    <nav class="hidden md:flex items-center gap-1.5">
                        <a href="{{ route('circulistas.index') }}" class="rounded-lg px-3.5 py-2 text-sm font-medium transition-all {{ Request::is('circulistas*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                            Circulistas
                        </a>
                        <a href="{{ route('eventos.index') }}" class="rounded-lg px-3.5 py-2 text-sm font-medium transition-all {{ Request::is('eventos*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                            Eventos
                        </a>
                        <a href="{{ route('busqueda.avanzada') }}" class="rounded-lg px-3.5 py-2 text-sm font-medium transition-all {{ Request::is('busqueda-avanzada*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                            Búsqueda Avanzada
                        </a>
                    </nav>
                </div>
                
                <!-- Perfil / Acceso Rápido -->
                <div class="flex items-center gap-4">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-800 ring-1 ring-inset ring-emerald-600/10">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        Postgres Conectado
                    </span>
                </div>
            </div>
        </div>
    </header>

    <!-- Contenido Principal -->
    <main class="flex-1 py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            
            <!-- Notificaciones Flash -->
            @if(session('success'))
                <div class="mb-6 rounded-xl border border-emerald-100 bg-emerald-50 p-4 text-emerald-800 shadow-sm flex items-start gap-3 transition-all duration-300" role="alert">
                    <svg class="h-5 w-5 text-emerald-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div class="text-sm font-medium">
                        {{ session('success') }}
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 rounded-xl border border-red-100 bg-red-50 p-4 text-red-800 shadow-sm flex items-start gap-3 transition-all duration-300" role="alert">
                    <svg class="h-5 w-5 text-red-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l-2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div class="text-sm font-medium">
                        {{ session('error') }}
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 rounded-xl border border-red-100 bg-red-50 p-4 text-red-800 shadow-sm flex items-start gap-3 transition-all duration-300" role="alert">
                    <svg class="h-5 w-5 text-red-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l-2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <div class="text-sm font-bold mb-1">Por favor corrige los siguientes errores:</div>
                        <ul class="list-disc list-inside text-xs space-y-1 font-medium text-red-700">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <!-- Contenedor de contenido -->
            <div class="animate-[fadeIn_0.3s_ease-out]">
                @yield('content')
            </div>

        </div>
    </main>

    <!-- Footer Moderno -->
    <footer class="mt-auto border-t border-slate-200/80 bg-white py-6">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 text-center text-xs text-slate-400">
            &copy; {{ date('Y') }} Movimiento de Círculos de Juventud (MCJ). Todos los derechos reservados.
        </div>
    </footer>

</body>
</html>