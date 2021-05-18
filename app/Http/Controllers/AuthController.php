<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|unique:users|email',
            'password' => 'required|confirmed',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        // $token = $user->createToken('mytoken')->plainTextToken;
        $response = [
            'user' => $user,
            // 'token' => $token,
        ];

        return response($response, 201);
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!auth()->attempt($request->only('email', 'password'))) {
            throw new AuthenticationException();
        }

        return [
            'token' => auth()->user()->createToken('test')->plainTextToken
        ];
    }

    public function me()
    {
        return auth()->user();
    }
}
