@extends('layouts.app')

@section('content')
<style>
    .historique-table thead th {
        background: #c9cdca;
        border-color: #b7b7b7;
        font-weight: 600;
        white-space: nowrap;
    }
    .historique-table td,
    .historique-table th {
        vertical-align: middle;
    }
    .historique-table-wrapper {
        border-radius: 14px;
        overflow: hidden;
        border: 1px solid #b7b7b7;
    }
</style>

<div class="container my-4">
    <div class="card shadow border-0 rounded-4 p-4 p-md-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold text-orientech mb-0">Historique des seances</h4>
            <button class="btn btn-outline-success rounded-pill" onclick="window.location.href='{{ url('/prof') }}'">
                <- Retour
            </button>
        </div>

        <div class="mb-4">
            <form class="row g-3" method="get">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Eleve (nom / prenom / login)</label>
                    <input type="text" name="eleve" class="form-control" value="{{ $filterEleve }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Classe</label>
                    <select name="classe" class="form-select">
                        <option value="">Toutes</option>
                        @foreach ($classes as $c)
                            <option value="{{ (int)$c->id_classe }}" {{ $filterClasse === (int)$c->id_classe ? 'selected' : '' }}>
                                {{ $c->nom }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Date</label>
                    <input type="date" name="date" class="form-control" value="{{ $filterDate }}">
                </div>
                <div class="col-12 d-flex flex-wrap gap-2">
                    <button class="btn btn-outline-success rounded-pill px-4" type="submit">Rechercher</button>
                    <a class="btn btn-success rounded-pill px-4"
                       href="{{ url('/prof/historique-seances/export') }}?eleve={{ urlencode($filterEleve) }}&classe={{ (int)$filterClasse }}&date={{ urlencode($filterDate) }}">
                        Export Excel
                    </a>
                </div>
            </form>
        </div>

        <div class="table-responsive historique-table-wrapper">
            <table class="table table-bordered align-middle text-center mb-0 historique-table">
                <thead class="table-secondary">
                    <tr>
                        <th>Date & heure</th>
                        <th>Eleve</th>
                        <th>Classe</th>
                        <th>Parcours</th>
                        <th>Validees</th>
                        <th>Note finale</th>
                        <th>Detail</th>
                    </tr>
                </thead>
                <tbody>
                    @if (count($results) === 0)
                        <tr><td colspan="7">Aucun resultat.</td></tr>
                    @else
                        @foreach ($results as $r)
                            @php
                                $total = (int)($r->total_balises ?? 0);
                                $valides = (int)$r->nb_valides;
                                $invalides = (int)$r->nb_invalides;
                                $denom = $total > 0 ? $total : ($valides + $invalides);
                            @endphp
                            <tr>
                                <td>{{ $r->date_heure }}</td>
                                <td>{{ $r->prenom }} {{ $r->nom }} ({{ $r->login }})</td>
                                <td>{{ $r->classe_nom ?? '' }}</td>
                                <td>{{ $r->parcours_nom }}</td>
                                <td>{{ $valides }}/{{ $denom > 0 ? $denom : '-' }}</td>
                                <td>{{ $r->note_finale }}</td>
                                <td>
                                    <a class="btn btn-sm btn-outline-success rounded-pill"
                                       href="{{ url('/prof/seance-trajet') }}?id={{ (int)$r->id_seance }}">
                                        Detail
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
@endsection
