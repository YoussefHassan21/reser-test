@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1>Créer un Nouveau Formulaire</h1>

    <!-- Afficher les erreurs de validation -->
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('forms.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="name">Nom du Formulaire :</label>
            <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
        </div>

        <div class="form-group">
            <label for="description">Description :</label>
            <textarea name="description" class="form-control">{{ old('description') }}</textarea>
        </div>

        <!-- Champ pour insérer un nouveau code -->
        <div class="form-group">
            <label for="code">Code :</label>
            <input type="text" name="code" class="form-control" required value="{{ old('code') }}">
            @if ($errors->has('code'))
                <div class="text-danger mt-2">
                    {{ $errors->first('code') }}
                </div>
            @endif
        </div>

        <button type="submit" class="btn btn-primary">Créer</button>
    </form>
</div>
@endsection
