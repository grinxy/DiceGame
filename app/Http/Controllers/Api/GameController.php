<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Game;

class GameController extends Controller
{
    public function throwDices()
    {

        $dice1_value = rand(1, 6);
        $dice2_value = rand(1, 6);

        $sum = $dice1_value + $dice2_value;

        $result = ($sum == 7) ? 'won' : 'lost';

        return [
            'dice1_value' => $dice1_value,
            'dice2_value' => $dice2_value,
            'sum' => $sum,
            'result' => $result
        ];
    }

    public function play()
    {
        // Obtener el ID del usuario autenticado
        $userId = Auth::id();

        $diceThrow = $this->throwDices();

        // Crear un nuevo juego en el historial de juegos del usuario
        $game = Game::create([
            'user_id' => $userId,
            'dice1_value' => $diceThrow['dice1_value'],
            'dice2_value' => $diceThrow['dice2_value'],
            'sum' => $diceThrow['sum'],
            'result' => $diceThrow['result']
        ]);

        return response()->json([
            'message' => 'Game stored successfully',
            'game' => $game
        ], 201);
    }
}
