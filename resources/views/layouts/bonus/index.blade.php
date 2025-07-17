
@extends('layouts.index')

@section('content')

<main class="mn-inner">
    <div class="row">
        <div class="col s12">
            <div class="card">
                <div class="card-content"><span class="card-title">CONSULTER LES DISTRIBUTEURS AYANT EU LE BONUS</span>
                    <div class="row">
                            <div class="card-content">
                                <div class="row">

                                    <form class="col s12" action="{{ route('rapportstats.show', ['rapportstat' => $bonus[0]->period])}}" target="_blank" method="GET">

                                    <div class="input-field col s3">
                                        <p>Veuillez selectionnez la période</p>

                                        @csrf
                                        @method('GET')
                                                <select class="js-states browser-default id_produit" name="period" style="width: 100%" class="validate">
                                                    <option value="" disabled selected>Secltionner la période</option>
                                                    <option value="all">Tout produire</option>
                                                    @foreach( $period as $lines )
                                                        <option value={{ $lines->period }}>{{ $lines->period }}</option>
                                                    @endforeach
                                                </select>

                                                @if($errors->has('period'))
                                                    <span class="red-text accent-4">{{ $errors->first('period') }}</span>
                                                @endif
                                    </div>

                                    <div class="col s12 m-t-sm">
                                        <button type="submit" class="waves-effect waves-light btn teal">PRODUIRE LE REVELE DE BONUS</button>
                                    </div>
                                    </form>

                                </div>
                            </div>

                            <table id="example" class="display responsive-table datatable-example">
                                <thead>
                                    <tr>
                                        <th data-field="periode">Période</th>
                                        <th data-field="distributeur_id">ID</th>
                                        <th data-field="nom_distributeur">Nom & Prénom</th>
                                        <th data-field="nb_etoile">Etoiles</th>
                                        <th data-field="num">N° bon</th>
                                        <th data-field="bonus_direct">Bonus Direct</th>
                                        <th data-field="bonus_indirect">Bonus Indirect</th>
                                        <th data-field="Bonus_leadership">Leadership</th>
                                        <th data-field="total_bonus">Total $</th>
                                        <th data-field="total_bonus">Total xaf</th>
                                        <th data-field="epargne">Epargne</th>
                                        <th data-field="date">Date</th>
                                        <th data-field="date">Actions</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th data-field="periode">Période</th>
                                        <th data-field="distributeur_id">ID</th>
                                        <th data-field="nom_distributeur">Nom & Prénom</th>
                                        <th data-field="nb_etoile">Etoiles</th>
                                        <th data-field="num">N° bon</th>
                                        <th data-field="bonus_direct">Bonus Direct</th>
                                        <th data-field="bonus_indirect">Bonus Indirect</th>
                                        <th data-field="Bonus_leadership">Leadership</th>
                                        <th data-field="total_bonus">Total $</th>
                                        <th data-field="total_bonus">Total xaf</th>
                                        <th data-field="epargne">Epargne</th>
                                        <th data-field="date">Date</th>
                                        <th data-field="date">Actions</th>
                                    </tr>
                                </tfoot>
                                <tbody>

                                    @if($bonus)

                                        @foreach($bonus as $items)
                                            <tr>
                                                <td>{{ $items->period }}</td>
                                                <td>{{ $items->distributeur_id ?? ''}}</td>
                                                <td>{{ $items->nom_distributeur ?? ''}} {{ $items->pnom_distributeur ?? ''}}</td>
                                                <td>{{ $items->etoiles_id ?? ''}}</td>
                                                <td>{{ $items->num ?? ''}}</td>
                                                <td>$ {{ $items->bonus_direct ?? ''}}</td>
                                                <td>$ {{ $items->bonus_indirect ?? ''}}</td>
                                                <td>$ {{ $items->Bonus_leadership ?? '' }}</td>
                                                <td>$ {{ $items->bonus ?? ''}}</td>
                                                <td> {{ number_format(($items->bonus * 550), 2, ',', ' ') ?? ''}} xaf</td>
                                                <td>$ {{ $items->epargne ?? ''}}</td>
                                                <td>{{ $items->created_at ?? '' }}</td>
                                                <td>
                                                    <a href="{{ route('bonus.show',  ['bonu' => $items->distributeur_id, 'period' => $items->period]) }}" target="_blank" class="waves-effect waves-light">
                                                        <i class="tiny material-icons">visibility</i>
                                                    </a>
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
