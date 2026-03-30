<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture Commande #{{ str_pad($commande->id, 6, '0', STR_PAD_LEFT) }}</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'DejaVu Sans',Arial,sans-serif; font-size:13px; color:#1a1a1a; background:#fff; }
        .header { background:#1a1a1a; color:#fff; padding:32px 40px; }
        .header-top { display:flex; justify-content:space-between; align-items:flex-start; }
        .logo { font-size:30px; font-weight:900; letter-spacing:6px; color:#f59e0b; }
        .logo-sub { font-size:10px; color:rgba(255,255,255,.5); letter-spacing:3px; text-transform:uppercase; margin-top:4px; }
        .facture-title { text-align:right; }
        .facture-title h1 { font-size:20px; font-weight:700; color:#f59e0b; letter-spacing:2px; }
        .facture-title .ref { font-size:13px; color:rgba(255,255,255,.6); margin-top:4px; }
        .facture-title .date { font-size:11px; color:rgba(255,255,255,.4); margin-top:2px; }
        .gold-line { height:2px; background:linear-gradient(to right,#f59e0b,#fbbf24,transparent); }
        .info-section { display:flex; justify-content:space-between; padding:24px 40px; background:#f9f9f9; border-bottom:1px solid #e5e5e5; }
        .info-block { flex:1; }
        .info-block + .info-block { margin-left:40px; }
        .info-label { font-size:9px; text-transform:uppercase; letter-spacing:2px; color:#888; margin-bottom:6px; font-weight:700; }
        .info-name { font-size:15px; font-weight:700; color:#1a1a1a; margin-bottom:3px; }
        .info-detail { font-size:12px; color:#555; line-height:1.6; }
        .content { padding:28px 40px; }
        .section-title { font-size:11px; text-transform:uppercase; letter-spacing:2px; color:#888; margin-bottom:14px; font-weight:700; border-left:3px solid #f59e0b; padding-left:10px; }
        .badge { display:inline-block; background:#16a34a; color:#fff; padding:5px 16px; border-radius:100px; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:1px; margin-bottom:22px; }
        table { width:100%; border-collapse:collapse; margin-bottom:24px; }
        table thead tr { background:#1a1a1a; }
        table thead th { padding:10px 14px; text-align:left; font-size:10px; text-transform:uppercase; letter-spacing:1.5px; color:#f59e0b; font-weight:700; }
        table tbody tr { border-bottom:1px solid #f0f0f0; }
        table tbody tr:nth-child(even) { background:#fafafa; }
        table tbody td { padding:11px 14px; font-size:12px; color:#333; }
        .total-section { display:flex; justify-content:flex-end; margin-bottom:24px; }
        .total-box { background:#1a1a1a; color:#fff; padding:20px 28px; min-width:260px; border-radius:4px; }
        .total-row { display:flex; justify-content:space-between; padding:5px 0; font-size:12px; color:rgba(255,255,255,.6); }
        .total-row.main { border-top:1px solid rgba(255,255,255,.15); padding-top:12px; margin-top:8px; }
        .total-row.main .label { font-size:14px; font-weight:700; color:#fff; }
        .total-row.main .amount { font-size:22px; font-weight:900; color:#f59e0b; }
        .note-box { background:#fffbeb; border:1px solid #fde68a; border-left:4px solid #f59e0b; padding:14px 18px; border-radius:4px; font-size:11px; color:#78350f; margin-bottom:24px; line-height:1.6; }
        .footer { background:#1a1a1a; color:rgba(255,255,255,.4); text-align:center; padding:18px 40px; font-size:10px; letter-spacing:1px; position:fixed; bottom:0; left:0; right:0; }
    </style>
</head>
<body>

<div class="header">
    <div class="header-top">
        <div>
            <div class="logo">MISALO</div>
            <div class="logo-sub">Restaurant</div>
        </div>
        <div class="facture-title">
            <h1>FACTURE RESTAURANT</h1>
            <div class="ref">#{{ str_pad($commande->id, 6, '0', STR_PAD_LEFT) }}</div>
            <div class="date">Émise le {{ now()->format('d/m/Y') }}</div>
        </div>
    </div>
</div>
<div class="gold-line"></div>

<div class="info-section">
    <div class="info-block">
        <div class="info-label">Restaurant</div>
        <div class="info-name">Hôtel & Restaurant MISALO</div>
        <div class="info-detail">123 Rue de l'Élégance<br>Antananarivo, Madagascar<br>contact@misalo.mg</div>
    </div>
    <div class="info-block">
        <div class="info-label">Client</div>
        <div class="info-name">{{ $commande->nom }}</div>
        <div class="info-detail">
            {{ $commande->email }}<br>
            Commande du {{ $commande->created_at->format('d/m/Y à H:i') }}
        </div>
    </div>
</div>

<div class="content">
    <div class="badge">✓ Commande livrée</div>
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
                <td style="text-align:right; font-weight:700">{{ number_format($item->sous_total, 0, ',', ' ') }} Ar</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @if($commande->note)
    <div class="note-box"><strong>Note client :</strong> {{ $commande->note }}</div>
    @endif

    <div class="total-section">
        <div class="total-box">
            <div class="total-row">
                <span>Nombre d'articles</span>
                <span>{{ $commande->items->sum('quantite') }}</span>
            </div>
            <div class="total-row"><span>Taxes</span><span>Incluses</span></div>
            <div class="total-row main">
                <span class="label">TOTAL</span>
                <span class="amount">{{ number_format($commande->total, 0, ',', ' ') }} Ar</span>
            </div>
        </div>
    </div>

    <div class="note-box">
        <strong>Merci</strong> pour votre commande au Restaurant MISALO.
        Ce document constitue votre reçu officiel de paiement. À bientôt !
    </div>
</div>

<div class="footer">
    MISALO — Restaurant · Antananarivo, Madagascar · contact@misalo.mg
    · Commande #{{ str_pad($commande->id, 6, '0', STR_PAD_LEFT) }} · {{ now()->format('d/m/Y') }}
</div>

</body>
</html>