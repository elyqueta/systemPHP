<header class="sticky top-0 z-30 h-16 flex items-center justify-between px-4 sm:px-6 bg-bg/88 backdrop-blur-xl border-b border-black/7">
    <div class="flex items-center gap-3 min-w-0">
        <button id="sidebar-toggle" class="w-9 h-9 rounded-lg border border-black/10 bg-white/80 flex items-center justify-center text-text-2 hover:bg-white hover:text-text hover:border-black/16 transition-all shadow-sm flex-shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="4"/><line x1="9" y1="3" x2="9" y2="21"/></svg>
        </button>
        <div class="min-w-0">
            <h1 class="text-base font-bold text-text tracking-tight truncate">@yield('title', 'Backoffice')</h1>
            @hasSection('breadcrumb')
                <div class="flex items-center gap-1 text-[11px] text-text-3">
                    <span>System</span>
                    @yield('breadcrumb')
                </div>
            @endif
        </div>
    </div>

    <div class="flex items-center gap-2 flex-shrink-0">
        <a href="{{ route('admin.profile') }}" class="hidden sm:inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white border border-black/10 text-text-2 text-sm font-semibold hover:bg-white/80 transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            <span>Perfil</span>
        </a>
        <form method="POST" action="{{ route('admin.logout') }}" class="inline">
            @csrf
            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-black text-white text-sm font-semibold hover:bg-black/80 transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                <span class="hidden sm:inline">Sair</span>
            </button>
        </form>
    </div>
</header>
