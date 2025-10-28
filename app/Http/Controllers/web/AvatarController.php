<?php

namespace App\Http\Controllers\web;

use Illuminate\Routing\Controller;
use App\Models\UserAvatar;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AvatarController extends Controller
{
    /**
     * Display the avatars listing page
     */
    public function index()
    {
        $avatars = UserAvatar::all();
        
        return Inertia::render('Avatars', [
            'avatars' => $avatars,
            'title' => 'Avatares'
        ]);
    }

    /**
     * Store a new avatar
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $file = $request->file('avatar');
        $fileName = Str::slug($validated['name']) . '_' . time() . '.' . $file->getClientOriginalExtension();
        
        // Salva em 'storage/app/public/images/avatars'
        // A variável $path conterá 'images/avatars/nome_do_arquivo.jpg'
        $path = $file->storeAs('images/avatars', $fileName, 'public');
        
        $avatar = UserAvatar::create([
            'name' => $validated['name'],
            'avatar_path' => 'storage/' . $path,
            'is_default' => false,
        ]);

        return response()->json([
            'message' => 'Avatar adicionado com sucesso',
            'avatar' => $avatar
        ], 201);
    }

    /**
     * Delete an avatar
     */
    public function destroy(UserAvatar $avatar)
    {
        if ($avatar->is_default) {
            return response()->json([
                'message' => 'Avatares padrão não podem ser excluídos'
            ], 403);
        }

        // Delete the actual image file
        if (file_exists(public_path($avatar->avatar_path))) {
            unlink(public_path($avatar->avatar_path));
        }

        $avatar->delete();

        return response()->json([
            'message' => 'Avatar excluído com sucesso'
        ]);
    }
} 