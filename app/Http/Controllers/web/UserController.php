<?php

namespace App\Http\Controllers\web;

use Illuminate\Routing\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Exports\UsersExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class UserController extends Controller
{
    /**
     * Exibe a página de listagem de usuários
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        
        // Excluir o usuário de ID 1 da listagem
        $users = User::where('id', '!=', 1)->paginate($perPage);
        
        return Inertia::render('Home', [
            'users' => $users
        ]);
    }

    /**
     * Busca usuários com filtro
     */
    public function search(Request $request)
    {
        $query = User::query()->where('id', '!=', 1);
        $perPage = $request->input('per_page', 10);

        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('email', 'like', "%{$searchTerm}%")
                  ->orWhere('user_name', 'like', "%{$searchTerm}%");
            });
        }

        // Retorna resultados paginados
        $users = $query->paginate($perPage);

        return response()->json($users);
    }

    /**
     * Atualiza os dados de um usuário
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'user_name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'password' => 'nullable|string|min:6',
        ]);

        // Atualizar dados básicos
        $user->user_name = $validated['user_name'] ?? $user->user_name;
        $user->email = $validated['email'];
        
        // Atualizar senha apenas se foi fornecida
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }
        
        $user->save();

        return response()->json([
            'message' => 'Usuário atualizado com sucesso',
            'user' => $user
        ]);
    }

    /**
     * Remove um usuário
     */
    public function destroy(User $user)
    {
        if ($user->id === 1) {
            return response()->json(['message' => 'Este usuário não pode ser excluído'], 403);
        }
        
        $user->forceDelete();

        return response()->json(['message' => 'Usuário excluído com sucesso']);
    }

    /**
     * Exporta os usuários para um arquivo Excel
     */
    public function exportExcel(Request $request)
    {
        try {
            $request->validate([
                'start_date' => 'required|date_format:Y-m',
                'end_date' => 'required|date_format:Y-m|after_or_equal:start_date',
            ]);

            $export = new UsersExport($request->start_date, $request->end_date);

            if (!$export->hasData()) {
                return response()->json([
                    'message' => 'Não existem dados de rotas finalizadas para o período selecionado.'
                ], 404);
            }

            $startDate = Carbon::parse($request->start_date)->format('m-Y');
            $endDate = Carbon::parse($request->end_date)->format('m-Y');
            $filename = "{$startDate}-{$endDate}-usuarios.xlsx";

            return Excel::download($export, $filename, \Maatwebsite\Excel\Excel::XLSX, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao exportar Excel: ' . $e->getMessage());
            return response()->json([
                'message' => 'Erro ao gerar arquivo Excel: ' . $e->getMessage()
            ], 500);
        }
    }
} 