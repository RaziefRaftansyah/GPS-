<nav style="padding: 18px 16px 0; background: linear-gradient(180deg, #f8f2e8 0%, #f3f4f6 100%);">
    <div style="max-width: 1120px; margin: 0 auto;">
        <div style="position: relative; display: flex; align-items: center; justify-content: space-between; gap: 16px; padding: 14px 18px; border: 1px solid rgba(106, 65, 45, 0.12); border-radius: 999px; background: rgba(255, 250, 242, 0.92); box-shadow: 0 16px 40px rgba(59, 36, 24, 0.08); backdrop-filter: blur(12px);">
            <a href="{{ route('dashboard') }}" style="display: inline-flex; align-items: center; gap: 12px; color: #3b2418; text-decoration: none; font-weight: 800; letter-spacing: 0.04em; text-transform: uppercase;">
                <span style="width: 40px; height: 40px; display: inline-grid; place-items: center; border-radius: 50%; color: #fff; background: linear-gradient(135deg, #6a412d, #b56a3b); font-size: 1.1rem;">K</span>
                <span>Kopi Keliling</span>
            </a>

            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="text-align: right;">
                    <div style="font-weight: 800; color: #3b2418;">{{ Auth::user()->name }}</div>
                    <div style="font-size: 0.86rem; color: #8a634b;">{{ Auth::user()->email }}</div>
                </div>

                <button
                    type="button"
                    id="dashboard-nav-toggle"
                    aria-expanded="false"
                    aria-controls="dashboard-nav-menu"
                    style="width: 46px; height: 46px; border: 1px solid rgba(106, 65, 45, 0.12); border-radius: 50%; background: rgba(255,255,255,0.8); cursor: pointer; color: #3b2418; display: inline-grid; place-items: center;"
                >
                    <span style="display: inline-block; width: 18px; height: 2px; background: currentColor; position: relative;">
                        <span style="position: absolute; left: 0; top: -6px; width: 18px; height: 2px; background: currentColor;"></span>
                        <span style="position: absolute; left: 0; top: 6px; width: 18px; height: 2px; background: currentColor;"></span>
                    </span>
                </button>
            </div>

            <div id="dashboard-nav-menu" style="display: none; position: absolute; top: calc(100% + 12px); right: 0; width: min(320px, calc(100vw - 32px)); padding: 14px; border-radius: 24px; border: 1px solid rgba(106, 65, 45, 0.12); background: rgba(255, 250, 242, 0.98); box-shadow: 0 18px 40px rgba(59, 36, 24, 0.12); z-index: 30;">
                <div style="padding: 8px 6px 12px;">
                    <div style="font-weight: 800; color: #3b2418;">{{ Auth::user()->name }}</div>
                    <div style="margin-top: 4px; font-size: 0.86rem; color: #8a634b;">{{ Auth::user()->email }}</div>
                </div>

                <div style="display: grid; gap: 8px;">
                    @if (request()->routeIs('profile.*'))
                        <a
                            href="#profile-info-panel"
                            style="padding: 12px 14px; border-radius: 18px; text-decoration: none; font-weight: 700; color: #6c5244; background: rgba(255,255,255,0.72);"
                        >
                            Pengaturan Profil
                        </a>
                        <a
                            href="#password-panel"
                            style="padding: 12px 14px; border-radius: 18px; text-decoration: none; font-weight: 700; color: #6c5244; background: rgba(255,255,255,0.72);"
                        >
                            Update Password
                        </a>
                        <a
                            href="#deactivate-panel"
                            style="padding: 12px 14px; border-radius: 18px; text-decoration: none; font-weight: 700; color: #7b2f2f; background: rgba(255, 243, 243, 0.92);"
                        >
                            Nonaktifkan Akun
                        </a>
                    @endif
                    <a
                        href="{{ route('dashboard') }}"
                        style="padding: 12px 14px; border-radius: 18px; text-decoration: none; font-weight: 700; {{ request()->routeIs('dashboard') ? 'background: linear-gradient(135deg, #6a412d, #b56a3b); color: #fff;' : 'color: #6c5244; background: rgba(255,255,255,0.72);' }}"
                    >
                        Dashboard
                    </a>
                    <a
                        href="{{ route('profile.edit') }}"
                        style="padding: 12px 14px; border-radius: 18px; text-decoration: none; font-weight: 700; {{ request()->routeIs('profile.*') ? 'background: linear-gradient(135deg, #6a412d, #b56a3b); color: #fff;' : 'color: #6c5244; background: rgba(255,255,255,0.72);' }}"
                    >
                        Profil
                    </a>
                    <a
                        href="{{ route('tracker.index') }}"
                        style="padding: 12px 14px; border-radius: 18px; text-decoration: none; font-weight: 700; color: #6c5244; background: rgba(255,255,255,0.72);"
                    >
                        Beranda
                    </a>
                    <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                        @csrf
                        <button
                            type="submit"
                            style="width: 100%; text-align: left; padding: 12px 14px; border: 0; border-radius: 18px; background: rgba(59, 36, 24, 0.08); color: #6a412d; font-weight: 800; cursor: pointer;"
                        >
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</nav>

<script>
    (() => {
        const toggle = document.getElementById('dashboard-nav-toggle');
        const menu = document.getElementById('dashboard-nav-menu');

        if (!toggle || !menu) {
            return;
        }

        const closeMenu = () => {
            menu.style.display = 'none';
            toggle.setAttribute('aria-expanded', 'false');
        };

        toggle.addEventListener('click', () => {
            const isOpen = menu.style.display === 'block';
            menu.style.display = isOpen ? 'none' : 'block';
            toggle.setAttribute('aria-expanded', String(!isOpen));
        });

        document.addEventListener('click', (event) => {
            if (!menu.contains(event.target) && !toggle.contains(event.target)) {
                closeMenu();
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                closeMenu();
            }
        });
    })();
</script>
