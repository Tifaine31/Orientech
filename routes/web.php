<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EleveController;

Route::get("/", function () {
    return redirect("/connexion");
});

Route::get("/connexion", [AuthController::class, "showLogin"]);
Route::post("/login", [AuthController::class, "login"]);
Route::get("/logout", [AuthController::class, "logout"]);

// Legacy entry points used by the former PHP app.
Route::get("/index.html", fn () => redirect("/connexion"));
Route::get("/connexion.html", fn () => redirect("/connexion"));
Route::get("/connexion.php", fn () => redirect("/connexion"));
Route::post("/login.php", [AuthController::class, "login"]);
Route::get("/logout.php", [AuthController::class, "logout"]);

Route::middleware(["session.auth", "role:prof"])->prefix("prof")->group(function () {
    Route::get("/", [ProfController::class, "dashboard"]);
    Route::get("/gestion-classes", [ProfController::class, "gestionClasses"]);
    Route::post("/gestion-classes", [ProfController::class, "gestionClassesCreate"]);
    Route::post("/gestion-classes/{id}/delete", [ProfController::class, "gestionClassesDelete"]);
    Route::get("/gestion-eleves", [ProfController::class, "gestionEleves"]);
    Route::post("/gestion-eleves", [ProfController::class, "gestionElevesCreate"]);
    Route::post("/gestion-eleves/affecter", [ProfController::class, "gestionElevesAffecter"]);
    Route::post("/gestion-eleves/{id}/delete", [ProfController::class, "gestionElevesDelete"]);
    Route::post("/gestion-eleves/delete-all", [ProfController::class, "gestionElevesDeleteAll"]);
    Route::get("/gestion-seances", [ProfController::class, "gestionSeances"]);
    Route::post("/gestion-seances", [ProfController::class, "gestionSeancesCreate"]);
    Route::post("/gestion-seances/{id}/delete", [ProfController::class, "gestionSeancesDelete"]);
    Route::get("/seance-detail", [ProfController::class, "seanceDetail"]);
    Route::post("/seance-detail/note", [ProfController::class, "seanceAddNoteManuelle"]);
    Route::get("/historique-seances", [ProfController::class, "historiqueSeances"]);
    Route::get("/historique-seances/export", [ProfController::class, "historiqueSeancesExport"]);
    Route::get("/seance-trajet", [ProfController::class, "seanceTrajet"]);
    Route::get("/gestion-boitiers", [ProfController::class, "gestionBoitiers"]);
    Route::post("/gestion-boitiers", [ProfController::class, "gestionBoitiersCreate"]);
    Route::post("/gestion-boitiers/{id}/etat", [ProfController::class, "gestionBoitiersUpdateEtat"]);
    Route::post("/gestion-boitiers/{id}/delete", [ProfController::class, "gestionBoitiersDelete"]);
    Route::get("/gestion-parcours", [ProfController::class, "gestionParcours"]);
    Route::post("/gestion-parcours", [ProfController::class, "gestionParcoursCreate"]);
    Route::post("/gestion-parcours/{id}/delete", [ProfController::class, "gestionParcoursDelete"]);
    Route::get("/modifier-parcours", [ProfController::class, "modifierParcours"]);
    Route::post("/modifier-parcours", [ProfController::class, "modifierParcoursUpdate"]);
    Route::post("/modifier-parcours/add-balise", [ProfController::class, "modifierParcoursAddBalise"]);
    Route::post("/modifier-parcours/update-ordre", [ProfController::class, "modifierParcoursUpdateOrdre"]);
    Route::get("/modifier-parcours/remove-balise", [ProfController::class, "modifierParcoursRemoveBalise"]);
    Route::get("/modifier-classe", [ProfController::class, "modifierClasse"]);
    Route::post("/modifier-classe/rename", [ProfController::class, "modifierClasseRename"]);
    Route::post("/modifier-classe/add-eleve", [ProfController::class, "modifierClasseAddEleve"]);
    Route::post("/modifier-classe/remove-eleve", [ProfController::class, "modifierClasseRemoveEleve"]);
    Route::post("/modifier-classe/import-csv", [ProfController::class, "modifierClasseImportCsv"]);
});

// Legacy professor pages/actions.
Route::middleware(["session.auth", "role:prof"])->prefix("prof")->group(function () {
    Route::get("/prof.php", fn () => redirect("/prof"));
    Route::get("/gestion_classes.php", fn () => redirect("/prof/gestion-classes"));
    Route::post("/gestion_classes_action.php", [ProfController::class, "gestionClassesCreate"]);
    Route::get("/gestion_classes_delete.php", function (\Illuminate\Http\Request $request) {
        $id = (int)$request->query("id_classe", 0);
        return app(ProfController::class)->gestionClassesDelete($request, $id);
    });
    Route::get("/gestion_eleves.php", fn () => redirect("/prof/gestion-eleves"));
    Route::post("/gestion_eleves_action.php", [ProfController::class, "gestionElevesCreate"]);
    Route::post("/gestion_eleves_affecter.php", [ProfController::class, "gestionElevesAffecter"]);
    Route::get("/gestion_eleves_supprimer.php", function (\Illuminate\Http\Request $request) {
        $id = (int)$request->query("id", 0);
        return app(ProfController::class)->gestionElevesDelete($request, $id);
    });
    Route::post("/gestion_eleves_supprimer_tout.php", [ProfController::class, "gestionElevesDeleteAll"]);
    Route::get("/gestion_seances.php", fn () => redirect("/prof/gestion-seances"));
    Route::post("/gestion_seances_action.php", [ProfController::class, "gestionSeancesCreate"]);
    Route::get("/gestion_seances_delete.php", function (\Illuminate\Http\Request $request) {
        $id = (int)$request->query("id", 0);
        return app(ProfController::class)->gestionSeancesDelete($request, $id);
    });
    Route::get("/historique_seances.php", fn () => redirect("/prof/historique-seances"));
    Route::get("/historique_seances_export.php", [ProfController::class, "historiqueSeancesExport"]);
    Route::get("/seance_detail.php", fn (\Illuminate\Http\Request $request) => redirect("/prof/seance-detail?id=" . (int)$request->query("id", 0)));
    Route::get("/seance_trajet.php", fn (\Illuminate\Http\Request $request) => redirect("/prof/seance-trajet?id=" . (int)$request->query("id", 0)));
    Route::post("/seance_add_note_manuelle.php", [ProfController::class, "seanceAddNoteManuelle"]);
    Route::get("/gestion_boitier.php", fn () => redirect("/prof/gestion-boitiers"));
    Route::post("/gestion_boitier_action.php", [ProfController::class, "gestionBoitiersCreate"]);
    Route::get("/gestion_boitier_delete.php", function (\Illuminate\Http\Request $request) {
        $id = (int)$request->query("id", 0);
        return app(ProfController::class)->gestionBoitiersDelete($request, $id);
    });
    Route::get("/gestion_parcours.php", fn () => redirect("/prof/gestion-parcours"));
    Route::post("/gestion_parcours_action.php", [ProfController::class, "gestionParcoursCreate"]);
    Route::get("/gestion_parcours_delete.php", function (\Illuminate\Http\Request $request) {
        $id = (int)$request->query("id", 0);
        return app(ProfController::class)->gestionParcoursDelete($request, $id);
    });
    Route::get("/modifier_parcours.php", fn (\Illuminate\Http\Request $request) => redirect("/prof/modifier-parcours?id=" . (int)$request->query("id", 0)));
    Route::post("/modifier_parcours_action.php", [ProfController::class, "modifierParcoursUpdate"]);
    Route::post("/modifier_parcours_add_balise.php", [ProfController::class, "modifierParcoursAddBalise"]);
    Route::post("/modifier_parcours_update_ordre.php", [ProfController::class, "modifierParcoursUpdateOrdre"]);
    Route::get("/modifier_parcours_remove_balise.php", [ProfController::class, "modifierParcoursRemoveBalise"]);
    Route::get("/modifier_classe.php", fn (\Illuminate\Http\Request $request) => redirect("/prof/modifier-classe?id_classe=" . (int)$request->query("id_classe", 0)));
    Route::post("/modifier_classe_rename.php", [ProfController::class, "modifierClasseRename"]);
    Route::post("/modifier_classe_add_eleve.php", [ProfController::class, "modifierClasseAddEleve"]);
    Route::get("/modifier_classe_remove_eleve.php", function (\Illuminate\Http\Request $request) {
        $request->merge([
            "id_classe" => (int)$request->query("id_classe", 0),
            "id_utilisateur" => (int)$request->query("id_utilisateur", 0),
        ]);
        return app(ProfController::class)->modifierClasseRemoveEleve($request);
    });
    Route::post("/modifier_classe_import_csv.php", [ProfController::class, "modifierClasseImportCsv"]);
});

Route::middleware(["session.auth", "role:admin"])->prefix("admin")->group(function () {
    Route::get("/", [AdminController::class, "dashboard"]);
    Route::get("/logs", [AdminController::class, "logs"]);
    Route::get("/utilisateurs", [AdminController::class, "utilisateurs"]);
    Route::get("/utilisateurs/ajouter", [AdminController::class, "ajouterUtilisateur"]);
    Route::post("/utilisateurs/ajouter", [AdminController::class, "ajouterUtilisateurPost"]);
    Route::get("/utilisateurs/{id}/modifier", [AdminController::class, "modifierUtilisateur"]);
    Route::post("/utilisateurs/{id}/modifier", [AdminController::class, "modifierUtilisateurPost"]);
    Route::post("/utilisateurs/{id}/supprimer", [AdminController::class, "supprimerUtilisateur"]);
    Route::get("/balises/ajouter", [AdminController::class, "ajouterBalise"]);
    Route::post("/balises/ajouter", [AdminController::class, "ajouterBalisePost"]);
    Route::post("/balises/ajouter/stop", [AdminController::class, "ajouterBaliseStop"]);
});

// Legacy admin pages/actions.
Route::middleware(["session.auth", "role:admin"])->prefix("admin")->group(function () {
    Route::get("/admin.php", fn () => redirect("/admin"));
    Route::get("/admin_logs.php", fn () => redirect("/admin/logs"));
    Route::get("/admin_utilisateurs.php", fn () => redirect("/admin/utilisateurs"));
    Route::get("/admin_ajouter_utilisateur.php", fn () => redirect("/admin/utilisateurs/ajouter"));
    Route::post("/admin_ajouter_utilisateur_action.php", [AdminController::class, "ajouterUtilisateurPost"]);
    Route::get("/admin_modifier_utilisateur.php", fn (\Illuminate\Http\Request $request) => redirect("/admin/utilisateurs/" . (int)$request->query("id", 0) . "/modifier"));
    Route::post("/admin_modifier_utilisateur_action.php", function (\Illuminate\Http\Request $request) {
        $id = (int)$request->input("id_utilisateur", 0);
        return app(AdminController::class)->modifierUtilisateurPost($request, $id);
    });
    Route::get("/admin_supprimer_utilisateur.php", function (\Illuminate\Http\Request $request) {
        $id = (int)$request->query("id", 0);
        return app(AdminController::class)->supprimerUtilisateur($request, $id);
    });
});

Route::middleware(["session.auth", "role:eleve"])->prefix("eleve")->group(function () {
    Route::get("/", [EleveController::class, "dashboard"]);
    Route::get("/historique", [EleveController::class, "historique"]);
    Route::get("/historique/trajet", [EleveController::class, "historiqueTrajet"]);
    Route::get("/profil", [EleveController::class, "profil"]);
    Route::post("/profil", [EleveController::class, "profilUpdate"]);
});

// Legacy student pages/actions.
Route::middleware(["session.auth", "role:eleve"])->prefix("eleve")->group(function () {
    Route::get("/eleve.php", fn () => redirect("/eleve"));
    Route::get("/historique_resultats_eleve.php", fn () => redirect("/eleve/historique"));
    Route::get("/modifier_profil.php", fn () => redirect("/eleve/profil"));
    Route::post("/modifier_profil_action.php", [EleveController::class, "profilUpdate"]);
});
