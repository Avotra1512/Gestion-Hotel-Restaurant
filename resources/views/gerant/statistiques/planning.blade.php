@extends('layouts.dashboard')
@section('title', 'Planning Chambres')
@include('components.nav-gerant')
@section('content')

<div class="mb-8">
    <h2 class="text-2xl font-bold text-white">Planning des Chambres</h2>
    <p class="text-white/50 text-sm mt-1">Occupation sur les 14 prochains jours</p>
</div>

{{-- LÉGENDE --}}
<div class="flex flex-wrap items-center gap-4 mb-6 p-4 bg-white/5 border border-white/10 rounded-2xl">
    <p class="text-white/50 text-xs uppercase tracking-widest">Légende :</p>
    <div class="flex items-center gap-2">
        <div class="w-4 h-4 rounded bg-amber-400/30 border border-amber-400/50"></div>
        <span class="text-white/60 text-xs">En attente</span>
    </div>
    <div class="flex items-center gap-2">
        <div class="w-4 h-4 rounded bg-blue-400/30 border border-blue-400/50"></div>
        <span class="text-white/60 text-xs">Confirmée</span>
    </div>
    <div class="flex items-center gap-2">
        <div class="w-4 h-4 rounded bg-green-400/30 border border-green-400/50"></div>
        <span class="text-white/60 text-xs">Payée</span>
    </div>
    <div class="flex items-center gap-2">
        <div class="w-4 h-4 rounded bg-white/5 border border-white/10"></div>
        <span class="text-white/60 text-xs">Libre</span>
    </div>
</div>

{{-- TABLEAU PLANNING --}}
<div class="bg-white/5 border border-white/10 rounded-2xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-xs">
            <thead>
                <tr class="border-b border-white/10">
                    <th class="text-left px-4 py-3 text-white/50 uppercase tracking-widest font-medium sticky left-0 bg-neutral-900/80 backdrop-blur-sm min-w-[130px]">
                        Chambre
                    </th>
                    @foreach($jours as $jour)
                    <th class="px-2 py-3 text-center min-w-[52px]
                               {{ $jour->isToday() ? 'text-amber-400 font-bold' : 'text-white/40 font-medium' }}">
                        <div>{{ $jour->format('d/m') }}</div>
                        <div class="font-normal capitalize text-[10px] opacity-70">
                            {{ mb_substr($jour->locale('fr')->dayName, 0, 3) }}
                        </div>
                    </th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @foreach($chambres as $chambre)
                <tr class="hover:bg-white/5 transition">
                    {{-- Chambre --}}
                    <td class="px-4 py-3 sticky left-0 bg-neutral-900/90 backdrop-blur-sm">
                        <p class="text-white font-medium">{{ $chambre->numero_chambre }}</p>
                        <div class="flex items-center gap-1.5 mt-0.5">
                            <span class="text-white/40 text-[10px] capitalize">{{ $chambre->type_chambre }}</span>
                            <span class="text-white/20">·</span>
                            @if($chambre->statut === 'disponible')
                                <span class="text-green-400 text-[10px]">● Libre</span>
                            @elseif($chambre->statut === 'occupee')
                                <span class="text-red-400 text-[10px]">● Occupée</span>
                            @else
                                <span class="text-neutral-500 text-[10px]">● Hors service</span>
                            @endif
                        </div>
                    </td>

                    {{-- Cellules par jour --}}
                    @foreach($jours as $jour)
                    @php
                        $dateStr = $jour->format('Y-m-d');
                        $reservationDuJour = $chambre->reservations->first(function($r) use ($dateStr) {
                            if ($r->date_reservation) {
                                return $r->date_reservation->format('Y-m-d') === $dateStr;
                            }
                            if ($r->date_arrivee && $r->date_depart) {
                                return $dateStr >= $r->date_arrivee->format('Y-m-d')
                                    && $dateStr < $r->date_depart->format('Y-m-d');
                            }
                            return false;
                        });

                        $couleur = match($reservationDuJour?->statut) {
                            'en_attente' => 'bg-amber-400/20 border border-amber-400/40',
                            'confirmee'  => 'bg-blue-400/20 border border-blue-400/40',
                            'payee'      => 'bg-green-400/20 border border-green-400/40',
                            default      => 'bg-white/5 border border-white/5',
                        };
                    @endphp
                    <td class="px-1 py-2 text-center">
                        <div class="w-10 h-8 rounded mx-auto flex items-center justify-center {{ $couleur }}
                                    {{ $jour->isToday() ? 'ring-1 ring-amber-400/50' : '' }}">
                            @if($reservationDuJour)
                                <span class="text-[9px] font-bold text-white/70" title="Réservation #{{ str_pad($reservationDuJour->id, 4, '0', STR_PAD_LEFT) }} — {{ $reservationDuJour->nom }}">
                                    #{{ str_pad($reservationDuJour->id, 4, '0', STR_PAD_LEFT) }}
                                </span>
                            @endif
                        </div>
                    </td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<p class="text-white/30 text-xs mt-4 text-center">
    Planning basé sur les réservations en attente, confirmées et payées.
</p>

@endsection