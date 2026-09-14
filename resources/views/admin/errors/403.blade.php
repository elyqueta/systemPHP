@extends('admin.layouts.app')

@section('title', 'Não autorizado')
@section('breadcrumb')
    <span class="sep">›</span>
    <span>Acesso negado</span>
@endsection

@section('content')
    <div class="flex flex-col items-center justify-center py-20 text-center">
        <div class="w-16 h-16 rounded-full bg-red/10 text-red flex items-center justify-center mb-6">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <h2 class="text-xl font-bold text-text mb-2">Não autorizado</h2>
        <p class="text-sm text-text-3 max-w-md">Não tem permissão para aceder a esta página.</p>
        <a href="{{ route('admin.dashboard') }}" class="mt-6 inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-blue text-white text-sm font-semibold hover:bg-blue/90 transition-colors shadow-sm">
            Voltar ao dashboard
        </a>
    </div>
@endsection
