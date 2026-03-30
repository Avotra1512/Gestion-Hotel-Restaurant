<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture Groupée MISALO — {{ $client->name }}</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'DejaVu Sans',Arial,sans-serif; font-size:12px; color:#1a1a1a; background:#fff; }
        .header { background:#1a1a1a; padding:32px 44px; }
        .header-inner { display:flex; justify-content:space-between; align-items:flex-start; }
        .logo { font-size:32px; font-weight:900; letter-spacing:7px; color:#f59e0b; }
        .logo-sub { font-size:9px; color:rgba(255,255,255,.4); letter-spacing:3px; text-transform:uppercase; margin-top:4px; }
        .facture-badge { text-align:right; }
        .facture-badge h1 { font-size:16px; font-weight:700; color:#f59e0b; letter-spacing:3px; }
        .facture-badge .client-name { font-size:13px; color:rgba(255,255,255,.7); margin-top:5px; }
        .facture-badge .date-emit { font-size:10px; color:rgba(255,255,255,.35); margin-top:3px; }
        .gold-strip { height:3px; background:linear-gradient(90deg,#f59e0b,#fbbf24,transparent); }

        .summary-bar { display:flex; gap:0; border-bottom:1px solid #eee; }
        .summary-item { flex:1; padding:18px 24px; border-right:1px solid #eee; }
        .summary-item:last-child { border-right:none; }
        .summary-label { font-size:8px; text-transform:uppercase; letter-spacing:2px; color:#999; font-weight:700; margin-bottom:6px; }
        .summary-val { font-size:22px; font-weight:900; }
        .summary-val.amber { color:#f59e0b; }
        .summary-val.blue  { color:#3b82f6; }
        .summary-val.green { color:#16a34a; }
        .summary-sub { font-size:10px; color:#999; margin-top:3px; }

        .body { padding:26px 44px; }

        .section-header { display:flex; align-items:center; gap:10px; margin:22px 0 12px; }
        .section-header-line { flex:1; height:1px; background:#eee; }
        .section-header-title { font-size:12px; font-weight:700; color:#1a1a1a; text-transform:uppercase; letter-spacing:1.5px; }
        .section-header-emoji { font-size:16px; }

        table { width:100%; border-collapse:collapse; margin-bottom:16px; }
        thead tr { background:#1a1a1a; }
        thead th { padding:9px 12px; text-align:left; font-size:8.5px; text-transform:uppercase; letter-spacing:2px; color:#f59e0b; font-weight:700; }
        tbody tr { border-bottom:1px solid #f0f0f0; }
        tbody tr:nth-child(even) { background:#fafafa; }
        tbody td { padding:10px 12px; font-size:11px; color:#333; }
        .subtotal-row td { background:#fffbeb; font-weight:700; color:#78350f; border-top:2px solid #fde68a; }

        .grand-total-wrap { display:flex; justify-content:flex-end; margin:20px 0; }
        .grand-total-box { background:#1a1a1a; color:#fff; padding:20px 28px; min-width:280px; border-radius:4px; }
        .gt-row { display:flex; justify-content:space-between; padding:5px 0; font-size:12px; color:rgba(255,255,255,.5); }
        .gt-row.main { border-top:1px solid rgba(255,255,255,.12); padding-top:12px; margin-top:8px; }
        .gt-row.main .gtl { font-size:14px; font-weight:700; color:#fff; }
        .gt-row.main .gtv { font-size:22px; font-weight:900; color:#f59e0b; }

        .info-box { background:#fffbeb; border:1px solid #fde68a; border-left:4px solid #f59e0b; padding:14px 18px; border-radius:4px; font-size:11px; color:#78350f; line-height:1.7; margin-bottom:20px; }

        .footer { position:fixed; bottom:0; left:0; right:0; background:#1a1a1a; padding:14px 44px; text-align:center; font-size:9px; color:rgba(255,255,255,.35); letter-spacing:1px; }
    </style>
</head>
<body>

<div class="header">
    <div class="header-inner">
        <div>
            <div class="logo">MISALO</div>
            <div class="logo-sub">Hôtel & Restaurant</div>
        </div>
        <div class="facture-badge">
            <h1>RELEVÉ DE FACTURES</h1>
            <div class="client-name">{{ $client->name }}</div>
            <div class="date-emit">Généré le {{ now()->format('d/m/Y à H:i') }}</div>
        </div>
    </div>
</div>
<div class="gold-strip"></div>

{{-- RÉSUMÉ EN BARRE --}}
<div class="summary-bar">
    <div class="summary-item">
        <div class="summary-label">Total chambres</div>
        <div class="summary-val amber">{{ number_format($totalReservations, 0, ',', ' ') }} Ar</div>
        <div class="summary-sub">{{ $reservations->count() }} réservation(s)</div>
    </div>
    <div class="summary-item">
        <div class="summary-label">Total restaurant</div>
        <div class="summary-val blue">{{ number_format($totalCommandes, 0, ',', ' ') }} Ar</div>
        <div class="summary-sub">{{ $commandes->count() }} commande(s)</div>
    </div>
    <div class="summary-item">
        <div class="summary-label">Total général</div>
        <div class="summary-val green">{{ number_format($totalGeneral, 0, ',', ' ') }} Ar</div>
        <div class="summary-sub">{{ $reservations->count() + $commandes->count() }} document(s)</div>
    </div>
</div>

<div class="body">

    {{-- RÉSERVATIONS --}}
    @if($reservations->count() > 0)
    <div class="section-header">
        <span class="section-header-emoji">🛏️</span>
        <span class="section-header-title">Réservations Chambres</span>
        <div class="section-header-line"></div>
    </div>
    <table>
        <thead>
            <tr>
                <th>Réf.</th>
                <th>Chambre</th>
                <th>Type</th>
                <th>Période</th>
                <th>Nuits</th>
                <th>Statut</th>
                <th style="text-align:right">Montant</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reservations as $res)
            <tr>
                <td style="font-family:monospace; color:#f59e0b">#{{ str_pad($res->id, 6, '0', STR_PAD_LEFT) }}</td>
                <td><strong>{{ $res->chambre?->numero_chambre ?? '—' }}</strong></td>
                <td style="text-transform:capitalize">{{ $res->chambre?->type_chambre ?? '—' }}</td>
                <td>
                    @if($res->date_reservation)
                        {{ $res->date_reservation->format('d/m/Y') }}
                    @else
                        {{ $res->date_arrivee?->format('d/m/Y') }} → {{ $res->date_depart?->format('d/m/Y') }}
                    @endif
                </td>
                <td>{{ $res->nombreNuits() }}</td>
                <td>{{ $res->libelleStatut() }}</td>
                <td style="text-align:right; font-weight:700">{{ number_format($res->prix_total, 0, ',', ' ') }} Ar</td>
            </tr>
            @endforeach
            <tr class="subtotal-row">
                <td colspan="6" style="text-align:right">Sous-total Chambres</td>
                <td style="text-align:right">{{ number_format($totalReservations, 0, ',', ' ') }} Ar</td>
            </tr>
        </tbody>
    </table>
    @endif

    {{-- COMMANDES --}}
    @if($commandes->count() > 0)
    <div class="section-header">
        <span class="section-header-emoji">🍽️</span>
        <span class="section-header-title">Commandes Restaurant</span>
        <div class="section-header-line"></div>
    </div>
    <table>
        <thead>
            <tr>
                <th>Réf.</th>
                <th>Date</th>
                <th>Articles</th>
                <th>Statut</th>
                <th style="text-align:right">Montant</th>
            </tr>
        </thead>
        <tbody>
            @foreach($commandes as $cmd)
            <tr>
                <td style="font-family:monospace; color:#f59e0b">#{{ str_pad($cmd->id, 6, '0', STR_PAD_LEFT) }}</td>
                <td>{{ $cmd->created_at->format('d/m/Y H:i') }}</td>
                <td>
                    {{ $cmd->items->take(2)->map(fn($i) => $i->menu?->nom ?? '—')->join(', ') }}
                    @if($cmd->items->count() > 2) +{{ $cmd->items->count() - 2 }} @endif
                </td>
                <td>{{ $cmd->libelleStatut() }}</td>
                <td style="text-align:right; font-weight:700">{{ number_format($cmd->total, 0, ',', ' ') }} Ar</td>
            </tr>
            @endforeach
            <tr class="subtotal-row">
                <td colspan="4" style="text-align:right">Sous-total Restaurant</td>
                <td style="text-align:right">{{ number_format($totalCommandes, 0, ',', ' ') }} Ar</td>
            </tr>
        </tbody>
    </table>
    @endif

    {{-- TOTAL GÉNÉRAL --}}
    <div class="grand-total-wrap">
        <div class="grand-total-box">
            <div class="gt-row">
                <span>Chambres ({{ $reservations->count() }} rés.)</span>
                <span>{{ number_format($totalReservations, 0, ',', ' ') }} Ar</span>
            </div>
            <div class="gt-row">
                <span>Restaurant ({{ $commandes->count() }} cmd.)</span>
                <span>{{ number_format($totalCommandes, 0, ',', ' ') }} Ar</span>
            </div>
            <div class="gt-row">
                <span>Taxes</span>
                <span>Incluses</span>
            </div>
            <div class="gt-row main">
                <span class="gtl">TOTAL GÉNÉRAL</span>
                <span class="gtv">{{ number_format($totalGeneral, 0, ',', ' ') }} Ar</span>
            </div>
        </div>
    </div>

    <div class="info-box">
        <strong>Relevé officiel</strong> — Ce document récapitule l'ensemble de vos dépenses
        à l'Hôtel & Restaurant MISALO. Tous les paiements ont été effectués directement
        à la caisse. Merci de votre fidélité.
    </div>

</div>

<div class="footer">
    MISALO Hôtel & Restaurant · Antananarivo, Madagascar · contact@misalo.mg
    · Relevé de {{ $client->name }} · {{ now()->format('d/m/Y') }}
</div>

</body>
</html>