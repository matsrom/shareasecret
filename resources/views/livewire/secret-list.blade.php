<div class="container mx-auto px-4">
    <h2 class="text-2xl font-bold mb-6 mt-10">Dashboard</h2>

    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead>
                <tr>
                    <th class="px-6 py-2 border-b text-center">Type</th>
                    <th class="px-6 py-2 border-b text-center">Alias</th>
                    <th class="px-6 py-2 border-b text-center">URL</th>
                    <th class="px-6 py-2 border-b text-center">Days left</th>
                    <th class="px-6 py-2 border-b text-center">Clicks left</th>
                    <th class="px-6 py-2 border-b text-center">Creation date</th>
                    <th class="px-6 py-2 border-b text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($secrets as $secret)
                    <tr class="">
                        <td class="px-6 py-2 border-b capitalize text-center">{{ $secret->secret_type }}</td>
                        <td class="px-6 py-2 border-b text-center">
                            <span title="{{ $secret->alias }}">
                                {{ Str::limit($secret->alias, 15, '...') }}
                            </span>
                        </td>
                        <td class="px-6 py-2 border-b text-center">
                            <button id="copyUrlButton{{ $secret->url_identifier }}"
                                onclick="copyToClipboard('{{ $secret->url_identifier }}', '{{ $secret->message_key }}', '{{ auth()->user()->master_key }}')"
                                class="text-blue-600 hover:text-blue-800">
                                <span class="material-symbols-outlined text-base" id="copyIcon">
                                    content_copy
                                </span>
                            </button>
                        </td>
                        <td class="px-6 py-2 border-b text-center">
                            {{ $secret->days_expiration ? $secret->days_remaining : '-' }}
                        </td>
                        <td class="px-6 py-2 border-b text-center">
                            {{ $secret->clicks_expiration ? $secret->clicks_remaining : '-' }}
                        </td>
                        <td class="px-6 py-2 border-b text-center">{{ $secret->created_at->format('d/m/Y') }}</td>
                        <td class="px-6 py-2 border-b text-center flex items-center justify-center">
                            <x-icon-link type="blue-700" icon="manage_search"
                                href="{{ route('secret.details', $secret->id) }}" />
                            <livewire:delete-secret :secretId="$secret->id" :wire:key="'delete-secret-' . uniqid()" />
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
    async function copyToClipboard(urlIdentifier, encryptedMessageKey, encryptedMasterKey) {
        const derivedKey = localStorage.getItem('derivedKey');
        const masterKey = await aesDecrypt(encryptedMasterKey, derivedKey);

        const decodedMessageKey = atob(encryptedMessageKey);
        const messageKey = await aesDecrypt(decodedMessageKey, masterKey);

        const url = window.location.origin + '/secret/' + urlIdentifier + '?key=' + messageKey;

        // Copiar la URL al portapapeles
        navigator.clipboard.writeText(url);
        const urlButton = document.getElementById('copyUrlButton' + urlIdentifier);
        urlButton.classList.add('text-green-600');


        // Eliminar clase después de 1 segundo
        setTimeout(() => {
            urlButton.classList.remove('text-green-600');
        }, 1000);



        Livewire.dispatch('show-toast', [{
            message: "URL copied to clipboard", // Mensaje que se mostrará en el toast
            class: "toast-success" // Clase CSS asociada al toast
        }]);

    }
</script>
