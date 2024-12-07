<x-guest-layout>
    <form method="POST" action="{{ route('register') }}" onsubmit="return handleSubmit(event)" id="registerForm">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required
                autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')"
                required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required
                autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password"
                name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <input type="hidden" id="encryptedMasterKey" name="encryptedMasterKey" />

        <div class="flex items-center justify-between mt-4">
            <x-primary-button class="">
                {{ __('Register') }}
            </x-primary-button>
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>


        </div>
    </form>

    <script>
        async function handleSubmit(event) {


            event.preventDefault();

            try {
                // Aquí puedes agregar tu función asíncrona
                const password = document.getElementById("password").value;
                const derivedKey = await deriveKey(password);
                localStorage.setItem('derivedKey', derivedKey);

                // const masterKey = randomBytes(32);
                const masterKey = "12345678901234567890123456789012";

                const encryptedMasterKey = await aesEncrypt(masterKey, derivedKey);
                document.getElementById('encryptedMasterKey').value = encryptedMasterKey;
                // Una vez que la función asíncrona se complete, envía el formulario
                document.getElementById('registerForm').submit();
            } catch (error) {
                console.error('Error:', error);
                // Maneja el error según tus necesidades
                return false;
            }

            return false; // Previene el envío normal del formulario
        }
    </script>
</x-guest-layout>
