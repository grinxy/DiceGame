<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class GameController extends Controller
{
    public function throwDices(): array
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

    /**
     * Play method
     * @OA\Schema(
     *     schema="PlayGame",
     *     required={"user_id", "dice1_value", "dice2_value", "sum", "result"},
     *     @OA\Property(property="user_id", type="integer"),
     *     @OA\Property(property="dice1_value", type="integer"),
     *     @OA\Property(property="dice2_value", type="integer"),
     *     @OA\Property(property="sum", type="integer"),
     *     @OA\Property(property="result", type="string", enum={"won", "lost"})
     * )
     * @OA\Post(
     *     path="/api/v1/players/{id}/play",
     *     tags={"Player"},
     *     summary="Play a game",
     *     operationId="play",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the player",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Game stored successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Game stored successfully"),
     *             @OA\Property(property="game", ref="#/components/schemas/PlayGame")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Unauthorized")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example="false"),
     *             @OA\Property(property="message", type="string", example="User not found")
     *         )
     *     )
     * )
     */
    public function play(int $id, Request $request): JsonResponse
    {
        // Obtener el ID del usuario autenticado
        $user = User::find($id);
        if ($request->user()->id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found',
            ], 404);
        }

        $diceThrow = $this->throwDices();

        // Crear un nuevo juego en el historial de juegos del usuario
        $game = Game::create([
            'user_id' => $id,
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
