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
            // On récupère les données peu importe le format (JSON ou Form)
            $inputs = $request->all();
            $mode = $inputs['mode'] ?? 0;

            // --- MODE 1 : ADMIN (Enregistrement balise) ---
            if ($mode == 1 && isset($inputs['tag'])) {
                Balise::updateOrCreate(
                    ['tag' => trim($inputs['tag'])],
                    [
                        'lat' => $inputs['latitude'] ?? 0,
                        'lng' => $inputs['longitude'] ?? 0,
                        'alt' => $inputs['altitude'] ?? 0,
                    ]
                );
                return Response::json(['status' => 'Success', 'message' => 'Balise mise à jour'], 201);
            }

            // --- MODE 0 : SUIVI (Fonctionnement normal) ---
            if ($mode == 0) {
                $macRecue = $request->input('device_id'); // Correspond au JSON envoyé

                // Utilisation de firstOrCreate pour éviter de créer des doublons
                $boitier = Boitier::firstOrCreate(
                    ['device_id' => $macRecue],
                );

                $seance = \DB::table('seance')
                    ->where('id_boitier', $boitier->id)
                    ->orderBy('date_debut', 'desc')
                    ->first();

                if (!$seance) {
                    return Response::json(['status' => 'Error', 'message' => 'Aucune séance'], 404);
                }

                Localisation::create([
                    'latitude'   => $request->input('latitude'), // Noms exacts du JSON
                    'longitude'  => $request->input('longitude'),
                    'altitude'   => $request->input('altitude'),
                    'id_seance'  => $seance->id,
                    'id_boitier' => $boitier->id,
                ]);

                return Response::json(['status' => 'Success'], 201);
            }

            return Response::json(['status' => 'Error', 'message' => 'Mode non reconnu'], 400);

        } catch (\Exception $e) {
            return Response::json(['status' => 'Error', 'message' => $e->getMessage()], 500);
        }
    }
}
