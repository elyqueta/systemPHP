<!DOCTYPE html>
<html lang="pt-AO">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} — @yield('title', 'Backoffice')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-bg font-sans text-text antialiased">
    @include('admin.partials.sidebar')

    <div class="flex flex-col min-h-screen transition-all duration-200 lg:ml-64 ml-0" id="main-content">
        @include('admin.partials.topbar')

        <main class="flex-1 p-4 sm:p-6 md:p-8">
            @yield('content')
        </main>

        @include('admin.partials.footer')
    </div>

    <div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-30 hidden lg:hidden" onclick="closeMobileSidebar()"></div>

    <script>
    function closeMobileSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        sidebar.classList.remove('mobile-open');
        overlay.classList.add('hidden');
    }

    function openMobileSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        sidebar.classList.add('mobile-open');
        overlay.classList.remove('hidden');
    }

    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('main-content');
        const toggleBtn = document.getElementById('sidebar-toggle');
        const overlay = document.getElementById('sidebar-overlay');

        function updateSidebarState() {
            const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
            if (window.innerWidth < 1024) {
                sidebar.classList.remove('w-64', 'collapsed', 'w-18');
                sidebar.classList.add('mobile-closed');
                mainContent.classList.remove('ml-64', 'ml-18');
                mainContent.classList.add('ml-0');
                if (!sidebar.classList.contains('mobile-open')) {
                    overlay.classList.add('hidden');
                }
            } else {
                sidebar.classList.remove('mobile-closed', 'mobile-open');
                overlay.classList.add('hidden');
                if (isCollapsed) {
                    sidebar.classList.remove('w-64');
                    sidebar.classList.add('w-18');
                    mainContent.classList.remove('ml-64');
                    mainContent.classList.add('ml-18');
                } else {
                    sidebar.classList.remove('w-18');
                    sidebar.classList.add('w-64');
                    mainContent.classList.remove('ml-18');
                    mainContent.classList.add('ml-64');
                }
            }
        }

        if (toggleBtn) {
            toggleBtn.addEventListener('click', function() {
                if (window.innerWidth < 1024) {
                    if (sidebar.classList.contains('mobile-open')) {
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

        window.addEventListener('resize', updateSidebarState);
        updateSidebarState();
    });
    </script>
    @stack('scripts')
</body>
</html>
