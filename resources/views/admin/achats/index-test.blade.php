@extends('layouts.admin')

@section('content')
<!DOCTYPE html>
<html>
<head>
    <title>Achats Test</title>
</head>
<body>
    <h1>🎉 Achats Sans Layout</h1>
    <p>Cette page fonctionne sans layout Blade.</p>
    <p>Si vous voyez ceci, le problème venait du layout.</p>
    
    <a href="/admin">Retour Dashboard</a> | 
    <a href="/admin/distributeurs">Test Distributeurs</a>
</body>
</html>
@endsection