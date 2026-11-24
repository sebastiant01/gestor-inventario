<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function index(Request $request): View
    {
        $query = Role::query()->with('permissions');

        if ($search = trim((string) $request->query('search'))) {
            $query->where(function ($builder) use ($search) {
                $builder->where('role_name', 'like', "%{$search}%")
                    ->orWhere('role_description', 'like', "%{$search}%");
            });
        }

        $roles = $query->orderBy('role_name')->paginate(10)->withQueryString();

        return view('roles.index', compact('roles'));
    }

    public function create(): View
    {
        $modules = Permission::all()->groupBy('module');

        return view('roles.create', ['modules' => $modules]);
    }

    public function store(Request $request): RedirectResponse
    {
        Validator::make($request->all(),
        [
            'role_name' => 'required|string|unique:roles,role_name',
            'role_description' => 'nullable|string',
            'permissions' => 'required|json',
        ],
        [
            'role_name.required' => 'The role name is required.',
            'role_name.unique' => 'The role name already exists.',
            'permissions.required' => 'At least one permission must be selected.',
            'permissions.json' => 'The permissions field has an invalid format.',
        ])->validate();

        $permissionIds = $this->validatedPermissionIds($request->input('permissions'));
        $permissionIds = $this->includeModuleShowPermissions($permissionIds);

        try {
            DB::transaction(function () use ($request, $permissionIds) {
                $role = new Role();
                $role->role_name = $request->role_name;
                $role->role_description = $request->role_description;
                $role->save();

                $role->permissions()->sync($permissionIds);
            });

            return redirect()->route('roles.index')->with('success', 'Role created successfully.');

        } catch (Exception $ex) {
            Log::error($ex);
            return redirect()->back()->withErrors(['error' => 'An error has occurred.']);
        }
    }

    public function edit(string $id): View | RedirectResponse
    {
        $role = Role::with('permissions')->findOrFail($id);

        $modules = Permission::all()
            ->map(function ($permission) use ($role) {
                $permission->selected = $role->permissions
                    ->contains('permission_id', $permission->permission_id);
                return $permission;
            })
            ->groupBy('module');

        return view('roles.edit', [
            'role' => $role,
            'modules' => $modules,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        Validator::make($request->all(),
        [
            'role_id' => 'required|exists:roles,role_id',
            'role_name' => 'required|string|unique:roles,role_name,' . $request->role_id . ',role_id',
            'role_description' => 'nullable|string',
            'permissions' => 'required|json',
        ],
        [
            'role_id.required' => 'The role is required.',
            'role_id.exists' => 'The given id does not exist.',
            'role_name.required' => 'The role name is required.',
            'role_name.unique' => 'The role name already exists.',
            'permissions.required' => 'At least one permission must be selected.',
            'permissions.json' => 'The permissions field has an invalid format.',
        ])->validate();

        $permissionIds = $this->validatedPermissionIds($request->input('permissions'));
        $permissionIds = $this->includeModuleShowPermissions($permissionIds);

        try {
            DB::transaction(function () use ($request, $permissionIds) {
                $role = Role::find($request->role_id);
                $role->role_name = $request->role_name;
                $role->role_description = $request->role_description;
                $role->save();

                $role->permissions()->sync($permissionIds);
            });

            return redirect()->route('roles.index')->with('success', 'Role updated successfully.');

        } catch (Exception $ex) {
            Log::error($ex);
            return redirect()->back()->withErrors(['error' => 'An error has occurred.']);
        }
    }

    public function delete(string $id): RedirectResponse
    {
        try {
            $role = Role::find($id);

            if (empty($role)) {
                return redirect()->back()->withErrors(['error' => "Role with id '$id' doesn't exist."]);
            }

            DB::transaction(function () use ($role) {
                $role->permissions()->detach();
                $role->delete();
            });

            return redirect()->route('roles.index')->with('success', 'Role deleted successfully.');

        } catch (Exception $ex) {
            Log::error($ex);
            return redirect()->back()->withErrors(['error' => 'An error has occurred.']);
        }
    }

    private function validatedPermissionIds(string $permissionsJson): array
    {
        $permissionIds = json_decode($permissionsJson, true);

        Validator::make(
            ['permissions' => $permissionIds],
            [
                'permissions' => ['required', 'array', 'min:1'],
                'permissions.*' => ['required', 'integer', 'distinct', 'exists:permissions,permission_id'],
            ],
            [
                'permissions.array' => 'The permissions field must be a list.',
                'permissions.min' => 'At least one permission must be selected.',
                'permissions.*.exists' => 'One or more selected permissions do not exist.',
            ]
        )->validate();

        return array_map('intval', $permissionIds);
    }

    private function includeModuleShowPermissions(array $permissionIds): array
    {
        $selectedPermissions = Permission::query()
            ->whereIn('permission_id', $permissionIds)
            ->get(['permission_id', 'module', 'code']);

        $modulesWithActions = $selectedPermissions
            ->filter(fn (Permission $permission) => ! str_ends_with($permission->code, '.show'))
            ->pluck('module')
            ->unique();

        if ($modulesWithActions->isEmpty()) {
            return $permissionIds;
        }

        $showPermissionIds = Permission::query()
            ->whereIn('module', $modulesWithActions)
            ->where('code', 'like', '%.show')
            ->pluck('permission_id')
            ->map(fn ($permissionId) => (int) $permissionId)
            ->all();

        return array_values(array_unique(array_merge($permissionIds, $showPermissionIds)));
    }
}
