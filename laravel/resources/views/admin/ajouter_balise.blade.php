@extends('layouts.app')

@section('content')
<div class="container my-4">
    <div class="card shadow border-0 rounded-4 p-4 p-md-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold text-orientech mb-0">Ajout balise</h4>
            <button class="btn btn-outline-success rounded-pill" onclick="window.location.href='{{ url('/admin') }}'">
                <- Retour
            </button>
        </div>

        <form method="post" action="{{ url('/admin/balises/ajouter') }}">
            @csrf
            <div class="row g-4">
                <div class="col-md-8">
                    <label class="form-label fw-bold">Boitier</label>
                    <select name="boitier_id" class="form-select rounded-pill" required>
                        <option value="">-- Choisir un boitier --</option>
                        @foreach($boitiers as $boitier)
                            <option value="{{ $boitier->id }}">
                                #{{ $boitier->id }} - {{ $boitier->mac }} ({{ $boitier->etat ?? 'inconnu' }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-orientech rounded-pill px-5 w-100">
                        Commencer
                    </button>
                </div>
            </div>
        </form>

        <div class="mt-4 p-3 rounded-3 bg-light">
            <div class="fw-bold mb-2">Etat acquisition API</div>
            @if(!empty($scanContext))
                <div>Boitier actif: #{{ $scanContext['boitier_id'] }} ({{ $scanContext['boitier_mac'] }})</div>
                <div>Demarrage: {{ $scanContext['started_at'] }}</div>
                <form method="post" action="{{ url('/admin/balises/ajouter/stop') }}" class="mt-3">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger rounded-pill px-4">
                        Arreter
                    </button>
                </form>
            @else
                <div>Aucune acquisition en cours. Selectionne un boitier puis clique sur Commencer.</div>
            @endif
        </div>
    </div>
</div>

<script>
const success = "{{ session('success') }}";
const error = "{{ session('error') }}";
const stopped = "{{ session('stopped') }}";
if (success === "1") {
    alert("Acquisition lancee. L'API peut maintenant gerer la creation des balises.");
}
if (error === "1") {
    alert("Erreur: boitier invalide.");
}
if (stopped === "1") {
    alert("Acquisition arretee.");
}
</script>
@endsection
