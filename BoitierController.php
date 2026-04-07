<?php

namespace App\Http\Controllers;

use App\Models\Boitier;
use App\Models\Balise; // On utilise Balise (pas BaliseRfid)
use App\Models\Localisation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class BoitierController extends Controller
{
    public function store(Request $request)
    {
        try {

            // Validation globale
            $request->validate([
                'mode' => 'required|integer',
                'devEui' => 'required_if:mode,0|string|max:255',
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
                'altitude' => 'nullable|numeric',
                'tag' => 'nullable|string|max:255',
            ]);

            $mode = $request->input('mode');

            // -------------------------
            //  MODE ADMIN (sécurisé)
            // -------------------------
            if ($mode == 1) {

                //  Protection simple (token admin)
                $adminToken = $request->header('X-ADMIN-TOKEN');

                if ($adminToken !== config('app.admin_token')) {
                    return response()->json(['error' => 'Unauthorized'], 403);
                }

                if (!$request->filled('tag')) {
                    return response()->json(['error' => 'Tag requis'], 400);
                }

                Balise::updateOrCreate(
                    ['tag' => trim($request->input('tag'))],
                    [
                        'lat' => $request->input('latitude', 0),
                        'lng' => $request->input('longitude', 0),
                        'alt' => $request->input('altitude', 0),
                    ]
                );

                return response()->json(['status' => 'Success'], 201);
            }

            // -------------------------
            //  MODE NORMAL (boîtier)
            // -------------------------
            if ($mode == 0) {

                $macRecue = $request->input('devEui');

                //  plus de création automatique !
                $boitier = Boitier::where('devEui', $macRecue)->first();

                if (!$boitier) {
                    return response()->json(['error' => 'Boitier inconnu'], 403);
                }

                $seance = DB::table('seance')
                    ->where('id_boitier', $boitier->id)
                    ->orderBy('date_debut', 'desc')
                    ->first();

                if (!$seance) {
                    return response()->json(['error' => 'Aucune séance'], 404);
                }

                Localisation::create([
                    'latitude' => $request->input('latitude'),
                    'longitude' => $request->input('longitude'),
                    'altitude' => $request->input('altitude'),
                    'id_seance' => $seance->id,
                    'id_boitier' => $boitier->id,
                ]);

                return response()->json(['status' => 'Success'], 201);
            }

            return response()->json(['error' => 'Mode invalide'], 400);

        } catch (\Exception $e) {

            \Log::error($e);

            return response()->json([
                'status' => 'Error',
                'message' => 'Erreur serveur'
            ], 500);
        }
    }
}
