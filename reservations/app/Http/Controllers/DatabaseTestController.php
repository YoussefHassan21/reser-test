<?php
// app/Http/Controllers/DatabaseTestController.php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class DatabaseTestController extends Controller
{
    public function testConnection()
    {
        try {
            // Essayez de vous connecter à la base de données
            DB::connection()->getPdo();
            return response()->json(['message' => 'Connexion réussie à la base de données!']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur de connexion : ' . $e->getMessage()]);
        }
    }
}
