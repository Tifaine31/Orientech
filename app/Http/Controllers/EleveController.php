<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EleveController extends Controller
{
    public function dashboard(Request $request)
    {
        $login = (string)$request->session()->get("user_login", "");
        $eleve = null;
        $classe = null;
        $lastResult = null;

        if ($login !== "") {
            $eleve = DB::table("utilisateur")
                ->select("id", "nom", "prenom", "login", "photo")
                ->where("login", $login)
                ->where("role", "eleve")
                ->first();

            if ($eleve) {
                $classe = DB::table("liaison_classe as lc")
                    ->join("classe as c", "c.id", "=", "lc.id_classe")
                    ->where("lc.id_eleve", $eleve->id)
                    ->value("c.nom");
            }
        }

        if (!empty($eleve?->id)) {
            $row = DB::table("seance as s")
                ->leftJoin("resultat as r", "r.id_seance", "=", "s.id")
                ->where("s.id_eleve", $eleve->id)
                ->orderByDesc("s.date_debut")
                ->select("s.date_debut as date_heure", "s.id_parcours", "r.nb_balise_valide", "r.note")
                ->first();

            if ($row) {
                $total = (int)DB::table("compose_parcours")
                    ->where("id_parcours", $row->id_parcours)
                    ->count();

                $valides = (int)($row->nb_balise_valide ?? 0);
                $lastResult = (object)[
                    "date_heure" => $row->date_heure,
                    "nb_valides" => $valides,
                    "nb_invalides" => max($total - $valides, 0),
                    "note_finale" => $row->note ?? 0,
                ];
            }
        }

        return view("eleve.dashboard", [
            "pageTitle" => "Espace eleve",
            "roleLabel" => "Eleve",
            "eleve" => $eleve,
            "classe" => $classe,
            "lastResult" => $lastResult,
        ]);
    }

    public function historique(Request $request)
    {
        $login = (string)$request->session()->get("user_login", "");
        $eleveId = null;
        if ($login !== "") {
            $eleveId = DB::table("utilisateur")
                ->where("login", $login)
                ->where("role", "eleve")
                ->value("id");
        }

        $results = [];
        if ($eleveId) {
            $rows = DB::table("seance as s")
                ->leftJoin("resultat as r", "r.id_seance", "=", "s.id")
                ->where("s.id_eleve", $eleveId)
                ->orderByDesc("s.date_debut")
                ->select("s.id as id_seance", "s.date_debut as date_heure", "s.id_parcours", "r.nb_balise_valide", "r.note")
                ->get();

            $results = $rows->map(function ($row) {
                $total = (int)DB::table("compose_parcours")
                    ->where("id_parcours", $row->id_parcours)
                    ->count();
                $valides = (int)($row->nb_balise_valide ?? 0);
                return (object)[
                    "id_seance" => (int)$row->id_seance,
                    "date_heure" => $row->date_heure,
                    "nb_valides" => $valides,
                    "nb_invalides" => max($total - $valides, 0),
                    "note_finale" => $row->note ?? 0,
                ];
            });
        }

        return view("eleve.historique", [
            "pageTitle" => "Espace eleve",
            "roleLabel" => "Eleve",
            "results" => $results,
        ]);
    }

    public function historiqueTrajet(Request $request)
    {
        $idSeance = (int)$request->query("id", 0);
        $eleveId = (int)$request->session()->get("user_id", 0);
        if ($idSeance <= 0 || $eleveId <= 0) {
            return response()->json(["ok" => false, "error" => "invalid_request"], 422);
        }

        $seance = DB::table("seance")
            ->select("id", "date_debut")
            ->where("id", $idSeance)
            ->where("id_eleve", $eleveId)
            ->first();
        if (!$seance) {
            return response()->json(["ok" => false, "error" => "not_found"], 404);
        }

        $points = collect();
        if (Schema::hasTable("localisation")) {
            $points = DB::table("localisation")
                ->where("id_seance", $idSeance)
                ->whereNotNull("latitude")
                ->whereNotNull("longitude")
                ->orderBy("recorded_at")
                ->select(
                    DB::raw("recorded_at as date_time"),
                    DB::raw("latitude as lat"),
                    DB::raw("longitude as lng"),
                    DB::raw("altitude as alt")
                )
                ->get();
        }

        if ($points->isEmpty()) {
            $points = DB::table("scan_seance as ss")
                ->join("balise as b", "b.id", "=", "ss.id_balise")
                ->where("ss.id_seance", $idSeance)
                ->where("ss.id_eleve", $eleveId)
                ->whereNotNull("b.lat")
                ->whereNotNull("b.lng")
                ->orderBy("ss.date_time")
                ->select(
                    "ss.date_time",
                    DB::raw("b.lat as lat"),
                    DB::raw("b.lng as lng"),
                    DB::raw("b.alt as alt")
                )
                ->get();
        }

        return response()->json([
            "ok" => true,
            "seance" => [
                "id" => (int)$seance->id,
                "date_debut" => $seance->date_debut,
            ],
            "points" => $points,
        ]);
    }

    public function profil(Request $request)
    {
        $login = (string)$request->session()->get("user_login", "");
        if ($login === "") {
            return redirect("/eleve");
        }

        $eleve = DB::table("utilisateur")
            ->select("id", "nom", "prenom", "login", "mdp", "photo")
            ->where("login", $login)
            ->where("role", "eleve")
            ->first();

        if (!$eleve) {
            return redirect("/eleve");
        }

        return view("eleve.profil", [
            "pageTitle" => "Mon profil",
            "roleLabel" => "Eleve",
            "eleve" => $eleve,
        ]);
    }

    public function profilUpdate(Request $request)
    {
        $login = (string)$request->session()->get("user_login", "");
        if ($login === "") {
            return redirect("/eleve");
        }

        $eleve = DB::table("utilisateur")
            ->select("id", "mdp", "photo")
            ->where("login", $login)
            ->where("role", "eleve")
            ->first();

        if (!$eleve) {
            return redirect("/eleve");
        }

        $current = (string)$request->input("current_password", "");
        $new = (string)$request->input("new_password", "");
        $confirm = (string)$request->input("confirm_password", "");
        $hasPasswordInput = ($current !== "" || $new !== "" || $confirm !== "");
        $data = [];

        if ($hasPasswordInput) {
            if ($current === "" || $new === "" || $confirm === "" || $new !== $confirm) {
                return redirect("/eleve/profil")->with("error", "1");
            }

            $stored = (string)$eleve->mdp;
            $ok = password_verify($current, $stored) || hash_equals($stored, $current);
            if (!$ok) {
                return redirect("/eleve/profil")->with("error", "1");
            }

            $data["mdp"] = password_hash($new, PASSWORD_DEFAULT);
        }

        if ($request->hasFile("photo")) {
            $file = $request->file("photo");
            if (!$file || !$file->isValid()) {
                return redirect("/eleve/profil")->with("error", "photo");
            }

            $ext = strtolower((string)$file->getClientOriginalExtension());
            $allowed = ["jpg", "jpeg", "png", "webp"];
            if (!in_array($ext, $allowed, true)) {
                return redirect("/eleve/profil")->with("error", "photo");
            }

            $destDir = public_path("uploads/profiles");
            if (!is_dir($destDir)) {
                @mkdir($destDir, 0775, true);
            }

            $filename = "eleve_" . $eleve->id . "_" . time() . "." . $ext;
            $file->move($destDir, $filename);
            $data["photo"] = "/uploads/profiles/" . $filename;
        }

        if (empty($data)) {
            return redirect("/eleve/profil")->with("error", "2");
        }

        DB::table("utilisateur")->where("id", $eleve->id)->update($data);

        return redirect("/eleve/profil")->with("success", "1");
    }
}
