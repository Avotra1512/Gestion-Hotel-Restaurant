@extends('layouts.app')

@section('content')
<x-navbar />

<!-- PRÉSENTATION DU RESTAURANT -->
<section class="py-24 bg-neutral-950">
    <div class="max-w-7xl mx-auto px-6 grid md:grid-cols-2 gap-16 items-center">
        <!-- Texte -->
        <div class="space-y-6 animate-fadeInLeft">
            <h2 class="text-5xl md:text-6xl font-extrabold text-amber-200 tracking-wide">
                Découvrez notre Restaurant
            </h2>
            <p class="text-neutral-400 leading-relaxed text-lg">
                Le restaurant MISALO vous invite à découvrir une cuisine raffinée,
                préparée avec passion par nos chefs expérimentés.
            </p>
            <p class="text-neutral-400 leading-relaxed text-lg">
                Profitez d’un cadre élégant et moderne, où chaque repas est une expérience unique,
                alliant saveurs, ambiance chaleureuse et service irréprochable.
            </p>
            <p class="text-neutral-400 leading-relaxed text-lg">
                Que ce soit pour un dîner romantique, un repas en famille ou un événement professionnel,
                notre restaurant est l’endroit idéal pour savourer des moments mémorables.
            </p>
        </div>

        <!-- Image -->
        <div class="overflow-hidden rounded-3xl shadow-2xl animate-fadeInRight">
            <img src="/images/restaurant-interior.jpg"
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
