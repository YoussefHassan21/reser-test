<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    /**
     * Affiche la liste des réservations.
     */
    public function index()
    {
        return view('reservations.index', ['reservations' => Reservation::all()]);
    }

    /**
     * Affiche le formulaire de création d'une nouvelle réservation.
     */
    public function create()
    {
        return view('reservations.create');
    }

    /**
     * Enregistre une nouvelle réservation dans la base de données.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom_reservation' => 'required|string|max:100',
            'date_reservation' => 'required|date'
        ]);

        Reservation::create($validated);

        return redirect()->route('reservations.index');
    }

    /**
     * Affiche les détails d'une réservation.
     */
    public function show(Reservation $reservation)
    {
        return view('reservations.show', compact('reservation'));
    }
}
