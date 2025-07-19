@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h1>🎉 Distributeurs Test</h1>
    
    <div class="alert alert-info">
        <h4>✅ La vue distributeurs fonctionne !</h4>
        <p>Le layout admin et la navigation fonctionnent correctement.</p>
    </div>

    <div class="card">
        <div class="card-header">Test sans contrôleur</div>
        <div class="card-body">
            <p>Cette vue fonctionne sans passer par le contrôleur DistributeurController.</p>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Retour dashboard</a>
        </div>
    </div>
</div>
@endsection