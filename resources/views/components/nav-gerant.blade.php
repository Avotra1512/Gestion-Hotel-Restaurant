@section('sidebar-links-nav')
<a href="{{ route('gerant.dashboard') }}"
   class="flex items-center gap-3 px-4 py-3 rounded-xl transition font-medium
          {{ request()->routeIs('gerant.dashboard') ? 'bg-amber-400 text-black font-bold shadow-lg shadow-amber-400/20' : 'text-white/70 hover:bg-white/5 hover:text-white' }}">
    <span class="text-xl">🏨</span><span>Tableau de bord</span>
</a>

<a href="{{ route('gerant.reservations.index') }}"
   class="flex items-center gap-3 px-4 py-3 rounded-xl transition font-medium
          {{ request()->routeIs('gerant.reservations.*') ? 'bg-amber-400 text-black font-bold shadow-lg shadow-amber-400/20' : 'text-white/70 hover:bg-white/5 hover:text-white' }}">
    <span class="text-xl">📋</span>
    <span>Réservations</span>
    @php $nbRes = \App\Models\ReservationChambre::where('statut','en_attente')->count(); @endphp
    @if($nbRes > 0)
        <span class="ml-auto px-2 py-0.5 bg-amber-400 text-black text-xs font-bold rounded-full">{{ $nbRes }}</span>
    @endif
</a>

<a href="{{ route('gerant.commandes.index') }}"
   class="flex items-center gap-3 px-4 py-3 rounded-xl transition font-medium
          {{ request()->routeIs('gerant.commandes.*') ? 'bg-amber-400 text-black font-bold shadow-lg shadow-amber-400/20' : 'text-white/70 hover:bg-white/5 hover:text-white' }}">
    <span class="text-xl">🍽️</span>
    <span>Commandes</span>
    @php $nbCmd = \App\Models\CommandeRepas::whereIn('statut',['en_attente','en_preparation'])->count(); @endphp
    @if($nbCmd > 0)
        <span class="ml-auto px-2 py-0.5 bg-amber-400 text-black text-xs font-bold rounded-full">{{ $nbCmd }}</span>
    @endif
</a>

<a href="{{ route('gerant.menus.index') }}"
   class="flex items-center gap-3 px-4 py-3 rounded-xl transition font-medium
          {{ request()->routeIs('gerant.menus.*') ? 'bg-amber-400 text-black font-bold shadow-lg shadow-amber-400/20' : 'text-white/70 hover:bg-white/5 hover:text-white' }}">
    <span class="text-xl">🍴</span><span>Menu Restaurant</span>
</a>

<a href="{{ route('gerant.clients.index') }}"
   class="flex items-center gap-3 px-4 py-3 rounded-xl transition font-medium
          {{ request()->routeIs('gerant.clients.*') ? 'bg-amber-400 text-black font-bold shadow-lg shadow-amber-400/20' : 'text-white/70 hover:bg-white/5 hover:text-white' }}">
    <span class="text-xl">👥</span><span>Clients</span>
</a>

<div class="my-3 border-t border-white/10"></div>
<p class="px-4 text-white/20 text-xs uppercase tracking-widest mb-2">Analyses</p>

<a href="{{ route('gerant.statistiques.ventes') }}"
   class="flex items-center gap-3 px-4 py-3 rounded-xl transition font-medium
          {{ request()->routeIs('gerant.statistiques.ventes') ? 'bg-amber-400 text-black font-bold shadow-lg shadow-amber-400/20' : 'text-white/70 hover:bg-white/5 hover:text-white' }}">
    <span class="text-xl">📊</span><span>Statistiques Ventes</span>
</a>

<a href="{{ route('gerant.statistiques.planning') }}"
   class="flex items-center gap-3 px-4 py-3 rounded-xl transition font-medium
          {{ request()->routeIs('gerant.statistiques.planning') ? 'bg-amber-400 text-black font-bold shadow-lg shadow-amber-400/20' : 'text-white/70 hover:bg-white/5 hover:text-white' }}">
    <span class="text-xl">🏨</span><span>Planning Chambres</span>
</a>

<a href="{{ route('gerant.statistiques.rapport') }}"
   class="flex items-center gap-3 px-4 py-3 rounded-xl transition font-medium
          {{ request()->routeIs('gerant.statistiques.rapport') ? 'bg-amber-400 text-black font-bold shadow-lg shadow-amber-400/20' : 'text-white/70 hover:bg-white/5 hover:text-white' }}">
    <span class="text-xl">📋</span><span>Rapports Quotidiens</span>
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