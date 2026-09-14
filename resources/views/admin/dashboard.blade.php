@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('breadcrumb')
    <span class="sep">›</span>
    <span>Dashboard</span>
@endsection

@section('content')
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
        <div class="bg-surface-card backdrop-blur-xl border border-glass-border rounded-xl shadow-card p-4 sm:p-6">
            <div class="text-text-3 text-xs font-semibold uppercase tracking-wider mb-2">Instituições</div>
            <div class="text-2xl sm:text-3xl font-extrabold text-text tracking-tight font-mono">{{ $stats['institutions'] ?? 0 }}</div>
            <div class="text-xs text-text-3 mt-1">Total registadas</div>
        </div>
        <div class="bg-surface-card backdrop-blur-xl border border-glass-border rounded-xl shadow-card p-4 sm:p-6">
            <div class="text-text-3 text-xs font-semibold uppercase tracking-wider mb-2">Utilizadores</div>
            <div class="text-2xl sm:text-3xl font-extrabold text-text tracking-tight font-mono">{{ $stats['users'] ?? 0 }}</div>
            <div class="text-xs text-text-3 mt-1">Com acesso ao sistema</div>
        </div>
        <div class="bg-surface-card backdrop-blur-xl border border-glass-border rounded-xl shadow-card p-4 sm:p-6">
            <div class="text-text-3 text-xs font-semibold uppercase tracking-wider mb-2">Estado</div>
            <div class="flex items-center gap-2 mt-4">
                <span class="w-2 h-2 rounded-full bg-green animate-pulse"></span>
                <span class="text-sm font-semibold text-text">Operacional</span>
            </div>
            <div class="text-xs text-text-3 mt-1">Todos os serviços ativos</div>
        </div>
    </div>
@endsection
