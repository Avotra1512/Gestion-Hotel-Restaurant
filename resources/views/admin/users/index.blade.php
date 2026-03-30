@extends('layouts.dashboard')
@section('title', 'Gestion Utilisateurs')
@include('components.nav-admin')
@section('content')

@if(session('success'))
<div class="mb-6 p-4 bg-amber-400/10 border border-amber-400/30 text-amber-400 rounded-xl text-sm flex items-center gap-3">
    <span>✅</span> {{ session('success') }}
</div>
@endif
@if($errors->any())
<div class="mb-6 p-4 bg-red-500/10 border border-red-500/20 text-red-400 rounded-xl text-sm">
    @foreach($errors->all() as $e) <p>⚠️ {{ $e }}</p> @endforeach
</div>
@endif

{{-- EN-TÊTE --}}
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
    <div>
        <h2 class="text-2xl font-bold text-white">Gestion Utilisateurs</h2>
        <p class="text-white/50 text-sm mt-1">Gérez tous les comptes de la plateforme</p>
    </div>
    <a href="{{ route('admin.users.create') }}"
       class="flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-amber-400 to-amber-300
              text-black font-semibold rounded-full shadow-lg shadow-amber-400/20
              hover:scale-[1.03] transition-all text-sm">
        + Ajouter un utilisateur
    </a>
</div>

{{-- STATS --}}
<div class="grid grid-cols-2 lg:grid-cols-6 gap-3 mb-8">
    <div class="lg:col-span-1 bg-white/5 border border-white/10 rounded-2xl p-4 text-center">
        <p class="text-2xl font-bold text-white">{{ $stats['total'] }}</p>
        <p class="text-white/40 text-xs mt-1">Total</p>
    </div>
    <div class="bg-white/5 border border-red-400/20 rounded-2xl p-4 text-center">
        <p class="text-2xl font-bold text-red-400">{{ $stats['admins'] }}</p>
        <p class="text-white/40 text-xs mt-1">Admins</p>
    </div>
    <div class="bg-white/5 border border-purple-400/20 rounded-2xl p-4 text-center">
        <p class="text-2xl font-bold text-purple-400">{{ $stats['gerants'] }}</p>
        <p class="text-white/40 text-xs mt-1">Gérants</p>
    </div>
    <div class="bg-white/5 border border-blue-400/20 rounded-2xl p-4 text-center">
        <p class="text-2xl font-bold text-blue-400">{{ $stats['clients'] }}</p>
        <p class="text-white/40 text-xs mt-1">Clients</p>
    </div>
    <div class="bg-white/5 border border-green-400/20 rounded-2xl p-4 text-center">
        <p class="text-2xl font-bold text-green-400">{{ $stats['actifs'] }}</p>
        <p class="text-white/40 text-xs mt-1">Actifs</p>
    </div>
    <div class="bg-white/5 border border-red-400/20 rounded-2xl p-4 text-center">
        <p class="text-2xl font-bold text-red-400">{{ $stats['inactifs'] }}</p>
        <p class="text-white/40 text-xs mt-1">Inactifs</p>
    </div>
</div>

{{-- FILTRES --}}
<div class="bg-white/5 border border-white/10 rounded-2xl p-5 mb-6">
    <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-white/50 text-xs uppercase tracking-widest mb-2">Recherche</label>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Nom ou email..."
                   class="w-full bg-black/40 border border-white/10 text-white placeholder-white/30
                          rounded-xl px-4 py-2 text-sm focus:border-amber-400 focus:outline-none transition">
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
        <div>
            <label class="block text-white/50 text-xs uppercase tracking-widest mb-2">Statut</label>
            <select name="active" class="bg-black/40 border border-white/10 text-white rounded-xl px-4 py-2 text-sm focus:border-amber-400 focus:outline-none transition">
                <option value="">Tous</option>
                <option value="1" {{ request('active')==='1' ? 'selected':'' }}>Actif</option>
                <option value="0" {{ request('active')==='0' ? 'selected':'' }}>Inactif</option>
            </select>
        </div>
        <button type="submit" class="px-6 py-2 bg-amber-400 text-black font-semibold rounded-full text-sm hover:bg-amber-300 transition">Filtrer</button>
        <a href="{{ route('admin.users.index') }}" class="px-6 py-2 border border-white/10 text-white/60 rounded-full text-sm hover:bg-white/5 transition">Réinitialiser</a>
    </form>
</div>

{{-- TABLEAU --}}
@if($users->isEmpty())
<div class="bg-white/5 border border-white/10 rounded-2xl p-16 text-center">
    <p class="text-5xl mb-4">👥</p>
    <p class="text-white/60">Aucun utilisateur trouvé.</p>
</div>
@else
<div class="bg-white/5 border border-white/10 rounded-2xl overflow-hidden mb-6">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-white/10 text-white/50 text-xs uppercase tracking-widest">
                <th class="text-left px-6 py-4">Utilisateur</th>
                <th class="text-left px-6 py-4">Rôle</th>
                <th class="text-left px-6 py-4">Statut</th>
                <th class="text-left px-6 py-4">Inscrit le</th>
                <th class="text-center px-6 py-4">Toggle</th>
                <th class="text-right px-6 py-4">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-white/5">
            @foreach($users as $user)
            <tr class="hover:bg-white/5 transition {{ !$user->active ? 'opacity-60' : '' }}">

                {{-- Avatar + nom --}}
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full border flex items-center justify-center text-sm font-bold flex-shrink-0
                                    {{ $user->active ? 'bg-amber-400/10 border-amber-400/20 text-amber-400' : 'bg-white/5 border-white/10 text-white/30' }}">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="text-white font-medium flex items-center gap-2">
                                {{ $user->name }}
                                @if($user->id === auth()->id())
                                    <span class="text-[10px] px-2 py-0.5 bg-amber-400/20 text-amber-400 rounded-full">Vous</span>
                                @endif
                            </p>
                            <p class="text-white/40 text-xs">{{ $user->email }}</p>
                        </div>
                    </div>
                </td>

                {{-- Rôle avec changement rapide --}}
                <td class="px-6 py-4">
                    @if($user->id !== auth()->id())
                    <form method="POST" action="{{ route('admin.users.role', $user) }}" class="flex items-center gap-2">
                        @csrf @method('PATCH')
                        <select name="role" onchange="this.form.submit()"
                                class="bg-black/40 border border-white/10 text-white rounded-lg px-3 py-1.5 text-xs
                                       focus:border-amber-400 focus:outline-none transition cursor-pointer">
                            <option value="admin"  {{ $user->role==='admin'  ? 'selected':'' }}>🛡️ Admin</option>
                            <option value="gerant" {{ $user->role==='gerant' ? 'selected':'' }}>🏨 Gérant</option>
                            <option value="client" {{ $user->role==='client' ? 'selected':'' }}>👤 Client</option>
                        </select>
                    </form>
                    @else
                    <span class="px-3 py-1 rounded-full text-xs font-semibold border {{ $user->couleurRole() }}">
                        {{ $user->libelleRole() }}
                    </span>
                    @endif
                </td>

                {{-- Statut --}}
                <td class="px-6 py-4">
                    @if($user->active)
                        <span class="flex items-center gap-1.5 text-green-400 text-xs">
                            <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span> Actif
                        </span>
                    @else
                        <span class="flex items-center gap-1.5 text-red-400 text-xs">
                            <span class="w-2 h-2 rounded-full bg-red-400"></span> Inactif
                        </span>
                    @endif
                </td>

                {{-- Date inscription --}}
                <td class="px-6 py-4 text-white/50 text-xs">
                    {{ $user->created_at->format('d/m/Y') }}<br>
                    <span class="text-white/30">{{ $user->created_at->diffForHumans() }}</span>
                </td>

                {{-- Toggle actif/inactif --}}
                <td class="px-6 py-4 text-center">
                    @if($user->id !== auth()->id())
                    <form method="POST" action="{{ route('admin.users.toggle-active', $user) }}">
                        @csrf @method('PATCH')
                        <button type="submit"
                                title="{{ $user->active ? 'Désactiver' : 'Activer' }}"
                                class="relative inline-flex items-center w-11 h-6 rounded-full transition-colors duration-300
                                       {{ $user->active ? 'bg-green-500' : 'bg-white/20' }}">
                            <span class="inline-block w-4 h-4 bg-white rounded-full shadow transition-transform duration-300
                                         {{ $user->active ? 'translate-x-6' : 'translate-x-1' }}"></span>
                        </button>
                    </form>
                    @else
                    <span class="text-white/20 text-xs">—</span>
                    @endif
                </td>

                {{-- Actions --}}
                <td class="px-6 py-4">
                    <div class="flex items-center justify-end gap-2">
                        <a href="{{ route('admin.users.edit', $user) }}"
                           class="px-4 py-1.5 border border-amber-400/30 text-amber-400 rounded-full text-xs hover:bg-amber-400/10 transition">
                            Modifier
                        </a>
                        @if($user->id !== auth()->id())
                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                              id="form-delete-{{ $user->id }}">
                            @csrf @method('DELETE')
                        </form>
                        <button type="button"
                                onclick="if(confirm('Supprimer « {{ addslashes($user->name) }} » ? Cette action est irréversible.')) { document.getElementById('form-delete-{{ $user->id }}').submit(); }"
                                class="px-4 py-1.5 border border-red-500/20 text-red-400 rounded-full text-xs hover:bg-red-500/10 transition">
                            Supprimer
                        </button>
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{ $users->links() }}
@endif

@endsection