<x-app-layout>
    <div class="flex flex-col items-center justify-center  mt-10 md:mt-48">
        <p class="text-2xl">Go ahead and share this secret!</p>

        <div class="flex flex-row gap-2 justify-center items-center mt-8 w-3/4">
            <x-text-input id="result" class="block w-full md:w-1/2 h-9" type="text" readonly />
            <button type="button" onclick="copyToClipboard()" title="Copiar al portapapeles">

                <span class="material-symbols-outlined" id="copyIcon">
                    content_copy
                </span>
            </button>

        </div>
        <div class="mt-8 px-4 md:px-0  md:max-w-5xl">
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
                <p class="break-words">This secret has been set to be tracked by you hand has been given the the
                    alias
                    <strong><span class="md:hidden"><br>{{ Str::limit($secret->alias, 25, '...') }}</span></strong>
                    <strong><span class="hidden md:block">{{ $secret->alias }}</span></strong>
                </p>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const url = window.location.origin + '/secret/' + '{{ $secret->url_identifier }}';
            const urlKey = sessionStorage.getItem('urlKey');
            const urlWithKey = url + '?key=' + urlKey;
            console.log(urlWithKey);
            document.getElementById("result").value = urlWithKey;
        });

        function copyToClipboard() {
            const secretInput = document.getElementById("result");
            const copyIcon = document.getElementById("copyIcon");

            // Seleccionar y copiar el texto al portapapeles
            secretInput.select();
            secretInput.setSelectionRange(0, 99999); // Para dispositivos móviles

            navigator.clipboard.writeText(secretInput.value);

            copyIcon.classList.add('text-green-600');


            // Eliminar clase después de 1 segundo
            setTimeout(() => {
                copyIcon.classList.remove('text-green-600');
            }, 1000);

            Livewire.dispatch('show-toast', [{
                message: "URL copied to clipboard", // Mensaje que se mostrará en el toast
                class: "toast-success" // Clase CSS asociada al toast
            }]);
        }
    </script>

</x-app-layout>
