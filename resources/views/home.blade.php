<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Hôtel & Restaurant MISALO</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-neutral-950 text-neutral-100 font-sans antialiased">

    <!-- NAVBAR -->
    <header class="fixed top-0 left-0 w-full z-50 backdrop-blur bg-black/40 border-b border-white/10">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
            <!-- Logo -->
            <h1
                class="relative text-4xl font-extrabold tracking-widest
           bg-gradient-to-r from-yellow-400 via-amber-300 to-amber-500
           bg-clip-text text-transparent
           drop-shadow-[0_4px_3px_rgba(0,0,0,0.3)]
           before:content-[''] before:absolute before:inset-0 before:bg-gradient-to-r before:from-black before:via-white/50 before:to-black before:blur-[2px] before:opacity-30 before:animate-glint">
                MISALO
            </h1>

            <nav class="hidden md:flex gap-8 text-sm uppercase tracking-wider">
                <a href="{{ route('home') }}" class="text-white hover:text-amber-400 transition duration-300">Accueil</a>
                <a href="{{ route('hotel') }}" class="text-white hover:text-amber-400 transition duration-300">Hôtel</a>
                <a href="{{ route('restaurant') }}"
                    class="text-white hover:text-amber-400 transition duration-300">Restaurant</a>
                <a href="{{ route('contact') }}"
                    class="text-white hover:text-amber-400 transition duration-300">Contact</a>
            </nav>

            <!-- Boutons Connexion / Inscription -->
            <div class="flex gap-3">
                <a href="{{ route('login') }}"
                    class="px-5 py-2 text-sm border border-amber-400 text-amber-400
              rounded-full hover:bg-amber-400 hover:text-black
              transition duration-300">
                    Connexion
                </a>

                <a href="{{ route('register') }}"
                    class="px-5 py-2 text-sm bg-amber-400 text-black
              rounded-full hover:bg-amber-300
              transition duration-300 shadow-md">
                    Inscription
                </a>
            </div>
        </div>
    </header>

    <!-- HERO -->
    <section class="h-screen relative overflow-hidden">
        <img src="/images/hotel-hero.jpg"
            class="absolute inset-0 w-full h-full object-cover scale-110 animate-[slowzoom_20s_linear_infinite]">

        <div class="absolute inset-0 bg-gradient-to-b from-black/70 via-black/50 to-black"></div>

        <div class="relative z-10 h-full flex items-center">
            <div class="max-w-5xl px-8">
                <h2
                    class="text-6xl md:text-7xl font-extralight leading-tight mb-8
                       animate-fadeInUp">
                    L’élégance du confort <br>
                    <span class="text-amber-300 font-semibold">
                        au cœur de votre séjour
                    </span>
                </h2>

                <p class="text-lg md:text-xl text-neutral-300 max-w-2xl mb-12 animate-fadeInUp delay-200">
                    Hôtel & Restaurant MISALO vous offre une expérience unique mêlant
                    luxe, sérénité et gastronomie raffinée.
                </p>

                <a href="{{ route('login') }}"
                    class="inline-flex items-center gap-4 px-12 py-5 uppercase tracking-[0.25em] text-sm
                      bg-amber-400 text-black rounded-full
                      hover:gap-6 hover:bg-amber-300 transition-all duration-300
                      shadow-xl shadow-amber-400/30">
                    Réserver
                    <span class="text-xl">→</span>
                </a>
            </div>
        </div>
    </section>

    <!-- À PROPOS -->
    <section class="py-32 bg-neutral-900 relative">
        <div class="max-w-7xl mx-auto px-6 grid md:grid-cols-2 gap-20 items-center">
            <div>
                <span class="text-amber-400 tracking-[0.3em] text-xs uppercase">
                    À propos
                </span>

                <h3 class="text-4xl mt-6 mb-8 font-light">
                    Une signature <br> de luxe discret
                </h3>

                <p class="text-neutral-400 leading-relaxed mb-6">
                    MISALO incarne une vision moderne de l’hôtellerie :
                    un lieu où chaque détail inspire calme, élégance et confort.
                </p>

                <p class="text-neutral-400 leading-relaxed">
                    Pensé pour les voyageurs exigeants,
                    notre hôtel allie architecture contemporaine et service personnalisé.
                </p>
            </div>

            <div class="relative group">
                <div
                    class="absolute -inset-2 bg-gradient-to-r from-amber-400 to-amber-300
                        rounded-2xl blur opacity-30 group-hover:opacity-60 transition">
                </div>

                <img src="/images/bienvenue.jpg"
                    class="relative rounded-2xl shadow-2xl
                        group-hover:scale-105 transition duration-700">
            </div>
        </div>
    </section>

    <!-- SERVICES -->
    <section class="py-32 bg-neutral-950">
        <div class="max-w-7xl mx-auto px-6">
            <h3 class="text-4xl text-center mb-20 font-light">
                Expériences MISALO
            </h3>

            <div class="grid md:grid-cols-3 gap-12">
                <div
                    class="p-10 rounded-2xl bg-gradient-to-b from-neutral-900 to-neutral-950
                        border border-white/10 hover:border-amber-400/40
                        hover:-translate-y-3 transition-all duration-300">
                    <h4 class="text-xl mb-4 text-amber-400">Chambres Premium</h4>
                    <p class="text-neutral-400">
                        Espaces lumineux, literie haut de gamme et atmosphère apaisante.
                    </p>
                </div>

                <div
                    class="p-10 rounded-2xl bg-gradient-to-b from-neutral-900 to-neutral-950
                        border border-white/10 hover:border-amber-400/40
                        hover:-translate-y-3 transition-all duration-300">
                    <h4 class="text-xl mb-4 text-amber-400">Gastronomie</h4>
                    <p class="text-neutral-400">
                        Une cuisine élégante inspirée des saveurs locales et internationales.
                    </p>
                </div>

                <div
                    class="p-10 rounded-2xl bg-gradient-to-b from-neutral-900 to-neutral-950
                        border border-white/10 hover:border-amber-400/40
                        hover:-translate-y-3 transition-all duration-300">
                    <h4 class="text-xl mb-4 text-amber-400">Service 24/7</h4>
                    <p class="text-neutral-400">
                        Une équipe attentive, disponible à chaque instant.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="bg-neutral-950 py-12 text-center text-neutral-500 text-sm tracking-widest">
        © {{ date('Y') }} MISALO — Élégance & Sérénité
    </footer>

</body>

</html>
