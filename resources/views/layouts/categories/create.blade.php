@extends('layouts.index')

@section('content')

<main class="mn-inner">
    <div class="row">
        <div class="col s12">
            <div class="page-title">Categories</div>
        </div>
        <div class="col s12 m12 l6">
            <div class="card">
                <div class="card-content">
                    <span class="card-title">Creer une catégorie</span><br>
                    <div class="row">
                        <form class="col s12" action="{{ route('categories.store') }}" method="post">
                            @csrf
                            <div class="row">
                                <div class="input-field col s6">
                                    <label for="name">Nom de la catégorie</label>
                                    <input name="name" value="" type="text" class="validate">
                                    
                                    @if($errors->has('name'))
                                        <span class="red-text accent-4">{{ $errors->first('name') }}</span>
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