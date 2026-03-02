@extends('layouts.app')

@section('content')
<div class="container my-4">
    <div class="card shadow border-0 rounded-4 p-4 p-md-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold text-orientech mb-0">Gestion des parcours</h4>
            <button class="btn btn-outline-success rounded-pill" onclick="window.location.href='{{ url('/prof') }}'">
                ← Retour
            </button>
        </div>

        <div class="mb-4">
            <form class="row g-3" method="post" action="{{ url('/prof/gestion-parcours') }}">
                @csrf
                <div class="col-md-4">
                    <label class="form-label fw-bold">Nom</label>
                    <input type="text" name="nom" class="form-control rounded-pill" placeholder="Ex: Orientation A" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Niveau</label>
                    <input type="text" name="niveau" class="form-control rounded-pill" placeholder="Ex: Débutant">
                </div>
                <div class="col-12">
                    <button class="btn btn-orientech rounded-pill px-4" type="submit">
                        + Créer un parcours
                    </button>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered align-middle text-center">
                <thead class="table-secondary">
                    <tr>
                        <th>Nom du parcours</th>
                        <th>Niveau</th>
                        <th>Nombre de balises</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($parcours as $p)
                        <tr>
                            <td>{{ $p->nom ?? '' }}</td>
                            <td>{{ $p->niveau ?? '' }}</td>
                            <td>{{ (int)($p->nb_balises ?? 0) }}</td>
                            <td>
                                <a class="btn btn-sm btn-outline-success rounded-pill me-2"
                                   href="{{ url('/prof/modifier-parcours') }}?id={{ (int)$p->numero_du_parcours }}">
                                    Gérer
                                </a>
                                <form class="d-inline"
                                      method="post"
                                      action="{{ url('/prof/gestion-parcours/' . (int)$p->numero_du_parcours . '/delete') }}"
                                      onsubmit="return confirm('Supprimer ce parcours ?');">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-danger rounded-pill" type="submit">
                                        Supprimer
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
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
