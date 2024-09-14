<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\Field;
use App\Models\Form;
use Illuminate\Http\Request;
use App\Models\Code;

class SectionController extends Controller
{
    // Afficher le formulaire de création de section
    public function create(Form $form)
    {
        return view('sections.create', compact('form'));
    }

    // Afficher le formulaire de création d'une sous-section
    public function createChild(Section $section)
    {
        return view('sections.create_child', compact('section'));
    }

    // Enregistrer une nouvelle section
    public function store(Request $request, Form $form)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'order' => 'required|integer',
            'code' => [
                'required',
                'string',
                'max:255',
                'unique:code,code',
                'regex:/^[a-zA-Z_][a-zA-Z0-9_]*$/', // Vérification de la syntaxe du code
            ],
        ], [
            'code.regex' => 'Le code doit commencer par une lettre ou un underscore _ et peut contenir uniquement des lettres, chiffres, et underscores _.',
        ]);

        // Vérifier si le code existe déjà
        if (Code::where('code', $validated['code'])->exists()) {
            return redirect()->back()->withErrors(['code' => 'Ce code existe déjà.'])->withInput();
        }

        // Décaler l'ordre des sections suivantes
        Section::where('form_id', $form->id)
            ->where('order', '>=', $validated['order'])
            ->increment('order');

        // Créer la section associée au formulaire
        $section = $form->sections()->create([
            'name' => $validated['name'],
            'order' => $validated['order'],
        ]);

        // Créer un nouveau code et l'enregistrer en associant section_id
        Code::create([
            'code' => $validated['code'],
            'section_id' => $section->id,
        ]);

        return redirect()->route('forms.show', $form)->with('success', 'Section ajoutée avec succès!');
    }

    // Enregistrer une nouvelle sous-section
    public function storeChild(Request $request, Section $section)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'order' => 'required|integer',
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

        // Décaler l'ordre des sous-sections suivantes
        Section::where('parent_id', $section->id)
            ->where('order', '>=', $validated['order'])
            ->increment('order');

        // Créer la sous-section associée à la section parente
        $childSection = $section->children()->create([
            'name' => $validated['name'],
            'order' => $validated['order'],
            'parent_id' => $section->id, // Associer à la section parente
        ]);

        // Créer un nouveau code et l'enregistrer en associant section_id
        Code::create([
            'code' => $validated['code'],
            'section_id' => $childSection->id,
        ]);

        return redirect()->route('sections.show', $section)->with('success', 'Sous-section ajoutée avec succès!');
    }

    // Afficher les détails d'une section
    public function show(Section $section)
{
    // Vérifie si la section est une sous-section
    $isSubSection = !is_null($section->parent_id);

    // Récupérer tous les champs de cette section
    $fields = $section->fields()->orderBy('order')->get();

    // Récupérer toutes les sous-sections de cette section
    $subSections = $section->children()->orderBy('order')->get();

    // Fusionner les champs et les sous-sections dans une seule collection
    $elements = collect();

    // Ajouter les champs à la collection
    foreach ($fields as $field) {
        $elements->push((object)[
            'id' => $field->id,
            'name' => $field->name,
            'type' => 'field',
            'order' => $field->order,
        ]);
    }

    // Ajouter les sous-sections à la collection avec leurs enfants
    foreach ($subSections as $subSection) {
        $elements->push($this->getSectionWithChildren($subSection)); // Utiliser une fonction récursive
    }

    // Trier les éléments par ordre
    $elements = $elements->sortBy('order')->values();

    return view('sections.show', compact('section', 'elements', 'isSubSection'));
}

/**
 * Fonction récursive pour obtenir une section avec toutes ses sous-sections et champs
 */
private function getSectionWithChildren($section)
{
    $children = [];

    // Récupérer les sous-sections de cette section
    $subSections = $section->children()->orderBy('order', 'asc')->get();

    foreach ($subSections as $subSection) {
        $children[] = $this->getSectionWithChildren($subSection); // Appel récursif pour obtenir tous les niveaux
    }

    // Récupérer les champs associés à cette section
    $sectionFields = $section->fields()->orderBy('order', 'asc')->get();

    foreach ($sectionFields as $field) {
        $children[] = (object)[
            'id' => $field->id,
            'name' => $field->name,
            'type' => 'field',
            'order' => $field->order,
        ];
    }

    return (object)[
        'id' => $section->id,
        'name' => $section->name,
        'type' => 'section',
        'order' => $section->order,
        'children' => $children, // Ajouter les enfants ici
    ];
}


    // Récupérer toutes les sous-sections de manière récursive
    private function getAllChildren(Section $section)
    {
        $children = collect();

        foreach ($section->children()->orderBy('order')->get() as $child) {
            $grandChildren = $this->getAllChildren($child); // Récursivité
            $children->push((object)[
                'id' => $child->id,
                'name' => $child->name,
                'type' => 'section',
                'order' => $child->order,
                'children' => $grandChildren
            ]);
        }

        return $children;
    }

    // Supprimer un champ dans la section
    public function destroyField(Section $section, Field $field)
    {
        $field->delete();
        return redirect()->route('sections.show', $section)->with('success', 'Champ supprimé avec succès!');
    }

    // Mettre à jour l'ordre des éléments dans une section
    public function updateElementOrder(Request $request, Section $section)
    {
        $elementOrders = $request->input('elements');
        foreach ($elementOrders as $element) {
            if ($element['type'] === 'field') {
                Field::where('id', $element['id'])->where('section_id', $section->id)->update(['order' => $element['order']]);
            } elseif ($element['type'] === 'section') {
                Section::where('id', $element['id'])->where('parent_id', $section->id)->update(['order' => $element['order']]);
            }
        }

        return response()->json(['success' => true]);
    }

    // Supprimer une section
    public function destroy(Section $section)
    {
        $section->delete();
        return redirect()->route('forms.show', $section->form)->with('success', 'Section supprimée avec succès!');
    }
}
