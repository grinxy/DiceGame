<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Game;
use Illuminate\Http\JsonResponse;


class PlayerController extends Controller
{

    public function getPlayers()
    {
        return User::whereHas('roles', function ($query) {
            $query->where('name', 'player');
        })->get();
    }
    //GET players list
    /**
     * List all players with their success rates.
     *

     * @OA\Schema(
     *     schema="PlayerListResponse",
     *     required={"status", "message", "data"},
     *     @OA\Property(property="status", type="boolean"),
     *     @OA\Property(property="message", type="string"),
     *     @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/User"))
     * )

     * @OA\Get(
     *     path="/api/v1/players/list",
     *     tags={"Admin"},
     *     summary="List all players with success rates",
     *     operationId="listPlayers",
     *     @OA\Response(
     *         response=200,
     *         description="Players list with success rates",
     *         @OA\JsonContent(ref="#/components/schemas/PlayerListResponse")
     *     )
     * )
     */
    public function listPlayers()
    {
        $players = $this->getPlayers();
        foreach ($players as $player) {
            $successRate = round(($this->calculateSuccessRate($player)), 2);
            $player->success_rate = $successRate;
        }


        return response()->json([
            'status' => true,
            'message' => 'Players list:',
            'data' => $players
        ]);
    }
    //GET AVG success rate
    /**
     * @OA\Get(
     *     path="/api/v1/players/ranking",
     *     tags={"Admin"},
     *     summary="Get ranking of players by success rate",
     *     operationId="ranking",
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example="true"),
     *             @OA\Property(property="message", type="string", example="Average success rate of all players: 70%"),
     *         )
     *     )
     * )
     */

    public function ranking()
    {
        $totalSuccessRate = 0;
        $players = $this->getPlayers();
        foreach ($players as $player) {
            $successRate = round(($this->calculateSuccessRate($player)), 2);
            $player->success_rate = $successRate;
            $totalSuccessRate += $successRate;
        }
        $averageSuccessRate = round(($totalSuccessRate / count($players)), 2);

        return response()->json([
            'status' => true,
            'message' => 'Average success rate of all players: ' . $averageSuccessRate . '%',
        ]);
    }
    /**
     * @OA\Schema(
     *     schema="User",
     *     required={"name", "email"},
     *     @OA\Property(property="name", type="string"),
     *     @OA\Property(property="email", type="string", format="email")
     * )
     * @OA\Get(
     *     path="/api/v1/players/ranking/winner",
     *     tags={"Admin"},
     *     summary="Get winner(s) with highest success rate",
     *     operationId="rankingWinner",
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example="true"),
     *             @OA\Property(property="message", type="string", example="Player with highest success score is:"),
     *             @OA\Property(property="Winner(s)", type="array", @OA\Items(ref="#/components/schemas/User")),
     *         )
     *     )
     * )
     */
    public function rankingWinner()
    {

        $players = $players = $this->getPlayers();
        $winner = collect();
        $highestSuccessRate = 0;

        foreach ($players as $player) {
            $successRate = round(($this->calculateSuccessRate($player)), 2);
            $player->success_rate = $successRate;

            if ($highestSuccessRate == null || $successRate > $highestSuccessRate) {
                $highestSuccessRate = $successRate;
                $winner = collect([$player]); // Inicializa la colección con el jugador actual
            } elseif ($successRate == $highestSuccessRate) {
                $winner->push($player); // Agrega el jugador a la colección de perdedores
            }
        }

        // Mapea los datos de los perdedores para su salida
        $winnersData = $winner->map(function ($winner) {
            return [
                'name' => $winner->name,
                'success_rate' => $winner->success_rate
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'Player with highest success score is:',
            'Winner(s)' => $winnersData->toArray()

        ]);
    }
    /**
     * @OA\Get(
     *     path="/api/v1/players/ranking/loser",
     *     tags={"Admin"},
     *     summary="Get loser(s) with lowest success rate",
     *     operationId="rankingLoser",
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example="true"),
     *             @OA\Property(property="message", type="string", example="Player with lowest success score is:"),
     *             @OA\Property(property="Loser(s)", type="array", @OA\Items(ref="#/components/schemas/User")),
     *         )
     *     )
     * )
     */
    public function rankingLoser(): JsonResponse
    {

        $players = $this->getPlayers();
        $losers = collect();
        $lowestSuccessRate = null;

        foreach ($players as $player) {
            $successRate = round($this->calculateSuccessRate($player), 2);
            $player->success_rate = $successRate;

            // Si es el primer jugador o tiene una tasa de éxito más baja que la actual más baja, actualiza los perdedores
            if ($lowestSuccessRate == null || $successRate < $lowestSuccessRate) {
                $lowestSuccessRate = $successRate;
                $losers = collect([$player]); // Inicializa la colección con el jugador actual
            } elseif ($successRate == $lowestSuccessRate) {
                $losers->push($player); // Agrega el jugador a la colección de perdedores
            }
        }

        // Mapea los datos de los perdedores para su salida
        $losersData = $losers->map(function ($loser) {
            return [
                'name' => $loser->name,
                'success_rate' => $loser->success_rate
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'Player with lowest success score is:',
            'Loser(s)' => $losersData->toArray()

        ]);
    }

    /**
     * @OA\Schema(
     *     schema="Game",
     *     required={"user_id", "dice1_value", "dice2_value", "sum", "result"},
     *     title="Game",
     *     description="Game schema",
     *     @OA\Property(property="user_id", type="integer", description="ID of the user associated with the game"),
     *     @OA\Property(property="dice1_value", type="integer", description="Value of the first dice rolled in the game"),
     *     @OA\Property(property="dice2_value", type="integer", description="Value of the second dice rolled in the game"),
     *     @OA\Property(property="sum", type="integer", description="Sum of the values of the two dice"),
     *     @OA\Property(property="result", type="string", description="Result of the game"),
     * )
     * @OA\Get(
     *     path="/api/v1/players/{id}/games",
     *     tags={"Player"},
     *     summary="Get games history of a player",
     *     operationId="gamesHistory",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the player",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example="true"),
     *             @OA\Property(property="message", type="string", example="Player John Doe games history:"),
     *             @OA\Property(property="games_played", type="integer", example="5"),
     *             @OA\Property(property="success_rate", type="string", example="75.00 %"),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Game")),
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Unauthorized"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="User not found"),
     *         )
     *     )
     * )
     */
    public function gamesHistory(int $id, Request $request): JsonResponse
    {
        $user = User::findOrFail($id);
        //validacion usuario logeado y solicitud
        if ($request->user()->id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $games = Game::where('user_id', $id)->get();
        $successRate = round($this->calculateSuccessRate($user), 2);

        return response()->json([
            'status' => true,
            'message' => 'Player ' . $user->name . ' games history:',
            'games_played' => count($games),
            'success_rate' => $successRate . ' %',
            'data' => $games
        ]);
    }

    private function calculateSuccessRate(User $user): float
    {
        $games = Game::where('user_id', $user->id)->get();
        $won = $games->filter(function ($game) {
            return $game->result == 'won';
        })->count();

        $totalGames = $games->count();
        return $totalGames > 0 ? ($won / $totalGames) * 100 : 0;
    }

    //DELETE history of a player
    /**
     * Delete game history for a specific player.
     *
     * @OA\Delete(
     *     path="/api/v1/players/{id}/games-history",
     *     tags={"Player"},
     *     summary="Delete game history for a specific player",
     *     operationId="deleteHistory",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the player",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Game history deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Game history deleted for user: John Doe")
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
    public function deleteHistory(int $id, Request $request): JsonResponse
    {
        $user = User::findOrFail($id);
        //validacion usuario logeado y solicitud
        if ($request->user()->id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        Game::where('user_id', $user->id)->delete();
        return response()->json([
            'status' => true,
            'message' => 'Game history deleted for user: ' . $user->name
        ]);
    }
}
