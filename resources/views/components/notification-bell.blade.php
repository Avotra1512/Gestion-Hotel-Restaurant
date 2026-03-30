@php
    /** @var \App\Models\User $authUser */
    $authUser = Auth::user();
    $nonLues  = $authUser->unreadNotifications->take(6);
    $nb       = $authUser->unreadNotifications->count();
@endphp

<div class="relative" x-data="{ open: false }">

    {{-- Bouton cloche --}}
    <button @click="open = !open" @click.outside="open = false"
            class="relative w-10 h-10 rounded-full border border-white/10
                   flex items-center justify-center text-white/60
                   hover:border-amber-400/50 hover:text-amber-400 transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>

        {{-- Badge compteur --}}
        @if($nb > 0)
        <span class="absolute -top-1 -right-1 w-5 h-5 rounded-full bg-amber-400 text-black
                     text-[10px] font-bold flex items-center justify-center leading-none">
            {{ $nb > 9 ? '9+' : $nb }}
        </span>
        @endif
    </button>

    {{-- Dropdown --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95 translate-y-1"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-1"
         style="display:none"
         class="absolute right-0 top-14 w-80 bg-zinc-900 border border-white/10
                rounded-2xl shadow-2xl shadow-black/60 z-50 overflow-hidden">

        {{-- Header dropdown --}}
        <div class="px-5 py-4 border-b border-white/10 flex justify-between items-center">
            <div class="flex items-center gap-2">
                <h3 class="text-white font-semibold text-sm">Notifications</h3>
                @if($nb > 0)
                <span class="px-2 py-0.5 bg-amber-400/20 text-amber-400 text-xs font-bold rounded-full">
                    {{ $nb }} nouvelle(s)
                </span>
                @endif
            </div>
            @if($nb > 0)
            <form method="POST" action="{{ route('notifications.read-all') }}">
                @csrf @method('PATCH')
                <button type="submit" class="text-white/40 text-xs hover:text-amber-400 transition">
                    Tout lire
                </button>
            </form>
            @endif
        </div>

        {{-- Liste notifications --}}
        <div class="max-h-72 overflow-y-auto divide-y divide-white/5">
            @forelse($nonLues as $notif)
            @php $d = $notif->data; @endphp
            <a href="{{ $d['lien'] ?? '#' }}"
               class="flex items-start gap-3 px-5 py-4 hover:bg-white/5 transition"
               onclick="markRead('{{ $notif->id }}')">
                <span class="text-xl flex-shrink-0">{{ $d['icone'] ?? '🔔' }}</span>
                <div class="flex-grow min-w-0">
                    <p class="text-white text-xs font-semibold">{{ $d['titre'] ?? '' }}</p>
                    <p class="text-white/50 text-xs mt-0.5 line-clamp-2 leading-relaxed">
                        {{ $d['message'] ?? '' }}
                    </p>
                    <p class="text-white/25 text-[10px] mt-1">
                        {{ $notif->created_at->diffForHumans() }}
                    </p>
                </div>
                <div class="w-2 h-2 rounded-full bg-amber-400 flex-shrink-0 mt-1.5"></div>
            </a>
            @empty
            <div class="px-5 py-10 text-center">
                <p class="text-3xl mb-2">🔔</p>
                <p class="text-white/40 text-xs">Aucune nouvelle notification</p>
            </div>
            @endforelse
        </div>

        {{-- Footer --}}
        <div class="px-5 py-3 border-t border-white/10">
            <a href="{{ route('notifications.index') }}"
               class="block text-center text-amber-400/70 text-xs hover:text-amber-400 transition">
                Voir toutes les notifications →
            </a>
        </div>
    </div>
</div>

<script>
function markRead(id) {
    fetch('/notifications/' + id + '/read', {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        }
    });
}
</script>
