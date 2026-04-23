<section>
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="profile-update-form" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <div class="profile-avatar-upload">
            <div class="profile-avatar-preview">
                @if (! blank($user->profile_photo_url))
                    <img src="{{ $user->profile_photo_url }}" alt="Avatar {{ $user->name }}">
                @else
                    <span>{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                @endif
            </div>

            <div class="profile-avatar-fields">
                <label for="avatar_file" class="profile-edit-label">Avatar</label>
                <input
                    id="avatar_file"
                    name="avatar_file"
                    type="file"
                    class="profile-edit-input profile-edit-input-file"
                    accept="image/png,image/jpeg,image/webp"
                >
                <p class="profile-avatar-help">Gunakan JPG, PNG, atau WEBP (maksimal 4MB).</p>

                @if (! blank($user->profile_photo_path))
                    <label class="profile-avatar-remove">
                        <input type="checkbox" name="remove_avatar" value="1">
                        Hapus avatar saat ini
                    </label>
                @endif

                @foreach ((array) $errors->get('avatar_file') as $message)
                    <p class="profile-edit-error">{{ $message }}</p>
                @endforeach
            </div>
        </div>

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
                <div class="profile-verification-box">
                    <p class="profile-verification-text">
                        Email kamu belum terverifikasi.
                        <button form="send-verification" type="submit" class="profile-verification-button">
                            Kirim ulang verifikasi
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="profile-verification-sent">
                            Link verifikasi baru sudah dikirim ke email kamu.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="profile-form-footer">
            @if (session('status') === 'profile-updated')
                <p class="profile-form-footer-success">Perubahan berhasil disimpan.</p>
            @else
                <span class="profile-form-footer-note">Pastikan nama dan email sudah benar sebelum menyimpan.</span>
            @endif

            <button type="submit" class="profile-edit-submit">Simpan Perubahan</button>
        </div>
    </form>
</section>
