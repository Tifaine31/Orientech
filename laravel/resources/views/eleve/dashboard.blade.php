@extends('layouts.app')

@section('content')
<div class="container my-4">
    <div class="row g-4">
        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100 rounded-4">
                <div class="card-body d-flex align-items-center gap-4">
                    <div class="user-icon">
                        @if (!empty($eleve->photo))
                            <img src="{{ $eleve->photo }}" alt="Photo profil"
                                 style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
                        @else
                            <i class="bi bi-person-fill"></i>
                        @endif
                    </div>
                    <div>
                        <p><strong>Nom :</strong> {{ $eleve->nom ?? '' }}</p>
                        <p><strong>Prénom :</strong> {{ $eleve->prenom ?? '' }}</p>
                        <p><strong>Classe :</strong> {{ $classe ?? 'Non assignée' }}</p>
                        <a class="btn btn-sm btn-outline-success rounded-pill mt-2"
                           href="{{ url('/eleve/profil') }}">
                            Modifier mon profil
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100 rounded-4">
                <div class="card-body">
                    <h5 class="text-center fw-bold mb-3">Dernière course</h5>

                    <table class="table table-bordered text-center align-middle">
                        <thead class="table-secondary">
                            <tr>
                                <th>Date</th>
                                <th>Résultats</th>
                                <th>Note</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (!$lastResult)
                                <tr><td colspan="3">Aucun résultat.</td></tr>
                            @else
                                <tr>
                                    <td>{{ \Illuminate\Support\Str::limit($lastResult->date_heure, 10, '') }}</td>
                                    <td>
                                        @for ($i = 0; $i < (int)$lastResult->nb_valides; $i++)
                                            <span class="ok">✔</span>
                                        @endfor
                                        @for ($i = 0; $i < (int)$lastResult->nb_invalides; $i++)
                                            <span class="ko">✖</span>
                                        @endfor
                                    </td>
                                    <td><strong>{{ $lastResult->note_finale }}/20</strong></td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-4 mt-5">
        <div class="card-body">
            <h5 class="text-center fw-bold mb-4">Historique</h5>

            <table class="table table-bordered text-center align-middle">
                <thead class="table-secondary">
                    <tr>
                        <th>Date</th>
                        <th>Résultats</th>
                        <th>Note</th>
                    </tr>
                </thead>
                <tbody>
                    @if (!$lastResult)
                        <tr><td colspan="3">Aucun résultat.</td></tr>
                    @else
                        <tr>
                            <td>{{ \Illuminate\Support\Str::limit($lastResult->date_heure, 10, '') }}</td>
                            <td>
                                @for ($i = 0; $i < (int)$lastResult->nb_valides; $i++)
                                    <span class="ok">✔</span>
                                @endfor
                                @for ($i = 0; $i < (int)$lastResult->nb_invalides; $i++)
                                    <span class="ko">✖</span>
                                @endfor
                            </td>
                            <td><strong>{{ $lastResult->note_finale }}/20</strong></td>
                        </tr>
                    @endif
                </tbody>
            </table>

            <div class="text-center mt-4">
                <button class="btn btn-orientech rounded-pill px-5"
                        onclick="window.location.href='{{ url('/eleve/historique') }}'">
                    Suite
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
