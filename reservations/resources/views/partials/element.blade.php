@php
    // Détecter si $element est un tableau ou un objet et accéder aux propriétés en conséquence
    $id = is_array($element) ? $element['id'] : $element->id;
    $type = is_array($element) ? $element['type'] : $element->type;
    $name = is_array($element) ? $element['name'] : $element->name;

    // Vérifier si la clé "children" existe et est non vide
    if (is_array($element)) {
        $children = isset($element['children']) ? $element['children'] : [];
    } else {
        $children = property_exists($element, 'children') ? $element->children : [];
    }
@endphp

<div class="list-group-item section-item d-flex justify-content-between align-items-center p-1 element" data-id="{{ $id }}" data-type="{{ $type }}" data-order="{{ is_array($element) ? $element['order'] : $element->order }}">
    @if ($type === 'section')
        <!-- Affichage des sections avec flèche -->
        <div class="d-flex align-items-center">
            <span class="toggle-icon mr-2" style="cursor: pointer;">&#9654;</span> <!-- Flèche pour toggler -->
            <a href="{{ route('sections.show', $id) }}" class="section-link">{{ $name }}</a>
        </div>
    @elseif ($type === 'field')
        <strong>{{ $name }}</strong> - Champ
    @endif

    <!-- Icône de suppression -->
    @if ($type === 'field')
        @if (isset($form)) <!-- Si nous sommes dans le contexte d'un formulaire -->
            <form action="{{ route('fields.destroy', ['form' => $form->id, 'field' => $id]) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </form>
        @elseif (isset($section)) <!-- Si nous sommes dans le contexte d'une section -->
            <form action="{{ route('section.fields.destroy', ['section' => $section->id, 'field' => $id]) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </form>
        @endif
    @elseif ($type === 'section')
        <form action="{{ route('sections.destroy', $id) }}" method="POST" class="d-inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger btn-sm">
                <i class="fas fa-trash-alt"></i>
            </button>
        </form>
    @endif
</div>

<!-- Récursivement inclure les sous-éléments -->
@if (!empty($children) && count($children) > 0)
    <div class="child-elements d-none mt-2">
        @foreach ($children as $child)
            @include('partials.element', ['element' => $child, 'form' => $form ?? null, 'section' => $section ?? null]) <!-- Inclusion récursive avec passage des variables -->
        @endforeach
    </div>
@endif
