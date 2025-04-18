<?php

namespace App\Http\Controllers\web;

use App\Models\UserLevel;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;

class UserLevelController extends Controller
{
    /**
     * Exibe a página de níveis de usuário
     */
    public function index()
    {
        $levels = UserLevel::orderBy('level_number')->get();
        
        return Inertia::render('Levels', [
            'levels' => $levels,
            'title' => 'Níveis de Usuário'
        ]);
    }

    /**
     * Atualiza os pontos necessários para um nível
     */
    public function update(Request $request, UserLevel $level)
    {
        $validated = $request->validate([
            'points_required' => 'required|integer|min:0',
        ]);

        // Verificar se o level é 1, que deve ter sempre 0 pontos
        if ($level->level_number === 1 && $validated['points_required'] !== 0) {
            return response()->json([
                'message' => 'O Nível 1 deve ter sempre 0 pontos',
                'errors' => ['points_required' => ['O Nível 1 deve ter sempre 0 pontos']]
            ], 422);
        }

        // Verificar se o level atual tem pontos maiores que o level anterior
        $previousLevel = UserLevel::where('level_number', $level->level_number - 1)->first();
        if ($previousLevel && $validated['points_required'] <= $previousLevel->points_required) {
            return response()->json([
                'message' => 'Os pontos devem ser maiores que o nível anterior',
                'errors' => ['points_required' => ['Os pontos devem ser maiores que o nível anterior (' . $previousLevel->points_required . ')']]
            ], 422);
        }

        // Verificar se o level atual tem pontos menores que o próximo level
        $nextLevel = UserLevel::where('level_number', $level->level_number + 1)->first();
        if ($nextLevel && $validated['points_required'] >= $nextLevel->points_required) {
            return response()->json([
                'message' => 'Os pontos devem ser menores que o próximo nível',
                'errors' => ['points_required' => ['Os pontos devem ser menores que o próximo nível (' . $nextLevel->points_required . ')']]
            ], 422);
        }

        $level->points_required = $validated['points_required'];
        $level->save();

        return response()->json([
            'message' => 'Pontos do nível atualizados com sucesso',
            'level' => $level
        ]);
    }
} 