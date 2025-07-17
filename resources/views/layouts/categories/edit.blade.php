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
                    <span class="card-title">Modifier la catégorie</span><br>
                    <div class="row">
                        <form role="form" class="col s12" action="{{ route('categories.update', $category->id) }}" method="post">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="input-field col s6">
                                    <label for="name">Nom de la catégorie</label>
                                    <input name="name" value="{{ $category->name}}" type="text" class="validate">
                                    
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