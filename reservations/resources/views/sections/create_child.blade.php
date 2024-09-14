@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1>Ajouter une Sous-section à la Section: {{ $section->name }}</h1>

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

    <form action="{{ route('sections.storeChild', $section) }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="name">Nom de la Sous-section</label>
            <input type="text" name="name" id="name" class="form-control" required value="{{ old('name') }}">
            @if ($errors->has('name'))
                <div class="text-danger mt-2">
                    {{ $errors->first('name') }}
                </div>
            @endif
        </div>

        <div class="form-group">
            <label for="order">Ordre</label>
            <input type="number" name="order" id="order" class="form-control" required value="{{ old('order') }}">
            @if ($errors->has('order'))
                <div class="text-danger mt-2">
                    {{ $errors->first('order') }}
                </div>
            @endif
        </div>

        <div class="form-group">
            <label for="code">Code</label>
            <input type="text" name="code" id="code" class="form-control" required value="{{ old('code') }}">
            @if ($errors->has('code'))
                <div class="text-danger mt-2">
                    {{ $errors->first('code') }}
                </div>
            @endif
        </div>

        <button type="submit" class="btn btn-success">Ajouter la Sous-section</button>
    </form>
</div>
@endsection
