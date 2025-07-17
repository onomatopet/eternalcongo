<!doctype html>
<html class="no-js" lang="">

<head>
  <meta charset="utf-8">
  <title>BONUS - {{ $distributeurs['distributeur_id'].' '.$distributeurs['nom_distributeur'].' '.$distributeurs['pnom_distributeur'] }}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link rel="stylesheet" href="{{ asset('assets/invoice/web/modern-normalize.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/invoice/web/web-base.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/invoice//invoice.css') }}">
  <script type="text/javascript" src="{{ asset('assets/invoice/web/scripts.js') }}"></script>

  <style>
    @media print{
        .boutonPrint
            {display: none;}
        }
</style>

</head>
<body>

<div class="web-container">

  <div class="page-container">
  Page
  <span class="page"></span>
  of
  <span class="pages"></span>
</div>

<div class="logo-container">
    <table class="invoice-info-container">
        <tr>
            <td>
                <img
                    style="height: 38px"
                    src="{{ asset('assets/invoice/img/logo.jpg') }}"
                >
            </td>
            <td>
                <div class="col s12 m-t-sm boutonPrint">
                    <button><a href="#" onclick="print();" class="waves-effect waves-light btn"><i class="material-icons right">print</i>IMPRIMER LE BON</a></button>
                </div>
            </td>
        </tr>
    </table>
</div>

<table class="invoice-info-container">
    @if($distributeurs['duplicata'])
    <tr>
      <td colspan="3" class="large total">
        <strong>COPIE CONFORME</strong>
      </td>
    </tr>
    @endif
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

<table class="line-items-container">
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
        @if($distributeurs)
            <tr>

                @php
                    $total_direct = $distributeurs['bonus_direct'];
                    $total_indirect = $distributeurs['bonus_indirect'];
                    $bonus = $distributeurs['bonus'];
                @endphp

                <td>{{ 1 }}</td>
                <td>{{ $distributeurs['period'] }}</td>
                <td class="right">$ {{ $total_direct }}</td>
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

</body></html>
