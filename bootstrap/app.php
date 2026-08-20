<?php

use App\Http\Middleware\CheckMaintenanceMode;
use App\Http\Middleware\SetLocale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');
        $middleware->validateCsrfTokens(except: [
            'api/ips/agent-sync',
            'logout',
        ]);

        $middleware->web(append: [
            SetLocale::class,
            CheckMaintenanceMode::class,
        ]);
        $middleware->alias([
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, $request) {
            if ($e->getStatusCode() === 419) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Sesi kedaluwarsa. Silakan refresh halaman.'], 419);
                }
                if (\Illuminate\Support\Facades\Auth::check()) {
                    return redirect()->route('dashboard')->with('warning', 'Sesi Anda telah diperbarui.');
                }
                return redirect()->route('login')->with('warning', 'Sesi login telah kedaluwarsa. Silakan coba masuk kembali.');
            }
        });

        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Sesi kedaluwarsa. Silakan refresh halaman.'], 419);
            }
            if (\Illuminate\Support\Facades\Auth::check()) {
                return redirect()->route('dashboard')->with('warning', 'Sesi Anda telah diperbarui.');
            }
            return redirect()->route('login')->with('warning', 'Sesi login telah kedaluwarsa. Silakan coba masuk kembali.');
        });
    })->create();
