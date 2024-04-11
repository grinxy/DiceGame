<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ApiController extends Controller
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
         User::create([
            'name' => $name,
            'email' => $request->email,
            'password' => Hash::make($request->password),

        ]);


        return response()->json([
            'status' => true,
            'message' => 'User created successfully'
        ]);
    }
    //Login API (POST)
    public function login(Request $request)
    {
    }

    //Profile API(GET)
    public function profile()
    {
    }
    //Logout API(GET)
    public function logout()
    {
    }
}
