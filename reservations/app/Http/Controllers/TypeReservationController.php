<?php

namespace App\Http\Controllers;

use App\Models\TypeReservation;
use App\Models\Form;
use App\Models\Section;
use App\Models\Field;
use App\Models\Code;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TypeReservationController extends Controller
{
    /**
     * Affiche la liste des types de réservation.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $types = TypeReservation::all();
        return view('types_reservation.index', compact('types'));
    }

    /**
     * Affiche le formulaire pour créer un nouveau type de réservation.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Récupérer les formulaires appartenant à l'utilisateur connecté
        $forms = Form::where('user_id', Auth::id())->get();

        // Passer les données à la vue
        return view('types_reservation.create', compact('forms'));
    }

    /**
     * Enregistre un nouveau type de réservation dans la base de données.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'form_id' => 'required|exists:forms,id',
            'code' => [
                'required',
                'string',
                'max:255',
                'unique:code,code',  // Assurez-vous que le nom de la table et la colonne sont corrects
                'regex:/^[a-zA-Z_][a-zA-Z0-9_]*$/', // Vérification de la syntaxe du code
            ],
        ], [
            'code.regex' => 'Le code doit commencer par une lettre ou un underscore _ et peut contenir uniquement des lettres, chiffres, et underscores _ .',
            'code.unique' => 'Ce code est déjà utilisé.',
        ]);

        // Créer un nouveau TypeReservation
        $typeReservation = TypeReservation::create([
            'name' => $request->name,
            'description' => $request->description,
            'form_id' => $request->form_id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Créer et associer un nouveau Code à ce TypeReservation
        Code::create([
            'code' => $request->code,
            'type_reservation_id' => $typeReservation->id,
        ]);

        return redirect()->route('types_reservation.index')->with('success', 'Type de réservation créé avec succès.');
    }

    /**
     * Affiche les détails d'un type de réservation spécifique.
     *
     * @param  \App\Models\TypeReservation  $typeReservation
     * @return \Illuminate\View\View
     */
    public function show(TypeReservation $typeReservation)
    {
        $form = $typeReservation->form; // Récupérer le formulaire associé
        $sections = $form->sections()->orderBy('order', 'asc')->get();  // Tri par ordre croissant
        $fields = $form->fields()->whereNull('section_id')->orderBy('order', 'asc')->get();  // Tri par ordre croissant

        return view('types_reservation.show', compact('typeReservation', 'form', 'sections', 'fields'));
    }

    /**
     * Met à jour un type de réservation existant dans la base de données.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TypeReservation  $typeReservation
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, TypeReservation $typeReservation)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'form_id' => 'required|exists:forms,id',
            'code' => [
                'required',
                'string',
                'max:255',
                'unique:code,code,' . $typeReservation->id,
                'regex:/^[a-zA-Z_][a-zA-Z0-9_]*$/', // Vérification de la syntaxe du code
            ],
        ], [
            'code.regex' => 'Le code doit commencer par une lettre ou un underscore _ et peut contenir uniquement des lettres, chiffres, et underscores _ .',
            'code.unique' => 'Ce code est déjà utilisé.',
        ]);

        $typeReservation->update([
            'name' => $request->name,
            'description' => $request->description,
            'form_id' => $request->form_id,
            'updated_at' => now(),
        ]);

        // Mettre à jour le code associé
        $code = Code::where('type_reservation_id', $typeReservation->id)->first();
        if ($code) {
            $code->update([
                'code' => $request->code,
            ]);
        }

        return redirect()->route('types_reservation.index')->with('success', 'Type de réservation mis à jour avec succès.');
    }

    /**
     * Supprime un type de réservation spécifique de la base de données.
     *
     * @param  \App\Models\TypeReservation  $typeReservation
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(TypeReservation $typeReservation)
    {
        // Supprimer le code associé
        Code::where('type_reservation_id', $typeReservation->id)->delete();

        $typeReservation->delete();
        return redirect()->route('types_reservation.index')->with('success', 'Type de réservation supprimé avec succès.');
    }

    /**
     * Affiche les détails d'un formulaire spécifique avec ses sections et champs.
     *
     * @param  \App\Models\Form  $form
     * @return \Illuminate\View\View
     */
    public function showForm(Form $form)
    {
        $sections = $form->sections()->orderBy('order', 'asc')->get();  // Tri par ordre croissant
        $fields = $form->fields()->orderBy('order', 'asc')->get();  // Tri par ordre croissant

        return view('forms.show', compact('form', 'sections', 'fields'));
    }

    /**
     * Met à jour l'ordre des sections d'un formulaire.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Form  $form
     * @return \Illuminate\Http\JsonResponse
     */
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

    /**
     * Met à jour l'ordre des champs d'un formulaire.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Form  $form
     * @return \Illuminate\Http\JsonResponse
     */
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

    /**
     * Met à jour l'ordre des enfants d'une section.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Section  $section
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateChildSectionOrder(Request $request, Section $section)
    {
        $childOrders = $request->input('children');
        foreach ($childOrders as $index => $child) {
            Section::where('id', $child['id'])
                ->where('parent_id', $section->id)
                ->update(['order' => $child['order']]);
        }

        return response()->json(['success' => true]);
    }
}
