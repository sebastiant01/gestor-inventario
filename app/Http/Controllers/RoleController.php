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
    public function index(): View
    {
        $roles = Role::with('permissions')->get();

        return view('roles.index', ['roles' => $roles]);
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

        try {
            DB::transaction(function () use ($request) {
                $role = new Role();
                $role->role_name = $request->role_name;
                $role->role_description = $request->role_description;
                $role->save();

                $permissions = json_decode($request->permissions);
                $role->permissions()->sync($permissions);
            });

            return redirect()->route('roles.index')->with('success', 'Role created successfully.');

        } catch (Exception $ex) {
            Log::error($ex);
            return redirect()->back()->withErrors(['error' => 'An error has occurred.']);
        }
    }

    public function edit(string $id): View | RedirectResponse
    {
        $role = Role::find($id);

        if (empty($role)) {
            return redirect()->back()->withErrors(['error' => "Role with id '$id' doesn't exist."]);
        }

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

        try {
            DB::transaction(function () use ($request) {
                $role = Role::find($request->role_id);
                $role->role_name = $request->role_name;
                $role->role_description = $request->role_description;
                $role->save();

                $permissions = json_decode($request->permissions);
                $role->permissions()->sync($permissions);
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
}
