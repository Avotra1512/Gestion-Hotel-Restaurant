@extends('layouts.dashboard')
@section('title', 'Modifier — ' . $user->name)
@include('components.nav-admin')
@section('content')

<div class="flex items-center gap-4 mb-8">
    <a href="{{ route('admin.users.index') }}"
       class="w-10 h-10 flex items-center justify-center rounded-full border border-white/10
              text-white/60 hover:border-amber-400/50 hover:text-amber-400 transition">←</a>
    <div>
        <h2 class="text-2xl font-bold text-white">
            Modifier — <span class="text-amber-400">{{ $user->name }}</span>
        </h2>
        <p class="text-white/50 text-sm mt-1">Mettez à jour les informations du compte</p>
    </div>
</div>

<div class="max-w-2xl">
    {{-- FORMULAIRE UPDATE --}}
    <form method="POST" action="{{ route('admin.users.update', $user) }}"
          class="bg-white/5 border border-white/10 rounded-2xl p-8 space-y-6">
        @csrf @method('PUT')

        {{-- Nom --}}
        <div>
            <label class="block text-white/70 text-sm tracking-wide mb-2">
                Nom complet <span class="text-amber-400">*</span>
            </label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}"
                   class="w-full bg-black/40 border text-white rounded-xl px-4 py-3 text-sm
                          focus:border-amber-400 focus:outline-none transition
                          {{ $errors->has('name') ? 'border-red-500' : 'border-white/10' }}" />
            @error('name') <p class="text-red-400 text-xs mt-2">{{ $message }}</p> @enderror
        </div>

        {{-- Email --}}
        <div>
            <label class="block text-white/70 text-sm tracking-wide mb-2">
                Email <span class="text-amber-400">*</span>
            </label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}"
                   class="w-full bg-black/40 border text-white rounded-xl px-4 py-3 text-sm
                          focus:border-amber-400 focus:outline-none transition
                          {{ $errors->has('email') ? 'border-red-500' : 'border-white/10' }}" />
            @error('email') <p class="text-red-400 text-xs mt-2">{{ $message }}</p> @enderror
        </div>

        {{-- Rôle --}}
        <div>
            <label class="block text-white/70 text-sm tracking-wide mb-3">Rôle <span class="text-amber-400">*</span></label>
            @if($user->id === auth()->id())
            <div class="p-4 bg-amber-400/5 border border-amber-400/20 rounded-xl">
                <p class="text-amber-400/80 text-xs">⚠️ Vous ne pouvez pas modifier votre propre rôle.</p>
                <input type="hidden" name="role" value="{{ $user->role }}">
                <span class="px-3 py-1 rounded-full text-xs font-semibold border {{ $user->couleurRole() }} mt-2 inline-block">
                    {{ $user->libelleRole() }}
                </span>
            </div>
            @else
            <div class="grid grid-cols-3 gap-4">
                @foreach([
                    ['value'=>'client',  'emoji'=>'👤', 'label'=>'Client',        'color'=>'peer-checked:border-blue-400 peer-checked:bg-blue-400/5'],
                    ['value'=>'gerant',  'emoji'=>'🏨', 'label'=>'Gérant',        'color'=>'peer-checked:border-purple-400 peer-checked:bg-purple-400/5'],
                    ['value'=>'admin',   'emoji'=>'🛡️', 'label'=>'Administrateur','color'=>'peer-checked:border-red-400 peer-checked:bg-red-400/5'],
                ] as $r)
                <label class="cursor-pointer">
                    <input type="radio" name="role" value="{{ $r['value'] }}"
                           class="hidden peer" {{ old('role', $user->role) === $r['value'] ? 'checked' : '' }}>
                    <div class="p-4 rounded-xl border border-white/10 text-center transition {{ $r['color'] }} hover:border-white/30">
                        <p class="text-2xl mb-1">{{ $r['emoji'] }}</p>
                        <p class="text-white text-sm font-medium">{{ $r['label'] }}</p>
                    </div>
                </label>
                @endforeach
            </div>
            @endif
            @error('role') <p class="text-red-400 text-xs mt-2">{{ $message }}</p> @enderror
        </div>

        {{-- Nouveau mot de passe (optionnel) --}}
        <div class="grid md:grid-cols-2 gap-6">
            <div>
                <label class="block text-white/70 text-sm tracking-wide mb-2">
                    Nouveau mot de passe
                    <span class="text-white/30 font-normal">(laisser vide pour ne pas changer)</span>
                </label>
                <input type="password" name="password"
                       placeholder="Min. 8 caractères"
                       class="w-full bg-black/40 border border-white/10 text-white placeholder-white/30 rounded-xl px-4 py-3 text-sm
                              focus:border-amber-400 focus:outline-none transition" />
                @error('password') <p class="text-red-400 text-xs mt-2">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-white/70 text-sm tracking-wide mb-2">Confirmer le nouveau mot de passe</label>
                <input type="password" name="password_confirmation"
                       placeholder="Répéter le mot de passe"
                       class="w-full bg-black/40 border border-white/10 text-white placeholder-white/30 rounded-xl px-4 py-3 text-sm
                              focus:border-amber-400 focus:outline-none transition" />
            </div>
        </div>

        {{-- Actif --}}
        @if($user->id !== auth()->id())
        <div class="flex items-center gap-3 p-4 bg-white/5 rounded-xl border border-white/10">
            <input type="checkbox" name="active" id="active" value="1"
                   {{ old('active', $user->active) ? 'checked' : '' }}
                   class="w-4 h-4 rounded border-white/20 bg-black/40 text-amber-400 focus:ring-amber-400 focus:ring-offset-0">
            <div>
                <label for="active" class="text-white/70 text-sm font-medium cursor-pointer">Compte actif</label>
                <p class="text-white/30 text-xs mt-0.5">Un compte inactif ne peut pas se connecter.</p>
            </div>
        </div>
        @endif
        @error('active') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror

        {{-- Boutons --}}
        <div class="flex items-center justify-between pt-4 border-t border-white/10">
            @if($user->id !== auth()->id())
            <button type="button"
                    onclick="if(confirm('Supprimer « {{ addslashes($user->name) }} » ?')) { document.getElementById('form-delete-user').submit(); }"
                    class="px-6 py-2 border border-red-500/30 text-red-400 rounded-full text-sm hover:bg-red-500/10 transition">
                Supprimer le compte
            </button>
            @else
            <div></div>
            @endif

            <div class="flex items-center gap-4">
                <a href="{{ route('admin.users.index') }}"
                   class="px-8 py-3 border border-white/10 text-white/60 rounded-full text-sm hover:bg-white/5 transition">
                    Annuler
                </a>
                <button type="submit"
                        class="px-8 py-3 bg-gradient-to-r from-amber-400 to-amber-300 text-black font-semibold
                               rounded-full text-sm shadow-lg shadow-amber-400/20 hover:scale-[1.02] transition-all">
                    Enregistrer
                </button>
            </div>
        </div>
    </form>

    {{-- Formulaire DELETE séparé --}}
    @if($user->id !== auth()->id())
    <form id="form-delete-user" method="POST" action="{{ route('admin.users.destroy', $user) }}">
        @csrf @method('DELETE')
    </form>
    @endif
</div>

@endsection