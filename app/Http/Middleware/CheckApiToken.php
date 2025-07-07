<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckApiToken
{
    /**
     * Обработка входящего запроса.
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->header('Authorization');

        if (!$token) {
            return response()->json(['errors' => ['API token is missing.']], 401);
        }

        // Обычно токен пользователя предают так: "Bearer {token}"
        if (str_starts_with($token, 'Bearer ')) {
            $token = substr($token, 7);
        }

        $user = User::where('api_token', $token)->first();

        if (!$user) {
            return response()->json(['errors' => ['Invalid API token.']], 401);
        }

        Auth::login($user);

        return $next($request);
    }
}
