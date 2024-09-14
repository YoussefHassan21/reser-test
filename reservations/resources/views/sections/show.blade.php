@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1>Section: {{ $section->name }}</h1>
    <p><strong>Ordre:</strong> {{ $section->order }}</p>

    <!-- Barre de Recherche pour rechercher des sections, sous-sections ou champs -->
    <div class="d-flex justify-content-center mt-3">
        <input type="text" id="search-bar" class="form-control w-50" placeholder="Rechercher des sections, sous-sections ou champs...">
    </div>

    <!-- Permettre d'ajouter un champ à la section ou sous-section -->
    <div class="d-flex justify-content-center mt-3">
        <a href="{{ route('section.fields.create', $section) }}" class="btn btn-primary mx-2">Ajouter un Champ à cette Section</a>
        <a href="{{ route('sections.createChild', $section) }}" class="btn btn-secondary mx-2">Ajouter une Sous-section</a>
    </div>

    <!-- Ordre combiné des Champs et Sous-sections -->
    <div id="sortable-elements" class="list-group mt-4">
        @foreach ($elements as $element)
            <div class="searchable-element">
                @include('partials.element', ['element' => $element, 'section' => $section]) <!-- Passage de la variable section -->
            </div>
        @endforeach
    </div>

    <!-- Bouton pour enregistrer l'ordre des éléments -->
    <div class="d-flex justify-content-center">
        <button id="save-order-elements" class="btn btn-success mt-3">Enregistrer l'Ordre des Éléments</button>
    </div>
</div>

<script>
    $(function() {
        // Rendre les éléments combinés triables
        $("#sortable-elements").sortable({
            update: function(event, ui) {
                var elementOrder = [];
                $(".element").each(function(index) {
                    elementOrder.push({
                        id: $(this).data('id'),
                        type: $(this).data('type'),
                        order: index + 1
                    });
                });

                $.ajax({
                    url: "{{ route('section.updateElementOrder', $section->id) }}",
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        elements: elementOrder
                    },
                    success: function(response) {
                        alert('Ordre des éléments mis à jour avec succès !');
                        location.reload();
                    },
                    error: function(xhr) {
                        alert('Erreur lors de la mise à jour de l\'ordre des éléments.');
                        console.error(xhr.responseText);
                    }
                });
            }
        });

        // Barre de recherche pour filtrer les éléments par nom
        $("#search-bar").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            
            // Filtrage des éléments en fonction de la saisie de l'utilisateur
            $(".searchable-element").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });

        // Fonction pour toggler l'affichage des sous-éléments
        function toggleElements(element) {
            var icon = element;
            var childElements = icon.closest('.element').next('.child-elements');

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

    #sortable-elements .list-group-item {
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
        background-color: #e0e0e0; /* Couleur de fond pour distinguer les sous-ensembles */
        border: 2px solid #b0b0b0; /* Bordure pour distinguer les sous-ensembles */
        border-radius: 5px; /* Coins arrondis */
        width: 100%; /* Largeur ajustée pour remplir le parent */
        box-sizing: border-box; /* Inclure la bordure et le padding dans la largeur */
        margin-bottom: 15px; /* Ajouter une marge inférieure pour éviter de coller aux champs en dessous */
    }

    .child-item {
        margin-bottom: 5px;
        width: 100%; /* Largeur uniforme pour tous les sous-éléments */
        padding: 5px 10px; /* Espacement uniforme */
        background-color: #f9f9f9; /* Couleur de fond légèrement différente */
        border: 1px solid #ddd; /* Bordure uniforme */
        border-radius: 5px; /* Coins arrondis */
        box-sizing: border-box; /* Inclure la bordure et le padding dans la largeur */
    }

    .section-link {
        color: #fff;
    }
</style>
@endsection
