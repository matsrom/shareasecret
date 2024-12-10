<div>
    @if ($this->passwordProtected)
        <div class="flex flex-col items-center justify-center mt-48 px-4">
            <h2 class="text-2xl font-bold mb-2">Enter the password to view the secret:</h2>
            <form wire:submit.prevent="showSecret" class="flex flex-col items-center justify-center gap-2 w-full">
                <x-text-input wire:model="password" type="password" placeholder="Enter password" class="w-2/3 md:w-64" />
                <x-primary-button type="submit" class="w-2/3 md:w-64 justify-center">Show secret</x-primary-button>
            </form>
            @if ($this->passwordError)
                <p class="text-red-500">Incorrect password</p>
            @endif
        </div>
    @else
        @if ($this->secret->secret_type === 'text')
            <div class="flex flex-col items-center justify-center mt-32 md:mt-48 px-4">
                <p class="text-2xl font-bold mb-2">Shared secret:</p>
                <x-text-input value="{{ $this->decryptedMessage }}"
                    class="w-full md:w-1/2 h-36 text-center text-xl bg-gray-200" />
            </div>
        @elseif ($this->secret->secret_type === 'file')
            <div class="flex flex-col items-center justify-center mt-48 p-10">
                <h2 class="text-2xl font-bold mb-2">Click the button below to download the file:</h2>
                <x-primary-button wire:click="decryptFile('{{ $messageKey }}')">
                    Download file
                </x-primary-button>
            </div>
        @endif
        @if ($this->manualDeletion)
            <div class="flex flex-col items-center justify-center mt-8">
                <x-primary-button wire:click="deleteSecret" class="bg-red-600 hover:bg-red-800">Delete
                    secret</x-primary-button>
                <p class="text-sm text-gray-500 mt-2">This secret won't be available anymore</p>
            </div>
        @endif
    @endif
</div>
