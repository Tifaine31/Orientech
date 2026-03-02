<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view("auth.login");
    }

    public function login(Request $request)
    {
        $login = trim((string)$request->input("login", ""));
        $password = (string)$request->input("password", "");

        if ($login === "" || $password === "") {
            return redirect("/connexion")->with("error", "1");
        }

        $user = DB::table("utilisateur")->where("login", $login)->first();
        if (!$user) {
            return redirect("/connexion")->with("error", "1");
        }
        $stored = (string)$user->mdp;
        $ok = password_verify($password, $stored) || hash_equals($stored, $password);
        if (!$ok) {
            return redirect("/connexion")->with("error", "1");
        }

        $role = $user->role ?? "eleve";

        $request->session()->put("role", $role);
        $request->session()->put("user_id", $user->id);
        $request->session()->put("user_login", $user->login);
        $request->session()->put("user_nom", $user->nom);
        $request->session()->put("user_prenom", $user->prenom);

        if ($role === "admin") {
            return redirect("/admin");
        }
        if ($role === "prof") {
            return redirect("/prof");
        }
        return redirect("/eleve");
    }

    public function logout(Request $request)
    {
        $request->session()->flush();
        return redirect("/connexion");
    }
}
