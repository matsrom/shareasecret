<div class="container mx-auto px-4">
    <h2 class="text-2xl font-bold mb-6 mt-10">Dashboard</h2>

    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead>
                <tr>
                    <th class="px-6 py-2 border-b text-center w-1/12 cursor-pointer" wire:click="sortBy('secret_type')">
                        Type</th>
                    <th class="px-6 py-2 border-b text-center w-5/12 cursor-pointer" wire:click="sortBy('alias')">Alias
                    </th>
                    <th class="px-6 py-2 border-b text-center w-1/12 cursor-pointer"
                        wire:click="sortBy('days_remaining')">Days left</th>
                    <th class="px-6 py-2 border-b text-center w-1/12 cursor-pointer"
                        wire:click="sortBy('clicks_remaining')">Clicks left</th>
                    <th class="px-6 py-2 border-b text-center w-2/12 cursor-pointer" wire:click="sortBy('created_at')">
                        Creation date</th>
                    <th class="px-6 py-2 border-b text-center w-2/12">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($secrets as $secret)
                    <tr>
                        <td class="px-6 py-2 border-b capitalize text-center">{{ $secret->secret_type }}</td>
                        <td class="px-6 py-2 border-b text-center">
                            <span title="{{ $secret->alias }}">
                                {{ Str::limit($secret->alias, 35, '...') }}
                            </span>
                        </td>
                        <td class="px-6 py-2 border-b text-center">
                            {{ $secret->days_expiration ? $secret->days_remaining : '-' }}
                        </td>
                        <td class="px-6 py-2 border-b text-center">
                            {{ $secret->clicks_expiration ? $secret->clicks_remaining : '-' }}
                        </td>
                        <td class="px-6 py-2 border-b text-center">{{ $secret->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-2 border-b flex items-center justify-center">
                            <x-icon-button type="blue-700" icon="content_copy"
                                id="copyUrlButton{{ $secret->url_identifier }}"
                                onclick="copyToClipboard('{{ $secret->url_identifier }}', '{{ $secret->message_key }}', '{{ auth()->user()->master_key }}')" />
                            <x-icon-link type="blue-700" icon="manage_search"
                                href="{{ route('secret.log', $secret->id) }}" />
                            <livewire:delete-secret :secretId="$secret->id" :wire:key="'delete-secret-' . uniqid()" />
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-gray-400 text-sm py-4">No secrets found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-10">
            {{ $secrets->links() }}


        </div>
    </div>
</div>

<script>
    async function copyToClipboard(urlIdentifier, encryptedMessageKey, encryptedMasterKey) {
        const derivedKey = localStorage.getItem('derivedKey');
        const masterKey = await aesDecrypt(encryptedMasterKey, derivedKey);

        console.log("masterKey", masterKey);
        console.log("encryptedMessageKey", encryptedMessageKey);

        const messageKey = await aesDecrypt(encryptedMessageKey, masterKey);

        console.log("messageKey", messageKey);

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
