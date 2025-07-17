@extends('layouts.index')

@section('content')

<main class="mn-inner">
    <div class="row">
        <div class="col s12">
            <div class="card">
                <div class="card-content">
                    <span class="card-title">BONUS - POUR PRODIORE LE BON</span>
                    <div class="row">

                        <div class="col s12">
                            <p>Veuillez introduire la periode sur laquelle souhaiter afficher les bonus. <br/>
                                Selectionner la date de la periode consernés.</p>

                                <form class="col s3" action="{{ route('bonus.store') }}" method="post">
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
                    </div>
                    <div class="row">
                        <div class="col s12 m-t-sm">
                            <div class="input-field col s1">

                                <input placeholder="Bonus Direct" name="bonusDirect" value="" type="text" class="validate" id="bonusDirect">
                                @if($errors->has('bonusDirect'))
                                    <span class="red-text accent-4">{{ $errors->first('bonusDirect') }}</span>
                                @endif

                            </div>
                            <div class="input-field col s1">
                                <input placeholder="Bonus Indirect" name="bonusIndirect" value="" type="text" class="validate" id="bonusIndirect">
                                @if($errors->has('bonusIndirect'))
                                    <span class="red-text accent-4">{{ $errors->first('bonusIndirect') }}</span>
                                @endif

                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col s12 m-t-sm">
                            <button type="submit" class="waves-effect waves-light btn teal">PRODUIRE LE BON</button>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

@endsection
