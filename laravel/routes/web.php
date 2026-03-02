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

Route::middleware(["session.auth", "role:eleve"])->prefix("eleve")->group(function () {
    Route::get("/", [EleveController::class, "dashboard"]);
    Route::get("/historique", [EleveController::class, "historique"]);
    Route::get("/historique/trajet", [EleveController::class, "historiqueTrajet"]);
    Route::get("/profil", [EleveController::class, "profil"]);
    Route::post("/profil", [EleveController::class, "profilUpdate"]);
});
