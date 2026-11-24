<div class="card content-card form-card">
    <div class="card-body">
        <div class="section-heading">
            <div>
                <h5 class="card-title">{{ $editing ? 'Editar rol' : 'Información del rol' }}</h5>
                <p class="text-muted mb-0">{{ $editing ? 'Actualiza el nombre, la descripción y los permisos del rol.' : 'Asigna un nombre y los permisos que tendrá este rol.' }}</p>
            </div>
            <div class="form-user-icon"><i class="bi bi-shield-lock"></i></div>
        </div>

        <form id="role-form" method="POST" action="{{ $editing ? route('roles.update') : route('roles.store') }}">
            @csrf
            @if ($editing)
                @method('PUT')
                <input type="hidden" name="role_id" value="{{ $role->role_id }}">
            @endif
            <input type="hidden" name="permissions" id="permissions" value="{{ old('permissions', '[]') }}">

            <div class="row g-4">
                <div class="col-12 col-md-6">
                    <label class="form-label" for="role_name">Nombre <span class="required">*</span></label>
                    <input class="form-control @error('role_name') is-invalid @enderror" id="role_name" name="role_name"
                        value="{{ old('role_name', $editing ? $role->role_name : '') }}" maxlength="50" required>
                    @error('role_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label class="form-label" for="role_description">Descripción</label>
                    <textarea class="form-control @error('role_description') is-invalid @enderror" id="role_description"
                        name="role_description" maxlength="255" rows="2">{{ old('role_description', $editing ? $role->role_description : '') }}</textarea>
                    @error('role_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <div class="form-divider"></div>
                    <h6 class="form-section-title"><i class="bi bi-key me-2"></i>Permisos</h6>
                    @error('permissions')<div class="text-danger small">{{ $message }}</div>@enderror
                    @php($selectedPermissions = json_decode(old('permissions', '[]'), true) ?: [])
                    <div class="row g-3">
                        @foreach ($modules as $module => $permissions)
                            <div class="col-12 col-md-6 col-xl-4">
                                <h6>{{ $module }}</h6>
                                @foreach ($permissions as $permission)
                                    <div class="form-check">
                                        <input class="form-check-input permission-checkbox" type="checkbox"
                                            id="permission-{{ $permission->permission_id }}"
                                            data-permission-id="{{ $permission->permission_id }}"
                                            data-permission-module="{{ $permission->module }}"
                                            data-permission-code="{{ $permission->code }}"
                                            @checked(old('permissions') !== null
                                                ? in_array((string) $permission->permission_id, $selectedPermissions, true)
                                                : ($permission->selected ?? false))>
                                        <label class="form-check-label" for="permission-{{ $permission->permission_id }}">
                                            {{ $permission->description }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('roles.index') }}" class="btn btn-light">Cancelar</a>
                <button type="submit" class="btn btn-primary">{{ $editing ? 'Guardar cambios' : 'Crear rol' }}</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
    <script>
        const permissionCheckboxes = document.querySelectorAll('.permission-checkbox');

        function hasModuleActionPermission(module) {
            return Array.from(permissionCheckboxes).some((candidate) =>
                candidate.checked
                && candidate.dataset.permissionModule === module
                && !candidate.dataset.permissionCode.endsWith('.show')
            );
        }

        function ensureModuleShowPermission(checkbox) {
            if (checkbox.dataset.permissionCode.endsWith('.show')) {
                if (!checkbox.checked && hasModuleActionPermission(checkbox.dataset.permissionModule)) {
                    checkbox.checked = true;
                }

                return;
            }

            if (!checkbox.checked) {
                return;
            }

            const showPermission = Array.from(permissionCheckboxes).find((candidate) =>
                candidate.dataset.permissionModule === checkbox.dataset.permissionModule
                && candidate.dataset.permissionCode.endsWith('.show')
            );

            if (showPermission) {
                showPermission.checked = true;
            }
        }

        permissionCheckboxes.forEach((checkbox) => {
            checkbox.addEventListener('change', () => ensureModuleShowPermission(checkbox));
            ensureModuleShowPermission(checkbox);
        });

        document.getElementById('role-form').addEventListener('submit', function () {
            const permissionIds = Array.from(document.querySelectorAll('.permission-checkbox:checked'))
                .map((checkbox) => checkbox.dataset.permissionId);

            document.getElementById('permissions').value = JSON.stringify(permissionIds);
        });
    </script>
@endpush
