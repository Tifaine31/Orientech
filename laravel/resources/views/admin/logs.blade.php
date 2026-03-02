@extends('layouts.app')

@section('content')
<div class="container my-4">
    <div class="card shadow border-0 rounded-4 p-4 p-md-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold text-orientech mb-0">Logs admin</h4>
            <a href="{{ url('/admin') }}" class="btn btn-outline-success rounded-pill">← Retour</a>
        </div>

        <h5 class="fw-bold mb-3">Logs boitiers</h5>
        <div class="table-responsive mb-4">
            <table class="table table-bordered align-middle text-center">
                <thead class="table-secondary">
                    <tr>
                        <th>Date</th>
                        <th>Boitier</th>
                        <th>Ancien etat</th>
                        <th>Nouvel etat</th>
                        <th>Modifie par</th>
                    </tr>
                </thead>
                <tbody>
                    @if (count($logsBoitiers ?? []) === 0)
                        <tr><td colspan="5">Aucun log boitier.</td></tr>
                    @else
                        @foreach ($logsBoitiers as $log)
                            <tr>
                                <td>{{ $log->created_at }}</td>
                                <td>#{{ (int)$log->boitier_id }} - {{ $log->boitier_mac }}</td>
                                <td>{{ $log->ancien_etat ?? '-' }}</td>
                                <td>{{ $log->nouvel_etat }}</td>
                                <td>{{ trim(($log->user_prenom ?? '') . ' ' . ($log->user_nom ?? '')) }} ({{ $log->user_login ?? '-' }})</td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>

        <h5 class="fw-bold mb-3">Logs application</h5>
        <form class="row g-3 mb-3" method="get" action="{{ url('/admin/logs') }}">
            <div class="col-md-3">
                <label class="form-label fw-bold">Source</label>
                <select name="source" class="form-select">
                    <option value="">Toutes</option>
                    @foreach (($logSources ?? []) as $source)
                        <option value="{{ $source }}" @selected(($filterSource ?? '') === (string)$source)>{{ $source }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold">Niveau</label>
                <select name="level" class="form-select">
                    <option value="">Tous</option>
                    @foreach (($logLevels ?? []) as $level)
                        <option value="{{ $level }}" @selected(($filterLevel ?? '') === (string)$level)>{{ $level }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold">Date</label>
                <input type="date" name="date" class="form-control" value="{{ $filterDate ?? '' }}">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">Utilisateur</label>
                <select name="user" class="form-select">
                    <option value="">Tous</option>
                    @foreach (($logUsers ?? []) as $u)
                        <option value="{{ (int)$u->id }}" @selected((int)($filterUser ?? 0) === (int)$u->id)>
                            {{ $u->prenom }} {{ $u->nom }} ({{ $u->login }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-outline-success rounded-pill w-100">Filtrer</button>
                <a href="{{ url('/admin/logs') }}" class="btn btn-outline-secondary rounded-pill w-100">Reset</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered align-middle text-center">
                <thead class="table-secondary">
                    <tr>
                        <th>Date</th>
                        <th>Source</th>
                        <th>Niveau</th>
                        <th>Type</th>
                        <th>Message</th>
                        <th>Utilisateur</th>
                        <th>Path</th>
                        <th>Status</th>
                        <th>IP</th>
                    </tr>
                </thead>
                <tbody>
                    @if (count($appLogs ?? []) === 0)
                        <tr><td colspan="9">Aucun log application.</td></tr>
                    @else
                        @foreach ($appLogs as $log)
                            <tr>
                                <td>{{ $log->created_at }}</td>
                                <td>{{ $log->source }}</td>
                                <td>{{ $log->level }}</td>
                                <td>{{ $log->event_type ?? '-' }}</td>
                                <td class="text-start">{{ $log->message }}</td>
                                <td>{{ $log->id_utilisateur ?? '-' }}</td>
                                <td>{{ $log->path ?? '-' }}</td>
                                <td>{{ $log->status_code ?? '-' }}</td>
                                <td>{{ $log->ip_address ?? '-' }}</td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
