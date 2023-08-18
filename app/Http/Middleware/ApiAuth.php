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
        // set default variable
        $token = $request->header('Authorization');
        $authenticate = true;
        $user = User::where('token', $token)->first();

        // if token is null or user is not found
        if (!$token || !$user) {
            $authenticate = false;
            return response()->json([
                "errors" => [
                    "message" => [
                        "unauthorized"
                    ]
                ]
            ])->setStatusCode(401);
        }

        if ($authenticate) {
            Auth::login($user);
            return $next($request);
        }
    }
}
