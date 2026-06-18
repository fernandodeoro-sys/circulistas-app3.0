@extends('layouts.app')

@section('content')

<!-- Encabezado de la Sección -->
<div class="mb-8">
    <a href="{{ route('usuarios.index') }}" class="group inline-flex items-center gap-1 text-sm font-semibold text-indigo-600 hover:text-indigo-800 transition">
        <svg class="h-4 w-4 transition group-hover:-translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Volver a Usuarios
    </a>
    <h1 class="text-3xl font-bold tracking-tight text-slate-900 mt-2">Registrar Nuevo Usuario</h1>
    <p class="mt-1.5 text-sm text-slate-500">Crea credenciales y asigna un nivel de acceso al sistema.</p>
</div>

<!-- Formulario en Tarjeta Premium -->
<div class="max-w-2xl rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
    <form action="{{ route('usuarios.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="space-y-4">
            <!-- Nombre -->
            <div>
                <label for="name" class="block text-sm font-semibold text-slate-700 mb-1">Nombre Completo</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                       class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:outline-none shadow-sm placeholder-slate-400"
                       placeholder="Ej. Juan Pérez">
            </div>

            <!-- Correo Electrónico -->
            <div>
                <label for="email" class="block text-sm font-semibold text-slate-700 mb-1">Correo Electrónico</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required
                       class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:outline-none shadow-sm placeholder-slate-400"
                       placeholder="Ej. juanperez@mcj.org">
            </div>

            <!-- Rol de Acceso -->
            <div>
                <label for="role" class="block text-sm font-semibold text-slate-700 mb-1">Rol / Permisos de Acceso</label>
                <select name="role" id="role" required
                        class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-indigo-500 focus:outline-none shadow-sm">
                    <option value="invitado" {{ old('role') === 'invitado' ? 'selected' : '' }}>Invitado (Solo Búsqueda y Visualización)</option>
                    <option value="administrador" {{ old('role') === 'administrador' ? 'selected' : '' }}>Administrador (Control Total del Sistema)</option>
                </select>
            </div>

            <!-- Contraseña -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 border-t border-slate-100 pt-4">
                <div>
                    <label for="password" class="block text-sm font-semibold text-slate-700 mb-1">Contraseña</label>
                    <input type="password" name="password" id="password" required
                           class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:outline-none shadow-sm placeholder-slate-400"
                           placeholder="Mínimo 6 caracteres">
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-slate-700 mb-1">Confirmar Contraseña</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                           class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:outline-none shadow-sm placeholder-slate-400"
                           placeholder="Repite la contraseña">
                </div>
            </div>
        </div>

        <!-- Botones de Acción -->
        <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-5">
            <a href="{{ route('usuarios.index') }}" 
               class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition cursor-pointer">
                Cancelar
            </a>
            <button type="submit" 
                    class="rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-md shadow-indigo-100 transition hover:bg-indigo-700 cursor-pointer">
                Crear Usuario
            </button>
        </div>
    </form>
</div>

@endsection
