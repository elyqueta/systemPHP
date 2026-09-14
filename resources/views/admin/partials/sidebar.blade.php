<aside id="sidebar" class="fixed inset-y-0 left-0 z-40 w-64 bg-sidebar-bg border-r border-white/10 transition-all duration-200 -translate-x-full lg:translate-x-0 lg:w-64" data-state="mobile-closed">
    <div class="flex h-16 items-center gap-3 px-5 border-b border-white/6">
        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-blue to-purple flex items-center justify-center shadow-lg shadow-blue/30">
            <span class="text-white font-bold text-xs">K</span>
        </div>
        <div class="sidebar-text">
            <div class="text-white font-bold text-sm leading-tight">System</div>
            <div class="text-white/30 text-[9px] font-medium tracking-widest uppercase">Backoffice</div>
        </div>
    </div>

    <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-6">
        <div>
            <div class="sidebar-text px-2 mb-2 text-[9px] font-bold tracking-widest uppercase text-white/20">Principal</div>
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-white/50 hover:bg-white/7 hover:text-white/90 transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-white/7 text-white/90' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>
                <span class="sidebar-text text-sm font-medium">Dashboard</span>
            </a>
            <a href="{{ route('admin.profile') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-white/50 hover:bg-white/7 hover:text-white/90 transition-colors {{ request()->routeIs('admin.profile') ? 'bg-white/7 text-white/90' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                <span class="sidebar-text text-sm font-medium">Perfil</span>
            </a>
        </div>

        <div>
            <div class="sidebar-text px-2 mb-2 text-[9px] font-bold tracking-widest uppercase text-white/20">Gestão</div>
            <a href="{{ route('admin.institutions.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-white/50 hover:bg-white/7 hover:text-white/90 transition-colors {{ request()->routeIs('admin.institutions.*') ? 'bg-white/7 text-white/90' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path d="M3 21V7l9-4 9 4v14"/><path d="M9 21V12h6v9"/></svg>
                <span class="sidebar-text text-sm font-medium">Instituições</span>
            </a>
        </div>
    </nav>

    <div class="border-t border-white/6 p-3">
        @if(Auth::check())
        <div class="flex items-center gap-3 px-3 py-2 rounded-lg bg-white/4 mb-2">
            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue to-purple flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                {{ substr(Auth::user()->name, 0, 2) }}
            </div>
            <div class="sidebar-text min-w-0">
                <div class="text-white/80 text-sm font-semibold truncate">{{ Auth::user()->name }}</div>
                <div class="text-white/30 text-[11px] truncate">{{ Auth::user()->email }}</div>
            </div>
        </div>
        @endif
        <form method="POST" action="{{ route('admin.logout') }}" class="block">
            @csrf
            <button type="submit" class="w-full flex items-center justify-center gap-2 px-3 py-2 rounded-lg border border-white/8 text-white/35 hover:border-red/40 hover:text-red/90 transition-colors text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                <span class="sidebar-text">Terminar Sessão</span>
            </button>
        </form>
    </div>
</aside>

<style>
@media (max-width: 1023px) {
    #sidebar.mobile-open {
        transform: translateX(0) !important;
    }
    #sidebar[data-state="mobile-closed"] {
        transform: translateX(-100%) !important;
    }
    #sidebar {
        box-shadow: 4px 0 24px rgba(0,0,0,0.25);
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('main-content');
    const toggleBtn = document.getElementById('sidebar-toggle');
    const overlay = document.getElementById('sidebar-overlay');

    function updateSidebarState() {
        const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
        const isMobile = window.innerWidth < 1024;

        if (isMobile) {
            sidebar.classList.remove('w-64', 'w-18', 'collapsed');
            sidebar.classList.add('-translate-x-full');
            sidebar.classList.remove('translate-x-0');
            mainContent.classList.remove('ml-64', 'ml-18');
            mainContent.classList.add('ml-0');
            overlay.classList.add('hidden');
            sidebar.dataset.state = 'mobile-closed';
        } else {
            sidebar.classList.remove('-translate-x-full', 'mobile-open');
            sidebar.classList.add('translate-x-0');
            overlay.classList.add('hidden');

            if (isCollapsed) {
                sidebar.classList.remove('w-64');
                sidebar.classList.add('w-18');
                mainContent.classList.remove('ml-64');
                mainContent.classList.add('ml-18');
                sidebar.dataset.state = 'collapsed';
            } else {
                sidebar.classList.remove('w-18');
                sidebar.classList.add('w-64');
                mainContent.classList.remove('ml-18');
                mainContent.classList.add('ml-64');
                sidebar.dataset.state = 'expanded';
            }
        }
    }

    function openMobileSidebar() {
        sidebar.classList.remove('-translate-x-full');
        sidebar.classList.add('translate-x-0');
        overlay.classList.remove('hidden');
        sidebar.dataset.state = 'mobile-open';
    }

    function closeMobileSidebar() {
        sidebar.classList.add('-translate-x-full');
        sidebar.classList.remove('translate-x-0');
        overlay.classList.add('hidden');
        sidebar.dataset.state = 'mobile-closed';
    }

    if (toggleBtn) {
        toggleBtn.addEventListener('click', function() {
            const isMobile = window.innerWidth < 1024;
            if (isMobile) {
                if (sidebar.dataset.state === 'mobile-open') {
                    closeMobileSidebar();
                } else {
                    openMobileSidebar();
                }
            } else {
                const isCollapsed = sidebar.classList.contains('w-18');
                localStorage.setItem('sidebarCollapsed', !isCollapsed);
                updateSidebarState();
            }
        });
    }

    if (overlay) {
        overlay.addEventListener('click', closeMobileSidebar);
    }

    window.addEventListener('resize', updateSidebarState);
    updateSidebarState();
});
</script>
