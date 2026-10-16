@extends('layouts.app')
@section('title', 'Usuarios')
@section('page-title', 'Usuarios')
@section('breadcrumb', 'Listado')
@section('content')
<div class="card content-card"><div class="card-body">
 <div class="section-heading"><div><h5 class="card-title">Listado de usuarios</h5><p class="text-muted mb-0">Consulta y administra los usuarios registrados.</p></div><a class="btn btn-primary" href="{{ route('users.create') }}"><i class="bi bi-plus-lg me-2"></i>Nuevo usuario</a></div>
 <form method="GET" action="{{ route('users.index') }}" class="filter-row"><div class="search-field"><i class="bi bi-search"></i><input type="search" name="search" value="{{ request('search') }}" placeholder="Buscar usuario..." aria-label="Buscar usuarios"></div><button class="btn btn-outline-primary" type="submit">Buscar</button>@if(request('search'))<a class="btn btn-light" href="{{ route('users.index') }}">Limpiar</a>@endif</form>
 <div class="table-responsive"><table class="table align-middle user-table"><thead><tr><th>Usuario</th><th>Correo electrónico</th><th>Teléfono</th><th>Fecha de registro</th><th class="text-end">Acciones</th></tr></thead><tbody>
 @forelse($users as $user)<tr><td><div class="user-cell"><div class="avatar">{{ mb_strtoupper(mb_substr($user->user_name, 0, 1)) }}</div><div><div class="fw-semibold">{{ $user->user_name }}</div><small class="text-muted">ID #{{ $user->user_id }}</small></div></div></td><td>{{ $user->email }}</td><td>{{ $user->phone_num }}</td><td>{{ optional($user->created_at)->format('d/m/Y') ?? '—' }}</td><td><div class="action-buttons"><a class="icon-action edit" href="{{ route('users.edit', $user->user_id) }}" title="Editar usuario" aria-label="Editar usuario"><i class="bi bi-pencil-square"></i></a><button class="icon-action delete" type="button" data-delete-form="delete-user-{{ $user->user_id }}" data-user-name="{{ $user->user_name }}" title="Eliminar usuario" aria-label="Eliminar usuario"><i class="bi bi-trash3"></i></button><form id="delete-user-{{ $user->user_id }}" method="POST" action="{{ route('users.destroy', $user->user_id) }}" class="d-none">@csrf @method('DELETE')</form></div></td></tr>
 @empty<tr><td colspan="5"><div class="empty-state"><div class="empty-icon"><i class="bi bi-person-x"></i></div><h6>No hay usuarios para mostrar</h6><p>{{ request('search') ? 'Prueba con otro término de búsqueda.' : 'Todavía no se han registrado usuarios.' }}</p>@if(!request('search'))<a class="btn btn-primary" href="{{ route('users.create') }}">Crear usuario</a>@endif</div></td></tr>@endforelse
 </tbody></table></div>
 @if($users->hasPages())<div class="pagination-wrap"><span class="text-muted small">Mostrando {{ $users->firstItem() }}–{{ $users->lastItem() }} de {{ $users->total() }} usuarios</span>{{ $users->links() }}</div>@endif
</div></div>
@endsection
