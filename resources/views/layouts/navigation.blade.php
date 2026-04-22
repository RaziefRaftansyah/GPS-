<div class="clinic-sidebar-overlay" data-sidebar-overlay></div>

<aside class="clinic-sidebar" data-mobile-sidebar>


    <button type="button" class="clinic-sidebar-close" data-sidebar-close aria-label="Tutup navigasi">&times;</button>

    <div class="clinic-brand">
        <strong>KOPI</strong>
        <span>{{ Auth::user()->isDriver() ? 'Driver Dashboard' : 'Owner Dashboard' }}</span>
    </div>

    <div class="clinic-account">
        <small>Akun Aktif</small>
        <strong>{{ Auth::user()->name }}</strong>
        <span>{{ Auth::user()->email }}</span>
        <div class="clinic-role-pill">{{ Auth::user()->role }}</div>
    </div>

    <nav class="clinic-nav">
        <a href="{{ route('dashboard') }}" class="clinic-nav-link {{ request()->routeIs('dashboard') ? 'is-active' : '' }}">
            <span class="clinic-nav-icon">D</span>
            <span>{{ Auth::user()->isDriver() ? 'Dashboard Driver' : 'Dashboard Owner' }}</span>
        </a>

        @if (! Auth::user()->isDriver())
            <div class="clinic-nav-manage" data-manage-menu>
                <input
                    id="manage-nav-toggle"
                    type="checkbox"
                    class="clinic-nav-toggle-input"
                    @checked(request()->routeIs('dashboard.manage.*'))
                >

                <a href="{{ route('dashboard.manage.index') }}" class="clinic-nav-link {{ request()->routeIs('dashboard.manage.*') ? 'is-active' : '' }}">
                    <span class="clinic-nav-icon">U</span>
                    <span>Kelola Unit & Driver</span>
                </a>

                <label
                    for="manage-nav-toggle"
                    class="clinic-nav-arrow-btn"
                    aria-label="Buka pilihan unit dan driver"
                >
                    <span class="clinic-nav-caret">&#9662;</span>
                </label>

                <div class="clinic-nav-submenu" data-manage-submenu>
                    <a href="{{ route('dashboard.manage.index', ['focus' => 'driver']) }}#driver-input" class="clinic-nav-sub-link {{ request()->routeIs('dashboard.manage.*') && request('focus') === 'driver' ? 'is-active' : '' }}">
                        Input Driver
                    </a>
                    <a href="{{ route('dashboard.manage.index', ['focus' => 'unit']) }}#unit-input" class="clinic-nav-sub-link {{ request()->routeIs('dashboard.manage.*') && request('focus') === 'unit' ? 'is-active' : '' }}">
                        Input Unit
                    </a>
                </div>
            </div>

            <a href="{{ route('dashboard.assignments.index') }}" class="clinic-nav-link {{ request()->routeIs('dashboard.assignments.*') ? 'is-active' : '' }}">
                <span class="clinic-nav-icon">A</span>
                <span>Assignment</span>
            </a>

            <a href="{{ route('dashboard.menus.index') }}" class="clinic-nav-link {{ request()->routeIs('dashboard.menus.*') ? 'is-active' : '' }}">
                <span class="clinic-nav-icon">K</span>
                <span>Katalog Menu</span>
            </a>

            <a href="{{ route('dashboard.traccar') }}" class="clinic-nav-link {{ request()->routeIs('dashboard.traccar') ? 'is-active' : '' }}">
                <span class="clinic-nav-icon">T</span>
                <span>Monitoring Traccar</span>
            </a>
        @endif

        <a href="{{ route('profile.edit') }}" class="clinic-nav-link {{ request()->routeIs('profile.*') ? 'is-active' : '' }}">
            <span class="clinic-nav-icon">P</span>
            <span>Profil</span>
        </a>

        <a href="{{ route('tracker.index') }}" class="clinic-nav-link">
            <span class="clinic-nav-icon">M</span>
            <span>Peta Publik</span>
        </a>
    </nav>

    <div class="clinic-sidebar-footer">
        <div class="clinic-pro-card">
            <strong>Panel aktif</strong>
            <span>Kelola driver, gerobak, assignment, dan tracking GPS dari satu tempat tanpa mengubah alur kerja.</span>
        </div>

        <form method="POST" action="{{ route('logout') }}" class="clinic-logout-form">
            @csrf
            <button type="submit" class="clinic-logout">Logout</button>
        </form>
    </div>
</aside>
