<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Info(
 *             title="API DiceGame",
 *             version="1.0",
 *             description="URI's List for DiceGameApi"
 * )
 *
 * @OA\Server(url="http://127.0.0.1:8000")
 */

class UserController extends Controller
{

    //Register API (POST)
    /**
     * Register API (POST)
     * @OA\Schema(
     *     schema="UserRegistrationRequest",
     *     required={"name", "email", "password"},
     *     @OA\Property(property="name", type="string"),
     *     @OA\Property(property="email", type="string", format="email"),
     *     @OA\Property(property="password", type="string", format="password"),
     * )
     * @OA\Post(
     *     path="/api/v1/players",
     *     tags={"Authentication"},
     *     summary="Register a new player",
     *     operationId="register",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UserRegistrationRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example="true"),
     *             @OA\Property(property="message", type="string", example="User created successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example="false"),
     *             @OA\Property(property="message", type="string", example="The given data was invalid.")
     *         )
     *     )
     * )
     */
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
    /**
     * Login API (POST)
     * @OA\Schema(
     *     schema="UserLoginRequest",
     *     required={"email", "password"},
     *     @OA\Property(property="email", type="string", format="email"),
     *     @OA\Property(property="password", type="string", format="password"),
     * )
     * @OA\Post(
     *     path="/api/v1/players/login",
     *     tags={"Authentication"},
     *     summary="Login with existing credentials",
     *     operationId="login",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UserLoginRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login Successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example="true"),
     *             @OA\Property(property="message", type="string", example="Login Successful"),
     *             @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0...")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid login details",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example="false"),
     *             @OA\Property(property="message", type="string", example="Invalid login details")
     *         )
     *     )
     * )
     */
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
            ], 401);
        }
    }

    //Profile update API(PUT)
    /**
     * Profile update API(PUT)
     * @OA\Schema(
     *     schema="UserNameChange",
     *     required={"id", "name", "email"},
     *     @OA\Property(property="id", type="integer"),
     *     @OA\Property(property="name", type="string"),
     *     @OA\Property(property="email", type="string", format="email"),
     * )
     * @OA\Put(
     *     path="/api/v1/players/{id}",
     *     tags={"Authentication"},
     *     summary="Update player's name",
     *     operationId="nameChange",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the player to update",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="New Name")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Profile successfully updated",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example="true"),
     *             @OA\Property(property="message", type="string", example="Profile successfully updated"),
     *             @OA\Property(property="data", ref="#/components/schemas/UserNameChange")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Unauthorized")
     *         )
     *     )
     * )
     */
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
    /**
     * Logout API(POST)
     * @OA\Schema(
     *     schema="UserLogoutRequest",
     *     required={"id"},
     *     @OA\Property(property="id", type="integer"),
     * )
     * @OA\Post(
     *     path="/api/v1/players/{id}/logout",
     *     tags={"Authentication"},
     *     summary="Logout user",
     *     operationId="logout",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the player to logout",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User is now logged out",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example="true"),
     *             @OA\Property(property="message", type="string", example="User is now logged out")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Unauthorized")
     *         )
     *     )
     * )
     */
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
