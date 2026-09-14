@extends('admin.layouts.app')

@section('title', 'Perfil')
@section('breadcrumb')
    <span class="sep">›</span>
    <span>Perfil</span>
@endsection

@section('content')
    <div class="max-w-2xl space-y-6">
        <div class="mb-6">
            <h2 class="text-lg sm:text-xl font-extrabold text-text tracking-tight">Perfil do Utilizador</h2>
            <p class="text-sm text-text-3 mt-0.5">Informações da sua conta.</p>
        </div>

        <div class="bg-surface-card backdrop-blur-xl border border-glass-border rounded-xl shadow-card p-4 sm:p-6 space-y-5">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-full bg-gradient-to-br from-blue to-purple flex items-center justify-center text-white text-xl font-bold flex-shrink-0">
                    {{ substr(Auth::user()->name, 0, 2) }}
                </div>
                <div class="min-w-0">
                    <div class="text-base font-bold text-text truncate">{{ Auth::user()->name }}</div>
                    <div class="text-sm text-text-3 truncate">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <div class="text-xs font-semibold text-text-3 mb-1">Nome completo</div>
                    <div class="text-sm text-text">{{ Auth::user()->name }}</div>
                </div>
                <div>
                    <div class="text-xs font-semibold text-text-3 mb-1">Email</div>
                    <div class="text-sm text-text">{{ Auth::user()->email }}</div>
                </div>
                <div>
                    <div class="text-xs font-semibold text-text-3 mb-1">Funções</div>
                    <div class="text-sm text-text">{{ Auth::user()->getRoleNames()->implode(', ') ?: 'Sem funções atribuídas' }}</div>
                </div>
                <div>
                    <div class="text-xs font-semibold text-text-3 mb-1">Conta criada em</div>
                    <div class="text-sm text-text">{{ Auth::user()->created_at->format('d/m/Y H:i') }}</div>
                </div>
                <div>
                    <div class="text-xs font-semibold text-text-3 mb-1">Último login</div>
                    <div class="text-sm text-text">{{ Auth::user()->last_login_at ? Auth::user()->last_login_at->format('d/m/Y H:i') : '—' }}</div>
                </div>
            </div>
        </div>
    </div>
@endsection
