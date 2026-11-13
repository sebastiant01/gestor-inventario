@extends('layouts.app')
@section('title', 'Productos')
@section('page-title', 'Productos')
@section('breadcrumb', 'Listado')
@section('content')
<div class="card content-card"><div class="card-body">
 <div class="section-heading"><div><h5 class="card-title">Listado de productos</h5><p class="text-muted mb-0">Consulta y administra los productos registrados.</p></div><a class="btn btn-primary" href="{{ route('products.create') }}"><i class="bi bi-plus-lg me-2"></i>Nuevo producto</a></div>
 <form method="GET" action="{{ route('products.index') }}" class="filter-row"><div class="search-field"><i class="bi bi-search"></i><input type="search" name="search" value="{{ request('search') }}" placeholder="Buscar producto..." aria-label="Buscar productos"></div><button class="btn btn-outline-primary" type="submit">Buscar</button>@if(request('search'))<a class="btn btn-light" href="{{ route('products.index') }}">Limpiar</a>@endif</form>
 <div class="table-responsive"><table class="table align-middle product-table"><thead><tr><th>Producto</th><th>Precio</th><th>Stock</th><th>Descripción</th><th>Disponibilida</th><th>Categoria</th><th>Proveedor</th><th class="text-end">Acciones</th></tr></thead><tbody>
 @forelse($products as $product)<tr><td><div class="product-cell"><div class="avatar">{{ mb_strtoupper(mb_substr($product->product_name, 0, 1)) }}</div><div><div class="fw-semibold">{{ $product->product_name }}</div><small class="text-muted">ID #{{ $product->product_id }}</small></div></div></td><td>${{ number_format($product->product_price, 2) }}</td><td>{{ $product->available_units }}</td><td>{{ $product->product_description }}</td><td><div class="action-buttons"><a class="icon-action edit" href="{{ route('products.edit', $product->product_id) }}" title="Editar producto" aria-label="Editar producto"><i class="bi bi-pencil-square"></i></a><button class="icon-action delete" type="button" data-delete-form="delete-product-{{ $product->product_id }}" data-product-name="{{ $product->product_name }}" title="Eliminar producto" aria-label="Eliminar producto"><i class="bi bi-trash3"></i></button><form id="delete-product-{{ $product->product_id }}" method="POST" action="{{ route('products.destroy', $product->product_id) }}" class="d-none">@csrf @method('DELETE')</form></div></td></tr>
 @empty<tr><td colspan="4"><div class="empty-state"><div class="empty-icon"><i class="bi bi-box"></i></div><h6>No hay productos para mostrar</h6><p>{{ request('search') ? 'Prueba con otro término de búsqueda.' : 'Todavía no se han registrado productos.' }}</p>@if(!request('search'))<a class="btn btn-primary" href="{{ route('products.create') }}">Crear producto</a>@endif</div></td></tr>@endforelse
 </tbody></table></div>
 @if($products->hasPages())<div class="pagination-wrap"><span class="text-muted small">Mostrando {{ $products->firstItem() }}–{{ $products->lastItem() }} de {{ $products->total() }} productos</span>{{ $products->links() }}</div>@endif
</div></div>
@endsection
