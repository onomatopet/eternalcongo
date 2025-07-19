@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h1>🎉 Dashboard Admin Test</h1>
    
    <div class="alert alert-success">
        <h4>✅ Le layout admin fonctionne !</h4>
        <p><strong>Utilisateur :</strong> {{ auth()->user()->name }}</p>
        <p><strong>Role ID :</strong> {{ auth()->user()->role_id }}</p>
        <p><strong>Date :</strong> {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Test Navigation</div>
                <div class="card-body">
                    <a href="{{ route('admin.distributeurs.index') }}" class="btn btn-primary">Test Distributeurs</a>
                    <a href="{{ route('admin.achats.index') }}" class="btn btn-success">Test Achats</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection