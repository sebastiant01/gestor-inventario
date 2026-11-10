<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}"><title>@yield('title', 'Usuarios') · Inventario</title>
<link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
<link href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
<link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
<link href="{{ asset('css/admin.css') }}" rel="stylesheet">
</head>
<body>
<header id="header" class="header fixed-top d-flex align-items-center">
  <div class="d-flex align-items-center justify-content-between"><a href="{{ route('users.index') }}" class="logo d-flex align-items-center"><span class="d-none d-lg-block">Inventario<span class="brand-dot">.</span></span></a><i class="bi bi-list toggle-sidebar-btn"></i></div>
  <div class="search-label ms-auto d-none d-lg-block"><i class="bi bi-shield-check me-2"></i>Panel de administración</div>
  <nav class="header-nav ms-auto">
    <ul class="d-flex align-items-center gap-3 mb-0 list-unstyled">
      <li class="d-none d-md-block"><span class="small text-muted"><i class="bi bi-person-circle me-1"></i>{{ auth()->user()->user_name }}</span></li>
      <li>
        <form method="POST" action="{{ route('logout') }}" class="m-0">
          @csrf
          <button type="submit" class="btn btn-outline-primary btn-sm logout-button"><i class="bi bi-box-arrow-right me-1"></i><span class="d-none d-sm-inline">Cerrar sesión</span><span class="d-sm-none">Salir</span></button>
        </form>
      </li>
    </ul>
  </nav>
</header>
<aside id="sidebar" class="sidebar"><ul class="sidebar-nav" id="sidebar-nav">
<li class="nav-heading">MENÚ PRINCIPAL</li><li class="nav-item"><a class="nav-link {{ request()->routeIs('users.*') ? '' : 'collapsed' }}" href="{{ route('users.index') }}"><i class="bi bi-people"></i><span>Usuarios</span></a></li>
<li class="nav-heading">GESTIÓN</li><li class="nav-item"><a class="nav-link collapsed" href="{{ route('users.create') }}"><i class="bi bi-person-plus"></i><span>Nuevo usuario</span></a></li>
<li class="nav-item mt-auto"><div class="sidebar-note"><i class="bi bi-info-circle"></i><span>Versión 1.0<br><small>CRUD de usuarios</small></span></div></li>
</ul></aside>
<main id="main" class="main">
<div class="pagetitle"><h1>@yield('page-title', 'Usuarios')</h1><nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('users.index') }}">Inicio</a></li><li class="breadcrumb-item active">@yield('breadcrumb', 'Usuarios')</li></ol></nav></div>
@if(session('success'))<div class="alert alert-success alert-dismissible fade show" role="alert"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif
@if($errors->any())<div class="alert alert-danger"><strong>Revisa los campos:</strong><ul class="mb-0 mt-1">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
@yield('content')
<footer class="footer"><div>© {{ date('Y') }} <strong>Inventario</strong> · Administración de usuarios</div><div class="text-muted">Panel interno</div></footer>
</main>
<script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script><script src="{{ asset('js/admin.js') }}"></script>@stack('scripts')
</body></html>