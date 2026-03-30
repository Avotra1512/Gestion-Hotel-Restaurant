@extends('layouts.app')

@section('title', 'Inscription')

@section('content')

{{-- NAVBAR --}}
@include('components.navbar')

<div class="min-h-screen flex items-center bg-black relative overflow-hidden pt-20 px-10">

    {{-- OVERLAY DARK --}}
    <div class="absolute inset-0 bg-gradient-to-br from-black via-black/90 to-black"></div>

    {{-- LUMIÈRE ANIMÉE --}}
    <div class="absolute -top-40 -left-40 w-[600px] h-[600px] bg-amber-400/20 rounded-full blur-[150px] animate-pulse"></div>
    <div class="absolute bottom-0 right-0 w-[500px] h-[500px] bg-amber-300/10 rounded-full blur-[150px] animate-pulse"></div>

    {{-- CONTAINER --}}
    <div class="relative z-10 w-full max-w-7xl mx-auto grid md:grid-cols-2 gap-16 items-center">

        {{-- LEFT SIDE --}}
        <div class="text-white space-y-8 opacity-0 translate-x-[-50px] animate-[slideInLeft_1s_ease-out_forwards]">
            <h2 class="text-5xl font-extrabold leading-tight">
                Rejoignez Hôtel & Restaurant <span class="bg-gradient-to-r from-amber-400 to-amber-200 bg-clip-text text-transparent">MISALO</span>
            </h2>
            <p class="text-white/70 text-lg leading-relaxed max-w-xl">
                Créez votre compte et profitez d’un accès exclusif à nos services premium, réservations prioritaires et expériences uniques.
            </p>
            <div class="flex gap-10 pt-6">
                <div><h3 class="text-3xl font-bold text-amber-400">VIP</h3><p class="text-white/50 text-sm">Accès exclusif</p></div>
                <div><h3 class="text-3xl font-bold text-amber-400">Fast</h3><p class="text-white/50 text-sm">Réservation rapide</p></div>
                <div><h3 class="text-3xl font-bold text-amber-400">Secure</h3><p class="text-white/50 text-sm">100% sécurisé</p></div>
            </div>
        </div>

        {{-- LIGNE VERTICALE --}}
        <div class="hidden md:block absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 h-[400px] w-px bg-gradient-to-b from-transparent via-amber-400/50 to-transparent"></div>

        {{-- RIGHT SIDE REGISTER --}}
        <div class="w-full max-w-md ml-auto bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl shadow-2xl px-10 py-12 opacity-0 translate-x-[50px] animate-[slideInRight_1s_ease-out_forwards]">

            <div class="text-center mb-10">
                <h1 class="text-4xl font-extrabold tracking-[0.3em] bg-gradient-to-r from-amber-400 to-amber-200 bg-clip-text text-transparent drop-shadow">MISALO</h1>
                <p class="mt-3 text-sm text-white/60 tracking-wide">Création de compte</p>
            </div>

            <form method="POST" action="{{ route('register') }}" class="space-y-6">
                @csrf

                {{-- NAME --}}
                <div>
                    <label for="name" class="block text-white/70 tracking-wide mb-2">Nom complet</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                           class="w-full h-10 bg-black/40 border border-white/10 text-white placeholder-white/40 focus:border-amber-400 focus:ring-amber-400 rounded-xl" />
                    @error('name') <p class="text-red-500 text-xs mt-2">{{ $message }}</p> @enderror
                </div>

                {{-- EMAIL --}}
                <div>
                    <label for="email" class="block text-white/70 tracking-wide mb-2">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required
                           class="w-full h-10 bg-black/40 border border-white/10 text-white placeholder-white/40 focus:border-amber-400 focus:ring-amber-400 rounded-xl" />
                    @error('email') <p class="text-red-500 text-xs mt-2">{{ $message }}</p> @enderror
                </div>

                {{-- PASSWORD --}}
                <div>
                    <label for="password" class="block text-white/70 tracking-wide mb-2">Mot de passe</label>
                    <input id="password" type="password" name="password" required
                           class="w-full h-10 bg-black/40 border border-white/10 text-white placeholder-white/40 focus:border-amber-400 focus:ring-amber-400 rounded-xl" />
                    @error('password') <p class="text-red-500 text-xs mt-2">{{ $message }}</p> @enderror
                </div>

                {{-- CONFIRM PASSWORD --}}
                <div>
                    <label for="password_confirmation" class="block text-white/70 tracking-wide mb-2">Confirmer le mot de passe</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required
                           class="w-full h-10 bg-black/40 border border-white/10 text-white placeholder-white/40 focus:border-amber-400 focus:ring-amber-400 rounded-xl" />
                </div>

                <button type="submit" class="w-full mt-6 py-3 rounded-full bg-gradient-to-r from-amber-400 to-amber-300 text-black font-semibold tracking-wide shadow-lg shadow-amber-400/30 hover:scale-[1.03] transition-all duration-300">
                    Créer un compte
                </button>

                <div class="text-center text-sm text-white/60 mt-6">
                    Déjà inscrit ? <a href="{{ route('login') }}" class="text-amber-400 hover:text-amber-300 transition">Se connecter</a>
                </div>
            </form>

            <p class="mt-8 text-center text-xs text-white/40">© {{ date('Y') }} MISALO — Luxe & excellence</p>
        </div>
    </div>
</div>

@push('styles')
<style>
    @keyframes slideInLeft { from { opacity: 0; transform: translateX(-50px); } to { opacity: 1; transform: translateX(0); } }
    @keyframes slideInRight { from { opacity: 0; transform: translateX(50px); } to { opacity: 1; transform: translateX(0); } }
</style>
@endpush

@endsection
