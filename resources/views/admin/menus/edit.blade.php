@extends('layouts.dashboard')
@section('title', 'Modifier — ' . $menu->nom)
@include('components.nav-admin')
@section('content')

<div class="flex items-center gap-4 mb-8">
    <a href="{{ route('admin.menus.index') }}"
       class="w-10 h-10 flex items-center justify-center rounded-full border border-white/10
              text-white/60 hover:border-amber-400/50 hover:text-amber-400 transition">←</a>
    <div>
        <h2 class="text-2xl font-bold text-white">Modifier — <span class="text-amber-400">{{ $menu->nom }}</span></h2>
        <p class="text-white/50 text-sm mt-1">Mettez à jour les informations de ce plat</p>
    </div>
</div>

<div class="max-w-2xl">
    {{-- FORMULAIRE UPDATE --}}
    <form method="POST" action="{{ route('admin.menus.update', $menu) }}" enctype="multipart/form-data"
          class="bg-white/5 border border-white/10 rounded-2xl p-8 space-y-6">
        @csrf @method('PUT')

        {{-- Nom --}}
        <div>
            <label class="block text-white/70 text-sm tracking-wide mb-2">Nom du plat <span class="text-amber-400">*</span></label>
            <input type="text" name="nom" value="{{ old('nom', $menu->nom) }}"
                   class="w-full bg-black/40 border text-white rounded-xl px-4 py-3 text-sm
                          focus:border-amber-400 focus:outline-none transition
                          {{ $errors->has('nom') ? 'border-red-500' : 'border-white/10' }}" />
            @error('nom') <p class="text-red-400 text-xs mt-2">{{ $message }}</p> @enderror
        </div>

        {{-- Catégorie + Prix --}}
        <div class="grid md:grid-cols-2 gap-6">
            <div>
                <label class="block text-white/70 text-sm tracking-wide mb-2">Catégorie <span class="text-amber-400">*</span></label>
                <select name="categorie"
                        class="w-full bg-black/40 border border-white/10 text-white rounded-xl px-4 py-3 text-sm focus:border-amber-400 focus:outline-none transition">
                    <option value="entree"         {{ old('categorie',$menu->categorie)==='entree'         ? 'selected':'' }}>Entrée</option>
                    <option value="plat_principal" {{ old('categorie',$menu->categorie)==='plat_principal' ? 'selected':'' }}>Plat principal</option>
                    <option value="dessert"        {{ old('categorie',$menu->categorie)==='dessert'        ? 'selected':'' }}>Dessert</option>
                    <option value="boisson"        {{ old('categorie',$menu->categorie)==='boisson'        ? 'selected':'' }}>Boisson</option>
                    <option value="formule"        {{ old('categorie',$menu->categorie)==='formule'        ? 'selected':'' }}>Formule</option>
                </select>
            </div>
            <div>
                <label class="block text-white/70 text-sm tracking-wide mb-2">Prix (Ar) <span class="text-amber-400">*</span></label>
                <div class="relative">
                    <input type="number" name="prix" min="0" value="{{ old('prix', $menu->prix) }}"
                           class="w-full bg-black/40 border border-white/10 text-white rounded-xl px-4 py-3 text-sm focus:border-amber-400 focus:outline-none transition" />
                    <span class="absolute right-4 top-1/2 -translate-y-1/2 text-white/30 text-sm">Ar</span>
                </div>
            </div>
        </div>

        {{-- Description --}}
        <div>
            <label class="block text-white/70 text-sm tracking-wide mb-2">Description</label>
            <textarea name="description" rows="3"
                      class="w-full bg-black/40 border border-white/10 text-white placeholder-white/30
                             rounded-xl px-4 py-3 text-sm focus:border-amber-400 focus:outline-none transition resize-none">{{ old('description', $menu->description) }}</textarea>
        </div>

        {{-- Disponible --}}
        <div class="flex items-center gap-3">
            <input type="checkbox" name="disponible" id="disponible" value="1"
                   {{ old('disponible', $menu->disponible) ? 'checked' : '' }}
                   class="w-4 h-4 rounded border-white/20 bg-black/40 text-amber-400 focus:ring-amber-400 focus:ring-offset-0">
            <label for="disponible" class="text-white/70 text-sm">Disponible à la commande</label>
        </div>

        {{-- Image actuelle + nouvelle --}}
        <div>
            <label class="block text-white/70 text-sm tracking-wide mb-2">Photo du plat</label>
            @if($menu->image)
                <div class="mb-4 relative group w-40">
                    <img src="{{ Storage::url($menu->image) }}" alt="{{ $menu->nom }}"
                         class="w-40 h-28 object-cover rounded-xl border border-white/10">
                    <div class="absolute inset-0 bg-black/40 rounded-xl opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                        <p class="text-white text-xs">Image actuelle</p>
                    </div>
                </div>
            @endif
            <input type="file" name="image" id="image-upload" accept="image/*" class="hidden" onchange="previewImage(event)">
            <label for="image-upload"
                   class="flex flex-col items-center justify-center w-full h-32 rounded-xl border-2 border-dashed border-white/10 cursor-pointer hover:border-amber-400/50 transition group">
                <div id="preview-container" class="hidden w-full h-full">
                    <img id="image-preview" class="w-full h-full object-cover rounded-xl" />
                </div>
                <div id="upload-placeholder" class="text-center py-4">
                    <p class="text-2xl mb-1">📷</p>
                    <p class="text-white/50 text-xs group-hover:text-amber-400 transition">
                        {{ $menu->image ? "Changer l'image" : "Choisir une image" }}
                    </p>
                </div>
            </label>
        </div>

        {{-- Boutons --}}
        <div class="flex items-center justify-between pt-4 border-t border-white/10">
            {{-- Bouton supprimer — déclenche le formulaire DELETE séparé via JS --}}
            <button type="button"
                    onclick="if(confirm('Supprimer « {{ addslashes($menu->nom) }} » ?')) { document.getElementById('form-delete').submit(); }"
                    class="px-6 py-2 border border-red-500/30 text-red-400 rounded-full text-sm hover:bg-red-500/10 transition">
                Supprimer
            </button>
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.menus.index') }}"
                   class="px-8 py-3 border border-white/10 text-white/60 rounded-full text-sm hover:bg-white/5 transition">Annuler</a>
                <button type="submit"
                        class="px-8 py-3 bg-gradient-to-r from-amber-400 to-amber-300 text-black font-semibold
                               rounded-full text-sm shadow-lg shadow-amber-400/20 hover:scale-[1.02] transition-all">
                    Enregistrer
                </button>
            </div>
        </div>
    </form>

    {{-- FORMULAIRE DELETE séparé --}}
    <form id="form-delete" method="POST" action="{{ route('admin.menus.destroy', $menu) }}">
        @csrf @method('DELETE')
    </form>
</div>

@push('scripts')
<script>
function previewImage(event) {
    const file = event.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => {
        document.getElementById('image-preview').src = e.target.result;
        document.getElementById('preview-container').classList.remove('hidden');
        document.getElementById('upload-placeholder').classList.add('hidden');
    };
    reader.readAsDataURL(file);
}
</script>
@endpush

@endsection