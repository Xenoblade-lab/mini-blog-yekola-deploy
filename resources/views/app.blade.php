{{--
    Layout racine — Partie publique du blog
    Les vues enfants utilisent @extends('app') et définissent @section('title') et @section('content')
--}}
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- @yield avec valeur par défaut si la vue enfant ne définit pas de titre --}}
    <title>@yield('title', 'Le Blog')</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=DM+Sans:wght@300;400;500&display=swap"
        rel="stylesheet">
    {{-- asset() génère l'URL vers public/css/public.css --}}
    <link rel="stylesheet" href="{{ asset('css/public.css') }}">
</head>

<body>

    @include('components.header')

    @yield('content')

    @include('components.footer')

</body>

</html>
