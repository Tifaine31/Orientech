<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        return view("admin.dashboard", [
            "pageTitle" => "Espace admin",
            "roleLabel" => "Administrateur",
        ]);
    }

    public function logs(Request $request)
    {
        $filterSource = trim((string)$request->query("source", ""));
        $filterLevel = trim((string)$request->query("level", ""));
        $filterDate = trim((string)$request->query("date", ""));
        $filterUser = (int)$request->query("user", 0);

        $appLogsQuery = DB::table("app_logs")
            ->select(
                "created_at",
                "source",
                "level",
                "event_type",
                "message",
                "id_utilisateur",
                "path",
                "status_code",
                "ip_address"
            );

        if ($filterSource !== "") {
            $appLogsQuery->where("source", $filterSource);
        }
        if ($filterLevel !== "") {
            $appLogsQuery->where("level", $filterLevel);
        }
        if ($filterDate !== "") {
            $appLogsQuery->whereDate("created_at", $filterDate);
        }
        if ($filterUser > 0) {
            $appLogsQuery->where("id_utilisateur", $filterUser);
        }

        $appLogs = $appLogsQuery
            ->orderByDesc("id")
            ->limit(100)
            ->get();

        $logSources = DB::table("app_logs")
            ->select("source")
            ->distinct()
            ->orderBy("source")
            ->pluck("source");

        $logLevels = DB::table("app_logs")
            ->select("level")
            ->distinct()
            ->orderBy("level")
            ->pluck("level");

        $logUsers = DB::table("utilisateur as u")
            ->join("app_logs as l", "l.id_utilisateur", "=", "u.id")
            ->select("u.id", "u.nom", "u.prenom", "u.login")
            ->distinct()
            ->orderBy("u.nom")
            ->orderBy("u.prenom")
            ->get();

        $logsBoitiers = DB::table("boitier_etat_logs as l")
            ->join("boitier as b", "b.id", "=", "l.id_boitier")
            ->leftJoin("utilisateur as u", "u.id", "=", "l.id_utilisateur")
            ->select(
                "l.created_at",
                "l.ancien_etat",
                "l.nouvel_etat",
                "b.id as boitier_id",
                "b.mac as boitier_mac",
                "u.nom as user_nom",
                "u.prenom as user_prenom",
                "u.login as user_login"
            )
            ->orderByDesc("l.id")
            ->limit(100)
            ->get();

        return view("admin.logs", [
            "pageTitle" => "Espace admin",
            "roleLabel" => "Administrateur",
            "appLogs" => $appLogs,
            "logsBoitiers" => $logsBoitiers,
            "filterSource" => $filterSource,
            "filterLevel" => $filterLevel,
            "filterDate" => $filterDate,
            "filterUser" => $filterUser > 0 ? $filterUser : null,
            "logSources" => $logSources,
            "logLevels" => $logLevels,
            "logUsers" => $logUsers,
        ]);
    }

    public function utilisateurs()
    {
        $users = DB::table("utilisateur")
            ->select("id", "nom", "prenom", "login", "role")
            ->orderByDesc("id")
            ->get();

        return view("admin.utilisateurs", [
            "pageTitle" => "Espace administrateur",
            "roleLabel" => "Admin",
            "users" => $users,
        ]);
    }

    public function ajouterUtilisateur()
    {
        return view("admin.ajouter_utilisateur", [
            "pageTitle" => "Espace administrateur",
            "roleLabel" => "Admin",
        ]);
    }

    public function ajouterUtilisateurPost(Request $request)
    {
        $nom = $this->normalizeName((string)$request->input("nom", ""));
        $prenom = $this->normalizeName((string)$request->input("prenom", ""));
        $login = $this->normalizeLoginInput((string)$request->input("login", ""));
        $password = (string)$request->input("password", "");
        $acreditation = trim((string)$request->input("acreditation", ""));

        if ($nom === "" || $prenom === "" || $login === "" || $password === "" || $acreditation === "") {
            return redirect("/admin/utilisateurs/ajouter")->with("error", "1");
        }

        $acreditation = $this->normalizeRole($acreditation);

        $exists = DB::table("utilisateur")->where("login", $login)->exists();
        if ($exists) {
            return redirect("/admin/utilisateurs/ajouter")->with("error", "1");
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        DB::table("utilisateur")->insert([
            "nom" => $nom,
            "prenom" => $prenom,
            "login" => $login,
            "mdp" => $hash,
            "role" => $acreditation,
        ]);

        return redirect("/admin/utilisateurs")->with("success", "1");
    }

    public function modifierUtilisateur(Request $request, int $id)
    {
        $user = DB::table("utilisateur")
            ->select("id", "nom", "prenom", "login", "role")
            ->where("id", $id)
            ->first();
        if (!$user) {
            return redirect("/admin/utilisateurs");
        }

        return view("admin.modifier_utilisateur", [
            "pageTitle" => "Espace administrateur",
            "roleLabel" => "Admin",
            "user" => $user,
        ]);
    }

    public function modifierUtilisateurPost(Request $request, int $id)
    {
        $nom = $this->normalizeName((string)$request->input("nom", ""));
        $prenom = $this->normalizeName((string)$request->input("prenom", ""));
        $login = $this->normalizeLoginInput((string)$request->input("login", ""));
        $password = (string)$request->input("password", "");
        $acreditation = trim((string)$request->input("acreditation", ""));

        if ($id <= 0 || $nom === "" || $prenom === "" || $login === "" || $acreditation === "") {
            return redirect("/admin/utilisateurs/" . $id . "/modifier")->with("error", "1");
        }

        $acreditation = $this->normalizeRole($acreditation);

        $exists = DB::table("utilisateur")
            ->where("login", $login)
            ->where("id", "<>", $id)
            ->exists();
        if ($exists) {
            return redirect("/admin/utilisateurs/" . $id . "/modifier")->with("error", "1");
        }

        $data = [
            "nom" => $nom,
            "prenom" => $prenom,
            "login" => $login,
            "role" => $acreditation,
        ];
        if ($password !== "") {
            $data["mdp"] = password_hash($password, PASSWORD_DEFAULT);
        }

        DB::table("utilisateur")
            ->where("id", $id)
            ->update($data);

        return redirect("/admin/utilisateurs")->with("updated", "1");
    }

    public function supprimerUtilisateur(Request $request, int $id)
    {
        if ($id > 0) {
            DB::table("utilisateur")->where("id", $id)->delete();
        }
        return redirect("/admin/utilisateurs");
    }

    public function ajouterBalise()
    {
        $boitiers = DB::table("boitier")
            ->select("id", "mac", "etat", "reseau")
            ->orderBy("id")
            ->get();

        return view("admin.ajouter_balise", [
            "pageTitle" => "Espace administrateur",
            "roleLabel" => "Admin",
            "boitiers" => $boitiers,
            "scanContext" => session("balise_scan_context"),
        ]);
    }

    public function ajouterBalisePost(Request $request)
    {
        $boitierId = (int)$request->input("boitier_id", 0);
        if ($boitierId <= 0) {
            return redirect("/admin/balises/ajouter")->with("error", "1");
        }

        $boitier = DB::table("boitier")
            ->select("id", "mac", "etat", "reseau")
            ->where("id", $boitierId)
            ->first();
        if (!$boitier) {
            return redirect("/admin/balises/ajouter")->with("error", "1");
        }

        session([
            "balise_scan_context" => [
                "boitier_id" => $boitier->id,
                "boitier_mac" => (string)$boitier->mac,
                "started_at" => now()->format("Y-m-d H:i:s"),
            ],
        ]);

        return redirect("/admin/balises/ajouter")->with("success", "1");
    }

    public function ajouterBaliseStop()
    {
        session()->forget("balise_scan_context");
        return redirect("/admin/balises/ajouter")->with("stopped", "1");
    }

    private function normalizeText(string $s): string
    {
        $s = trim($s);
        if ($s === "") {
            return $s;
        }
        $s = iconv("UTF-8", "ASCII//TRANSLIT", $s);
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
        $s = iconv("UTF-8", "ASCII//TRANSLIT", $s);
        $s = strtolower($s);
        $s = preg_replace("/[^a-z0-9._-]/", "", $s);
        $s = preg_replace("/\\.{2,}/", ".", $s);
        $s = preg_replace("/_{2,}/", "_", $s);
        $s = preg_replace("/-{2,}/", "-", $s);
        $s = trim($s, "._-");
        return $s;
    }

    private function normalizeRole(string $value): string
    {
        $map = [
            "admin" => "admin",
            "prof" => "prof",
            "eleve" => "eleve",
            "Admin" => "admin",
            "Prof" => "prof",
            "Eleve" => "eleve",
            "ELEVE" => "eleve",
        ];
        return $map[$value] ?? $value;
    }
}

