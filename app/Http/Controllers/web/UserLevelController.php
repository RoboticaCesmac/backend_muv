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
            'carbon_footprint_required' => 'required|integer|min:0',
        ]);

        // Verificar se o level é 1, que deve ter sempre 0 pontos
        if ($level->level_number === 1 && $validated['carbon_footprint_required'] !== 0) {
            return response()->json([
                'message' => 'O Nível 1 deve ter sempre 0 pontos',
                'errors' => ['carbon_footprint_required' => ['O Nível 1 deve ter sempre 0 pontos']]
            ], 422);
        }

        // Verificar se o level atual tem pontos maiores que o level anterior
        $previousLevel = UserLevel::where('level_number', $level->level_number - 1)->first();
        if ($previousLevel && $validated['carbon_footprint_required'] <= $previousLevel->carbon_footprint_required) {
            return response()->json([
                'message' => 'A pegada de carbono deve ser maior que o nível anterior',
                'errors' => ['carbon_footprint_required' => ['A pegada de carbono deve ser maior que o nível anterior (' . $previousLevel->carbon_footprint_required . ')']]
            ], 422);
        }

        // Verificar se o level atual tem pontos menores que o próximo level
        $nextLevel = UserLevel::where('level_number', $level->level_number + 1)->first();
        if ($nextLevel && $validated['carbon_footprint_required'] >= $nextLevel->carbon_footprint_required) {
            return response()->json([
                'message' => 'A pegada de carbono deve ser menor que o próximo nível',
                'errors' => ['carbon_footprint_required' => ['A pegada de carbono deve ser menor que o próximo nível (' . $nextLevel->carbon_footprint_required . ')']]
            ], 422);
        }

        $level->carbon_footprint_required = $validated['carbon_footprint_required'];
        $level->save();

        return response()->json([
            'message' => 'Pegada de carbono do nível atualizados com sucesso',
            'level' => $level
        ]);
    }
} 