<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Padrón MCJ</title>
    @if(file_exists(public_path('images/logo-mcj.png')))
        <link class="favicon" rel="icon" href="{{ asset('images/logo-mcj.png') }}" type="image/png">
    @endif

    <!-- Google Fonts: Instrument Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400..700;1,400..700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Instrument Sans', sans-serif;
            background-color: #f8fafc; /* bg-slate-50 */
        }
        /* Fade in animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .btn-premium {
            background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%) !important;
            border-top: 1px solid rgba(255, 255, 255, 0.15) !important;
            box-shadow: 0 4px 15px rgba(79, 70, 229, 0.25), inset 0 1px 0 rgba(255, 255, 255, 0.2) !important;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }
        .btn-premium:hover {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%) !important;
            box-shadow: 0 6px 20px rgba(79, 70, 229, 0.35), inset 0 1px 0 rgba(255, 255, 255, 0.3) !important;
            transform: translateY(-1px);
        }
        .btn-premium:active {
            transform: translateY(1px) scale(0.98);
            box-shadow: 0 2px 10px rgba(79, 70, 229, 0.2) !important;
        }
        .input-premium {
            border: 1px solid #cbd5e1 !important;
            transition: all 0.15s ease-in-out !important;
        }
        .input-premium:focus {
            border-color: #4f46e5 !important;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.12) !important;
            background-color: #ffffff !important;
        }
    </style>
</head>
<body class="h-full flex items-center justify-center p-4 relative overflow-hidden select-none">

    <!-- Logo de fondo (Watermark) centrado y cubriendo gran parte de la pantalla -->
    <div class="absolute inset-0 flex items-center justify-center pointer-events-none z-0 overflow-hidden select-none">
        @if(file_exists(public_path('images/logo-mcj.png')))
            <img src="{{ asset('images/logo-mcj.png') }}" class="w-[85vw] max-w-[650px] aspect-square object-contain opacity-[0.06] select-none" alt="Watermark Logo">
        @else
            <!-- Escudo Cruz MCJ SVG grande en caso de no existir la imagen -->
            <svg class="w-[85vw] max-w-[650px] aspect-square text-indigo-600/5 select-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
        @endif
    </div>

    <div class="w-full max-w-[440px] z-10 animate-[fadeIn_0.5s_ease-out]">
        <!-- Card Contenedor con distribución más sobria -->
        <div class="rounded-3xl border border-slate-200/80 bg-white/90 backdrop-blur-xl px-8 py-10 shadow-[0_8px_30px_rgb(0,0,0,0.03)] ring-1 ring-slate-100/50">
            
            <!-- Logo y Encabezado -->
            <div class="flex flex-col items-center text-center mb-8">
                <div class="flex items-center justify-center bg-transparent mb-4">
                    @if(file_exists(public_path('images/logo-mcj.png')))
                        <img src="{{ asset('images/logo-mcj.png') }}" class="h-20 w-auto max-h-24 object-contain" alt="Logo MCJ">
                    @else
                        <!-- Escudo Cruz MCJ SVG -->
                        <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-700 shadow-sm border border-indigo-100">
                            <svg class="h-10 w-10 text-indigo-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                    @endif
                </div>
                <h1 class="text-3xl font-bold tracking-tight text-slate-900">Padrón <span class="text-indigo-800 font-extrabold">MCJ</span></h1>
                <p class="mt-1.5 text-sm text-slate-500 font-medium">Ingresa al sistema de gestión de circulistas</p>
            </div>

            <!-- Alertas de Estado/Error -->
            @if(session('success'))
                <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 p-3.5 text-xs font-semibold text-emerald-700 flex items-center gap-2">
                    <svg class="h-4.5 w-4.5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-5 rounded-2xl border border-rose-200 bg-rose-50 p-3.5 text-xs font-semibold text-rose-700">
                    <div class="flex items-center gap-2">
                        <svg class="h-4.5 w-4.5 text-rose-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span>{{ $errors->first() }}</span>
                    </div>
                </div>
            @endif

            <!-- Formulario -->
            <form action="{{ route('login') }}" method="POST" class="space-y-5">
                @csrf

                <!-- Email -->
                <div>
                    <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5 pl-1">Correo Electrónico</label>
                    <div class="relative rounded-2xl shadow-sm">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                            <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206" />
                            </svg>
                        </div>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                               class="block w-full rounded-2xl bg-slate-50/50 py-3 pl-11 pr-4 text-sm text-slate-800 placeholder-slate-400 outline-none input-premium"
                               placeholder="ejemplo@mcj.org">
                    </div>
                </div>

                <!-- Contraseña -->
                <div>
                    <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5 pl-1">Contraseña</label>
                    <div class="relative rounded-2xl shadow-sm">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                            <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <input type="password" name="password" id="password" required
                               class="block w-full rounded-2xl bg-slate-50/50 py-3 pl-11 pr-4 text-sm text-slate-800 placeholder-slate-400 outline-none input-premium"
                               placeholder="••••••••">
                    </div>
                </div>

                <!-- Recordarme -->
                <div class="flex items-center justify-between pl-1">
                    <label class="flex items-center cursor-pointer gap-2.5">
                        <input type="checkbox" name="remember" class="h-4.5 w-4.5 rounded-lg border-slate-300 bg-white text-indigo-600 focus:ring-indigo-600 focus:ring-offset-white focus:ring-2">
                        <span class="text-xs font-semibold text-slate-500 select-none">Recordar mi sesión</span>
                    </label>
                </div>

                <!-- Botón de Envío con terminación pulida -->
                <button type="submit"
                        class="w-full flex justify-center items-center rounded-2xl py-3.5 text-sm font-semibold text-white cursor-pointer btn-premium">
                    Iniciar Sesión
                </button>
            </form>

        </div>
    </div>

</body>
</html>
