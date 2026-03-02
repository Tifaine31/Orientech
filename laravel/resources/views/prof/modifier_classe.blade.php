@extends('layouts.app')

@section('content')
<div class="container my-4">
    <div class="card shadow border-0 rounded-4 p-4 p-md-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold text-orientech mb-0">Classe : {{ $classe->nom }}</h4>
            <button class="btn btn-outline-success rounded-pill" onclick="window.location.href='{{ url('/prof/gestion-classes') }}'">
                ← Retour
            </button>
        </div>

        <form class="row g-3 mb-4" method="post" action="{{ url('/prof/modifier-classe/rename') }}">
            @csrf
            <input type="hidden" name="id_classe" value="{{ (int)$classe->id_classe }}">
            <div class="col-md-6">
                <label class="form-label fw-bold">Nom de la classe</label>
                <input type="text" name="nom" class="form-control rounded-pill" value="{{ $classe->nom }}" required>
            </div>
            <div class="col-12">
                <button class="btn btn-orientech rounded-pill px-4" type="submit">Renommer</button>
            </div>
        </form>

        <div class="mb-4">
            <form class="d-flex gap-2" method="post" action="{{ url('/prof/modifier-classe/add-eleve') }}">
                @csrf
                <input type="hidden" name="id_classe" value="{{ (int)$classe->id_classe }}">
                <select name="id_utilisateur" class="form-select rounded-pill" required>
                    <option value="">Sélectionner un élève</option>
                    @foreach ($elevesDisponibles as $e)
                        <option value="{{ (int)$e->id_utilisateur }}">
                            {{ $e->prenom }} {{ $e->nom }} ({{ $e->login }})
                        </option>
                    @endforeach
                </select>
                <button class="btn btn-orientech rounded-pill px-4" type="submit">
                    + Ajouter l’élève
                </button>
            </form>
        </div>

        <div class="mb-4">
            <form class="d-flex gap-2" method="post" action="{{ url('/prof/modifier-classe/import-csv') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id_classe" value="{{ (int)$classe->id_classe }}">
                <input type="file" name="csv_file" class="form-control rounded-pill" accept=".csv" required>
                <button class="btn btn-orientech rounded-pill px-4" type="submit">
                    Importer CSV
                </button>
            </form>
            <small class="text-muted d-block mt-2">
                Format attendu (CSV) : la 1ère colonne contient "NOM Prénom". Le login est généré "nom.prenom" et le mot de passe par défaut est "1234".
            </small>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered align-middle text-center">
                <thead class="table-secondary">
                    <tr>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Login</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @if (count($elevesClasse) === 0)
                        <tr><td colspan="4">Aucun élève dans cette classe.</td></tr>
                    @else
                        @foreach ($elevesClasse as $e)
                            <tr>
                                <td>{{ $e->nom }}</td>
                                <td>{{ $e->prenom }}</td>
                                <td>{{ $e->login }}</td>
                                <td>
                                    <form class="d-inline"
                                          method="post"
                                          action="{{ url('/prof/modifier-classe/remove-eleve') }}"
                                          onsubmit="return confirm('Retirer cet élève de la classe ?');">
                                        @csrf
                                        <input type="hidden" name="id_classe" value="{{ (int)$classe->id_classe }}">
                                        <input type="hidden" name="id_utilisateur" value="{{ (int)$e->id_utilisateur }}">
                                        <button class="btn btn-sm btn-outline-danger rounded-pill" type="submit">
                                            Retirer
                                        </button>
                                    </form>
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
    alert("Erreur: nom de classe déjà utilisé ou invalide.");
}
</script>
@endsection
