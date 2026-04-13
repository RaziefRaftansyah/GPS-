<x-guest-layout>
    <style>
        .coffee-register-shell {
            width: min(520px, 100%);
            border-radius: 36px;
            overflow: hidden;
            box-shadow: 0 26px 80px rgba(59, 36, 24, 0.16);
            border: 1px solid rgba(106, 65, 45, 0.12);
            background: rgba(255, 250, 242, 0.92);
        }

        .coffee-register-panel {
            position: relative;
            padding: 34px 32px 30px;
            background:
                radial-gradient(circle at top center, rgba(181, 106, 59, 0.16), transparent 26%),
                linear-gradient(180deg, #fffaf4 0%, #f8f0e4 100%);
        }

        .coffee-owl-wrap {
            display: grid;
            justify-items: center;
            margin-bottom: 8px;
        }

        .coffee-owl {
            position: relative;
            width: 162px;
            height: 126px;
            animation: owl-bob 3s ease-in-out infinite;
        }

        .coffee-owl-head {
            position: absolute;
            left: 50%;
            top: 0;
            transform: translateX(-50%);
            width: 118px;
            height: 96px;
            border-radius: 34px 34px 28px 28px;
            background: linear-gradient(180deg, #8f5636 0%, #6a412d 62%, #4d2f20 100%);
            overflow: hidden;
            box-shadow: 0 14px 26px rgba(59, 36, 24, 0.22);
        }

        .coffee-owl-head::before,
        .coffee-owl-head::after {
            content: "";
            position: absolute;
            top: -10px;
            width: 24px;
            height: 28px;
            background: #4d2f20;
            clip-path: polygon(50% 0, 100% 100%, 0 100%);
        }

        .coffee-owl-head::before {
            left: 8px;
            transform: rotate(-12deg);
        }

        .coffee-owl-head::after {
            right: 8px;
            transform: rotate(12deg);
        }

        .coffee-eye {
            position: absolute;
            top: 28px;
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: #fff;
            box-shadow: inset 0 -2px 0 rgba(59, 36, 24, 0.12);
            overflow: hidden;
            animation: owl-blink 4.4s infinite;
        }

        .coffee-eye.left { left: 20px; }
        .coffee-eye.right { right: 20px; }

        .coffee-eye::after {
            content: "";
            position: absolute;
            top: 12px;
            left: 12px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #2e1b11;
            transition: transform 180ms ease;
        }

        .coffee-owl.is-name .coffee-eye::after {
            transform: translateX(-3px);
        }

        .coffee-owl.is-email .coffee-eye::after {
            transform: translateX(4px);
        }

        .coffee-owl.is-password .coffee-eye::after,
        .coffee-owl.is-password-confirmation .coffee-eye::after {
            transform: translateY(5px);
        }

        .coffee-beak {
            position: absolute;
            left: 50%;
            top: 58px;
            transform: translateX(-50%);
            width: 16px;
            height: 12px;
            background: #f2bf43;
            clip-path: polygon(50% 100%, 0 0, 100% 0);
        }

        .coffee-wing {
            position: absolute;
            top: 56px;
            width: 34px;
            height: 22px;
            border-radius: 999px;
            background: #4d2f20;
        }

        .coffee-wing.left { left: 2px; }
        .coffee-wing.right { right: 2px; }

        .coffee-register-card {
            position: relative;
            margin-top: -10px;
            padding: 24px;
            border-radius: 28px;
            border: 1px solid rgba(106, 65, 45, 0.12);
            background: rgba(255, 255, 255, 0.82);
            box-shadow: 0 18px 40px rgba(59, 36, 24, 0.08);
        }

        .coffee-register-card h2 {
            margin: 0 0 8px;
            color: #3b2418;
            font-size: 1.7rem;
            font-family: "Cormorant Garamond", serif;
        }

        .coffee-register-card p {
            margin: 0 0 18px;
            color: rgba(59, 36, 24, 0.72);
            line-height: 1.7;
        }

        .coffee-form-group + .coffee-form-group {
            margin-top: 16px;
        }

        .coffee-label {
            display: block;
            margin-bottom: 8px;
            font-size: 0.98rem;
            font-weight: 800;
            color: #5a3726;
        }

        .coffee-input-wrap {
            position: relative;
        }

        .coffee-input-icon {
            position: absolute;
            inset: 50% auto auto 16px;
            transform: translateY(-50%);
            color: rgba(106, 65, 45, 0.46);
            font-size: 1rem;
            pointer-events: none;
        }

        .coffee-input {
            width: 100%;
            padding: 16px 18px 16px 48px;
            border-radius: 18px;
            border: 1px solid rgba(181, 106, 59, 0.28);
            background: rgba(252, 248, 242, 0.96);
            color: #3b2418;
            font-size: 1rem;
            outline: none;
            transition: border-color 180ms ease, box-shadow 180ms ease, transform 180ms ease;
        }

        .coffee-input:focus {
            border-color: #b56a3b;
            box-shadow: 0 0 0 4px rgba(181, 106, 59, 0.16);
            transform: translateY(-1px);
        }

        .coffee-link {
            color: #8b5634;
            font-weight: 700;
            text-decoration: none;
        }

        .coffee-link:hover {
            text-decoration: underline;
        }

        .coffee-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
            margin-top: 24px;
            padding-top: 18px;
            border-top: 1px solid rgba(106, 65, 45, 0.1);
        }

        .coffee-actions-links {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
            color: rgba(59, 36, 24, 0.62);
        }

        .coffee-back-home {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 12px 16px;
            border-radius: 14px;
            border: 1px solid rgba(106, 65, 45, 0.14);
            background: rgba(255, 255, 255, 0.76);
            color: #7a4f37;
            font-weight: 800;
            text-decoration: none;
        }

        .coffee-back-home:hover {
            background: rgba(248, 240, 228, 0.96);
        }

        .coffee-submit {
            border: 0;
            padding: 14px 22px;
            border-radius: 16px;
            background: linear-gradient(135deg, #6a412d, #b56a3b);
            color: #fff;
            font-weight: 800;
            cursor: pointer;
            box-shadow: 0 16px 28px rgba(106, 65, 45, 0.22);
            transition: transform 180ms ease, box-shadow 180ms ease;
        }

        .coffee-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 20px 34px rgba(106, 65, 45, 0.26);
        }

        .coffee-error {
            margin-top: 8px;
            color: #9f2f20;
            font-size: 0.92rem;
            font-weight: 600;
        }

        @keyframes owl-bob {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-6px); }
        }

        @keyframes owl-blink {
            0%, 44%, 48%, 100% { transform: scaleY(1); }
            46% { transform: scaleY(0.08); }
        }
        @media (max-width: 560px) {
            .coffee-register-panel,
            .coffee-register-shell {
                padding: 24px 20px;
            }

            .coffee-register-card {
                padding: 20px;
            }

            .coffee-actions {
                align-items: stretch;
            }

            .coffee-submit {
                width: 100%;
            }
        }
    </style>

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

                        <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                            <a class="coffee-back-home" href="{{ route('tracker.index') }}">Kembali ke Beranda</a>
                            <button type="submit" class="coffee-submit">Register</button>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </div>

    <script>
        (() => {
            const owl = document.getElementById('coffee-owl');
            const name = document.getElementById('name');
            const email = document.getElementById('email');
            const password = document.getElementById('password');
            const passwordConfirmation = document.getElementById('password_confirmation');

            if (!owl || !name || !email || !password || !passwordConfirmation) {
                return;
            }

            const clearState = () => {
                owl.classList.remove('is-name', 'is-email', 'is-password', 'is-password-confirmation');
            };

            name.addEventListener('focus', () => {
                clearState();
                owl.classList.add('is-name');
            });

            email.addEventListener('focus', () => {
                clearState();
                owl.classList.add('is-email');
            });

            password.addEventListener('focus', () => {
                clearState();
                owl.classList.add('is-password');
            });

            passwordConfirmation.addEventListener('focus', () => {
                clearState();
                owl.classList.add('is-password-confirmation');
            });

            [name, email, password, passwordConfirmation].forEach((element) => {
                element.addEventListener('blur', clearState);
            });
        })();
    </script>
</x-guest-layout>
