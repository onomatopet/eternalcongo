@extends('layouts.index')

@section('content')

<main class="mn-inner">
    <div class="row">
        <div class="col s12">
            <div class="card">
                <div class="card-content">
                    <span class="card-title">BONUS - AFFICHAGE DU BONUS</span>
                    <div class="row">
                        <table id="example" class="display responsive-table datatable-example">
                            <thead>
                                <tr>
                                    <th data-field="periode">Période</th>
                                    <th data-field="distributeur_id">ID</th>
                                    <th data-field="nom_distributeur">Nom & Prénom</th>
                                    <th data-field="nb_etoile">Etoiles</th>
                                    <th data-field="new_cumul">New Cumul</th>
                                    <th data-field="cumul_total">Cumul Total</th>
                                    <th data-field="bonus_direct">Bonus Direct</th>
                                    <th data-field="bonus_indirect">Bonus Indirect</th>
                                    <th data-field="total_bonus">Total $</th>
                                    <th data-field="total_bonus">Total xaf</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th data-field="periode">Période</th>
                                    <th data-field="distributeur_id">ID</th>
                                    <th data-field="nom_distributeur">Nom & Prénom</th>
                                    <th data-field="nb_etoile">Etoiles</th>
                                    <th data-field="new_cumul">New Cumul</th>
                                    <th data-field="cumul_total">Cumul Total</th>
                                    <th data-field="bonus_direct">Bonus Direct</th>
                                    <th data-field="bonus_indirect">Bonus Indirect</th>
                                    <th data-field="total_bonus">Total $</th>
                                    <th data-field="total_bonus">Total xaf</th>
                                </tr>
                            </tfoot>
                            <tbody>
                                @if($distributeurs)

                                    @foreach($distributeurs as $key => $items)
                                        <tr>
                                            <td>{{ $date }}</td>
                                            <td>{{ $items->distributeur_id ?? ''}}</td>
                                            <td>{{ $items->nom_distributeur ?? ''}} {{ $items->pnom_distributeur ?? ''}}</td>
                                            <td>{{ $items->etoiles ?? ''}}</td>
                                            <td>$ {{ $items->new_cumul ?? ''}}</td>
                                            <td>$ {{ $items->cumul_total ?? ''}}</td>
                                            <td>$ {{ $total_direct = $operandDirect}}</td>
                                            <td>
                                                @php
                                                    $total_indirect = $operandIndirect
                                                @endphp
                                                $ {{ $total_indirect }}
                                            </td>
                                            <td>$ {{ $bonus = $total_direct + $total_indirect}}</td>
                                            <td>$ {{ $bonus * 500}}</td>
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
