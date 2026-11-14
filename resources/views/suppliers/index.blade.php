@extends('layouts.app')
@section('title', 'Proveedores')
@section('page-title', 'Proveedores')
@section('breadcrumb', 'Listado')
@section('content')
<div class="card content-card"><div class="card-body">
    <div class="section-heading"><div><h5 class="card-title">Listado de proveedores</h5><p class="text-muted mb-0">Consulta y administra los datos de contacto de tus proveedores.</p></div>@if(auth()->user()->hasPermission('suppliers.create'))<a class="btn btn-primary" href="{{ route('suppliers.create') }}"><i class="bi bi-plus-lg me-2"></i>Nuevo proveedor</a>@endif</div>
    <form method="GET" action="{{ route('suppliers.search') }}" class="filter-row"><div class="search-field"><i class="bi bi-search"></i><input type="search" name="supplier_name" value="{{ request('supplier_name') }}" placeholder="Buscar proveedor..." aria-label="Buscar proveedor"></div><button class="btn btn-outline-primary" type="submit">Buscar</button>@if(request('supplier_name'))<a class="btn btn-light" href="{{ route('suppliers.index') }}">Limpiar</a>@endif</form>
    <div class="table-responsive"><table class="table align-middle user-table"><thead><tr><th>Proveedor</th><th>Persona de contacto</th><th>Correo</th><th>Teléfono</th>@if(auth()->user()->hasPermission('suppliers.update') || auth()->user()->hasPermission('suppliers.delete'))<th class="text-end">Acciones</th>@endif</tr></thead><tbody>
    @forelse($suppliers as $supplier)
        <tr><td class="fw-semibold">{{ $supplier->supplier_name }}</td><td>{{ $supplier->contact_person }}</td><td>{{ $supplier->email }}</td><td>{{ $supplier->phone }}</td>
        @if(auth()->user()->hasPermission('suppliers.update') || auth()->user()->hasPermission('suppliers.delete'))<td><div class="action-buttons justify-content-end">
            @if(auth()->user()->hasPermission('suppliers.update'))<a class="icon-action edit" href="{{ route('suppliers.edit', $supplier->supplier_id) }}" title="Editar"><i class="bi bi-pencil-square"></i></a>@endif
            @if(auth()->user()->hasPermission('suppliers.delete'))<form method="POST" action="{{ route('suppliers.destroy', $supplier->supplier_id) }}" onsubmit="return confirm('¿Eliminar este proveedor? Si tiene productos asociados no podrá eliminarse.');">@csrf @method('DELETE')<button class="icon-action delete" type="submit" title="Eliminar"><i class="bi bi-trash3"></i></button></form>@endif
        </div></td>@endif</tr>
    @empty <tr><td colspan="5"><div class="empty-state"><div class="empty-icon"><i class="bi bi-truck"></i></div><h6>No hay proveedores para mostrar</h6><p>{{ request('supplier_name') ? 'No se encontraron coincidencias.' : 'Registra el primer proveedor para comenzar.' }}</p>@if(!request('supplier_name') && auth()->user()->hasPermission('suppliers.create'))<a class="btn btn-primary" href="{{ route('suppliers.create') }}">Crear proveedor</a>@endif</div></td></tr>@endforelse
    </tbody></table></div>
</div></div>
@endsection
