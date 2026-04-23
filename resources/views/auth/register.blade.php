<x-guest-layout>
    @push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/auth/register.css') }}">
@endpush

    <div class="coffee-register-shell">
        <section class="coffee-register-panel">
            <div class="coffee-owl-wrap" aria-hidden="true">
                <div class="coffee-owl" id="coffee-owl">
                    <div class="coffee-owl-head">
                        <span class="coffee-eye left"></span>
                        <span class="coffee-eye right"></span>
                        <span class="coffee-beak"></span>
                    </div>
                    <span class="coffee-wing left"></span>
                    <span class="coffee-wing right"></span>
                </div>
            </div>

            <div class="coffee-register-card">
                <h2>Create your account</h2>
                <p>Isi data berikut untuk membuat akun user baru di Kopi Keliling.</p>

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="coffee-form-group">
                        <label class="coffee-label" for="name">Nama</label>
                        <div class="coffee-input-wrap">
                            <span class="coffee-input-icon">☺</span>
                            <input
                                id="name"
                                class="coffee-input"
                                type="text"
                                name="name"
                                value="{{ old('name') }}"
                                required
                                autofocus
                                autocomplete="name"
                            >
                        </div>
                        @foreach ((array) $errors->get('name') as $message)
                            <div class="coffee-error">{{ $message }}</div>
                        @endforeach
                    </div>

                    <div class="coffee-form-group">
                        <label class="coffee-label" for="email">Email</label>
                        <div class="coffee-input-wrap">
                            <span class="coffee-input-icon">@</span>
                            <input
                                id="email"
                                class="coffee-input"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                required
                                autocomplete="username"
                            >
                        </div>
                        @foreach ((array) $errors->get('email') as $message)
                            <div class="coffee-error">{{ $message }}</div>
                        @endforeach
                    </div>

                    <div class="coffee-form-group">
                        <label class="coffee-label" for="password">Password</label>
                        <div class="coffee-input-wrap">
                            <span class="coffee-input-icon">•</span>
                            <input
                                id="password"
                                class="coffee-input"
                                type="password"
                                name="password"
                                required
                                autocomplete="new-password"
                            >
                        </div>
                        @foreach ((array) $errors->get('password') as $message)
                            <div class="coffee-error">{{ $message }}</div>
                        @endforeach
                    </div>

                    <div class="coffee-form-group">
                        <label class="coffee-label" for="password_confirmation">Konfirmasi Password</label>
                        <div class="coffee-input-wrap">
                            <span class="coffee-input-icon">•</span>
                            <input
                                id="password_confirmation"
                                class="coffee-input"
                                type="password"
                                name="password_confirmation"
                                required
                                autocomplete="new-password"
                            >
                        </div>
                        @foreach ((array) $errors->get('password_confirmation') as $message)
                            <div class="coffee-error">{{ $message }}</div>
                        @endforeach
                    </div>

                    <div class="coffee-actions">
                        <div class="coffee-actions-links">
                            <a class="coffee-link" href="{{ route('login') }}">Sudah punya akun?</a>
                        </div>

                        <div class="coffee-actions-cta">
                            <a class="coffee-back-home" href="{{ route('tracker.index') }}">Kembali ke Beranda</a>
                            <button type="submit" class="coffee-submit">Register</button>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </div>

    @push('scripts')
    <script src="{{ asset('js/pages/auth/register.js') }}"></script>
@endpush
</x-guest-layout>
