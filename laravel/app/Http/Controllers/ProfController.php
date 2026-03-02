<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProfController extends Controller
{
    public function dashboard()
    {
        return view("prof.dashboard", [
            "pageTitle" => "Espace professeur",
            "roleLabel" => "Professeur",
        ]);
    }

    public function gestionClasses()
    {
        $classes = DB::table("classe as c")
            ->leftJoin("liaison_classe as lc", "lc.id_classe", "=", "c.id")
            ->select(
                "c.id as id_classe",
                "c.nom as classe",
                DB::raw("COUNT(lc.id_eleve) as nb_eleves")
            )
            ->groupBy("c.id", "c.nom")
            ->orderBy("c.nom")
            ->get();

        return view("prof.gestion_classes", [
            "pageTitle" => "Espace professeur",
            "roleLabel" => "Professeur",
            "classes" => $classes,
        ]);
    }

    public function gestionClassesCreate(Request $request)
    {
        $name = $this->normalizeClassName((string)$request->input("classe", ""));
        if ($name === "") {
            return redirect("/prof/gestion-classes")->with("error", "invalid");
        }
        $exists = DB::table("classe")->where("nom", $name)->exists();
        if ($exists) {
            return redirect("/prof/gestion-classes")->with("error", "exists");
        }
        DB::table("classe")->insert(["nom" => $name]);
        return redirect("/prof/gestion-classes");
    }

    public function gestionClassesDelete(Request $request, int $id)
    {
        if ($id <= 0) {
            return redirect("/prof/gestion-classes");
        }
        $hasStudents = DB::table("liaison_classe")->where("id_classe", $id)->exists();
        if ($hasStudents) {
            return redirect("/prof/gestion-classes")->with("error", "has_students");
        }
        DB::table("classe")->where("id", $id)->delete();
        return redirect("/prof/gestion-classes");
    }

    public function gestionEleves(Request $request)
    {
        $filterClasse = (int)$request->query("id_classe", 0);
        $filterNom = trim((string)$request->query("nom", ""));
        $filterPrenom = trim((string)$request->query("prenom", ""));

        $classes = DB::table("classe")
            ->select("id as id_classe", "nom")
            ->orderBy("nom")
            ->get();

        $query = DB::table("utilisateur as u")
            ->leftJoin("liaison_classe as lc", "lc.id_eleve", "=", "u.id")
            ->leftJoin("classe as c", "c.id", "=", "lc.id_classe")
            ->where("u.role", "eleve")
            ->select(
                "u.id as id_utilisateur",
                "u.nom",
                "u.prenom",
                "u.login",
                DB::raw("COALESCE(c.nom, '') as classe")
            )
            ->orderBy("u.nom");

        if ($filterClasse > 0) {
            $query->where("lc.id_classe", $filterClasse);
        }
        if ($filterNom !== "") {
            $query->where("u.nom", "like", "%" . $filterNom . "%");
        }
        if ($filterPrenom !== "") {
            $query->where("u.prenom", "like", "%" . $filterPrenom . "%");
        }

        $eleves = $query->get();

        return view("prof.gestion_eleves", [
            "pageTitle" => "Espace professeur",
            "roleLabel" => "Professeur",
            "classes" => $classes,
            "eleves" => $eleves,
            "filterClasse" => $filterClasse ?: null,
            "filterNom" => $filterNom,
            "filterPrenom" => $filterPrenom,
        ]);
    }

    public function gestionElevesCreate(Request $request)
    {
        $nom = $this->normalizeName((string)$request->input("nom", ""));
        $prenom = $this->normalizeName((string)$request->input("prenom", ""));
        $login = $this->normalizeLoginInput((string)$request->input("login", ""));
        $password = (string)$request->input("password", "");
        $idClasse = (int)$request->input("id_classe", 0);

        if ($nom === "" || $prenom === "" || $login === "" || $password === "") {
            return redirect("/prof/gestion-eleves")->with("error", "1");
        }
        $exists = DB::table("utilisateur")->where("login", $login)->exists();
        if ($exists) {
            return redirect("/prof/gestion-eleves")->with("error", "1");
        }

        $idEleve = DB::table("utilisateur")->insertGetId([
            "nom" => $nom,
            "prenom" => $prenom,
            "login" => $login,
            "mdp" => password_hash($password, PASSWORD_DEFAULT),
            "role" => "eleve",
        ]);

        if ($idClasse > 0) {
            DB::table("liaison_classe")->insert([
                "id_classe" => $idClasse,
                "id_eleve" => $idEleve,
            ]);
        }

        return redirect("/prof/gestion-eleves");
    }

    public function gestionElevesAffecter(Request $request)
    {
        $idEleve = (int)$request->input("id_utilisateur", 0);
        $idClasse = (int)$request->input("id_classe", 0);

        if ($idEleve <= 0) {
            return redirect("/prof/gestion-eleves");
        }

        DB::table("liaison_classe")->where("id_eleve", $idEleve)->delete();
        if ($idClasse > 0) {
            DB::table("liaison_classe")->insert([
                "id_classe" => $idClasse,
                "id_eleve" => $idEleve,
            ]);
        }

        return redirect("/prof/gestion-eleves");
    }

    public function gestionElevesDelete(Request $request, int $id)
    {
        if ($id <= 0) {
            return redirect("/prof/gestion-eleves");
        }
        $seanceIds = DB::table("seance")->where("id_eleve", $id)->pluck("id")->all();
        if (!empty($seanceIds)) {
            DB::table("scan_seance")->whereIn("id_seance", $seanceIds)->delete();
            DB::table("resultat")->whereIn("id_seance", $seanceIds)->delete();
            DB::table("seance")->whereIn("id", $seanceIds)->delete();
        }
        DB::table("liaison_classe")->where("id_eleve", $id)->delete();
        DB::table("utilisateur")->where("id", $id)->delete();
        return redirect("/prof/gestion-eleves");
    }

    public function gestionElevesDeleteAll()
    {
        $eleveIds = DB::table("utilisateur")->where("role", "eleve")->pluck("id")->all();
        if (!empty($eleveIds)) {
            $seanceIds = DB::table("seance")->whereIn("id_eleve", $eleveIds)->pluck("id")->all();
            if (!empty($seanceIds)) {
                DB::table("scan_seance")->whereIn("id_seance", $seanceIds)->delete();
                DB::table("resultat")->whereIn("id_seance", $seanceIds)->delete();
                DB::table("seance")->whereIn("id", $seanceIds)->delete();
            }
            DB::table("liaison_classe")->whereIn("id_eleve", $eleveIds)->delete();
            DB::table("utilisateur")->whereIn("id", $eleveIds)->delete();
        }
        return redirect("/prof/gestion-eleves");
    }

    public function gestionSeances(Request $request)
    {
        $dateFilter = (string)$request->query("date", "");
        $filterNom = trim((string)$request->query("nom", ""));
        $filterPrenom = trim((string)$request->query("prenom", ""));

        $query = DB::table("seance as s")
            ->join("utilisateur as u", "u.id", "=", "s.id_eleve")
            ->select(
                "s.id as id_seance",
                DB::raw("s.date_debut as date_heure"),
                "u.nom",
                "u.prenom",
                "u.login"
            )
            ->orderByDesc("s.date_debut");

        if ($dateFilter !== "") {
            $query->whereDate("s.date_debut", $dateFilter);
        }
        if ($filterNom !== "") {
            $query->where("u.nom", "like", "%" . $filterNom . "%");
        }
        if ($filterPrenom !== "") {
            $query->where("u.prenom", "like", "%" . $filterPrenom . "%");
        }

        $seances = $query->get();

        $eleves = DB::table("utilisateur")
            ->where("role", "eleve")
            ->select("id as id_utilisateur", "nom", "prenom", "login")
            ->orderBy("nom")
            ->get();

        $parcours = DB::table("parcours")
            ->select("id as numero_du_parcours", "nom")
            ->orderBy("nom")
            ->get();

        $boitiers = DB::table("boitier")
            ->select(
                "id as numero",
                "mac as add_mac",
                "reseau as add_reseau",
                "etat"
            )
            ->orderBy("id")
            ->get();

        return view("prof.gestion_seances", [
            "pageTitle" => "Espace professeur",
            "roleLabel" => "Professeur",
            "seances" => $seances,
            "eleves" => $eleves,
            "parcours" => $parcours,
            "boitiers" => $boitiers,
            "dateFilter" => $dateFilter,
            "filterNom" => $filterNom,
            "filterPrenom" => $filterPrenom,
        ]);
    }

    public function gestionSeancesCreate(Request $request)
    {
        $idEleve = (int)$request->input("id_eleve", 0);
        $date = (string)$request->input("date", "");
        $idParcours = (int)$request->input("id_parcours", 0);
        $idBoitier = (int)$request->input("numero_boitier", 0);

        if ($idEleve <= 0 || $date === "" || $idParcours <= 0 || $idBoitier <= 0) {
            return redirect("/prof/gestion-seances")->with("error", "1");
        }

        DB::table("seance")->insert([
            "id_eleve" => $idEleve,
            "id_parcours" => $idParcours,
            "id_boitier" => $idBoitier,
            "date_debut" => $date . " 00:00:00",
        ]);

        return redirect("/prof/gestion-seances");
    }

    public function gestionSeancesDelete(Request $request, int $id)
    {
        if ($id <= 0) {
            return redirect("/prof/gestion-seances");
        }
        DB::table("scan_seance")->where("id_seance", $id)->delete();
        DB::table("resultat")->where("id_seance", $id)->delete();
        DB::table("seance")->where("id", $id)->delete();
        return redirect("/prof/gestion-seances");
    }

    public function seanceDetail(Request $request)
    {
        $id = (int)$request->query("id", 0);
        if ($id <= 0) {
            return redirect("/prof/gestion-seances");
        }

        $seance = DB::table("seance as s")
            ->join("utilisateur as u", "u.id", "=", "s.id_eleve")
            ->select("s.id as id_seance", "u.nom", "u.prenom", "s.id_parcours")
            ->where("s.id", $id)
            ->first();
        if (!$seance) {
            return redirect("/prof/gestion-seances");
        }

        $parcours = DB::table("parcours")
            ->select("id as numero_du_parcours", "nom", "niveau")
            ->where("id", $seance->id_parcours)
            ->get();

        $parcoursInfo = $parcours->first();
        $resultRow = DB::table("resultat")
            ->where("id_seance", $id)
            ->select("nb_balise_valide", "note")
            ->first();
        $resultats = collect();
        if ($parcoursInfo) {
            $resultats->push((object)[
                "nb_valides" => (int)($resultRow->nb_balise_valide ?? 0),
                "nb_invalides" => 0,
                "note_finale" => $resultRow->note ?? null,
                "heure_debut" => null,
                "heure_fin" => null,
                "duree" => null,
                "parcours_nom" => $parcoursInfo->nom,
                "niveau" => $parcoursInfo->niveau,
            ]);
        }

        $totalBalises = DB::table("compose_parcours")
            ->where("id_parcours", $seance->id_parcours)
            ->count();

        $resultats = $resultats->map(function ($r) use ($totalBalises) {
            $r->total_balises = $totalBalises;
            return $r;
        });

        return view("prof.seance_detail", [
            "pageTitle" => "Seance",
            "roleLabel" => "Professeur",
            "seance" => $seance,
            "parcours" => $parcours,
            "resultats" => $resultats,
        ]);
    }

    public function seanceAddNoteManuelle(Request $request)
    {
        $idSeance = (int)$request->input("id_seance", 0);
        $idParcours = (int)$request->input("id_parcours", 0);
        $note = str_replace(",", ".", (string)$request->input("note_manuelle", ""));
        $noteValue = is_numeric($note) ? (float)$note : null;
        if ($idSeance <= 0 || $idParcours <= 0 || $noteValue === null) {
            return redirect("/prof/seance-detail?id=" . $idSeance);
        }

        $nbValides = DB::table("scan_seance")
            ->where("id_seance", $idSeance)
            ->where("valide", 1)
            ->count();

        $exists = DB::table("resultat")->where("id_seance", $idSeance)->exists();
        if ($exists) {
            DB::table("resultat")->where("id_seance", $idSeance)->update([
                "nb_balise_valide" => $nbValides,
                "note" => $noteValue,
            ]);
        } else {
            DB::table("resultat")->insert([
                "id_seance" => $idSeance,
                "nb_balise_valide" => $nbValides,
                "note" => $noteValue,
            ]);
        }

        return redirect("/prof/seance-detail?id=" . $idSeance);
    }

    public function historiqueSeances(Request $request)
    {
        $filterEleve = trim((string)$request->query("eleve", ""));
        $filterClasse = (int)$request->query("classe", 0);
        $filterDate = (string)$request->query("date", "");

        $classes = DB::table("classe")
            ->select("id as id_classe", "nom")
            ->orderBy("nom")
            ->get();

        $query = DB::table("seance as s")
            ->join("utilisateur as u", "u.id", "=", "s.id_eleve")
            ->leftJoin("liaison_classe as lc", "lc.id_eleve", "=", "u.id")
            ->leftJoin("classe as c", "c.id", "=", "lc.id_classe")
            ->leftJoin("parcours as p", "p.id", "=", "s.id_parcours")
            ->leftJoin("resultat as r", "r.id_seance", "=", "s.id")
            ->select(
                "s.id as id_seance",
                DB::raw("s.date_debut as date_heure"),
                "u.nom",
                "u.prenom",
                "u.login",
                "c.nom as classe_nom",
                "p.nom as parcours_nom",
                "p.niveau as niveau",
                DB::raw("COALESCE(r.nb_balise_valide, 0) as nb_valides"),
                DB::raw("0 as nb_invalides"),
                DB::raw("COALESCE(r.note, 0) as note_finale"),
                DB::raw("NULL as heure_debut"),
                DB::raw("NULL as heure_fin"),
                DB::raw("NULL as duree"),
                "s.id_parcours"
            )
            ->orderByDesc("s.date_debut");

        if ($filterEleve !== "") {
            $query->where(function ($q) use ($filterEleve) {
                $q->where("u.nom", "like", "%" . $filterEleve . "%")
                    ->orWhere("u.prenom", "like", "%" . $filterEleve . "%")
                    ->orWhere("u.login", "like", "%" . $filterEleve . "%");
            });
        }
        if ($filterClasse > 0) {
            $query->where("lc.id_classe", $filterClasse);
        }
        if ($filterDate !== "") {
            $query->whereDate("s.date_debut", $filterDate);
        }

        $results = $query->get();

        $parcoursCounts = DB::table("compose_parcours")
            ->select("id_parcours", DB::raw("COUNT(*) as total"))
            ->groupBy("id_parcours")
            ->pluck("total", "id_parcours");

        $results = $results->map(function ($r) use ($parcoursCounts) {
            $total = (int)($parcoursCounts[$r->id_parcours] ?? 0);
            $r->total_balises = $total;
            $r->nb_invalides = max($total - (int)$r->nb_valides, 0);
            return $r;
        });

        return view("prof.historique_seances", [
            "pageTitle" => "Espace professeur",
            "roleLabel" => "Professeur",
            "results" => $results,
            "classes" => $classes,
            "filterEleve" => $filterEleve,
            "filterClasse" => $filterClasse ?: null,
            "filterDate" => $filterDate,
        ]);
    }

    public function historiqueSeancesExport(Request $request)
    {
        $filterEleve = trim((string)$request->query("eleve", ""));
        $filterClasse = (int)$request->query("classe", 0);
        $filterDate = (string)$request->query("date", "");

        $results = $this->getHistoriqueResults($filterEleve, $filterClasse, $filterDate);
        $filename = $this->buildHistoriqueFilename($filterEleve, $filterClasse, $filterDate);

        $headers = [
            "Content-Type" => "application/vnd.ms-excel; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=\"" . $filename . ".xls\"",
        ];

        $html = '<table border="1">';
        $html .= '<tr><th>Date & heure</th><th>Eleve</th><th>Classe</th><th>Parcours</th><th>Validees</th><th>Note</th></tr>';
        foreach ($results as $r) {
            $total = (int)($r->total_balises ?? 0);
            $valides = (int)$r->nb_valides;
            $denom = $total > 0 ? $total : $valides;
            $html .= '<tr>';
            $html .= '<td>' . htmlspecialchars((string)$r->date_heure) . '</td>';
            $html .= '<td>' . htmlspecialchars($r->prenom . ' ' . $r->nom . ' (' . $r->login . ')') . '</td>';
            $html .= '<td>' . htmlspecialchars((string)$r->classe_nom) . '</td>';
            $html .= '<td>' . htmlspecialchars((string)$r->parcours_nom) . '</td>';
            $html .= '<td>' . htmlspecialchars($valides . '/' . $denom) . '</td>';
            $html .= '<td>' . htmlspecialchars((string)$r->note_finale) . '</td>';
            $html .= '</tr>';
        }
        $html .= '</table>';

        return response($html, 200, $headers);
    }

    public function seanceTrajet(Request $request)
    {
        $id = (int)$request->query("id", 0);
        if ($id <= 0) {
            return redirect("/prof/historique-seances");
        }

        $seance = DB::table("seance as s")
            ->join("utilisateur as u", "u.id", "=", "s.id_eleve")
            ->select("s.id as id_seance", DB::raw("s.date_debut as date_heure"), "u.nom", "u.prenom", "s.id_boitier")
            ->where("s.id", $id)
            ->first();
        if (!$seance) {
            return redirect("/prof/historique-seances");
        }

        $boitier = null;
        if (!empty($seance->id_boitier)) {
            $boitier = DB::table("boitier")
                ->select("id as numero_boitier")
                ->where("id", $seance->id_boitier)
                ->first();
        }

        // 1) Prefer real GPS points from localisation table for this session.
        $points = collect();
        try {
            $points = DB::table("localisation")
                ->where("id_seance", $id)
                ->whereNotNull("latitude")
                ->whereNotNull("longitude")
                ->orderBy("recorded_at")
                ->select(
                    "recorded_at as created_at",
                    DB::raw("NULL as numerodecarte"),
                    "latitude",
                    "longitude"
                )
                ->get();
        } catch (\Throwable $e) {
            $points = collect();
        }

        // 2) Fallback to localisation points by boitier on the same day.
        if ($points->isEmpty() && !empty($seance->id_boitier)) {
            try {
                $points = DB::table("localisation")
                    ->where("id_boitier", (int)$seance->id_boitier)
                    ->whereNotNull("latitude")
                    ->whereNotNull("longitude")
                    ->whereDate("recorded_at", substr((string)$seance->date_heure, 0, 10))
                    ->orderBy("recorded_at")
                    ->select(
                        "recorded_at as created_at",
                        DB::raw("NULL as numerodecarte"),
                        "latitude",
                        "longitude"
                    )
                    ->get();
            } catch (\Throwable $e) {
                $points = collect();
            }
        }

        // 3) Last fallback to balise coordinates if no GPS points exist.
        if ($points->isEmpty()) {
            $points = DB::table("scan_seance as ss")
                ->join("balise as b", "b.id", "=", "ss.id_balise")
                ->where("ss.id_seance", $id)
                ->orderBy("ss.date_time")
                ->select(
                    "ss.date_time as created_at",
                    "b.id as numerodecarte",
                    "b.lat as latitude",
                    "b.lng as longitude"
                )
                ->get();
        }

        return view("prof.seance_trajet", [
            "pageTitle" => "Trajet",
            "roleLabel" => "Professeur",
            "seance" => $seance,
            "boitier" => $boitier,
            "points" => $points,
        ]);
    }

    public function gestionBoitiers()
    {
        $boitiers = DB::table("boitier")
            ->select(
                "id as numero",
                "mac as add_mac",
                "reseau as add_reseau",
                "etat"
            )
            ->orderBy("id")
            ->get();

        return view("prof.gestion_boitier", [
            "pageTitle" => "Espace professeur",
            "roleLabel" => "Professeur",
            "boitiers" => $boitiers,
        ]);
    }

    public function gestionBoitiersCreate(Request $request)
    {
        $mac = trim((string)$request->input("add_mac", ""));
        $reseau = trim((string)$request->input("add_reseau", ""));
        $etat = trim((string)$request->input("etat", ""));

        DB::table("boitier")->insert([
            "mac" => $mac,
            "reseau" => $reseau,
            "etat" => $etat,
        ]);

        return redirect("/prof/gestion-boitiers");
    }

    public function gestionBoitiersUpdateEtat(Request $request, int $id)
    {
        $nouvelEtat = trim((string)$request->input("etat", ""));
        $etatsAutorises = ["disponible", "occupe", "hors_service"];
        if ($id <= 0 || !in_array($nouvelEtat, $etatsAutorises, true)) {
            return redirect("/prof/gestion-boitiers")->with("error", "etat");
        }

        $boitier = DB::table("boitier")
            ->select("id", "etat")
            ->where("id", $id)
            ->first();
        if (!$boitier) {
            return redirect("/prof/gestion-boitiers")->with("error", "etat");
        }

        $ancienEtat = (string)($boitier->etat ?? "");
        if ($ancienEtat === $nouvelEtat) {
            return redirect("/prof/gestion-boitiers");
        }

        DB::table("boitier")
            ->where("id", $id)
            ->update(["etat" => $nouvelEtat]);

        DB::table("boitier_etat_logs")->insert([
            "id_boitier" => $id,
            "ancien_etat" => $ancienEtat !== "" ? $ancienEtat : null,
            "nouvel_etat" => $nouvelEtat,
            "id_utilisateur" => (int)$request->session()->get("user_id"),
            "created_at" => now(),
        ]);

        return redirect("/prof/gestion-boitiers")->with("updated", "etat");
    }

    public function gestionBoitiersDelete(Request $request, int $id)
    {
        if ($id > 0) {
            DB::table("boitier")->where("id", $id)->delete();
        }
        return redirect("/prof/gestion-boitiers");
    }

    public function gestionParcours()
    {
        $parcours = DB::table("parcours as p")
            ->leftJoin("compose_parcours as cp", "cp.id_parcours", "=", "p.id")
            ->select(
                "p.id as numero_du_parcours",
                "p.nom",
                "p.niveau",
                DB::raw("NULL as commentaire"),
                DB::raw("COUNT(cp.id_balise) as nb_balises")
            )
            ->groupBy("p.id", "p.nom", "p.niveau")
            ->orderBy("p.nom")
            ->get();

        return view("prof.gestion_parcours", [
            "pageTitle" => "Espace professeur",
            "roleLabel" => "Professeur",
            "parcours" => $parcours,
        ]);
    }

    public function gestionParcoursCreate(Request $request)
    {
        $nom = $this->normalizeText((string)$request->input("nom", ""));
        $niveau = $this->normalizeText((string)$request->input("niveau", ""));
        if ($nom === "") {
            return redirect("/prof/gestion-parcours")->with("error", "1");
        }
        $exists = DB::table("parcours")->where("nom", $nom)->exists();
        if ($exists) {
            return redirect("/prof/gestion-parcours")->with("error", "1");
        }
        DB::table("parcours")->insert([
            "nom" => $nom,
            "niveau" => $niveau,
        ]);
        return redirect("/prof/gestion-parcours");
    }

    public function gestionParcoursDelete(Request $request, int $id)
    {
        if ($id > 0) {
            DB::table("compose_parcours")->where("id_parcours", $id)->delete();
            DB::table("parcours")->where("id", $id)->delete();
        }
        return redirect("/prof/gestion-parcours");
    }

    public function modifierParcours(Request $request)
    {
        $id = (int)$request->query("id", 0);
        if ($id <= 0) {
            return redirect("/prof/gestion-parcours");
        }

        $parcours = DB::table("parcours")
            ->select("id as numero_du_parcours", "nom", "niveau", DB::raw("NULL as commentaire"))
            ->where("id", $id)
            ->first();
        if (!$parcours) {
            return redirect("/prof/gestion-parcours");
        }

        $balises = DB::table("balise")
            ->select("id as numerodecarte", "tag as tagRFID")
            ->orderBy("id")
            ->get();

        $balisesParcours = DB::table("compose_parcours as cp")
            ->join("balise as b", "b.id", "=", "cp.id_balise")
            ->where("cp.id_parcours", $id)
            ->orderBy("b.id")
            ->select("b.id as numerodecarte", "b.tag as tagRFID")
            ->get()
            ->values()
            ->map(function ($b, $idx) {
                $b->ordre = $idx + 1;
                return $b;
            });

        return view("prof.modifier_parcours", [
            "pageTitle" => "Espace professeur",
            "roleLabel" => "Professeur",
            "parcours" => $parcours,
            "balises" => $balises,
            "balisesParcours" => $balisesParcours,
        ]);
    }

    public function modifierParcoursUpdate(Request $request)
    {
        $id = (int)$request->input("id", 0);
        $nom = $this->normalizeText((string)$request->input("nom", ""));
        $niveau = $this->normalizeText((string)$request->input("niveau", ""));
        if ($id <= 0 || $nom === "") {
            return redirect("/prof/gestion-parcours")->with("error", "1");
        }
        DB::table("parcours")->where("id", $id)->update([
            "nom" => $nom,
            "niveau" => $niveau,
        ]);
        return redirect("/prof/modifier-parcours?id=" . $id);
    }

    public function modifierParcoursAddBalise(Request $request)
    {
        $id = (int)$request->input("id", 0);
        $baliseId = (int)$request->input("numerodecarte", 0);
        if ($id <= 0 || $baliseId <= 0) {
            return redirect("/prof/modifier-parcours?id=" . $id);
        }
        $exists = DB::table("compose_parcours")
            ->where("id_parcours", $id)
            ->where("id_balise", $baliseId)
            ->exists();
        if (!$exists) {
            DB::table("compose_parcours")->insert([
                "id_parcours" => $id,
                "id_balise" => $baliseId,
            ]);
        }
        return redirect("/prof/modifier-parcours?id=" . $id);
    }

    public function modifierParcoursUpdateOrdre()
    {
        return redirect("/prof/gestion-parcours");
    }

    public function modifierParcoursRemoveBalise(Request $request)
    {
        $id = (int)$request->query("id", 0);
        $baliseId = (int)$request->query("numerodecarte", 0);
        if ($id > 0 && $baliseId > 0) {
            DB::table("compose_parcours")
                ->where("id_parcours", $id)
                ->where("id_balise", $baliseId)
                ->delete();
        }
        return redirect("/prof/modifier-parcours?id=" . $id);
    }

    public function modifierClasse(Request $request)
    {
        $id = (int)$request->query("id_classe", 0);
        if ($id <= 0) {
            return redirect("/prof/gestion-classes");
        }

        $classe = DB::table("classe")
            ->select("id as id_classe", "nom")
            ->where("id", $id)
            ->first();
        if (!$classe) {
            return redirect("/prof/gestion-classes");
        }

        $elevesClasse = DB::table("liaison_classe as lc")
            ->join("utilisateur as u", "u.id", "=", "lc.id_eleve")
            ->where("lc.id_classe", $id)
            ->select("u.id as id_utilisateur", "u.nom", "u.prenom", "u.login")
            ->orderBy("u.nom")
            ->get();

        $elevesDisponibles = DB::table("utilisateur as u")
            ->where("u.role", "eleve")
            ->whereNotIn("u.id", $elevesClasse->pluck("id_utilisateur")->all())
            ->select("u.id as id_utilisateur", "u.nom", "u.prenom", "u.login")
            ->orderBy("u.nom")
            ->get();

        return view("prof.modifier_classe", [
            "pageTitle" => "Espace professeur",
            "roleLabel" => "Professeur",
            "classe" => $classe,
            "elevesClasse" => $elevesClasse,
            "elevesDisponibles" => $elevesDisponibles,
        ]);
    }

    public function modifierClasseRename(Request $request)
    {
        $id = (int)$request->input("id_classe", 0);
        $nom = $this->normalizeClassName((string)$request->input("nom", ""));
        if ($id <= 0 || $nom === "") {
            return redirect("/prof/modifier-classe?id_classe=" . $id)->with("error", "1");
        }
        DB::table("classe")->where("id", $id)->update(["nom" => $nom]);
        return redirect("/prof/modifier-classe?id_classe=" . $id);
    }

    public function modifierClasseAddEleve(Request $request)
    {
        $idClasse = (int)$request->input("id_classe", 0);
        $idEleve = (int)$request->input("id_utilisateur", 0);
        if ($idClasse > 0 && $idEleve > 0) {
            DB::table("liaison_classe")->insert([
                "id_classe" => $idClasse,
                "id_eleve" => $idEleve,
            ]);
        }
        return redirect("/prof/modifier-classe?id_classe=" . $idClasse);
    }

    public function modifierClasseRemoveEleve(Request $request)
    {
        $idClasse = (int)$request->input("id_classe", 0);
        $idEleve = (int)$request->input("id_utilisateur", 0);
        if ($idClasse > 0 && $idEleve > 0) {
            DB::table("liaison_classe")
                ->where("id_classe", $idClasse)
                ->where("id_eleve", $idEleve)
                ->delete();
        }
        return redirect("/prof/modifier-classe?id_classe=" . $idClasse);
    }

    public function modifierClasseImportCsv(Request $request)
    {
        $idClasse = (int)$request->input("id_classe", 0);
        if ($idClasse <= 0 || !$request->hasFile("csv_file")) {
            return redirect("/prof/modifier-classe?id_classe=" . $idClasse);
        }

        $file = $request->file("csv_file");
        if (!$file->isValid()) {
            return redirect("/prof/modifier-classe?id_classe=" . $idClasse);
        }

        $path = $file->getRealPath();
        if (!$path) {
            return redirect("/prof/modifier-classe?id_classe=" . $idClasse);
        }

        $rows = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $delimiter = $this->detectCsvDelimiter($rows);
        foreach ($rows as $row) {
            $cols = str_getcsv($row, $delimiter);
            if (empty($cols[0])) {
                continue;
            }
            $full = trim((string)$cols[0]);
            if ($full === "") {
                continue;
            }
            // Ignore the header row of school exports (Eleves / Élèves...)
            if (preg_match("/^eleves?$/i", $this->normalizeLoginInput($full))) {
                continue;
            }
            $parts = preg_split("/\\s+/", $full);
            if (count($parts) < 2) {
                continue;
            }
            $nom = substr($this->normalizeName((string)$parts[0]), 0, 50);
            $prenom = substr($this->normalizeName(implode(" ", array_slice($parts, 1))), 0, 50);
            if ($nom === "" || $prenom === "") {
                continue;
            }
            $login = substr($this->normalizeLoginInput($nom . "." . $prenom), 0, 100);
            if ($login === "") {
                continue;
            }

            $exists = DB::table("utilisateur")->where("login", $login)->exists();
            if ($exists) {
                continue;
            }

            $idEleve = DB::table("utilisateur")->insertGetId([
                "nom" => $nom,
                "prenom" => $prenom,
                "login" => $login,
                "mdp" => password_hash("1234", PASSWORD_DEFAULT),
                "role" => "eleve",
            ]);

            DB::table("liaison_classe")->insert([
                "id_classe" => $idClasse,
                "id_eleve" => $idEleve,
            ]);
        }

        return redirect("/prof/modifier-classe?id_classe=" . $idClasse);
    }

    private function detectCsvDelimiter(array $rows): string
    {
        $sample = "";
        foreach ($rows as $row) {
            $line = trim((string)$row);
            if ($line !== "") {
                $sample = $line;
                break;
            }
        }

        $semi = substr_count($sample, ";");
        $comma = substr_count($sample, ",");
        return $semi > $comma ? ";" : ",";
    }

    private function getHistoriqueResults(string $filterEleve, int $filterClasse, string $filterDate)
    {
        $query = DB::table("seance as s")
            ->join("utilisateur as u", "u.id", "=", "s.id_eleve")
            ->leftJoin("liaison_classe as lc", "lc.id_eleve", "=", "u.id")
            ->leftJoin("classe as c", "c.id", "=", "lc.id_classe")
            ->leftJoin("parcours as p", "p.id", "=", "s.id_parcours")
            ->leftJoin("resultat as r", "r.id_seance", "=", "s.id")
            ->select(
                "s.id as id_seance",
                DB::raw("s.date_debut as date_heure"),
                "u.nom",
                "u.prenom",
                "u.login",
                "c.nom as classe_nom",
                "p.nom as parcours_nom",
                "p.niveau as niveau",
                DB::raw("COALESCE(r.nb_balise_valide, 0) as nb_valides"),
                DB::raw("COALESCE(r.note, 0) as note_finale"),
                "s.id_parcours"
            )
            ->orderByDesc("s.date_debut");

        if ($filterEleve !== "") {
            $query->where(function ($q) use ($filterEleve) {
                $q->where("u.nom", "like", "%" . $filterEleve . "%")
                    ->orWhere("u.prenom", "like", "%" . $filterEleve . "%")
                    ->orWhere("u.login", "like", "%" . $filterEleve . "%");
            });
        }
        if ($filterClasse > 0) {
            $query->where("lc.id_classe", $filterClasse);
        }
        if ($filterDate !== "") {
            $query->whereDate("s.date_debut", $filterDate);
        }

        $results = $query->get();
        $parcoursCounts = DB::table("compose_parcours")
            ->select("id_parcours", DB::raw("COUNT(*) as total"))
            ->groupBy("id_parcours")
            ->pluck("total", "id_parcours");

        return $results->map(function ($r) use ($parcoursCounts) {
            $r->total_balises = (int)($parcoursCounts[$r->id_parcours] ?? 0);
            $r->nb_invalides = max($r->total_balises - (int)$r->nb_valides, 0);
            return $r;
        });
    }

    private function buildHistoriqueFilename(string $filterEleve, int $filterClasse, string $filterDate): string
    {
        $parts = ["historique_seances"];

        if ($filterDate !== "") {
            $parts[] = str_replace("-", "", $filterDate);
        }

        if ($filterClasse > 0) {
            $nomClasse = DB::table("classe")->where("id", $filterClasse)->value("nom");
            if ($nomClasse) {
                $parts[] = $this->normalizeLoginInput($nomClasse);
            }
        }

        if ($filterEleve !== "") {
            $user = DB::table("utilisateur")
                ->where("nom", "like", "%" . $filterEleve . "%")
                ->orWhere("prenom", "like", "%" . $filterEleve . "%")
                ->orWhere("login", "like", "%" . $filterEleve . "%")
                ->first();
            if ($user) {
                $parts[] = $this->normalizeLoginInput($user->prenom . $user->nom);
            } else {
                $parts[] = $this->normalizeLoginInput($filterEleve);
            }
        }

        return implode("_", array_filter($parts));
    }

    private function normalizeText(string $s): string
    {
        $s = trim($s);
        if ($s === "") {
            return $s;
        }
        if (function_exists("mb_convert_encoding")) {
            $s = mb_convert_encoding($s, "UTF-8", "UTF-8, ISO-8859-1, Windows-1252");
        }
        $ascii = @iconv("UTF-8", "ASCII//TRANSLIT//IGNORE", $s);
        if ($ascii !== false) {
            $s = $ascii;
        }
        $s = preg_replace("/[^A-Za-z0-9 _-]/", " ", $s);
        $s = preg_replace("/\\s+/", " ", $s);
        return trim($s);
    }

    private function normalizeName(string $s): string
    {
        $s = $this->normalizeText($s);
        return str_replace(" ", "", $s);
    }

    private function normalizeLoginInput(string $s): string
    {
        $s = trim($s);
        if ($s === "") {
            return $s;
        }
        if (function_exists("mb_convert_encoding")) {
            $s = mb_convert_encoding($s, "UTF-8", "UTF-8, ISO-8859-1, Windows-1252");
        }
        $ascii = @iconv("UTF-8", "ASCII//TRANSLIT//IGNORE", $s);
        if ($ascii !== false) {
            $s = $ascii;
        }
        $s = strtolower($s);
        $s = preg_replace("/[^a-z0-9._-]/", "", $s);
        $s = preg_replace("/\\.{2,}/", ".", $s);
        $s = preg_replace("/_{2,}/", "_", $s);
        $s = preg_replace("/-{2,}/", "-", $s);
        $s = trim($s, "._-");
        return $s;
    }

    private function normalizeClassName(string $s): string
    {
        $s = trim($s);
        if ($s === "") {
            return "";
        }
        $s = iconv("UTF-8", "ASCII//TRANSLIT", $s);
        $s = preg_replace("/[^A-Za-z0-9\\- _]/", " ", $s);
        $s = preg_replace("/\\s+/", " ", $s);
        return trim($s);
    }
}
