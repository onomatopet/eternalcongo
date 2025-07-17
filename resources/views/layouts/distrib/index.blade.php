@extends('layouts.index')

@section('content')

<main class="mn-inner">
    <div class="row">
        <div class="col s12">
            <div class="page-title"><h5>Distributeurs</h5></div>
        </div>
        <div class="col s12 m12 l12">
            <div class="card">
                <div class="card-content">
                    <span class="card-title">Gestion des Distributeurs</span>
                    <a href="{{ route('distrib.create')}}" class="btn btn-sm btn-primary"><i class="material-icons left">add</i>Ajouter un Distributeur</a><p></p>
                    <p>&nbsp;</p>
                    <table id="example" class="display responsive-table datatable-example">
                        <thead>
                            <tr>
                                <th data-field="distributeur_id">ID</th>
                                <th data-field="id">#</th>
                                <th data-field="nom_distributeur">Nom & Prénom</th>
                                <th data-field="nb_etoile">Etoiles</th>
                                <th data-field="tel_distributeur">Téléphone</th>
                                <th data-field="adress_distributeur">Adresse</th>
                                <th data-field="id_parent">ID référence</th>
                                <th data-field="tel_distributeur">Ajouter le</th>
                                <th data-field="adress_distributeur">Modifier le</th>
                                <th data-field="actions">Actions</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th data-field="distributeur_id">ID</th>
                                <th data-field="id">#</th>
                                <th data-field="nom_distributeur">Nom & Prénom</th>
                                <th data-field="nb_etoile">Etoiles</th>
                                <th data-field="tel_distributeur">Téléphone</th>
                                <th data-field="adress_distributeur">Adresse</th>
                                <th data-field="id_parent">ID référence</th>
                                <th data-field="tel_distributeur">Ajouter le</th>
                                <th data-field="adress_distributeur">Modifier le</th>
                                <th data-field="actions">Actions</th>
                            </tr>
                        </tfoot>
                        <tbody>
                            @if($distributeurs)

                                @foreach($distributeurs as $key => $items)
                                    <tr>
                                        <td>{{ $items->distributeur_id ?? ''}}</td>
                                        <td>{{ ++$key}}</td>
                                        <td>{{ $items->nom_distributeur ?? ''}} {{ $items->pnom_distributeur ?? ''}}</td>
                                        <td>{{ $items->etoiles_id ?? ''}}</td>
                                        <td>{{ $items->tel_distributeur ?? ''}}</td>
                                        <td>{{ $items->adress_distributeur ?? ''}}</td>
                                        <td>{{ $items->id_distrib_parent }}</td>
                                        <td>{{ $items->created_at ?? ''}}</td>
                                        <td>{{ $items->updated_at ?? ''}}</td>
                                        <td>
                                            <a href="{{ route('distrib.edit', $items->distributeur_id) }}" class="waves-effect waves-light"><i class="tiny material-icons">edit</i></a>
                                             |
                                            <a href="{{ route('distrib.show', [$items->id, $items->distributeur_id]) }}" class="waves-effect waves-light"><i class="tiny material-icons">visibility</i></a>
                                            <form id="etoile-delete-{{ $items->id }}" action="{{ route('distrib.destroy', $items->id) }}" method="post">
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

