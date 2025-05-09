<?php

use App\Http\Middleware\EnsureIsAdmin;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin' => EnsureIsAdmin::class,
        ]);
        
    })
    ->withExceptions(function (Exceptions $exceptions) {

        $exceptions->render(function (Throwable $e, Request $request) {
        if ($request->expectsJson()) {
                return match ($e->getCode()) {
                    404 => response()->json(['message' => 'Ressource non trouvée'], 404),
                    403 => response()->json(['message' => 'Accès refusé'], 403),
                    401 => response()->json(['message' => 'Non autorisé'], 401),
                    422 => response()->json(['message' => 'Données invalides'], 422),
                    429 => response()->json(['message' => 'Trop de requêtes'], 429),
                    default => response()->json(['message' => 'Erreur interne du serveur'], 500),
                };
            }
        });
        
    })->create();
