<div class="clinic-sidebar-overlay" data-sidebar-overlay></div>

<aside class="clinic-sidebar" data-mobile-sidebar>
    <style>
        .clinic-sidebar-overlay {
            display: none;
        }

        .clinic-sidebar {
            color: #fff;
            padding: 28px 18px 20px;
            display: flex;
            flex-direction: column;
            gap: 24px;
            min-height: 100%;
            background: var(--sidebar-bg);
            position: relative;
            overflow: hidden;
        }

        .clinic-sidebar::before {
            content: "";
            position: absolute;
            inset: auto -30% -10% auto;
            width: 240px;
            height: 240px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.18) 0%, rgba(255, 255, 255, 0) 70%);
            pointer-events: none;
        }

        .clinic-brand {
            padding: 6px 14px 10px;
            flex-shrink: 0;
        }

        .clinic-brand strong {
            display: block;
            font-size: 3rem;
            letter-spacing: 0.12em;
            font-weight: 300;
        }

        .clinic-brand span {
            display: block;
            margin-top: 8px;
            color: rgba(255, 255, 255, 0.78);
            font-size: 0.88rem;
        }

        .clinic-account {
            margin: 0 6px;
            padding: 16px 18px;
            border-radius: 24px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid var(--sidebar-border);
            backdrop-filter: blur(12px);
            flex-shrink: 0;
        }

        .clinic-account small,
        .clinic-account strong,
        .clinic-account span {
            display: block;
        }

        .clinic-account small {
            color: rgba(255, 255, 255, 0.66);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-size: 0.72rem;
        }

        .clinic-account strong {
            margin-top: 10px;
            font-size: 1.02rem;
        }

        .clinic-account span {
            margin-top: 6px;
            color: rgba(255, 255, 255, 0.82);
            font-size: 0.88rem;
            word-break: break-word;
        }

        .clinic-role-pill {
            display: inline-flex;
            margin-top: 12px;
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.16);
            color: rgba(255, 255, 255, 0.94);
            font-size: 0.76rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .clinic-nav {
            display: grid;
            gap: 10px;
            padding: 0 6px;
            flex-shrink: 0;
        }

        .clinic-nav-link {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 16px;
            border-radius: 16px;
            text-decoration: none;
            color: rgba(255, 255, 255, 0.88);
            transition: background 0.2s ease, transform 0.2s ease;
        }

        .clinic-nav-link:hover {
            background: rgba(255, 255, 255, 0.12);
            transform: translateX(2px);
        }

        .clinic-nav-link.is-active {
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.08);
        }

        .clinic-nav-manage {
            position: relative;
            display: grid;
            gap: 6px;
        }

        .clinic-nav-toggle-input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .clinic-nav-manage .clinic-nav-link {
            margin: 0;
            width: 100%;
            padding-right: 46px;
        }

        .clinic-nav-arrow-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            width: 26px;
            height: 26px;
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.24);
            background: rgba(255, 255, 255, 0.08);
            color: rgba(255, 255, 255, 0.9);
            cursor: pointer;
            display: inline-grid;
            place-items: center;
            transition: background 0.2s ease, transform 0.2s ease;
            z-index: 1;
        }

        .clinic-nav-arrow-btn:hover {
            background: rgba(255, 255, 255, 0.18);
        }

        .clinic-nav-toggle-input:checked ~ .clinic-nav-arrow-btn {
            background: rgba(255, 255, 255, 0.22);
        }

        .clinic-nav-arrow-btn .clinic-nav-caret {
            font-size: 0.76rem;
            line-height: 1;
            transition: transform 0.2s ease;
        }

        .clinic-nav-toggle-input:checked ~ .clinic-nav-arrow-btn .clinic-nav-caret {
            transform: rotate(180deg);
        }

        .clinic-nav-submenu {
            display: none;
            gap: 6px;
            width: 100%;
            padding: 0 10px 6px;
            box-sizing: border-box;
        }

        .clinic-nav-toggle-input:checked ~ .clinic-nav-submenu {
            display: grid;
        }

        .clinic-nav-sub-link {
            text-decoration: none;
            color: rgba(255, 255, 255, 0.86);
            padding: 10px 12px;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.08);
            font-size: 0.9rem;
            transition: background 0.2s ease;
        }

        .clinic-nav-sub-link:hover {
            background: rgba(255, 255, 255, 0.16);
        }

        .clinic-nav-sub-link.is-active {
            background: rgba(255, 255, 255, 0.22);
            color: #fff;
            font-weight: 700;
        }

        .clinic-nav-icon {
            width: 22px;
            height: 22px;
            border-radius: 7px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            display: inline-grid;
            place-items: center;
            font-size: 0.74rem;
            flex-shrink: 0;
        }

        .clinic-sidebar-footer {
            margin-top: auto;
            padding: 18px 10px 0;
            border-top: 1px solid var(--sidebar-border);
            display: grid;
            gap: 14px;
            flex-shrink: 0;
        }

        .clinic-pro-card {
            padding: 18px;
            border-radius: 28px;
            background: linear-gradient(180deg, rgba(255, 249, 241, 0.14) 0%, rgba(181, 106, 59, 0.2) 100%);
            border: 1px solid rgba(255, 255, 255, 0.18);
        }

        .clinic-pro-card strong,
        .clinic-pro-card span {
            display: block;
        }

        .clinic-pro-card span {
            margin-top: 8px;
            color: rgba(255, 255, 255, 0.78);
            font-size: 0.88rem;
            line-height: 1.5;
        }

        .clinic-logout {
            width: 100%;
            padding: 14px 16px;
            border: 1px solid rgba(255, 255, 255, 0.28);
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.08);
            color: #fff;
            font-weight: 700;
            cursor: pointer;
        }

        @media (max-width: 1024px) {
            .clinic-sidebar-overlay {
                position: fixed;
                inset: 0;
                background: rgba(59, 36, 24, 0.34);
                backdrop-filter: blur(4px);
                z-index: 39;
                opacity: 0;
                pointer-events: none;
                transition: opacity 0.2s ease;
                display: block;
            }

            .clinic-sidebar-overlay.is-open {
                opacity: 1;
                pointer-events: auto;
            }

            .clinic-sidebar {
                min-height: auto;
                position: fixed;
                inset: 12px auto 12px 12px;
                width: min(320px, calc(100vw - 24px));
                max-width: calc(100vw - 24px);
                border-radius: 28px;
                z-index: 40;
                transform: translateX(calc(-100% - 18px));
                transition: transform 0.24s ease;
                box-shadow: var(--shadow-lg);
                border: 1px solid rgba(255, 249, 241, 0.12);
                overflow-y: auto;
                gap: 18px;
            }

            .clinic-sidebar.is-open {
                transform: translateX(0);
            }

            .clinic-brand strong {
                font-size: 2.2rem;
            }

            .clinic-sidebar-footer {
                margin-top: 0;
                padding-top: 16px;
            }

        }

        .clinic-sidebar-close {
            display: none;
        }

        @media (max-width: 1024px) {
            .clinic-sidebar-close {
                display: inline-grid;
                width: 40px;
                height: 40px;
                border-radius: 50%;
                border: 1px solid rgba(255, 255, 255, 0.24);
                background: rgba(255, 255, 255, 0.08);
                color: #fff;
                cursor: pointer;
                flex-shrink: 0;
                position: absolute;
                top: 18px;
                right: 18px;
                z-index: 2;
                place-items: center;
            }
        }
    </style>

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

        <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
            @csrf
            <button type="submit" class="clinic-logout">Logout</button>
        </form>
    </div>
</aside>
