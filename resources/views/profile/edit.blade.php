<x-app-layout>
    <style>
        .profile-focus-layout {
            display: grid;
            place-items: center;
        }

        .profile-focus-card {
            width: min(860px, 100%);
            padding: 26px;
            border-radius: 26px;
            border: 1px solid rgba(106, 65, 45, 0.12);
            background: rgba(255, 250, 242, 0.92);
            box-shadow: 0 18px 40px rgba(59, 36, 24, 0.08);
        }

        .profile-modal {
            position: fixed;
            inset: 0;
            display: none;
            padding: 32px 16px;
            background: rgba(59, 36, 24, 0.34);
            backdrop-filter: blur(6px);
            z-index: 50;
            overflow: auto;
        }

        .profile-modal:target {
            display: block;
        }

        .profile-modal-dialog {
            width: min(760px, 100%);
            margin: 0 auto;
            padding: 26px;
            border-radius: 28px;
            border: 1px solid rgba(106, 65, 45, 0.12);
            background: rgba(255, 250, 242, 0.98);
            box-shadow: 0 22px 60px rgba(59, 36, 24, 0.16);
        }

        .profile-modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            margin-bottom: 18px;
        }

        .profile-modal-close {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: rgba(59, 36, 24, 0.08);
            color: #6a412d;
            text-decoration: none;
            font-size: 1.4rem;
            font-weight: 700;
        }

        .profile-edit-showcase {
            display: grid;
            grid-template-columns: 0.9fr 1.1fr;
            gap: 34px;
            align-items: stretch;
        }

        .profile-edit-visual {
            position: relative;
            min-height: 430px;
            padding: 26px;
            border-radius: 30px;
            overflow: hidden;
            background:
                radial-gradient(circle at 25% 20%, rgba(255,255,255,0.2), transparent 20%),
                linear-gradient(155deg, #bf7846 0%, #935937 46%, #6f422d 100%);
            color: #fffaf4;
        }

        .profile-edit-visual::before,
        .profile-edit-visual::after {
            content: "";
            position: absolute;
            border-radius: 999px;
            background: rgba(255, 244, 231, 0.2);
        }

        .profile-edit-visual::before {
            width: 180px;
            height: 180px;
            right: -40px;
            top: -30px;
        }

        .profile-edit-visual::after {
            width: 220px;
            height: 220px;
            left: -70px;
            bottom: -90px;
        }

        .profile-edit-badge {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            border-radius: 999px;
            background: rgba(255,255,255,0.14);
            font-size: 0.78rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .profile-edit-visual h3 {
            margin: 18px 0 12px;
            font-family: "Cormorant Garamond", serif;
            font-size: clamp(2.5rem, 4.6vw, 3.9rem);
            line-height: 0.92;
            max-width: 8ch;
        }

        .profile-edit-visual p {
            margin: 0;
            line-height: 1.75;
            font-size: 0.98rem;
            color: rgba(255, 250, 244, 0.88);
            max-width: 30ch;
        }

        .profile-edit-illustration {
            position: absolute;
            left: 28px;
            right: 28px;
            bottom: 26px;
            height: 128px;
            border-radius: 28px;
            background: rgba(255,255,255,0.12);
            border: 1px solid rgba(255,255,255,0.14);
            overflow: hidden;
            backdrop-filter: blur(10px);
        }

        .profile-edit-illustration .cup {
            position: absolute;
            left: 32px;
            bottom: 20px;
            width: 108px;
            height: 78px;
            border-radius: 0 0 28px 28px;
            background: linear-gradient(180deg, #fff4e8, #efd3b0);
            box-shadow: 0 14px 26px rgba(59, 36, 24, 0.18);
        }

        .profile-edit-illustration .cup::before {
            content: "";
            position: absolute;
            left: 18px;
            right: 18px;
            top: 16px;
            height: 18px;
            border-radius: 999px;
            background: rgba(106, 65, 45, 0.22);
        }

        .profile-edit-illustration .cup::after {
            content: "";
            position: absolute;
            right: -18px;
            top: 18px;
            width: 28px;
            height: 34px;
            border: 7px solid #efd3b0;
            border-left: 0;
            border-radius: 0 20px 20px 0;
        }

        .profile-edit-illustration .phone {
            position: absolute;
            right: 34px;
            bottom: 14px;
            width: 86px;
            height: 120px;
            border-radius: 22px;
            background: linear-gradient(180deg, #4a2d20, #2e1b11);
            box-shadow: 0 18px 28px rgba(46, 27, 17, 0.28);
            transform: rotate(-10deg);
        }

        .profile-edit-illustration .phone::before {
            content: "";
            position: absolute;
            inset: 12px;
            border-radius: 16px;
            background:
                radial-gradient(circle at 50% 24%, rgba(181, 106, 59, 0.58), transparent 18%),
                linear-gradient(180deg, #fffaf4 0%, #f3e1ca 100%);
        }

        .profile-edit-illustration .dot {
            position: absolute;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #fff1dd;
        }

        .profile-edit-illustration .dot.one {
            left: 160px;
            top: 26px;
        }

        .profile-edit-illustration .dot.two {
            left: 184px;
            top: 48px;
            width: 10px;
            height: 10px;
            background: rgba(255, 241, 221, 0.72);
        }

        .profile-edit-form-shell {
            padding: 20px 0 6px;
        }

        .profile-edit-kicker {
            margin: 0;
            color: #9a6b4d;
            font-size: 0.78rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .profile-edit-form-shell h3 {
            margin: 0;
            color: #3b2418;
            font-family: "Cormorant Garamond", serif;
            font-size: clamp(2.8rem, 5vw, 4.2rem);
            line-height: 0.92;
            max-width: 9ch;
        }

        .profile-edit-form-shell p {
            margin: 12px 0 0;
            color: #6c5244;
            line-height: 1.8;
            max-width: 26ch;
        }

        .profile-edit-form-card {
            margin-top: 34px;
            padding: 0;
        }

        .profile-edit-label {
            display: block;
            margin-bottom: 10px;
            color: #5a3726;
            font-size: 0.95rem;
            font-weight: 800;
        }

        .profile-edit-input {
            box-sizing: border-box;
            width: 100%;
            padding: 17px 18px;
            border-radius: 22px;
            border: 1px solid rgba(181, 106, 59, 0.24);
            background: rgba(255,255,255,0.84);
            color: #3b2418;
            font-size: 1rem;
            outline: none;
            transition: border-color 180ms ease, box-shadow 180ms ease, transform 180ms ease;
        }

        .profile-edit-input:focus {
            border-color: #b56a3b;
            box-shadow: 0 0 0 4px rgba(181, 106, 59, 0.16);
            transform: translateY(-1px);
        }

        .profile-edit-submit {
            border: 0;
            padding: 14px 22px;
            border-radius: 999px;
            background: linear-gradient(135deg, #6a412d, #b56a3b);
            color: #fff;
            font-weight: 800;
            cursor: pointer;
            box-shadow: 0 16px 28px rgba(106, 65, 45, 0.2);
            transition: transform 180ms ease, box-shadow 180ms ease;
        }

        .profile-edit-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 20px 32px rgba(106, 65, 45, 0.24);
        }

        .profile-edit-error {
            margin: 8px 0 0;
            color: #9f2f20;
            font-weight: 700;
        }

        @media (max-width: 860px) {
            .profile-edit-showcase {
                grid-template-columns: 1fr;
            }

            .profile-edit-visual {
                min-height: 320px;
            }

            .profile-edit-form-shell {
                padding-top: 4px;
            }
        }

        @media (max-width: 640px) {
            .profile-edit-visual {
                min-height: 300px;
                padding: 22px;
            }

            .profile-edit-illustration {
                left: 20px;
                right: 20px;
                bottom: 20px;
            }

            .profile-edit-illustration .cup {
                left: 22px;
                width: 92px;
            }

            .profile-edit-illustration .phone {
                right: 24px;
            }
        }
    </style>

    <x-slot name="header">
        <div>
            <p style="margin: 0; font-size: 0.8rem; letter-spacing: 0.08em; text-transform: uppercase; color: #9a6b4d;">
                Profil User
            </p>
            <h2 style="margin: 6px 0 0; font-size: 1.8rem; color: #3b2418;">
                Atur akun kamu
            </h2>
        </div>
    </x-slot>

    <div style="padding: 32px 0 48px; background: linear-gradient(180deg, #f8f2e8 0%, #f3f4f6 100%); min-height: 100vh;">
        <div style="max-width: 1120px; margin: 0 auto; padding: 0 16px;">
            @if (session('profile_notice'))
                <div style="margin-bottom: 18px; padding: 16px 18px; border-radius: 18px; background: rgba(159, 47, 32, 0.08); color: #9f2f20; border: 1px solid rgba(159, 47, 32, 0.14); font-weight: 700;">
                    {{ session('profile_notice') }}
                </div>
            @endif

            <section class="profile-focus-layout">
                <article class="profile-focus-card">
                    <p style="margin: 0; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.08em; color: #9a6b4d;">
                        Status Akun
                    </p>
                    <h3 style="margin: 12px 0 8px; font-size: 2rem; color: #3b2418;">{{ $user->name }}</h3>
                    <p style="margin: 0; color: #6c5244;">{{ $user->email }}</p>

                    <div style="margin-top: 22px; display: grid; gap: 12px;">
                        <div style="padding: 16px; border-radius: 18px; background: rgba(255,255,255,0.72); border: 1px solid rgba(106, 65, 45, 0.1);">
                            <strong style="display: block; margin-bottom: 6px; color: #5a3726;">Status</strong>
                            <span style="color: #6c5244;">
                                {{ $user->is_active ? 'Akun aktif dan bisa dipakai login.' : 'Akun sedang dinonaktifkan.' }}
                            </span>
                        </div>
                        <div style="padding: 16px; border-radius: 18px; background: rgba(255,255,255,0.72); border: 1px solid rgba(106, 65, 45, 0.1);">
                            <strong style="display: block; margin-bottom: 6px; color: #5a3726;">Verifikasi Email</strong>
                            <span style="color: #6c5244;">
                                {{ $user->email_verified_at ? 'Email sudah terverifikasi.' : 'Email belum terverifikasi.' }}
                            </span>
                        </div>
                        <div style="padding: 16px; border-radius: 18px; background: rgba(255,255,255,0.72); border: 1px solid rgba(106, 65, 45, 0.1);">
                            <strong style="display: block; margin-bottom: 6px; color: #5a3726;">Member Sejak</strong>
                            <span style="color: #6c5244;">{{ $user->created_at?->translatedFormat('d F Y') }}</span>
                        </div>
                    </div>
                </article>
            </section>
        </div>
    </div>

    <section id="profile-info-panel" class="profile-modal">
        <div class="profile-modal-dialog" style="width: min(980px, 100%);">
            <div class="profile-modal-header">
                <div>
                    <p style="margin: 0; font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.08em; color: #9a6b4d;">Pengaturan Profil</p>
                    <h3 style="margin: 8px 0 0; font-size: 2rem; color: #3b2418;">Edit data akun</h3>
                </div>
                <a href="{{ route('profile.edit') }}" class="profile-modal-close" aria-label="Tutup panel">×</a>
            </div>

            <div class="profile-edit-showcase">
                <aside class="profile-edit-visual">
                    <span class="profile-edit-badge">Coffee Profile</span>
                    <h3>Edit profil dengan tampilan yang lebih rapi.</h3>
                    <p>
                        Panel ini saya rapikan supaya terasa ringan, modern, dan tetap
                        nyambung dengan warna khas Kopi Tracker.
                    </p>

                    <div class="profile-edit-illustration" aria-hidden="true">
                        <span class="cup"></span>
                        <span class="phone"></span>
                        <span class="dot one"></span>
                        <span class="dot two"></span>
                    </div>
                </aside>

                <div class="profile-edit-form-shell">
                    <p class="profile-edit-kicker">Form Akun</p>
                    <h3>Atur identitas akunmu.</h3>
                    <p>Perbarui nama dan email supaya data user tetap akurat di dashboard Kopi Keliling.</p>
                    <div class="profile-edit-form-card">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="password-panel" class="profile-modal">
        <div class="profile-modal-dialog">
            <div class="profile-modal-header">
                <div>
                    <p style="margin: 0; font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.08em; color: #9a6b4d;">Keamanan Akun</p>
                    <h3 style="margin: 8px 0 0; font-size: 2rem; color: #3b2418;">Update password</h3>
                </div>
                <a href="{{ route('profile.edit') }}" class="profile-modal-close" aria-label="Tutup panel">×</a>
            </div>
            @include('profile.partials.update-password-form')
        </div>
    </section>

    <section id="deactivate-panel" class="profile-modal">
        <div class="profile-modal-dialog" style="background: rgba(255, 245, 245, 0.98);">
            <div class="profile-modal-header">
                <div>
                    <p style="margin: 0; font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.08em; color: #9a6b4d;">Status Akun</p>
                    <h3 style="margin: 8px 0 0; font-size: 2rem; color: #3b2418;">Nonaktifkan akun</h3>
                </div>
                <a href="{{ route('profile.edit') }}" class="profile-modal-close" aria-label="Tutup panel">×</a>
            </div>
            @include('profile.partials.deactivate-account-form')
        </div>
    </section>
</x-app-layout>
