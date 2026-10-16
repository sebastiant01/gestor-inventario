<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Services\Auth as AuthService;

class AuthController extends Controller
{

    /**
     * Thing to show the login form
     */
    public function showLogin(): View {
        return view('auth.login');
    }

    /**
     * Thing to show the registration form
     */
    public function showRegister(): View {
        return view('auth.register');
    }

    /**
     * Thing that handles the login request
     */
    public function login(Request $request, AuthService $auth): RedirectResponse
    {
    try {
        // Validate the request data
        $data = $request->validate([
            'userName' => 'required|string|max:255',
            'password' => 'required|string|min:6',
        ]);

        $isAuthenticated = $auth->login($data['userName'], $data['password']);

        if (!$isAuthenticated) {
            return back()
                ->withErrors(['login' => 'Credenciales inválidas.'])
                ->onlyInput('userName');
        }

        return redirect()->route('login')->with('success', 'Usuario autenticado exitosamente.');
    } catch (\Exception $e) {
        return back()
            ->withErrors(['login' => 'Error al autenticar el usuario: ' . $e->getMessage()])
            ->onlyInput('userName');
    }
    }

    /**
     * thing that handles the registration request
     */
    public function register(Request $request, AuthService $auth): RedirectResponse
    {
try {
        // Validate the request data
        $data = $request->validate([
            'userName' => 'required|string|max:255|unique:users,user_name',
            'email' => 'required|string|email|max:255|unique:users',
            'phoneNum' => 'required|string|max:20',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $result = $auth->register($data['userName'], $data['email'], $data['phoneNum'], $data['password']);

        if ($result === false) {
            return back()
                ->withErrors(['registration' => 'Error al registrar el usuario.'])
                ->onlyInput('userName', 'email', 'phoneNum');
        }

        return redirect()->route('register')->with('success', 'Usuario registrado exitosamente.');
    } catch (\Exception $e) {
        return back()
            ->withErrors(['registration' => 'Error al registrar el usuario: ' . $e->getMessage()])
            ->onlyInput('userName', 'email', 'phoneNum');
    }
    }

    /**
     * Thing that handles the logout request
     */
    public function logout(Request $request): RedirectResponse {
        try {
            // Invalidate the session
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->with('success', 'Usuario desconectado exitosamente.');
        } catch (\Exception $e) {
            return back()
                ->withErrors(['logout' => 'Error al desconectar el usuario: ' . $e->getMessage()]);
        }
    }
}
