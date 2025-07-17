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
                    <span class="card-title">Modifier un Produit</span><br>
                    <div class="row">
                        <form role="form" class="col s12" action="{{ route('products.update', $products->id) }}" method="post">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="input-field col s3">
                                    <label for="code_product" class="active">Code du Produit</label>
                                    <input name="code_product" value="{{ $products->code_product }}" type="text" class="validate">

                                    @if($errors->has('code_product'))
                                        <span class="red-text accent-4">{{ $errors->first('code_product') }}</span>
                                    @endif

                                </div>
                                <div class="input-field col s3">
                                    <select name="category_id" class="browser-default">
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id ?? ''}}" {{$category->id == $products->category_id  ? 'selected' : ''}}>{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="input-field col s6">
                                    <input name="nom_produit" value="{{ $products->nom_produit }}" type="text" class="validate">
                                    <label for="nom_produit" class="active">Nom du Produit</label>

                                    @if($errors->has('nom_produit'))
                                        <span class="red-text accent-4">{{ $errors->first('nom_produit') }}</span>
                                    @endif

                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s12">
                                    <input name="description" value="{{ $products->description }}" type="text">
                                    <label for="description" class="active">Description</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s6">
                                    <select name="pointvaleur_id" class="browser-default">
                                        @foreach($pvs as $item)
                                            <option value="{{ $item->id ?? ''}}" {{$item->id == $products->pointvaleur_id  ? 'selected' : ''}}>{{ $item->numbers }}</option>
                                        @endforeach
                                    </select>
                                    <label for="point_valeur" class="active">Point Valeur</label>

                                    @if($errors->has('point_valeur'))
                                        <span class="red-text accent-4">{{ $errors->first('point_valeur') }}</span>
                                    @endif
                                </div>
                                <div class="input-field col s6">
                                    <input name="prix_product" value="{{ $products->prix_product }}" type="text" class="validate">
                                    <label for="prix_product" class="active">Prix du Produit</label>

                                    @if($errors->has('prix_product'))
                                        <span class="red-text accent-4">{{ $errors->first('prix_product') }}</span>
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
