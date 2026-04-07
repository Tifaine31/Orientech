<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Balise;
use App\Models\Boitier;
use App\Models\ComposeParcours;
use App\Models\ScanSeance;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class BaliseValidateController extends Controller
{
    public function validateTag(Request $request)
    {
        try {

            // ✅ Validation
            $request->validate([
                'tag' => 'required|string|max:255',
                'devEui' => 'required|string|max:255',
            ]);

            $tagId = trim($request->input('tag'));

            if ($tagId === '') {
                return response()->json(['error' => 'Tag vide'], 400);
            }

            // 1. Balise
            $balise = Balise::where('tag', $tagId)->first();
            if (!$balise) {
                return response()->json(['error' => 'Balise inconnue'], 404);
            }

            // 2. Boîtier
            $boitier = Boitier::where('devEui', $request->input('devEui'))->first();
            if (!$boitier) {
                return response()->json(['error' => 'Boitier inconnu'], 403);
            }

            // 3. Séance
            $seance = DB::table('seance')
                ->where('id_boitier', $boitier->id)
                ->orderBy('date_debut', 'desc')
                ->first();

            if (!$seance) {
                return response()->json(['error' => 'Aucune séance'], 404);
            }

            // 4. Vérification parcours
            $isCorrectPath = ComposeParcours::where('id_parcours', $seance->id_parcours)
                ->where('id_balise', $balise->id)
                ->exists();

            $valideStatus = $isCorrectPath ? 1 : 0;

            // 5. Déjà validé
            if ($valideStatus == 1) {
                $alreadyValidated = DB::table('scan_seance')
                    ->where('id_seance', $seance->id)
                    ->where('id_balise', $balise->id)
                    ->where('valide', 1)
                    ->exists();

                if ($alreadyValidated) {
                    return response()->json([
                        'status' => 'AlreadyDone'
                    ], 200);
                }
            }

            // 6. Enregistrement
            DB::table('scan_seance')->insert([
                'id_seance'  => $seance->id,
                'id_balise'  => $balise->id,
                'id_eleve'   => $seance->id_eleve,
                'valide'     => $valideStatus,
                'heure_scan' => now()
            ]);

            return response()->json([
                'status' => $valideStatus ? 'Success' : 'WrongPath',
                'valide' => $valideStatus
            ], 201);

        } catch (\Exception $e) {

            \Log::error($e);

            return response()->json([
                'status' => 'Error',
                'message' => 'Erreur serveur'
            ], 500);
        }
    }
    }
