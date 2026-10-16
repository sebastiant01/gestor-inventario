<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Crear cuenta inicial · Gestor de Inventario</title>
    <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('css/auth.css') }}" rel="stylesheet">
</head>
<body class="auth-page">
    <main class="auth-shell auth-shell-register">
        <section class="auth-visual" aria-label="Gestor de Inventario">
            <div class="auth-brand auth-brand-light">
                <span class="auth-brand-icon"><i class="bi bi-box-seam"></i></span>
                <span><strong>Inventario<span class="brand-dot">.</span></strong><small>Panel de administración</small></span>
            </div>
            <div class="auth-visual-copy">
                <span class="auth-eyebrow">CONFIGURACIÓN INICIAL</span>
                <h1>Comencemos.</h1>
                <p>Crea la primera cuenta para habilitar el acceso al panel administrativo.</p>
            </div>
            <div class="auth-visual-footer"><i class="bi bi-shield-check me-2"></i>El registro se cerrará después de crear esta cuenta</div>
            <span class="auth-orb auth-orb-one"></span><span class="auth-orb auth-orb-two"></span>
        </section>
        <section class="auth-form-panel">
            <div class="auth-mobile-brand auth-brand">
                <span class="auth-brand-icon"><i class="bi bi-box-seam"></i></span>
                <span><strong>Inventario<span class="brand-dot">.</span></strong><small>Panel de administración</small></span>
            </div>
            <div class="auth-form-wrap auth-register-form-wrap">
                <div class="auth-heading-icon"><i class="bi bi-person-plus"></i></div>
                <p class="auth-kicker">PRIMER ACCESO</p>
                <h2>Crear cuenta inicial</h2>
                <p class="auth-subtitle">Registra la primera cuenta para acceder al sistema.</p>
                @if ($errors->any())
                    <div class="alert alert-danger" role="alert"><strong>Revisa los datos:</strong><ul class="mb-0 mt-1">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
                @endif
                <form method="POST" action="{{ route('register.attempt') }}" class="auth-form">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label" for="user_name">Nombre de usuario</label>
                        <div class="auth-input-wrap"><i class="bi bi-person" aria-hidden="true"></i><input id="user_name" name="user_name" type="text" class="form-control" value="{{ old('user_name') }}" maxlength="255" autocomplete="username" required autofocus></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="email">Correo electrónico</label>
                        <div class="auth-input-wrap"><i class="bi bi-envelope" aria-hidden="true"></i><input id="email" name="email" type="email" class="form-control" value="{{ old('email') }}" maxlength="255" autocomplete="email" required></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="phone_num">Teléfono</label>
                        <div class="auth-input-wrap"><i class="bi bi-telephone" aria-hidden="true"></i><input id="phone_num" name="phone_num" type="tel" class="form-control" value="{{ old('phone_num') }}" maxlength="20" autocomplete="tel" required></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="password">Contraseña</label>
                        <div class="auth-input-wrap"><i class="bi bi-lock" aria-hidden="true"></i><input id="password" name="password" type="password" class="form-control" minlength="8" autocomplete="new-password" required></div>
                        <div class="form-text">Usa al menos 8 caracteres.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="password_confirmation">Confirmar contraseña</label>
                        <div class="auth-input-wrap"><i class="bi bi-lock-fill" aria-hidden="true"></i><input id="password_confirmation" name="password_confirmation" type="password" class="form-control" minlength="8" autocomplete="new-password" required></div>
                    </div>
                    <button class="btn btn-primary auth-submit" type="submit">Crear cuenta</button>
                </form>
                <div class="auth-register-hint"><a href="{{ route('login') }}"><i class="bi bi-arrow-left me-1"></i> Volver al inicio de sesión</a></div>
            </div>
            <footer class="auth-footer">© {{ date('Y') }} Gestor de Inventario</footer>
        </section>
    </main>
</body>
</html>
