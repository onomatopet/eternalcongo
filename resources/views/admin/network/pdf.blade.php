<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Réseau {{ $mainDistributor->distributeur_id }} - {{ $period }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 15mm;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9pt;
            line-height: 1.3;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #000;
        }

        h1 {
            font-size: 18pt;
            margin: 0 0 5px 0;
            color: #000;
        }

        h2 {
            font-size: 14pt;
            margin: 0 0 5px 0;
            color: #333;
        }

        .info {
            font-size: 10pt;
            color: #666;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th {
            background-color: #f0f0f0;
            font-weight: bold;
            padding: 8px 5px;
            text-align: left;
            border: 1px solid #ddd;
            font-size: 8pt;
        }

        td {
            padding: 6px 5px;
            border: 1px solid #ddd;
            font-size: 8pt;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .grade {
            display: inline-block;
            padding: 2px 6px;
            background-color: #fef3c7;
            color: #92400e;
            border-radius: 3px;
            font-weight: bold;
        }

        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 8pt;
            color: #666;
        }

        .level-indent {
            display: inline-block;
            width: 15px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $mainDistributor->nom_distributeur }} {{ $mainDistributor->pnom_distributeur }}</h1>
        <h2>({{ $mainDistributor->distributeur_id }})</h2>
        <p class="info">
            ETERNAL Details Network Structure - Période : {{ $period }}<br>
            Total : {{ $totalCount }} distributeur(s) | Date : {{ $printDate }}
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="8%">ID</th>
                <th width="5%">Niv.</th>
                <th width="20%">Nom & Prénom</th>
                <th width="5%" class="text-center">Grade</th>
                <th width="8%" class="text-right">New PV</th>
                <th width="8%" class="text-right">Total PV</th>
                <th width="10%" class="text-right">Cumul PV</th>
                <th width="8%" class="text-center">ID Parent</th>
                <th width="20%">Nom Parent</th>
            </tr>
        </thead>
        <tbody>
            @foreach($distributeurs as $distributeur)
                <tr>
                    <td>{{ $distributeur['distributeur_id'] }}</td>
                    <td>
                        @for($i = 0; $i < $distributeur['rang']; $i++)
                            <span class="level-indent">—</span>
                        @endfor
                        {{ $distributeur['rang'] }}
                    </td>
                    <td>{{ $distributeur['nom_distributeur'] }} {{ $distributeur['pnom_distributeur'] }}</td>
                    <td class="text-center">
                        <span class="grade">{{ $distributeur['etoiles'] }} ★</span>
                    </td>
                    <td class="text-right">{{ number_format($distributeur['new_cumul'], 0, ',', ' ') }}</td>
                    <td class="text-right">{{ number_format($distributeur['cumul_total'], 0, ',', ' ') }}</td>
                    <td class="text-right"><strong>{{ number_format($distributeur['cumul_collectif'], 0, ',', ' ') }}</strong></td>
                    <td class="text-center">{{ $distributeur['id_distrib_parent'] ?: '-' }}</td>
                    <td>
                        @if($distributeur['id_distrib_parent'])
                            {{ $distributeur['nom_parent'] }} {{ $distributeur['pnom_parent'] }}
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>
            eternalcongo.com - contact@eternalcongo.com<br>
            Document généré automatiquement - {{ config('app.name') }}
        </p>
    </div>
</body>
</html>
