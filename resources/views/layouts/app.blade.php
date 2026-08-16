<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Padrón MCJ - Sistema de Gestión</title>
    @if(file_exists(public_path('images/logo-mcj.png')))
        <link rel="icon" href="{{ asset('images/logo-mcj.png') }}" type="image/png">
    @endif
    
    <!-- Google Fonts: Instrument Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400..700;1,400..700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    
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
                        @if(file_exists(public_path('images/logo-mcj.png')))
                            <img src="{{ asset('images/logo-mcj.png') }}" class="h-10 w-auto object-contain" alt="Logo MCJ">
                        @else
                            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-700 text-white shadow-md shadow-indigo-200">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                        @endif
                        <span class="text-lg font-bold tracking-tight text-slate-900">Padrón <span class="text-[#94763e] font-extrabold">MCJ</span></span>
                    </a>
                    
                    <!-- Navegación principal -->
                    <nav class="hidden md:flex items-center gap-1.5">
                        <a href="{{ route('circulistas.index') }}" class="rounded-lg px-3.5 py-2 text-sm font-medium transition-all {{ Request::routeIs('circulistas.index') || (Request::is('circulistas*') && !Request::is('circulistas/duplicados')) ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                            Circulistas
                        </a>
                        <a href="{{ route('eventos.index') }}" class="rounded-lg px-3.5 py-2 text-sm font-medium transition-all {{ Request::is('eventos*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                            Eventos
                        </a>
                        @if(Auth::check() && in_array(Auth::user()->role, ['administrador', 'supervisor']))
                        <a href="{{ route('busqueda.avanzada') }}" class="rounded-lg px-3.5 py-2 text-sm font-medium transition-all {{ Request::is('busqueda-avanzada*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                            Búsqueda Avanzada
                        </a>
                        <a href="{{ route('busqueda.persona') }}" class="rounded-lg px-3.5 py-2 text-sm font-medium transition-all {{ Request::is('busqueda-persona*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                            Historial (2 Años)
                        </a>
                        <a href="{{ route('circulistas.duplicados') }}" class="rounded-lg px-3.5 py-2 text-sm font-medium transition-all {{ Request::routeIs('circulistas.duplicados') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                            Duplicados
                        </a>
                        @endif
                        @if(Auth::check() && Auth::user()->role === 'administrador')
                        <a href="{{ route('usuarios.index') }}" class="rounded-lg px-3.5 py-2 text-sm font-medium transition-all {{ Request::is('usuarios*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                            Usuarios
                        </a>
                        @endif
                    </nav>
                </div>
                
                <!-- Perfil / Acceso Rápido -->
                <div class="flex items-center gap-4">
                    @auth
                        <div class="flex items-center gap-3">
                            <div class="text-right hidden sm:block">
                                <div class="text-sm font-bold text-slate-900">{{ Auth::user()->name }}</div>
                                <div class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">
                                    {{ ucfirst(Auth::user()->role) }}
                                </div>
                            </div>
                            
                            <!-- Role Badge -->
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-indigo-50 text-xs font-bold text-indigo-600 border border-indigo-100" title="{{ Auth::user()->email }}">
                                {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                            </span>
                            
                            <form action="{{ route('logout') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" 
                                        class="hidden sm:inline-flex rounded-xl border border-slate-200 bg-white p-2 text-slate-500 hover:text-rose-600 hover:bg-rose-50 transition cursor-pointer"
                                        title="Cerrar Sesión">
                                    <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    @else
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-800 ring-1 ring-inset ring-emerald-600/10">
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            Postgres Conectado
                        </span>
                    @endauth
                    
                    @auth
                    <!-- Botón de Menú Móvil (Hamburguesa) -->
                    <button type="button" id="mobile-menu-button" class="md:hidden rounded-xl border border-slate-200 bg-white p-2 text-slate-500 hover:text-slate-950 hover:bg-slate-50 transition cursor-pointer" aria-label="Menú Principal">
                        <svg class="h-5 w-5 block" id="hamburger-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg class="h-5 w-5 hidden" id="close-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    @endauth
                </div>
            </div>
        </div>
        
        <!-- Menú Móvil Desplegable -->
        @auth
        <div id="mobile-menu" class="hidden md:hidden border-t border-slate-100 bg-white px-4 py-3 space-y-1 transition-all duration-300">
            <a href="{{ route('circulistas.index') }}" class="block rounded-lg px-3.5 py-2.5 text-sm font-medium {{ Request::routeIs('circulistas.index') || (Request::is('circulistas*') && !Request::is('circulistas/duplicados')) ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                Circulistas
            </a>
            <a href="{{ route('eventos.index') }}" class="block rounded-lg px-3.5 py-2.5 text-sm font-medium {{ Request::is('eventos*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                Eventos
            </a>
            @if(in_array(Auth::user()->role, ['administrador', 'supervisor']))
            <a href="{{ route('busqueda.avanzada') }}" class="block rounded-lg px-3.5 py-2.5 text-sm font-medium {{ Request::is('busqueda-avanzada*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                Búsqueda Avanzada
            </a>
            <a href="{{ route('busqueda.persona') }}" class="block rounded-lg px-3.5 py-2.5 text-sm font-medium {{ Request::is('busqueda-persona*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                Historial (2 Años)
            </a>
            <a href="{{ route('circulistas.duplicados') }}" class="block rounded-lg px-3.5 py-2.5 text-sm font-medium {{ Request::routeIs('circulistas.duplicados') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                Duplicados
            </a>
            @endif
            @if(Auth::user()->role === 'administrador')
            <a href="{{ route('usuarios.index') }}" class="block rounded-lg px-3.5 py-2.5 text-sm font-medium {{ Request::is('usuarios*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                Usuarios
            </a>
            @endif
            
            <!-- Perfil y Cierre de Sesión en móvil -->
            <div class="border-t border-slate-100 my-2 pt-2">
                <div class="px-3.5 py-2">
                    <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Usuario Conectado</div>
                    <div class="text-sm font-bold text-slate-900">{{ Auth::user()->name }}</div>
                    <div class="text-xs font-medium text-slate-500">{{ ucfirst(Auth::user()->role) }}</div>
                </div>
                <form action="{{ route('logout') }}" method="POST" class="block w-full">
                    @csrf
                    <button type="submit" class="w-full text-left rounded-lg px-3.5 py-2 text-sm font-semibold text-rose-600 hover:bg-rose-50 transition cursor-pointer flex items-center gap-2">
                        <svg class="h-4.5 w-4.5 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Cerrar Sesión
                    </button>
                </form>
            </div>
        </div>
        @endauth
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

    <!-- Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Lógica para el menú móvil desplegable
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');
            const hamburgerIcon = document.getElementById('hamburger-icon');
            const closeIcon = document.getElementById('close-icon');

            if (mobileMenuButton && mobileMenu) {
                mobileMenuButton.addEventListener('click', function() {
                    const isHidden = mobileMenu.classList.contains('hidden');
                    if (isHidden) {
                        mobileMenu.classList.remove('hidden');
                        hamburgerIcon.classList.add('hidden');
                        closeIcon.classList.remove('hidden');
                    } else {
                        mobileMenu.classList.add('hidden');
                        hamburgerIcon.classList.remove('hidden');
                        closeIcon.classList.add('hidden');
                    }
                });
            }

            flatpickr("input[type='date']", {
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "d/m/Y",
                locale: "es",
                allowInput: true,
                onReady: function(selectedDates, dateStr, instance) {
                    if (instance.altInput) {
                        instance.altInput.placeholder = "dd/mm/aaaa";
                        // Asegurar consistencia visual copiando clases del original
                        instance.altInput.className = instance.input.className;
                    }
                }
            });
        });
    </script>
</body>
</html>