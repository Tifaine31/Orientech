<?php
require_once "../includes/auth.php";
require_once "../includes/db.php";

protectPage("prof");

$id = (int)($_GET["id"] ?? 0);
if ($id <= 0) {
    header("Location: historique_seances.php");
    exit;
}

// Seance + eleve
$stmt = $pdo->prepare(
    "SELECT s.id_seance, s.date_heure, u.id_utilisateur, u.nom, u.prenom, u.login
     FROM seance s
     JOIN utilisateur u ON u.id_utilisateur = s.id_eleve
     WHERE s.id_seance = ?"
);
$stmt->execute([$id]);
$seance = $stmt->fetch();
if (!$seance) {
    header("Location: historique_seances.php");
    exit;
}

// Boitier assigned
$stmtB = $pdo->prepare(
    "SELECT numero_boitier, assigned_at, ended_at
     FROM seance_boitier
     WHERE id_seance = ? LIMIT 1"
);
$stmtB->execute([$id]);
$boitier = $stmtB->fetch();

$points = [];
if ($boitier) {
    $params = [$boitier["numero_boitier"]];
    $where = "numero_boitier = ?";
    if (!empty($boitier["assigned_at"])) {
        $where .= " AND created_at >= ?";
        $params[] = $boitier["assigned_at"];
    }
    if (!empty($boitier["ended_at"])) {
        $where .= " AND created_at <= ?";
        $params[] = $boitier["ended_at"];
    }

    $stmtP = $pdo->prepare(
        "SELECT id_loc, latitude, longitude, numerodecarte, created_at
         FROM localisation
         WHERE $where
         ORDER BY created_at ASC"
    );
    $stmtP->execute($params);
    $points = $stmtP->fetchAll();
}

$pageTitle = "Trajet séance";
$roleLabel = "Professeur";

include "../includes/header.php";
?>

<div class="container my-4">
    <div class="card shadow border-0 rounded-4 p-4 p-md-5">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold text-orientech mb-0">Trajet séance</h4>
            <button class="btn btn-outline-success rounded-pill" onclick="window.location.href='historique_seances.php'">
                ← Retour
            </button>
        </div>

        <p class="mb-1"><strong>Élève :</strong> <?= htmlspecialchars($seance["prenom"] . " " . $seance["nom"]) ?></p>
        <p class="mb-1"><strong>Date :</strong> <?= htmlspecialchars($seance["date_heure"]) ?></p>
        <p class="mb-3"><strong>Boîtier :</strong> <?= $boitier ? ("#" . (int)$boitier["numero_boitier"]) : "Non attribué" ?></p>

        <div id="map" style="height: 420px; border-radius: 16px;"></div>

        <h5 class="fw-bold mt-4 mb-3">Historique balises</h5>
        <div class="table-responsive">
            <table class="table table-bordered align-middle text-center">
                <thead class="table-secondary">
                    <tr>
                        <th>Heure</th>
                        <th>Balise</th>
                        <th>Latitude</th>
                        <th>Longitude</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($points) === 0): ?>
                        <tr><td colspan="4">Aucune localisation.</td></tr>
                    <?php else: ?>
                        <?php foreach ($points as $p): ?>
                            <tr>
                                <td><?= htmlspecialchars($p["created_at"]) ?></td>
                                <td><?= htmlspecialchars((string)$p["numerodecarte"]) ?></td>
                                <td><?= htmlspecialchars((string)$p["latitude"]) ?></td>
                                <td><?= htmlspecialchars((string)$p["longitude"]) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const points = <?= json_encode($points, JSON_UNESCAPED_UNICODE) ?>;
const map = L.map('map');

if (points.length === 0) {
  // Lycée André Malraux - Béthune
  map.setView([50.51942, 2.65176], 17);
} else {
  const latlngs = points.map(p => [parseFloat(p.latitude), parseFloat(p.longitude)]);
  map.fitBounds(latlngs, { padding: [20, 20] });

  const polyline = L.polyline(latlngs, { color: '#4CAF50', weight: 4 }).addTo(map);
  latlngs.forEach((ll, idx) => {
    const label = points[idx].numerodecarte ? `Balise ${points[idx].numerodecarte}` : `Point ${idx+1}`;
    L.marker(ll).addTo(map).bindPopup(label);
  });
}

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
  maxZoom: 19,
  attribution: '&copy; OpenStreetMap'
}).addTo(map);
</script>

<?php
include "../includes/footer.php";
?>
