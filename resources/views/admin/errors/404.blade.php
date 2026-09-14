@extends('admin.layouts.app')

@section('title', 'Página não encontrada')
@section('breadcrumb')
    <span class="sep">›</span>
    <span>Não encontrado</span>
@endsection

@section('content')
    <div class="flex flex-col items-center justify-center py-20 text-center">
        <div class="w-16 h-16 rounded-full bg-black/5 text-text-2 flex items-center justify-center mb-6">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        </div>
        <h2 class="text-xl font-bold text-text mb-2">Página não encontrada</h2>
        <p class="text-sm text-text-3 max-w-md">A página que procura não existe ou foi removida.</p>
        <a href="{{ route('admin.dashboard') }}" class="mt-6 inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-blue text-white text-sm font-semibold hover:bg-blue/90 transition-colors shadow-sm">
            Voltar ao dashboard
        </a>
    </div>
@endsection
