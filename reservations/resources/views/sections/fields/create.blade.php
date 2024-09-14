  @extends('layouts.app')

  @section('content')
      <h1>Ajouter un Champ à la Section: {{ $section->name }}</h1>

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

      <form action="{{ route('section.fields.store', $section) }}" method="POST">
          @csrf
          <div class="form-group">
              <label for="name">Nom du Champ</label>
              <input type="text" name="name" id="name" class="form-control" required value="{{ old('name') }}">
              @if ($errors->has('name'))
                  <div class="text-danger mt-2">
                      {{ $errors->first('name') }}
                  </div>
              @endif
          </div>

          <div class="form-group">
              <label for="type">Type du Champ</label>
              <select name="type" id="type" class="form-control" required>
                  <option value="text" {{ old('type') == 'text' ? 'selected' : '' }}>Texte</option>
                  <option value="url" {{ old('type') == 'url' ? 'selected' : '' }}>URL</option>
                  <option value="photo" {{ old('type') == 'photo' ? 'selected' : '' }}>Photo</option>
                  <option value="numerique" {{ old('type') == 'numerique' ? 'selected' : '' }}>Numérique</option>
                  <option value="signature" {{ old('type') == 'signature' ? 'selected' : '' }}>Signature</option>
                  <option value="date" {{ old('type') == 'date' ? 'selected' : '' }}>Date</option>
                  <option value="telephone" {{ old('type') == 'telephone' ? 'selected' : '' }}>Téléphone</option>
              </select>
              @if ($errors->has('type'))
                  <div class="text-danger mt-2">
                      {{ $errors->first('type') }}
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

          <button type="submit" class="btn btn-success">Ajouter le Champ</button>
      </form>
  @endsection
