{{-- resources/views/client/chambres/reserver.blade.php --}}
@extends('layouts.dashboard')

@section('title', 'Réserver — ' . $chambre->numero_chambre)

@include('components.nav-client')

@section('content')

    {{-- EN-TÊTE --}}
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('client.chambres.index') }}"
            class="w-10 h-10 flex items-center justify-center rounded-full border border-white/10
              text-white/60 hover:border-amber-400/50 hover:text-amber-400 transition">
            ←
        </a>
        <div>
            <h2 class="text-2xl font-bold text-white tracking-wide">Réserver une chambre</h2>
            <p class="text-white/50 text-sm mt-1">Complétez votre demande de réservation</p>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-8 max-w-6xl">

        {{-- ── FORMULAIRE (2/3) ───────────────────────────────────────── --}}
        <div class="lg:col-span-2">
            <form method="POST" action="{{ route('client.reservations.store') }}"
                class="bg-white/5 border border-white/10 rounded-2xl p-8 space-y-7">
                @csrf
                <input type="hidden" name="chambre_id" value="{{ $chambre->id }}">

                {{-- ERREURS GLOBALES --}}
                @if ($errors->any())
                    <div class="p-4 bg-red-500/10 border border-red-500/20 rounded-xl text-red-400 text-sm space-y-1">
                        @foreach ($errors->all() as $error)
                            <p>⚠️ {{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                {{-- ── SECTION 1 : Mode de réservation ──────────────── --}}
                <div>
                    <h3 class="text-white font-semibold mb-4 flex items-center gap-2">
                        <span
                            class="w-6 h-6 rounded-full bg-amber-400 text-black text-xs flex items-center justify-center font-bold">1</span>
                        Type de séjour
                    </h3>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="cursor-pointer">
                            {{-- Radio nuit unique --}}
                            <input type="radio" name="mode_reservation" value="nuit_unique" class="sr-only peer"
                                {{ old('mode_reservation', 'nuit_unique') === 'nuit_unique' ? 'checked' : '' }}>
                            <div
                                class="p-4 rounded-xl border border-white/10 text-center transition
                                    peer-checked:border-amber-400 peer-checked:bg-amber-400/5 hover:border-white/30">
                                <p class="text-2xl mb-1">🌙</p>
                                <p class="text-white text-sm font-medium">Nuit unique</p>
                                <p class="text-white/40 text-xs mt-1">Une seule nuit</p>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            {{-- Radio séjour --}}
                            <input type="radio" name="mode_reservation" value="sejour" class="sr-only peer"
                                {{ old('mode_reservation') === 'sejour' ? 'checked' : '' }}>
                            <div
                                class="p-4 rounded-xl border border-white/10 text-center transition
                                    peer-checked:border-amber-400 peer-checked:bg-amber-400/5 hover:border-white/30">
                                <p class="text-2xl mb-1">🗓️</p>
                                <p class="text-white text-sm font-medium">Séjour</p>
                                <p class="text-white/40 text-xs mt-1">Plusieurs nuits</p>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- ── SECTION 2 : Dates ────────────────────────────── --}}
                <div>
                    <h3 class="text-white font-semibold mb-4 flex items-center gap-2">
                        <span
                            class="w-6 h-6 rounded-full bg-amber-400 text-black text-xs flex items-center justify-center font-bold">2</span>
                        Choisissez vos dates
                    </h3>

                    {{-- Nuit unique --}}
                    <div id="bloc-nuit-unique" class="{{ old('mode_reservation', 'nuit_unique') !== 'nuit_unique' ? 'hidden' : '' }}">
                        <label class="block text-white/70 text-sm tracking-wide mb-2">
                            Date de la nuit <span class="text-amber-400">*</span>
                        </label>
                        <input type="date" name="date_reservation" id="date-reservation"
                            value="{{ old('date_reservation') }}" min="{{ date('Y-m-d') }}"
                            class="w-full bg-black/40 border border-white/10 text-white rounded-xl px-4 py-3 text-sm
                                  focus:border-amber-400 focus:outline-none transition
                                  [color-scheme:dark]" />
                        @error('date_reservation')
                            <p class="text-red-400 text-xs mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Séjour --}}
                    <div id="bloc-sejour" class="{{ old('mode_reservation') !== 'sejour' ? 'hidden' : '' }}">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-white/70 text-sm tracking-wide mb-2">
                                    Date d'arrivée <span class="text-amber-400">*</span>
                                </label>
                                <input type="date" name="date_arrivee" value="{{ old('date_arrivee') }}"
                                    min="{{ date('Y-m-d') }}" id="date-arrivee"
                                    class="w-full bg-black/40 border border-white/10 text-white rounded-xl px-4 py-3 text-sm
                                          focus:border-amber-400 focus:outline-none transition [color-scheme:dark]" />
                                @error('date_arrivee')
                                    <p class="text-red-400 text-xs mt-2">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-white/70 text-sm tracking-wide mb-2">
                                    Date de départ <span class="text-amber-400">*</span>
                                </label>
                                <input type="date" name="date_depart" value="{{ old('date_depart') }}"
                                    min="{{ date('Y-m-d', strtotime('+1 day')) }}" id="date-depart"
                                    class="w-full bg-black/40 border border-white/10 text-white rounded-xl px-4 py-3 text-sm
                                          focus:border-amber-400 focus:outline-none transition [color-scheme:dark]" />
                                @error('date_depart')
                                    <p class="text-red-400 text-xs mt-2">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Message nombre de nuits --}}
                        <div id="msg-nuits"
                            class="hidden mt-3 px-4 py-2.5 bg-amber-400/5 border border-amber-400/20 rounded-xl">
                            <p class="text-amber-400 text-sm font-medium" id="texte-nuits"></p>
                        </div>
                    </div>

                    {{-- Dates indisponibles --}}
                    @if (!empty($datesIndisponibles))
                        <div class="mt-3 p-3 bg-amber-400/5 border border-amber-400/20 rounded-xl">
                            <p class="text-amber-400/80 text-xs">
                                ⚠️ Certaines dates sont déjà réservées. Veuillez les éviter lors de votre sélection.
                            </p>
                        </div>
                    @endif

                    {{-- Alerte date bloquée (affichée dynamiquement par JS) --}}
                    <div id="alerte-date-bloquee" class="hidden mt-3 p-4 bg-red-500/10 border border-red-500/20 rounded-xl flex items-start gap-3">
                        <span class="text-red-400 text-lg flex-shrink-0">🚫</span>
                        <div>
                            <p class="text-red-400 text-sm font-semibold">Date non disponible</p>
                            <p class="text-red-400/70 text-xs mt-1" id="alerte-date-texte"></p>
                        </div>
                    </div>
                        {{-- Section Total à payer --}}
                    <div id="bloc-total" class="hidden mt-5 p-5 bg-amber-400/5 border border-amber-400/20 rounded-xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-white/50 text-xs uppercase tracking-widest mb-1">Total à payer</p>
                                <p class="text-white/60 text-sm" id="detail-total">—</p>
                            </div>
                            <div class="text-right">
                                <p class="text-amber-400 font-bold text-3xl" id="montant-total">0</p>
                                <p class="text-white/40 text-sm">Ar</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── SECTION 3 : Informations personnelles ─────────── --}}
                <div>
                    <h3 class="text-white font-semibold mb-4 flex items-center gap-2">
                        <span
                            class="w-6 h-6 rounded-full bg-amber-400 text-black text-xs flex items-center justify-center font-bold">3</span>
                        Vos informations
                    </h3>
                    <div class="grid md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-white/70 text-sm tracking-wide mb-2">
                                Nom complet <span class="text-amber-400">*</span>
                            </label>
                            <input type="text" name="nom" value="{{ old('nom', Auth::user()->name) }}"
                                class="w-full bg-black/40 border border-white/10 text-white rounded-xl px-4 py-3 text-sm
                                      focus:border-amber-400 focus:outline-none transition
                                      {{ $errors->has('nom') ? 'border-red-500' : '' }}" />
                            @error('nom')
                                <p class="text-red-400 text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-white/70 text-sm tracking-wide mb-2">
                                Email <span class="text-amber-400">*</span>
                            </label>
                            <input type="email" name="email" value="{{ old('email', Auth::user()->email) }}"
                                class="w-full bg-black/40 border border-white/10 text-white rounded-xl px-4 py-3 text-sm
                                      focus:border-amber-400 focus:outline-none transition
                                      {{ $errors->has('email') ? 'border-red-500' : '' }}" />
                            @error('email')
                                <p class="text-red-400 text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="mt-5">
                        <label class="block text-white/70 text-sm tracking-wide mb-2">Motif du séjour</label>
                        <input type="text" name="motif" value="{{ old('motif') }}"
                            placeholder="Ex : vacances, déplacement professionnel..."
                            class="w-full bg-black/40 border border-white/10 text-white placeholder-white/30
                                  rounded-xl px-4 py-3 text-sm focus:border-amber-400 focus:outline-none transition" />
                    </div>
                </div>

                {{-- ── SECTION 4 : Note paiement ───────────────────── --}}
                <div class="p-4 bg-amber-400/5 border border-amber-400/20 rounded-xl flex items-start gap-3">
                    <span class="text-amber-400 text-xl mt-0.5">💡</span>
                    <div>
                        <p class="text-amber-400 text-sm font-semibold">Paiement à l'hôtel</p>
                        <p class="text-white/60 text-xs mt-1 leading-relaxed">
                            Aucun paiement en ligne n'est requis. Votre réservation sera enregistrée
                            et le paiement s'effectuera directement à la réception de l'hôtel,
                            à votre arrivée ou après votre séjour.
                        </p>
                    </div>
                </div>

                {{-- BOUTONS --}}
                <div class="flex items-center justify-end gap-4 pt-4 border-t border-white/10">
                    <a href="{{ route('client.chambres.index') }}"
                        class="px-8 py-3 border border-white/10 text-white/60 rounded-full text-sm hover:bg-white/5 transition">
                        Annuler
                    </a>
                    <button type="submit"
                        class="px-10 py-3 bg-gradient-to-r from-amber-400 to-amber-300 text-black font-semibold
                               rounded-full text-sm shadow-lg shadow-amber-400/20
                               hover:scale-[1.02] transition-all duration-300">
                        Confirmer la réservation →
                    </button>
                </div>
            </form>
        </div>

        {{-- ── RÉCAPITULATIF CHAMBRE (1/3) ──────────────────────────── --}}
        <div class="lg:col-span-1">
            <div class="bg-white/5 border border-white/10 rounded-2xl overflow-hidden sticky top-6">

                {{-- Image --}}
                <div class="h-44 bg-neutral-900 overflow-hidden">
                    @if ($chambre->image)
                        <img src="{{ Storage::url($chambre->image) }}" alt="{{ $chambre->numero_chambre }}"
                            class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-4xl text-white/20">🛏️</div>
                    @endif
                </div>

                <div class="p-6 space-y-4">
                    <div>
                        <h3 class="text-white font-bold text-lg">{{ $chambre->numero_chambre }}</h3>
                        <span
                            class="inline-block mt-1 px-3 py-0.5 bg-white/5 border border-white/10
                                 text-white/60 text-xs rounded-full capitalize">
                            {{ $chambre->type_chambre }}
                        </span>
                    </div>

                    <div class="border-t border-white/10 pt-4">
                        <p class="text-white/50 text-xs uppercase tracking-widest mb-1">Prix par nuit</p>
                        <p class="text-amber-400 font-bold text-2xl">
                            {{ number_format($chambre->prix_nuit, 0, ',', ' ') }}
                            <span class="text-sm font-normal text-white/40">Ar</span>
                        </p>
                    </div>

                    @if ($chambre->equipements && count($chambre->equipements) > 0)
                        <div class="border-t border-white/10 pt-4">
                            <p class="text-white/50 text-xs uppercase tracking-widest mb-3">Équipements</p>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach ($chambre->equipements as $eq)
                                    <span
                                        class="px-2.5 py-1 bg-white/5 border border-white/10
                                             text-white/60 text-xs rounded-full">
                                        {{ $eq }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if ($chambre->description)
                        <div class="border-t border-white/10 pt-4">
                            <p class="text-white/50 text-xs uppercase tracking-widest mb-2">Description</p>
                            <p class="text-white/60 text-sm leading-relaxed">{{ $chambre->description }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>

{{-- Script directement dans la page, PAS dans @push --}}
    <script>
    const PRIX_NUIT = {{ $chambre->prix_nuit }};

    // ── Dates bloquées (confirmées uniquement) ────────────────────
    const DATES_BLOQUEES = @json($datesIndisponibles);

    function estDateBloquee(dateStr) {
        return DATES_BLOQUEES.includes(dateStr);
    }

    function formaterPrix(montant) {
        return montant.toLocaleString('fr-FR');
    }

    // ── Marquer visuellement les dates bloquées ───────────────────
    // Le <input type="date"> natif ne supporte pas le grisage individuel
    // On utilise l'attribut min/max + validation JS + message d'avertissement
    function verifierDateBloquee(dateStr, inputEl) {
        if (!dateStr) return false;
        if (estDateBloquee(dateStr)) {
            inputEl.classList.add('border-red-500', 'bg-red-500/5');
            inputEl.classList.remove('border-white/10');
            return true;
        } else {
            inputEl.classList.remove('border-red-500', 'bg-red-500/5');
            inputEl.classList.add('border-white/10');
            return false;
        }
    }

    // ── Vérifier une plage de dates ───────────────────────────────
    function plageContientDateBloquee(arriveeStr, departStr) {
        if (!arriveeStr || !departStr) return false;
        const debut = new Date(arriveeStr);
        const fin   = new Date(departStr);
        let current = new Date(debut);
        while (current < fin) {
            const str = current.toISOString().split('T')[0];
            if (estDateBloquee(str)) return str;
            current.setDate(current.getDate() + 1);
        }
        return false;
    }

    function toggleModeReservation(mode) {
        const blocNuit   = document.getElementById('bloc-nuit-unique');
        const blocSejour = document.getElementById('bloc-sejour');
        const blocTotal  = document.getElementById('bloc-total');
        const msgNuits   = document.getElementById('msg-nuits');
        const alerteDate = document.getElementById('alerte-date-bloquee');

        if (mode === 'nuit_unique') {
            blocNuit.classList.remove('hidden');
            blocSejour.classList.add('hidden');
        } else {
            blocNuit.classList.add('hidden');
            blocSejour.classList.remove('hidden');
        }

        blocTotal.classList.add('hidden');
        msgNuits.classList.add('hidden');
        if (alerteDate) alerteDate.classList.add('hidden');
        calculerTotal();
    }

    function updateMinDepart() {
        const arrivee     = document.getElementById('date-arrivee').value;
        const departInput = document.getElementById('date-depart');
        if (!arrivee) return;
        const nextDay = new Date(arrivee);
        nextDay.setDate(nextDay.getDate() + 1);
        departInput.min = nextDay.toISOString().split('T')[0];
        if (departInput.value && departInput.value <= arrivee) {
            departInput.value = nextDay.toISOString().split('T')[0];
        }
    }

    function calculerTotal() {
        const modeChecked = document.querySelector('input[name="mode_reservation"]:checked');
        if (!modeChecked) return;

        const mode       = modeChecked.value;
        const blocTotal  = document.getElementById('bloc-total');
        const montantEl  = document.getElementById('montant-total');
        const detailEl   = document.getElementById('detail-total');
        const msgNuits   = document.getElementById('msg-nuits');
        const texteNuits = document.getElementById('texte-nuits');
        const alerteDate = document.getElementById('alerte-date-bloquee');

        if (mode === 'nuit_unique') {
            const dateVal = document.getElementById('date-reservation').value;
            const inputEl = document.getElementById('date-reservation');

            if (!dateVal) { blocTotal.classList.add('hidden'); return; }

            // Vérifier si la date est bloquée
            if (verifierDateBloquee(dateVal, inputEl)) {
                blocTotal.classList.add('hidden');
                if (alerteDate) {
                    document.getElementById('alerte-date-texte').textContent =
                        'La date du ' + dateVal + ' est déjà réservée et confirmée. Veuillez choisir une autre date.';
                    alerteDate.classList.remove('hidden');
                }
                return;
            }
            if (alerteDate) alerteDate.classList.add('hidden');

            detailEl.textContent  = '1 nuit × ' + formaterPrix(PRIX_NUIT) + ' Ar';
            montantEl.textContent = formaterPrix(PRIX_NUIT);
            blocTotal.classList.remove('hidden');
            msgNuits.classList.add('hidden');

        } else {
            const arriveeVal = document.getElementById('date-arrivee').value;
            const departVal  = document.getElementById('date-depart').value;
            const arriveeEl  = document.getElementById('date-arrivee');
            const departEl   = document.getElementById('date-depart');

            if (!arriveeVal || !departVal) {
                blocTotal.classList.add('hidden');
                msgNuits.classList.add('hidden');
                return;
            }

            // Vérifier si la plage contient une date bloquée
            const dateBloquee = plageContientDateBloquee(arriveeVal, departVal);
            if (dateBloquee) {
                blocTotal.classList.add('hidden');
                msgNuits.classList.add('hidden');
                if (alerteDate) {
                    document.getElementById('alerte-date-texte').textContent =
                        'La date du ' + dateBloquee + ' est déjà confirmée dans votre plage. Veuillez choisir d\'autres dates.';
                    alerteDate.classList.remove('hidden');
                }
                arriveeEl.classList.add('border-red-500');
                departEl.classList.add('border-red-500');
                return;
            }

            arriveeEl.classList.remove('border-red-500');
            departEl.classList.remove('border-red-500');
            if (alerteDate) alerteDate.classList.add('hidden');

            const nbNuits = Math.round((new Date(departVal) - new Date(arriveeVal)) / 86400000);
            if (nbNuits <= 0) {
                blocTotal.classList.add('hidden');
                msgNuits.classList.add('hidden');
                return;
            }

            texteNuits.textContent = '✅ ' + nbNuits + ' nuit' + (nbNuits > 1 ? 's' : '') + ' sélectionnée' + (nbNuits > 1 ? 's' : '');
            msgNuits.classList.remove('hidden');
            detailEl.textContent  = nbNuits + ' nuit' + (nbNuits > 1 ? 's' : '') + ' × ' + formaterPrix(PRIX_NUIT) + ' Ar';
            montantEl.textContent = formaterPrix(nbNuits * PRIX_NUIT);
            blocTotal.classList.remove('hidden');
        }
    }

    window.addEventListener('load', function () {
        document.querySelectorAll('input[name="mode_reservation"]').forEach(function(radio) {
            radio.addEventListener('change', function() {
                toggleModeReservation(this.value);
            });
        });

        document.getElementById('date-reservation').addEventListener('change', calculerTotal);
        document.getElementById('date-arrivee').addEventListener('change', function() {
            updateMinDepart();
            calculerTotal();
        });
        document.getElementById('date-depart').addEventListener('change', calculerTotal);

        const modeActif = document.querySelector('input[name="mode_reservation"]:checked');
        if (modeActif) toggleModeReservation(modeActif.value);
    });
</script>

@endsection
