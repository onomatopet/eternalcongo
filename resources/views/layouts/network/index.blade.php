@extends('layouts.index')

@section('content')

<main class="mn-inner">
    <div class="row">
        <div class="col s12">
            <div class="card">
                <div class="card-content">

                    <span class="card-title">DETAILS - NETWORK</span>
                    <div class="row">

                        <form class="col s12" action="network/create" target="_blank" method="GET">
                        <div class="input-field col s3">
                            <p>Veuillez selectionnez l'ID pou afficher son details network.</p>
                                @csrf
                                @method('GET')

                                <select class="js-states browser-default id_produit" name="distributeur_id" style="width: 100%" class="validate">
                                    <option value="" disabled selected>Secltionner le Distributeur à afficher</option>
                                    @foreach( $distributeurs as $lines )
                                        <option value={{ $lines->distributeur_id }} title="{{ $lines->id }}">{{ $lines->distributeur_id.'-'.$lines->nom_distributeur.' '.$lines->pnom_distributeur }}</option>
                                    @endforeach
                                </select>

                                @if($errors->has('distributeur_id'))
                                    <span class="red-text accent-4">{{ $errors->first('distributeur_id') }}</span>
                                @endif
                        </div>

                        <div class="input-field col s3">
                            <p>Veuillez selectionnez la période</p>

                                    <select class="js-states browser-default id_produit" name="period" style="width: 100%" class="validate">
                                        <option value="" disabled selected>Secltionner la période</option>
                                        @foreach( $period as $lines )
                                            <option value={{ $lines->period }}>{{ $lines->period }}</option>
                                        @endforeach
                                    </select>

                                    @if($errors->has('period'))
                                        <span class="red-text accent-4">{{ $errors->first('period') }}</span>
                                    @endif
                        </div>

                        <div class="col s12 m-t-sm">
                            <button type="submit" class="waves-effect waves-light btn teal">PRODUIRE LE PRINTOUT</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</main>

@endsection


