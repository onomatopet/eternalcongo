@extends('layouts.index')

@section('content')

<main class="mn-inner">
    <div class="row">
        <div class="col s12">
            <div class="page-title"><h5>Produits</h5></div>
        </div>
        <div class="col s12 m12 l12">
            <div class="card">
                <div class="card-content">
                    <span class="card-title">Liste des Produits</span>
                    <a href="{{ route('products.create')}}" class="btn btn-sm btn-primary"><i class="material-icons left">add</i>Ajouter un Produit</a><p></p>
                    <p>&nbsp;</p>
                    <table id="example" class="display responsive-table datatable-example">
                        <thead>
                            <tr>
                                <th data-field="id">#</th>
                                <th data-field="code_product">Code Produit</th>
                                <th data-field="nom_produit">Nom Produit</th>
                                <th data-field="description">Description</th>
                                <th data-field="category_id">Unité</th>
                                <th data-field="point_valeur">PV</th>
                                <th data-field="prix_product">Prix</th>
                                <th data-field="actions">Actions</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th data-field="id">#</th>
                                <th data-field="code_product">Code Produit</th>
                                <th data-field="nom_produit">Nom Produit</th>
                                <th data-field="description">Description</th>
                                <th data-field="category_id">Unité</th>
                                <th data-field="point_valeur">PV</th>
                                <th data-field="prix_product">Prix</th>
                                <th data-field="actions">Actions</th>
                            </tr>
                        </tfoot>
                        <tbody>
                            @if($products)

                                @foreach($products as $key => $product)
                                    <tr>
                                        <td>{{ ++$key}}</td>
                                        <td>{{ $product->code_product ?? ''}}</td>
                                        <td>{{ $product->nom_produit ?? ''}}</td>
                                        <td>{{ $product->description ?? ''}}</td>

                                            @foreach ( $categories->keys() as $items)
                                                @if ($items == $product->category_id)
                                                    <td>{{ $categories[$items] }}</td>
                                                @endif
                                            @endforeach

                                            @foreach ( $pointvaleurs->keys() as $item)
                                                @if ($item == $product->pointvaleur_id)
                                                    <td>{{ $pointvaleurs[$item] }}</td>
                                                @endif
                                            @endforeach

                                        <td>{{ $product->prix_product ?? ''}}</td>
                                        <td>
                                            <a href="{{ route('products.edit', [$product->id]) }}" class="waves-effect waves-light"><i class="tiny material-icons">edit</i></a>
                                            <a href="javascript:;" class="waves-effect waves-light" data-form-id="product-delete-{{ $product->id }}">
                                                <i class="tiny material-icons">delete</i></a>

                                            <form id="product-delete-{{ $product->id }}" action="{{ route('products.destroy', $product->id) }}" method="post">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

@endsection

