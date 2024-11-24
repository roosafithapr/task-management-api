<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Hash;

use App\Http\Resources\UserResource;
use App\Http\Resources\ErrorResource;

class UserController extends Controller
{
    public function showUser($id)
    {
        $user = User::find($id);
        if (!$user) {
            // return response()->json(['error' => 'User not found'], 404);
            return (new ErrorResource([
                'error' => 'User not found',
                ]))
                ->response()
                ->setStatusCode(404);
        }

        // return response()->json([
        //     'message' => 'User retrieved successfully',
        //     'data' => $user,
        //  ],200);

        return (new UserResource($user))
        ->additional([
            'message' => 'User retrieved successfully',
        ])
        ->response()
        ->setStatusCode(200);
    }

    public function updateUser(LoginRequest $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return (new ErrorResource([
                'error' => 'User not found',
                ]))
                ->response()
                ->setStatusCode(404);
        }
        $validatedData = $request->validated();
        // Check if the password exists 
        if (isset($validatedData['password'])) {
            $validatedData['password'] = Hash::make($validatedData['password']);
        }

        $user->update($validatedData);

        // return response()->json([
        //     'message' => 'User updated successfully', 
        //     'user' => $user,
        // ], 200);
        return (new UserResource($user))
        ->additional([
            'message' => 'User updated successfully',
        ])
        ->response()
        ->setStatusCode(200);
    }

    public function deleteUser($id)
    {
        $user = User::find($id);

        if (!$user) {
            return (new ErrorResource([
                'error' => 'User not found',
                ]))
                ->response()
                ->setStatusCode(404);
        }

        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully'
        ], 204);
    }
}
