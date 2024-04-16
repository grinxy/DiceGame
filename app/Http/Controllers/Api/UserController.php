<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{

    //Register API (POST)
    public function register(Request $request)
    {
        //Data validation
        $validator =  Validator::make($request->all(), [
            'name' => [
                'nullable',
                // valor único excepto en caso null que se repite anonimo
                Rule::unique('users')->where(function ($query) {
                    return $query->whereNotNull('name')
                        ->where('name', '!=', 'anonimo')
                        ->orWhereNull('name');
                }),
            ],
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

    //Profile API(GET)
    public function profile()
    {
        $user = Auth::user();

        return response()->json([
            'status' => true,
            'message' => 'Profile Information',
            'data' => $user
        ]);
    }
    //Logout API(GET)
    public function logout()
    {
        Auth::user()->token()->revoke();
        return response()->json([
            'status' => true,
            'message' => 'User is now logged out',

        ]);
    }
    public function listPlayers()
    {
        Auth::user();
        $players = User::whereHas('roles', function ($query) {
            $query->where('name', 'player');
        })->get();

        return response()->json([
            'status' => true,
            'message' => 'Players list:',
            'data' => $players
        ]);
    }
}
