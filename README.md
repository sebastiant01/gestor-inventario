<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

In addition, [Laracasts](https://laracasts.com) contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

You can also watch bite-sized lessons with real-world projects on [Laravel Learn](https://laravel.com/learn), where you will be guided through building a Laravel application from scratch while learning PHP fundamentals.

## Agentic Development

Laravel's predictable structure and conventions make it ideal for AI coding agents like Claude Code, Cursor, and GitHub Copilot. Install [Laravel Boost](https://laravel.com/docs/ai) to supercharge your AI workflow:

```bash
composer require laravel/boost --dev

php artisan boost:install
```

Boost provides your agent 15+ tools and skills that help agents build Laravel applications while following best practices.

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).


## Autenticación, roles, permisos y módulos del inventario

El sistema utiliza la autenticación de sesiones de Laravel. El inicio de sesión se realiza con correo electrónico y contraseña; el cierre de sesión invalida la sesión y regenera el token CSRF. El registro público solo está disponible para crear la primera cuenta administrativa.

Las rutas internas están protegidas por `auth` y por el middleware `permission`, que valida en el backend el código de permiso asignado al rol del usuario. Ocultar enlaces en el menú solo mejora la navegación: la protección efectiva se realiza en cada ruta.

Los módulos visuales disponibles son usuarios, productos, categorías, proveedores e inventarios. La gestión de inventarios permite agregar, retirar y ajustar las cantidades de productos, respetando existencias y capacidad.

### Preparar el entorno en Windows (PowerShell)

1. Crea el archivo de entorno local desde la plantilla:

   ```powershell
   Copy-Item .env.example .env
   ```

2. Configura en `.env` la conexión a la base de datos. Luego instala las dependencias, genera la clave de Laravel, ejecuta las migraciones y carga los roles y permisos iniciales:

   ```powershell
   composer install
   php artisan key:generate
   php artisan migrate --seed
   php artisan serve
   ```

   Para una base local desechable que necesites reconstruir desde cero, usa `php artisan migrate:fresh --seed`. Este comando elimina y vuelve a crear todas las tablas: no lo ejecutes sobre una base con datos que debas conservar.

3. Abre `http://127.0.0.1:8000/register` para crear la primera cuenta administrativa solo si la tabla `users` está vacía. Después inicia sesión desde `http://127.0.0.1:8000/login` con el correo y la contraseña registrados.

4. Desde el menú lateral puedes ingresar a los módulos permitidos. El rol `admin` recibe todos los permisos definidos; el rol `user` recibe permisos de consulta sobre categorías, proveedores, productos e inventarios. La asignación de roles a usuarios requiere el permiso `users.assign-role`.

No se incluye una cuenta con contraseña predeterminada. El seeder no crea usuarios; únicamente prepara el catálogo de roles y permisos.
