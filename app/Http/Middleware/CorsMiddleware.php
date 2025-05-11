<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CorsMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $origin = $request->headers->get('Origin');

        // Autorise uniquement le frontend local
        if ($origin === 'http://localhost:8000/') {

            if ($request->getMethod() === "OPTIONS") {
                $response = response('', 204);
            } else {
                $response = $next($request);
            }

            $response->headers->set('Access-Control-Allow-Origin', $origin);
            $response->headers->set('Access-Control-Allow-Credentials', 'true');
            $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
            $response->headers->set('Access-Control-Allow-Headers', 'Origin, Content-Type, Authorization, X-Requested-With, Accept');

            return $response;
        }

        // Sinon, passe la requête sans modifier les en-têtes
        return $next($request);
    }
}
