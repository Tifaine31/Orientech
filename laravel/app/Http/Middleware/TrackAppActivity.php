<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class TrackAppActivity
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($this->shouldSkip($request)) {
            return $response;
        }

        try {
            $userId = null;
            if ($request->hasSession()) {
                $userId = $request->session()->get("user_id");
            }

            DB::table("app_logs")->insert([
                "source" => "app",
                "level" => $response->getStatusCode() >= 400 ? "error" : "info",
                "event_type" => "http_request",
                "message" => $request->method() . " " . $request->path(),
                "context" => json_encode([
                    "query" => $request->query(),
                ], JSON_UNESCAPED_UNICODE),
                "id_utilisateur" => $userId,
                "http_method" => $request->method(),
                "path" => "/" . ltrim($request->path(), "/"),
                "status_code" => $response->getStatusCode(),
                "ip_address" => (string)$request->ip(),
                "created_at" => now(),
            ]);
        } catch (\Throwable $e) {
            Log::warning("track_app_activity_failed", [
                "message" => $e->getMessage(),
                "path" => $request->path(),
            ]);
        }

        return $response;
    }

    private function shouldSkip(Request $request): bool
    {
        $path = ltrim($request->path(), "/");
        if ($path === "") {
            return false;
        }

        if (str_starts_with($path, "_debugbar") || str_starts_with($path, "vendor")) {
            return true;
        }

        if ($path === "api/logs" || $path === "api/logs/ingest") {
            return true;
        }

        return false;
    }
}
