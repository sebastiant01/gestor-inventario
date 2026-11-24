<div class="card content-card form-card"><div class="card-body">
    <div class="section-heading"><div><h5 class="card-title">{{ $editing ? 'Editar proveedor' : 'Datos del proveedor' }}</h5><p class="text-muted mb-0">{{ $editing ? 'Actualiza los datos de contacto.' : 'Completa la información de contacto del proveedor.' }}</p></div><div class="form-user-icon"><i class="bi bi-truck"></i></div></div>
    <form method="POST" action="{{ $editing ? route('suppliers.update') : route('suppliers.store') }}">@csrf @if($editing) @method('PUT')<input type="hidden" name="supplier_id" value="{{ $supplier->supplier_id }}">@endif
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label" for="supplier_name">Nombre del proveedor <span class="required">*</span></label><input class="form-control" id="supplier_name" name="supplier_name" value="{{ old('supplier_name', $supplier->supplier_name ?? '') }}" maxlength="100" required>@error('supplier_name')<div class="text-danger small">{{ $message }}</div>@enderror</div>
            <div class="col-md-6"><label class="form-label" for="contact_person">Persona de contacto <span class="required">*</span></label><input class="form-control" id="contact_person" name="contact_person" value="{{ old('contact_person', $supplier->contact_person ?? '') }}" maxlength="100" required>@error('contact_person')<div class="text-danger small">{{ $message }}</div>@enderror</div>
            <div class="col-md-6"><label class="form-label" for="email">Correo electrónico <span class="required">*</span></label><input class="form-control" id="email" type="email" name="email" value="{{ old('email', $supplier->email ?? '') }}" maxlength="100" required>@error('email')<div class="text-danger small">{{ $message }}</div>@enderror</div>
            <div class="col-md-6"><label class="form-label" for="phone">Teléfono <span class="required">*</span></label><input class="form-control" id="phone" name="phone" value="{{ old('phone', $supplier->phone ?? '') }}" maxlength="30" required>@error('phone')<div class="text-danger small">{{ $message }}</div>@enderror</div>
        </div>
        <div class="form-actions"><a class="btn btn-light" href="{{ route('suppliers.index') }}">Cancelar</a><button class="btn btn-primary" type="submit">{{ $editing ? 'Guardar cambios' : 'Crear proveedor' }}</button></div>
    </form>
</div></div>
