<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            Nonaktifkan Akun
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            Akun akan tetap tersimpan, tetapi kamu akan langsung logout dan tidak bisa login lagi sampai diaktifkan kembali oleh admin.
        </p>
    </header>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-account-deactivation')"
    >Nonaktifkan Akun</x-danger-button>

    <x-modal name="confirm-account-deactivation" :show="$errors->accountDeactivation->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.deactivate') }}" class="p-6">
            @csrf
            @method('patch')

            <h2 class="text-lg font-medium text-gray-900">
                Yakin ingin menonaktifkan akun?
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                Setelah dinonaktifkan, semua sesi login akan ditutup dan akun ini tidak bisa dipakai masuk lagi sampai admin mengaktifkannya kembali.
            </p>

            <div class="mt-6">
                <x-input-label for="deactivate_password" value="Password" class="sr-only" />

                <x-text-input
                    id="deactivate_password"
                    name="password"
                    type="password"
                    class="mt-1 block w-3/4"
                    placeholder="Password"
                />

                <x-input-error :messages="$errors->accountDeactivation->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    Batal
                </x-secondary-button>

                <x-danger-button class="ms-3">
                    Nonaktifkan
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
