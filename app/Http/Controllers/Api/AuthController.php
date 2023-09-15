<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request) {
        $request->validate(
            [
                'email' => ['required', 'email'],
                'password' => 'required',
            ],
            $request->all()
        );

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {  
            
           $token = $request->user()->createToken('kanban-token');

            return response()->json([
                'code' => 200,
                'message' => 'User ' . $request->email . ' successfully login',
                'token' => $token->plainTextToken
            ], 200);
        }
    }

    public function signup(Request $request)
    {
        $request->validate(
            [
                'name' => 'required',
                'email' => ['required', 'email', 'unique:users'],
                'password' => 'required',
            ],
            [
                'email.unique' => 'The email address is already taken.',
            ],
            $request->all()
        );

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);     
        
        return response()->json([
            'code' => 200,
            'message' => 'New username ' . $user->name . ' successfully created',
            'data' => [
                'username' => $user->name,
                'email' => $user->email
            ]
        ], 200);    
    }

    public function revoke() {     
        
        $user = Auth::user();

        $user->tokens()->delete();

        return response()->json([
            'code' => 200,
            'message' => 'Token successfully revoke'
        ], 200);
    }
}
