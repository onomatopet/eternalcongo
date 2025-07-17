
@extends('layouts.printer')

@section('content')

<main class="mn-inner">
    <div class="row">
        <div class="col s12">
            <div class="card">
                <div class="card-content">


                    <div class="web-container">

                    <div class="page-container">
                        <div class="col s12 m-t-sm">
                            <a href="{{ route('bonus.update', ['bonu' => $distributeurs['bonus'], 'direct' => $distributeurs['bonus_direct'], 'indirect' => $distributeurs['bonus_indirect'], 'distributeur_id' => $distributeurs['distributeur_id']]) }}" target="_blank" class="waves-effect waves-light btn"><i class="material-icons right">print</i>IMPRIMER LE BON</a></button>
                        </div>
                    </div>

                    <div class="logo-container">
                    <img
                        style="height: 38px"
                        src="{{ asset('assets/invoice/img/logo.jpg') }}"
                    >
                    </div>

                    <table class="invoice-info-container">
                    <tr>
                        <td rowspan="2" class="client-name">
                        Bulletin Bonus<br/>
                        {{ $distributeurs['nom_distributeur'].' '.$distributeurs['pnom_distributeur'] }}
                        <br/>
                        ID : {{$distributeurs['distributeur_id']}}
                        </td>
                        <td>
                        ETERNAL CONGO SARL
                        </td>
                    </tr>
                    <tr>
                        <td>
                        45, rue BAYAS POTO-POTO
                        </td>
                    </tr>
                    <tr>
                        <td>
                        Date: {{ \Carbon\Carbon::now() }}
                        </td>
                        <td>
                        Tel : 04 403 16 16
                        </td>
                    </tr>
                    <tr>
                        <td>
                        No: <strong>{{$distributeurs['numero']}}</strong>
                        </td>
                        <td>
                        contact@eternalcongo.com
                        </td>
                    </tr>
                    </table>

                    <table class="line-items-container" border="1">
                    <thead>
                        <tr>
                        <th class="heading-quantity">#</th>
                        <th class="heading-description">Période</th>
                        <th class="heading-price">Bonus Direct</th>
                        <th class="heading-price">Bonus Indirect</th>
                        <th class="heading-price">Total $</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $total = 0 @endphp
                        @php
                            $total_direct = $distributeurs['bonus_direct'];
                            $total_indirect = $distributeurs['bonus_indirect'];
                            $bonus = $distributeurs['bonus'];
                        @endphp
                        @if($distributeurs)
                        <tr>
                            <td>{{ 1 }}</td>
                            <td>{{ $distributeurs['period'] }}</td>
                            <td class="center">$ {{ $total_direct }}</td>
                            <td class="right">$ {{ $total_indirect }}</td>
                            <td class="bold">$ {{ $bonus }}</td>
                        @php
                            $xaf = number_format(($distributeurs['bonusFinal'] * 550), 2, ',', ' ');
                        @endphp
                        </tr>
                        @endif

                    </tbody>
                    </table>


                    <table class="line-items-container has-bottom-border">
                    <thead>
                        <tr>
                        <th>Montant en XAF</th>
                        <th width="20"></th>
                        <th class="right">TOTAL A PAYER</th>
                        <th>Montant en Dollars</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                        <td class="large total">{{ $xaf }} xaf</td>
                        <td class="large"></td>
                        <td class="large"></td>
                        <td class="large total">${{ $distributeurs['bonusFinal'] }}</td>
                        </tr>
                    </tbody>
                    </table>


                    <table class="line-items-container has-bottom-border">
                    <thead>
                        <tr>
                        <th>VISA DU CAISSIER</th>
                        <th width="20"></th>
                        <th></th>
                        <th>VISA DE L'AYANT DROIT</th>
                        </tr>
                    </thead>
                    </table>

                    <div class="footer">
                    <div class="footer-info">
                        <span>contact@eternalcongo.com</span> |
                        <span>Tel : 04 403 16 16</span> |
                        <span>eternalcongo.com</span>
                    </div>
                    <div class="footer-thanks">
                        <img src="https://github.com/anvilco/html-pdf-invoice-template/raw/main/img/heart.png" alt="heart">
                        <span>Thank you!</span>
                    </div>
                    </div>


                    </div>



                </div>
            </div>
        </div>
    </div>
</main>

@endsection
