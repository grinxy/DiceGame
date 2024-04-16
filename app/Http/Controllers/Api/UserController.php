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
        $user->token()->revoke();
        return response()->json([
            'status' => true,
            'message' => 'User is now logged out',

        ]);
    }
    public function listPlayers()
    {
<<<<<<< Updated upstream
        Auth::user();
        $players = User::whereHas('roles', function ($query) {
            $query->where('name', 'player');
        })->get();

        return response()->json([
            'status' => true,
            'message' => 'Players list:',
            'data' => $players
=======
        if (Auth::user()) {
            //mostrar solo jugadores
            $players = User::whereHas('roles', function ($query) {
                $query->where('name', 'player');
            })->get();
            foreach ($players as $player) {
                $successRate = round(($this->calculateSuccessRate($player)), 2);
                $player->success_rate = $successRate;
                $totalSuccessRate = +$successRate;
            }
            $averageSuccessRate = round(($totalSuccessRate / count($players)), 2);

            return response()->json([
                'status' => true,
                'average succes rate players' => $averageSuccessRate,
                'message' => 'Players list:',
                'data' => $players
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ]);
        }
    }
    public function gamesHistory($id)
    {
        $user = User::findOrFail($id);
        $games = Game::where('user_id', $id)->get();
        $successRate = $this->calculateSuccessRate($user);

        return response()->json([
            'status' => true,
            'message' => 'Player ' . $user->name . ' games history:',
            'games_played' => count($games),
            'success_rate' => $successRate . ' %',
            'data' => $games
        ]);
    }

    private function calculateSuccessRate($user)
    {
        $games = Game::where('user_id', $user->id)->get();
        $won = $games->filter(function ($game) {
            return $game->result == 'won';
        })->count();

        $totalGames = $games->count();
        return $totalGames > 0 ? ($won / $totalGames) * 100 : 0;
    }
    public function deleteHistory($id)
    {
        $user = User::findOrFail($id);
        Game::where('user_id', $user->id)->delete();
        return response()->json([
            'status' => true,
            'message' => 'Game history deleted for user: ' . $user->name
>>>>>>> Stashed changes
        ]);
    }
}
