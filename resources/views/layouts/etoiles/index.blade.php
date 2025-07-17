@extends('layouts.index')

@section('content')

<main class="mn-inner">
    <div class="row">
        <div class="col s12">
            <div class="page-title"><h5>Etoiles</h5></div>
        </div>
        <div class="col s12 m12 l12">
            <div class="card">
                <div class="card-content">
                    <span class="card-title">Configuration des étoiles</span>
                    <a href="{{ route('etoiles.create')}}" class="btn btn-sm btn-primary"><i class="material-icons left">add</i>Ajouter une Etoile</a><p></p>
                    <p>&nbsp;</p>
                    <table id="example" class="display responsive-table datatable-example">
                        <thead>
                            <tr>
                                <th data-field="id">#</th>
                                <th data-field="etoile_level">Etoiles</th>
                                <th data-field="cumul_collectif">Cumul Individuel</th>
                                <th data-field="cumul_collectif_1">Cumul collectif 1</th>
                                <th data-field="cumul_collectif_2">Cumul collectif 2</th>
                                <th data-field="cumul_collectif_3">Cumul collectif 3</th>
                                <th data-field="cumul_collectif_4">Cumul collectif 4</th>
                                <th data-field="actions">Actions</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th data-field="id">#</th>
                                <th data-field="etoile_level">Etoiles</th>
                                <th data-field="cumul_individuel">Cumul Individuel</th>
                                <th data-field="cumul_collectif_1">Cumul collectif 1</th>
                                <th data-field="cumul_collectif_2">Cumul collectif 2</th>
                                <th data-field="cumul_collectif_3">Cumul collectif 3</th>
                                <th data-field="cumul_collectif_4">Cumul collectif 4</th>
                                <th data-field="actions">Actions</th>
                            </tr>
                        </tfoot>
                        <tbody>
                            @if($etoiles)

                                @foreach($etoiles as $key => $etoile)
                                    <tr>
                                        <td>{{ ++$key}}</td>
                                        <td>{{ $etoile->etoile_level ?? ''}}</td>
                                        <td>{{ $etoile->cumul_individuel ?? ''}}</td>
                                        <td>{{ $etoile->cumul_collectif_1 ?? ''}}</td>
                                        <td>{{ $etoile->cumul_collectif_2 ?? ''}}</td>
                                        <td>{{ $etoile->cumul_collectif_3 ?? ''}}</td>
                                        <td>{{ $etoile->cumul_collectif_4 ?? ''}}</td>
                                        <td>
                                            <a href="{{ route('etoiles.edit', $etoile->id) }}" class="waves-effect waves-light btn green m-b-xs"><i class="material-icons">edit</i></a>
                                            <a href="javascript:;" class="waves-effect waves-light btn red m-b-xs sa-delete" data-form-id="etoile-delete-{{ $etoile->id }}">
                                                <i class="material-icons">delete</i></a>
    
                                            <form id="etoile-delete-{{ $etoile->id }}" action="{{ route('etoiles.destroy', $etoile->id) }}" method="post">
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

