<!-- resources/views/forms/index.blade.php -->
<div class="container mt-5">
    <h1>Gestion des Formulaires</h1>
    
    <a href="{{ route('forms.create') }}" class="btn btn-primary mb-3">Créer un Nouveau Formulaire</a>
 
    @if($forms->isEmpty())
        <p>Aucun formulaire trouvé.</p>
    @else
        <ul class="list-group">
            @foreach($forms as $form)
                <!-- Affiche uniquement les formulaires appartenant à l'utilisateur connecté -->
                <li class="list-group-item">
                    <a href="{{ route('forms.show', $form->id) }}">{{ $form->name }}</a>
                </li>
            @endforeach
        </ul>
    @endif
</div>
