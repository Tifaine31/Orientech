<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!$request->session()->has("role")) {
            return redirect("/connexion");
        }

        if ($request->session()->get("role") !== $role) {
            abort(403, "Accès refusé.");
        }

        return $next($request);
    }
}
