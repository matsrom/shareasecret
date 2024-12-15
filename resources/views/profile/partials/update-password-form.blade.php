<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Update Password') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6"
        onsubmit="return handleSubmit(event)" id="updatePasswordForm">
        @csrf
        @method('put')

        <div>
            <x-input-label for="update_password_current_password" :value="__('Current Password')" />
            <x-text-input id="update_password_current_password" name="current_password" type="password"
                class="mt-1 block w-full" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password" :value="__('New Password')" />
            <x-text-input id="update_password_password" name="password" type="password" class="mt-1 block w-full"
                autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password"
                class="mt-1 block w-full" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <input type="hidden" id="newMasterKey" name="newMasterKey" />
        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600">{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>

    <script>
        async function handleSubmit(event) {
            event.preventDefault();

            try {
                // Aquí puedes agregar tu función asíncrona

                const encryptedMasterKey = "{{ auth()->user()->master_key }}";
                const derivedKey = localStorage.getItem('derivedKey');
                const masterKey = await aesDecrypt(encryptedMasterKey, derivedKey);


                const newPassword = document.getElementById("update_password_password").value;
                const newDerivedKey = await deriveKey(newPassword);
                localStorage.setItem('derivedKey', newDerivedKey);
                const newEncryptedMasterKey = await aesEncrypt(masterKey, newDerivedKey);


                document.getElementById('newMasterKey').value = newEncryptedMasterKey;
                // Una vez que la función asíncrona se complete, envía el formulario
                document.getElementById('updatePasswordForm').submit();
            } catch (error) {
                console.error('Error:', error);
                // Maneja el error según tus necesidades
                return false;
            }

            return false; // Previene el envío normal del formulario
        }
    </script>
</section>
