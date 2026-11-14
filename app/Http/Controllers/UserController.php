<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class UserController extends Controller
{
    public function indexUsers(Request $request): View
    {
        $query = User::query()->with('role');

        if ($search = trim((string) $request->query('search'))) {
            $query->where(function ($builder) use ($search) {
                $builder->where('user_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone_num', 'like', "%{$search}%");
            });
        }

        $users = $query->orderByDesc('created_at')->paginate(10)->withQueryString();

        return view('users.index', compact('users'));
    }

    public function create(): View
    {
        return view('users.create', [
            'roles' => Role::query()->orderBy('role_name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $canAssignRole = $request->user()->hasPermission('users.assign-role');

        $rules = [
            'user_name' => ['required', 'string', 'max:255', 'unique:users,user_name'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone_num' => ['required', 'string', 'max:20', 'unique:users,phone_num'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];

        if ($canAssignRole) {
            $rules['role_id'] = ['required', 'exists:roles,role_id'];
        }

        $data = $request->validate($rules);

        $user = new User();
        $user->user_name = $data['user_name'];
        $user->email = $data['email'];
        $user->phone_num = $data['phone_num'];
        $user->password_hash = Hash::make($data['password']);
        $user->role_id = $canAssignRole
            ? $data['role_id']
            : Role::query()->firstOrCreate(['role_name' => 'user'])->role_id;
        $user->save();

        return redirect()->route('users.index')->with('success', 'El usuario fue creado correctamente.');
    }

    public function edit(string $userId): View
    {
        $user = User::query()->with('role')->findOrFail($userId);

        return view('users.edit', [
            'user' => $user,
            'roles' => Role::query()->orderBy('role_name')->get(),
        ]);
    }

    public function update(Request $request, string $userId): RedirectResponse
    {
        $user = User::query()->findOrFail($userId);
        $canAssignRole = $request->user()->hasPermission('users.assign-role');

        $rules = [
            'user_name' => ['required', 'string', 'max:255', Rule::unique('users', 'user_name')->ignore($user->user_id, 'user_id')],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->user_id, 'user_id')],
            'phone_num' => ['required', 'string', 'max:20', Rule::unique('users', 'phone_num')->ignore($user->user_id, 'user_id')],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ];

        if ($canAssignRole) {
            $rules['role_id'] = ['required', 'exists:roles,role_id'];
        }

        $data = $request->validate($rules);

        if ($canAssignRole && (int) $data['role_id'] !== (int) $user->role_id) {
            if ($request->user()->is($user)) {
                return redirect()->route('users.edit', $user->user_id)->withErrors([
                    'role_id' => 'No puedes cambiar tu propio rol mientras tienes la sesión iniciada.',
                ])->withInput($request->except(['password', 'password_confirmation']));
            }

            if ($user->role?->role_name === 'admin'
                && User::query()->where('role_id', $user->role_id)->count() === 1
                && Role::query()->where('role_id', $data['role_id'])->value('role_name') !== 'admin') {
                return redirect()->route('users.edit', $user->user_id)->withErrors([
                    'role_id' => 'No puedes quitar el rol al último administrador del sistema.',
                ])->withInput($request->except(['password', 'password_confirmation']));
            }
        }

        $user->user_name = $data['user_name'];
        $user->email = $data['email'];
        $user->phone_num = $data['phone_num'];

        if (! empty($data['password'])) {
            $user->password_hash = Hash::make($data['password']);
        }

        if ($canAssignRole) {
            $user->role_id = $data['role_id'];
        }

        $user->save();

        return redirect()->route('users.index')->with('success', 'Los datos del usuario fueron actualizados.');
    }

    public function destroy(Request $request, string $userId): RedirectResponse
    {
        $user = User::query()->findOrFail($userId);

        if ($request->user()->is($user)) {
            return redirect()->route('users.index')->withErrors([
                'user' => 'No puedes eliminar la cuenta con la que has iniciado sesión.',
            ]);
        }

        if ($user->role?->role_name === 'admin'
            && User::query()->where('role_id', $user->role_id)->count() === 1) {
            return redirect()->route('users.index')->withErrors([
                'user' => 'No puedes eliminar al último administrador del sistema.',
            ]);
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'El usuario fue eliminado.');
    }

    public function showUserById(string $userId): RedirectResponse
    {
        return redirect()->route('users.edit', $userId);
    }

    public function showUserByName(string $userName): RedirectResponse
    {
        $user = User::query()->where('user_name', $userName)->firstOrFail();

        return redirect()->route('users.edit', $user->user_id);
    }
}
