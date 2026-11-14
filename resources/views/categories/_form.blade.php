<div class="card content-card form-card"><div class="card-body">
    <div class="section-heading"><div><h5 class="card-title">{{ $editing ? 'Editar categoría' : 'Datos de la categoría' }}</h5><p class="text-muted mb-0">{{ $editing ? 'Actualiza la información de esta categoría.' : 'Registra una categoría para clasificar los productos.' }}</p></div><div class="form-user-icon"><i class="bi bi-tags"></i></div></div>
    <form method="POST" action="{{ $editing ? route('categories.update') : route('categories.store') }}">
        @csrf @if($editing) @method('PUT')<input type="hidden" name="category_id" value="{{ $category->category_id }}">@endif
        <div class="mb-3"><label class="form-label" for="category_name">Nombre <span class="required">*</span></label><input class="form-control" id="category_name" name="category_name" value="{{ old('category_name', $category->category_name ?? '') }}" maxlength="255" required>@error('category_name')<div class="text-danger small mt-1">{{ $message }}</div>@enderror</div>
        <div class="mb-3"><label class="form-label" for="category_description">Descripción</label><textarea class="form-control" id="category_description" name="category_description" rows="4">{{ old('category_description', $category->category_description ?? '') }}</textarea>@error('category_description')<div class="text-danger small mt-1">{{ $message }}</div>@enderror</div>
        <div class="form-actions"><a class="btn btn-light" href="{{ route('categories.index') }}">Cancelar</a><button class="btn btn-primary" type="submit">{{ $editing ? 'Guardar cambios' : 'Crear categoría' }}</button></div>
    </form>
</div></div>
