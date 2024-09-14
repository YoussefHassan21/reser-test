<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\Field;
use App\Models\Code;
use Illuminate\Http\Request;

class SectionFieldController extends Controller
{
    public function create(Section $section)
    {
        return view('sections.fields.create', compact('section'));
    }

    public function store(Request $request, Section $section)
    {
        // Valider les données du formulaire
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:text,url,photo,numerique,signature,date,telephone',
            'order' => 'required|integer|min:1',
            'code' => [
                'required',
                'string',
                'max:255',
                'unique:code,code',
                'regex:/^[a-zA-Z_][a-zA-Z0-9_]*$/', // Vérification de la syntaxe du code
            ],
        ], [
            'code.regex' => 'Le code doit commencer par une lettre ou un underscore _ et peut contenir uniquement des lettres, chiffres, et underscores _ .',
        ]);

        // Vérifier si le code existe déjà
        if (Code::where('code', $validated['code'])->exists()) {
            return redirect()->back()->withErrors(['code' => 'Ce code existe déjà.'])->withInput();
        }

        // Décaler l'ordre des champs suivants dans la section uniquement si l'ordre existe déjà
        Field::where('section_id', $section->id)
            ->where('order', '>=', $validated['order'])
            ->increment('order');

        // Créer le champ et l'associer uniquement à la section
        $field = Field::create([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'order' => $validated['order'],
            'section_id' => $section->id,
        ]);

        // Créer un nouveau code et l'enregistrer en associant le field_id
        Code::create([
            'code' => $validated['code'],
            'field_id' => $field->id,
        ]);

        return redirect()->route('sections.show', $section->id)->with('success', 'Champ ajouté à la section avec succès!');
    }

    // Méthode pour supprimer un champ de la section
    public function destroy(Section $section, Field $field)
    {
        $field->delete();
        return redirect()->route('sections.show', $section->id)->with('success', 'Champ supprimé avec succès!');
    }

    // Méthode pour mettre à jour l'ordre des champs dans une section
    public function updateFieldOrder(Request $request, Section $section)
    {
        $fieldOrders = $request->input('fields');
        foreach ($fieldOrders as $index => $fieldId) {
            Field::where('id', $fieldId)->where('section_id', $section->id)->update(['order' => $index + 1]);
        }

        return response()->json(['success' => true]);
    }
}
