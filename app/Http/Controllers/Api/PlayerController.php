<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Game;

class PlayerController extends Controller
{
    public function getPlayers()
    {
        return User::whereHas('roles', function ($query) {
            $query->where('name', 'player');
        })->get();
    }
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
    public function ranking()
    {
        $totalSuccessRate =0;
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
    public function rankingLoser()
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
    public function gamesHistory($id)
    {
        $user = User::findOrFail($id);
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
        ]);
    }
}
