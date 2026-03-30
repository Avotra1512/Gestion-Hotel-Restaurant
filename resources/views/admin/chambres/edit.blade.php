{{-- resources/views/admin/chambres/edit.blade.php --}}
@extends('layouts.dashboard')

@section('title', 'Modifier la chambre')

@include('components.nav-admin')


@section('content')

{{-- EN-TÊTE --}}
<div class="flex items-center gap-4 mb-8">
    <a href="{{ route('admin.chambres.index') }}"
       class="w-10 h-10 flex items-center justify-center rounded-full border border-white/10
              text-white/60 hover:border-amber-400/50 hover:text-amber-400 transition">
        ←
    </a>
    <div>
        <h2 class="text-2xl font-bold text-white tracking-wide">
            Modifier — <span class="text-amber-400">{{ $chambre->numero_chambre }}</span>
        </h2>
        <p class="text-white/50 text-sm mt-1">Mettez à jour les informations de cette chambre</p>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════
     FORMULAIRE UPDATE — formulaire principal
     ═══════════════════════════════════════════════════════════════ --}}
<div class="max-w-3xl">
    <form method="POST" action="{{ route('admin.chambres.update', $chambre) }}" enctype="multipart/form-data"
          class="bg-white/5 border border-white/10 rounded-2xl p-8 space-y-6">
        @csrf
        @method('PUT')

        {{-- LIGNE 1 : Numéro + Type --}}
        <div class="grid md:grid-cols-2 gap-6">
            <div>
                <label class="block text-white/70 text-sm tracking-wide mb-2">
                    Numéro de chambre <span class="text-amber-400">*</span>
                </label>
                <input type="text" name="numero_chambre"
                       value="{{ old('numero_chambre', $chambre->numero_chambre) }}"
                       class="w-full bg-black/40 border text-white placeholder-white/30 rounded-xl px-4 py-3 text-sm
                              focus:border-amber-400 focus:outline-none transition
                              {{ $errors->has('numero_chambre') ? 'border-red-500' : 'border-white/10' }}" />
                @error('numero_chambre')
                    <p class="text-red-400 text-xs mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-white/70 text-sm tracking-wide mb-2">
                    Type de chambre <span class="text-amber-400">*</span>
                </label>
                <select name="type_chambre"
                        class="w-full bg-black/40 border text-white rounded-xl px-4 py-3 text-sm
                               focus:border-amber-400 focus:outline-none transition
                               {{ $errors->has('type_chambre') ? 'border-red-500' : 'border-white/10' }}">
                    <option value="simple"  {{ old('type_chambre', $chambre->type_chambre) === 'simple'  ? 'selected' : '' }}>Simple</option>
                    <option value="double"  {{ old('type_chambre', $chambre->type_chambre) === 'double'  ? 'selected' : '' }}>Double</option>
                    <option value="triple"  {{ old('type_chambre', $chambre->type_chambre) === 'triple'  ? 'selected' : '' }}>Triple</option>
                </select>
                @error('type_chambre')
                    <p class="text-red-400 text-xs mt-2">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- LIGNE 2 : Prix + Statut --}}
        <div class="grid md:grid-cols-2 gap-6">
            <div>
                <label class="block text-white/70 text-sm tracking-wide mb-2">
                    Prix par nuit (Ar) <span class="text-amber-400">*</span>
                </label>
                <div class="relative">
                    <input type="number" name="prix_nuit" min="0"
                           value="{{ old('prix_nuit', $chambre->prix_nuit) }}"
                           class="w-full bg-black/40 border text-white placeholder-white/30 rounded-xl px-4 py-3 text-sm
                                  focus:border-amber-400 focus:outline-none transition
                                  {{ $errors->has('prix_nuit') ? 'border-red-500' : 'border-white/10' }}" />
                    <span class="absolute right-4 top-1/2 -translate-y-1/2 text-white/30 text-sm">Ar</span>
                </div>
                @error('prix_nuit')
                    <p class="text-red-400 text-xs mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-white/70 text-sm tracking-wide mb-2">
                    Statut <span class="text-amber-400">*</span>
                </label>
                <select name="statut"
                        class="w-full bg-black/40 border text-white rounded-xl px-4 py-3 text-sm
                               focus:border-amber-400 focus:outline-none transition
                               {{ $errors->has('statut') ? 'border-red-500' : 'border-white/10' }}">
                    <option value="disponible"   {{ old('statut', $chambre->statut) === 'disponible'   ? 'selected' : '' }}>Disponible</option>
                    <option value="occupee"      {{ old('statut', $chambre->statut) === 'occupee'      ? 'selected' : '' }}>Occupée</option>
                    <option value="hors_service" {{ old('statut', $chambre->statut) === 'hors_service' ? 'selected' : '' }}>Hors service</option>
                </select>
                @error('statut')
                    <p class="text-red-400 text-xs mt-2">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Équipements --}}
        <div>
            <label class="block text-white/70 text-sm tracking-wide mb-2">
                Équipements
                <span class="text-white/40 font-normal">(séparés par des virgules)</span>
            </label>
            <input type="text" name="equipements"
                   value="{{ old('equipements', $chambre->equipements ? implode(', ', $chambre->equipements) : '') }}"
                   placeholder="Ex: WiFi, Climatisation, TV, Bureau"
                   class="w-full bg-black/40 border border-white/10 text-white placeholder-white/30
                          rounded-xl px-4 py-3 text-sm focus:border-amber-400 focus:outline-none transition" />
        </div>

        {{-- Description --}}
        <div>
            <label class="block text-white/70 text-sm tracking-wide mb-2">Description</label>
            <textarea name="description" rows="4"
                      class="w-full bg-black/40 border border-white/10 text-white placeholder-white/30
                             rounded-xl px-4 py-3 text-sm focus:border-amber-400 focus:outline-none transition resize-none">{{ old('description', $chambre->description) }}</textarea>
        </div>

        {{-- Image --}}
        <div>
            <label class="block text-white/70 text-sm tracking-wide mb-2">Photo de la chambre</label>

            @if ($chambre->image)
                <div class="mb-4 relative group w-40">
                    <img src="{{ Storage::url($chambre->image) }}"
                         alt="{{ $chambre->numero_chambre }}"
                         class="w-40 h-28 object-cover rounded-xl border border-white/10">
                    <div class="absolute inset-0 bg-black/40 rounded-xl opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                        <p class="text-white text-xs">Image actuelle</p>
                    </div>
                </div>
            @endif

            <div class="relative">
                <input type="file" name="image" id="image-upload" accept="image/*"
                       class="hidden" onchange="previewImage(event)">
                <label for="image-upload"
                       class="flex flex-col items-center justify-center w-full h-32 rounded-xl
                              border-2 border-dashed border-white/10 cursor-pointer
                              hover:border-amber-400/50 transition group">
                    <div id="preview-container" class="hidden w-full h-full">
                        <img id="image-preview" class="w-full h-full object-cover rounded-xl" />
                    </div>
                    <div id="upload-placeholder" class="text-center py-4">
                        <p class="text-2xl mb-1">📷</p>
                        <p class="text-white/50 text-xs group-hover:text-amber-400 transition">
                            {{ $chambre->image ? "Cliquez pour changer l'image" : 'Cliquez pour choisir une image' }}
                        </p>
                    </div>
                </label>
            </div>
            @error('image')
                <p class="text-red-400 text-xs mt-2">{{ $message }}</p>
            @enderror
        </div>

        {{-- BOUTONS --}}
        <div class="flex items-center justify-between pt-4 border-t border-white/10">

            {{--
                ⚠️ CORRECTION DU BUG :
                Ce bouton est de type="button" (pas type="submit").
                Il déclenche via JS le formulaire DELETE séparé (#form-delete)
                qui se trouve EN DEHORS de ce formulaire UPDATE.
                Ainsi, "Enregistrer les modifications" (type="submit") ne peut
                déclencher QUE ce formulaire UPDATE, jamais le DELETE.
            --}}
            <button type="button"
                    onclick="if(confirm('Supprimer définitivement la chambre {{ addslashes($chambre->numero_chambre) }} ? Cette action est irréversible.')) { document.getElementById('form-delete').submit(); }"
                    class="px-6 py-2 border border-red-500/30 text-red-400 rounded-full text-sm
                           hover:bg-red-500/10 transition">
                Supprimer la chambre
            </button>

            <div class="flex items-center gap-4">
                <a href="{{ route('admin.chambres.index') }}"
                   class="px-8 py-3 border border-white/10 text-white/60 rounded-full text-sm hover:bg-white/5 transition">
                    Annuler
                </a>
                <button type="submit"
                        class="px-8 py-3 bg-gradient-to-r from-amber-400 to-amber-300 text-black font-semibold
                               rounded-full text-sm shadow-lg shadow-amber-400/20 hover:scale-[1.02] transition-all duration-300">
                    Enregistrer les modifications
                </button>
            </div>
        </div>

    </form>
    {{-- ↑ FIN du formulaire UPDATE ↑ --}}


    {{-- ═══════════════════════════════════════════════════════════════
         FORMULAIRE DELETE — séparé, invisible, hors du formulaire UPDATE
         Déclenché uniquement par le bouton type="button" ci-dessus via JS
         ═══════════════════════════════════════════════════════════════ --}}
    <form id="form-delete"
          method="POST"
          action="{{ route('admin.chambres.destroy', $chambre) }}">
        @csrf
        @method('DELETE')
    </form>

</div>

@push('scripts')
<script>
    function previewImage(event) {
        const file = event.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('image-preview').src = e.target.result;
            document.getElementById('preview-container').classList.remove('hidden');
            document.getElementById('upload-placeholder').classList.add('hidden');
        };
        reader.readAsDataURL(file);
    }
</script>
@endpush

@endsection
