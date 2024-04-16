<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthenticateApi
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next@return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Si el usuario no está autenticado
        if (!Auth::check()) {
            return response()->json([
                'status' => false,
                'message' => 'User is not authenticated, please login first',
            ], 401);
        }

        // Si el usuario está autenticado, continuar con la solicitud
        return $next($request);
    }
}
