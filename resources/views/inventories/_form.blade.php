<div class="card content-card form-card">
    <div class="card-body">
        <div class="section-heading">
            <div>
                <h5 class="card-title">{{ $editing ? 'Editar inventario' : 'Datos del inventario' }}</h5>
                <p class="text-muted mb-0">Define el nombre, la capacidad total y el estado del inventario.</p>
            </div>
            <div class="form-user-icon"><i class="bi bi-boxes"></i></div>
        </div>

        <form method="POST" action="{{ $editing ? route('inventories.update') : route('inventories.store') }}">
            @csrf
            @if ($editing)
                @method('PUT')
                <input type="hidden" name="inventory_id" value="{{ $inventory->inventory_id }}">
            @endif

            <div class="mb-3">
                <label class="form-label" for="inventory_name">Nombre del inventario <span class="required">*</span></label>
                <input class="form-control" id="inventory_name" name="inventory_name" value="{{ old('inventory_name', $inventory->inventory_name ?? '') }}" maxlength="100" required>
                @error('inventory_name')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label" for="capacity">Capacidad total (unidades) <span class="required">*</span></label>
                <input class="form-control" id="capacity" type="number" name="capacity" value="{{ old('capacity', $inventory->capacity ?? 1) }}" min="{{ $editing ? max(1, $inventory->total_units) : 1 }}" step="1" required>
                @error('capacity')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
                @if ($editing)
                    <div class="form-text">Actualmente contiene {{ $inventory->total_units }} unidades. La capacidad no puede ser inferior a esa cantidad.</div>
                @endif
            </div>

            @if ($editing)
                <input type="hidden" name="is_active" value="0">
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $inventory->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Inventario activo</label>
                </div>
            @endif

            <div class="form-actions">
                <a class="btn btn-light" href="{{ route('inventories.index') }}">Cancelar</a>
                <button class="btn btn-primary" type="submit">{{ $editing ? 'Guardar cambios' : 'Crear inventario' }}</button>
            </div>
        </form>
    </div>
</div>
