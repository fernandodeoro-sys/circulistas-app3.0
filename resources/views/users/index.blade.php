@extends('layouts.app')

@section('content')

<!-- Encabezado de la Sección -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
    <div>
        <h1 class="text-3xl font-bold tracking-tight text-slate-900">Usuarios del Sistema</h1>
        <p class="mt-1.5 text-sm text-slate-500 font-medium">Gestiona los accesos, contraseñas y permisos del padrón de MCJ.</p>
    </div>
    
    <div>
        <a href="{{ route('usuarios.create') }}" 
           class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md shadow-indigo-100 transition hover:bg-indigo-700 hover:shadow-lg focus:outline-none cursor-pointer">
            <svg class="h-4.5 w-4.5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Nuevo Usuario
        </a>
    </div>
</div>

<!-- Tarjeta de Contenedor -->
<div class="overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm">
    <!-- Barra de Búsqueda y Filtro -->
    <div class="border-b border-slate-200/80 bg-slate-50/50 p-4">
        <form action="{{ route('usuarios.index') }}" method="GET" class="flex gap-3">
            <div class="relative flex-1">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}"
                       placeholder="Buscar usuario por nombre, correo o rol..." 
                       class="w-full rounded-xl border border-slate-200 bg-white pl-10 pr-4 py-2 text-sm text-slate-900 placeholder-slate-400 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none shadow-sm">
            </div>
            
            <button type="submit" 
                    class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none cursor-pointer">
                Buscar
            </button>
            @if(request('search'))
                <a href="{{ route('usuarios.index') }}" 
                   class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 focus:outline-none">
                    Limpiar
                </a>
            @endif
        </form>
    </div>

    <!-- Tabla de Usuarios -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50/70">
                <tr>
                    <th scope="col" class="py-4 pl-6 pr-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Usuario</th>
                    <th scope="col" class="px-3 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Correo Electrónico</th>
                    <th scope="col" class="px-3 py-4 text-center text-xs font-bold uppercase tracking-wider text-slate-500">Rol</th>
                    <th scope="col" class="px-3 py-4 text-center text-xs font-bold uppercase tracking-wider text-slate-500">Creado</th>
                    <th scope="col" class="py-4 pl-3 pr-6 text-center text-xs font-bold uppercase tracking-wider text-slate-500">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white text-sm font-medium text-slate-700">
                @forelse($users as $user)
                    <tr class="hover:bg-slate-50/50 transition">
                        <!-- Nombre e Iniciales -->
                        <td class="whitespace-nowrap py-4 pl-6 pr-3">
                            <div class="flex items-center gap-3">
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-indigo-50 font-bold text-indigo-600 text-sm border border-indigo-100">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                                <div>
                                    <div class="font-semibold text-slate-900">{{ $user->name }}</div>
                                    @if($user->id === Auth::id())
                                        <span class="inline-flex items-center rounded-md bg-indigo-50 px-1.5 py-0.5 text-[10px] font-bold text-indigo-700 ring-1 ring-inset ring-indigo-700/10 mt-0.5">
                                            Tú (Sesión activa)
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        
                        <!-- Correo -->
                        <td class="whitespace-nowrap px-3 py-4 text-slate-600 font-normal">
                            {{ $user->email }}
                        </td>
                        
                        <!-- Rol Badge -->
                        <td class="whitespace-nowrap px-3 py-4 text-center">
                            @if($user->role === 'administrador')
                                <span class="inline-flex items-center rounded-full bg-purple-50 px-3 py-1 text-xs font-semibold text-purple-800 ring-1 ring-inset ring-purple-700/10">
                                    Administrador
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600 ring-1 ring-inset ring-slate-500/10">
                                    Invitado
                                </span>
                            @endif
                        </td>
                        
                        <!-- Creado -->
                        <td class="whitespace-nowrap px-3 py-4 text-center text-xs text-slate-400 font-normal">
                            {{ $user->created_at->format('d/m/Y H:i') }}
                        </td>
                        
                        <!-- Acciones -->
                        <td class="whitespace-nowrap py-4 pl-3 pr-6 text-center text-sm font-medium">
                            <div class="flex items-center justify-center gap-2">
                                <!-- Editar -->
                                <a href="{{ route('usuarios.edit', $user->id) }}" 
                                   title="Editar Usuario"
                                   class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 text-slate-600 bg-white hover:bg-slate-50 transition hover:text-indigo-600">
                                    <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                                
                                <!-- Eliminar (deshabilitado si es uno mismo) -->
                                @if($user->id !== Auth::id())
                                    <form action="{{ route('usuarios.destroy', $user->id) }}" 
                                          method="POST" 
                                          class="inline" 
                                          onsubmit="return confirm('¿Estás seguro de que deseas eliminar permanentemente a este usuario? Ya no podrá acceder al sistema.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                title="Eliminar Usuario"
                                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-red-100 text-red-500 bg-white hover:bg-red-50 transition cursor-pointer">
                                            <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                @else
                                    <button disabled 
                                            title="No puedes eliminarte a ti mismo"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-100 text-slate-300 bg-slate-50 cursor-not-allowed">
                                        <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center text-slate-400">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <svg class="h-10 w-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <span class="text-sm font-semibold text-slate-500">No se encontraron usuarios</span>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    @if($users->hasPages())
        <div class="border-t border-slate-100 px-6 py-4 bg-slate-50/50">
            {{ $users->links() }}
        </div>
    @endif
</div>

@endsection
