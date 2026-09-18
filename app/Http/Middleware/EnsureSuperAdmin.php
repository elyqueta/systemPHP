<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSuperAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->hasRole('super_admin')) {
            return response()->json([
                'status' => 'error',
                'message' => 'Não tem permissão para aceder a este recurso.',
            ], 403);
        }

        return $next($request);
    }
}
