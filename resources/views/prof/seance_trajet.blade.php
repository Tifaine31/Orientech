@extends('layouts.app')

@section('content')
<div class="container my-4">
    <div class="card shadow border-0 rounded-4 p-4 p-md-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold text-orientech mb-0">Trajet séance</h4>
            <button class="btn btn-outline-success rounded-pill" onclick="window.location.href='{{ url('/prof/historique-seances') }}'">
                ← Retour
            </button>
        </div>

        <p class="mb-1"><strong>Élève :</strong> {{ $seance->prenom }} {{ $seance->nom }}</p>
        <p class="mb-1"><strong>Date :</strong> {{ $seance->date_heure }}</p>
        <p class="mb-3"><strong>Boîtier :</strong> {{ $boitier ? ('#' . (int)$boitier->numero_boitier) : 'Non attribué' }}</p>

        <div id="map" style="height: 420px; border-radius: 16px;"></div>

        <h5 class="fw-bold mt-4 mb-3">Historique localisation</h5>
        <div class="table-responsive">
            <table class="table table-bordered align-middle text-center">
                <thead class="table-secondary">
                    <tr>
                        <th>Heure</th>
                        <th>Point</th>
                        <th>Latitude</th>
                        <th>Longitude</th>
                    </tr>
                </thead>
                <tbody>
                    @if (count($points) === 0)
                        <tr><td colspan="4">Aucune localisation.</td></tr>
                    @else
                        @foreach ($points as $index => $p)
                            <tr>
                                <td>{{ $p->created_at }}</td>
                                <td>{{ $p->numerodecarte ?? ('P' . ($index + 1)) }}</td>
                                <td>{{ $p->latitude }}</td>
                                <td>{{ $p->longitude }}</td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const points = @json($points);
const map = L.map('map');
const defaultCenter = [50.51942, 2.65176];
const defaultZoom = 17;

map.setView(defaultCenter, defaultZoom);

if (points.length > 0) {
  const latlngs = points
    .map(p => [parseFloat(p.latitude), parseFloat(p.longitude)])
    .filter(ll => Number.isFinite(ll[0]) && Number.isFinite(ll[1]));

  if (latlngs.length > 0) {
    L.polyline(latlngs, { color: '#4CAF50', weight: 4 }).addTo(map);
    latlngs.forEach((ll, idx) => {
      const label = points[idx].numerodecarte ? `Balise ${points[idx].numerodecarte}` : `Point ${idx + 1}`;
      L.marker(ll).addTo(map).bindPopup(label);
    });
    map.fitBounds(latlngs, { padding: [25, 25], maxZoom: 18 });
  }
}

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
  maxZoom: 19,
  attribution: '&copy; OpenStreetMap'
}).addTo(map);
</script>
@endsection
