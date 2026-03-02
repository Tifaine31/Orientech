<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $pageTitle ?? 'ORIENTECH95 - Connexion' }}</title>

    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v=1">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v=1">
    <link rel="apple-touch-icon" href="{{ asset('favicon.jpg') }}?v=1">
    <link rel="icon" type="image/jpeg" href="{{ asset('favicon.jpg') }}?v=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('style.css') }}">
</head>
<body>

@yield('content')

</body>
</html>
