<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->intended(route('products.index'));
        }

        return view('auth.login', [
            // El registro público solo se permite para crear la cuenta inicial.
            'registrationEnabled' => User::query()->doesntExist(),
        ]);
    }

    public function showRegister(): View|RedirectResponse
    {
        if (User::query()->exists()) {
            return redirect()->route('login')->withErrors([
                'registration' => 'El registro inicial ya se completó. Inicia sesión con una cuenta existente.',
            ]);
        }

        return view('auth.register');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Escribe tu correo electrónico.',
            'email.email' => 'Escribe un correo electrónico válido.',
            'password.required' => 'Escribe tu contraseña.',
        ]);

        if (! Auth::attempt([
            'email' => $credentials['email'],
            'password' => $credentials['password'],
        ], $request->boolean('remember'))) {
            return back()
                ->withErrors(['email' => 'El correo electrónico o la contraseña son incorrectos.'])
                ->onlyInput('email');
        }

        // Cambiar el identificador de sesión evita reutilizar la sesión previa al login.
        $request->session()->regenerate();

        return redirect()->intended(route('products.index'))
            ->with('success', 'Has iniciado sesión correctamente.');
    }

    public function register(Request $request): RedirectResponse
    {
        // El registro público se cierra una vez creada la cuenta administrativa inicial.
        if (User::query()->exists()) {
            return redirect()->route('login')->withErrors([
                'registration' => 'El registro inicial ya está cerrado. Inicia sesión con una cuenta existente.',
            ]);
        }

        $data = $request->validate([
            'user_name' => ['required', 'string', 'max:255', 'unique:users,user_name'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone_num' => ['required', 'string', 'max:20', 'unique:users,phone_num'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // La primera cuenta recibe el rol administrativo para configurar el sistema.
        $adminRole = Role::query()->firstOrCreate(
            ['role_name' => 'admin'],
            ['role_description' => 'Administrador del sistema']
        );

        $user = new User();
        $user->user_name = $data['user_name'];
        $user->email = $data['email'];
        $user->phone_num = $data['phone_num'];
        $user->password_hash = Hash::make($data['password']);
        $user->role_id = $adminRole->role_id;
        $user->save();

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('products.index')
            ->with('success', 'Cuenta administrativa inicial creada correctamente.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Has cerrado sesión correctamente.');
    }
}
