<x-app-layout>
    <div class="container mx-auto px-4">
        <h2 class="text-2xl font-bold mb-6 mt-10">Secret details</h2>


        <div class="flex flex-row gap-10">
            <div class="max-w-sm p-6 bg-white border border-gray-200 rounded-lg shadow">
                <a href="#">
                    <h5 class="mb-2 text-xl font-bold tracking-tight text-gray-900">Secret details</h5>
                </a>
                <p class="text-gray-700">Alias: <span class="text-gray-800 font-bold">{{ $secret->alias }}</span></p>
                <p class="text-gray-700">Type: <span class="text-gray-800 font-bold">{{ $secret->secret_type }}</span></p>
                </p>
                <div class="text-gray-700 flex items-center gap-1">URL:
                    <button id="urlButton" class="items-center text-blue-700 font-bold" onclick="copyUrlToClipboard()">
                        {{ $secret->url_identifier }}
                        <span class="material-symbols-outlined text-base" id="copyIcon">
                            content_copy
                        </span>
                    </button>
                    <input type="text" id="secret-url" value="" class="hidden">
                </div>
                <p class="text-gray-700">Creation date: <span
                        class="text-gray-800 font-bold">{{ $secret->created_at->format('d/m/Y H:i') }}</span></p>
            </div>

            {{-- Secret settings --}}
            <div class="max-w-sm p-6 bg-white border border-gray-200 rounded-lg shadow">
                <a href="#">
                    <h5 class="mb-2 text-xl font-bold tracking-tight text-gray-900">Secret settings</h5>
                </a>
                <p class="text-gray-700 flex gap-1">Days left:
                    <span class="text-gray-800 font-bold">
                        {{ $secret->days_expiration ? $secret->days_remaining : '-' }}</span>

                </p>

                <p class="text-gray-700 flex gap-1">Clicks left:
                    <span class="text-gray-800 font-bold">
                        {{ $secret->clicks_expiration ? $secret->clicks_remaining : '-' }}</span>

                </p>

                <p class="text-gray-700">Manual deletion : <span
                        class="text-gray-800 font-bold">{{ $secret->manual_deletion ? 'Yes' : 'No' }}</span></p>

                <p class="text-gray-700">Password protection : <span
                        class="text-gray-800 font-bold">{{ $secret->is_password_protected ? 'Yes' : 'No' }}</span></p>



            </div>

        </div>

        {{-- Secret logs --}}
        <div class="overflow-x-auto mt-10">
            <table class="min-w-full">
                <thead>
                    <tr>
                        <th class="px-6 py-2 border-b text-center">IP</th>
                        <th class="px-6 py-2 border-b text-center">Browser</th>
                        <th class="px-6 py-2 border-b text-center">OS</th>
                        <th class="px-6 py-2 border-b text-center">Device</th>
                        <th class="px-6 py-2 border-b text-center">Country</th>
                        <th class="px-6 py-2 border-b text-center">City</th>
                        <th class="px-6 py-2 border-b text-center">Access date</th>
                        <th class="px-6 py-2 border-b text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($secretLogs as $secretLog)
                        <tr class="text-gray-700">
                            <td class="px-6 py-2 border-b text-center">{{ $secretLog->ip_address }}</td>
                            <td class="px-6 py-2 border-b text-center">{{ $secretLog->browser }}</td>
                            <td class="px-6 py-2 border-b text-center">{{ $secretLog->os }}</td>
                            <td class="px-6 py-2 border-b text-center">{{ $secretLog->device }}</td>
                            <td class="px-6 py-2 border-b text-center">{{ $secretLog->country }}</td>
                            <td class="px-6 py-2 border-b text-center">{{ $secretLog->city }}</td>
                            <td class="px-6 py-2 border-b text-center">
                                {{ \Carbon\Carbon::parse($secretLog->access_date)->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-2 border-b text-center">
                                {{ $secretLog->is_successful ? 'Success' : 'Failed' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', async function() {
            const urlField = document.getElementById('secret-url');
            const urlIdentifier = "{{ $secret->url_identifier }}";

            const derivedKey = localStorage.getItem('derivedKey');
            const encryptedMasterKey = "{{ auth()->user()->master_key }}"
            const encryptedMessageKey = "{{ $secret->message_key }}"

            const masterKey = await aesDecrypt(encryptedMasterKey, derivedKey);
            const decodedMessageKey = atob(encryptedMessageKey);
            const messageKey = await aesDecrypt(decodedMessageKey, masterKey);

            const url = window.location.origin + '/secret/' + urlIdentifier + '?key=' + messageKey;

            urlField.value = url;
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
