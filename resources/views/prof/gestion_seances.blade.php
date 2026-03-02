@extends('layouts.app')

@section('content')
<div class="container my-4">
<div class="card shadow border-0 rounded-4 p-4 p-md-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-orientech mb-0">Gestion des séances</h4>
        <button class="btn btn-outline-success rounded-pill" onclick="window.location.href='{{ url('/prof') }}'">
            ← Retour
        </button>
    </div>

    <div class="mb-4">
        <form class="row g-3" method="get">
            <div class="col-md-4">
                <label class="form-label fw-bold">Date</label>
                <input type="date" name="date" class="form-control" value="{{ $dateFilter }}">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Nom</label>
                <input type="text" name="nom" class="form-control" value="{{ $filterNom }}">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Prénom</label>
                <input type="text" name="prenom" class="form-control" value="{{ $filterPrenom }}">
            </div>
            <div class="col-12">
                <button class="btn btn-outline-success rounded-pill px-4" type="submit">Rechercher</button>
            </div>
        </form>
    </div>

    <div class="mb-4">
        <button class="btn btn-orientech rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalSeance">
            + Créer une séance
        </button>
    </div>

    <h5 class="fw-bold mb-3">Séances</h5>
    <div class="table-responsive">
        <table class="table table-bordered align-middle text-center">
            <thead class="table-secondary">
                <tr>
                    <th>Date & heure</th>
                    <th>Élève</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @if (count($seances) === 0)
                    <tr><td colspan="3">Aucune séance.</td></tr>
                @else
                    @foreach ($seances as $s)
                        <tr>
                            <td>{{ $s->date_heure }}</td>
                            <td>{{ $s->prenom }} {{ $s->nom }} ({{ $s->login }})</td>
                            <td>
                                <a class="btn btn-sm btn-outline-success rounded-pill me-2"
                                   href="{{ url('/prof/seance-detail') }}?id={{ (int)$s->id_seance }}">
                                    Gérer
                                </a>
                                <form class="d-inline"
                                      method="post"
                                      action="{{ url('/prof/gestion-seances/' . (int)$s->id_seance . '/delete') }}"
                                      onsubmit="return confirm('Supprimer cette séance ?');">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-danger rounded-pill" type="submit">
                                        Supprimer
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

<div class="modal fade" id="modalSeance" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content rounded-4">
      <div class="modal-header">
        <h5 class="modal-title">Créer une séance (par élève)</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
      </div>
      <div class="modal-body">
        <form class="row g-3" method="post" action="{{ url('/prof/gestion-seances') }}">
          @csrf
          <div class="col-md-6">
            <label class="form-label fw-bold">Élève</label>
            <select name="id_eleve" class="form-select" required>
              <option value="">Sélectionner un élève</option>
              @foreach ($eleves as $e)
                <option value="{{ (int)$e->id_utilisateur }}">
                  {{ $e->prenom }} {{ $e->nom }} ({{ $e->login }})
                </option>
              @endforeach
            </select>
          </div>

          <div class="col-md-6">
            <label class="form-label fw-bold">Date de la séance</label>
            <input type="date" name="date" class="form-control" required>
          </div>

          <div class="col-md-6">
            <label class="form-label fw-bold">Parcours</label>
            <select name="id_parcours" class="form-select" required>
              <option value="">Sélectionner un parcours</option>
              @foreach ($parcours as $p)
                <option value="{{ (int)$p->numero_du_parcours }}">
                  {{ $p->nom }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="col-md-6">
            <label class="form-label fw-bold">Boîtier</label>
            <select name="numero_boitier" class="form-select" required>
              <option value="">Sélectionner un boîtier</option>
              @foreach ($boitiers as $b)
                <option value="{{ (int)$b->numero }}">
                  Boîtier #{{ (int)$b->numero }}@if ($b->add_mac) - {{ $b->add_mac }}@endif
                </option>
              @endforeach
            </select>
          </div>

          <div class="col-12 d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Annuler</button>
            <button class="btn btn-orientech rounded-pill px-4" type="submit">Créer</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
const error = "{{ session('error') }}";
if (error === "1") {
    alert("Erreur: champs invalides.");
}
</script>
@endsection
