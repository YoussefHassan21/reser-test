<?php

namespace App\Http\Controllers;

use App\Models\Form;  // Assurez-vous d'importer le modèle Form
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Récupère tous les formulaires
        $forms = Form::all();
        return view('dashboard', compact('forms'));
    }
}
