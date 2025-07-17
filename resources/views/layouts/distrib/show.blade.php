<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Distributeur #{{ $distributeur->distributeur_id }}</title>
</head>
<body>
    <h1>Distributeur {{ $distributeur->distributeur_id }}</h1>

    @include('distributeurs.partials.children', ['distributeur' => $distributeur])
</body>
</html>
