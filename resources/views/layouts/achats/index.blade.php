@extends('layouts.index')

@section('content')

<main class="mn-inner">
    <div class="row">
        <div class="col s12">
            <div class="page-title"><h5>Achats éffectués</h5></div>
        </div>
        <div class="col s12 m12 l12">
            <div class="card">
                <div class="card-content">
                    <span class="card-title">Gestion des achats</span>
                    <a href="{{ route('achats.create')}}" class="btn btn-sm btn-primary"><i class="material-icons left">add</i>Effectuer un achat</a><p></p>
                    <p>&nbsp;</p>
                    <table id="example" class="display" style="width:100%">
                        <thead>
                            <tr>
                                <th data-field="id">#</th>
                                <th data-field="period">Periode</th>
                                <th data-field="distributeur_id">ID Acheteur</th>
                                <th data-field="nom_distributeur">Nom & Prénom Acheteur</th>
                                <th data-field="code_product">code Produit</th>
                                <th data-field="nom_produit">Produit</th>
                                <th data-field="montant">Montant U</th>
                                <th data-field="pointvaleur">PV U</th>
                                <th data-field="qt">Qté</th>
                                <th data-field="montanttotal">Montant total</th>
                                <th data-field="pvtotal">PV total</th>
                                <th data-field="created_at">Date</th>
                                <th data-field="created_at">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($achats)

                                @foreach($achats as $key => $items)
                                <tr>
                                    <td>{{ ++$key}}</td>
                                    <td>{{ $items['period'] ?? ''}}</td>
                                    <td>{{ $items['distributeur_id'] ?? ''}}</td>
                                    <td>{{ $items['nom_distributeur'].' '.$items['pnom_distributeur'] }}</td>
                                    <td>{{ $items['code_product'] ?? '' }}</td>
                                    <td>{{ $items['nom_produit'] ?? '' }}</td>
                                    <td>{{ number_format($items['prix_product'], 2,",",".") ?? '' }}</td>
                                    <td>{{  $items['pointvaleur'] ?? ''}}</td>
                                    <td>{{ $items['qt'] ?? ''}}</td>
                                    <td>{{ number_format($items['montanttotal'], 2,",",".") ?? ''}}</td>
                                    <td>{{ $items['pvtotal'] ?? ''}}</td>
                                    <td>{{ date('d/m/Y', strtotime($items['created_at'])) ?? ''}}</td>
                                    <td>
                                        <a href="{{ route('achats.edit', $items['id']) }}" class="waves-effect waves-light"><i class="tiny material-icons">edit</i></a>
                                            |
                                        <a href="#" class="waves-effect waves-light achats-delete" data-form-id="achats-delete-{{ $items['id'] }}">
                                                <i class="tiny material-icons">delete</i></a>

                                        <form id="achats-delete-{{ $items['id'] }}" action="{{ route('achats.destroy', $items['id']) }}" method="post">
                                            @csrf
                                            @method('POST')
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            @endif
                        </tbody>
                        <tfoot>

                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

@endsection
