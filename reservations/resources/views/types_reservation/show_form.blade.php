@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Détails du formulaire: {{ $form->name }}</h1>

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

    <h2>Sections</h2>
    @foreach ($sections as $section)
        <div class="section">
            <h3>{{ $section->name }}</h3>
            <p>{{ $section->description }}</p>

            <h4>Champs</h4>
            <ul>
                @foreach ($section->fields as $field)
                    <li>{{ $field->name }}: {{ $field->type }}</li>
                @endforeach
            </ul>
        </div>
    @endforeach
</div>
@endsection
