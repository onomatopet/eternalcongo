<!doctype html>
<html lang="fr">
    <head>

        <title>{{ $distributeurs[0]['distributeur_id'] }}_{{$distributeurs[0]['nom_distributeur'] }}_{{ $distributeurs[0]['distributeur_id'] }}_NETWORK_STRUCTURE </title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
        <!-- Styles -->
        <link type="text/css" rel="stylesheet" href="{{ asset('assets/plugins/materialize/css/materialize.min.css') }}"/>
        <link href="{{ asset('assets/plugins/tabfinaltables/css/jquery.tabfinalTables.min.css') }}" rel="stylesheet">

    <style>

        @media print {
            html,
            body {
                margin: 0pt -10pt 0pt -10pt;
            }
        }
        html,
        body {
            margin: 0pt 5pt 0pt 5pt;
        }

        table .border td {
            border-bottom:1px solid #ccc;
            font-size:8pt;
            padding: 6pt;
            margin: 0pt;
            line-height: 1em
        }

        table .border th {
            font-size: 9pt;
            padding: 6pt;
            margin: 0pt;
            line-height: 1em;
            border-bottom:2px solid #aaa;
        }

        table h4 {
            font-family:'Times New Roman', Times, serif;
            font-size: 19pt;
            font-weight: 900;
            margin-bottom: -10pt
        }

        table h5 {
            font-family:'Times New Roman', Times, serif;
            font-size: 14pt;
            font-weight: 900;
            margin-bottom: -10pt
        }

        table h6 {
            font-family:'Times New Roman', Times, serif;
            font-size: 9pt;
            font-weight: 900;
            margin-bottom: -10pt
        }

    @media print{
        .boutonPrint
            {display: none;}
        }
    </style>

    </head>
    <body class="white">
<body>

<div class="col s12 m-t-sm boutonPrint">
    <button><a href="#" onclick="print();" class="waves-effect waves-light btn"><i class="material-icons right">print</i>IMPRIMER LE BON</a></button>
</div>

<table align="center">
    <tr>
        <td align="center" colspan="2">
            <p>
            <h4 class="center">
                {{ $distributeurs[0]['nom_distributeur'] }} {{$distributeurs[0]['pnom_distributeur'] }} ( {{ $distributeurs[0]['distributeur_id'] }} ) [Row: 1--{{ count($distributeurs) }}]
            </h4>
            <h5 class="center">ETERNAL Details Network Structure ({{ $distributeurs[0]['period'] }})</h5>
            <h5 class="center">eternalcongo.com - contact@eternalcongo.com</h5>
            <h5 class="center">(Time: {{ $distributeurs[0]['period'] }})</h5>
            </p>

        </td>
    </tr>
    <tr>
        <td align="left">
            <h6 class="left">Print time : {{ \Carbon\Carbon::now()->format('d-m-Y') }}</h6>
        </td>
        <td align="right"><h6 class="right">Details Network Structure</h6></td>
    </tr>
    <tr>
        <td colspan="2">
            <table id="example" class="border">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th></th>
                        <th>Nom & Prénom</th>
                        <th>Rang</th>
                        <th>New PV</th>
                        <th>Total PV</th>
                        <th>Cumulative PV</th>
                        <th>ID references</th>
                        <th>References Name</th>
                    </tr>
                </thead>
                <tbody>

                    @foreach ($distributeurs as $key => $items)
                    <tr>
                        <td>{{ $items['distributeur_id'] }}</td>
                        <td>{{ $items['rang'] }}</td>
                        <td>{{ $items['nom_distributeur'].' '.$items['pnom_distributeur'] }}</td>
                        <td>{{ $items['etoiles'] }}</td>
                        <td>${{ $items['new_cumul'] }}</td>
                        <td>${{ $items['cumul_total'] }}</td>
                        <td>${{ $items['cumul_collectif'] }}</td>
                        <td>{{ $items['id_distrib_parent'] }}</td>
                        <td>{{ $items['nom_parent'].' '.$items['pnom_parent'] }}</td>
                    </tr>

                    @endforeach
                </tbody>
            </table>
        </td>
    </tr>
</table>


