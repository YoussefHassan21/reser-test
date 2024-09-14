@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1>Détails du Formulaire</h1>
    <p><strong>Nom :</strong> {{ $form->name }}</p>
    <p><strong>Description :</strong> {{ $form->description }}</p>

    <!-- Barre de Recherche pour rechercher des sections, sous-sections ou champs -->
    <div class="d-flex justify-content-center mt-3">
        <input type="text" id="search-bar" class="form-control w-50" placeholder="Rechercher des sections, sous-sections ou champs...">
    </div>

    <!-- Bouton pour ajouter une section -->
    <div class="d-flex justify-content-center mt-3">
        <a href="{{ route('sections.create', $form->id) }}" class="btn btn-primary mx-2">Ajouter une Section</a>
        <a href="{{ route('fields.create', $form->id) }}" class="btn btn-primary mx-2">Ajouter un Champ</a>
    </div>

    <!-- Afficher la liste mixte des Sections et Champs du Formulaire -->
    <div id="sortable-main-elements" class="list-group mt-4">
        @foreach ($mainUnits as $element)
            <div class="searchable-element">
                @include('partials.element', ['element' => $element, 'form' => $form]) <!-- Passage de la variable form -->
            </div>
        @endforeach
    </div>

    <!-- Bouton pour enregistrer l'ordre des éléments principaux -->
    <div class="d-flex justify-content-center">
        <button id="save-order-main-elements" class="btn btn-success mt-3">Enregistrer l'Ordre des Éléments Principaux</button>
    </div>
</div>

<script>
    $(function() {
        // Rendre les éléments combinés triables
        $("#sortable-main-elements").sortable({
            update: function(event, ui) {
                var mainElementOrder = [];
                $(".element").each(function(index) {
                    mainElementOrder.push({
                        id: $(this).data('id'),
                        type: $(this).data('type'),
                        order: index + 1 // Utiliser l'index pour donner l'ordre exact
                    });
                });

                $.ajax({
                    url: "{{ route('forms.updateMainElementOrder', $form->id) }}",
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        mainUnits: mainElementOrder
                    },
                    success: function(response) {
                        alert('Ordre des éléments principaux mis à jour avec succès !');
                        location.reload();
                    },
                    error: function(xhr) {
                        alert('Erreur lors de la mise à jour de l\'ordre des éléments principaux.');
                        console.error(xhr.responseText);
                    }
                });
            }
        });

        // Barre de recherche pour filtrer les éléments par nom
        $("#search-bar").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $(".searchable-element").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });

        // Fonction pour toggler l'affichage des sous-éléments
        function toggleElements(element) {
            var icon = element;
            var childElements = icon.closest('.element, .main-element').next('.child-elements');

            // Afficher/masquer les enfants seulement s'il y a des éléments
            if (childElements.length > 0) {
                childElements.toggleClass('d-none');
                
                // Ajuster les tailles des sous-éléments uniformément
                if (!childElements.data('size-adjusted')) {
                    adjustChildElementSizes(childElements);
                    childElements.data('size-adjusted', true); // Marquer comme ajusté
                }
            }

            // Toujours changer la flèche
            icon.text(icon.text() === '▶' ? '▼' : '▶'); // Changer la flèche
        }

        // Fonction pour ajuster la taille des sous-éléments
        function adjustChildElementSizes(childElements) {
            var maxWidth = childElements.closest('.element, .main-element').width(); // Utiliser la largeur de l'élément parent
            childElements.find('.child-item').each(function() {
                $(this).css('width', maxWidth + 'px'); // Appliquer la largeur maximale à chaque sous-élément
            });
        }

        // Attacher le gestionnaire d'événements de clic pour toutes les icônes de basculement
        $(document).on('click', '.toggle-icon', function() {
            toggleElements($(this));
        });
    });
</script>

<style>
    .toggle-header-container, #element-content {
        width: 100%;
    }

    .list-group-item {
        font-size: 0.9rem; /* Réduire la taille du texte */
    }

    #sortable-main-elements .list-group-item {
        margin-bottom: 10px;
    }

    .section-item {
        background-color: #85929e; /* Couleur grise plus forte */
        width: 100%; /* Utiliser 100% pour s'adapter au conteneur parent */
        padding: 5px 10px;
        line-height: 1.2;
        border-radius: 5px;
        margin: 0 auto; /* Centrer l'élément */
    }

    .section-item .toggle-icon {
        color: #fff;
    }

    .child-elements {
        margin-left: 0; /* Supprimer la marge gauche pour éviter le décalage */
        padding: 10px; /* Ajouter un padding pour le contour */
        background-color: #e8e8e8; /* Couleur de fond pour distinguer les sous-ensembles */
        border: 2px solid #b3b3b3; /* Bordure pour distinguer les sous-ensembles */
        border-radius: 5px; /* Coins arrondis */
        width: 100%; /* Largeur ajustée pour remplir le parent */
        box-sizing: border-box; /* Inclure la bordure et le padding dans la largeur */
        margin-bottom: 15px; /* Ajouter une marge inférieure pour éviter de coller aux champs en dessous */
    }

    .child-item {
        margin-bottom: 5px;
        width: 100%; /* Largeur uniforme pour tous les sous-éléments */
        padding: 5px 10px; /* Espacement uniforme */
        background-color: #ffffff; /* Couleur de fond blanche pour les sous-éléments */
        border: 1px solid #ddd; /* Bordure uniforme */
        border-radius: 5px; /* Coins arrondis */
        box-sizing: border-box; /* Inclure la bordure et le padding dans la largeur */
    }

    .section-link {
        color: #fff;
    }
</style>
@endsection
