<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ActiveLogin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;

use App\Http\Resources\UserResource;
use App\Http\Resources\TokenResource;
use App\Http\Resources\ErrorResource;

class AuthController extends Controller
{
    public function register(RegisterRequest $request){
      
        $validatedData = $request->validated(); // Returns an array of validated fields
        //Create User
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
        ]);
        // return response()->json([
        //    'message' => 'New user registered',
        //    'data' => $user,
        //    'access_token' => $user->createToken('api_token')->plainTextToken,
        //    'token_type' => 'Bearer' 
        // ],201);

        $token = (object)[
            'access_token' => $user->createToken('api_token')->plainTextToken,
            'token_type' => 'Bearer',
        ];

        return (new TokenResource($token))
        ->additional([
            'message' => 'New user registered',
            'data' => new UserResource($user),
        ])
        ->response()
        ->setStatusCode(201);
    }

    public function login(LoginRequest $request){
        $validatedData = $request->validated();

        if(!Auth::attempt($validatedData)){
           return (new ErrorResource([
            'error' => 'Unauthorized.Invalid credentials',
            ]))
            ->response()
            ->setStatusCode(401);
        }

        $user = User::where('email', $validatedData['email'])->first();

        // return response()->json([
        //    'message' => 'Logged in successfully',
        //    'access_token' => $user->createToken('api_token')->plainTextToken,
        //    'token_type' => 'Bearer'
        // ],200);

        $token = $user->createToken('api_token');
        $token->accessToken->expires_at = now()->addHours(2); // Set expiration to 1 hour,can be done in sanctum file expiration
        $token->accessToken->save();

        $tokenDetails = (object)[
            'access_token' => $token->plainTextToken,
            'token_type' => 'Bearer',
        ];
        return (new TokenResource($tokenDetails))
        ->additional([
            'message' => 'Logged in successfully',
        ])
        ->response()
        ->setStatusCode(200);

    }
    public function logout(Request $request)
    {
        ActiveLogin::where('user_id', $request->user()->id)->delete();
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully.'], 200);
    }
}
