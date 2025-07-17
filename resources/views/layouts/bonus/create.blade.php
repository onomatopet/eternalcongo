@extends('layouts.index')

@section('content')

<main class="mn-inner">
    <div class="row">
        <div class="col s12">
            <div class="card">
                <div class="card-content">
                    <span class="card-title">BONUS - POUR PRODUIRE LE BONUS</span>
                    <div class="row">

                        <div class="col s3">
                            <p>Veuillez selectionner le Distributeur demandeur de Bonus et valider pour produire le bonus.</p>

                                <form class="col s12" action="{{ route('bonus.store') }}" target="_blank" method="post">
                                    @csrf

                                    <select class="js-states browser-default" name="distributeur_id" tabindex="-1" style="width: 100%" id="distributeur_id" class="validate">
                                    <optgroup label="Selectionnez le Distributeur">
                                        @if($distributeurs)

                                            @foreach($distributeurs as $lines)
                                                <option value={{ $lines->distributeur_id }}>{{ $lines->distributeur_id }}</option>
                                            @endforeach

                                        @endif

                                    </optgroup>
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

                    </div>
                    <div class="row">
                        <div class="col s12 m-t-sm">
                            <button type="submit" class="waves-effect waves-light btn teal">PRODUIRE LE BONUS</button>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

@endsection
