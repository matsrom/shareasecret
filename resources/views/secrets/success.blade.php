<x-app-layout>
    <div class="flex flex-col items-center justify-center  mt-32 md:mt-48">
        <p class="text-2xl">Go ahead and share this secret!</p>

        <div class="flex flex-row gap-2 justify-center items-center mt-8 w-3/4">
            <x-text-input id="result" class="block w-full md:w-1/2 h-9" type="text" value="{{ $shareLink }}"
                readonly />
            <button type="button" onclick="copyToClipboard()" title="Copiar al portapapeles">

                <svg id="copyIcon" xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960"
                    width="24px" fill="#1D4ED8">
                    <path
                        d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h168q13-36 43.5-58t68.5-22q38 0 68.5 22t43.5 58h168q33 0 56.5 23.5T840-760v560q0 33-23.5 56.5T760-120H200Zm0-80h560v-560H200v560Zm80-80h280v-80H280v80Zm0-160h400v-80H280v80Zm0-160h400v-80H280v80Zm200-190q13 0 21.5-8.5T510-820q0-13-8.5-21.5T480-850q-13 0-21.5 8.5T450-820q0 13 8.5 21.5T480-790ZM200-200v-560 560Z" />
                </svg>
            </button>

        </div>
        <div class="mt-8 px-4 md:px-0">
            @if ($secret->days_remaining && $secret->clicks_remaining)
                <p class="">This secret has been set to expire in <strong>{{ $secret->days_remaining }}</strong>
                    day{{ $secret->days_remaining > 1 ? 's' : '' }} and
                    <strong>{{ $secret->clicks_remaining }}</strong>
                    click{{ $secret->clicks_remaining > 1 ? 's' : '' }}.
                </p>
            @elseif ($secret->days_remaining)
                <p class="">This secret has been set to expire in
                    <strong>{{ $secret->days_remaining }}</strong> day{{ $secret->days_remaining > 1 ? 's' : '' }}.
                </p>
            @elseif ($secret->clicks_expiration)
                <p class="">This secret has been set to expire in <strong>{{ $secret->clicks_remaining }}</strong>
                    click{{ $secret->clicks_remaining > 1 ? 's' : '' }}.</p>
            @endif

            @if ($secret->allow_manual_deletion)
                <p>This secret has been set so that it can be deleted by anyone viewing it.</p>
            @endif

            @if ($secret->is_password_protected)
                <p>This secret is password protected.</p>
            @endif

            @if ($secret->keep_track)
                <p>This secret has been set to be tracked by you hand has been given the the alias
                    <strong>{{ $secret->alias }}</strong>.
                </p>
            @endif
        </div>
    </div>

    <script>
        function copyToClipboard() {
            const secretInput = document.getElementById("result");
            const copyIcon = document.getElementById("copyIcon");

            // Seleccionar y copiar el texto al portapapeles
            secretInput.select();
            secretInput.setSelectionRange(0, 99999); // Para dispositivos móviles

            navigator.clipboard.writeText(secretInput.value)
                .then(() => {
                    // Cambiar el color a verde si la copia es exitosa
                    copyIcon.style.fill = "#16A34A"; // Verde
                    setTimeout(() => {
                        copyIcon.style.fill = "#1D4ED8"; // Volver al color original
                    }, 2000);
                })
                .catch(() => {
                    // Cambiar el color a rojo si ocurre un error
                    copyIcon.style.fill = "#DC2626"; // Rojo
                    setTimeout(() => {
                        copyIcon.style.fill = "#1D4ED8"; // Volver al color original
                    }, 2000);
                });

            Livewire.dispatch('show-toast', [{
                message: "URL copied to clipboard", // Mensaje que se mostrará en el toast
                class: "toast-success" // Clase CSS asociada al toast
            }]);
        }
    </script>

</x-app-layout>
