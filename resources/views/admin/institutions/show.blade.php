@extends('admin.layouts.app')

@section('title', $institution->name)
@section('breadcrumb')
    <span class="sep">›</span>
    <a href="{{ route('admin.institutions.index') }}" class="text-text-3 hover:text-text transition-colors">Instituições</a>
    <span class="sep">›</span>
    <span>{{ $institution->name }}</span>
@endsection

@section('content')
    <div class="space-y-4 sm:space-y-6">
        <div class="flex items-center justify-between gap-4 flex-wrap">
            <div>
                <h2 class="text-lg sm:text-xl font-extrabold text-text tracking-tight">{{ $institution->name }}</h2>
                <p class="text-sm text-text-3 mt-0.5">Detalhes completos da instituição.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.institutions.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-white border border-black/10 text-text text-sm font-semibold hover:bg-white/80 transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
                    <span>Voltar</span>
                </a>
                @if($institution->active)
                    <form method="POST" action="{{ route('admin.institutions.deactivate', $institution->uuid) }}" class="inline" onsubmit="return confirm('Desativar esta instituição?')">
                        @csrf
                        @method('POST')
                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-red text-white text-sm font-semibold hover:bg-red/90 transition-colors shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
                            <span>Desativar</span>
                        </button>
                    </form>
                @else
                    <form method="POST" action="{{ route('admin.institutions.activate', $institution->uuid) }}" class="inline" onsubmit="return confirm('Ativar esta instituição?')">
                        @csrf
                        @method('POST')
                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-green text-white text-sm font-semibold hover:bg-green/90 transition-colors shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 6L9 17l-5-5"/></svg>
                            <span>Ativar</span>
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
            <div class="lg:col-span-2 space-y-4 sm:space-y-6">
                <div class="bg-surface-card backdrop-blur-xl border border-glass-border rounded-xl shadow-card overflow-hidden">
                    <div class="px-4 sm:px-6 py-4 border-b border-black/6">
                        <h3 class="text-sm font-bold text-text uppercase tracking-wider">Informações Gerais</h3>
                    </div>
                    <div class="p-4 sm:p-6">
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <dt class="text-xs font-semibold text-text-3 uppercase tracking-wider mb-1">Nome</dt>
                                <dd class="text-sm text-text font-medium">{{ $institution->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold text-text-3 uppercase tracking-wider mb-1">Nome Comercial</dt>
                                <dd class="text-sm text-text font-medium">{{ $institution->commercial_name ?? '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold text-text-3 uppercase tracking-wider mb-1">NIF</dt>
                                <dd class="text-sm text-text font-mono">{{ $institution->tax_id ?? '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold text-text-3 uppercase tracking-wider mb-1">Tipo</dt>
                                <dd class="text-sm text-text font-medium">{{ $institution->institution_type ?? '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold text-text-3 uppercase tracking-wider mb-1">Data de Fundação</dt>
                                <dd class="text-sm text-text font-medium">{{ $institution->founding_date ? \Carbon\Carbon::parse($institution->founding_date)->format('d/m/Y') : '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold text-text-3 uppercase tracking-wider mb-1">Estado</dt>
                                <dd class="text-sm">
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
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold text-text-3 uppercase tracking-wider mb-1">Telefone</dt>
                                <dd class="text-sm text-text font-medium">{{ $institution->phone ?? '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold text-text-3 uppercase tracking-wider mb-1">Email</dt>
                                <dd class="text-sm text-text font-medium">{{ $institution->email ?? '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold text-text-3 uppercase tracking-wider mb-1">Website</dt>
                                <dd class="text-sm text-text font-medium">
                                    @if($institution->website)
                                        <a href="{{ $institution->website }}" target="_blank" rel="noopener" class="text-blue hover:underline break-all">{{ $institution->website }}</a>
                                    @else
                                        —
                                    @endif
                                </dd>
                            </div>
                            <div class="sm:col-span-2">
                                <dt class="text-xs font-semibold text-text-3 uppercase tracking-wider mb-1">Endereço</dt>
                                <dd class="text-sm text-text font-medium">{{ $institution->address ?? '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold text-text-3 uppercase tracking-wider mb-1">Bairro</dt>
                                <dd class="text-sm text-text font-medium">{{ $institution->neighborhood ?? '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold text-text-3 uppercase tracking-wider mb-1">Cidade</dt>
                                <dd class="text-sm text-text font-medium">{{ $institution->city ?? '—' }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <div class="bg-surface-card backdrop-blur-xl border border-glass-border rounded-xl shadow-card overflow-hidden">
                    <div class="px-4 sm:px-6 py-4 border-b border-black/6">
                        <h3 class="text-sm font-bold text-text uppercase tracking-wider">Configuração Fiscal</h3>
                    </div>
                    <div class="p-4 sm:p-6">
                        @if($institution->taxConfiguration)
                            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <dt class="text-xs font-semibold text-text-3 uppercase tracking-wider mb-1">Taxa Segurança Social (Funcionário)</dt>
                                    <dd class="text-sm text-text font-medium">{{ $institution->taxConfiguration->employee_social_security_rate ? $institution->taxConfiguration->employee_social_security_rate.'%' : '—' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-semibold text-text-3 uppercase tracking-wider mb-1">Taxa Segurança Social (Empregador)</dt>
                                    <dd class="text-sm text-text font-medium">{{ $institution->taxConfiguration->employer_social_security_rate ? $institution->taxConfiguration->employer_social_security_rate.'%' : '—' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-semibold text-text-3 uppercase tracking-wider mb-1">Subsídio Alimentação</dt>
                                    <dd class="text-sm text-text font-medium">{{ $institution->taxConfiguration->meal_allowance ? number_format($institution->taxConfiguration->meal_allowance, 2, ',', '.').' Kz' : '—' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-semibold text-text-3 uppercase tracking-wider mb-1">Subsídio Transporte</dt>
                                    <dd class="text-sm text-text font-medium">{{ $institution->taxConfiguration->transport_allowance ? number_format($institution->taxConfiguration->transport_allowance, 2, ',', '.').' Kz' : '—' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-semibold text-text-3 uppercase tracking-wider mb-1">Regime Fiscal</dt>
                                    <dd class="text-sm text-text font-medium">{{ $institution->taxConfiguration->tax_regime ?? '—' }}</dd>
                                </div>
                            </dl>
                        @else
                            <p class="text-sm text-text-3">Nenhuma configuração fiscal definida.</p>
                        @endif
                    </div>
                </div>

                <div class="bg-surface-card backdrop-blur-xl border border-glass-border rounded-xl shadow-card overflow-hidden">
                    <div class="px-4 sm:px-6 py-4 border-b border-black/6">
                        <h3 class="text-sm font-bold text-text uppercase tracking-wider">Contas Bancárias</h3>
                    </div>
                    <div class="p-4 sm:p-6">
                        @if($institution->bankAccounts->count() > 0)
                            <div class="overflow-x-auto -mx-4 sm:mx-0">
                                <table class="w-full text-sm min-w-[640px]">
                                    <thead>
                                        <tr class="border-b border-black/7">
                                            <th class="text-left px-4 sm:px-6 py-3 text-[10.5px] font-bold text-text-3 uppercase tracking-wider">Banco</th>
                                            <th class="text-left px-4 sm:px-6 py-3 text-[10.5px] font-bold text-text-3 uppercase tracking-wider">Número da Conta</th>
                                            <th class="text-left px-4 sm:px-6 py-3 text-[10.5px] font-bold text-text-3 uppercase tracking-wider hidden md:table-cell">IBAN</th>
                                            <th class="text-left px-4 sm:px-6 py-3 text-[10.5px] font-bold text-text-3 uppercase tracking-wider hidden lg:table-cell">SWIFT/BIC</th>
                                            <th class="text-left px-4 sm:px-6 py-3 text-[10.5px] font-bold text-text-3 uppercase tracking-wider">Moeda</th>
                                            <th class="text-left px-4 sm:px-6 py-3 text-[10.5px] font-bold text-text-3 uppercase tracking-wider">Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-black/4">
                                        @foreach ($institution->bankAccounts as $account)
                                            <tr class="hover:bg-black/[0.015] transition-colors">
                                                <td class="px-4 sm:px-6 py-3.5 text-text font-medium">{{ $account->bank_name ?? '—' }}</td>
                                                <td class="px-4 sm:px-6 py-3.5 text-text-2 font-mono">{{ $account->account_number ?? '—' }}</td>
                                                <td class="px-4 sm:px-6 py-3.5 text-text-2 font-mono hidden md:table-cell">{{ $account->iban ?? '—' }}</td>
                                                <td class="px-4 sm:px-6 py-3.5 text-text-2 font-mono hidden lg:table-cell">{{ $account->swift_bic ?? '—' }}</td>
                                                <td class="px-4 sm:px-6 py-3.5 text-text-2">{{ $account->currency ?? 'AOA' }}</td>
                                                <td class="px-4 sm:px-6 py-3.5">
                                                    @if($account->active)
                                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-green/10 text-green text-xs font-semibold">
                                                            <span class="w-1.5 h-1.5 rounded-full bg-green"></span>
                                                            Ativa
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-red/10 text-red text-xs font-semibold">
                                                            <span class="w-1.5 h-1.5 rounded-full bg-red"></span>
                                                            Inativa
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-sm text-text-3">Nenhuma conta bancária registada.</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="space-y-4 sm:space-y-6">
                <div class="bg-surface-card backdrop-blur-xl border border-glass-border rounded-xl shadow-card overflow-hidden">
                    <div class="px-4 sm:px-6 py-4 border-b border-black/6">
                        <h3 class="text-sm font-bold text-text uppercase tracking-wider">Equipa / Utilizadores</h3>
                    </div>
                    <div class="p-4 sm:p-6">
                        @if($institution->users->count() > 0)
                            <ul class="space-y-3">
                                @foreach ($institution->users as $user)
                                    <li class="flex items-center gap-3 p-3 rounded-lg bg-black/[0.02] border border-black/5">
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue to-purple flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                            {{ substr($user->name, 0, 2) }}
                                        </div>
                                        <div class="min-w-0">
                                            <div class="text-sm font-semibold text-text truncate">{{ $user->name }}</div>
                                            <div class="text-xs text-text-3 truncate">{{ $user->email }}</div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-sm text-text-3">Nenhum utilizador associado.</p>
                        @endif
                    </div>
                </div>

                <div class="bg-surface-card backdrop-blur-xl border border-glass-border rounded-xl shadow-card overflow-hidden">
                    <div class="px-4 sm:px-6 py-4 border-b border-black/6">
                        <h3 class="text-sm font-bold text-text uppercase tracking-wider">Resumo</h3>
                    </div>
                    <div class="p-4 sm:p-6 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-text-3">Contas Bancárias</span>
                            <span class="text-sm font-semibold text-text">{{ $institution->bankAccounts->count() }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-text-3">Utilizadores</span>
                            <span class="text-sm font-semibold text-text">{{ $institution->users->count() }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-text-3">Configuração Fiscal</span>
                            <span class="text-sm font-semibold text-text">{{ $institution->taxConfiguration ? 'Definida' : 'Não definida' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
