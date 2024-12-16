<x-app-layout>
    <div class="container mx-auto px-4 pb-10">
        <h2 class="text-2xl font-bold mb-6 mt-10">Secret info</h2>


        <div class="flex flex-col md:flex-row gap-4 md:gap-10">
            <div class="flex flex-col gap-4 md:w-96">
                {{-- Secret details --}}
                <div class="px-6 py-4 bg-white border border-gray-200 rounded-lg shadow">

                    <h5 class="mb-2  font-bold tracking-tight text-gray-900">Details</h5>

                    <p class="text-gray-700">Alias: <span class="text-gray-800 font-bold">{{ $secret->alias }}</span></p>
                    <p class="text-gray-700">Type: <span class="text-gray-800 font-bold">{{ $secret->secret_type }}</span>
                    </p>
                    </p>
                    <div class="text-gray-700 flex items-center gap-1">URL:
                        <button id="urlButton" class="items-center text-blue-700 font-bold"
                            onclick="copyUrlToClipboard()">
                            {{ $secret->url_identifier }}
                            <span class="material-symbols-outlined text-base" id="copyIcon">
                                content_copy
                            </span>
                        </button>
                        <input type="text" id="secret-url" value="" class="hidden">
                    </div>
                    <p class="text-gray-700 mb-2">Creation date: <span
                            class="text-gray-800 font-bold">{{ $secret->created_at->format('d/m/Y H:i') }}</span></p>


                    <h5 class="mb-2  font-bold tracking-tight text-gray-900">Settings</h5>

                    <p class="text-gray-700 flex gap-1">Days left:
                        <span class="text-gray-800 font-bold">
                            {{ $secret->days_expiration ? max(0, ceil(now()->diffInDays(\Carbon\Carbon::parse($secret->created_at)->addDays($secret->days_remaining)))) : '-' }}</span>

                    </p>

                    <p class="text-gray-700 flex gap-1">Clicks left:
                        <span class="text-gray-800 font-bold">
                            {{ $secret->clicks_expiration ? $secret->clicks_remaining : '-' }}</span>

                    </p>

                    <p class="text-gray-700">Manual deletion : <span
                            class="text-gray-800 font-bold">{{ $secret->manual_deletion ? 'Yes' : 'No' }}</span></p>

                    <p class="text-gray-700">Password protection : <span
                            class="text-gray-800 font-bold">{{ $secret->is_password_protected ? 'Yes' : 'No' }}</span>
                    </p>

                </div>
            </div>

            {{-- Map --}}
            <livewire:log-map :secretId="$secret->id" />

        </div>

        <h2 class="text-2xl font-bold mb-4 mt-5">Secret log</h2>
        {{-- Secret logs --}}

        <livewire:secret-log-list :secret="$secret" />


    </div>

    <script>
        document.addEventListener('DOMContentLoaded', async function() {
            const urlField = document.getElementById('secret-url');
            const urlIdentifier = "{{ $secret->url_identifier }}";

            const derivedKey = localStorage.getItem('derivedKey');
            const encryptedMasterKey = "{{ auth()->user()->master_key }}"
            const encryptedMessageKey = "{{ $secret->message_key }}"

            const masterKey = await aesDecrypt(encryptedMasterKey, derivedKey);
            const messageKey = await aesDecrypt(encryptedMessageKey, masterKey);

            const url = window.location.origin + '/secret/' + urlIdentifier + '?key=' + messageKey;

            urlField.value = url;

            // Llamada a la API
            const response = await fetch('/proxy/ip-location?ip=81.47.137.137');
            const data = await response.json();

        });

        function copyUrlToClipboard() {
            const url = document.getElementById('secret-url').value;
            const copyIcon = document.getElementById('copyIcon');

            navigator.clipboard.writeText(url);
            urlButton.classList.add('text-green-600');
            copyIcon.classList.add('text-green-600');

            // Eliminar clase después de 1 segundo
            setTimeout(() => {
                urlButton.classList.remove('text-green-600');
                copyIcon.classList.remove('text-green-600');
            }, 1000);

            Livewire.dispatch('show-toast', [{
                message: "URL copied to clipboard", // Mensaje que se mostrará en el toast
                class: "toast-success" // Clase CSS asociada al toast
            }]);
        }
    </script>
</x-app-layout>
