<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AppLogController extends Controller
{
    public function ingest(Request $request)
    {
        $apiKey = (string)env("APP_LOG_API_KEY", "");
        $givenKey = (string)$request->header("X-Log-Key", "");
        if ($apiKey === "" || $givenKey === "" || !hash_equals($apiKey, $givenKey)) {
            return response()->json(["ok" => false, "error" => "unauthorized"], 401);
        }

        $message = trim((string)$request->input("message", ""));
        $source = trim((string)$request->input("source", "api"));
        $level = trim((string)$request->input("level", "info"));
        $eventType = trim((string)$request->input("event_type", "external_event"));
        $context = $request->input("context");
        $idUtilisateur = $request->input("id_utilisateur");
        $statusCode = $request->input("status_code");

        if ($message === "") {
            return response()->json(["ok" => false, "error" => "message_required"], 422);
        }

        if (!is_array($context)) {
            $context = null;
        }

        DB::table("app_logs")->insert([
            "source" => substr($source !== "" ? $source : "api", 0, 40),
            "level" => substr($level !== "" ? $level : "info", 0, 20),
            "event_type" => substr($eventType !== "" ? $eventType : "external_event", 0, 80),
            "message" => $message,
            "context" => $context ? json_encode($context, JSON_UNESCAPED_UNICODE) : null,
            "id_utilisateur" => is_numeric((string)$idUtilisateur) ? (int)$idUtilisateur : null,
            "http_method" => $request->method(),
            "path" => "/" . ltrim($request->path(), "/"),
            "status_code" => is_numeric((string)$statusCode) ? (int)$statusCode : 200,
            "ip_address" => (string)$request->ip(),
            "created_at" => now(),
        ]);

        return response()->json(["ok" => true], 201);
    }
}

