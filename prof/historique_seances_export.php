<?php
require_once "../includes/auth.php";
require_once "../includes/db.php";

protectPage("prof");

$filterEleve = trim($_GET["eleve"] ?? "");
$filterClasse = (int)($_GET["classe"] ?? 0);
$filterDate = trim($_GET["date"] ?? "");

$where = [];
$params = [];
if ($filterEleve !== "") {
    $where[] = "(u.nom LIKE ? OR u.prenom LIKE ? OR u.login LIKE ?)";
    $params[] = "%" . $filterEleve . "%";
    $params[] = "%" . $filterEleve . "%";
    $params[] = "%" . $filterEleve . "%";
}
if ($filterClasse > 0) {
    $where[] = "c.id_classe = ?";
    $params[] = $filterClasse;
}
if ($filterDate !== "") {
    $where[] = "DATE(s.date_heure) = ?";
    $params[] = $filterDate;
}

$sql = "SELECT s.date_heure,
               u.nom, u.prenom, u.login,
               p.nom AS parcours_nom,
               sr.nb_valides, sr.nb_invalides, sr.note_finale,
               sr.heure_debut, sr.heure_fin, sr.duree,
               (SELECT COUNT(*) FROM composer c2 WHERE c2.numero_du_parcours = sr.id_parcours) AS total_balises,
               c.nom AS classe_nom
        FROM seance_resultat sr
        JOIN seance s ON s.id_seance = sr.id_seance
        JOIN utilisateur u ON u.id_utilisateur = s.id_eleve
        JOIN parcours p ON p.numero_du_parcours = sr.id_parcours
        LEFT JOIN liaison_classe lc ON lc.id_utilisateur = u.id_utilisateur AND lc.date_fin IS NULL
        LEFT JOIN classe c ON c.id_classe = lc.id_classe";
if (count($where) > 0) {
    $sql .= " WHERE " . implode(" AND ", $where);
}
$sql .= " ORDER BY s.date_heure DESC, sr.id_resultat DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

function sanitize_filename_part(string $value): string
{
    $value = iconv("UTF-8", "ASCII//TRANSLIT//IGNORE", $value);
    $value = strtolower($value);
    $value = preg_replace("/[^a-z0-9]+/", "", $value);
    return $value ?: "all";
}

$parts = ["historique_seances"];
if ($filterDate !== "") {
    $parts[] = str_replace("-", "", $filterDate);
}
if ($filterClasse > 0) {
    $parts[] = "classe" . $filterClasse;
}

if ($filterEleve !== "") {
    $stmtEleve = $pdo->prepare(
        "SELECT prenom, nom
         FROM utilisateur
         WHERE login = ?
            OR nom LIKE ?
            OR prenom LIKE ?
            OR CONCAT(prenom, ' ', nom) LIKE ?
         ORDER BY nom ASC, prenom ASC
         LIMIT 1"
    );
    $like = "%" . $filterEleve . "%";
    $stmtEleve->execute([$filterEleve, $like, $like, $like]);
    $eleveRow = $stmtEleve->fetch();
    if ($eleveRow) {
        $fullName = trim($eleveRow["prenom"] . " " . $eleveRow["nom"]);
        $parts[] = sanitize_filename_part($fullName);
    } else {
        $parts[] = sanitize_filename_part($filterEleve);
    }
}
$filename = implode("_", $parts) . ".xls";

header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
header("Content-Disposition: attachment; filename=" . $filename);

echo "\xEF\xBB\xBF";
?>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #333; padding: 6px; text-align: center; }
        th { background: #c9cdca; font-weight: bold; }
    </style>
</head>
<body>
<table>
    <colgroup>
        <col style="width: 160px;">
        <col style="width: 220px;">
        <col style="width: 120px;">
        <col style="width: 180px;">
        <col style="width: 90px;">
        <col style="width: 90px;">
        <col style="width: 120px;">
        <col style="width: 120px;">
        <col style="width: 100px;">
    </colgroup>
    <thead>
        <tr>
            <th>Date &amp; heure</th>
            <th>Eleve</th>
            <th>Classe</th>
            <th>Parcours</th>
            <th>Validees</th>
            <th>Note finale</th>
            <th>Heure debut</th>
            <th>Heure fin</th>
            <th>Duree</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($rows as $r): ?>
        <?php
            $total = (int)($r["total_balises"] ?? 0);
            $valides = (int)$r["nb_valides"];
            $invalides = (int)$r["nb_invalides"];
            $denom = $total > 0 ? $total : ($valides + $invalides);
            $validesStr = $valides . "/" . ($denom > 0 ? $denom : "-");
            $eleve = trim($r["prenom"] . " " . $r["nom"] . " (" . $r["login"] . ")");
        ?>
        <tr>
            <td><?= htmlspecialchars($r["date_heure"]) ?></td>
            <td><?= htmlspecialchars($eleve) ?></td>
            <td><?= htmlspecialchars($r["classe_nom"] ?? "") ?></td>
            <td><?= htmlspecialchars($r["parcours_nom"]) ?></td>
            <td><?= htmlspecialchars($validesStr) ?></td>
            <td><?= htmlspecialchars((string)$r["note_finale"]) ?></td>
            <td><?= htmlspecialchars($r["heure_debut"] ?? "") ?></td>
            <td><?= htmlspecialchars($r["heure_fin"] ?? "") ?></td>
            <td><?= htmlspecialchars($r["duree"] ?? "") ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</body>
</html>
<?php
exit;
