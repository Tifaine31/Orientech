@extends('layouts.app')

@section('content')
<div class="container my-4">
    <div class="card shadow border-0 rounded-4 p-4 p-md-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold text-orientech mb-0">Gestion des élèves</h4>
            <button class="btn btn-outline-success rounded-pill" onclick="window.location.href='{{ url('/prof') }}'">
                ← Retour
            </button>
        </div>

        <div class="mb-4">
            <form class="row g-3" method="get">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Classe</label>
                    <select name="id_classe" class="form-select rounded-pill">
                        <option value="">Toutes</option>
                        @foreach ($classes as $c)
                            <option value="{{ (int)$c->id_classe }}" {{ $filterClasse === (int)$c->id_classe ? 'selected' : '' }}>
                                {{ $c->nom }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Nom</label>
                    <input type="text" name="nom" class="form-control rounded-pill" value="{{ $filterNom }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Prénom</label>
                    <input type="text" name="prenom" class="form-control rounded-pill" value="{{ $filterPrenom }}">
                </div>
                <div class="col-12">
                    <button class="btn btn-outline-success rounded-pill px-4" type="submit">Rechercher</button>
                </div>
            </form>
        </div>

        <div class="mb-4">
            <button class="btn btn-orientech rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalEleve">
                + Ajouter un élève
            </button>
        </div>

        <div class="mb-4">
            <form method="post" action="{{ url('/prof/gestion-eleves/delete-all') }}" onsubmit="return confirm('Supprimer tous les élèves ? Cette action est irréversible.');">
                @csrf
                <button class="btn btn-outline-danger rounded-pill px-4" type="submit">
                    Supprimer tous les élèves
                </button>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered align-middle text-center">
                <thead class="table-secondary">
                    <tr>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Classe</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($eleves as $e)
                    <tr>
                        <td>{{ $e->nom }}</td>
                        <td>{{ $e->prenom }}</td>
                        <td>
                            <form class="d-flex gap-2" method="post" action="{{ url('/prof/gestion-eleves/affecter') }}">
                                @csrf
                                <input type="hidden" name="id_utilisateur" value="{{ (int)$e->id_utilisateur }}">
                                <select name="id_classe" class="form-select form-select-sm">
                                    <option value="">Sans classe</option>
                                    @foreach ($classes as $c)
                                        <option value="{{ (int)$c->id_classe }}" {{ $e->classe == $c->nom ? 'selected' : '' }}>
                                            {{ $c->nom }}
                                        </option>
                                    @endforeach
                                </select>
                                <button class="btn btn-sm btn-outline-success rounded-pill" type="submit">
                                    Affecter
                                </button>
                            </form>
                        </td>
                        <td>
                            <form class="d-inline"
                                  method="post"
                                  action="{{ url('/prof/gestion-eleves/' . (int)$e->id_utilisateur . '/delete') }}"
                                  onsubmit="return confirm('Supprimer cet élève ?');">
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
    alert("Erreur: login déjà utilisé ou champs invalides.");
}
</script>

<div class="modal fade" id="modalEleve" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content rounded-4">
      <div class="modal-header">
        <h5 class="modal-title">Ajouter un élève</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
      </div>
      <div class="modal-body">
        <form class="row g-3" method="post" action="{{ url('/prof/gestion-eleves') }}">
          @csrf
          <div class="col-md-3">
            <label class="form-label fw-bold">Nom</label>
            <input type="text" name="nom" class="form-control rounded-pill" placeholder="Ex: Dupont" required>
          </div>
          <div class="col-md-3">
            <label class="form-label fw-bold">Prénom</label>
            <input type="text" name="prenom" class="form-control rounded-pill" placeholder="Ex: Marie" required>
          </div>
          <div class="col-md-2">
            <label class="form-label fw-bold">Login</label>
            <input type="text" name="login" class="form-control rounded-pill" placeholder="Ex: m.dupont" required>
          </div>
          <div class="col-md-2">
            <label class="form-label fw-bold">Mot de passe</label>
            <input type="password" name="password" class="form-control rounded-pill" placeholder="••••••••" required>
          </div>
          <div class="col-md-2">
            <label class="form-label fw-bold">Classe</label>
            <select name="id_classe" class="form-select rounded-pill">
              <option value="">Sans classe</option>
              @foreach ($classes as $c)
                <option value="{{ (int)$c->id_classe }}">{{ $c->nom }}</option>
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
@endsection
