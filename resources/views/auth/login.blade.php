<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" onsubmit="return handleSubmit(event)" id="loginForm">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="text" name="email" :value="old('email')" required
                autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4 relative">
            <x-input-label for="password" :value="__('Password')" />

            <div class="relative">
                <x-text-input id="password" class="block mt-1 w-full pr-10" type="password" name="password" required
                    autocomplete="current-password" id="password" />

                <span id="togglePasswordIcon"
                    class="text-blue-700 material-symbols-outlined absolute right-2 top-1/2 transform -translate-y-1/2 cursor-pointer select-none"
                    onclick="togglePassword()">
                    visibility
                </span>
            </div>

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox"
                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex flex-col md:flex-row items-start gap-2 md:items-center justify-between mt-4">
            <x-primary-button class="mb-4 md:mb-0">
                {{ __('Log in') }}
            </x-primary-button>
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                href="{{ route('register') }}">
                {{ __('No account? Sign up') }}
            </a>

        </div>
    </form>
    <script>
        function togglePassword() {
            var x = document.getElementById("password");
            var icon = document.getElementById("togglePasswordIcon");

            if (x.type === "password") {
                x.type = "text";
                icon.textContent = "visibility_off";
            } else {
                x.type = "password";
                icon.textContent = "visibility";
            }
        }

        async function handleSubmit(event) {


            event.preventDefault();

            try {
                const password = document.getElementById("password").value;
                const derivedKey = await deriveKey(password);
                localStorage.setItem('derivedKey', derivedKey);

                // Una vez que la función asíncrona se complete, envía el formulario
                document.getElementById('loginForm').submit();
            } catch (error) {
                console.error('Error:', error);
                // Maneja el error según tus necesidades
                return false;
            }

            return false; // Previene el envío normal del formulario
        }
    </script>
</x-guest-layout>
