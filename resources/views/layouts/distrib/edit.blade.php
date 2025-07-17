@extends('layouts.index')

@section('content')

<main class="mn-inner">
    <div class="row">
        <div class="col s12">
            <div class="page-title"><h5>Modifier le Distributeur : {{ $distributeurs[0]->distributeur_id }} - {{ $distributeurs[0]->nom_distributeur }} {{ $distributeurs[0]->pnom_distributeur }}</h5></div>
        </div>
        <div class="col s12 m12 l8">
            <div class="card">
                <div class="card-content">
                    <span class="card-title">Ajouter un Distributeur</span><br>
                    <div class="row">
                        <form class="col s12" action="{{ route('distrib.update', $distributeurs[0]->distributeur_id) }}" method="post">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="input-field col s2">
                                    <input name="distributeur_id" value="{{ $distributeurs[0]->distributeur_id }}" type="text" class="validate">
                                    <label for="distributeur_id" class="active">Code Distributeur</label>

                                    @if($errors->has('distributeur_id'))
                                        <span class="red-text accent-4">{{ $errors->first('distributeur_id') }}</span>
                                    @endif

                                </div>
                                <div class="input-field col s2">
                                    <input name="id_distrib_parent" value="{{ $distributeurs[0]->id_distrib_parent }}" type="text" class="validate">
                                    <label for="id_distrib_parent" class="active">ID réfrérent</label>

                                    @if($errors->has('id_distrib_parent'))
                                        <span class="red-text accent-4">{{ $errors->first('id_distrib_parent') }}</span>
                                    @endif

                                </div>
                                <div class="input-field col s3">
                                    <input name="nom_distributeur" value="{{ $distributeurs[0]->nom_distributeur }}" type="text" class="validate">
                                    <label for="nom_distributeur" class="active">Nom</label>

                                    @if($errors->has('nom_distributeur'))
                                        <span class="red-text accent-4">{{ $errors->first('nom_distributeur') }}</span>
                                    @endif

                                </div>
                                <div class="input-field col s3">
                                    <input name="pnom_distributeur" value="{{ $distributeurs[0]->pnom_distributeur }}" type="text" class="validate">
                                    <label for="pnom_distributeur" class="active">Prénom</label>

                                    @if($errors->has('pnom_distributeur'))
                                        <span class="red-text accent-4">{{ $errors->first('pnom_distributeur') }}</span>
                                    @endif

                                </div>
                                <div class="input-field col s2">
                                    <input name="tel_distributeur" value="{{ $distributeurs[0]->tel_distributeur }}" type="text" class="validate">
                                    <label for="tel_distributeur" class="active">Téléphone</label>

                                    @if($errors->has('tel_distributeur'))
                                        <span class="red-text accent-4">{{ $errors->first('tel_distributeur') }}</span>
                                    @endif

                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s4">
                                    <input name="adress_distributeur" value="{{ $distributeurs[0]->adress_distributeur }}" type="text" class="validate">
                                    <label for="adress_distributeur" class="active">Adresse</label>

                                    @if($errors->has('adress_distributeur'))
                                        <span class="red-text accent-4">{{ $errors->first('adress_distributeur') }}</span>
                                    @endif

                                </div>
                                <div class="input-field col s2">
                                    <input disabled name="etoiles_id" value="{{ $distributeurs[0]->etoiles_id }}" type="text" class="validate">
                                    <label for="etoiles_id" class="active">Etoiles</label>

                                    @if($errors->has('etoiles_id'))
                                        <span class="red-text accent-4">{{ $errors->first('etoiles_id') }}</span>
                                    @endif
                                </div>
                                <div class="input-field col s2">
                                    <input disabled name="new_cumul" value="{{ $distributeurs[0]->new_cumul }}" type="text" class="validate">
                                    <label for="new_cumul" class="active">New cumul</label>

                                    @if($errors->has('adress_distributeur'))
                                        <span class="red-text accent-4">{{ $errors->first('new_cumul') }}</span>
                                    @endif

                                </div>
                                <div class="input-field col s2">
                                    <input disabled name="cumul_individuel" value="{{ $distributeurs[0]->cumul_total }}" type="text" class="validate">
                                    <label for="cumul_individuel" class="active">Total PV</label>

                                    @if($errors->has('cumul_individuel'))
                                        <span class="red-text accent-4">{{ $errors->first('cumul_total') }}</span>
                                    @endif

                                </div>
                                <div class="input-field col s2">
                                    <input disabled name="cumul_collectif" value="{{ $distributeurs[0]->cumul_collectif }}" type="text" class="validate">
                                    <label for="cumul_collectif" class="active">Cumulative PV</label>

                                    @if($errors->has('cumul_collectif'))
                                        <span class="red-text accent-4">{{ $errors->first('cumul_collectif') }}</span>
                                    @endif

                                </div>
                            </div>
                            <div class="col s12 m-t-sm">
                                <button type="submit" class="waves-effect waves-light btn teal">Modifier</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>


@endsection
