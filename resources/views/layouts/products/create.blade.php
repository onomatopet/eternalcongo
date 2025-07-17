@extends('layouts.index')

@section('content')

<main class="mn-inner">
    <div class="row">
        <div class="col s12">
            <div class="page-title"><h5>Produits</h5></div>
        </div>
        <div class="col s12 m12 l8">
            <div class="card">
                <div class="card-content">
                    <span class="card-title">Ajouter un Produit</span><br>
                    <div class="row">
                        <form class="col s12" action="{{ route('products.store') }}" method="post">
                            @csrf
                            <div class="row">
                                <div class="input-field col s4">
                                    <input name="code_product" value="" type="text" class="validate">
                                    <label for="code_product">Code du Produit</label>

                                    @if($errors->has('code_product'))
                                        <span class="red-text accent-4">{{ $errors->first('code_product') }}</span>
                                    @endif

                                </div>
                                <div class="input-field col s4">
                                    <select name="category_id" class="browser-default">     
                                        @foreach($categories as $category)                     
                                            <option value="{{ $category->id ?? ''}}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="input-field col s4">
                                    <select name="pointvaleur_id" class="browser-default">     
                                        @foreach($pointvaleurs as $item)                     
                                            <option value="{{ $item->id ?? ''}}">{{ $item->numbers }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s9">
                                    <input name="nom_produit" value="" type="text" class="validate">
                                    <label for="nom_produit">Nom du Produit</label>

                                    @if($errors->has('nom_produit'))
                                        <span class="red-text accent-4">{{ $errors->first('nom_produit') }}</span>
                                    @endif
                                    
                                </div>
                                <div class="input-field col s3">
                                    <input name="prix_product" value="" type="text" class="validate">
                                    <label for="prix_product">Prix du Produit</label>

                                    @if($errors->has('prix_product'))
                                        <span class="red-text accent-4">{{ $errors->first('prix_product') }}</span>
                                    @endif
                                    
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s12">
                                    <input name="description" value="" type="text">
                                    <label for="description">Description</label>                                    
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