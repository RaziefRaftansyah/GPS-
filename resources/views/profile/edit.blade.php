<x-app-layout>
    @push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/profile-edit.css') }}">
@endpush

    <x-slot name="header">
        <div>
            <p class="profile-header-kicker">
                Profil User
            </p>
            <h2 class="profile-header-title">
                Atur akun kamu
            </h2>
        </div>
    </x-slot>

    <div class="profile-page-shell">
        <div class="profile-page-container">
            <section class="profile-focus-layout">
                <article class="profile-focus-card">
                    <p class="profile-status-kicker">
                        Status Akun
                    </p>
                    <h3 class="profile-status-name">{{ $user->name }}</h3>
                    <p class="profile-status-email">{{ $user->email }}</p>

                    <div class="profile-status-grid">
                        <div class="profile-status-item">
                            <strong class="profile-status-label">Status</strong>
                            <span class="profile-status-value">
                                {{ $user->is_active ? 'Akun aktif dan bisa dipakai login.' : 'Akun sedang dinonaktifkan.' }}
                            </span>
                        </div>
                        <div class="profile-status-item">
                            <strong class="profile-status-label">Verifikasi Email</strong>
                            <span class="profile-status-value">
                                {{ $user->email_verified_at ? 'Email sudah terverifikasi.' : 'Email belum terverifikasi.' }}
                            </span>
                        </div>
                        <div class="profile-status-item">
                            <strong class="profile-status-label">Member Sejak</strong>
                            <span class="profile-status-value">{{ $user->created_at?->translatedFormat('d F Y') }}</span>
                        </div>
                    </div>

                    <div class="profile-status-actions">
                        <a href="#profile-info-panel" class="profile-action-btn is-primary">
                            Edit nama &amp; email
                        </a>
                        <a href="#password-panel" class="profile-action-btn">
                            Ganti password
                        </a>
                        <a href="#deactivate-panel" class="profile-action-btn is-danger">
                            Nonaktifkan akun
                        </a>
                    </div>
                </article>
            </section>
        </div>
    </div>

    <section id="profile-info-panel" class="profile-modal">
        <div class="profile-modal-dialog profile-modal-dialog-wide">
            <div class="profile-modal-header">
                <div>
                    <p class="profile-modal-kicker">Pengaturan Profil</p>
                    <h3 class="profile-modal-title">Edit data akun</h3>
                </div>
                <a href="{{ route('profile.edit') }}" class="profile-modal-close" aria-label="Tutup panel">&times;</a>
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
                    <p class="profile-modal-kicker">Keamanan Akun</p>
                    <h3 class="profile-modal-title">Update password</h3>
                </div>
                <a href="{{ route('profile.edit') }}" class="profile-modal-close" aria-label="Tutup panel">&times;</a>
            </div>
            @include('profile.partials.update-password-form')
        </div>
    </section>

    <section id="deactivate-panel" class="profile-modal">
        <div class="profile-modal-dialog profile-modal-dialog-danger">
            <div class="profile-modal-header">
                <div>
                    <p class="profile-modal-kicker">Status Akun</p>
                    <h3 class="profile-modal-title">Nonaktifkan akun</h3>
                </div>
                <a href="{{ route('profile.edit') }}" class="profile-modal-close" aria-label="Tutup panel">&times;</a>
            </div>
            @include('profile.partials.deactivate-account-form')
        </div>
    </section>
</x-app-layout>
