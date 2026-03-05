<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $pageTitle ?? 'ORIENTECH95' }}</title>

    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v=1">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v=1">
    <link rel="apple-touch-icon" href="{{ asset('favicon.jpg') }}?v=1">
    <link rel="icon" type="image/jpeg" href="{{ asset('favicon.jpg') }}?v=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('style.css') }}">
</head>
<body class="bg-light">

<div class="container-fluid d-flex justify-content-between align-items-start py-3 px-4">
    <img src="{{ asset('logo_complet.png') }}" height="60" alt="ORIENTECH95">

    <div class="text-end">
        <div class="fw-bold text-orientech mb-2">
            {{ $roleLabel ?? 'Utilisateur' }}
        </div>
        <a class="btn btn-outline-success rounded-pill" href="{{ url('/logout') }}">
            Déconnexion
        </a>
    </div>
</div>

@yield('content')

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
