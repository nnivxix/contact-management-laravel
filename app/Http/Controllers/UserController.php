<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Requests\UserRegisterRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserController extends Controller
{
    protected function setUp(): void
    {
        parent::setUp();
        DB::delete('delete from contacts');
        DB::delete('delete from addresses');
        DB::delete('delete from users');
    }

    public function register(UserRegisterRequest $request)
    {
        $validated = $request->validated();

        $user = new User($validated);
        $user->password = Hash::make($validated['password']);
        $user->save();

        return (new UserResource($user))->response()->setStatusCode(201);
    }

    public function login(UserLoginRequest $request): UserResource
    {
        $validated = $request->validated();

        $user = User::where('username', $validated['username'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            throw new HttpResponseException(response([
                "errors" => [
                    "message" => [
                        "username or password wrong"
                    ]
                ]
            ], 401));
        }

        $user->token = Str::uuid()->toString();
        $user->save();

        return new UserResource($user);
    }

    public function show(Request $request): UserResource
    {
        $user = Auth::user();
        return new UserResource($user);
    }

    public function update(UserUpdateRequest $request): UserResource
    {
        $validated = $request->validated();
        $user = Auth::user(); // current user log in

        // check if input name is not null
        if (isset($validated['name'])) {
            $user->name = $validated['name'];
        }

        // check if input password is not null
        if (isset($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();
        return new UserResource($user);
    }

    public function destroy(Request $request): JsonResponse
    {
        $user = Auth::user();
        $user->token = null;
        $user->save();

        return response()
            ->json([
                'message' => 'Logout successful',
            ])
            ->setStatusCode(200);
    }
}
