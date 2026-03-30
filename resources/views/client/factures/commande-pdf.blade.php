<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture Commande #{{ str_pad($commande->id, 6, '0', STR_PAD_LEFT) }}</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'DejaVu Sans',Arial,sans-serif; font-size:13px; color:#1a1a1a; background:#fff; }
        .header { background:#1a1a1a; padding:36px 44px; }
        .header-inner { display:flex; justify-content:space-between; align-items:flex-start; }
        .logo { font-size:34px; font-weight:900; letter-spacing:7px; color:#f59e0b; }
        .logo-sub { font-size:9px; color:rgba(255,255,255,.45); letter-spacing:3px; text-transform:uppercase; margin-top:5px; }
        .facture-badge { text-align:right; }
        .facture-badge h1 { font-size:18px; font-weight:700; color:#f59e0b; letter-spacing:3px; margin-bottom:6px; }
        .facture-badge .ref { font-size:14px; color:rgba(255,255,255,.65); }
        .facture-badge .date-emit { font-size:10px; color:rgba(255,255,255,.35); margin-top:3px; }
        .gold-strip { height:3px; background:linear-gradient(90deg,#f59e0b 0%,#fbbf24 50%,transparent 100%); }
        .parties { display:flex; padding:26px 44px; background:#f8f8f8; border-bottom:1px solid #eee; gap:40px; }
        .partie { flex:1; }
        .partie-label { font-size:8px; text-transform:uppercase; letter-spacing:2.5px; color:#999; font-weight:700; margin-bottom:8px; }
        .partie-name { font-size:16px; font-weight:700; color:#111; margin-bottom:4px; }
        .partie-detail { font-size:11px; color:#555; line-height:1.7; }
        .body { padding:30px 44px; }
        .badge-livree { display:inline-block; background:#16a34a; color:#fff; padding:5px 18px; border-radius:50px; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:1.5px; margin-bottom:22px; }
        .section-title { font-size:10px; text-transform:uppercase; letter-spacing:2px; color:#999; font-weight:700; margin-bottom:12px; padding-left:10px; border-left:3px solid #f59e0b; }
        table { width:100%; border-collapse:collapse; margin-bottom:26px; }
        thead tr { background:#1a1a1a; }
        thead th { padding:11px 14px; text-align:left; font-size:9px; text-transform:uppercase; letter-spacing:2px; color:#f59e0b; font-weight:700; }
        tbody tr { border-bottom:1px solid #f0f0f0; }
        tbody tr:nth-child(even) { background:#fafafa; }
        tbody td { padding:12px 14px; font-size:12px; color:#333; }
        .total-wrap { display:flex; justify-content:flex-end; margin-bottom:26px; }
        .total-box { background:#1a1a1a; color:#fff; padding:20px 28px; min-width:270px; border-radius:4px; }
        .t-row { display:flex; justify-content:space-between; padding:5px 0; font-size:12px; color:rgba(255,255,255,.55); }
        .t-row.main { border-top:1px solid rgba(255,255,255,.12); padding-top:12px; margin-top:8px; }
        .t-row.main .tl { font-size:14px; font-weight:700; color:#fff; }
        .t-row.main .tv { font-size:22px; font-weight:900; color:#f59e0b; }
        .info-box { background:#fffbeb; border:1px solid #fde68a; border-left:4px solid #f59e0b; padding:14px 18px; border-radius:4px; font-size:11px; color:#78350f; line-height:1.7; }
        .footer { position:fixed; bottom:0; left:0; right:0; background:#1a1a1a; padding:16px 44px; text-align:center; font-size:9px; color:rgba(255,255,255,.35); letter-spacing:1.5px; }
    </style>
</head>
<body>

<div class="header">
    <div class="header-inner">
        <div>
            <div class="logo">MISALO</div>
            <div class="logo-sub">Restaurant</div>
        </div>
        <div class="facture-badge">
            <h1>FACTURE RESTAURANT</h1>
            <div class="ref">N° {{ str_pad($commande->id, 6, '0', STR_PAD_LEFT) }}</div>
            <div class="date-emit">Émise le {{ now()->format('d/m/Y à H:i') }}</div>
        </div>
    </div>
</div>
<div class="gold-strip"></div>

<div class="parties">
    <div class="partie">
        <div class="partie-label">Restaurant</div>
        <div class="partie-name">Hôtel & Restaurant MISALO</div>
        <div class="partie-detail">
            123 Rue de l'Élégance<br>
            Antananarivo, Madagascar<br>
            contact@misalo.mg
        </div>
    </div>
    <div class="partie">
        <div class="partie-label">Client</div>
        <div class="partie-name">{{ $commande->nom }}</div>
        <div class="partie-detail">
            {{ $commande->email }}<br>
            Commande du {{ $commande->created_at->format('d/m/Y à H:i') }}
        </div>
    </div>
</div>

<div class="body">

    <div class="badge-livree">✓ Commande livrée</div>

    @if($commande->note)
    <div style="margin-bottom:18px; padding:10px 14px; background:#f8f8f8; border-radius:4px; font-size:11px; color:#555; border-left:3px solid #e5e7eb;">
        <strong>Note :</strong> {{ $commande->note }}
    </div>
    @endif

    <div class="section-title">Détail de la commande</div>

    <table>
        <thead>
            <tr>
                <th>Plat</th>
                <th>Catégorie</th>
                <th>Qté</th>
                <th>Prix unitaire</th>
                <th style="text-align:right">Sous-total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($commande->items as $item)
            <tr>
                <td><strong>{{ $item->menu?->nom ?? 'Plat supprimé' }}</strong></td>
                <td>{{ $item->menu ? $item->menu->libelleCategorie() : '—' }}</td>
                <td>{{ $item->quantite }}</td>
                <td>{{ number_format($item->prix_unitaire, 0, ',', ' ') }} Ar</td>
                <td style="text-align:right; font-weight:700">
                    {{ number_format($item->sous_total, 0, ',', ' ') }} Ar
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-wrap">
        <div class="total-box">
            <div class="t-row">
                <span>Nombre d'articles</span>
                <span>{{ $commande->items->sum('quantite') }}</span>
            </div>
            <div class="t-row">
                <span>Taxes</span>
                <span>Incluses</span>
            </div>
            <div class="t-row main">
                <span class="tl">TOTAL</span>
                <span class="tv">{{ number_format($commande->total, 0, ',', ' ') }} Ar</span>
            </div>
        </div>
    </div>

    <div class="info-box">
        <strong>Merci</strong> pour votre commande au Restaurant MISALO.
        Ce document constitue votre reçu officiel de paiement. À bientôt !
    </div>

</div>

<div class="footer">
    MISALO Restaurant · Antananarivo, Madagascar · contact@misalo.mg
    · Facture N° {{ str_pad($commande->id, 6, '0', STR_PAD_LEFT) }} · {{ now()->format('d/m/Y') }}
</div>

</body>
</html>