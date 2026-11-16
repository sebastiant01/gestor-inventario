@extends('layouts.app')
@section('title', 'Categorías')
@section('page-title', 'Categorías')
@section('breadcrumb', 'Listado')
@section('content')
<div class="card content-card"><div class="card-body">
    <div class="section-heading">
        <div><h5 class="card-title">Listado de categorías</h5><p class="text-muted mb-0">Organiza los productos por tipo o grupo.</p></div>
        @if(auth()->user()->hasPermission('categories.create'))
            <a class="btn btn-primary" href="{{ route('categories.create') }}"><i class="bi bi-plus-lg me-2"></i>Nueva categoría</a>
        @endif
    </div>
    <div class="table-responsive"><table class="table align-middle user-table">
        <thead><tr><th>Nombre</th><th>Descripción</th><th>Productos</th>@if(auth()->user()->hasPermission('categories.update') || auth()->user()->hasPermission('categories.delete'))<th class="text-end">Acciones</th>@endif</tr></thead>
        <tbody>
        @forelse($categories as $category)
            <tr>
                <td class="fw-semibold">{{ $category->category_name }}</td>
                <td>{{ $category->category_description ?: '—' }}</td>
                <td><span class="badge bg-light text-dark">{{ $category->products()->count() }}</span></td>
                @if(auth()->user()->hasPermission('categories.update') || auth()->user()->hasPermission('categories.delete'))
                    <td><div class="action-buttons justify-content-end">
                        @if(auth()->user()->hasPermission('categories.update'))<a class="icon-action edit" href="{{ route('categories.edit', $category->category_id) }}" title="Editar"><i class="bi bi-pencil-square"></i></a>@endif
                        @if(auth()->user()->hasPermission('categories.delete'))
                            <form method="POST" action="{{ route('categories.destroy', $category->category_id) }}" onsubmit="return confirm('¿Eliminar esta categoría? Solo será posible si no tiene productos asociados.');">@csrf @method('DELETE')<button class="icon-action delete" type="submit" title="Eliminar"><i class="bi bi-trash3"></i></button></form>
                        @endif
                    </div></td>
                @endif
            </tr>
        @empty
            <tr><td colspan="4"><div class="empty-state"><div class="empty-icon"><i class="bi bi-tags"></i></div><h6>No hay categorías registradas</h6><p>Cuando crees una categoría aparecerá en este listado.</p>@if(auth()->user()->hasPermission('categories.create'))<a class="btn btn-primary" href="{{ route('categories.create') }}">Crear categoría</a>@endif</div></td></tr>
        @endforelse
        </tbody>
    </table></div>
</div></div>
@endsection
