@extends('layouts.dashboard')
@section('title', 'Mes Notifications')

@section('sidebar-links')
    @include('components.nav-' . Auth::user()->role)
@endsection

@section('content')

<div class="flex justify-between items-center mb-8">
    <div>
        <h2 class="text-2xl font-bold text-white">Notifications</h2>
        <p class="text-white/50 text-sm mt-1">Toutes vos notifications</p>
    </div>
    @if(Auth::user()->unreadNotifications->count() > 0)
    <form method="POST" action="{{ route('notifications.read-all') }}">
        @csrf @method('PATCH')
        <button type="submit"
                class="px-5 py-2 border border-white/10 text-white/60 rounded-full text-sm hover:bg-white/5 transition">
            ✓ Tout marquer comme lu
        </button>
    </form>
    @endif
</div>

@if($notifications->isEmpty())
<div class="bg-white/5 border border-white/10 rounded-2xl p-16 text-center">
    <p class="text-5xl mb-4">🔔</p>
    <p class="text-white/60">Aucune notification.</p>
</div>
@else
<div class="space-y-2 mb-6">
    @foreach($notifications as $notif)
    @php $d = $notif->data; $lue = !is_null($notif->read_at); @endphp
    <div class="flex items-start gap-4 p-5 rounded-2xl border transition
                {{ $lue ? 'bg-white/3 border-white/5' : 'bg-amber-400/3 border-amber-400/10' }}">

        {{-- Icône --}}
        <span class="text-2xl flex-shrink-0">{{ $d['icone'] ?? '🔔' }}</span>

        {{-- Contenu --}}
        <div class="flex-grow">
            <div class="flex justify-between items-start gap-4">
                <p class="text-white {{ $lue ? '' : 'font-semibold' }} text-sm">
                    {{ $d['titre'] ?? '' }}
                </p>
                <span class="text-white/30 text-xs flex-shrink-0">
                    {{ $notif->created_at->diffForHumans() }}
                </span>
            </div>
            <p class="text-white/50 text-sm mt-1">{{ $d['message'] ?? '' }}</p>
            @if(!empty($d['lien']) && $d['lien'] !== '#')
            <a href="{{ $d['lien'] }}"
               class="inline-block mt-2 text-amber-400/70 text-xs hover:text-amber-400 transition">
                Voir le détail →
            </a>
            @endif
        </div>

        {{-- Point non lu --}}
        @if(!$lue)
        <div class="w-2.5 h-2.5 rounded-full bg-amber-400 flex-shrink-0 mt-2"></div>
        @endif
    </div>
    @endforeach
</div>

{{ $notifications->links() }}
@endif

@endsection
