@extends('layouts.dashboard')
@section('title', 'Clients')
@include('components.nav-gerant')
@section('content')

<div class="mb-8">
    <h2 class="text-2xl font-bold text-white">Clients</h2>
    <p class="text-white/50 text-sm mt-1">Liste de tous les clients enregistrés</p>
</div>

<div class="bg-white/5 border border-white/10 rounded-2xl p-5 mb-6">
    <form method="GET" action="{{ route('gerant.clients.index') }}" class="flex gap-4 items-end">
        <div class="flex-1">
            <label class="block text-white/50 text-xs uppercase tracking-widest mb-2">Recherche</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Nom ou email..."
                   class="w-full bg-black/40 border border-white/10 text-white placeholder-white/30 rounded-xl px-4 py-2 text-sm focus:border-amber-400 focus:outline-none transition">
        </div>
        <button type="submit" class="px-6 py-2 bg-amber-400 text-black font-semibold rounded-full text-sm hover:bg-amber-300 transition">Rechercher</button>
        <a href="{{ route('gerant.clients.index') }}" class="px-6 py-2 border border-white/10 text-white/60 rounded-full text-sm hover:bg-white/5 transition">Réinitialiser</a>
    </form>
</div>

@if($clients->isEmpty())
<div class="bg-white/5 border border-white/10 rounded-2xl p-16 text-center">
    <p class="text-5xl mb-4">👥</p><p class="text-white/60">Aucun client trouvé.</p>
</div>
@else
<div class="bg-white/5 border border-white/10 rounded-2xl overflow-hidden mb-6">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-white/10 text-white/50 text-xs uppercase tracking-widest">
                <th class="text-left px-6 py-4">Client</th>
                <th class="text-left px-6 py-4">Email</th>
                <th class="text-left px-6 py-4">Inscrit le</th>
                <th class="text-left px-6 py-4">Réservations</th>
                <th class="text-right px-6 py-4">Action</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-white/5">
            @foreach($clients as $client)
            <tr class="hover:bg-white/5 transition">
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-amber-400/10 border border-amber-400/20 flex items-center justify-center text-sm font-bold text-amber-400">
                            {{ strtoupper(substr($client->name,0,1)) }}
                        </div>
                        <p class="text-white font-medium">{{ $client->name }}</p>
                    </div>
                </td>
                <td class="px-6 py-4 text-white/60">{{ $client->email }}</td>
                <td class="px-6 py-4 text-white/60">{{ $client->created_at->format('d/m/Y') }}</td>
                <td class="px-6 py-4">
                    <span class="px-3 py-1 bg-amber-400/10 border border-amber-400/20 text-amber-400 rounded-full text-xs font-semibold">
                        {{ $client->reservation_chambres_count }} réservation(s)
                    </span>
                </td>
                <td class="px-6 py-4 text-right">
                    <a href="{{ route('gerant.clients.show', $client) }}"
                       class="px-4 py-1.5 border border-white/10 text-white/60 rounded-full text-xs hover:bg-white/5 hover:text-white transition">Voir →</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
{{ $clients->links() }}
@endif

@endsection