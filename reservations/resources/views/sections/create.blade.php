@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1>Ajouter une Section au Formulaire: {{ $form->name }}</h1>

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

    <form action="{{ route('sections.store', $form) }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="name">Nom de la Section</label>
            <input type="text" name="name" id="name" class="form-control" required value="{{ old('name') }}">
        </div>

        <div class="form-group">
            <label for="order">Ordre</label>
            <input type="number" name="order" id="order" class="form-control" required value="{{ old('order') }}">
        </div>

        <!-- Champ pour insérer un nouveau code -->
        <div class="form-group">
            <label for="code">Code :</label>
            <input type="text" name="code" id="code" class="form-control" required value="{{ old('code') }}">
            @if ($errors->has('code'))
                <div class="text-danger mt-2">
                    {{ $errors->first('code') }}
                </div>
            @endif
        </div>

        <button type="submit" class="btn btn-success">Ajouter la Section</button>
    </form>
</div>
@endsection
