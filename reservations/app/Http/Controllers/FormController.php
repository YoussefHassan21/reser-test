<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\Field;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FormController extends Controller
{
    // Afficher le formulaire pour ajouter un champ au formulaire
    public function createField(Form $form)
    {
        return view('forms.add_field', compact('form'));
    }

    // Enregistrer un nouveau champ dans le formulaire
    public function storeField(Request $request, Form $form)
    {
        // Valider les données du champ
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:text,url,photo,numerique,signature,date,telephone',
            'order' => 'required|integer',
            'code' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z_][a-zA-Z0-9_]*$/',
                function ($attribute, $value, $fail) use ($form) {
                    $existingField = Field::where('form_id', $form->id)
                        ->where('code', $value)
                        ->first();
                    if ($existingField) {
                        $fail('Ce code est déjà utilisé dans ce formulaire.');
                    }
                },
            ],
        ], [
            'code.regex' => 'Le code doit commencer par une lettre ou un underscore _ et peut contenir uniquement des lettres, chiffres, et underscores _ .',
        ]);

        // Vérifier si un champ existe déjà à cet ordre
        $existingFieldAtPosition = Field::where('form_id', $form->id)
            ->where('order', $validated['order'])
            ->exists();

        if ($existingFieldAtPosition) {
            // Décaler l'ordre des champs suivants de 1
            Field::where('form_id', $form->id)
                ->where('order', '>=', $validated['order'])
                ->increment('order');
        }

        // Créer le nouveau champ avec l'ordre correct
        $form->fields()->create($validated);

        return redirect()->route('forms.show', $form)->with('success', 'Champ ajouté avec succès!');
    }

    // Afficher le formulaire pour créer un nouveau formulaire
    public function create()
    {
        return view('forms.create');
    }

    // Enregistrer un nouveau formulaire
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'code' => [
                'required',
                'string',
                'max:255',
                'unique:codes,code', // Assurez-vous que le nom de la table et la colonne sont corrects
                'regex:/^[a-zA-Z_][a-zA-Z0-9_]*$/', // Vérification de la syntaxe du code
            ],
        ], [
            'code.regex' => 'Le code doit commencer par une lettre ou un underscore _ et peut contenir uniquement des lettres, chiffres, et underscores _ .',
            'code.unique' => 'Ce code est déjà utilisé.',
        ]);

        // Si la validation passe, le code continue ici...
        $form = Form::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'user_id' => auth()->id(),
        ]);

        Code::create([
            'code' => $validated['code'],
            'form_id' => $form->id,
        ]);

        return redirect()->route('forms.index')->with('success', 'Formulaire ajouté avec succès!');
    }

    // Afficher la liste des formulaires
    public function index()
    {
        $forms = Form::where('user_id', auth()->id())->get();
        return view('forms.index', compact('forms'));
    }

    // Afficher un formulaire spécifique avec ses détails
    public function show(Form $form)
{
    // Vérification de l'accès utilisateur
    if ($form->user_id !== auth()->id()) {
        return redirect()->route('forms.index')->with('error', 'Accès non autorisé.');
    }

    // Récupérer les sections principales du formulaire (celles qui n'ont pas de parent_id)
    $sections = Section::where('form_id', $form->id)
        ->whereNull('parent_id')
        ->orderBy('order', 'asc')  // Utiliser 'order' pour trier les sections
        ->get();

    // Récupérer les champs du formulaire sans section associée
    $fields = $form->fields()->whereNull('section_id')->orderBy('order', 'asc')->get();

    // Rassembler les sections et les champs dans une seule unité principale
    $mainUnits = collect();

    // Ajouter les sections à la collection avec leurs sous-sections et champs
    foreach ($sections as $section) {
        $mainUnits->push($this->getSectionWithChildren($section));
    }

    // Ajouter les champs non associés à une section
    foreach ($fields as $field) {
        $mainUnits->push([
            'id' => $field->id,
            'name' => $field->name,
            'type' => 'field',
            'order' => $field->order,
        ]);
    }

    // Assurez-vous de trier par 'order' et d'utiliser 'values' pour réindexer la collection
    $mainUnits = $mainUnits->sortBy('order')->values();

    return view('forms.show', compact('form', 'mainUnits'));
}

/**
 * Fonction récursive pour obtenir une section avec toutes ses sous-sections et champs
 */
private function getSectionWithChildren($section)
{
    $children = [];

    // Ajouter les sous-sections
    $subSections = Section::where('parent_id', $section->id)
        ->orderBy('order', 'asc')
        ->get();

    foreach ($subSections as $subSection) {
        $children[] = $this->getSectionWithChildren($subSection); // Appel récursif
    }

    // Ajouter les champs associés à la section
    $sectionFields = Field::where('section_id', $section->id)
        ->orderBy('order', 'asc')
        ->get();

    foreach ($sectionFields as $field) {
        $children[] = [
            'id' => $field->id,
            'name' => $field->name,
            'type' => 'field',
            'order' => $field->order,
        ];
    }

    return [
        'id' => $section->id,
        'name' => $section->name,
        'type' => 'section',
        'order' => $section->order,
        'children' => $children, // Ajouter les enfants ici
    ];
}


    // Supprimer une section du formulaire
    public function destroySection(Form $form, Section $section)
    {
        $section->delete();
        return redirect()->route('forms.show', $form)->with('success', 'Section supprimée avec succès!');
    }

    // Supprimer un champ du formulaire
    public function destroyField(Form $form, Field $field)
    {
        $field->delete();
        return redirect()->route('forms.show', $form)->with('success', 'Champ supprimé avec succès!');
    }

    // Mettre à jour l'ordre des sections
    public function updateSectionOrder(Request $request, Form $form)
    {
        $sectionOrders = $request->input('sections');
        $logMessages = [];

        foreach ($sectionOrders as $sectionData) {
            $section = Section::where('id', $sectionData['id'])
                ->where('form_id', $form->id)
                ->first();

            if ($section) {
                $section->order = $sectionData['order'];
                if ($section->save()) {
                    $logMessages[] = "Section ID {$section->id} mis à jour avec succès à l'ordre {$section->order}.";
                } else {
                    $logMessages[] = "Échec de la sauvegarde de la section ID {$section->id}.";
                }
            } else {
                $logMessages[] = "Section ID {$sectionData['id']} non trouvée pour Form ID {$form->id}.";
            }
        }

        return response()->json(['success' => true, 'messages' => $logMessages]);
    }

    // Mettre à jour l'ordre des champs
    public function updateFieldOrder(Request $request, Form $form)
    {
        $fieldOrders = $request->input('fields');
        $logMessages = [];

        foreach ($fieldOrders as $fieldData) {
            $field = Field::where('id', $fieldData['id'])
                ->where('form_id', $form->id)
                ->whereNull('section_id')
                ->first();

            if ($field) {
                $field->order = $fieldData['order'];
                if ($field->save()) {
                    $logMessages[] = "Champ ID {$field->id} mis à jour avec succès à l'ordre {$field->order}.";
                } else {
                    $logMessages[] = "Échec de la sauvegarde du champ ID {$field->id}.";
                }
            } else {
                $logMessages[] = "Champ ID {$fieldData['id']} non trouvé pour Form ID {$form->id}.";
            }
        }

        return response()->json(['success' => true, 'messages' => $logMessages]);
    }

    // Mettre à jour l'ordre des unités principales
    public function updateMainElementOrder(Request $request, Form $form)
    {
        $mainUnits = $request->input('mainUnits');
        $logMessages = [];

        foreach ($mainUnits as $mainUnit) {
            $order = $mainUnit['order'];

            if ($mainUnit['type'] === 'section') {
                $section = Section::where('id', $mainUnit['id'])->where('form_id', $form->id)->first();
                if ($section) {
                    $section->order = $order;
                    if ($section->save()) {
                        $logMessages[] = "Ordre de la section ID {$section->id} mis à jour avec succès à l'ordre {$order}.";
                    } else {
                        $logMessages[] = "Échec de la sauvegarde de l'ordre de la section ID {$section->id}.";
                    }
                }
            } elseif ($mainUnit['type'] === 'field') {
                $field = Field::where('id', $mainUnit['id'])->where('form_id', $form->id)->first();
                if ($field) {
                    $field->order = $order;
                    if ($field->save()) {
                        $logMessages[] = "Ordre du champ ID {$field->id} mis à jour avec succès à l'ordre {$order}.";
                    } else {
                        $logMessages[] = "Échec de la sauvegarde de l'ordre du champ ID {$field->id}.";
                    }
                }
            }
        }

        return response()->json(['success' => true, 'messages' => $logMessages]);
    }
}
