@extends('layouts.app')

@section('content')
<div class="container my-4">
    <div class="card shadow border-0 rounded-4 p-4 p-md-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold text-orientech mb-0">Gestion des boitiers</h4>
            <button class="btn btn-outline-success rounded-pill" onclick="window.location.href='{{ url('/prof') }}'">
                <- Retour
            </button>
        </div>

        <div class="mb-4">
            <form class="row g-3" method="post" action="{{ url('/prof/gestion-boitiers') }}">
                @csrf
                <div class="col-md-4">
                    <label class="form-label fw-bold">Adresse MAC</label>
                    <input type="text" name="add_mac" class="form-control" placeholder="AA:BB:CC:DD:EE:FF">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Adresse reseau</label>
                    <input type="text" name="add_reseau" class="form-control" placeholder="192.168.1.10">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Etat</label>
                    <select name="etat" class="form-select">
                        <option value="disponible">Disponible</option>
                        <option value="occupe">Occupe</option>
                        <option value="hors_service">Hors service</option>
                    </select>
                </div>
                <div class="col-12">
                    <button class="btn btn-orientech rounded-pill px-4" type="submit">Ajouter le boitier</button>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered align-middle text-center">
                <thead class="table-secondary">
                    <tr>
                        <th>#</th>
                        <th>Adresse MAC</th>
                        <th>Adresse reseau</th>
                        <th>Etat</th>
                        <th>Modifier etat</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                @if (count($boitiers) === 0)
                    <tr><td colspan="6">Aucun boitier.</td></tr>
                @else
                    @foreach ($boitiers as $b)
                        <tr>
                            <td>{{ (int)$b->numero }}</td>
                            <td>{{ $b->add_mac ?? '' }}</td>
                            <td>{{ $b->add_reseau ?? '' }}</td>
                            <td>{{ $b->etat ?? '' }}</td>
                            <td>
                                <form class="d-flex gap-2 justify-content-center"
                                      method="post"
                                      action="{{ url('/prof/gestion-boitiers/' . (int)$b->numero . '/etat') }}">
                                    @csrf
                                    <select name="etat" class="form-select form-select-sm w-auto">
                                        @php $etatCourant = (string)($b->etat ?? ''); @endphp
                                        <option value="disponible" @selected($etatCourant === 'disponible')>Disponible</option>
                                        <option value="occupe" @selected($etatCourant === 'occupe')>Occupe</option>
                                        <option value="hors_service" @selected($etatCourant === 'hors_service')>Hors service</option>
                                    </select>
                                    <button class="btn btn-sm btn-outline-success rounded-pill" type="submit">
                                        Modifier
                                    </button>
                                </form>
                            </td>
                            <td>
                                <form class="d-inline"
                                      method="post"
                                      action="{{ url('/prof/gestion-boitiers/' . (int)$b->numero . '/delete') }}"
                                      onsubmit="return confirm('Supprimer ce boitier ?');">
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

<script>
const updated = "{{ session('updated') }}";
const error = "{{ session('error') }}";
if (updated === "etat") {
    alert("Etat du boitier mis a jour.");
}
if (error === "etat") {
    alert("Erreur de mise a jour de l'etat du boitier.");
}
</script>
@endsection
