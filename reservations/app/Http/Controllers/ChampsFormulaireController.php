<?php

namespace App\Http\Controllers;

use App\Models\ChampsFormulaire;
use App\Models\Formulaire;
use Illuminate\Http\Request;

class ChampsFormulaireController extends Controller
{
    public function create(Formulaire $formulaire)
    {
        return view('champs.create', compact('formulaire'));
    }

    public function store(Request $request, Formulaire $formulaire)
    {
        // Validation pour éviter les doublons
        $request->validate([
            'nom_champ' => [
                'required',
                'string',
                'max:100',
                // Cette règle assure que le nom du champ est unique pour ce formulaire
                function ($attribute, $value, $fail) use ($formulaire) {
                    if (ChampsFormulaire::where('formulaire_id', $formulaire->id)->where('nom_champ', $value)->exists()) {
                        $fail('Le champ avec le même nom existe déjà pour ce formulaire.');
                    }
                },
            ],
            'types_champs' => 'required|in:texte,nombre,photo',
        ]);

        // Créer le champ si la validation réussit
        ChampsFormulaire::create([
            'nom_champ' => $request->nom_champ,
            'types_champs' => $request->types_champs,
            'formulaire_id' => $formulaire->id,
        ]);

        return redirect()->route('formulaires.show', $formulaire->id)->with('success', 'Champ ajouté avec succès !');
    }
}
