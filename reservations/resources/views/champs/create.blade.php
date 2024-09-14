@extends('layouts.app')

@section('content')
<h1>Ajouter un Champ pour le Formulaire: {{ $formulaire->nom_formulaire }}</h1>

<form action="{{ route('champs.store', $formulaire->id) }}" method="POST">
    @csrf
    <div class="form-group">
        <label for="nom_champ">Nom du Champ :</label>
        <input type="text" name="nom_champ" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="types_champs">Type du Champ :</label>
        <select name="types_champs" class="form-control" required>
            <option value="texte">Texte</option>
            <option value="nombre">Nombre</option>
            <option value="photo">Photo</option>
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Ajouter Champ</button>
</form>

<a href="{{ route('formulaires.show', $formulaire->id) }}" class="btn btn-secondary mt-3">Retour</a>
@endsection
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
