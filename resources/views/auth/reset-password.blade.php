@extends('layouts.app')
@section('title', 'Nouveau mot de passe')
@section('content')

@include('components.navbar')

<div class="min-h-screen flex items-center bg-black relative overflow-hidden pt-10 px-10">

    <div class="absolute -top-40 -left-40 w-[600px] h-[600px] bg-amber-400/20 rounded-full blur-[150px] animate-pulse"></div>
    <div class="absolute bottom-0 right-0 w-[500px] h-[500px] bg-amber-300/10 rounded-full blur-[150px] animate-pulse"></div>

    <div class="relative z-10 w-full max-w-7xl mx-auto grid md:grid-cols-2 gap-16 items-center">

        {{-- LEFT --}}
        <div class="text-white space-y-8 opacity-0 translate-x-[-50px] animate-[slideInLeft_1s_ease-out_forwards]">
            <h2 class="text-5xl font-extrabold leading-tight">
                Créez votre <br>
                <span class="bg-gradient-to-r from-amber-400 to-amber-200 bg-clip-text text-transparent">
                    nouveau mot de passe
                </span>
            </h2>
            <p class="text-white/60 text-lg leading-relaxed max-w-xl">
                Choisissez un mot de passe sécurisé d'au moins 8 caractères
                pour protéger votre compte MISALO.
            </p>
            <div class="space-y-3">
                <div class="flex items-center gap-3 text-white/50 text-sm">
                    <span class="text-green-400">✓</span>
                    Au moins 8 caractères
                </div>
                <div class="flex items-center gap-3 text-white/50 text-sm">
                    <span class="text-green-400">✓</span>
                    Différent de votre ancien mot de passe
                </div>
                <div class="flex items-center gap-3 text-white/50 text-sm">
                    <span class="text-green-400">✓</span>
                    Les deux champs doivent correspondre
                </div>
            </div>
        </div>

        {{-- Ligne verticale --}}
        <div class="hidden md:block absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2
                    h-[400px] w-px bg-gradient-to-b from-transparent via-amber-400/50 to-transparent"></div>

        {{-- RIGHT --}}
        <div class="w-full max-w-md ml-auto bg-white/5 backdrop-blur-xl border border-white/10
                    rounded-2xl shadow-2xl px-10 py-12
                    opacity-0 translate-x-[50px] animate-[slideInRight_1s_ease-out_forwards]">

            <div class="text-center mb-8">
                <h1 class="text-4xl font-extrabold tracking-[0.3em]
                           bg-gradient-to-r from-amber-400 to-amber-200 bg-clip-text text-transparent">
                    MISALO
                </h1>
                <p class="mt-3 text-sm text-white/60 tracking-wide">Nouveau mot de passe</p>
            </div>

            <form method="POST" action="{{ route('password.update') }}" class="space-y-6">
                @csrf

                <input type="hidden" name="token" value="{{ $token }}">

                {{-- Email --}}
                <div>
                    <label class="block text-white/70 tracking-wide mb-2">Adresse email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                           placeholder="votre@email.com"
                           class="w-full h-12 bg-black/40 border text-white placeholder-white/30
                                  focus:border-amber-400 focus:outline-none rounded-xl px-4 transition
                                  {{ $errors->has('email') ? 'border-red-500' : 'border-white/10' }}" />
                    @error('email')
                    <p class="text-red-400 text-xs mt-2">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nouveau mot de passe --}}
                <div>
                    <label class="block text-white/70 tracking-wide mb-2">Nouveau mot de passe</label>
                    <div class="relative">
                        <input type="password" name="password" id="password" required
                               placeholder="Min. 8 caractères"
                               class="w-full h-12 bg-black/40 border text-white placeholder-white/30
                                      focus:border-amber-400 focus:outline-none rounded-xl px-4 pr-12 transition
                                      {{ $errors->has('password') ? 'border-red-500' : 'border-white/10' }}" />
                        <button type="button" onclick="togglePassword('password', 'eye1')"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-white/30 hover:text-amber-400 transition">
                            <span id="eye1">👁</span>
                        </button>
                    </div>
                    @error('password')
                    <p class="text-red-400 text-xs mt-2">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Confirmation --}}
                <div>
                    <label class="block text-white/70 tracking-wide mb-2">Confirmer le mot de passe</label>
                    <div class="relative">
                        <input type="password" name="password_confirmation" id="password_confirmation" required
                               placeholder="Répéter le mot de passe"
                               class="w-full h-12 bg-black/40 border border-white/10 text-white placeholder-white/30
                                      focus:border-amber-400 focus:outline-none rounded-xl px-4 pr-12 transition" />
                        <button type="button" onclick="togglePassword('password_confirmation', 'eye2')"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-white/30 hover:text-amber-400 transition">
                            <span id="eye2">👁</span>
                        </button>
                    </div>
                </div>

                {{-- Indicateur de force --}}
                <div id="strength-bar" class="hidden">
                    <div class="flex gap-1 mb-1">
                        <div id="s1" class="h-1 flex-1 rounded-full bg-white/10 transition-colors"></div>
                        <div id="s2" class="h-1 flex-1 rounded-full bg-white/10 transition-colors"></div>
                        <div id="s3" class="h-1 flex-1 rounded-full bg-white/10 transition-colors"></div>
                        <div id="s4" class="h-1 flex-1 rounded-full bg-white/10 transition-colors"></div>
                    </div>
                    <p id="strength-label" class="text-xs text-white/40"></p>
                </div>

                <button type="submit"
                        class="w-full py-3 rounded-full bg-gradient-to-r from-amber-400 to-amber-300
                               text-black font-semibold tracking-wide
                               shadow-lg shadow-amber-400/30 hover:scale-[1.03] transition-all duration-300">
                    Réinitialiser mon mot de passe
                </button>
            </form>

            <p class="mt-8 text-center text-xs text-white/30">© {{ date('Y') }} MISALO — Luxe & excellence</p>
        </div>
    </div>
</div>

@push('styles')
<style>
    @keyframes slideInLeft  { from { opacity:0; transform:translateX(-50px); } to { opacity:1; transform:translateX(0); } }
    @keyframes slideInRight { from { opacity:0; transform:translateX(50px);  } to { opacity:1; transform:translateX(0); } }
</style>
@endpush

@push('scripts')
<script>
// Toggle visibilité mot de passe
function togglePassword(fieldId, eyeId) {
    const field = document.getElementById(fieldId);
    field.type = field.type === 'password' ? 'text' : 'password';
}

// Indicateur de force du mot de passe
document.getElementById('password').addEventListener('input', function() {
    const val = this.value;
    const bar = document.getElementById('strength-bar');

    if (val.length === 0) { bar.classList.add('hidden'); return; }
    bar.classList.remove('hidden');

    let score = 0;
    if (val.length >= 8)                    score++;
    if (/[A-Z]/.test(val))                 score++;
    if (/[0-9]/.test(val))                 score++;
    if (/[^A-Za-z0-9]/.test(val))          score++;

    const colors  = ['bg-red-500', 'bg-orange-500', 'bg-yellow-500', 'bg-green-500'];
    const labels  = ['Très faible', 'Faible', 'Moyen', 'Fort'];
    const bars    = ['s1','s2','s3','s4'];

    bars.forEach((id, i) => {
        const el = document.getElementById(id);
        el.className = 'h-1 flex-1 rounded-full transition-colors ' +
            (i < score ? colors[score - 1] : 'bg-white/10');
    });

    document.getElementById('strength-label').textContent = labels[score - 1] ?? '';
});
</script>
@endpush

@endsection