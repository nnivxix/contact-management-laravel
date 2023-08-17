<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function register(UserRegisterRequest $request)
    {
        $validated = $request->validated();

        $user = new User($validated);
        $user->password = Hash::make($validated['password']);
        $user->save();

        return (new UserResource($user))->response()->setStatusCode(201);
    }
}
