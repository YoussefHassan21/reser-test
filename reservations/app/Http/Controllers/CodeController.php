<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Code;

class CodeController extends Controller
{
    public function index()
    {
        $codes = Code::all(); // Récupérer tous les codes
        return view('codes.index', compact('codes')); // Retourner la vue avec les codes
    }

    public function create()
    {
        return view('codes.create'); // Retourner la vue pour créer un code
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:codes,code', // Code unique requis
        ]);

        Code::create($request->all()); // Créer un nouveau code
        return redirect()->route('codes.index')->with('success', 'Code créé avec succès.');
    }

    // Ajouter d'autres méthodes (edit, update, destroy) si nécessaire
}
