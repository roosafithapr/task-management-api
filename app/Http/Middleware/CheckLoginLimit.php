<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\ActiveLogin;
use App\Models\User;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\ErrorResource;

class CheckLoginLimit
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $request->validate([
            'email' => 'required|email',
        ]);
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return (new ErrorResource([
                'error' => 'Unauthorized',
                ]))
                ->response()
                ->setStatusCode(401);
        }
        // Check if the user is already in the active logins table
        $activeLogin = ActiveLogin::where('user_id', $user->id)->first();
        if (!$activeLogin) {
            // If not, check if the number of active logins exceeds the limit
            $activeLoginsCount = ActiveLogin::count();
            if ($activeLoginsCount >= 2) {
                return (new ErrorResource([
                    'error' => 'Login limit exceeded.Only 2 login allowed',
                    ]))
                    ->response()
                    ->setStatusCode(403);
            }

            // Add the user to active logins
            ActiveLogin::create([
                'user_id' => $user->id,
                'session_id' => session()->getId(), // Assuming you manage session IDs
            ]);
        }


        return $next($request);
    }
}
