@section('sidebar-links-nav')
<a href="{{ route('client.dashboard') }}"
   class="flex items-center gap-3 px-4 py-3 rounded-xl transition font-medium
          {{ request()->routeIs('client.dashboard') ? 'bg-amber-400 text-black font-bold shadow-lg shadow-amber-400/20' : 'text-white/70 hover:bg-white/5 hover:text-white' }}">
    <span class="text-xl">🏠</span><span>Tableau de bord</span>
</a>

<a href="{{ route('client.chambres.index') }}"
   class="flex items-center gap-3 px-4 py-3 rounded-xl transition font-medium
          {{ request()->routeIs('client.chambres.*') ? 'bg-amber-400 text-black font-bold shadow-lg shadow-amber-400/20' : 'text-white/70 hover:bg-white/5 hover:text-white' }}">
    <span class="text-xl">🛏️</span><span>Chambres Misalo</span>
</a>

<a href="{{ route('client.reservations.index') }}"
   class="flex items-center gap-3 px-4 py-3 rounded-xl transition font-medium
          {{ request()->routeIs('client.reservations.*') ? 'bg-amber-400 text-black font-bold shadow-lg shadow-amber-400/20' : 'text-white/70 hover:bg-white/5 hover:text-white' }}">
    <span class="text-xl">📅</span><span>Mes Réservations</span>
</a>

<a href="{{ route('client.restaurant.index') }}"
   class="flex items-center gap-3 px-4 py-3 rounded-xl transition font-medium
          {{ request()->routeIs('client.restaurant.index') || request()->routeIs('client.restaurant.recapitulatif') ? 'bg-amber-400 text-black font-bold shadow-lg shadow-amber-400/20' : 'text-white/70 hover:bg-white/5 hover:text-white' }}">
    <span class="text-xl">🍽️</span>
    <span>Restaurant</span>
    @php $nbPanier = collect(session('panier', []))->sum('quantite'); @endphp
    @if($nbPanier > 0)
        <span class="ml-auto px-2 py-0.5 bg-amber-400 text-black text-xs font-bold rounded-full">{{ $nbPanier }}</span>
    @endif
</a>

<a href="{{ route('client.restaurant.commandes') }}"
   class="flex items-center gap-3 px-4 py-3 rounded-xl transition font-medium
          {{ request()->routeIs('client.restaurant.commandes') ? 'bg-amber-400 text-black font-bold shadow-lg shadow-amber-400/20' : 'text-white/70 hover:bg-white/5 hover:text-white' }}">
    <span class="text-xl">🧾</span><span>Mes Commandes</span>
</a>

<a href="{{ route('client.factures.index') }}"
   class="flex items-center gap-3 px-4 py-3 rounded-xl transition font-medium
          {{ request()->routeIs('client.factures.*') ? 'bg-amber-400 text-black font-bold shadow-lg shadow-amber-400/20' : 'text-white/70 hover:bg-white/5 hover:text-white' }}">
    <span class="text-xl">📄</span><span>Mes Factures</span>
</a>

<a href="{{ route('client.profil.index') }}"
   class="flex items-center gap-3 px-4 py-3 rounded-xl transition font-medium
          {{ request()->routeIs('client.profil.*') ? 'bg-amber-400 text-black font-bold shadow-lg shadow-amber-400/20' : 'text-white/70 hover:bg-white/5 hover:text-white' }}">
    <span class="text-xl">👤</span><span>Mon Profil</span>
</a>

<a href="{{ route('client.fidelite.index') }}"
   class="flex items-center gap-3 px-4 py-3 rounded-xl transition font-medium
          {{ request()->routeIs('client.fidelite.*') ? 'bg-amber-400 text-black font-bold shadow-lg shadow-amber-400/20' : 'text-white/70 hover:bg-white/5 hover:text-white' }}">
    <span class="text-xl">⭐</span><span>Programme Fidélité</span>
</a>
@endsection

@section('sidebar-logout')
<form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit"
            class="w-full flex items-center gap-3 px-4 py-3 rounded-xl
                   text-white/50 hover:bg-red-500/10 hover:text-red-400 transition">
        <span class="text-xl">🚪</span><span>Déconnexion</span>
    </button>
</form>
@endsection