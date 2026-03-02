@extends('layouts.app')

@section('content')
<div class="container my-4">
    <div class="card shadow border-0 rounded-4 p-4 p-md-5">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold text-orientech mb-0">
                Seance de {{ $seance->prenom }} {{ $seance->nom }}
            </h4>
            <button class="btn btn-outline-success rounded-pill" onclick="window.location.href='{{ url('/prof/gestion-seances') }}'">
                <- Retour
            </button>
        </div>

        <div class="mb-4">
            <label class="form-label fw-bold">Parcours de la seance</label>
            <input type="text" class="form-control"
                   value="{{ $parcours->count() ? $parcours[0]->nom : 'Aucun parcours' }}"
                   disabled>
        </div>

        @if ($parcours->count() > 0)
        <div class="mb-4">
            <form class="row g-3" method="post" action="{{ url('/prof/seance-detail/note') }}">
                @csrf
                <input type="hidden" name="id_seance" value="{{ (int)$seance->id_seance }}">
                <input type="hidden" name="id_parcours" value="{{ (int)$parcours[0]->numero_du_parcours }}">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Note manuelle</label>
                    <input type="text" name="note_manuelle" class="form-control"
                           inputmode="decimal"
                           placeholder="Ex: 13 ou 13,5"
                           title="Nombre uniquement (decimales autorisees)"
                           required>
                </div>
                <div class="col-12">
                    <button class="btn btn-orientech rounded-pill px-4" type="submit">
                        Enregistrer la note manuelle
                    </button>
                </div>
            </form>
        </div>
        @endif

        <h5 class="fw-bold mb-3">Resultats</h5>
        <div class="table-responsive">
            <table class="table table-bordered align-middle text-center">
                <thead class="table-secondary">
                    <tr>
                        <th>Parcours</th>
                        <th>Difficulte</th>
                        <th>Validees</th>
                        <th>Note manuelle</th>
                    </tr>
                </thead>
                <tbody>
                @if (count($resultats) === 0)
                    <tr><td colspan="4">Aucun resultat.</td></tr>
                @else
                    @foreach ($resultats as $r)
                        <tr>
                            <td>{{ $r->parcours_nom }}</td>
                            <td>{{ $r->niveau ?? '' }}</td>
                            <td>{{ (int)$r->nb_valides }}/{{ (int)$r->total_balises }}</td>
                            <td>{{ $r->note_finale }}</td>
                        </tr>
                    @endforeach
                @endif
                </tbody>
            </table>
        </div>

    </div>
</div>
@endsection
