@extends('layouts.index')

@section('content')

<main class="mn-inner">
    <div class="row">
        <div class="col s12">
            <div class="page-title"><h5>Distributeurs</h5></div>
        </div>
        <div class="col s12 m12 l8">
            <div class="card">
                <div class="card-content">
                    <span class="card-title">Ajouter un Distributeur</span><br>
                    <div class="row">
                        <form class="col s12" action="{{ route('distrib.store') }}" method="post">
                            @csrf
                            <div class="row">
                                <div class="input-field col s2">
                                    <input name="distributeur_id" value="" type="text" class="validate">
                                    <label for="distributeur_id" class="active">Code Distributeur</label>

                                    @if($errors->has('distributeur_id'))
                                        <span class="red-text accent-4">{{ $errors->first('distributeur_id') }}</span>
                                    @endif

                                </div>
                                <div class="input-field col s3">
                                    <input name="nom_distributeur" value="" type="text" class="validate">
                                    <label for="nom_distributeur" class="active">Nom</label>

                                    @if($errors->has('nom_distributeur'))
                                        <span class="red-text accent-4">{{ $errors->first('nom_distributeur') }}</span>
                                    @endif

                                </div>
                                <div class="input-field col s4">
                                    <input name="pnom_distributeur" value="" type="text" class="validate">
                                    <label for="pnom_distributeur" class="active">Prénom</label>

                                    @if($errors->has('pnom_distributeur'))
                                        <span class="red-text accent-4">{{ $errors->first('pnom_distributeur') }}</span>
                                    @endif

                                </div>

                                <div class="input-field col s3">
                                    <input name="tel_distributeur" value="" type="text" class="validate">
                                    <label for="tel_distributeur" class="active">Téléphone</label>

                                    @if($errors->has('tel_distributeur'))
                                        <span class="red-text accent-4">{{ $errors->first('tel_distributeur') }}</span>
                                    @endif

                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s4">
                                    <input name="adress_distributeur" value="" type="text" class="validate">
                                    <label for="adress_distributeur" class="active">Adresse</label>

                                    @if($errors->has('adress_distributeur'))
                                        <span class="red-text accent-4">{{ $errors->first('adress_distributeur') }}</span>
                                    @endif

                                </div>
                                <div class="input-field col s2">
                                    <input name="etoiles_id" value="" type="text" class="validate">
                                    <label for="etoiles_id" class="active">Rang</label>

                                    @if($errors->has('etoiles_id'))
                                        <span class="red-text accent-4">{{ $errors->first('etoiles_id') }}</span>
                                    @endif

                                </div>
                                <div class="input-field col s2">
                                    <input name="new_cumul" value="" type="text" class="validate">
                                    <label for="new_cumul" class="active">New PV</label>

                                    @if($errors->has('new_cumul'))
                                        <span class="red-text accent-4">{{ $errors->first('new_cumul') }}</span>
                                    @endif

                                </div>
                                <div class="input-field col s2">
                                    <input name="cumul_individuel" value="" type="text" class="validate">
                                    <label for="cumul_individuel" class="active">Total PV</label>

                                    @if($errors->has('cumul_individuel'))
                                        <span class="red-text accent-4">{{ $errors->first('cumul_individuel') }}</span>
                                    @endif

                                </div>
                                <div class="input-field col s2">
                                    <input name="cumul_collectif" value="" type="text" class="validate">
                                    <label for="cumul_collectif" class="active">Cumulative PV</label>

                                    @if($errors->has('cumul_collectif'))
                                        <span class="red-text accent-4">{{ $errors->first('cumul_collectif') }}</span>
                                    @endif

                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s3">

                                    <input name="id_parent" value="" type="text" class="validate">
                                    <label for="id_parent" class="active">Référend</label>

                                    @if($errors->has('id_parent'))
                                        <span class="red-text accent-4">{{ $errors->first('id_parent') }}</span>
                                    @endif

                                </div>
                                <div class="input-field col s3">

                                    <input name="date_ins" value="" type="text" class="validate">
                                    <label for="date_ins" class="active">Date du Print-Out AU FORMAT : DD/MM/YYYY</label>

                                    @if($errors->has('date_ins'))
                                        <span class="red-text accent-4">{{ $errors->first('date_ins') }}</span>
                                    @endif

                                </div>
                            </div>
                            <div class="col s12 m-t-sm">
                                <button type="submit" class="waves-effect waves-light btn teal">Ajouter</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>


@endsection
