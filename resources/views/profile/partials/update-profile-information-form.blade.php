<section>
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" style="display: grid; gap: 20px;">
        @csrf
        @method('patch')

        <div>
            <label for="name" class="profile-edit-label">Nama</label>
            <input
                id="name"
                name="name"
                type="text"
                class="profile-edit-input"
                value="{{ old('name', $user->name) }}"
                required
                autofocus
                autocomplete="name"
            >
            @foreach ((array) $errors->get('name') as $message)
                <p class="profile-edit-error">{{ $message }}</p>
            @endforeach
        </div>

        <div>
            <label for="email" class="profile-edit-label">Email</label>
            <input
                id="email"
                name="email"
                type="email"
                class="profile-edit-input"
                value="{{ old('email', $user->email) }}"
                required
                autocomplete="username"
            >
            @foreach ((array) $errors->get('email') as $message)
                <p class="profile-edit-error">{{ $message }}</p>
            @endforeach

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div style="margin-top: 12px; padding: 14px 16px; border-radius: 20px; background: rgba(255,255,255,0.66); border: 1px solid rgba(106, 65, 45, 0.1);">
                    <p style="margin: 0; color: #6c5244; line-height: 1.7;">
                        Email kamu belum terverifikasi.
                        <button form="send-verification" type="submit" style="padding: 0; border: 0; background: transparent; color: #8b5634; font-weight: 800; cursor: pointer;">
                            Kirim ulang verifikasi
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p style="margin: 8px 0 0; color: #2f6b55; font-weight: 700;">
                            Link verifikasi baru sudah dikirim ke email kamu.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div style="display: flex; align-items: center; justify-content: space-between; gap: 14px; flex-wrap: wrap; padding-top: 10px;">
            @if (session('status') === 'profile-updated')
                <p style="margin: 0; color: #2f6b55; font-weight: 700;">Perubahan berhasil disimpan.</p>
            @else
                <span style="color: #8a634b; line-height: 1.7;">Pastikan nama dan email sudah benar sebelum menyimpan.</span>
            @endif

            <button type="submit" class="profile-edit-submit">Simpan Perubahan</button>
        </div>
    </form>
</section>
