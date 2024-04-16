<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Game;

class PlayerController extends Controller
{
    public function listPlayers()
    {
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
        ]);
    }
}
