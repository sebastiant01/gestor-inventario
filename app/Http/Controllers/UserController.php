<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function indexUsers(Request $request)
    {
        $query = User::query();
        if ($search = trim((string) $request->query('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('user_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone_num', 'like', "%{$search}%");
            });
        }
        $users = $query->orderByDesc('created_at')->paginate(10)->withQueryString();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_name' => ['required', 'string', 'max:255', 'unique:users,user_name'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone_num' => ['required', 'string', 'max:20', 'unique:users,phone_num'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        User::create([
            'user_name' => $data['user_name'], 'email' => $data['email'],
            'phone_num' => $data['phone_num'], 'password_hash' => Hash::make($data['password']),
        ]);
        return redirect()->route('users.index')->with('success', 'El usuario fue creado correctamente.');
    }

    public function edit(string $userId)
    {
        $user = User::findOrFail($userId);
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, string $userId)
    {
        $user = User::findOrFail($userId);
        $data = $request->validate([
            'user_name' => ['required', 'string', 'max:255', Rule::unique('users', 'user_name')->ignore($user->user_id, 'user_id')],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->user_id, 'user_id')],
            'phone_num' => ['required', 'string', 'max:20', Rule::unique('users', 'phone_num')->ignore($user->user_id, 'user_id')],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);
        $user->user_name = $data['user_name'];
        $user->email = $data['email'];
        $user->phone_num = $data['phone_num'];
        if (!empty($data['password'])) $user->password_hash = Hash::make($data['password']);
        $user->save();
        return redirect()->route('users.index')->with('success', 'Los datos del usuario fueron actualizados.');
    }

    public function destroy(string $userId)
    {
        $user = User::findOrFail($userId);
        $user->delete();
        return redirect()->route('users.index')->with('success', 'El usuario fue eliminado.');
    }

    public function showUserById(string $userId) { return redirect()->route('users.edit', $userId); }
    public function showUserByName(string $userName) { $user = User::where('user_name', $userName)->firstOrFail(); return redirect()->route('users.edit', $user->user_id); }
}
