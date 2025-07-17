@extends('layouts.index')

@section('content')

<main class="mn-inner">
    <div class="row">
        <div class="col s12">
            <div class="page-title">Etoiles</div>
        </div>
        <div class="col s12 m12 l6">
            <div class="card">
                <div class="card-content">
                    <span class="card-title">Creer une Etoile</span><br>
                    <div class="row">
                        <form class="col s12" action="{{ route('etoiles.store') }}" method="post">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="input-field col s4">
                                    <?php $i = 0 ?>
                                    <select name="etoile_level" class="browser-default" class="validate">

                                        @for ($i=0; $i<=9; $i++)
                                        
                                            @if ($etoiles->etoile_level == $i)
                                                <option value="{{ $etoiles->etoile_level }}" selected>{{ $etoiles->etoile_level }} étoile</option>                                                    
                                            @else                                                    
                                                <option value="$i">$i étoile</option>  
                                            @endif
                                            <?php $i++ ?>
                                        @endfor
                                    </select>  

                                        @if($errors->has('etoile_level'))
                                            <span class="red-text accent-4">{{ $errors->first('etoile_level') }}</span>
                                        @endif
                                </div>
                                <div class="input-field col s4">
                                    <label for="cumul_individuel">Cumul individuel PV</label>
                                    <input name="cumul_individuel" value="{{ $etoiles->cumul_individuel }}" type="text" class="validate">
                                    
                                    @if($errors->has('cumul_individuel'))
                                        <span class="red-text accent-4">{{ $errors->first('cumul_individuel') }}</span>
                                    @endif
                                    
                                </div>
                                <div class="input-field col s4">
                                    <label for="cumul_collectif_1">Cumul collectif PV 1</label>
                                    <input name="cumul_collectif_1" value="{{ $etoiles->cumul_collectif_1 }}" type="text" class="validate">
                                    
                                    @if($errors->has('cumul_collectif_1'))
                                        <span class="red-text accent-4">{{ $errors->first('cumul_collectif_1') }}</span>
                                    @endif
                                    
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s4">
                                    <label for="cumul_collectif_2">Cumul collectif PV 2</label>
                                    <input name="cumul_collectif_2" value="{{ $etoiles->cumul_collectif_2 }}" type="text" class="validate">
                                    
                                    @if($errors->has('cumul_collectif_2'))
                                        <span class="red-text accent-4">{{ $errors->first('cumul_collectif_2') }}</span>
                                    @endif
                                    
                                </div>
                                <div class="input-field col s4">
                                    <label for="cumul_collectif_3">Cumul collectif PV 2</label>
                                    <input name="cumul_collectif_3" value="{{ $etoiles->cumul_collectif_3 }}" type="text" class="validate">
                                    
                                    @if($errors->has('cumul_collectif_3'))
                                        <span class="red-text accent-4">{{ $errors->first('cumul_collectif_3') }}</span>
                                    @endif
                                    
                                </div>
                                <div class="input-field col s4">
                                    <label for="cumul_collectif_4">Cumul collectif PV 4</label>
                                    <input name="cumul_collectif_4" value="{{ $etoiles->cumul_collectif_4 }}" type="text" class="validate">
                                    
                                    @if($errors->has('cumul_collectif_4'))
                                        <span class="red-text accent-4">{{ $errors->first('cumul_collectif_4') }}</span>
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