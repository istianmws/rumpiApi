<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Http\Requests\LoginRequest;

class AuthenticationController extends Controller
{
    //
    public function register(RegisterRequest $request)
    {
        $request->validated();

        $userData = [
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ];

        $user = User::create($userData);
        $token = $user->createToken('rumpiapp')->plainTextToken;

        return response([
            'user' => $user,
            'token' => $token
        ], 201);
    }
    public function login(LoginRequest $request)
    {
        $request->validated();

        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response([
                'message' => 'Invalid credentials'
            ], 401);
        }


        $token = $user->createToken('rumpiapp')->plainTextToken;

        return response([
            'user' => $user,
            'token' => $token
        ], 200);
    }
}
