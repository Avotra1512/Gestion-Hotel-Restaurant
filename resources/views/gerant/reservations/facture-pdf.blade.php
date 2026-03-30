<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture MISALO #{{ str_pad($reservation->id, 6, '0', STR_PAD_LEFT) }}</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'DejaVu Sans',Arial,sans-serif; font-size:13px; color:#1a1a1a; background:#fff; }
        .header { background:#1a1a1a; color:#fff; padding:32px 40px; }
        .header-top { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:24px; }
        .logo { font-size:32px; font-weight:900; letter-spacing:6px; color:#f59e0b; }
        .logo-sub { font-size:10px; color:rgba(255,255,255,.5); letter-spacing:3px; text-transform:uppercase; margin-top:4px; }
        .facture-title { text-align:right; }
        .facture-title h1 { font-size:22px; font-weight:700; color:#f59e0b; letter-spacing:2px; }
        .facture-title .ref { font-size:13px; color:rgba(255,255,255,.6); margin-top:4px; }
        .facture-title .date { font-size:11px; color:rgba(255,255,255,.4); margin-top:2px; }
        .gold-line { height:2px; background:linear-gradient(to right,#f59e0b,#fbbf24,transparent); }
        .info-section { display:flex; justify-content:space-between; padding:28px 40px; background:#f9f9f9; border-bottom:1px solid #e5e5e5; }
        .info-block { flex:1; }
        .info-block + .info-block { margin-left:40px; }
        .info-label { font-size:9px; text-transform:uppercase; letter-spacing:2px; color:#888; margin-bottom:8px; font-weight:700; }
        .info-name { font-size:16px; font-weight:700; color:#1a1a1a; margin-bottom:4px; }
        .info-detail { font-size:12px; color:#555; line-height:1.6; }
        .content { padding:28px 40px; }
        .section-title { font-size:11px; text-transform:uppercase; letter-spacing:2px; color:#888; margin-bottom:14px; font-weight:700; border-left:3px solid #f59e0b; padding-left:10px; }
        table { width:100%; border-collapse:collapse; margin-bottom:28px; }
        table thead tr { background:#1a1a1a; }
        table thead th { padding:11px 14px; text-align:left; font-size:10px; text-transform:uppercase; letter-spacing:1.5px; color:#f59e0b; font-weight:700; }
        table tbody tr { border-bottom:1px solid #f0f0f0; }
        table tbody tr:nth-child(even) { background:#fafafa; }
        table tbody td { padding:12px 14px; font-size:12px; color:#333; vertical-align:top; }
        .total-section { display:flex; justify-content:flex-end; margin-bottom:28px; }
        .total-box { background:#1a1a1a; color:#fff; padding:20px 28px; min-width:280px; border-radius:4px; }
        .total-row { display:flex; justify-content:space-between; align-items:center; padding:5px 0; font-size:12px; color:rgba(255,255,255,.6); }
        .total-row.main { border-top:1px solid rgba(255,255,255,.15); padding-top:12px; margin-top:8px; }
        .total-row.main .label { font-size:14px; font-weight:700; color:#fff; }
        .total-row.main .amount { font-size:22px; font-weight:900; color:#f59e0b; }
        .badge-paye { display:inline-block; background:#16a34a; color:#fff; padding:5px 16px; border-radius:100px; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:1px; margin-bottom:28px; }
        .note { background:#fffbeb; border:1px solid #fde68a; border-left:4px solid #f59e0b; padding:14px 18px; border-radius:4px; font-size:11px; color:#78350f; margin-bottom:28px; line-height:1.6; }
        .footer { background:#1a1a1a; color:rgba(255,255,255,.4); text-align:center; padding:20px 40px; font-size:10px; letter-spacing:1px; position:fixed; bottom:0; left:0; right:0; }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-top">
            <div>
                <div class="logo">MISALO</div>
                <div class="logo-sub">Hôtel & Restaurant</div>
            </div>
            <div class="facture-title">
                <h1>FACTURE</h1>
                <div class="ref">#{{ str_pad($reservation->id, 6, '0', STR_PAD_LEFT) }}</div>
                <div class="date">Émise le {{ now()->format('d/m/Y') }}</div>
            </div>
        </div>
    </div>
    <div class="gold-line"></div>

    <div class="info-section">
        <div class="info-block">
            <div class="info-label">Émetteur</div>
            <div class="info-name">Hôtel & Restaurant MISALO</div>
            <div class="info-detail">123 Rue de l'Élégance<br>Antananarivo, Madagascar<br>contact@misalo.mg</div>
        </div>
        <div class="info-block">
            <div class="info-label">Client</div>
            <div class="info-name">{{ $reservation->nom }}</div>
            <div class="info-detail">{{ $reservation->email }}@if($reservation->motif)<br>Motif : {{ $reservation->motif }}@endif</div>
        </div>
    </div>

    <div class="content">
        <div class="badge-paye">✓ Paiement validé</div>
        <div class="section-title">Détails de la réservation</div>
        <table>
            <thead>
                <tr>
                    <th>Désignation</th><th>Chambre</th><th>Période</th><th>Nuits</th><th>Prix/nuit</th><th style="text-align:right">Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Hébergement</strong></td>
                    <td><strong>{{ $reservation->chambre?->numero_chambre ?? '—' }}</strong></td>
                    <td>
                        @if($reservation->date_reservation)
                            {{ $reservation->date_reservation->format('d/m/Y') }}
                        @else
                            {{ $reservation->date_arrivee?->format('d/m/Y') }} au {{ $reservation->date_depart?->format('d/m/Y') }}
                        @endif
                    </td>
                    <td>{{ $reservation->nombreNuits() }}</td>
                    <td>{{ number_format($reservation->chambre?->prix_nuit ?? 0,0,',',' ') }} Ar</td>
                    <td style="text-align:right; font-weight:700">{{ number_format($reservation->prix_total,0,',',' ') }} Ar</td>
                </tr>
            </tbody>
        </table>

        <div class="total-section">
            <div class="total-box">
                <div class="total-row"><span>Sous-total : </span><span>{{ number_format($reservation->prix_total,0,',',' ') }} Ar</span></div>
                <div class="total-row"><span>Taxes</span><span>Incluses</span></div>
                <div class="total-row main"><span class="label">TOTAL : </span><span class="amount">{{ number_format($reservation->prix_total,0,',',' ') }} Ar</span></div>
            </div>
        </div>

        <div class="note">
            <strong>Note :</strong> Le paiement a été effectué directement à la réception de l'Hôtel MISALO.
            Cette facture constitue un reçu officiel. Merci de votre confiance.
        </div>
    </div>

    <div class="footer">MISALO — Hôtel & Restaurant · Antananarivo, Madagascar · contact@misalo.mg · Facture #{{ str_pad($reservation->id,6,'0',STR_PAD_LEFT) }}</div>
</body>
</html>