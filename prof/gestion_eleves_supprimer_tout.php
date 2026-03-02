<?php
require_once "../includes/auth.php";
require_once "../includes/db.php";

protectPage("prof");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: gestion_eleves.php");
    exit;
}

// Delete in dependency order
$pdo->beginTransaction();
try {
    // Notes/resultats legacy
    $pdo->exec(
        "DELETE r FROM resultat r
         JOIN note n ON n.noteID = r.noteID
         JOIN utilisateur u ON u.id_utilisateur = n.id_utilisateur
         WHERE u.acreditation = 'eleve'"
    );
    $pdo->exec(
        "DELETE n FROM note n
         JOIN utilisateur u ON u.id_utilisateur = n.id_utilisateur
         WHERE u.acreditation = 'eleve'"
    );

    // Seances and related tables
    $pdo->exec(
        "DELETE sr FROM seance_resultat sr
         JOIN seance s ON s.id_seance = sr.id_seance
         JOIN utilisateur u ON u.id_utilisateur = s.id_eleve
         WHERE u.acreditation = 'eleve'"
    );
    $pdo->exec(
        "DELETE sb FROM seance_boitier sb
         JOIN seance s ON s.id_seance = sb.id_seance
         JOIN utilisateur u ON u.id_utilisateur = s.id_eleve
         WHERE u.acreditation = 'eleve'"
    );
    $pdo->exec(
        "DELETE sp FROM seance_parcours sp
         JOIN seance s ON s.id_seance = sp.id_seance
         JOIN utilisateur u ON u.id_utilisateur = s.id_eleve
         WHERE u.acreditation = 'eleve'"
    );
    $pdo->exec(
        "DELETE s FROM seance s
         JOIN utilisateur u ON u.id_utilisateur = s.id_eleve
         WHERE u.acreditation = 'eleve'"
    );

    // Liaison classe
    $pdo->exec(
        "DELETE lc FROM liaison_classe lc
         JOIN utilisateur u ON u.id_utilisateur = lc.id_utilisateur
         WHERE u.acreditation = 'eleve'"
    );

    // Finally delete students
    $pdo->exec("DELETE FROM utilisateur WHERE acreditation = 'eleve'");

    $pdo->commit();
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
}

header("Location: gestion_eleves.php");
exit;
