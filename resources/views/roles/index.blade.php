@extends('layouts.app')
@section('title', 'Roles')
@section('page-title', 'Roles')
@section('breadcrumb', 'Listado')
@section('content')
    <div class="card content-card">
        <div class="card-body">
            <div class="section-heading">
                <div>
                    <h5 class="card-title">Listado de roles</h5>
                    <p class="text-muted mb-0">Consulta y administra los roles y sus permisos.</p>
                </div>
                @if (auth()->user()->hasPermission('roles.create'))
                    <a class="btn btn-primary" href="{{ route('roles.create') }}"><i class="bi bi-plus-lg me-2"></i>Nuevo rol</a>
                @endif
            </div>

            <form method="GET" action="{{ route('roles.index') }}" class="filter-row">
                <div class="search-field">
                    <i class="bi bi-search"></i>
                    <input type="search" name="search" value="{{ request('search') }}" placeholder="Buscar rol..." aria-label="Buscar roles">
                </div>
                <button class="btn btn-outline-primary" type="submit">Buscar</button>
                @if (request('search'))
                    <a class="btn btn-light" href="{{ route('roles.index') }}">Limpiar</a>
                @endif
            </form>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Permisos</th>
                            @if (auth()->user()->hasPermission('roles.update') || auth()->user()->hasPermission('roles.delete'))
                                <th class="text-end">Acciones</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($roles as $role)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $role->role_name }}</div>
                                    <small class="text-muted">ID #{{ $role->role_id }}</small>
                                </td>
                                <td>{{ $role->role_description ?: '—' }}</td>
                                <td>{{ $role->permissions->count() }}</td>
                                @if (auth()->user()->hasPermission('roles.update') || auth()->user()->hasPermission('roles.delete'))
                                    <td>
                                        <div class="action-buttons justify-content-end">
                                            @if (auth()->user()->hasPermission('roles.update'))
                                                <a class="icon-action edit" href="{{ route('roles.edit', $role->role_id) }}" title="Editar rol" aria-label="Editar rol">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                            @endif
                                            @if (auth()->user()->hasPermission('roles.delete'))
                                                <form method="POST" action="{{ route('roles.delete', $role->role_id) }}"
                                                    onsubmit="return confirm('¿Deseas eliminar este rol? Esta acción no se puede deshacer.')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="icon-action delete" type="submit" title="Eliminar rol" aria-label="Eliminar rol">
                                                        <i class="bi bi-trash3"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ auth()->user()->hasPermission('roles.update') || auth()->user()->hasPermission('roles.delete') ? 4 : 3 }}">
                                    <div class="empty-state">
                                        <div class="empty-icon"><i class="bi bi-shield-x"></i></div>
                                        <h6>No hay roles para mostrar</h6>
                                        <p>{{ request('search') ? 'Prueba con otro término de búsqueda.' : 'Todavía no se han creado roles.' }}</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($roles->hasPages())
                <div class="pagination-wrap">
                    <span class="text-muted small">Mostrando {{ $roles->firstItem() }}–{{ $roles->lastItem() }} de {{ $roles->total() }} roles</span>
                    {{ $roles->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
