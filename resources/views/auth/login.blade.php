@extends('layouts.app')

@section('title', 'Connexion')

@section('content')

    {{-- NAVBAR --}}
    @include('components.navbar')

    {{-- BACKGROUND --}}
    <div class="min-h-screen flex items-center bg-black relative overflow-hidden pt-10 px-10">

        {{-- OVERLAY DARK --}}
        <div class="absolute inset-0 bg-gradient-to-br from-black via-black/90 to-black"></div>

        {{-- LUMIÈRE ANIMÉE --}}
        <div class="absolute -top-40 -left-40 w-[600px] h-[600px] bg-amber-400/20 rounded-full blur-[150px] animate-pulse">
        </div>
        <div class="absolute bottom-0 right-0 w-[500px] h-[500px] bg-amber-300/10 rounded-full blur-[150px] animate-pulse">
        </div>

        {{-- CONTAINER --}}
        <div class="relative z-10 w-full max-w-7xl mx-auto grid md:grid-cols-2 gap-16 items-center">

            {{-- LEFT SIDE --}}
            <div class="text-white space-y-8 opacity-0 translate-x-[-50px] animate-[slideInLeft_1s_ease-out_forwards]">
                <h2 class="text-5xl font-extrabold leading-tight">
                    Bienvenue chez <span
                        class="bg-gradient-to-r from-amber-400 to-amber-200 bg-clip-text text-transparent">MISALO</span>
                </h2>
                <p class="text-white/70 text-lg leading-relaxed max-w-xl">
                    Découvrez une expérience unique où le luxe rencontre l'élégance. Connectez-vous pour gérer vos
                    réservations, profiter de nos services exclusifs et vivre l’excellence hôtelière.
                </p>
                <div class="flex gap-10 pt-6">
                    <div>
                        <h3 class="text-3xl font-bold text-amber-400">5★</h3>
                        <p class="text-white/50 text-sm">Service Premium</p>
                    </div>
                    <div>
                        <h3 class="text-3xl font-bold text-amber-400">24/7</h3>
                        <p class="text-white/50 text-sm">Assistance</p>
                    </div>
                    <div>
                        <h3 class="text-3xl font-bold text-amber-400">100%</h3>
                        <p class="text-white/50 text-sm">Satisfaction</p>
                    </div>
                </div>
            </div>

            {{-- LIGNE VERTICALE --}}
            <div
                class="hidden md:block absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 h-[400px] w-px bg-gradient-to-b from-transparent via-amber-400/50 to-transparent">
            </div>

            {{-- RIGHT SIDE LOGIN --}}
            <div
                class="w-full max-w-md ml-auto bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl shadow-2xl px-10 py-12 opacity-0 translate-x-[50px] animate-[slideInRight_1s_ease-out_forwards]">

                <div class="text-center mb-10">
                    <h1
                        class="text-4xl font-extrabold tracking-[0.3em] bg-gradient-to-r from-amber-400 to-amber-200 bg-clip-text text-transparent drop-shadow">
                        MISALO</h1>
                    <p class="mt-3 text-sm text-white/60 tracking-wide">Hôtel & Restaurant de prestige</p>
                </div>
                @if (session('success'))
                    <div
                        class="mb-6 p-4 bg-amber-400/10 border border-amber-400/20 text-amber-400 rounded-xl text-sm text-center">
                        {{ session('success') }}
                    </div>
                @endif

                {{-- FORM --}}
                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    {{-- EMAIL --}}
                    <div>
                        <label for="email" class="block text-white/70 tracking-wide mb-2">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                            class="w-full h-10 bg-black/40 border border-white/10 text-white placeholder-white/40 focus:border-amber-400 focus:ring-amber-400 rounded-xl" />
                        @error('email')
                            <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- PASSWORD --}}
                    <div>
                        <label for="password" class="block text-white/70 tracking-wide mb-2">Mot de passe</label>
                        <input id="password" type="password" name="password" required
                            class="w-full h-10 bg-black/40 border border-white/10 text-white placeholder-white/40 focus:border-amber-400 focus:ring-amber-400 rounded-xl" />
                        @error('password')
                            <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- REMEMBER & FORGOT --}}
                    <div class="flex items-center justify-between text-sm">
                        <label for="remember_me" class="flex items-center text-white/60">
                            <input id="remember_me" type="checkbox" name="remember"
                                class="rounded border-white/20 bg-black/40 text-amber-400 focus:ring-amber-400">
                            <span class="ml-2">Se souvenir de moi</span>
                        </label>
                        <a href="{{ route('password.request') }}" class="text-amber-400 hover:text-amber-300 transition">Mot de passe oublié ?</a>
                    </div>

                    <button type="submit"
                        class="w-full mt-6 py-3 rounded-full bg-gradient-to-r from-amber-400 to-amber-300 text-black font-semibold tracking-wide shadow-lg shadow-amber-400/30 hover:scale-[1.03] transition-all duration-300">
                        Se connecter
                    </button>
                </form>

                <p class="mt-8 text-center text-xs text-white/40">© {{ date('Y') }} MISALO — Luxe & excellence</p>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            @keyframes slideInLeft {
                from {
                    opacity: 0;
                    transform: translateX(-50px);
                }

                to {
                    opacity: 1;
                    transform: translateX(0);
                }
            }

            @keyframes slideInRight {
                from {
                    opacity: 0;
                    transform: translateX(50px);
                }

                to {
                    opacity: 1;
                    transform: translateX(0);
                }
            }
        </style>
    @endpush

@endsection
