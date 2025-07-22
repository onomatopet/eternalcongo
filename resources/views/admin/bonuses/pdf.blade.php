{{-- resources/views/admin/bonuses/pdf.blade.php --}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Reçu de Bonus - {{ $numero_recu }}</title>
    <style>
        @page {
            margin: 100px 50px;
        }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            position: fixed;
            top: -60px;
            left: 0;
            right: 0;
            height: 50px;
            text-align: center;
            line-height: 35px;
        }
        .footer {
            position: fixed;
            bottom: -60px;
            left: 0;
            right: 0;
            height: 50px;
            text-align: center;
            line-height: 35px;
            font-size: 10px;
            color: #666;
        }
        .logo {
            text-align: center;
            margin-bottom: 20px;
        }
        .company-info {
            text-align: center;
            margin-bottom: 30px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #2c5282;
            margin-bottom: 5px;
        }
        .document-title {
            font-size: 20px;
            font-weight: bold;
            text-align: center;
            margin: 30px 0;
            text-decoration: underline;
        }
        .info-section {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f7fafc;
            border: 1px solid #e2e8f0;
            border-radius: 5px;
        }
        .info-row {
            margin-bottom: 8px;
            display: table;
            width: 100%;
        }
        .info-label {
            display: table-cell;
            width: 40%;
            font-weight: bold;
            color: #4a5568;
        }
        .info-value {
            display: table-cell;
            width: 60%;
            color: #2d3748;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .table th {
            background-color: #2c5282;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: bold;
        }
        .table td {
            padding: 10px;
            border-bottom: 1px solid #e2e8f0;
        }
        .table tr:nth-child(even) {
            background-color: #f7fafc;
        }
        .amount {
            text-align: right;
            font-weight: bold;
        }
        .total-row {
            background-color: #e6fffa !important;
            font-weight: bold;
        }
        .total-final {
            background-color: #2c5282 !important;
            color: white;
            font-size: 14px;
        }
        .signature-section {
            margin-top: 50px;
            display: table;
            width: 100%;
        }
        .signature-box {
            display: table-cell;
            width: 45%;
            text-align: center;
        }
        .signature-line {
            margin-top: 60px;
            border-top: 1px solid #333;
            padding-top: 5px;
        }
        .note {
            margin-top: 30px;
            padding: 15px;
            background-color: #fef5e7;
            border: 1px solid #f9e79f;
            border-radius: 5px;
            font-size: 11px;
        }
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 100px;
            color: rgba(0, 0, 0, 0.05);
            z-index: -1;
        }
    </style>
</head>
<body>
    <div class="watermark">ORIGINAL</div>
    
    <div class="header">
        <p>{{ config('app.name', 'ETERNEL') }} - Système de Gestion MLM</p>
    </div>

    <div class="footer">
        <p>Document généré le {{ $date_generation }} | Page <span class="pagenum"></span></p>
    </div>

    <div class="company-info">
        <div class="company-name">{{ config('app.name', 'ETERNEL') }}</div>
        <p>Réseau de Marketing Multi-Niveaux<br>
        Tél: +242 00 000 00 00 | Email: contact@eternel.com</p>
    </div>

    <h1 class="document-title">REÇU DE BONUS</h1>

    <div class="info-section">
        <div class="info-row">
            <div class="info-label">Numéro de reçu :</div>
            <div class="info-value">{{ $numero_recu }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Période :</div>
            <div class="info-value">{{ $periode }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Date d'émission :</div>
            <div class="info-value">{{ $date_generation }}</div>
        </div>
    </div>

    <div class="info-section">
        <h3 style="margin-top: 0; color: #2c5282;">BÉNÉFICIAIRE</h3>
        <div class="info-row">
            <div class="info-label">Matricule :</div>
            <div class="info-value">#{{ $distributeur->distributeur_id }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Nom complet :</div>
            <div class="info-value">{{ $distributeur->pnom_distributeur }} {{ $distributeur->nom_distributeur }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Grade actuel :</div>
            <div class="info-value">Grade {{ $distributeur->etoiles_id }}</div>
        </div>
        @if($distributeur->tel_distributeur)
        <div class="info-row">
            <div class="info-label">Téléphone :</div>
            <div class="info-value">{{ $distributeur->tel_distributeur }}</div>
        </div>
        @endif
        @if($distributeur->adress_distributeur)
        <div class="info-row">
            <div class="info-label">Adresse :</div>
            <div class="info-value">{{ $distributeur->adress_distributeur }}</div>
        </div>
        @endif
    </div>

    <table class="table">
        <thead>
            <tr>
                <th style="width: 60%;">Description</th>
                <th style="width: 40%; text-align: right;">Montant (FCFA)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Bonus Direct (Achats personnels)</td>
                <td class="amount">{{ number_format($details['bonus_direct'], 0, ',', ' ') }}</td>
            </tr>
            <tr>
                <td>Bonus Indirect (Achats des filleuls)</td>
                <td class="amount">{{ number_format($details['bonus_indirect'], 0, ',', ' ') }}</td>
            </tr>
            @if($details['bonus_leadership'] > 0)
            <tr>
                <td>Bonus Leadership</td>
                <td class="amount">{{ number_format($details['bonus_leadership'], 0, ',', ' ') }}</td>
            </tr>
            @endif
            <tr class="total-row">
                <td><strong>TOTAL BRUT</strong></td>
                <td class="amount">{{ number_format($details['total_brut'], 0, ',', ' ') }}</td>
            </tr>
            <tr>
                <td>Épargne (10%)</td>
                <td class="amount" style="color: #e53e3e;">- {{ number_format($details['epargne'], 0, ',', ' ') }}</td>
            </tr>
            <tr class="total-final">
                <td><strong>NET À PAYER</strong></td>
                <td class="amount" style="font-size: 16px;">{{ number_format($details['net_payer'], 0, ',', ' ') }} FCFA</td>
            </tr>
        </tbody>
    </table>

    <div class="note">
        <strong>Note importante :</strong> Ce reçu constitue un justificatif officiel de vos commissions pour la période indiquée. 
        L'épargne de 10% est automatiquement mise de côté pour votre compte épargne. 
        Le montant net sera versé selon les modalités convenues avec l'administration.
    </div>

    <div class="signature-section">
        <div class="signature-box">
            <p><strong>Le Bénéficiaire</strong></p>
            <div class="signature-line">
                {{ $distributeur->pnom_distributeur }} {{ $distributeur->nom_distributeur }}
            </div>
        </div>
        <div style="display: table-cell; width: 10%;"></div>
        <div class="signature-box">
            <p><strong>Pour l'Administration</strong></p>
            <div class="signature-line">
                Signature et Cachet
            </div>
        </div>
    </div>

    <div style="margin-top: 40px; text-align: center; font-size: 10px; color: #666;">
        <p>Ce document a été généré électroniquement et est valide sans signature manuscrite.</p>
        <p>Pour toute réclamation, veuillez contacter l'administration dans les 30 jours suivant l'émission.</p>
    </div>
</body>
</html>