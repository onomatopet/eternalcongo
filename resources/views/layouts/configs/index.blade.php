@extends('layouts.index')

@section('content')

<main class="mn-inner">
    @if (session('status'))
        <h5 class="flash mt-2 flash-full flash-warn">{{ session('status') }}</h5>
    @endif
    <h5>CONFIGURATION</h5>
    <div class="row">

        <div class="col s6">
            <div class="card">
                <div class="card-content">
                    <span class="card-title">FORCER L'AMORCAGE DU NOUVEAU MOIS</span>
                    <div class="row">

                        <div class="col s12">
                            <p>Pour clôturer le mois en cours et initier un nouveau mois, veuillez cliquer sur le bouton ci-dessus</p>

                        </div>
                    </div>
                    <div class="row">
                        <div class="col s12 m-t-sm">
                            <a href="{{ route('configs.show', $grade)}}" class="btn btn-sm btn-primary">
                                <i class="material-icons left">add</i>CLOTURER LE MOIS EN COURS</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col s6">
            <div class="card">
                <div class="card-content">
                    <span class="card-title">AVANCEMENT EN GRADE</span>
                    <div class="row">

                        <div class="col s12">
                            <p>Pour forcer le calcul de l'avancements en grade de tous les distributeur, veuillez cliquer sur le bouton ci-dessous.</p>

                        </div>
                    </div>
                    <div class="row">
                        <div class="col s12 m-t-sm">
                            <a href="{{ route('configs.show', $grade)}}" target="_blank" class="btn btn-sm btn-primary"><i class="material-icons left">add</i>CALCULER L'AVANCENENT EN GRADE</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col s6">
            <div class="card">
                <div class="card-content">
                    <span class="card-title">INSERER AUTOMATIQUEMENT DE NOUVEAUX DISTRIBUTEURS</span>
                    <div class="row">

                        <div class="col s12">
                            <p>Pour insérer automatiquement de nouveaux distributeurs veuillez renseigner l'url du fichier txt à scanner.</p>

                        </div>
                    </div>
                    <div class="row">
                        <div class="col s12 m-t-sm">
                            <a href="#" target="_blank" class="btn btn-sm btn-primary"><i class="material-icons left">add</i>INSERER DES DISTRIBUTEURS</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="col s6">
            <div class="card">
                <div class="card-content">
                    <span class="card-title">CALCUL DU CUMUL INDIVIDUEL</span>
                    <div class="row">

                        <div class="col s12">
                            <p>Pour calculer le cumul individuel de tous les distributeur, veuillez cliquer sur le bouton ci-dessous</p>

                        </div>
                    </div>
                    <div class="row">
                        <div class="col s12 m-t-sm">
                            <a href="{{ route('configs.create')}}" target="_blank" class="btn btn-sm btn-primary"><i class="material-icons left">add</i>CALCULER LE CUMUL INDIVIDUEL</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>


</main>

@endsection
