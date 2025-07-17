<!doctype html>
<html class="no-js" lang="">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <title>BONUS - {{ $distributeurs[0]['nom_distributeur'].' '.$distributeurs[0]['pnom_distributeur'] }}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="{{ asset('assets/invoice/web/web-base.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/invoice//invoice.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/invoice//invoice-pdf.css') }}">
  <script type="text/javascript" src="{{ asset('assets/invoice/web/scripts.js') }}"></script>

  <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">

  <style>
.page-break {
    page-break-after: always;
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
  <img
    style="height: 38px"
    src="{{ asset('assets/invoice/img/logo.jpg') }}"
  >
</div>

<table class="invoice-info-container">
  <tr>
    <td rowspan="2" class="client-name">
      Bulletin Bonus<br/>
      {{ $distributeurs[0]['nom_distributeur'].' '.$distributeurs[0]['pnom_distributeur'] }}
      <br/>
      ID : {{$distributeurs[0]['distributeur_id']}}
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
      No: <strong>77701</strong>
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

            @foreach($distributeurs as $key => $items)
                <tr>
                    <td>{{ $key+1 }}</td>
                    <td>{{ $items['new_date'] }}</td>
                    <td class="right">$ {{ $total_direct = $items['bonus_direct']}}</td>
                    <td class="right">
                        @php
                            $total_indirect = $items['bonus_indirect'];
                        @endphp
                        $ {{ $total_indirect }}
                    </td>
                    <td class="bold">$ {{ $bonus = $total_direct + $total_indirect}}</td>
                    @php
                        $total = $total + $bonus;
                        $xaf = number_format(($total * 550), 2, ',', ' ');
                    @endphp
                </tr>
            @endforeach
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
      <td class="large total">${{ $total }}</td>
    </tr>
  </tbody>
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
