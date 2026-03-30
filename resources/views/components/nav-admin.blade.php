{{-- SECTION NAV --}}
@section('sidebar-links-nav')
<a href="{{ route('admin.dashboard') }}"
   class="flex items-center gap-3 px-4 py-3 rounded-xl transition
          {{ request()->routeIs('admin.dashboard') ? 'bg-amber-400 text-black font-bold' : 'text-white/70 hover:bg-white/5' }}">
    <span class="text-xl">🛡️</span><span>Console Admin</span>
</a>

<a href="{{ route('admin.chambres.index') }}"
   class="flex items-center gap-3 px-4 py-3 rounded-xl transition
          {{ request()->routeIs('admin.chambres.*') ? 'bg-amber-400 text-black font-bold' : 'text-white/70 hover:bg-white/5' }}">
    <span class="text-xl">🛏️</span><span>Gestion Chambres</span>
</a>

<a href="{{ route('admin.menus.index') }}"
   class="flex items-center gap-3 px-4 py-3 rounded-xl transition
          {{ request()->routeIs('admin.menus.*') ? 'bg-amber-400 text-black font-bold' : 'text-white/70 hover:bg-white/5' }}">
    <span class="text-xl">🍽️</span><span>Menus Restaurant</span>
</a>

<a href="{{ route('admin.reservations.index') }}"
   class="flex items-center gap-3 px-4 py-3 rounded-xl transition
          {{ request()->routeIs('admin.reservations.*') ? 'bg-amber-400 text-black font-bold' : 'text-white/70 hover:bg-white/5' }}">
    <span class="text-xl">📅</span><span>Réservations</span>
    @php $nbAtt = \App\Models\ReservationChambre::where('statut','en_attente')->count(); @endphp
    @if($nbAtt > 0)
        <span class="ml-auto px-2 py-0.5 bg-amber-400/20 text-amber-400 text-xs font-bold rounded-full">{{ $nbAtt }}</span>
    @endif
</a>

<a href="{{ route('admin.users.index') }}"
   class="flex items-center gap-3 px-4 py-3 rounded-xl transition
          {{ request()->routeIs('admin.users.*') ? 'bg-amber-400 text-black font-bold' : 'text-white/70 hover:bg-white/5' }}">
    <span class="text-xl">👥</span><span>Utilisateurs</span>
    @php $inactifs = \App\Models\User::where('active', false)->count(); @endphp
    @if($inactifs > 0)
        <span class="ml-auto px-2 py-0.5 bg-red-500/20 text-red-400 text-xs font-bold rounded-full">{{ $inactifs }}</span>
    @endif
</a>

<a href="{{ route('admin.logs.index') }}"
   class="flex items-center gap-3 px-4 py-3 rounded-xl transition
          {{ request()->routeIs('admin.logs.*') ? 'bg-amber-400 text-black font-bold' : 'text-white/70 hover:bg-white/5' }}">
    <span class="text-xl">📋</span><span>Logs d'activité</span>
    @php $dangerToday = \App\Models\ActivityLog::where('niveau','danger')->whereDate('created_at', today())->count(); @endphp
    @if($dangerToday > 0)
        <span class="ml-auto px-2 py-0.5 bg-red-500/20 text-red-400 text-xs font-bold rounded-full">{{ $dangerToday }}</span>
    @endif
</a>

<a href="{{ route('admin.statistiques.index') }}"
   class="flex items-center gap-3 px-4 py-3 rounded-xl transition
          {{ request()->routeIs('admin.statistiques.*') ? 'bg-amber-400 text-black font-bold' : 'text-white/70 hover:bg-white/5' }}">
    <span class="text-xl">📊</span><span>Statistiques</span>
</a>


<div class="my-3 border-t border-white/10"></div>
<p class="px-4 text-white/20 text-xs uppercase tracking-widest mb-2">Exports</p>

<a href="{{ route('admin.export.index') }}"
   class="flex items-center gap-3 px-4 py-3 rounded-xl transition
          {{ request()->routeIs('admin.export.index') ? 'bg-amber-400 text-black font-bold' : 'text-white/70 hover:bg-white/5' }}">
    <span class="text-xl">⬇️</span><span>Centre d'export</span>
</a>

<a href="{{ route('admin.export.reservations') }}"
   class="flex items-center gap-3 px-4 py-3 rounded-xl text-white/50 hover:bg-white/5 hover:text-white/70 transition text-sm">
    <span class="text-base ml-1">📅</span><span>Réservations CSV</span>
</a>

<a href="{{ route('admin.export.clients') }}"
   class="flex items-center gap-3 px-4 py-3 rounded-xl text-white/50 hover:bg-white/5 hover:text-white/70 transition text-sm">
    <span class="text-base ml-1">👥</span><span>Clients CSV</span>
</a>

<a href="{{ route('admin.export.commandes') }}"
   class="flex items-center gap-3 px-4 py-3 rounded-xl text-white/50 hover:bg-white/5 hover:text-white/70 transition text-sm">
    <span class="text-base ml-1">🍽️</span><span>Commandes CSV</span>
</a>
@endsection

{{-- BOUTON DÉCONNEXION FIXE EN BAS --}}
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
