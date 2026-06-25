    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const tombolTema = document.getElementById('tombol-tema');
        const ikonTema = document.getElementById('ikon-tombol-tema');

        function perbaruiIkon() {
            if (!ikonTema) return;
            if (document.documentElement.classList.contains('dark')) {
                ikonTema.classList.replace('fa-moon', 'fa-sun');
            } else {
                ikonTema.classList.replace('fa-sun', 'fa-moon');
            }
        }
        perbaruiIkon();

        if (tombolTema) {
            tombolTema.addEventListener('click', () => {
                setTimeout(() => {
                    if (document.documentElement.classList.contains('dark')) {
                        document.documentElement.classList.remove('dark');
                        localStorage.setItem('theme', 'light');
                    } else {
                        document.documentElement.classList.add('dark');
                        localStorage.setItem('theme', 'dark');
                    }
                    perbaruiIkon();
                }, 150);
            });
        }

        // Kontrol Sidebar Seluler
        const sidebar = document.getElementById('sidebar');
        const backdrop = document.getElementById('sidebar-backdrop');
        const tombolMenuMobile = document.getElementById('tombol-menu-mobile');
        const tombolTutupSidebar = document.getElementById('tombol-tutup-sidebar');

        function bukaSidebar() {
            if (sidebar && backdrop) {
                sidebar.classList.add('active');
                backdrop.classList.replace('opacity-0', 'opacity-100');
                backdrop.classList.replace('pointer-events-none', 'pointer-events-auto');
                document.body.style.overflow = 'hidden';
            }
        }
        function tutupSidebar() {
            if (sidebar && backdrop) {
                sidebar.classList.remove('active');
                backdrop.classList.replace('opacity-100', 'opacity-0');
                backdrop.classList.replace('pointer-events-auto', 'pointer-events-none');
                document.body.style.overflow = '';
            }
        }

        if (tombolMenuMobile) tombolMenuMobile.addEventListener('click', bukaSidebar);
        if (tombolTutupSidebar) tombolTutupSidebar.addEventListener('click', tutupSidebar);
        if (backdrop) backdrop.addEventListener('click', tutupSidebar);
    });
    </script>
</body>
</html>
