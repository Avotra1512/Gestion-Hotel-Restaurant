@extends('layouts.dashboard')
@section('title', "Logs d'activité")
@section('sidebar-links') @include('components.nav-admin') @endsection
@section('content')

@if(session('success'))
<div class="mb-6 p-4 bg-amber-400/10 border border-amber-400/30 text-amber-400 rounded-xl text-sm flex items-center gap-3">
    <span>✅</span> {{ session('success') }}
</div>
@endif

{{-- EN-TÊTE --}}
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
    <div>
        <h2 class="text-2xl font-bold text-white">Logs d'activité</h2>
        <p class="text-white/50 text-sm mt-1">Historique de toutes les actions du système</p>
    </div>
    <form method="POST" action="{{ route('admin.logs.purge') }}"
          onsubmit="return confirm('Supprimer les logs de plus de 30 jours ?')">
        @csrf @method('DELETE')
        <button class="px-5 py-2 border border-red-500/20 text-red-400 rounded-full text-xs hover:bg-red-500/10 transition">
            🗑 Purger les anciens logs
        </button>
    </form>
</div>

{{-- STATS --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="bg-white/5 border border-white/10 rounded-2xl p-5">
        <p class="text-white/50 text-xs uppercase tracking-widest mb-2">Total</p>
        <p class="text-3xl font-bold text-white">{{ number_format($stats['total']) }}</p>
    </div>
    <div class="bg-white/5 border border-blue-400/20 rounded-2xl p-5">
        <p class="text-white/50 text-xs uppercase tracking-widest mb-2">Aujourd'hui</p>
        <p class="text-3xl font-bold text-blue-400">{{ $stats['today'] }}</p>
    </div>
    <div class="bg-white/5 border border-amber-400/20 rounded-2xl p-5">
        <p class="text-white/50 text-xs uppercase tracking-widest mb-2">Avertissements</p>
        <p class="text-3xl font-bold text-amber-400">{{ $stats['warning'] }}</p>
    </div>
    <div class="bg-white/5 border border-red-400/20 rounded-2xl p-5">
        <p class="text-white/50 text-xs uppercase tracking-widest mb-2">Actions critiques</p>
        <p class="text-3xl font-bold text-red-400">{{ $stats['danger'] }}</p>
    </div>
</div>

{{-- FILTRES --}}
<div class="bg-white/5 border border-white/10 rounded-2xl p-5 mb-6">
    <form method="GET" action="{{ route('admin.logs.index') }}" class="flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-white/50 text-xs uppercase tracking-widest mb-2">Recherche</label>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Description..."
                   class="w-full bg-black/40 border border-white/10 text-white placeholder-white/30
                          rounded-xl px-4 py-2 text-sm focus:border-amber-400 focus:outline-none transition">
        </div>
        <div>
            <label class="block text-white/50 text-xs uppercase tracking-widest mb-2">Module</label>
            <select name="module" class="bg-black/40 border border-white/10 text-white rounded-xl px-4 py-2 text-sm focus:border-amber-400 focus:outline-none transition">
                <option value="">Tous</option>
                <option value="reservations" {{ request('module')==='reservations' ? 'selected':'' }}>Réservations</option>
                <option value="commandes"    {{ request('module')==='commandes'    ? 'selected':'' }}>Commandes</option>
                <option value="chambres"     {{ request('module')==='chambres'     ? 'selected':'' }}>Chambres</option>
                <option value="menus"        {{ request('module')==='menus'        ? 'selected':'' }}>Menus</option>
                <option value="users"        {{ request('module')==='users'        ? 'selected':'' }}>Utilisateurs</option>
            </select>
        </div>
        <div>
            <label class="block text-white/50 text-xs uppercase tracking-widest mb-2">Niveau</label>
            <select name="niveau" class="bg-black/40 border border-white/10 text-white rounded-xl px-4 py-2 text-sm focus:border-amber-400 focus:outline-none transition">
                <option value="">Tous</option>
                <option value="info"    {{ request('niveau')==='info'    ? 'selected':'' }}>Info</option>
                <option value="success" {{ request('niveau')==='success' ? 'selected':'' }}>Succès</option>
                <option value="warning" {{ request('niveau')==='warning' ? 'selected':'' }}>Avertissement</option>
                <option value="danger"  {{ request('niveau')==='danger'  ? 'selected':'' }}>Danger</option>
            </select>
        </div>
        <div>
            <label class="block text-white/50 text-xs uppercase tracking-widest mb-2">Rôle</label>
            <select name="role" class="bg-black/40 border border-white/10 text-white rounded-xl px-4 py-2 text-sm focus:border-amber-400 focus:outline-none transition">
                <option value="">Tous</option>
                <option value="admin"  {{ request('role')==='admin'  ? 'selected':'' }}>Admin</option>
                <option value="gerant" {{ request('role')==='gerant' ? 'selected':'' }}>Gérant</option>
                <option value="client" {{ request('role')==='client' ? 'selected':'' }}>Client</option>
            </select>
        </div>
        <button type="submit" class="px-6 py-2 bg-amber-400 text-black font-semibold rounded-full text-sm hover:bg-amber-300 transition">Filtrer</button>
        <a href="{{ route('admin.logs.index') }}" class="px-6 py-2 border border-white/10 text-white/60 rounded-full text-sm hover:bg-white/5 transition">Réinitialiser</a>
    </form>
</div>

{{-- TABLEAU --}}
@if($logs->isEmpty())
<div class="bg-white/5 border border-white/10 rounded-2xl p-16 text-center">
    <p class="text-5xl mb-4">📋</p>
    <p class="text-white/60">Aucun log trouvé.</p>
</div>
@else
<div class="bg-white/5 border border-white/10 rounded-2xl overflow-hidden mb-6">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-white/10 text-white/50 text-xs uppercase tracking-widest">
                    <th class="text-left px-5 py-4">Action</th>
                    <th class="text-left px-5 py-4">Utilisateur</th>
                    <th class="text-left px-5 py-4">Module</th>
                    <th class="text-left px-5 py-4">Niveau</th>
                    <th class="text-left px-5 py-4">IP</th>
                    <th class="text-left px-5 py-4">Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @foreach($logs as $log)
                <tr class="hover:bg-white/5 transition">
                    <td class="px-5 py-4">
                        <div class="flex items-start gap-3">
                            <span class="text-xl flex-shrink-0">{{ $log->icone }}</span>
                            <div>
                                <p class="text-white text-sm">{{ $log->description }}</p>
                                <p class="text-white/30 text-xs mt-0.5 font-mono">{{ $log->action }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-4">
                        @if($log->user)
                        <p class="text-white text-sm">{{ $log->user->name }}</p>
                        <span class="text-white/40 text-xs capitalize">{{ $log->role }}</span>
                        @else
                        <span class="text-white/30 text-xs">Système</span>
                        @endif
                    </td>
                    <td class="px-5 py-4">
                        <span class="px-3 py-1 bg-white/5 border border-white/10 text-white/60 rounded-full text-xs capitalize">
                            {{ $log->module ?? '—' }}
                        </span>
                    </td>
                    <td class="px-5 py-4">
                        <span class="px-3 py-1 rounded-full text-xs font-semibold border {{ $log->couleurNiveau() }}">
                            {{ ucfirst($log->niveau) }}
                        </span>
                    </td>
                    <td class="px-5 py-4 text-white/30 text-xs font-mono">{{ $log->ip ?? '—' }}</td>
                    <td class="px-5 py-4 text-white/50 text-xs">
                        {{ $log->created_at->format('d/m/Y') }}<br>
                        <span class="text-white/30">{{ $log->created_at->format('H:i:s') }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{ $logs->links() }}
@endif

@endsection
