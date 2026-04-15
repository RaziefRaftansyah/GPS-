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

        .clinic-action-dropdown {
            margin: 2px 6px 0;
            border-radius: 22px;
            background: rgba(255, 249, 241, 0.1);
            border: 1px solid rgba(255, 249, 241, 0.18);
            backdrop-filter: blur(10px);
            overflow: hidden;
            flex-shrink: 0;
        }

        .clinic-action-summary {
            list-style: none;
            cursor: pointer;
            padding: 16px 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .clinic-action-summary::-webkit-details-marker {
            display: none;
        }

        .clinic-action-summary small,
        .clinic-action-summary strong {
            display: block;
        }

        .clinic-action-summary small {
            color: rgba(255, 249, 241, 0.72);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-size: 0.72rem;
        }

        .clinic-action-summary strong {
            margin-top: 6px;
            font-size: 1rem;
            line-height: 1.3;
        }

        .clinic-action-chevron {
            flex-shrink: 0;
            width: 34px;
            height: 34px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            background: rgba(255, 255, 255, 0.12);
            transition: transform 0.2s ease;
        }

        .clinic-action-dropdown[open] .clinic-action-chevron {
            transform: rotate(180deg);
        }

        .clinic-action-menu {
            display: grid;
            gap: 10px;
            padding: 0 16px 16px;
        }

        .clinic-action-item {
            padding: 14px;
            border-radius: 18px;
            background: rgba(255, 249, 241, 0.08);
            border: 1px solid rgba(255, 249, 241, 0.1);
        }

        .clinic-action-item small,
        .clinic-action-item strong,
        .clinic-action-item span {
            display: block;
        }

        .clinic-action-item small {
            color: rgba(255, 249, 241, 0.72);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-size: 0.7rem;
        }

        .clinic-action-item strong {
            margin-top: 8px;
            font-size: 0.96rem;
            line-height: 1.35;
        }

        .clinic-action-item span {
            margin-top: 8px;
            color: rgba(255, 249, 241, 0.8);
            font-size: 0.84rem;
            line-height: 1.45;
        }

        .clinic-action-button {
            width: 100%;
            margin-top: 12px;
            padding: 11px 14px;
            border: 0;
            border-radius: 999px;
            background: linear-gradient(135deg, rgba(255, 249, 241, 0.22) 0%, rgba(181, 106, 59, 0.3) 100%);
            color: #fff;
            font-weight: 700;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .clinic-action-button.is-secondary {
            background: linear-gradient(135deg, rgba(47, 107, 85, 0.92) 0%, rgba(77, 139, 115, 0.92) 100%);
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

            .clinic-action-dropdown {
                margin-top: 4px;
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

    <button type="button" class="clinic-sidebar-close" data-sidebar-close aria-label="Tutup navigasi">×</button>

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
            <a href="{{ route('dashboard.assignments.index') }}" class="clinic-nav-link {{ request()->routeIs('dashboard.assignments.*') ? 'is-active' : '' }}">
                <span class="clinic-nav-icon">A</span>
                <span>Assignment</span>
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

    @if (! Auth::user()->isDriver())
        <details class="clinic-action-dropdown" open>
            <summary class="clinic-action-summary">
                <div>
                    <small>Aksi Cepat</small>
                    <strong>Kelola unit dan driver</strong>
                </div>
                <span class="clinic-action-chevron">⌄</span>
            </summary>

            <div class="clinic-action-menu">
                <article class="clinic-action-item">
                    <small>Data Unit</small>
                    <strong>Tambah gerobak</strong>
                    <span>Buat master gerobak baru tanpa memenuhi area utama dashboard.</span>
                    <button
                        type="button"
                        class="clinic-action-button"
                        data-open-modal="unit-form-modal"
                        aria-expanded="{{ $errors->hasAny(['name', 'code', 'status', 'notes']) ? 'true' : 'false' }}"
                    >
                        Tambah Gerobak
                    </button>
                </article>

                <article class="clinic-action-item">
                    <small>Data Driver</small>
                    <strong>Buat akun driver</strong>
                    <span>Siapkan akun login dan <code>device_id</code> untuk driver dari sidebar.</span>
                    <button
                        type="button"
                        class="clinic-action-button"
                        data-open-modal="driver-form-modal"
                        aria-expanded="{{ $errors->hasAny(['email', 'device_id', 'password']) ? 'true' : 'false' }}"
                    >
                        Buat Akun Driver
                    </button>
                </article>

            </div>
        </details>
    @endif

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
