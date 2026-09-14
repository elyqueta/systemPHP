@extends('admin.layouts.app')

@section('title', 'Nova Instituição')
@section('breadcrumb')
    <span class="sep">›</span>
    <a href="{{ route('admin.institutions.index') }}" class="hover:text-text-2 transition-colors">Instituições</a>
    <span class="sep">›</span>
    <span>Nova</span>
@endsection

@section('content')
    <div class="max-w-2xl">
        <div class="mb-6">
            <h2 class="text-lg sm:text-xl font-extrabold text-text tracking-tight">Nova Instituição</h2>
            <p class="text-sm text-text-3 mt-0.5">Preencha os dados da nova instituição.</p>
        </div>

        <form method="POST" action="{{ route('admin.institutions.store') }}" class="space-y-5">
            @csrf

            <div class="bg-surface-card backdrop-blur-xl border border-glass-border rounded-xl shadow-card p-4 sm:p-6 space-y-5">
                <div>
                    <label for="name" class="block text-xs font-semibold text-text-2 mb-1.5">Nome <span class="text-red ml-0.5">*</span></label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required
                        class="w-full px-3 py-2.5 bg-black/3 border border-black/10 rounded-lg text-sm text-text placeholder:text-text-3 focus:outline-none focus:border-blue/50 focus:bg-blue/2 transition-all">
                    @error('name')
                        <p class="mt-1.5 text-xs text-red">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="commercial_name" class="block text-xs font-semibold text-text-2 mb-1.5">Nome Comercial</label>
                    <input id="commercial_name" type="text" name="commercial_name" value="{{ old('commercial_name') }}"
                        class="w-full px-3 py-2.5 bg-black/3 border border-black/10 rounded-lg text-sm text-text placeholder:text-text-3 focus:outline-none focus:border-blue/50 focus:bg-blue/2 transition-all">
                    @error('commercial_name')
                        <p class="mt-1.5 text-xs text-red">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="tax_id" class="block text-xs font-semibold text-text-2 mb-1.5">NIF <span class="text-red ml-0.5">*</span></label>
                    <input id="tax_id" type="text" name="tax_id" value="{{ old('tax_id') }}" required
                        class="w-full px-3 py-2.5 bg-black/3 border border-black/10 rounded-lg text-sm text-text placeholder:text-text-3 focus:outline-none focus:border-blue/50 focus:bg-blue/2 transition-all font-mono">
                    @error('tax_id')
                        <p class="mt-1.5 text-xs text-red">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="institution_type" class="block text-xs font-semibold text-text-2 mb-1.5">Tipo <span class="text-red ml-0.5">*</span></label>
                        <select id="institution_type" name="institution_type" required
                            class="w-full px-3 py-2.5 bg-black/3 border border-black/10 rounded-lg text-sm text-text focus:outline-none focus:border-blue/50 focus:bg-blue/2 transition-all appearance-none cursor-pointer">
                            <option value="">Selecionar</option>
                            <option value="LDA" {{ old('institution_type') === 'LDA' ? 'selected' : '' }}>LDA</option>
                            <option value="SA" {{ old('institution_type') === 'SA' ? 'selected' : '' }}>SA</option>
                            <option value="ENI" {{ old('institution_type') === 'ENI' ? 'selected' : '' }}>ENI</option>
                            <option value="ONG" {{ old('institution_type') === 'ONG' ? 'selected' : '' }}>ONG</option>
                            <option value="EP" {{ old('institution_type') === 'EP' ? 'selected' : '' }}>EP</option>
                            <option value="OUTRO" {{ old('institution_type') === 'OUTRO' ? 'selected' : '' }}>OUTRO</option>
                        </select>
                        @error('institution_type')
                            <p class="mt-1.5 text-xs text-red">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="city" class="block text-xs font-semibold text-text-2 mb-1.5">Cidade</label>
                        <input id="city" type="text" name="city" value="{{ old('city') }}"
                            class="w-full px-3 py-2.5 bg-black/3 border border-black/10 rounded-lg text-sm text-text placeholder:text-text-3 focus:outline-none focus:border-blue/50 focus:bg-blue/2 transition-all">
                        @error('city')
                            <p class="mt-1.5 text-xs text-red">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="email" class="block text-xs font-semibold text-text-2 mb-1.5">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}"
                        class="w-full px-3 py-2.5 bg-black/3 border border-black/10 rounded-lg text-sm text-text placeholder:text-text-3 focus:outline-none focus:border-blue/50 focus:bg-blue/2 transition-all">
                    @error('email')
                        <p class="mt-1.5 text-xs text-red">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="website" class="block text-xs font-semibold text-text-2 mb-1.5">Website</label>
                    <input id="website" type="url" name="website" value="{{ old('website') }}"
                        class="w-full px-3 py-2.5 bg-black/3 border border-black/10 rounded-lg text-sm text-text placeholder:text-text-3 focus:outline-none focus:border-blue/50 focus:bg-blue/2 transition-all">
                    @error('website')
                        <p class="mt-1.5 text-xs text-red">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex flex-col-reverse sm:flex-row items-center justify-end gap-3">
                <a href="{{ route('admin.institutions.index') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-white border border-black/10 text-text-2 text-sm font-semibold hover:bg-white/80 transition-colors shadow-sm">
                    Cancelar
                </a>
                <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-blue text-white text-sm font-semibold hover:bg-blue/90 transition-colors shadow-sm">
                    Guardar
                </button>
            </div>
        </form>
    </div>
@endsection
