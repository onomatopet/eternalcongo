@extends('layouts.index')

@section('content')

<main class="mn-inner">
    <div class="row">
        <div class="col s12">
            <div class="card">
                <div class="card-content">
                    <span class="card-title">BONUS - POUR PRODIORE LE BON</span>
                    <div class="row">

                        <div class="col s12">
                            <p>Veuillez introduire la periode sur laquelle souhaiter afficher les bonus. <br/>
                                Selectionner la date de la periode consernés.</p>

                            <p>Veuillez introduire la periode sur laquelle souhaiter afficher les bonus. <br/>
                                Selectionner la date de la periode consernés.</p>

                                <form name="production" class="col s3" action="{{ route('bonus.store') }}" method="post">
                                    @csrf

                                    <select class="js-states browser-default" name="period_date" tabindex="-1" style="width: 100%" id="basic" class="validate">
                                        <optgroup label="Selectionnez la période">
                                            @if($period)

                                                @foreach($period as $lines)
                                                    <option value={{ $lines->new_date }}>{{ $lines->new_date }}</option>
                                                @endforeach

                                            @endif
                                        </optgroup>
                                    </select>
                                    @if($errors->has('distributeur_id'))
                                        <span class="red-text accent-4">{{ $errors->first('distributeur_id') }}</span>
                                    @endif
                                    <p></p>

                                    <p></p>
                                    <select name="bonuslist[]" class="js-example-tokenizer js-states browser-default" id="bonuslist" multiple="multiple" tabindex="-1" style="width: 100%" id="tokenizer">
                                        <optgroup label="Selectionnez L'ID">
                                            @if($distributeurs)

                                                @foreach($distributeurs as $lines)
                                                    <option value={{ $lines->distributeur_id }}>{{ $lines->distributeur_id }}</option>
                                                @endforeach

                                            @endif
                                        </optgroup>
                                    </select>

                                    @if($errors->has('bonuslist'))
                                        <span class="red-text accent-4">{{ $errors->first('bonuslist') }}</span>
                                    @endif
                        </div>
                        <div class="col s12 m-t-sm">
                            <button type="submit" class="waves-effect waves-light btn teal">PRODUIRE LE BON</button>
                        </div>
                    </form>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-content">
                    <span class="card-title">CONSULTER LES DISTRIBUTEURS AYANT EU LE BONUS</span>
                    <div class="row">
                        <div class="col s12 m-t-sm">
                            <form name="consultation" class="col s3" action="{{ route('bonus.store') }}" method="post">
                                @csrf

                                <select class="js-states browser-default" name="period_show" tabindex="-1" style="width: 100%" id="period_show" class="validate">
                                    <optgroup label="Selectionnez la période">
                                        @if($period)

                                            @foreach($period as $lines)
                                                <option value={{ $lines->new_date }}>{{ $lines->new_date }}</option>
                                            @endforeach

                                        @endif
                                    </optgroup>
                                </select>
                                @if($errors->has('distributeur_id'))
                                    <span class="red-text accent-4">{{ $errors->first('distributeur_id') }}</span>
                                @endif
                            </form>

                        </div>
                    </div>
                    <div class="row"></div>
                    <div class="row">
                        <table id="example" class="display responsive-table datatable-example">
                            <thead>
                                <tr>
                                    <th data-field="id">#</th>
                                    <th data-field="nom_distributeur">Nom & Prénom</th>
                                    <th data-field="nb_etoile">Etoiles</th>
                                    <th data-field="tel_distributeur">Téléphone</th>
                                    <th data-field="adress_distributeur">Adresse</th>
                                    <th data-field="id_parent">ID référence</th>
                                    <th data-field="actions">Actions</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th data-field="id">#</th>
                                    <th data-field="nom_distributeur">Nom & Prénom</th>
                                    <th data-field="nb_etoile">Etoiles</th>
                                    <th data-field="tel_distributeur">Téléphone</th>
                                    <th data-field="adress_distributeur">Adresse</th>
                                    <th data-field="id_parent">ID référence</th>
                                    <th data-field="actions">Actions</th>
                                </tr>
                            </tfoot>
                            <tbody>
                                @if($distributeurs)

                                    @foreach($distributeurs as $key => $items)
                                        <tr>
                                            <td>{{ $items->distributeur_id ?? ''}}</td>
                                            <td>{{ $items->nom_distributeur ?? ''}} {{ $items->pnom_distributeur ?? ''}}</td>
                                            <td>{{ $items->etoiles_id ?? ''}}</td>
                                            <td>{{ $items->tel_distributeur ?? ''}}</td>
                                            <td>{{ $items->adress_distributeur ?? ''}}</td>
                                            <td>{{ $items->id_distrib_parent }}</td>
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
    </div>
</main>

@endsection
