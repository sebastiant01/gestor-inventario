@extends('layouts.app')
@section('title', 'Inventarios')
@section('page-title', 'Inventarios')
@section('breadcrumb', 'Listado')
@section('content')
<div class="section-heading mb-3"><div><h5 class="card-title mb-1">Control de inventarios</h5><p class="text-muted mb-0">Administra la capacidad y los productos almacenados en cada inventario.</p></div>@if(auth()->user()->hasPermission('inventories.create'))<a class="btn btn-primary" href="{{ route('inventories.create') }}"><i class="bi bi-plus-lg me-2"></i>Nuevo inventario</a>@endif</div>
<div class="row g-4">
@forelse($inventories as $inventory)
    <div class="col-12"><div class="card content-card"><div class="card-body">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3"><div><h5 class="card-title mb-1">{{ $inventory->inventory_name }}</h5><div class="small text-muted">Inventario #{{ $inventory->inventory_id }} · {{ $inventory->is_active ? 'Activo' : 'Inactivo' }}</div></div><div class="d-flex align-items-center gap-2">@if(auth()->user()->hasPermission('inventories.update'))<a class="btn btn-outline-primary btn-sm" href="{{ route('inventories.edit', $inventory->inventory_id) }}"><i class="bi bi-pencil me-1"></i>Editar</a>@endif @if(auth()->user()->hasPermission('inventories.delete'))<form method="POST" action="{{ route('inventories.destroy', $inventory->inventory_id) }}" onsubmit="return confirm('¿Eliminar este inventario? Primero debes retirar los productos asociados.');">@csrf @method('DELETE')<button class="btn btn-outline-danger btn-sm" type="submit"><i class="bi bi-trash me-1"></i>Eliminar</button></form>@endif</div></div>
        @php($percent = $inventory->capacity > 0 ? min(100, round(($inventory->total_units / $inventory->capacity) * 100)) : 0)
        <div class="row g-3 mb-3"><div class="col-md-4"><div class="p-3 bg-light rounded"><small class="text-muted d-block">Unidades ocupadas</small><strong>{{ $inventory->total_units }} / {{ $inventory->capacity }}</strong></div></div><div class="col-md-8"><div class="d-flex justify-content-between small mb-1"><span>Capacidad utilizada</span><strong>{{ $percent }}%</strong></div><div class="progress" role="progressbar" aria-label="Capacidad utilizada" aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100" style="height:10px"><div class="progress-bar" style="width:{{ $percent }}%"></div></div></div></div>
        <h6 class="fw-semibold mb-2">Productos almacenados</h6>
        <div class="table-responsive"><table class="table table-sm align-middle user-table mb-3"><thead><tr><th>Producto</th><th>Precio</th><th>Unidades en este inventario</th>@if(auth()->user()->hasPermission('inventories.products.manage'))<th>Actualizar cantidad</th><th>Acción</th>@endif</tr></thead><tbody>
        @forelse($inventory->products as $product)
            <tr><td>{{ $product->product_name }}</td><td>${{ number_format((float)$product->product_price, 2, ',', '.') }}</td><td>{{ $product->pivot->product_quantity }}</td>
            @if(auth()->user()->hasPermission('inventories.products.manage'))
                <td><form class="d-flex gap-2" method="POST" action="{{ route('inventories.products.quantity') }}">@csrf @method('PUT')<input type="hidden" name="inventory_id" value="{{ $inventory->inventory_id }}"><input type="hidden" name="product_id" value="{{ $product->product_id }}"><input class="form-control form-control-sm" style="max-width:100px" type="number" name="product_quantity" min="0" max="{{ $product->available_units + $product->pivot->product_quantity }}" value="{{ $product->pivot->product_quantity }}" required><button class="btn btn-outline-primary btn-sm" type="submit">Guardar</button></form></td>
                <td><form method="POST" action="{{ route('inventories.products.remove') }}" onsubmit="return confirm('¿Retirar este producto del inventario?');">@csrf @method('DELETE')<input type="hidden" name="inventory_id" value="{{ $inventory->inventory_id }}"><input type="hidden" name="product_id" value="{{ $product->product_id }}"><button class="btn btn-outline-danger btn-sm" type="submit"><i class="bi bi-x-lg"></i> Retirar</button></form></td>
            @endif</tr>
        @empty <tr><td colspan="5" class="text-muted py-3">Todavía no hay productos en este inventario.</td></tr>@endforelse
        </tbody></table></div>
        @if(auth()->user()->hasPermission('inventories.products.manage'))
            <div class="border-top pt-3"><h6 class="fw-semibold mb-3">Agregar producto</h6><form class="row g-2 align-items-end" method="POST" action="{{ route('inventories.products.add') }}">@csrf<input type="hidden" name="inventory_id" value="{{ $inventory->inventory_id }}"><div class="col-md-7"><label class="form-label" for="product_id_{{ $inventory->inventory_id }}">Producto</label><select class="form-select" id="product_id_{{ $inventory->inventory_id }}" name="product_id" required><option value="">Selecciona un producto disponible</option>@foreach($availableProducts as $product)@unless($inventory->products->contains('product_id', $product->product_id))<option value="{{ $product->product_id }}">{{ $product->product_name }} ({{ $product->available_units }} disponibles)</option>@endunless @endforeach</select></div><div class="col-md-3"><label class="form-label" for="quantity_{{ $inventory->inventory_id }}">Cantidad</label><input class="form-control" id="quantity_{{ $inventory->inventory_id }}" type="number" name="product_quantity" min="1" value="1" required></div><div class="col-md-2"><button class="btn btn-primary w-100" type="submit"><i class="bi bi-plus-lg me-1"></i>Agregar</button></div></form></div>
        @endif
    </div></div></div>
@empty
    <div class="col-12"><div class="card content-card"><div class="card-body"><div class="empty-state"><div class="empty-icon"><i class="bi bi-boxes"></i></div><h6>No hay inventarios registrados</h6><p>Crea un inventario para comenzar a distribuir productos.</p>@if(auth()->user()->hasPermission('inventories.create'))<a class="btn btn-primary" href="{{ route('inventories.create') }}">Crear inventario</a>@endif</div></div></div></div>
@endforelse
</div>
@endsection
