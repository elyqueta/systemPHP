@extends('admin.layouts.app')

@section('title', 'Visão geral')
@section('breadcrumb')
    <span class="sep">›</span>
    <span>Visão geral</span>
@endsection

@section('content')
    <div class="space-y-6">
        <div>
            <h2 class="text-lg sm:text-xl font-extrabold text-text tracking-tight">Visão geral</h2>
            <p class="text-sm text-text-3 mt-0.5">Resumo das instituições, contas bancárias e configuração fiscal.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6">
            <div class="bg-surface-card backdrop-blur-xl border border-glass-border rounded-xl shadow-card p-4 sm:p-6">
                <div class="text-text-3 text-xs font-semibold uppercase tracking-wider mb-2">Instituições</div>
                <div class="text-2xl sm:text-3xl font-extrabold text-text tracking-tight font-mono">{{ $stats['institutions'] ?? 0 }}</div>
                <div class="text-xs text-text-3 mt-1">Total registadas</div>
            </div>
            <div class="bg-surface-card backdrop-blur-xl border border-glass-border rounded-xl shadow-card p-4 sm:p-6">
                <div class="text-text-3 text-xs font-semibold uppercase tracking-wider mb-2">Contas bancárias</div>
                <div class="text-2xl sm:text-3xl font-extrabold text-text tracking-tight font-mono">{{ $stats['bank_accounts'] ?? 0 }}</div>
                <div class="text-xs text-text-3 mt-1">Total associadas</div>
            </div>
            <div class="bg-surface-card backdrop-blur-xl border border-glass-border rounded-xl shadow-card p-4 sm:p-6">
                <div class="text-text-3 text-xs font-semibold uppercase tracking-wider mb-2">Configurações fiscais</div>
                <div class="text-2xl sm:text-3xl font-extrabold text-text tracking-tight font-mono">{{ $stats['tax_configs'] ?? 0 }}</div>
                <div class="text-xs text-text-3 mt-1">Definidas</div>
            </div>
        </div>

        <div class="bg-surface-card backdrop-blur-xl border border-glass-border rounded-xl shadow-card overflow-hidden">
            <div class="px-4 sm:px-6 py-4 border-b border-black/6">
                <h3 class="text-sm font-bold text-text">Instituições</h3>
            </div>
            <div class="overflow-x-auto -mx-4 sm:mx-0">
                <table class="w-full text-sm min-w-[640px]">
                    <thead>
                        <tr class="border-b border-black/7">
                            <th class="text-left px-4 sm:px-6 py-3 text-[10.5px] font-bold text-text-3 uppercase tracking-wider">Nome</th>
                            <th class="text-left px-4 sm:px-6 py-3 text-[10.5px] font-bold text-text-3 uppercase tracking-wider">NIF</th>
                            <th class="text-left px-4 sm:px-6 py-3 text-[10.5px] font-bold text-text-3 uppercase tracking-wider hidden sm:table-cell">Tipo</th>
                            <th class="text-left px-4 sm:px-6 py-3 text-[10.5px] font-bold text-text-3 uppercase tracking-wider hidden md:table-cell">Cidade</th>
                            <th class="text-left px-4 sm:px-6 py-3 text-[10.5px] font-bold text-text-3 uppercase tracking-wider">Estado</th>
                            <th class="text-left px-4 sm:px-6 py-3 text-[10.5px] font-bold text-text-3 uppercase tracking-wider">Contas</th>
                            <th class="text-left px-4 sm:px-6 py-3 text-[10.5px] font-bold text-text-3 uppercase tracking-wider hidden md:table-cell">Fiscal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-black/4">
                        @forelse ($institutions as $institution)
                            <tr class="hover:bg-black/[0.015] transition-colors">
                                <td class="px-4 sm:px-6 py-3.5">
                                    <div class="font-semibold text-text">{{ $institution->name }}</div>
                                    @if($institution->commercial_name)
                                        <div class="text-xs text-text-3 mt-0.5">{{ $institution->commercial_name }}</div>
                                    @endif
                                </td>
                                <td class="px-4 sm:px-6 py-3.5 text-text-2 font-mono">{{ $institution->tax_id ?? '—' }}</td>
                                <td class="px-4 sm:px-6 py-3.5 text-text-2 hidden sm:table-cell">{{ $institution->institution_type }}</td>
                                <td class="px-4 sm:px-6 py-3.5 text-text-2 hidden md:table-cell">{{ $institution->city ?? '—' }}</td>
                                <td class="px-4 sm:px-6 py-3.5">
                                    @if($institution->active)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-green/10 text-green text-xs font-semibold">
                                            <span class="w-1.5 h-1.5 rounded-full bg-green"></span>
                                            Ativo
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-red/10 text-red text-xs font-semibold">
                                            <span class="w-1.5 h-1.5 rounded-full bg-red"></span>
                                            Inativo
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 sm:px-6 py-3.5 text-text-2">{{ $institution->bank_accounts_count ?? 0 }}</td>
                                <td class="px-4 sm:px-6 py-3.5 text-text-2 hidden md:table-cell">
                                    @if($institution->taxConfiguration)
                                        <span class="text-xs">{{ $institution->taxConfiguration->tax_regime ?? '—' }}</span>
                                    @else
                                        <span class="text-xs text-text-3">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 sm:px-6 py-12 text-center text-text-3 text-sm">
                                    Nenhuma instituição registada.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
