@extends('layouts.app')

@section('content')
<div class="container my-4">
    <div class="card shadow border-0 rounded-4 p-4 p-md-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold text-orientech mb-0">Parcours : {{ $parcours->nom }}</h4>
            <button class="btn btn-outline-success rounded-pill" onclick="window.location.href='{{ url('/prof/gestion-parcours') }}'">
                ← Retour
            </button>
        </div>

        <form class="row g-3 mb-4" method="post" action="{{ url('/prof/modifier-parcours') }}">
            @csrf
            <input type="hidden" name="id" value="{{ (int)$parcours->numero_du_parcours }}">
            <div class="col-md-6">
                <label class="form-label fw-bold">Nom</label>
                <input type="text" name="nom" class="form-control rounded-pill" value="{{ $parcours->nom }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">Niveau</label>
                <input type="text" name="niveau" class="form-control rounded-pill" value="{{ $parcours->niveau ?? '' }}">
            </div>
            <div class="col-12">
                <button class="btn btn-orientech rounded-pill px-4" type="submit">
                    Enregistrer
                </button>
            </div>
        </form>

        <div class="mb-4">
            <form class="d-flex gap-2" method="post" action="{{ url('/prof/modifier-parcours/add-balise') }}">
                @csrf
                <input type="hidden" name="id" value="{{ (int)$parcours->numero_du_parcours }}">
                <select name="numerodecarte" class="form-select rounded-pill" required>
                    <option value="">Sélectionner une balise</option>
                    @foreach ($balises as $b)
                        <option value="{{ (int)$b->numerodecarte }}">
                            {{ ($b->tagRFID ?? 'Balise') . ' (#' . (int)$b->numerodecarte . ')' }}
                        </option>
                    @endforeach
                </select>
                <button class="btn btn-orientech rounded-pill px-4" type="submit">
                    + Ajouter la balise
                </button>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered align-middle text-center">
                <thead class="table-secondary">
                    <tr>
                        <th>Ordre</th>
                        <th>Balise</th>
                        <th>Tag</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @if (count($balisesParcours) === 0)
                        <tr><td colspan="4">Aucune balise associée.</td></tr>
                    @else
                        @foreach ($balisesParcours as $b)
                            <tr>
                                <td>{{ (int)$b->ordre }}</td>
                                <td>{{ (int)$b->numerodecarte }}</td>
                                <td>{{ $b->tagRFID ?? '' }}</td>
                                <td>
                                    <a class="btn btn-sm btn-outline-danger rounded-pill"
                                       href="{{ url('/prof/modifier-parcours/remove-balise') }}?id={{ (int)$parcours->numero_du_parcours }}&numerodecarte={{ (int)$b->numerodecarte }}"
                                       onclick="return confirm('Retirer cette balise ?');">
                                        Retirer
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
const error = "{{ session('error') }}";
if (error === "1") {
    alert("Erreur: nom déjà utilisé ou champs invalides.");
}
</script>
@endsection
