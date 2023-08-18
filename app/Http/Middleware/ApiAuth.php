<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ApiAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('Authorization');
        $authenticate = true;

        // if token is null
        if (!$token) {
            $authenticate = false;
        }

        $user = User::where('token', $token)->first();

        // if user not found
        if (!$user) {
            $authenticate = false;
        } else {
            Auth::login($user);
        }

        if ($authenticate) {
            return $next($request);
        }
        return response()->json([
            "errors" => [
                "message" => [
                    "unauthorized"
                ]
            ]
        ])->setStatusCode(401);
    }
}
