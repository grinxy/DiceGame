<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{

    //Register API (POST)
    public function register(Request $request)
    {
        //Data validation
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255', // Permitir nombre nulo ya que puede ser 'anonimo' por default
            'email' => 'required|email|unique:users',
            'password' => 'required',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        //User anonimo en caso de estar vacio o no estar
        $name = $request->filled('name') ? $request->name : 'anonimo';

        //Create User
        $user = User::create([
            'name' => $name,
            'email' => $request->email,
            'password' => Hash::make($request->password),

        ]);
        $user->assignrole('player');

        return response()->json([
            'status' => true,
            'message' => 'User created successfully'
        ]);
    }
    //Login API (POST)
    public function login(Request $request)
    {
        //Data validation
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt([
            'email' => $request->email,
            'password' => $request->password,

        ])) {
            $user = Auth::user();
            $token = $user->createToken('userToken')->accessToken;
            return response()->json([
                'status' => true,
                'message' => 'Login Successful',
                'token' => $token
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Invalid login details'
            ]);
        }
    }

    //Profile update API(PUT)
    public function nameChange($id, Request $request)
    {
        $request->validate([
            'name' => 'nullable|string|max:255', // Permitir nombre nulo o cadena de hasta 255 caracteres
        ]);

        $user = User::findOrFail($id);


        $newName = $request->input('name');
        if (empty($newName)) {
            $newName = 'anonimo';
        }
        $user->update(['name' => $newName]);


         return response()->json([
            'status' => true,
            'message' => 'Profile successfully updated',
            'data' => $user
        ]);
    }
    //Logout API(POST)
    public function logout($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found',
            ], 404);
        }

        $user->tokens()->delete();
        return response()->json([
            'status' => true,
            'message' => 'User is now logged out',

        ]);
    }
}
