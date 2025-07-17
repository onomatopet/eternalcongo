
@extends('layouts.index')

@section('content')

<main class="mn-inner">
    <div class="row">
        <div class="col s12">
            <div class="page-title"><h5>Détails distributeur</h5></div>
        </div>
        <div class="col s12 m12 l12">
            <div class="row">
                <div class="col s12 m4 l3">
                    <div class="card">
                        <div class="card-content center-align">
                            <img src="{{ asset('assets/images/profile-image-1.png') }}" class="responsive-img circle" width="128px" alt="">
                            <p class="m-t-lg flow-text"> {{ $distributeurs->nom_distributeur.' '.$distributeurs->pnom_distributeur }} <br/>ID : {{ $distributeurs->distributeur_id }} </p>
                        </div>
                    </div>
                    <!-- ici-->
                    <div class="card">
                        <div class="card-content">
                            <span class="card-title">Informations</span>
                            <div class="collection">
                            <ul>
                                <li class="collection-item">PV du mois en cours :<span class="badge white" data-badge-caption="$">{{ $pv->new_cumul }}</span></li>
                                <li class="collection-item">total PV : <span class="badge white" data-badge-caption="$">{{ $pv->cumul_individuel ?? 'Aucun'}}</span></li>
                                <li class="collection-item">Cumulative PV : <span class="badge white" data-badge-caption="$">{{ $pv->cumul_collectif }}</span></li>
                                <li class="collection-item">Bonus Direct du mois : <span class="badge white" data-badge-caption="$">{{ $bonus }}</span></li>
                                <li class="collection-item">Bonus Indirect du mois : <span class="badge white" data-badge-caption="$">{{ $bonusInd }}</span></li>
                            </ul>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="col s12 m4 l9">
                    <div class="card">
                        <div class="card-content ">
                            <!-- TREEVIEW GRID -->
                            <!--<smart-grid id="grid"></smart-grid>-->

                            <div class="card-content">
                                <span class="card-title">Réseaux distributeurs de : {{ $distributeurs->nom_distributeur.' ' .$distributeurs->pnom_distributeur}}</span>
                                <table id="example" class="display responsive-table datatable-example">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nom & Prénom</th>
                                            <th>Nb étoiles</th>
                                            <th>New Cumul</th>
                                            <th>Cumul individuel</th>
                                            <th>Cumul collectif</th>
                                            <th>Bonus</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nom & Prénom</th>
                                            <th>Nb étoiles</th>
                                            <th>New Cumul</th>
                                            <th>Cumul individuel</th>
                                            <th>Cumul collectif</th>
                                            <th>Bonus</th>
                                            <th>Actions</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                    @foreach ($distribut as $keys => $items)

                                            <tr>
                                                <td>{{ $items->distributeur_id }}</td>
                                                <td>{{ $items->nom_distributeur.' '.$items->pnom_distributeur }}</td>
                                                <td>{{ $items->etoiles_id }}</td>
                                                    @foreach ($levelel as $cle => $level)
                                                        @if($level->distributeur_id == $items->distributeur_id)
                                                            <td>${{ $level->new_cumul }}</td>
                                                            <td>${{ $level->cumul_individuel }}</td>
                                                            <td>${{ $level->cumul_collectif }}</td>
                                                        @endif
                                                    @endforeach
                                                </td>
                                                <td>{{ $bonusInd }}</td>
                                                <td>
                                                    <a href="{{ route('distrib.show', [$items->id,$items->distributeur_id]) }}" class="waves-effect waves-light"><i class="tiny material-icons">visibility</i></a>
                                                </td>
                                            </tr>

                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</main>

@endsection

