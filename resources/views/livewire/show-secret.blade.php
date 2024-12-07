<div>
    @if ($this->passwordProtected)
        <div class="flex flex-col items-center justify-center mt-48">
            <h2 class="text-2xl font-bold mb-2">Enter the password to view the secret:</h2>
            <form wire:submit.prevent="showSecret">
                <x-text-input wire:model="password" type="password" placeholder="Enter password" />
                <x-primary-button type="submit">Show secret</x-primary-button>
            </form>
            @if ($this->passwordError)
                <p class="text-red-500">Incorrect password</p>
            @endif
        </div>
    @else
        @if ($this->secret->secret_type === 'text')
            <div class="flex flex-col items-center justify-center mt-48">
                <p class="text-2xl font-bold mb-2">Shared secret:</p>
                <x-text-input value="{{ $this->decryptedMessage }}"
                    class="w-1/2 h-36 text-center text-xl bg-gray-200" />
            </div>
        @elseif ($this->secret->secret_type === 'file')
            <div class="flex flex-col items-center justify-center mt-48 p-10">
                <h2 class="text-2xl font-bold mb-2">Click the button below to download the file:</h2>
                <x-primary-button wire:click="decryptFile('{{ $messageKey }}')">
                    Download file
                </x-primary-button>
            </div>
        @endif
    @endif
</div>
