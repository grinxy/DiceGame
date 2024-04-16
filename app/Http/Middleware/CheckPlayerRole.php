<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
class CheckPlayerRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return response()->json([
                'status' => false,
                'message' => 'Only players are permitted',
            ], 401);
        }

        if (!Auth::user()->hasRole('player')) {
            return response()->json([
                'status' => false,
                'message' => 'Only players are permitted',
            ], 403);
        }
        return $next($request);
    }
}
