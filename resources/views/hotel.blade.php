@extends('layouts.app')

@section('content')
<x-navbar />

<!-- PRÉSENTATION DE L'HÔTEL -->
<section class="py-24 bg-neutral-900">
    <div class="max-w-7xl mx-auto px-6 grid md:grid-cols-2 gap-16 items-center">
        <!-- Texte -->
        <div class="space-y-6 animate-fadeInLeft">
            <h2 class="text-5xl md:text-6xl font-extrabold text-amber-200 tracking-wide">
                Découvrez notre Hôtel
            </h2>
            <p class="text-neutral-400 leading-relaxed text-lg">
                MISALO est un hôtel moderne conçu pour offrir un confort absolu
                et une hospitalité exceptionnelle.
            </p>
            <p class="text-neutral-400 leading-relaxed text-lg">
                Profitez d’un cadre paisible, de chambres élégantes et d’un service de restauration haut de gamme.
            </p>

            <a href="{{ route('login') }}"
               class="inline-block bg-amber-300 text-black px-8 py-3 rounded-full font-semibold
                      uppercase tracking-wide shadow-lg hover:shadow-amber-400/50
                      hover:bg-amber-200 transition-all duration-300">
                Réserver maintenant
            </a>
        </div>

        <!-- Image -->
        <div class="overflow-hidden rounded-3xl shadow-2xl animate-fadeInRight">
            <img src="/images/hotel-room.jpg"
                 class="w-full h-full object-cover transform hover:scale-105 hover:rotate-1 transition-transform duration-700">
        </div>
    </div>
</section>

<!-- FOOTER -->
<footer class="bg-neutral-950 py-12 text-center text-neutral-500 text-sm tracking-widest">
    © {{ date('Y') }} MISALO — Élégance & Sérénité
</footer>

<!-- Animations CSS -->
<style>
@keyframes fadeInLeft {
    from {opacity:0; transform: translateX(-30px);}
    to {opacity:1; transform: translateX(0);}
}
@keyframes fadeInRight {
    from {opacity:0; transform: translateX(30px);}
    to {opacity:1; transform: translateX(0);}
}
.animate-fadeInLeft { animation: fadeInLeft 1s ease-out forwards; }
.animate-fadeInRight { animation: fadeInRight 1s ease-out forwards; }
</style>

@endsection
