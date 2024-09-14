<?php

namespace App\Http\Controllers;

use App\Models\Formulaire;
use Illuminate\Http\Request;

class FormulaireController extends Controller
{
    public function index()
    {
        $formulaires = Formulaire::all();
        return view('formulaires.index', compact('formulaires'));
    }

    public function create()
    {
        return view('formulaires.create');
    }

    public function store(Request $request)
    {
        // Validation pour éviter les doublons
        $request->validate([
            'nom_formulaire' => [
                'required',
                'string',
                'max:100',
                // Cette règle assure que le nom du formulaire est unique
                function ($attribute, $value, $fail) {
                    if (Formulaire::where('nom_formulaire', $value)->exists()) {
                        $fail('Un formulaire avec le même nom existe déjà.');
                    }
                },
            ],
            'date_creation' => 'required|date',
        ]);

        // Créer le formulaire si la validation réussit
        Formulaire::create([
            'nom_formulaire' => $request->nom_formulaire,
            'date_creation' => $request->date_creation,
        ]);

        return redirect()->route('formulaires.index')->with('success', 'Formulaire ajouté avec succès !');
    }

    public function show(Formulaire $formulaire)
    {
        $champs = $formulaire->champsFormulaires; // Assumez que vous avez une relation définie dans le modèle Formulaire
        return view('formulaires.show', compact('formulaire', 'champs'));
    }
}
