@extends('layouts.index')

@section('content')

<main class="mn-inner">
    <div class="row">
        <div class="col s12">
            <div class="page-title">Points Valeurs</div>
        </div>
        <div class="col s12 m12 l6">
            <div class="card">
                <div class="card-content">
                    <span class="card-title">Ajouter des points valeurs</span><br>
                    <div class="row">
                        <form class="col s12" action="{{ route('pointsval.store') }}" method="post">
                            @csrf
                            <div class="row">
                                <div class="input-field col s6">
                                    <label for="numbers">Point Valeur</label>
                                    <input name="numbers" value="" type="text" class="validate">
                                    
                                    @if($errors->has('numbers'))
                                        <span class="red-text accent-4">{{ $errors->first('numbers') }}</span>
                                    @endif
                                    
                                </div>
                            </div>
                            
                            <div class="col s12 m-t-sm">
                                <button type="submit" class="waves-effect waves-light btn teal">Valider</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>    
    </div>
</main>


@endsection