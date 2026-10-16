<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\Auth as AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->intended(route('users.index'));
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
            'user_name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string'],
        ], [
            'user_name.required' => 'Escribe tu nombre de usuario.',
            'password.required' => 'Escribe tu contraseña.',
        ]);

        if (!Auth::attempt([
            'user_name' => $credentials['user_name'],
            'password' => $credentials['password'],
        ], $request->boolean('remember'))) {
            return back()
                ->withErrors(['user_name' => 'El usuario o la contraseña son incorrectos.'])
                ->onlyInput('user_name');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('users.index'))
            ->with('success', 'Has iniciado sesión correctamente.');
    }

    public function register(Request $request, AuthService $auth): RedirectResponse
    {
        // Evita que usuarios nuevos se registren y obtengan acceso al panel una vez
        // que ya existe una cuenta capaz de administrar usuarios.
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

        $result = $auth->register(
            $data['user_name'],
            $data['email'],
            $data['phone_num'],
            $data['password']
        );

        if ($result !== true) {
            return back()
                ->withErrors(['registration' => is_string($result) ? $result : 'No se pudo crear la cuenta inicial.'])
                ->withInput($request->except(['password', 'password_confirmation']));
        }

        $user = User::query()->where('user_name', $data['user_name'])->firstOrFail();
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('users.index')
            ->with('success', 'Cuenta inicial creada. Ya puedes administrar los usuarios.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Has cerrado sesión correctamente.');
    }
}
