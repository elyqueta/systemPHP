@extends('admin.layouts.app')

@section('title', 'Instituições')
@section('breadcrumb')
    <span class="sep">›</span>
    <span>Instituições</span>
@endsection

@section('content')
    <div class="space-y-4 sm:space-y-6">
        <div class="flex items-center justify-between gap-4 flex-wrap">
            <div>
                <h2 class="text-lg sm:text-xl font-extrabold text-text tracking-tight">Instituições</h2>
                <p class="text-sm text-text-3 mt-0.5">Gerir instituições registadas na plataforma.</p>
            </div>
            <a href="{{ route('admin.institutions.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-blue text-white text-sm font-semibold hover:bg-blue/90 transition-colors shadow-sm whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                <span class="hidden sm:inline">Nova Instituição</span>
                <span class="sm:hidden">Nova</span>
            </a>
        </div>

        <div class="bg-surface-card backdrop-blur-xl border border-glass-border rounded-xl shadow-card overflow-hidden">
            <div class="overflow-x-auto -mx-4 sm:mx-0">
                <table class="w-full text-sm min-w-[640px]" id="institutions-table">
                    <thead>
                        <tr class="border-b border-black/7">
                            <th class="text-left px-4 sm:px-6 py-3 text-[10.5px] font-bold text-text-3 uppercase tracking-wider">Nome</th>
                            <th class="text-left px-4 sm:px-6 py-3 text-[10.5px] font-bold text-text-3 uppercase tracking-wider">NIF</th>
                            <th class="text-left px-4 sm:px-6 py-3 text-[10.5px] font-bold text-text-3 uppercase tracking-wider hidden sm:table-cell">Tipo</th>
                            <th class="text-left px-4 sm:px-6 py-3 text-[10.5px] font-bold text-text-3 uppercase tracking-wider hidden md:table-cell">Cidade</th>
                            <th class="text-left px-4 sm:px-6 py-3 text-[10.5px] font-bold text-text-3 uppercase tracking-wider">Estado</th>
                            <th class="text-right px-4 sm:px-6 py-3 text-[10.5px] font-bold text-text-3 uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-black/4">
                        @forelse ($institutions as $institution)
                            <tr class="hover:bg-black/[0.015] transition-colors" data-uuid="{{ $institution->uuid }}">
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
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold status-badge {{ $institution->active ? 'bg-green/10 text-green' : 'bg-red/10 text-red' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $institution->active ? 'bg-green' : 'bg-red' }}"></span>
                                        <span class="status-text">{{ $institution->active ? 'Ativo' : 'Inativo' }}</span>
                                    </span>
                                </td>
                                <td class="px-4 sm:px-6 py-3.5 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.institutions.show', $institution->uuid) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-white border border-black/10 text-text-2 text-xs font-semibold hover:bg-white/80 transition-colors shadow-sm" title="Ver detalhes">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                            <span class="hidden sm:inline">Ver</span>
                                        </a>
                                        <form method="POST" action="/api/v1/institutions/{{ $institution->uuid }}/{{ $institution->active ? 'deactivate' : 'activate' }}" class="inline toggle-form">
                                            @csrf
                                            @method('POST')
                                            <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors shadow-sm toggle-btn {{ $institution->active ? 'bg-white border border-black/10 text-text-2 hover:bg-white/80' : 'bg-green text-white hover:bg-green/90' }}" title="{{ $institution->active ? 'Desativar' : 'Ativar' }}">
                                                @if($institution->active)
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
                                                    <span class="hidden sm:inline">Desativar</span>
                                                @else
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path d="M20 6L9 17l-5-5"/></svg>
                                                    <span class="hidden sm:inline">Ativar</span>
                                                @endif
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 sm:px-6 py-12 text-center">
                                    <div class="text-text-3 text-sm">Nenhuma instituição registada.</div>
                                    <a href="{{ route('admin.institutions.create') }}" class="inline-flex items-center gap-2 mt-4 px-4 py-2 rounded-lg bg-blue/10 border border-blue/18 text-blue text-sm font-semibold hover:bg-blue/14 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                        Nova Instituição
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($institutions->hasPages())
                <div class="px-4 sm:px-6 py-4 border-t border-black/6">
                    {{ $institutions->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const table = document.getElementById('institutions-table');

    if (!table) return;

    table.addEventListener('submit', function(e) {
        const form = e.target.closest('form');
        if (!form || !form.classList.contains('toggle-form')) return;

        e.preventDefault();

        const row = form.closest('tr');
        const button = form.querySelector('button[type="submit"]');
        const originalContent = button.innerHTML;
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || form.querySelector('input[name="_token"]')?.value;

        button.disabled = true;
        button.innerHTML = '<span class="inline-flex items-center gap-1"><svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> A processar...</span>';

        const isActivate = button.title === 'Ativar' || button.textContent.includes('Ativar');

        fetch(form.action, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}),
            },
            body: new URLSearchParams(new FormData(form)),
            credentials: 'same-origin',
        })
        .then(response => {
            if (!response.ok) {
                return response.json().catch(() => ({})).then(err => Promise.reject(err));
            }
            return response.json();
        })
        .then(data => {
            const apiData = data.data || data;
            const isActive = typeof apiData.active === 'boolean' ? apiData.active : !isActivate;

            const statusCell = row.querySelector('td:nth-child(5)');

            statusCell.innerHTML = `
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold status-badge ${isActive ? 'bg-green/10 text-green' : 'bg-red/10 text-red'}">
                    <span class="w-1.5 h-1.5 rounded-full ${isActive ? 'bg-green' : 'bg-red'}"></span>
                    <span class="status-text">${isActive ? 'Ativo' : 'Inativo'}</span>
                </span>
            `;

            const newAction = isActive
                ? '/api/v1/institutions/' + row.dataset.uuid + '/deactivate'
                : '/api/v1/institutions/' + row.dataset.uuid + '/activate';

            form.action = newAction;

            const newButtonClasses = isActive
                ? 'inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-white border border-black/10 text-text-2 text-xs font-semibold hover:bg-white/80 transition-colors shadow-sm'
                : 'inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-green text-white text-xs font-semibold hover:bg-green/90 transition-colors shadow-sm';

            const newButtonTitle = isActive ? 'Desativar' : 'Ativar';
            const newButtonContent = isActive
                ? '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg><span class="hidden sm:inline">Desativar</span>'
                : '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path d="M20 6L9 17l-5-5"/></svg><span class="hidden sm:inline">Ativar</span>';

            button.className = newButtonClasses;
            button.title = newButtonTitle;
            button.innerHTML = newButtonContent;
            button.disabled = false;
        })
        .catch(error => {
            button.disabled = false;
            button.innerHTML = originalContent;
            const message = (error && error.message) ? error.message : 'Não foi possível atualizar o estado. Tente novamente.';
            alert(message);
        });
    });
});
</script>
@endpush
