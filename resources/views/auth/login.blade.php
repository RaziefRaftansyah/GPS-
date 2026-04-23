<x-guest-layout>
    @push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/auth/login.css') }}">
@endpush

    <div class="coffee-login-shell">
        <section class="coffee-login-panel">
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

            <div class="coffee-login-card">
                <h2>Welcome back</h2>
                <p>Masukkan email dan password untuk lanjut ke dashboard Kopi Keliling.</p>

                @if (session('status'))
                    <div class="coffee-status">{{ session('status') }}</div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

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
                                autofocus
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
                                autocomplete="current-password"
                            >
                        </div>
                        @foreach ((array) $errors->get('password') as $message)
                            <div class="coffee-error">{{ $message }}</div>
                        @endforeach
                    </div>

                    <div class="coffee-meta">
                        <label for="remember_me" class="coffee-check">
                            <input id="remember_me" type="checkbox" name="remember">
                            <span>Remember me</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a class="coffee-link" href="{{ route('password.request') }}">
                                Forgot your password?
                            </a>
                        @endif
                    </div>

                    <div class="coffee-actions">
                        <div class="coffee-actions-links"></div>

                        <div class="coffee-actions-cta">
                            <a class="coffee-back-home" href="{{ route('tracker.index') }}">Kembali ke Beranda</a>
                            <button type="submit" class="coffee-submit">Log in</button>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </div>

    @push('scripts')
    <script src="{{ asset('js/pages/auth/login.js') }}"></script>
@endpush
</x-guest-layout>
