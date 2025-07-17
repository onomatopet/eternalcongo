<!doctype html>
<html lang="fr">
    <head>

        <title></title>
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
    </style>
    </head>
<body class="white">

<table align="center">
    <tr>
        <td align="center" colspan="2">
            <p>
            <h4 class="center">

            </h4>
            <h5 class="center">ETERNAL Details Bonus</h5>
            <h5 class="center">eternalcongo.com - contact@eternalcongo.com</h5>
            <h5 class="center">Periode : {{ $bonus[0]->period }}</h5>
            <h5 class="center">Montant total : $ {{ number_format(($statistic[0]->total), 2, ',', ' ') ?? '' }}</h5>
            <h5 class="center">Montant total : {{ number_format(($statistic[0]->total * 550), 2, ',', ' ') ?? '' }} xaf</h5>
            <h5 class="center">Montant épargné : {{ number_format(($statistic[0]->epargn * 550), 2, ',', ' ') ?? '' }} xaf</h5>
            </p>

        </td>
    </tr>
    <tr>
        <td align="left">
            <h6 class="left">Print time : {{ \Carbon\Carbon::now()->format('d-m-Y') }}</h6>
        </td>
        <td align="right"><h6 class="right">Details Bonus paied</h6></td>
    </tr>
    <tr>
        <td colspan="2">
            <table id="example" class="border">
                <thead>
                    <tr>
                        <th>#</th>
                        <th data-field="periode">Période</th>
                        <th data-field="distributeur_id">ID</th>
                        <th data-field="nom_distributeur">Nom & Prénom</th>
                        <th data-field="nb_etoile">Rang</th>
                        <th data-field="num">N° bon</th>
                        <th data-field="bonus_direct">Bonus Direct</th>
                        <th data-field="bonus_indirect">Bonus Indirect</th>
                        <th data-field="total_bonus">Total $</th>
                        <th data-field="total_bonus">Total xaf</th>
                        <th data-field="epargne">Epargne</th>
                        <th data-field="date">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @if($bonus)

                        @foreach($bonus as $key => $items)
                            <tr>
                                <th>{{ $key+1 }}</th>
                                <td>{{ $items->period }}</td>
                                <td>{{ $items->distributeur_id ?? ''}}</td>
                                <td>{{ $items->nom_distributeur ?? ''}} {{ $items->pnom_distributeur ?? ''}}</td>
                                <td>{{ $items->etoiles_id ?? ''}}</td>
                                <td>{{ $items->num ?? ''}}</td>
                                <td>$ {{ $items->bonus_direct ?? ''}}</td>
                                <td>$ {{ $items->bonus_indirect ?? ''}}</td>
                                <td>$ {{ $items->bonus ?? ''}}</td>
                                <td> {{ number_format(($items->bonus * 550), 2, ',', ' ') ?? ''}} xaf</td>
                                <td>$ {{ $items->epargne ?? ''}}</td>
                                <td>{{ $items->created_at ?? '' }}</td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </td>
    </tr>
</table>

</body>
</html>



