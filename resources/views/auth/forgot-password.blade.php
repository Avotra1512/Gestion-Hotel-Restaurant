@extends('layouts.app')
@section('title', 'Mot de passe oublié')
@section('content')

@include('components.navbar')

<div class="min-h-screen flex items-center bg-black relative overflow-hidden pt-10 px-10">

    {{-- Lumières animées --}}
    <div class="absolute -top-40 -left-40 w-[600px] h-[600px] bg-amber-400/20 rounded-full blur-[150px] animate-pulse"></div>
    <div class="absolute bottom-0 right-0 w-[500px] h-[500px] bg-amber-300/10 rounded-full blur-[150px] animate-pulse"></div>

    <div class="relative z-10 w-full max-w-7xl mx-auto grid md:grid-cols-2 gap-16 items-center">

        {{-- LEFT --}}
        <div class="text-white space-y-8 opacity-0 translate-x-[-50px] animate-[slideInLeft_1s_ease-out_forwards]">
            <h2 class="text-5xl font-extrabold leading-tight">
                Mot de passe <br>
                <span class="bg-gradient-to-r from-amber-400 to-amber-200 bg-clip-text text-transparent">
                    oublié ?
                </span>
            </h2>
            <p class="text-white/60 text-lg leading-relaxed max-w-xl">
                Pas de panique. Entrez votre adresse email et nous vous enverrons
                un lien pour réinitialiser votre mot de passe.
            </p>
            <div class="flex gap-8 pt-4">
                <div>
                    <h3 class="text-2xl font-bold text-amber-400">🔒</h3>
                    <p class="text-white/50 text-sm mt-1">Sécurisé</p>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-amber-400">📧</h3>
                    <p class="text-white/50 text-sm mt-1">Par email</p>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-amber-400">⚡</h3>
                    <p class="text-white/50 text-sm mt-1">Instantané</p>
                </div>
            </div>

            <a href="{{ route('login') }}"
               class="inline-flex items-center gap-2 text-amber-400/70 hover:text-amber-400 transition text-sm">
                ← Retour à la connexion
            </a>
        </div>

        {{-- Ligne verticale --}}
        <div class="hidden md:block absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2
                    h-[350px] w-px bg-gradient-to-b from-transparent via-amber-400/50 to-transparent"></div>

        {{-- RIGHT --}}
        <div class="w-full max-w-md ml-auto bg-white/5 backdrop-blur-xl border border-white/10
                    rounded-2xl shadow-2xl px-10 py-12
                    opacity-0 translate-x-[50px] animate-[slideInRight_1s_ease-out_forwards]">

            <div class="text-center mb-8">
                <h1 class="text-4xl font-extrabold tracking-[0.3em]
                           bg-gradient-to-r from-amber-400 to-amber-200 bg-clip-text text-transparent">
                    MISALO
                </h1>
                <p class="mt-3 text-sm text-white/60 tracking-wide">Réinitialisation du mot de passe</p>
            </div>

            {{-- Succès --}}
            @if(session('success'))
            <div class="mb-6 p-4 bg-green-400/10 border border-green-400/20 text-green-400 rounded-xl text-sm text-center">
                {{ session('success') }}
            </div>
            @endif

            {{-- Formulaire --}}
            <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
                @csrf

                <div>
                    <label class="block text-white/70 tracking-wide mb-2">
                        Adresse email
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                           placeholder="votre@email.com"
                           class="w-full h-12 bg-black/40 border text-white placeholder-white/30
                                  focus:border-amber-400 focus:ring-amber-400 focus:outline-none
                                  rounded-xl px-4 transition
                                  {{ $errors->has('email') ? 'border-red-500' : 'border-white/10' }}" />
                    @error('email')
                    <p class="text-red-400 text-xs mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                        class="w-full py-3 rounded-full bg-gradient-to-r from-amber-400 to-amber-300
                               text-black font-semibold tracking-wide
                               shadow-lg shadow-amber-400/30 hover:scale-[1.03] transition-all duration-300">
                    Envoyer le lien de réinitialisation
                </button>
            </form>

            <div class="mt-6 text-center text-sm text-white/40">
                <a href="{{ route('login') }}" class="text-amber-400 hover:text-amber-300 transition">
                    ← Retour à la connexion
                </a>
            </div>

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

@endsection