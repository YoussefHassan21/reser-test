@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Détails du type de réservation: {{ $typeReservation->name }}</h1>
    <p>Description: {{ $typeReservation->description ?? 'Aucune description' }}</p>

    <h3>Formulaire associé:</h3>
    <p>
        <strong>Nom du formulaire :</strong> 
        <a href="{{ route('forms.show', $form->id) }}">{{ $form->name }}</a>
    </p>
    <p><strong>Description du formulaire:</strong> {{ $form->description ?? 'Aucune description' }}</p>
</div>
@endsection
