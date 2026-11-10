<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Iniciar sesión · Gestor de Inventario</title>
    <link href="{{ asset('assets/img/favicon.png') }}" rel="icon">
    <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('css/auth.css') }}" rel="stylesheet">
</head>
<body class="auth-page">
    <main class="auth-shell">
        <section class="auth-visual" aria-label="Gestor de Inventario">
            <div class="auth-brand auth-brand-light">
                <span class="auth-brand-icon"><i class="bi bi-box-seam"></i></span>
                <span><strong>Inventario<span class="brand-dot">.</span></strong><small>Panel de administración</small></span>
            </div>
            <div class="auth-visual-copy">
                <span class="auth-eyebrow">GESTIÓN INTERNA</span>
                <h1>Todo bajo control.</h1>
                <p>Administra de forma sencilla la información de los usuarios de tu sistema.</p>
            </div>
            <div class="auth-visual-footer"><i class="bi bi-shield-lock me-2"></i>Acceso protegido para usuarios autorizados</div>
            <span class="auth-orb auth-orb-one"></span>
            <span class="auth-orb auth-orb-two"></span>
        </section>

        <section class="auth-form-panel">
            <div class="auth-mobile-brand auth-brand">
                <span class="auth-brand-icon"><i class="bi bi-box-seam"></i></span>
                <span><strong>Inventario<span class="brand-dot">.</span></strong><small>Panel de administración</small></span>
            </div>
            <div class="auth-form-wrap">
                <div class="auth-heading-icon"><i class="bi bi-person-lock"></i></div>
                <p class="auth-kicker">BIENVENIDO DE NUEVO</p>
                <h2>Iniciar sesión</h2>
                <p class="auth-subtitle">Introduce tu nombre de usuario y contraseña.</p>

                @if (session('success'))
                    <div class="alert alert-success" role="status"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}</div>
                @endif
                @if ($errors->has('registration'))
                    <div class="alert alert-warning" role="alert">{{ $errors->first('registration') }}</div>
                @endif
                @if ($errors->has('user_name'))
                    <div class="alert alert-danger" role="alert">{{ $errors->first('user_name') }}</div>
                @endif
                @if ($errors->has('password'))
                    <div class="alert alert-danger" role="alert">{{ $errors->first('password') }}</div>
                @endif

                <form method="POST" action="{{ route('login.attempt') }}" class="auth-form">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label" for="user_name">Nombre de usuario</label>
                        <div class="auth-input-wrap">
                            <i class="bi bi-person" aria-hidden="true"></i>
                            <input id="user_name" name="user_name" type="text" class="form-control @error('user_name') is-invalid @enderror" value="{{ old('user_name') }}" placeholder="Nombre de usuario" autocomplete="username" maxlength="255" required autofocus>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="password">Contraseña</label>
                        <div class="auth-input-wrap">
                            <i class="bi bi-lock" aria-hidden="true"></i>
                            <input id="password" name="password" type="password" class="form-control @error('password') is-invalid @enderror" placeholder="Contraseña" autocomplete="current-password" required>
                            <button class="password-toggle" type="button" aria-label="Mostrar contraseña" data-password-toggle="password"><i class="bi bi-eye"></i></button>
                        </div>
                    </div>
                    <div class="auth-options">
                        <label class="auth-remember"><input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}> <span>Recordar mi sesión</span></label>
                    </div>
                    <button class="btn btn-primary auth-submit" type="submit">Iniciar sesión</button>
                </form>

                @if ($registrationEnabled)
                    <div class="auth-register-hint">
                        <i class="bi bi-info-circle me-1"></i> Aún no hay usuarios registrados.
                        <a href="{{ route('register') }}">Crear cuenta inicial</a>
                    </div>
                @endif
            </div>
            <footer class="auth-footer">© {{ date('Y') }} Gestor de Inventario</footer>
        </section>
    </main>
    <script>
        document.querySelectorAll('[data-password-toggle]').forEach(function (button) {
            button.addEventListener('click', function () {
                const input = document.getElementById(button.dataset.passwordToggle);
                const isPassword = input.type === 'password';
                input.type = isPassword ? 'text' : 'password';
                button.innerHTML = isPassword ? '<i class="bi bi-eye-slash"></i>' : '<i class="bi bi-eye"></i>';
                button.setAttribute('aria-label', isPassword ? 'Ocultar contraseña' : 'Mostrar contraseña');
            });
        });
    </script>
</body>
</html>
