@extends('layouts.index')

@section('content')

<main class="mn-inner">
    <div class="row">
        <div class="col s12">
            <div class="page-title"><h5>Effectuer un Achat</h5></div>
        </div>
        <div class="col s12 m12 l8">
            <div class="card">
                <div class="card-content">
                    <span class="card-title">Veuillez insérer les informations relatives à l'achat</span><br>
                    <div class="row">
                        <form class="col s12" action="{{ route('achats.store') }}" method="post">
                            @csrf
                            <div class="row">
                                <div class="input-field col s6">

                                    <select class="js-states browser-default" name="distributeur_id" tabindex="-1" style="width: 100%" class="validate">
                                        <option value="" disabled selected>ID Distributeurs</option>

                                        @foreach($distributeurs as $distributeur)

                                            <option value={{ $distributeur->distributeur_id }} title="{{ $distributeur->id }}">{{ $distributeur->distributeur_id }}</option>

                                        @endforeach

                                    </select>

                                    @if($errors->has('distributeur_id'))
                                        <span class="red-text accent-4">{{ $errors->first('distributeur_id') }}</span>
                                    @endif

                                </div>
                                <div class="input-field col s6">

                                    <select class="js-states browser-default id_produit" name="id_produit" style="width: 100%" class="validate">
                                        <option value="" disabled selected>Produit</option>
                                        @foreach( $products as $product )
                                        <option value={{ $product->prix_product }} title="{{ $product->pointvaleur_id }}">{{ $product->id.'-'.$product->nom_produit }}</option>
                                        @endforeach
                                    </select>
                                    @if($errors->has('id_produit'))
                                        <span class="red-text accent-4">{{ $errors->first('id_produit') }}</span>
                                    @endif

                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s4">

                                    <select class="js-states browser-default prix_product validate" name="prix_product" tabindex="-1" style="width: 100%">
                                        <option value="" disabled selected>Prix</option>
                                        @foreach( $prixproducts  as $prixproduct )
                                        <option value={{ $prixproduct->prix_product }}>{{ $prixproduct->prix_product }}</option>
                                        @endforeach
                                    </select>
                                    @if($errors->has('prix_product'))
                                        <span class="red-text accent-4">{{ $errors->first('prix_product') }}</span>
                                    @endif

                                </div>
                                <div class="input-field col s4">

                                    <select class="js-states browser-default pointvaleur_id validate" name="pointvaleur_id" tabindex="-1" style="width: 100%">
                                        <option value="" disabled selected>Points Valeurs</option>
                                        @foreach( $pointvaleurs  as $pointvaleur )
                                        <option value={{ $pointvaleur->id }} title="{{ $pointvaleur->numbers }}">{{ $pointvaleur->numbers }}</option>
                                        @endforeach
                                    </select>
                                    @if($errors->has('pointvaleur_id'))
                                        <span class="red-text accent-4">{{ $errors->first('pointvaleur_id') }}</span>
                                    @endif

                                </div>
                                <div class="input-field col s4">

                                    <select class="js-states browser-default select_id qte validate" name="Qt" style="width: 100%">
                                        <option value="" disabled selected>Quantités</option>
                                        @for( $i=1; $i<=50; $i++ )
                                        <option value={{ $i }}>{{ $i }}</option>
                                        @endfor
                                    </select>
                                    @if($errors->has('Qt'))
                                        <span class="red-text accent-4">{{ $errors->first('Qt') }}</span>
                                    @endif

                                </div>
                            </div>
                            <div class="col s12 m-t-sm">
                                <div class="input-field col s4">

                                    <input placeholder="Montant Total" name="value" value="" type="text" class="validate" id="value">
                                    @if($errors->has('value'))
                                        <span class="red-text accent-4">{{ $errors->first('value') }}</span>
                                    @endif

                                </div>
                                <div class="input-field col s4">
                                    <input type="hidden" name="idproduit" value="" id="idproduit">
                                    <input placeholder="Points Valeurs Total" name="value_pv" value="" type="text" class="validate" id="value_pv">
                                    @if($errors->has('value_pv'))
                                        <span class="red-text accent-4">{{ $errors->first('value_pv') }}</span>
                                    @endif

                                </div>

                                <div class="input-field col s4">
                                    <input placeholder="" type="text" name="created_at" value="">
                                    <label for="mask2" class="active">Date AU FORMAT : DD/MM/YYYY</label>
                                    @if($errors->has('created_at'))
                                        <span class="red-text accent-4">{{ $errors->first('created_at') }}</span>
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
