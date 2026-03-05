@extends('layouts.app')

@section('content')
<div class="container my-4">
    <div class="card shadow border-0 rounded-4 p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold text-orientech mb-0">Historique</h4>
            <button class="btn btn-outline-success rounded-pill" onclick="window.location.href='{{ url('/eleve') }}'">
                <- Retour
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered align-middle text-center">
                <thead class="table-secondary">
                    <tr>
                        <th style="width: 20%">Date</th>
                        <th style="width: 45%">Resultats</th>
                        <th style="width: 15%">Note</th>
                        <th style="width: 20%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @if (count($results) === 0)
                        <tr><td colspan="4">Aucun resultat.</td></tr>
                    @else
                        @foreach ($results as $r)
                            <tr>
                                <td>{{ \Illuminate\Support\Str::limit($r->date_heure, 10, '') }}</td>
                                <td>
                                    @for ($i = 0; $i < (int)$r->nb_valides; $i++)
                                        <span class="ok">OK</span>
                                    @endfor
                                    @for ($i = 0; $i < (int)$r->nb_invalides; $i++)
                                        <span class="ko">KO</span>
                                    @endfor
                                </td>
                                <td><strong>{{ $r->note_finale }}/20</strong></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-success rounded-pill btn-tracer"
                                            data-seance-id="{{ (int)$r->id_seance }}"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalTrajetEleve">
                                        Tracer
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTrajetEleve" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content rounded-4">
            <div class="modal-header">
                <h5 class="modal-title">Tracer de la seance</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
                <p id="trajetInfo" class="mb-3">Chargement...</p>
                <div id="mapEleveTrajet" style="height: 420px; border-radius: 16px; border: 1px solid #ddd;"></div>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
let eleveMap = null;
let eleveLayerGroup = null;
const defaultCenter = [50.51942, 2.65176];
const defaultZoom = 17;

function ensureEleveMap() {
    if (!eleveMap) {
        eleveMap = L.map('mapEleveTrajet');
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap'
        }).addTo(eleveMap);
        eleveLayerGroup = L.layerGroup().addTo(eleveMap);
    }
    setTimeout(() => eleveMap.invalidateSize(), 150);
}

function drawEleveTrajet(points) {
    eleveLayerGroup.clearLayers();
    eleveMap.setView(defaultCenter, defaultZoom);

    if (!points || points.length === 0) {
        return;
    }

    const latlngs = points.map(p => [parseFloat(p.lat), parseFloat(p.lng)])
        .filter(ll => Number.isFinite(ll[0]) && Number.isFinite(ll[1]));

    if (latlngs.length === 0) {
        return;
    }

    L.polyline(latlngs, { color: '#4CAF50', weight: 4 }).addTo(eleveLayerGroup);
    latlngs.forEach((ll, idx) => {
        const time = points[idx] && points[idx].date_time ? String(points[idx].date_time) : '';
        L.marker(ll).addTo(eleveLayerGroup).bindPopup('Point ' + (idx + 1) + (time ? ('<br>' + time) : ''));
    });
}

document.querySelectorAll('.btn-tracer').forEach(btn => {
    btn.addEventListener('click', async function () {
        const idSeance = this.getAttribute('data-seance-id');
        const info = document.getElementById('trajetInfo');
        ensureEleveMap();
        info.textContent = 'Chargement du trace...';
        drawEleveTrajet([]);

        try {
            const res = await fetch('{{ url('/eleve/historique/trajet') }}?id=' + encodeURIComponent(idSeance), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();
            if (!res.ok || !data.ok) {
                info.textContent = 'Erreur de chargement du trace.';
                return;
            }
            info.textContent = 'Seance #' + data.seance.id + ' - ' + (data.seance.date_debut || '');
            drawEleveTrajet(data.points || []);
        } catch (e) {
            info.textContent = 'Erreur reseau.';
        }
    });
});
</script>
@endsection
