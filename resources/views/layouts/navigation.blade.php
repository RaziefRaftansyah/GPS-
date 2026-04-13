<aside style="background: var(--sidebar-bg); color: #fff; padding: 26px 20px; display: flex; flex-direction: column; gap: 24px; min-height: 100vh; position: sticky; top: 0;">
    <div style="padding: 0 8px 18px; border-bottom: 1px solid var(--sidebar-border);">
        <div style="display: flex; align-items: center; gap: 12px;">
            <div style="width: 44px; height: 44px; border-radius: 14px; background: linear-gradient(135deg, #2563eb, #1d4ed8); display: grid; place-items: center; font-weight: 800; font-size: 1.1rem;">
                KK
            </div>
            <div>
                <strong style="display: block; font-size: 1rem;">Kopi Keliling</strong>
                <span style="display: block; margin-top: 4px; color: rgba(255,255,255,0.68); font-size: 0.85rem;">
                    {{ Auth::user()->isDriver() ? 'Driver Dashboard' : 'Owner Dashboard' }}
                </span>
            </div>
        </div>
    </div>

    <div style="padding: 0 8px;">
        <span style="display: block; color: rgba(255,255,255,0.52); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.08em;">Akun Aktif</span>
        <strong style="display: block; margin-top: 10px; font-size: 1rem;">{{ Auth::user()->name }}</strong>
        <span style="display: block; margin-top: 6px; color: rgba(255,255,255,0.68); font-size: 0.88rem;">{{ Auth::user()->email }}</span>
        <span style="display: inline-flex; margin-top: 12px; padding: 8px 12px; border-radius: 999px; background: rgba(255,255,255,0.08); color: rgba(255,255,255,0.9); font-size: 0.76rem; text-transform: uppercase; letter-spacing: 0.08em;">
            {{ Auth::user()->role }}
        </span>
    </div>

    <nav style="display: grid; gap: 8px;">
        <a
            href="{{ route('dashboard') }}"
            style="display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 14px 16px; border-radius: 14px; text-decoration: none; {{ request()->routeIs('dashboard') ? 'background: rgba(37, 99, 235, 0.18); color: #fff;' : 'background: transparent; color: rgba(255,255,255,0.82);' }}"
        >
            <span>{{ Auth::user()->isDriver() ? 'Dashboard Driver' : 'Dashboard Owner' }}</span>
            <span>›</span>
        </a>

        @if (! Auth::user()->isDriver())
            <a
                href="{{ route('dashboard.traccar') }}"
                style="display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 14px 16px; border-radius: 14px; text-decoration: none; {{ request()->routeIs('dashboard.traccar') ? 'background: rgba(37, 99, 235, 0.18); color: #fff;' : 'background: transparent; color: rgba(255,255,255,0.82);' }}"
            >
                <span>Monitoring Traccar</span>
                <span>›</span>
            </a>
        @endif

        <a
            href="{{ route('profile.edit') }}"
            style="display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 14px 16px; border-radius: 14px; text-decoration: none; {{ request()->routeIs('profile.*') ? 'background: rgba(37, 99, 235, 0.18); color: #fff;' : 'background: transparent; color: rgba(255,255,255,0.82);' }}"
        >
            <span>Profil</span>
            <span>›</span>
        </a>

        <a
            href="{{ route('tracker.index') }}"
            style="display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 14px 16px; border-radius: 14px; text-decoration: none; background: transparent; color: rgba(255,255,255,0.82);"
        >
            <span>Peta Publik</span>
            <span>›</span>
        </a>
    </nav>

    <div style="margin-top: auto; padding-top: 16px; border-top: 1px solid var(--sidebar-border); display: grid; gap: 12px;">
        <div style="padding: 14px 16px; border-radius: 16px; background: var(--sidebar-soft);">
            <span style="display: block; color: rgba(255,255,255,0.54); font-size: 0.74rem; text-transform: uppercase; letter-spacing: 0.08em;">Status Sistem</span>
            <strong style="display: block; margin-top: 8px; font-size: 0.98rem;">Panel aktif</strong>
            <span style="display: block; margin-top: 6px; color: rgba(255,255,255,0.68); font-size: 0.86rem;">Kelola driver, gerobak, dan tracking dari satu tempat.</span>
        </div>

        <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
            @csrf
            <button
                type="submit"
                style="width: 100%; padding: 14px 16px; border: 0; border-radius: 14px; background: rgba(220, 38, 38, 0.18); color: #fff; font-weight: 700; cursor: pointer;"
            >
                Logout
            </button>
        </form>
    </div>
</aside>
