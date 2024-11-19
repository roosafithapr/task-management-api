<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request){
      
        $validated = $request->validate([
           'name'=>'required|max:255',
           'email'=>'required|max:255|email|unique:users',
           'password'=>'required|min:8',
        ]);
  
        //Create User
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);
        return response()->json([
           'message' => 'New user registered',
           'data' => $user,
           'access_token' => $user->createToken('api_token')->plainTextToken,
           'token_type' => 'Bearer' 
        ],201);
    }

    public function login(Request $request){
        $validated = $request->validate([
           'email' => 'required|email',
           'password' => 'required'
        ]);

        if(!Auth::attempt($validated)){
           return response()->json([
               'message'=>'Unauthorized.Invalid credentials'
           ], 401);
        }

        $user = User::where('email', $validated['email'])->first();

        return response()->json([
           'message' => 'Logged in successfully',
           'access_token' => $user->createToken('api_token')->plainTextToken,
           'token_type' => 'Bearer'
        ],200);

    }
}
