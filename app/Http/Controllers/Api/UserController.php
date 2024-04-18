<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{

    //Register API (POST)
    public function register(Request $request): JsonResponse
    {
        // Definir el valor predeterminado del nombre
        $name = $request->filled('name') ? $request->name : 'anonimo';
        //Data validation
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255|unique:users,name,' . ($request->filled('name') ? null : 'anonimo'),
            // Permitir nombre nulo ya que puede ser 'anonimo' por default, en ese caso, no será unique --> concatenacion aplica excepciona  la regla unique
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
        //$name = $request->filled('name') ? $request->name : 'anonimo';

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

        ], 201);
    }
    //Login API (POST)
    public function login(Request $request): JsonResponse
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
            ],401);
        }
    }

    //Profile update API(PUT)
    public function nameChange(int $id, Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'nullable|string|max:255', // Permitir nombre nulo o cadena de hasta 255 caracteres
        ]);

        $user = User::findOrFail($id);

        // coincidencia usuario logeado y usuario a modificar
        if ($request->user()->id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

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
    public function logout(int $id, Request $request): JsonResponse
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found',
            ], 404);
        }
        //validacion usuario logeado y solicitud
        if ($request->user()->id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $user->tokens()->delete();
        return response()->json([
            'status' => true,
            'message' => 'User is now logged out',

        ]);
    }
}
