@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Créer un nouveau type de réservation</h1>

    <!-- Afficher les messages d'erreur -->
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('types_reservation.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="name">Nom</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea name="description" id="description" class="form-control">{{ old('description') }}</textarea>
        </div>

        <div class="form-group">
            <label for="form_id">Formulaire</label>
            <select name="form_id" id="form_id" class="form-control" required>
                @if($forms && $forms->count() > 0)
                    @foreach ($forms as $form)
                        <option value="{{ $form->id }}" {{ old('form_id') == $form->id ? 'selected' : '' }}>{{ $form->name }}</option>
                    @endforeach
                @else
                    <option value="">Aucun formulaire disponible</option>
                @endif
            </select>
        </div>

        <!-- Champ de saisie pour le code -->
        <div class="form-group">
            <label for="code">Code</label>
            <input type="text" name="code" id="code" class="form-control" value="{{ old('code') }}" required>
        </div>

        <button type="submit" class="btn btn-primary">Créer</button>
    </form>
</div>
@endsection
