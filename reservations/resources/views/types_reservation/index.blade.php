@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Liste des types de réservation</h1>

    <ul>
        @foreach ($types as $type)
            <li>
                <a href="{{ route('types_reservation.show', $type->id) }}">
                    {{ $type->name }}
                </a>
            </li>
        @endforeach
    </ul>
</div>
@endsection
