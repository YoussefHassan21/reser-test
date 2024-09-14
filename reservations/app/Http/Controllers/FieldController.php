<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\Field;
use App\Models\Code;
use Illuminate\Http\Request;

class FieldController extends Controller
{
    public function create(Form $form)
    {
        return view('fields.create', compact('form'));
    }

    public function store(Request $request, Form $form)
    {
        // Valider les données du formulaire
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:text,url,photo,numerique,signature,date,telephone',
            'order' => 'required|integer',
            'code' => [
                'required',
                'string',
                'max:255',
                'unique:code,code', // Vérification d'unicité
                'regex:/^[a-zA-Z_][a-zA-Z0-9_]*$/' // Vérification de la syntaxe du code
            ],
        ], [
            'code.unique' => 'Ce code existe déjà.',
            'code.regex' => 'Le code doit commencer par une lettre ou un underscore _ et peut contenir uniquement des lettres, chiffres, et underscores _ .',
        ]);

        // Décaler l'ordre des champs suivants
        Field::where('form_id', $form->id)
            ->where('order', '>=', $validated['order'])
            ->increment('order');

        // Créer le champ associé au formulaire
        $field = $form->fields()->create([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'order' => $validated['order'],
        ]);

        // Créer un nouveau code et l'enregistrer en associant le field_id
        Code::create([
            'code' => $validated['code'],
            'field_id' => $field->id,  // Associe le code au champ
        ]);

        return redirect()->route('forms.show', $form)->with('success', 'Champ ajouté avec succès!');
    }

    // Méthode pour supprimer un champ
    public function destroy(Form $form, Field $field)
    {
        // Supprimer le champ
        $field->delete();

        // Rediriger avec un message de succès
        return redirect()->route('forms.show', $form)->with('success', 'Champ supprimé avec succès!');
    }
}
