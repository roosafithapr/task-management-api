<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckTokenExpiration
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        if (!$request->user()) {
            return response()->json(['message' => 'Unauthenticatedh.'], 401);
        }
        $token = $request->user()->currentAccessToken();
        // Check if the token has expired
        if ($token && $token->expires_at < now()) {
            $token->delete();
            return response()->json(['message' => 'Token has expired'], 401);
        }

        return $next($request);
    }
}
